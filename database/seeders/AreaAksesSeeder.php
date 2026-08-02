<?php

namespace Database\Seeders;

use App\Models\AreaAkses;
use Illuminate\Database\Seeder;

class AreaAksesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'A', 'keterangan' => 'Daerah Kedatangan (Arrival) Penumpang'],
            ['kode' => 'B', 'keterangan' => 'Ruang Tunggu Keberangkatan (Boarding Lounge)'],
            ['kode' => 'C', 'keterangan' => 'Daerah Pelaporan Diri (Check-in)'],
            ['kode' => 'F', 'keterangan' => 'Bagian Luar Gudang Kargo (Kade)'],
            ['kode' => 'G', 'keterangan' => 'Bagian Dalam Gudang Kargo'],
            ['kode' => 'L', 'keterangan' => 'Gedung Listrik (Main Power House)'],
            ['kode' => 'M', 'keterangan' => 'Daerah Fasilitas Meteorologi'],
            ['kode' => 'N', 'keterangan' => 'Gedung Daerah Peralatan Navigasi & Telekomunikasi'],
            ['kode' => 'O', 'keterangan' => 'Daerah Fasilitas Suplai Bahan Bakar (Fuel Supply)'],
            ['kode' => 'P', 'keterangan' => 'Platform / Apron Area'],
            ['kode' => 'R', 'keterangan' => 'Gedung Radar'],
            ['kode' => 'T', 'keterangan' => 'Tower'],
            ['kode' => 'U', 'keterangan' => 'Daerah Penyiapan Bagasi Tercatat (Airside)'],
            ['kode' => 'V', 'keterangan' => 'Seluruh Daerah Fasilitas Vital Bandar Udara (Tower, Radar, Listrik, dll.)'],
        ];

        foreach ($data as $item) {
            AreaAkses::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
