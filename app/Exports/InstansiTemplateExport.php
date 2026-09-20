<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InstansiTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Template Instansi';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Instansi / Perusahaan',
            'Kuota PAS',
            'Email',
            'Nomor Telepon',
            'Alamat',
            'Status (Aktif/Nonaktif)',
        ];
    }

    public function array(): array
    {
        return [
            [
                1,
                'PT Angkasa Pura Indonesia',
                50,
                'info@angkasapura.co.id',
                '0211234567',
                'Bandara Internasional Mopah Merauke',
                'Aktif',
            ],
            [
                2,
                'Kantor Imigrasi Kelas II TPI Merauke',
                20,
                'imigrasi.merauke@kemenkumham.go.id',
                '081234567890',
                'Jl. Sabang No. 12, Merauke',
                'Aktif',
            ],
            [
                3,
                'Satuan Radar 244 Merauke',
                15,
                'radar244@tni-au.mil.id',
                '081298765432',
                'Kawasan Militer Merauke',
                'Aktif',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0284C7'], // Sky Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:G4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);

        $sheet->getStyle('A2:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }
}
