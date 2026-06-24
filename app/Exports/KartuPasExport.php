<?php

namespace App\Exports;

use App\Models\KartuPas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KartuPasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = KartuPas::latest();

        if (!empty($this->filters['search'])) {
            $query->where(function($q) {
                $q->where('nama_pemegang', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('nomor_kartu', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        if (!empty($this->filters['instansi'])) {
            $query->where('perusahaan', $this->filters['instansi']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->get();
    }

    public function title(): string
    {
        return 'Data Kartu PAS';
    }

    public function headings(): array
    {
        return [
            'No. Kartu',
            'Nama Pemegang',
            'Instansi/Perusahaan',
            'Area Akses',
            'Tanggal Terbit',
            'Tanggal Berlaku',
            'Status',
        ];
    }

    public function map($kartu): array
    {
        return [
            $kartu->nomor_kartu,
            $kartu->nama_pemegang,
            $kartu->perusahaan,
            $kartu->area_akses,
            $kartu->tanggal_terbit->format('d/m/Y'),
            $kartu->tanggal_berlaku->format('d/m/Y'),
            ucfirst(str_replace('_', ' ', $kartu->status)),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Style header
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1e3a5f'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Border semua cell
        $sheet->getStyle('A1:G' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Warna baris ganjil
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                ]);
            }
        }

        // Alignment semua data
        $sheet->getStyle('A2:G' . $lastRow)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Warna status
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = $sheet->getCell('G' . $row)->getValue();
            if ($status === 'Aktif') {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '16a34a'], 'bold' => true],
                ]);
            } elseif ($status === 'Kadaluarsa') {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'dc2626'], 'bold' => true],
                ]);
            } else {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '6b7280'], 'bold' => true],
                ]);
            }
        }

        // Tinggi baris header
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $lastRow   = $sheet->getHighestRow();
                $lastCol   = $sheet->getHighestColumn();

                // Tambah judul di atas tabel
                $sheet->insertNewRowBefore(1, 3);

                // Judul utama
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'LAPORAN DATA KARTU PAS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Subjudul
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'MONPASKU - Sistem Monitoring PAS Bandara');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['rgb' => '64748b']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Tanggal cetak
                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', 'Dicetak: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 9, 'color' => ['rgb' => '94a3b8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Tinggi baris judul
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(15);
            },
        ];
    }
}