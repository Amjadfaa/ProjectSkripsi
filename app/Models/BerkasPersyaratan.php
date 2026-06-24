<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasPersyaratan extends Model
{
    use HasFactory;

    protected $table = 'berkas_persyaratan';

    protected $fillable = [
        'nama_berkas',
        'file_path',
        'keterangan',
        'is_active',
    ];
}