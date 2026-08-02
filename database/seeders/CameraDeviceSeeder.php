<?php

namespace Database\Seeders;

use App\Models\CameraDevice;
use Illuminate\Database\Seeder;

class CameraDeviceSeeder extends Seeder
{
    public function run(): void
    {
        $devices = [
            [
                'nama_kamera' => 'Kamera Gate Kedatangan A1',
                'kode_area'   => 'A',
                'kode_akses'  => 'CAM-AREA-A',
                'tipe_scan'   => 'masuk_keluar',
                'is_active'   => true,
            ],
            [
                'nama_kamera' => 'Kamera Gate Boarding Lounge B1',
                'kode_area'   => 'B',
                'kode_akses'  => 'CAM-AREA-B',
                'tipe_scan'   => 'masuk_keluar',
                'is_active'   => true,
            ],
            [
                'nama_kamera' => 'Kamera Gate Check-In C1',
                'kode_area'   => 'C',
                'kode_akses'  => 'CAM-AREA-C',
                'tipe_scan'   => 'masuk_keluar',
                'is_active'   => true,
            ],
            [
                'nama_kamera' => 'Kamera Gudang Kargo F1',
                'kode_area'   => 'F',
                'kode_akses'  => 'CAM-AREA-F',
                'tipe_scan'   => 'masuk_keluar',
                'is_active'   => true,
            ],
        ];

        foreach ($devices as $d) {
            CameraDevice::updateOrCreate(
                ['kode_akses' => $d['kode_akses']],
                $d
            );
        }
    }
}
