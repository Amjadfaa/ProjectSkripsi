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