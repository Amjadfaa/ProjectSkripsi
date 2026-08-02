<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    use HasFactory;

    protected $table = 'scan_logs';

    protected $fillable = [
        'camera_device_id',
        'kode_area',
        'nomor_kartu',
        'nama_pemegang',
        'perusahaan',
        'status_akses',
        'alasan',
        'waktu_scan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    public function cameraDevice()
    {
        return $this->belongsTo(CameraDevice::class, 'camera_device_id');
    }
}
