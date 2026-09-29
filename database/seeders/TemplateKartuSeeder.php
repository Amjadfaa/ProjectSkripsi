<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TemplateKartu;
use Illuminate\Support\Facades\File;

class TemplateKartuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Template PAS Kuning (Terminal / Kedatangan / Check-in)
        TemplateKartu::updateOrCreate(
            ['nama_template' => 'Template PAS Kuning (Terminal & Penumpang)'],
            [
                'kode_warna'        => 'kuning',
                'warna_label'       => 'Kuning (Area Publik / Terminal)',
                'warna_hex'         => '#FACC15',
                'warna_teks'        => '#000000',
                'gambar_template'   => 'template_kartu/pas_kuning.png',
                'area_akses'        => ['A', 'B', 'C'], // Kedatangan, Ruang Tunggu, Check-in
                'prioritas'         => 1,
                'is_default'        => true,
                'is_active'         => true,
                'keterangan'        => 'Template standar warna kuning untuk area publik, daerah kedatangan, ruang tunggu, dan pelaporan diri (Area A, B, C).',
                'posisi_pengaturan' => [
                    'header_title'    => 'OTORITAS BANDAR UDARA WILAYAH X MERAUKE',
                    'subheader_title' => 'BANDAR UDARA MOPAH - MERAUKE',
                    'badge_bg'        => '#fef08a',
                    'badge_text'      => '#854d0e',
                ],
            ]
        );

        // 2. Template PAS Merah (Apron / Sisi Udara Keamanan Tinggi)
        TemplateKartu::updateOrCreate(
            ['nama_template' => 'Template PAS Merah (Apron & Fasilitas Vital)'],
            [
                'kode_warna'        => 'merah',
                'warna_label'       => 'Merah (Apron / Keamanan Tinggi)',
                'warna_hex'         => '#EF4444',
                'warna_teks'        => '#FFFFFF',
                'gambar_template'   => 'template_kartu/pas_merah.png',
                'area_akses'        => ['P', 'L', 'M', 'N', 'O', 'R', 'T', 'V'], // Apron, Tower, Fasilitas Vital
                'prioritas'         => 10,
                'is_default'        => false,
                'is_active'         => true,
                'keterangan'        => 'Template warna merah keamanan khusus untuk area sisi udara (Apron), Tower, Radar, dan instalasi vital bandara.',
                'posisi_pengaturan' => [
                    'header_title'    => 'OTORITAS BANDAR UDARA WILAYAH X MERAUKE',
                    'subheader_title' => 'BANDAR UDARA MOPAH - MERAUKE',
                    'badge_bg'        => '#fee2e2',
                    'badge_text'      => '#991b1b',
                ],
            ]
        );

        // 3. Template PAS Biru (Gudang Kargo & Penanganan Bagasi)
        TemplateKartu::updateOrCreate(
            ['nama_template' => 'Template PAS Biru (Kargo & Penyiapan Bagasi)'],
            [
                'kode_warna'        => 'biru',
                'warna_label'       => 'Biru (Kargo / Airside Logistik)',
                'warna_hex'         => '#2563EB',
                'warna_teks'        => '#FFFFFF',
                'gambar_template'   => 'template_kartu/pas_biru.png',
                'area_akses'        => ['F', 'G', 'U'], // Kargo luar/dalam, Penyiapan Bagasi
                'prioritas'         => 5,
                'is_default'        => false,
                'is_active'         => true,
                'keterangan'        => 'Template warna biru untuk operasional penanganan kargo, pergudangan, dan penyiapan bagasi tercatat (Airside).',
                'posisi_pengaturan' => [
                    'header_title'    => 'OTORITAS BANDAR UDARA WILAYAH X MERAUKE',
                    'subheader_title' => 'BANDAR UDARA MOPAH - MERAUKE',
                    'badge_bg'        => '#dbeafe',
                    'badge_text'      => '#1e40af',
                ],
            ]
        );
    }
}
