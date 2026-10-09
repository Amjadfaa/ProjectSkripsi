<?php

namespace App\Imports;

use App\Models\KartuPas;
use App\Models\Instansi;
use App\Models\Jabatan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class KartuPasImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public $imported = 0;
    public $skipped  = 0;
    public $updated  = 0;
    public $quotaMap = [];
    public $quotaMapRaw = [];

    public function sheets(): array
    {
        // Mendukung hingga 24 sheet (untuk sheet bulanan atau per kategori)
        $sheets = [];
        for ($i = 0; $i < 24; $i++) {
            $sheets[$i] = new KartuPasSheetImport($this, $i);
        }
        return $sheets;
    }

    public function onUnknownSheet($sheetName)
    {
        // Abaikan sheet yang tidak ditemukan
    }
}

class KartuPasSheetImport implements ToCollection, WithEvents
{
    protected $parent;
    protected $sheetIndex;
    protected $sheetTitle = '';
    protected $currentInstansi = '';

    public function __construct($parent, $sheetIndex = 0)
    {
        $this->parent     = $parent;
        $this->sheetIndex = $sheetIndex;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetTitle = $event->sheet->getTitle();
            },
        ];
    }

    public function collection(Collection $rows)
    {
        // 1. Cek apakah ini sheet Master Kuota (seperti sheet 'DATA PAS' atau tabel kuota instansi)
        if ($this->isQuotaMasterSheet($rows)) {
            $this->processQuotaMasterSheet($rows);
            return;
        }

        $monthFromSheet = $this->detectMonthFromSheetTitle();

        foreach ($rows as $index => $row) {
            // Skip baris kosong
            if ($row->filter()->isEmpty()) continue;

            // Deteksi nama instansi (header section di Kolom C, D, atau E)
            $c3 = trim((string)($row[3] ?? ''));
            $c4 = trim((string)($row[4] ?? ''));
            $c5 = trim((string)($row[5] ?? ''));
            $c6 = trim((string)($row[6] ?? ''));

            if (!empty($c3) && empty($c4) && empty($c5) && !is_numeric($c3) && !str_contains($c3, 'NAMA') && !str_contains($c3, 'NO')) {
                $this->currentInstansi = strtoupper($c3);
                continue;
            }
            if (!empty($c4) && empty($c5) && empty($c6) && !is_numeric($c4) && !str_contains($c4, 'NAMA') && !str_contains($c4, 'NO') && !str_contains($c4, 'BACK')) {
                $this->currentInstansi = strtoupper($c4);
                continue;
            }

            // Skip header baris
            if ($c3 === 'NAMA' || $c4 === 'NAMA' || $c3 === 'NO' || ($row[2] ?? '') === 'NO') continue;

            // Cari kolom yang memuat nomor registrasi kartu PAS secara dinamis
            $colCardIdx = null;
            foreach ($row as $colIdx => $val) {
                $valStr = trim((string)$val);
                if (str_contains($valStr, '.') && preg_match('/[0-9]{3,}/', $valStr) && !str_contains($valStr, ' ')) {
                    $colCardIdx = $colIdx;
                    break;
                }
            }

            if ($colCardIdx === null) continue;

            $nomor       = trim((string)$row[$colCardIdx]);
            $nama        = trim((string)($row[$colCardIdx - 1] ?? ''));
            $areaRaw     = trim((string)($row[$colCardIdx + 1] ?? ''));
            $area        = KartuPas::normalizeAreaAkses($areaRaw);
            $jabatanRaw  = trim((string)($row[$colCardIdx + 2] ?? ''));
            $jabatan     = trim($jabatanRaw, " `\t\n\r\0\x0B");
            if ($jabatan === '-') $jabatan = '';
            $masaBerlaku = $row[$colCardIdx + 3] ?? null;
            $ketRaw      = trim((string)($row[$colCardIdx + 4] ?? ''));

            // Validasi nama & nomor
            if (empty($nomor) || empty($nama) || strtoupper($nama) === 'NAMA' || is_numeric($nama)) continue;

            // Daftarkan jabatan ke master tabel jika ada
            if (!empty($jabatan)) {
                try {
                    Jabatan::firstOrCreate(['nama_jabatan' => $jabatan]);
                } catch (\Throwable $t) {}
            }

            // Parse tanggal berlaku
            $tanggalBerlaku = $this->parseTanggal($masaBerlaku, $masaBerlaku);
            if (!$tanggalBerlaku) {
                // Fallback jika format tanggal adalah akhir tahun atau tanggal terbit + 1 tahun
                $tanggalBerlaku = Carbon::create((int)date('Y'), $monthFromSheet ?? 6, 1)->addYear()->subDay()->startOfDay();
            }

            // Tentukan tipe permohonan ('perpanjangan' vs 'baru')
            $tipePermohonan = 'baru';
            $ketUpper = strtoupper($ketRaw);
            if (str_contains($ketUpper, 'PERPANJANG')) {
                $tipePermohonan = 'perpanjangan';
            }

            // Tentukan tanggal terbit berdasarkan bulan dari sheet
            $tanggalTerbit = $this->determineTanggalTerbit($tanggalBerlaku, $monthFromSheet, $row);

            // Cari atau buat instansi jika belum ada untuk memastikan relasi ID tersambung dan kuota akurat
            $instansiId = null;
            if (!empty($this->currentInstansi)) {
                $instansi = $this->findInstansiInDb($this->currentInstansi);
                $expectedQuota = $this->lookupQuota($this->currentInstansi);

                if (!$instansi) {
                    $instansi = Instansi::create([
                        'nama_instansi' => $this->currentInstansi,
                        'kuota'         => $expectedQuota ?? 10,
                        'is_active'     => true,
                    ]);
                } else {
                    // Update kuota jika di master ada nilai kuota riil dan berbeda
                    if ($expectedQuota !== null && $instansi->kuota != $expectedQuota) {
                        $instansi->update(['kuota' => $expectedQuota]);
                    }
                }
                $instansiId = $instansi->id;
            }

            // Cek apakah kartu sudah ada
            $existing = KartuPas::where('nomor_kartu', $nomor)->first();

            if ($existing) {
                $existing->update([
                    'instansi_id'     => $instansiId ?? $existing->instansi_id,
                    'nama_pemegang'   => $nama,
                    'perusahaan'      => $this->currentInstansi,
                    'area_akses'      => !empty($area) ? $area : $existing->area_akses,
                    'jabatan'         => !empty($jabatan) ? $jabatan : $existing->jabatan,
                    'tanggal_terbit'  => $tanggalTerbit,
                    'tanggal_berlaku' => $tanggalBerlaku,
                    'tipe_permohonan' => $tipePermohonan,
                    'keterangan'      => !empty($ketRaw) ? ucfirst(strtolower($ketRaw)) : ucfirst($tipePermohonan),
                    'status'          => $tanggalBerlaku->isPast() ? 'kadaluarsa' : 'aktif',
                    'updated_at'      => $tanggalTerbit,
                ]);
                $this->parent->updated++;
            } else {
                KartuPas::create([
                    'instansi_id'     => $instansiId,
                    'permohonan_id'   => null,
                    'nomor_kartu'     => $nomor,
                    'nama_pemegang'   => $nama,
                    'perusahaan'      => $this->currentInstansi,
                    'area_akses'      => $area,
                    'jabatan'         => $jabatan,
                    'tanggal_terbit'  => $tanggalTerbit,
                    'tanggal_berlaku' => $tanggalBerlaku,
                    'tipe_permohonan' => $tipePermohonan,
                    'keterangan'      => !empty($ketRaw) ? ucfirst(strtolower($ketRaw)) : ucfirst($tipePermohonan),
                    'status'          => $tanggalBerlaku->isPast() ? 'kadaluarsa' : 'aktif',
                    'created_at'      => $tanggalTerbit,
                    'updated_at'      => $tanggalTerbit,
                ]);
                $this->parent->imported++;
            }
        }
    }

    /**
     * Deteksi apakah sheet ini adalah Master Kuota Instansi (e.g. DATA PAS)
     */
    protected function isQuotaMasterSheet(Collection $rows): bool
    {
        $title = strtoupper($this->sheetTitle);
        if (str_contains($title, 'DATA PAS') || str_contains($title, 'KUOTA') || str_contains($title, 'MASTER')) {
            return true;
        }

        // Cek baris 1 s/d 10 apakah memuat tabel kuota
        $rowCount = 0;
        foreach ($rows as $row) {
            $rowCount++;
            if ($rowCount > 10) break;
            $rowStr = strtoupper(implode(' ', array_filter(array_map('strval', is_array($row) ? $row : $row->toArray()))));
            if ((str_contains($rowStr, 'KUOTA') || str_contains($rowStr, 'QUOTA')) && (str_contains($rowStr, 'INSTANSI') || str_contains($rowStr, 'PERUSAHAAN'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Memproses sheet Master Kuota (DATA PAS): update/create Instansi dengan kuota asli
     */
    protected function processQuotaMasterSheet(Collection $rows): void
    {
        $colNama = 4;  // Default Kolom E
        $colKuota = 5; // Default Kolom F
        $headerRowIdx = null;

        // Cari posisi kolom header
        foreach ($rows as $idx => $row) {
            $rowArr = is_array($row) ? $row : $row->toArray();
            foreach ($rowArr as $colIdx => $val) {
                $valUpper = strtoupper(trim((string)$val));
                if (str_contains($valUpper, 'INSTANSI') || str_contains($valUpper, 'PERUSAHAAN')) {
                    $colNama = $colIdx;
                    $headerRowIdx = $idx;
                }
                if (str_contains($valUpper, 'KUOTA') || str_contains($valUpper, 'QUOTA')) {
                    $colKuota = $colIdx;
                    $headerRowIdx = $idx;
                }
            }
            if ($headerRowIdx !== null) break;
        }

        foreach ($rows as $idx => $row) {
            if ($headerRowIdx !== null && $idx <= $headerRowIdx) continue;

            $namaRaw  = trim((string)($row[$colNama] ?? ''));
            $kuotaRaw = trim((string)($row[$colKuota] ?? ''));

            if (empty($namaRaw) || !is_numeric($kuotaRaw)) continue;

            $kuota = (int)$kuotaRaw;
            $clean = $this->normalizeName($namaRaw);

            // Simpan ke in-memory map
            $this->parent->quotaMap[$clean] = $kuota;
            $this->parent->quotaMapRaw[strtoupper($namaRaw)] = $kuota;

            // Simpan / update ke database
            $instansi = $this->findInstansiInDb($namaRaw);
            if ($instansi) {
                $instansi->update(['kuota' => $kuota]);
            } else {
                Instansi::create([
                    'nama_instansi' => $namaRaw,
                    'kuota'         => $kuota,
                    'is_active'     => true,
                ]);
            }
        }
    }

    /**
     * Normalisasi nama instansi untuk pencocokan toleran singkatan & tanda baca
     */
    public function normalizeName($str): string
    {
        $str = strtoupper(trim((string)$str));
        $str = preg_replace('/[.,\-\/\(\)]+/', ' ', $str);
        $words = explode(' ', preg_replace('/\s+/', ' ', trim($str)));
        $mapped = [];
        foreach ($words as $w) {
            if ($w === 'SATUAN') continue;
            if ($w === 'TUGAS') $w = 'SATGAS';
            if ($w === 'RADAR') $w = 'SATRAD';
            if ($w === 'WILAYAH') $w = 'WIL';
            if ($w === 'MERAUKE') $w = 'MKQ';
            if ($w === 'AERIASIA') $w = 'AEROASIA';
            $mapped[] = $w;
        }
        return implode(' ', $mapped);
    }

    /**
     * Cari instansi di DB berdasarkan nama eksak, alias, atau normalisasi
     */
    public function findInstansiInDb($name)
    {
        if (empty($name)) return null;

        $trimmed = trim($name);

        // 1. Direct match
        $instansi = Instansi::whereRaw('TRIM(LOWER(nama_instansi)) = ?', [strtolower($trimmed)])->first();
        if ($instansi) return $instansi;

        // 2. Known alias map
        $aliasMap = [
            'PT. GMF AEROASIA' => 'PT. GMF AERIASIA',
            'PT. GMF AERIASIA' => 'PT. GMF AEROASIA',
            'PT. MAF MERAUKE' => 'MAF MERAUKE',
            'MAF MERAUKE' => 'PT. MAF MERAUKE',
            'PT.LION AIR' => 'PT. LION AIR',
            'PT. LION AIR' => 'PT.LION AIR',
            'KANTOR OTBAN X' => 'KANTOR OTBAN WIL. X',
            'KANTOR OTBAN WIL. X' => 'KANTOR OTBAN X',
            'SATUAN TUGAS PRAYUDHA MAMTA SEKTOR MERAUKE' => 'SATGAS PRAYUDHA MAMTA SEKTOR MKQ',
            'SATGAS PRAYUDHA MAMTA SEKTOR MKQ' => 'SATUAN TUGAS PRAYUDHA MAMTA SEKTOR MERAUKE',
            'SATRAD 244 MERAUKE' => 'SATUAN RADAR 244 MERAUKE',
        ];
        $upper = strtoupper($trimmed);
        if (isset($aliasMap[$upper])) {
            $alias = $aliasMap[$upper];
            $instansi = Instansi::whereRaw('TRIM(LOWER(nama_instansi)) = ?', [strtolower(trim($alias))])->first();
            if ($instansi) return $instansi;
        }

        // 3. Normalized match
        $clean = $this->normalizeName($trimmed);
        $all = Instansi::all();
        foreach ($all as $item) {
            if ($this->normalizeName($item->nama_instansi) === $clean) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Cari kuota dari master quota map berdasarkan nama
     */
    public function lookupQuota($name): ?int
    {
        if (empty($name)) return null;

        $upper = strtoupper(trim($name));
        if (isset($this->parent->quotaMapRaw[$upper])) {
            return $this->parent->quotaMapRaw[$upper];
        }

        $clean = $this->normalizeName($name);
        if (isset($this->parent->quotaMap[$clean])) {
            return $this->parent->quotaMap[$clean];
        }

        $aliasMap = [
            'PT. GMF AEROASIA' => 'PT. GMF AERIASIA',
            'PT. MAF MERAUKE' => 'MAF MERAUKE',
            'PT.LION AIR' => 'PT. LION AIR',
            'KANTOR OTBAN X' => 'KANTOR OTBAN WIL. X',
            'SATUAN TUGAS PRAYUDHA MAMTA SEKTOR MERAUKE' => 'SATGAS PRAYUDHA MAMTA SEKTOR MKQ',
            'SATRAD 244 MERAUKE' => 'SATUAN RADAR 244 MERAUKE',
        ];
        if (isset($aliasMap[$upper])) {
            $aliasUpper = $aliasMap[$upper];
            if (isset($this->parent->quotaMapRaw[$aliasUpper])) {
                return $this->parent->quotaMapRaw[$aliasUpper];
            }
            $aliasClean = $this->normalizeName($aliasUpper);
            if (isset($this->parent->quotaMap[$aliasClean])) {
                return $this->parent->quotaMap[$aliasClean];
            }
        }

        return null;
    }

    /**
     * Deteksi nomor bulan (1-12) dari nama sheet di Excel jika ada
     */
    protected function detectMonthFromSheetTitle(): ?int
    {
        if (empty($this->sheetTitle)) {
            return null;
        }

        $title = strtoupper(trim($this->sheetTitle));

        $bulanMap = [
            1  => ['JANUARI', 'JANUARY', 'JAN'],
            2  => ['FEBRUARI', 'FEBRUARY', 'FEB'],
            3  => ['MARET', 'MARCH', 'MAR'],
            4  => ['APRIL', 'APR'],
            5  => ['MEI', 'MAY'],
            6  => ['JUNI', 'JUNE', 'JUN'],
            7  => ['JULI', 'JULY', 'JUL'],
            8  => ['AGUSTUS', 'AUGUST', 'AGU', 'AUG'],
            9  => ['SEPTEMBER', 'SEPT', 'SEP'],
            10 => ['OKTOBER', 'OCTOBER', 'OKT', 'OCT'],
            11 => ['NOVEMBER', 'NOV'],
            12 => ['DESEMBER', 'DECEMBER', 'DES', 'DEC'],
        ];

        foreach ($bulanMap as $bulanNum => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($title, $kw)) {
                    return $bulanNum;
                }
            }
        }

        // Cek jika title hanya angka bulan 1-12 (misal "01", "1", "06")
        if (preg_match('/^(0?[1-9]|1[0-2])$/', $title, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * Parsing tanggal masa berlaku dari string teks maupun serial number Excel
     */
    protected function parseTanggal($masaBerlaku, $rawCell = null): ?Carbon
    {
        // 1. Cek Excel serial date (angka bulat/desimal e.g. 40000 - 60000)
        if (is_numeric($rawCell) && $rawCell > 1000 && $rawCell < 100000) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawCell);
                return Carbon::instance($dt)->startOfDay();
            } catch (\Throwable $t) {}
        }

        if (empty($masaBerlaku)) return null;

        $str = strtoupper(trim((string)$masaBerlaku));
        if ($str === '' || $str === '-') return null;

        $bulanMap = [
            'JANUARI'   => '01', 'JANUARY'  => '01', 'JAN' => '01',
            'FEBRUARI'  => '02', 'FEBRUARY' => '02', 'FEB' => '02',
            'MARET'     => '03', 'MARCH'    => '03', 'MAR' => '03',
            'APRIL'     => '04', 'APR'      => '04',
            'MEI'       => '05', 'MAY'      => '05',
            'JUNI'      => '06', 'JUNE'     => '06', 'JUN' => '06',
            'JULI'      => '07', 'JULY'     => '07', 'JUL' => '07',
            'AGUSTUS'   => '08', 'AUGUST'   => '08', 'AGU' => '08', 'AUG' => '08',
            'SEPTEMBER' => '09', 'SEPT'     => '09', 'SEP' => '09',
            'OKTOBER'   => '10', 'OCTOBER'  => '10', 'OKT' => '10', 'OCT' => '10',
            'NOVEMBER'  => '11', 'NOV'      => '11',
            'DESEMBER'  => '12', 'DECEMBER' => '12', 'DES' => '12', 'DEC' => '12',
        ];

        // Ganti nama bulan teks dengan angka
        $normalized = $str;
        foreach ($bulanMap as $namaBulan => $nomorBulan) {
            $normalized = str_replace($namaBulan, $nomorBulan, $normalized);
        }

        // Coba format umum: d m Y (e.g. "30 05 2026")
        $clean = preg_replace('/[\s\-\/\.]+/', ' ', trim($normalized));
        $parts = explode(' ', $clean);

        if (count($parts) === 3) {
            $p1 = $parts[0];
            $p2 = $parts[1];
            $p3 = $parts[2];

            // Format d m Y (e.g. "30 05 2026")
            if (is_numeric($p1) && is_numeric($p2) && is_numeric($p3)) {
                $day   = (int) $p1;
                $month = (int) $p2;
                $year  = (int) $p3;
                if ($year < 100) $year += 2000;

                // Jika format Y m d (e.g. 2026 05 30)
                if ($day > 1000) {
                    $tempYear = $day;
                    $day      = $year;
                    $year     = $tempYear;
                }

                if (checkdate($month, $day, $year)) {
                    return Carbon::create($year, $month, $day)->startOfDay();
                }
            }
        }

        try {
            return Carbon::parse($str)->startOfDay();
        } catch (\Throwable $t) {
            return null;
        }
    }

    /**
     * Tentukan tanggal terbit kartu PAS berdasarkan bulan sheet
     */
    protected function determineTanggalTerbit(?Carbon $tanggalBerlaku, ?int $monthFromSheet, $row): Carbon
    {
        // 1. Tentukan target bulan penerbitan
        $month = $monthFromSheet;
        if (!$month && $tanggalBerlaku) {
            $month = $tanggalBerlaku->month;
        }
        if (!$month || $month < 1 || $month > 12) {
            $month = min(max($this->sheetIndex, 1), 12);
        }

        // 2. Tahun penerbitan: jika sheet memiliki nama bulan, gunakan tahun berjalan (2026)
        $year = (int) date('Y');
        if (!$monthFromSheet && $tanggalBerlaku) {
            if ($tanggalBerlaku->year > $year + 1) {
                $year = $tanggalBerlaku->year - 1;
            } else {
                $year = $tanggalBerlaku->year;
            }
        }

        // 3. Hari penerbitan: awal bulan terkait
        $day = 1;

        return Carbon::create($year, $month, $day)->startOfDay();
    }
}