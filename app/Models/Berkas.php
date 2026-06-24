<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    protected $fillable = [
        'permohonan_id',
        'nama_berkas',
        'file_path',
        'status',
        'catatan',
    ];

    // Relasi ke Permohonan
    public function permohonan()
    {
        return $this->belongsTo(Permohonan::class);
    }

    // Relasi ke Verifikasi
    public function verifikasi()
    {
        return $this->hasOne(Verifikasi::class);
    }
}