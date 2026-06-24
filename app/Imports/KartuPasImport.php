<?php

namespace App\Imports;

use App\Models\KartuPas;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Illuminate\Support\Collection;

class KartuPasImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public $imported = 0;
    public $skipped  = 0;
    public $updated  = 0;

    public function sheets(): array
    {
        return [
            0 => new KartuPasSheetImport($this),
            1 => new KartuPasSheetImport($this),
            2 => new KartuPasSheetImport($this),
            3 => new KartuPasSheetImport($this),
            4 => new KartuPasSheetImport($this),
            5 => new KartuPasSheetImport($this),
            6 => new KartuPasSheetImport($this),
            7 => new KartuPasSheetImport($this),
            8 => new KartuPasSheetImport($this),
            9 => new KartuPasSheetImport($this),
            10 => new KartuPasSheetImport($this),
            11 => new KartuPasSheetImport($this),
            12 => new KartuPasSheetImport($this),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // Abaikan sheet yang tidak ditemukan
    }
}

class KartuPasSheetImport implements ToCollection
{
    protected $parent;
    protected $currentInstansi = '';

    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip baris kosong
            if ($row->filter()->isEmpty()) continue;

            // Deteksi nama instansi
            if (!empty($row[3]) && empty($row[4]) && empty($row[5])) {
                $this->currentInstansi = strtoupper(trim($row[3]));
                continue;
            }

            // Skip header
            if ($row[3] === 'NAMA' || $row[2] === 'NO' || $row[3] === 'NO') continue;

            // Ambil data
            $nomor       = trim($row[4] ?? '');
            $nama        = trim($row[3] ?? '');
            $area        = trim($row[5] ?? '');
            $jabatan     = trim($row[6] ?? '');
            $masaBerlaku = trim($row[7] ?? '');

            // Skip jika tidak ada nomor atau nama
            if (empty($nomor) || empty($nama) || !is_string($nomor)) continue;

            // Skip jika nomor bukan format kartu PAS
            if (!str_contains($nomor, '.')) continue;

            // Parse tanggal berlaku
            try {
                $bulanMap = [
                    'JANUARI'   => '01', 'FEBRUARI' => '02', 'MARET'    => '03',
                    'APRIL'     => '04', 'MEI'      => '05', 'JUNI'     => '06',
                    'JULI'      => '07', 'AGUSTUS'  => '08', 'SEPTEMBER'=> '09',
                    'OKTOBER'   => '10', 'NOVEMBER' => '11', 'DESEMBER' => '12',
                ];

                $masaBerlaku = strtoupper($masaBerlaku);
                foreach ($bulanMap as $namaBulan => $nomorBulan) {
                    $masaBerlaku = str_replace($namaBulan, $nomorBulan, $masaBerlaku);
                }

                $tanggalBerlaku = \Carbon\Carbon::createFromFormat('d m Y', $masaBerlaku);
            } catch (\Exception $e) {
                try {
                    $tanggalBerlaku = \Carbon\Carbon::parse($row[7]);
                } catch (\Exception $e2) {
                    $this->parent->skipped++;
                    continue;
                }
            }

            // Cek apakah kartu sudah ada
            $existing = KartuPas::where('nomor_kartu', $nomor)->first();

            if ($existing) {
                $existing->update([
                    'nama_pemegang'   => $nama,
                    'perusahaan'      => $this->currentInstansi,
                    'area_akses'      => $area,
                    'jabatan'         => $jabatan,
                    'tanggal_berlaku' => $tanggalBerlaku,
                    'status'          => $tanggalBerlaku->isPast() ? 'kadaluarsa' : 'aktif',
                ]);
                $this->parent->updated++;
            } else {
                KartuPas::create([
                    'permohonan_id'   => null,
                    'nomor_kartu'     => $nomor,
                    'nama_pemegang'   => $nama,
                    'perusahaan'      => $this->currentInstansi,
                    'area_akses'      => $area,
                    'jabatan'         => $jabatan,
                    'tanggal_terbit'  => now(),
                    'tanggal_berlaku' => $tanggalBerlaku,
                    'status'          => $tanggalBerlaku->isPast() ? 'kadaluarsa' : 'aktif',
                ]);
                $this->parent->imported++;
            }
        }
    }
}