<?php

namespace App\Exports;

use App\Models\Instansi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InstansiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $rowNumber = 0;

    public function collection()
    {
        // Sinkronisasi unlinked kartu sebelum export
        Instansi::syncUnlinkedKartuPas();

        return Instansi::withCount([
            'kartuPas as total_kartu',
            'kartuPas as kartu_aktif' => function($q) {
                $q->where('status', 'aktif');
            },
            'kartuPas as kartu_nonaktif' => function($q) {
                $q->where('status', '!=', 'aktif');
            }
        ])->latest()->get();
    }

    public function title(): string
    {
        return 'Data Instansi';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Instansi / Perusahaan',
            'Total Kuota PAS',
            'Kuota Terpakai (Kartu Terdaftar)',
            'Kartu Aktif',
            'Sisa Kuota',
            'Email',
            'Telepon',
            'Alamat',
            'Status',
        ];
    }

    public function map($instansi): array
    {
        $this->rowNumber++;
        $terpakai = $instansi->total_kartu ?? 0;
        $aktif    = $instansi->kartu_aktif ?? 0;
        $sisa     = max(0, $instansi->kuota - $terpakai);

        return [
            $this->rowNumber,
            $instansi->nama_instansi,
            $instansi->kuota . ' Kartu',
            $terpakai . ' Kartu',
            $aktif . ' Kartu',
            $sisa . ' Kartu',
            $instansi->email ?? '-',
            $instansi->telepon ?? '-',
            $instansi->alamat ?? '-',
            $instansi->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Navy Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:J' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center alignment untuk kolom No, Kuota, Terpakai, Aktif, Sisa, Status
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }
}
