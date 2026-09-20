<?php

namespace App\Imports;

use App\Models\Instansi;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class InstansiImport implements ToCollection
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $skippedNames = [];

    public function collection(Collection $rows)
    {
        // Ambil data instansi yang sudah ada di database (lowercase trim nama_instansi)
        $existingMap = Instansi::pluck('id', 'nama_instansi')->mapWithKeys(function ($id, $name) {
            return [strtolower(trim((string)$name)) => $id];
        })->toArray();

        $processedInBatch = [];

        // 1. Deteksi dinamis indeks kolom Nama, Kuota, Email, Telepon, Alamat, Status
        $nameCol    = null;
        $kuotaCol   = null;
        $emailCol   = null;
        $telpCol    = null;
        $alamatCol  = null;
        $statusCol  = null;
        $headerRowIdx = null;

        foreach ($rows as $rIdx => $row) {
            $rowArr = is_array($row) ? $row : $row->toArray();
            foreach ($rowArr as $cIdx => $val) {
                $v = strtoupper(trim((string)$val));
                if (str_contains($v, 'NAMA INSTANSI') || str_contains($v, 'NAMA PERUSAHAAN') || $v === 'INSTANSI' || $v === 'NAMA') {
                    $nameCol = $cIdx;
                    $headerRowIdx = $rIdx;
                }
                if (str_contains($v, 'KUOTA') || str_contains($v, 'QUOTA')) {
                    $kuotaCol = $cIdx;
                    $headerRowIdx = $rIdx;
                }
                if (str_contains($v, 'EMAIL')) {
                    $emailCol = $cIdx;
                }
                if (str_contains($v, 'TELEPON') || str_contains($v, 'TELP') || str_contains($v, 'HP')) {
                    $telpCol = $cIdx;
                }
                if (str_contains($v, 'ALAMAT')) {
                    $alamatCol = $cIdx;
                }
                if (str_contains($v, 'STATUS')) {
                    $statusCol = $cIdx;
                }
            }
            if ($headerRowIdx !== null && $nameCol !== null && $kuotaCol !== null) {
                break;
            }
        }

        foreach ($rows as $index => $row) {
            $rowCol = is_array($row) ? collect($row) : $row;
            // Lewati baris kosong
            if ($rowCol->filter(fn($val) => !is_null($val) && trim((string)$val) !== '')->isEmpty()) {
                continue;
            }

            // Lewati baris header jika terdeteksi
            if ($headerRowIdx !== null && $index <= $headerRowIdx) {
                continue;
            }

            $namaInstansi = '';
            $kuotaRaw     = null;
            $email        = null;
            $telepon      = null;
            $alamat       = null;
            $statusRaw    = null;
            $isActive     = true;

            if ($nameCol !== null && $kuotaCol !== null) {
                // Gunakan kolom yang terdeteksi secara otomatis (misal format DATA PAS: Col 4 & 5, atau Template: Col 1 & 2)
                $namaInstansi = trim((string)($row[$nameCol] ?? ''));
                $kuotaRaw     = trim((string)($row[$kuotaCol] ?? ''));
                if ($emailCol !== null)  $email = trim((string)($row[$emailCol] ?? ''));
                if ($telpCol !== null)   $telepon = trim((string)($row[$telpCol] ?? ''));
                if ($alamatCol !== null) $alamat = trim((string)($row[$alamatCol] ?? ''));
                if ($statusCol !== null) $statusRaw = trim((string)($row[$statusCol] ?? ''));
            } else {
                // Fallback deteksi posisi kolom
                $col0 = trim((string)($row[0] ?? ''));
                $col1 = trim((string)($row[1] ?? ''));

                // Jika baris header
                $headerKeywords = ['NAMA', 'INSTANSI', 'PERUSAHAAN', 'KUOTA', 'NO', 'NO.'];
                $col0Upper = strtoupper($col0);
                $col1Upper = strtoupper($col1);
                $isHeader = false;
                foreach ($headerKeywords as $kw) {
                    if ($col0Upper === $kw || str_contains($col0Upper, $kw) || $col1Upper === $kw || str_contains($col1Upper, $kw)) {
                        $isHeader = true;
                        break;
                    }
                }
                if ($isHeader && !is_numeric($col0)) {
                    continue;
                }

                // Cek format DATA PAS (Col 4 = Nama, Col 5 = Kuota)
                $col4 = trim((string)($row[4] ?? ''));
                $col5 = trim((string)($row[5] ?? ''));
                if (!empty($col4) && is_numeric($col5) && empty($col0) && empty($col1)) {
                    $namaInstansi = $col4;
                    $kuotaRaw     = $col5;
                } elseif (is_numeric($col0) && !empty($col1)) {
                    // Template format: Col 0 = No, Col 1 = Nama
                    $namaInstansi = $col1;
                    $kuotaRaw     = trim((string)($row[2] ?? ''));
                    $email        = trim((string)($row[3] ?? ''));
                    $telepon      = trim((string)($row[4] ?? ''));
                    $alamat       = trim((string)($row[5] ?? ''));
                    $statusRaw    = trim((string)($row[6] ?? ''));
                } else {
                    // 2-column format: Col 0 = Nama
                    $namaInstansi = $col0;
                    $kuotaRaw     = trim((string)($row[1] ?? ''));
                    $email        = trim((string)($row[2] ?? ''));
                    $telepon      = trim((string)($row[3] ?? ''));
                    $alamat       = trim((string)($row[4] ?? ''));
                    $statusRaw    = trim((string)($row[5] ?? ''));
                }
            }

            if (empty($namaInstansi)) {
                continue;
            }

            // Abaikan jika ternyata masih baris header judul kolom
            $namaUpper = strtoupper($namaInstansi);
            if ($namaUpper === 'NAMA INSTANSI' || $namaUpper === 'NAMA' || $namaUpper === 'INSTANSI') {
                continue;
            }

            $normalizedKey = strtolower(trim($namaInstansi));

            // Parse Kuota
            $kuota = 10;
            if (is_numeric($kuotaRaw) && (int)$kuotaRaw >= 0) {
                $kuota = (int)$kuotaRaw;
            }

            // Parse Status
            if (!empty($statusRaw)) {
                $statusLower = strtolower($statusRaw);
                if (in_array($statusLower, ['nonaktif', 'tidak aktif', '0', 'false', 'inactive', 'non-aktif'])) {
                    $isActive = false;
                }
            }

            // JIKA SUDAH ADA DI DATABASE:
            // Pertahankan data kontak yang ada, namun perbarui kuota jika ada kuota resmi di file
            if (isset($existingMap[$normalizedKey])) {
                $instansiId = $existingMap[$normalizedKey];
                $existing = Instansi::find($instansiId);
                if ($existing) {
                    if (is_numeric($kuotaRaw) && (int)$kuotaRaw > 0 && $existing->kuota != $kuota) {
                        $existing->update(['kuota' => $kuota]);
                    }
                }
                $this->skipped++;
                $this->skippedNames[] = $namaInstansi;
                continue;
            }

            if (isset($processedInBatch[$normalizedKey])) {
                $this->skipped++;
                $this->skippedNames[] = $namaInstansi;
                continue;
            }

            // Simpan data instansi baru
            $instansi = Instansi::create([
                'nama_instansi' => $namaInstansi,
                'kuota'         => $kuota,
                'email'         => !empty($email) ? $email : null,
                'telepon'       => !empty($telepon) ? $telepon : null,
                'alamat'        => !empty($alamat) ? $alamat : null,
                'is_active'     => $isActive,
            ]);

            $existingMap[$normalizedKey]      = $instansi->id;
            $processedInBatch[$normalizedKey] = true;
            $this->imported++;
        }
    }
}
