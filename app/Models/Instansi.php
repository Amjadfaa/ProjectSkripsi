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
    
    public function kartuPas()
    {
        return $this->hasMany(\App\Models\KartuPas::class, 'perusahaan', 'nama_instansi');
    }
}

