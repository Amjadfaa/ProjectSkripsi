<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraDevice extends Model
{
    use HasFactory;

    protected $table = 'camera_devices';

    protected $fillable = [
        'nama_kamera',
        'kode_area',
        'kode_akses',
        'tipe_scan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function areaAkses()
    {
        return $this->belongsTo(AreaAkses::class, 'kode_area', 'kode');
    }

    public function scanLogs()
    {
        return $this->hasMany(ScanLog::class, 'camera_device_id');
    }
}
