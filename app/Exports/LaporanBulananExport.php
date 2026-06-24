<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\KartuPas;
use App\Models\Permohonan;

class LaporanBulananExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function title(): string
    {
        return 'Laporan Bulanan ' . $this->tahun;
    }

    public function collection()
    {
        $laporanBulanan = collect();

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $totalPermohonan    = Permohonan::whereYear('created_at', $this->tahun)->whereMonth('created_at', $bulan)->count();
            $disetujui          = Permohonan::whereYear('created_at', $this->tahun)->whereMonth('created_at', $bulan)->where('status', 'disetujui')->count();
            $ditolak            = Permohonan::whereYear('created_at', $this->tahun)->whereMonth('created_at', $bulan)->where('status', 'ditolak')->count();
            $kartuBaru          = KartuPas::whereYear('tanggal_terbit', $this->tahun)->whereMonth('tanggal_terbit', $bulan)->count();
            $kartuKadaluarsa    = KartuPas::whereYear('tanggal_berlaku', $this->tahun)->whereMonth('tanggal_berlaku', $bulan)->where('status', 'kadaluarsa')->count();
            $kartuDiperpanjang  = KartuPas::whereYear('updated_at', $this->tahun)->whereMonth('updated_at', $bulan)->where('status', 'aktif')->whereYear('tanggal_terbit', '!=', $this->tahun)->count();

            $laporanBulanan->push((object)[
                'bulan'              => \DateTime::createFromFormat('!m', $bulan)->format('F'),
                'total_permohonan'   => $totalPermohonan,
                'disetujui'          => $disetujui,
                'ditolak'            => $ditolak,
                'kartu_baru'         => $kartuBaru,
                'kartu_kadaluarsa'   => $kartuKadaluarsa,
                'kartu_diperpanjang' => $kartuDiperpanjang,
            ]);
        }

        return $laporanBulanan;
    }

    public function headings(): array
    {
        return [
            'Bulan',
            'Total Permohonan',
            'Disetujui',
            'Ditolak',
            'Kartu Baru',
            'Kartu Kadaluarsa',
            'Diperpanjang',
        ];
    }

    public function map($row): array
    {
        return [
            $row->bulan,
            $row->total_permohonan,
            $row->disetujui,
            $row->ditolak,
            $row->kartu_baru,
            $row->kartu_kadaluarsa,
            $row->kartu_diperpanjang,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Header style
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e3a5f']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Border semua cell
        $sheet->getStyle('A1:G' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        // Warna baris ganjil
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
            }
        }

        // Center alignment data
        $sheet->getStyle('B2:G' . $lastRow)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Insert judul
                $sheet->insertNewRowBefore(1, 3);

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'LAPORAN BULANAN MONPASKU - TAHUN ' . $this->tahun);
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Sistem Monitoring PAS Bandara - MONPASKU');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['rgb' => '64748b']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells('A3:G3');
                $sheet->setCellValue('A3', 'Dicetak: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 9, 'color' => ['rgb' => '94a3b8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Total row
                $totalRow = $sheet->getHighestRow() + 1;
                $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                ]);

                for ($col = 2; $col <= 7; $col++) {
                    $colLetter = chr(64 + $col);
                    $sheet->setCellValue($colLetter . $totalRow, '=SUM(' . $colLetter . '5:' . $colLetter . ($totalRow - 1) . ')');
                }

                // Auto size kolom
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}