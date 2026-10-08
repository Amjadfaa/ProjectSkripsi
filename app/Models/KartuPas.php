<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuPas extends Model
{
    use HasFactory;

    protected $table = 'kartu_pas';

    protected $fillable = [
        'instansi_id',
        'nomor_kartu',
        'email',
        'nama_pemegang',
        'perusahaan',
        'area_akses',
        'jabatan',
        'tanggal_terbit',
        'tanggal_berlaku',
        'status',
        'tipe_permohonan',
        'keterangan_nonaktif',
        'catatan_nonaktif',
    ];

    protected $casts = [
        'tanggal_terbit'  => 'date',
        'tanggal_berlaku' => 'date',
    ];

    protected static function booted()
    {
        static::saving(function ($kartu) {
            // Normalisasi area_akses ke format per huruf dipisah koma (contoh: 'ABC' -> 'A, B, C')
            if (isset($kartu->area_akses)) {
                $kartu->area_akses = self::normalizeAreaAkses($kartu->area_akses);
            }

            // Daftarkan jabatan baru ke tabel master Jabatan jika belum ada
            if (!empty($kartu->jabatan) && trim($kartu->jabatan) !== '-') {
                try {
                    \App\Models\Jabatan::firstOrCreate(['nama_jabatan' => trim($kartu->jabatan)]);
                } catch (\Throwable $t) {
                    // Abaikan jika tabel jabatan belum siap/migrasi
                }
            }

            // Sinkronisasi otomatis instansi_id berdasarkan perusahaan
            if (empty($kartu->instansi_id) && !empty($kartu->perusahaan)) {
                $instansi = Instansi::whereRaw('TRIM(LOWER(nama_instansi)) = ?', [strtolower(trim($kartu->perusahaan))])->first();
                if ($instansi) {
                    $kartu->instansi_id = $instansi->id;
                }
            }
            // Sinkronisasi otomatis perusahaan berdasarkan instansi_id
            if (!empty($kartu->instansi_id) && empty($kartu->perusahaan)) {
                $instansi = Instansi::find($kartu->instansi_id);
                if ($instansi) {
                    $kartu->perusahaan = $instansi->nama_instansi;
                }
            }
        });
    }

    /**
     * Normalisasi string atau array area akses ke format huruf kapital dipisah koma dan spasi
     * Contoh: 'ABC' => 'A, B, C', ['A', 'B'] => 'A, B', 'ABCV' => 'A, B, C, V'
     */
    public static function normalizeAreaAkses($value): string
    {
        if (is_array($value)) {
            $letters = [];
            foreach ($value as $item) {
                $item = strtoupper(trim((string)$item));
                if (str_contains($item, ',')) {
                    foreach (explode(',', $item) as $part) {
                        $p = trim($part);
                        if ($p !== '') $letters = array_merge($letters, str_split($p));
                    }
                } else {
                    $clean = preg_replace('/[^A-Z0-9]/', '', $item);
                    if ($clean !== '') {
                        $letters = array_merge($letters, str_split($clean));
                    }
                }
            }
            return implode(', ', array_values(array_unique(array_filter($letters))));
        }

        if (empty($value)) return '';

        $str = strtoupper(trim((string)$value));

        if (str_contains($str, ',')) {
            $parts = array_filter(array_map('trim', explode(',', $str)));
            $letters = [];
            foreach ($parts as $part) {
                $clean = preg_replace('/[^A-Z0-9]/', '', $part);
                if (strlen($clean) > 1) {
                    $letters = array_merge($letters, str_split($clean));
                } elseif ($clean !== '') {
                    $letters[] = $clean;
                }
            }
            return implode(', ', array_values(array_unique($letters)));
        }

        preg_match_all('/[A-Z0-9]/', $str, $matches);
        if (!empty($matches[0])) {
            return implode(', ', array_values(array_unique($matches[0])));
        }

        return $str;
    }

    /**
     * Daftar area akses dalam bentuk array
     */
    public function getAreaAksesListAttribute(): array
    {
        if (empty($this->area_akses)) return [];
        return array_filter(array_map('trim', explode(',', $this->area_akses)));
    }

    // Relasi ke Instansi
    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    // Cek apakah kartu sudah kadaluarsa
    public function isKadaluarsa(): bool
    {
        if ($this->status === 'kadaluarsa') {
            return true;
        }

        return $this->tanggal_berlaku ? $this->tanggal_berlaku->isPast() : false;
    }

    /**
     * Resolusi template kartu PAS aktif berdasarkan area akses kartu
     */
    public function getTemplateKartuAttribute(): ?TemplateKartu
    {
        return TemplateKartu::resolveTemplateForKartu($this);
    }
}