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
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;

class KartuPasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    protected $filters;
    protected $dataItems;

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

        $this->dataItems = $query->get();
        return $this->dataItems;
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
            'Jabatan',
            'Tanggal Terbit',
            'Tanggal Berlaku',
            'Status',
            'QR Code',
        ];
    }

    public function map($kartu): array
    {
        return [
            $kartu->nomor_kartu,
            $kartu->nama_pemegang,
            $kartu->perusahaan,
            $kartu->area_akses,
            $kartu->jabatan ?? '-',
            $kartu->tanggal_terbit->format('d/m/Y'),
            $kartu->tanggal_berlaku->format('d/m/Y'),
            ucfirst(str_replace('_', ' ', $kartu->status)),
            '', // QR code column - will be filled with image in afterSheet
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    /**
     * Generate a GD image resource for a QR code PNG
     */
    private function generateQrGdImage(string $text): \GdImage|false
    {
        $options = new QROptions;
        $options->outputInterface = QRGdImagePNG::class;
        $options->scale = 4;
        $options->imageTransparent = false;
        $options->outputBase64 = false;

        $qr = new QRCode($options);
        $pngData = $qr->render($text);

        return imagecreatefromstring($pngData);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 3 header title rows
                $sheet->insertNewRowBefore(1, 3);

                // Main Title
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'LAPORAN DATA KARTU PAS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e3a5f']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Subtitle
                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'MONPASKU - Sistem Monitoring PAS Bandara');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['rgb' => '64748b']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Print Date
                $sheet->mergeCells('A3:I3');
                $sheet->setCellValue('A3', 'Dicetak: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 9, 'color' => ['rgb' => '94a3b8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $lastRow = $sheet->getHighestRow();

                // Style table header row (Row 4)
                $sheet->getStyle('A4:I4')->applyFromArray([
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

                // Style all data cells
                $sheet->getStyle('A4:I' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Alternating row colors
                for ($row = 5; $row <= $lastRow; $row++) {
                    if ($row % 2 == 1) {
                        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                            'fill' => [
                                'fillType'   => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'],
                            ],
                        ]);
                    }
                }

                // Status column coloring
                for ($row = 5; $row <= $lastRow; $row++) {
                    $status = $sheet->getCell('H' . $row)->getValue();
                    $color = match($status) {
                        'Aktif'      => '16a34a',
                        'Kadaluarsa' => 'dc2626',
                        default      => '6b7280',
                    };
                    $sheet->getStyle('H' . $row)->applyFromArray([
                        'font' => ['color' => ['rgb' => $color], 'bold' => true],
                    ]);
                }

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(15);
                $sheet->getRowDimension(4)->setRowHeight(25);

                // Set QR Code column width
                $sheet->getColumnDimension('I')->setAutoSize(false);
                $sheet->getColumnDimension('I')->setWidth(12);

                // Embed QR Code PNG images for each data row
                if ($this->dataItems && count($this->dataItems) > 0) {
                    foreach ($this->dataItems as $index => $kartu) {
                        $rowNum = $index + 5; // data starts at row 5

                        // Set row height for QR code
                        $sheet->getRowDimension($rowNum)->setRowHeight(55);

                        // Clear the text placeholder
                        $sheet->setCellValue('I' . $rowNum, '');

                        try {
                            $gdImage = $this->generateQrGdImage($kartu->nomor_kartu);

                            if ($gdImage !== false) {
                                $drawing = new MemoryDrawing();
                                $drawing->setName('QR-' . $kartu->nomor_kartu);
                                $drawing->setDescription('QR Code: ' . $kartu->nomor_kartu);
                                $drawing->setImageResource($gdImage);
                                $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
                                $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);
                                $drawing->setHeight(50);
                                $drawing->setCoordinates('I' . $rowNum);
                                $drawing->setOffsetX(8);
                                $drawing->setOffsetY(3);
                                $drawing->setWorksheet($sheet);
                            }
                        } catch (\Throwable $e) {
                            // Fallback: write text if image fails
                            $sheet->setCellValue('I' . $rowNum, $kartu->nomor_kartu);
                        }
                    }
                }
            },
        ];
    }
}