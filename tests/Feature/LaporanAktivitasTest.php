<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ScanLog;
use App\Models\AreaAkses;
use App\Models\CameraDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LaporanAktivitasTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'role'     => 'administrator',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_laporan_aktivitas_page_loads_with_default_filters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.laporan-aktivitas.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Aktivitas Area Akses');
        $response->assertSee('Rentang Tanggal');
        $response->assertSee('Area Akses');
        $response->assertSee('Status Akses');
        $response->assertSee('Tipe Scan');
        $response->assertSee('Total Scan');
        $response->assertSee('Riwayat Log Aktivitas Scan Masuk / Keluar');
    }

    public function test_laporan_aktivitas_filters_by_area_and_status(): void
    {
        $camera = CameraDevice::create([
            'nama_kamera' => 'Kamera Gate A1',
            'kode_area'   => 'A',
            'kode_akses'  => 'A',
            'is_active'   => true,
        ]);

        AreaAkses::create([
            'kode'       => 'A',
            'keterangan' => 'Area Kedatangan Penumpang',
        ]);

        AreaAkses::create([
            'kode'       => 'B',
            'keterangan' => 'Area Keberangkatan',
        ]);

        ScanLog::create([
            'camera_device_id' => $camera->id,
            'kode_area'        => 'A',
            'nomor_kartu'      => 'PAS-MKQ-001',
            'nama_pemegang'    => 'RAHMAD HIDAYAT',
            'perusahaan'       => 'PT GARUDA INDONESIA',
            'status_akses'     => 'diterima',
            'tipe_aktivitas'   => 'masuk',
            'alasan'           => 'Akses diterima',
            'waktu_scan'       => now(),
        ]);

        ScanLog::create([
            'camera_device_id' => $camera->id,
            'kode_area'        => 'B',
            'nomor_kartu'      => 'PAS-MKQ-002',
            'nama_pemegang'    => 'DEWI LESTARI',
            'perusahaan'       => 'PT LION AIR',
            'status_akses'     => 'ditolak',
            'tipe_aktivitas'   => 'masuk',
            'alasan'           => 'Area tidak diizinkan',
            'waktu_scan'       => now(),
        ]);

        // Filter: Area A
        $responseAreaA = $this->actingAs($this->admin)->get(route('administrator.laporan-aktivitas.index', [
            'kode_area' => 'A',
        ]));
        $responseAreaA->assertStatus(200);
        $responseAreaA->assertSee('RAHMAD HIDAYAT');
        $responseAreaA->assertSee('PAS-MKQ-001');
        $responseAreaA->assertDontSee('DEWI LESTARI');

        // Filter: Status Ditolak
        $responseDitolak = $this->actingAs($this->admin)->get(route('administrator.laporan-aktivitas.index', [
            'status_akses' => 'ditolak',
        ]));
        $responseDitolak->assertStatus(200);
        $responseDitolak->assertSee('DEWI LESTARI');
        $responseDitolak->assertSee('PAS-MKQ-002');
        $responseDitolak->assertDontSee('RAHMAD HIDAYAT');
    }

    public function test_laporan_aktivitas_search_by_card_number_and_name(): void
    {
        ScanLog::create([
            'kode_area'      => 'C',
            'nomor_kartu'    => 'PAS-VIP-999',
            'nama_pemegang'  => 'ANTON WIBAWA',
            'perusahaan'     => 'OTBAN WIL X',
            'status_akses'   => 'diterima',
            'tipe_aktivitas' => 'masuk',
            'alasan'         => 'Akses VIP',
            'waktu_scan'     => now(),
        ]);

        $responseSearch = $this->actingAs($this->admin)->get(route('administrator.laporan-aktivitas.index', [
            'search' => 'VIP-999',
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('ANTON WIBAWA');
        $responseSearch->assertSee('PAS-VIP-999');
    }

    public function test_laporan_aktivitas_export_excel_and_pdf(): void
    {
        $responseExcel = $this->actingAs($this->admin)->get(route('administrator.laporan-aktivitas.export.excel', [
            'start_date' => date('Y-m-01'),
            'end_date'   => date('Y-m-d'),
        ]));
        $responseExcel->assertStatus(200);

        $responsePdf = $this->actingAs($this->admin)->get(route('administrator.laporan-aktivitas.export.pdf', [
            'start_date' => date('Y-m-01'),
            'end_date'   => date('Y-m-d'),
        ]));
        $responsePdf->assertStatus(200);
    }

    public function test_spa_ajax_request_returns_laporan_aktivitas_json_and_table(): void
    {
        ScanLog::create([
            'kode_area'      => 'D',
            'nomor_kartu'    => 'PAS-SPA-123',
            'nama_pemegang'  => 'FARHAN ALIFI',
            'perusahaan'     => 'AIRNAV MKQ',
            'status_akses'   => 'diterima',
            'tipe_aktivitas' => 'keluar',
            'alasan'         => 'Selesai tugas',
            'waktu_scan'     => now(),
        ]);

        // Request with XMLHttpRequest / JSON Accept
        $response = $this->actingAs($this->admin)->get(route('administrator.laporan-aktivitas.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept'           => 'application/json',
            'X-SPA'            => 'true',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'table_html',
            'totalScan',
            'totalMasuk',
            'totalKeluar',
            'totalDiterima',
            'totalDitolak',
            'chartLabels',
            'chartValues',
            'trendLabels',
            'trendMasuk',
            'trendKeluar',
            'trendDitolak',
        ]);

        $jsonData = $response->json();
        $this->assertStringContainsString('PAS-SPA-123', $jsonData['table_html']);
        $this->assertStringContainsString('FARHAN ALIFI', $jsonData['table_html']);
        $this->assertStringContainsString('spaPaginationWrapper', $jsonData['table_html']);
    }
}
