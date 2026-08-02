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
        'permohonan_id',
        'nomor_kartu',
        'email',
        'nama_pemegang',
        'perusahaan',
        'area_akses',
        'jabatan',
        'tanggal_terbit',
        'tanggal_berlaku',
        'status',
        'keterangan_nonaktif',
        'catatan_nonaktif',
    ];

    protected $casts = [
        'tanggal_terbit'  => 'date',
        'tanggal_berlaku' => 'date',
    ];

    // Relasi ke Instansi
    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    // Relasi ke Permohonan
    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    // Cek apakah kartu sudah kadaluarsa
    public function isKadaluarsa(): bool
    {
        return $this->tanggal_berlaku->isPast();
    }
}