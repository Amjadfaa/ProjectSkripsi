<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaAkses extends Model
{
    use HasFactory;

    protected $table = 'area_akses';

    protected $fillable = [
        'kode',
        'keterangan',
    ];
}
