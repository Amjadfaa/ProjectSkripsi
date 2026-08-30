<?php

namespace Tests\Feature;

use App\Models\CameraDevice;
use App\Models\Instansi;
use App\Models\KartuPas;
use App\Models\AreaAkses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ScanExpiredCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_card_cannot_be_scanned_and_returns_expired_information()
    {
        $device = CameraDevice::create([
            'nama_kamera' => 'Kamera Pintu 1',
            'kode_akses'  => 'CAM-P1-001',
            'kode_area'   => '1',
            'tipe_scan'   => 'masuk',
            'is_active'   => true,
        ]);

        $instansi = Instansi::create([
            'nama_instansi' => 'PT Garuda Maintenance',
            'kuota'         => 10,
            'is_active'     => true,
        ]);

        // Kartu yang sudah kadaluarsa (tanggal_berlaku di masa lalu)
        $kartuExpired = KartuPas::create([
            'instansi_id'     => $instansi->id,
            'perusahaan'      => $instansi->nama_instansi,
            'nomor_kartu'     => 'PAS-EXP-001',
            'nama_pemegang'   => 'Budi Santoso',
            'area_akses'      => '1, 2',
            'jabatan'         => 'Teknisi',
            'tanggal_terbit'  => Carbon::now()->subMonths(6),
            'tanggal_berlaku' => Carbon::now()->subDays(5),
            'status'          => 'aktif', // status awalnya aktif tapi tanggal_berlaku lewat
        ]);

        // Scan the expired card
        $response = $this->withSession(['camera_device_id' => $device->id])
            ->postJson(route('scan.process'), [
                'qr_code' => 'PAS-EXP-001',
                'tipe_aktivitas' => 'masuk',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'       => false,
            'status'        => 'ditolak',
            'is_kadaluarsa' => true,
        ]);

        $this->assertStringContainsString('Kadaluarsa', $response->json('message'));
        $this->assertStringContainsString('Kadaluarsa', $response->json('alasan'));

        // Status kartu otomatis berubah jadi 'kadaluarsa'
        $kartuExpired->refresh();
        $this->assertEquals('kadaluarsa', $kartuExpired->status);
    }

    public function test_valid_active_card_can_be_scanned_successfully()
    {
        $device = CameraDevice::create([
            'nama_kamera' => 'Kamera Pintu 1',
            'kode_akses'  => 'CAM-P1-002',
            'kode_area'   => '1',
            'tipe_scan'   => 'masuk',
            'is_active'   => true,
        ]);

        $instansi = Instansi::create([
            'nama_instansi' => 'PT Angkasa Pura',
            'kuota'         => 10,
            'is_active'     => true,
        ]);

        $kartuValid = KartuPas::create([
            'instansi_id'     => $instansi->id,
            'perusahaan'      => $instansi->nama_instansi,
            'nomor_kartu'     => 'PAS-VAL-001',
            'nama_pemegang'   => 'Siti Aminah',
            'area_akses'      => '1, 2, 3',
            'jabatan'         => 'Supervisor',
            'tanggal_terbit'  => Carbon::now()->subDays(10),
            'tanggal_berlaku' => Carbon::now()->addMonths(6),
            'status'          => 'aktif',
        ]);

        $response = $this->withSession(['camera_device_id' => $device->id])
            ->postJson(route('scan.process'), [
                'qr_code' => 'PAS-VAL-001',
                'tipe_aktivitas' => 'masuk',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'       => true,
            'status'        => 'diterima',
            'is_kadaluarsa' => false,
        ]);
    }
}
