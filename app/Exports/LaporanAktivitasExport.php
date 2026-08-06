<?php

namespace App\Exports;

use App\Models\ScanLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanAktivitasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $kodeArea;
    protected $statusAkses;
    protected $tipeAktivitas;

    public function __construct($startDate, $endDate, $kodeArea = null, $statusAkses = null, $tipeAktivitas = null)
    {
        $this->startDate     = $startDate;
        $this->endDate       = $endDate;
        $this->kodeArea      = $kodeArea;
        $this->statusAkses   = $statusAkses;
        $this->tipeAktivitas = $tipeAktivitas;
    }

    public function collection()
    {
        $query = ScanLog::with('cameraDevice')
            ->whereDate('waktu_scan', '>=', $this->startDate)
            ->whereDate('waktu_scan', '<=', $this->endDate);

        if (!empty($this->kodeArea)) {
            $query->where('kode_area', $this->kodeArea);
        }

        if (!empty($this->statusAkses)) {
            $query->where('status_akses', $this->statusAkses);
        }

        if (!empty($this->tipeAktivitas)) {
            $query->where('tipe_aktivitas', $this->tipeAktivitas);
        }

        return $query->latest('waktu_scan')->get();
    }

    public function headings(): array
    {
        return [
            'Waktu Scan',
            'No. Kartu PAS',
            'Nama Pemegang',
            'Perusahaan / Instansi',
            'Area Akses',
            'Perangkat Kamera',
            'Tipe Aktivitas',
            'Status Akses',
            'Keterangan / Alasan',
            'Catatan Akses',
        ];
    }

    public function map($log): array
    {
        return [
            $log->waktu_scan ? $log->waktu_scan->format('d/m/Y H:i:s') : '-',
            $log->nomor_kartu,
            $log->nama_pemegang,
            $log->perusahaan,
            'Area ' . $log->kode_area,
            optional($log->cameraDevice)->nama_kamera ?? '-',
            strtoupper($log->tipe_aktivitas ?: 'MASUK'),
            strtoupper($log->status_akses),
            $log->alasan,
            $log->catatan ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A5F'],
                ],
            ],
        ];
    }
}
