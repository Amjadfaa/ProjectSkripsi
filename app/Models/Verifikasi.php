<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    use HasFactory;

    protected $table = 'verifikasi';

    protected $fillable = [
        'berkas_id',
        'verifikator_id',
        'is_verified',
        'catatan',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relasi ke Berkas
    public function berkas()
    {
        return $this->belongsTo(Berkas::class);
    }

    // Relasi ke User (verifikator)
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}