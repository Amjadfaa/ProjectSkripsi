<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_instansi',
        'kuota',
        'alamat',
        'telepon',
        'email',
        'is_active',
    ];
    
    protected static function booted()
    {
        static::saved(function ($instansi) {
            // Hubungkan otomatis kartu PAS yang instansi_id nya null tapi nama perusahaannya sama
            KartuPas::whereNull('instansi_id')
                ->whereRaw('TRIM(LOWER(perusahaan)) = ?', [strtolower(trim($instansi->nama_instansi))])
                ->update(['instansi_id' => $instansi->id]);
        });
    }

    public static function syncUnlinkedKartuPas(): void
    {
        try {
            $unlinked = KartuPas::whereNull('instansi_id')
                ->whereNotNull('perusahaan')
                ->where('perusahaan', '!=', '')
                ->get();

            if ($unlinked->isNotEmpty()) {
                $instansiMap = self::all()->keyBy(function($item) {
                    return strtolower(trim($item->nama_instansi));
                });

                foreach ($unlinked as $kartu) {
                    $key = strtolower(trim($kartu->perusahaan));
                    if (isset($instansiMap[$key])) {
                        $kartu->update(['instansi_id' => $instansiMap[$key]->id]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel belum siap atau ada issue koneksi sementara
        }
    }

    public function kartuPas()
    {
        return $this->hasMany(\App\Models\KartuPas::class, 'instansi_id');
    }

    public function getSisaKuotaAttribute(): int
    {
        $aktif = $this->kartuPas()->where('status', 'aktif')->count();
        if ($this->id) {
            $aktifByName = KartuPas::whereNull('instansi_id')
                ->whereRaw('TRIM(LOWER(perusahaan)) = ?', [strtolower(trim($this->nama_instansi))])
                ->where('status', 'aktif')
                ->count();
            $aktif += $aktifByName;
        }
        return max(0, $this->kuota - $aktif);
    }
}

