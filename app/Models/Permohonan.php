<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    use HasFactory;

    protected $table = 'permohonan';

    protected $fillable = [
        'user_id',
        'nomor_permohonan',
        'nama_pemohon',
        'perusahaan',
        'keperluan',
        'status',
        'catatan',
    ];

    // Relasi ke User (pemohon)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Berkas
    public function berkas()
    {
        return $this->hasMany(Berkas::class);
    }

    // Relasi ke KartuPas
    public function kartuPas()
    {
        return $this->hasOne(KartuPas::class);
    }
}