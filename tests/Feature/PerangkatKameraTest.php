<?php

namespace Tests\Feature;

use App\Models\AreaAkses;
use App\Models\CameraDevice;
use App\Models\User;
use Database\Seeders\AreaAksesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PerangkatKameraTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AreaAksesSeeder::class);

        $this->admin = User::create([
            'name'     => 'Administrator Test',
            'email'    => 'admin@test.com',
            'role'     => 'administrator',
            'password' => Hash::make('password'),
        ]);

        $this->operator = User::create([
            'name'       => 'Operator Test',
            'email'      => 'operator@test.com',
            'role'       => 'operator',
            'perusahaan' => 'Avsec',
            'password'   => Hash::make('password'),
        ]);
    }

    public function test_guest_cannot_access_perangkat_kamera(): void
    {
        $response = $this->get(route('administrator.perangkat-kamera.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_operator_cannot_access_perangkat_kamera(): void
    {
        $response = $this->actingAs($this->operator)->get(route('administrator.perangkat-kamera.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_perangkat_kamera_page(): void
    {
        CameraDevice::create([
            'nama_kamera' => 'Kamera Gate 1 - Kedatangan',
            'kode_area'   => 'A',
            'kode_akses'  => 'CAM-GATE-01',
            'tipe_scan'   => 'masuk_keluar',
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('administrator.perangkat-kamera.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Perangkat Kamera Scan');
        $response->assertSee('Kamera Gate 1 - Kedatangan');
        $response->assertSee('CAM-GATE-01');
    }

    public function test_admin_can_filter_and_search_camera(): void
    {
        CameraDevice::create([
            'nama_kamera' => 'Kamera Scanner Alpha VIP',
            'kode_area'   => 'A',
            'kode_akses'  => 'CAM-ALPHA-01',
            'tipe_scan'   => 'masuk_keluar',
            'is_active'   => true,
        ]);

        CameraDevice::create([
            'nama_kamera' => 'Kamera Cargo Airside',
            'kode_area'   => 'F',
            'kode_akses'  => 'CAM-CARGO-02',
            'tipe_scan'   => 'masuk',
            'is_active'   => false,
        ]);

        // Search by name
        $response = $this->actingAs($this->admin)->get(route('administrator.perangkat-kamera.index', ['search' => 'Cargo']));
        $response->assertStatus(200);
        $response->assertSee('Kamera Cargo Airside');
        $response->assertDontSee('Kamera Scanner Alpha VIP');

        // Filter by area
        $responseArea = $this->actingAs($this->admin)->get(route('administrator.perangkat-kamera.index', ['area' => 'A']));
        $responseArea->assertStatus(200);
        $responseArea->assertSee('Kamera Scanner Alpha VIP');
        $responseArea->assertDontSee('Kamera Cargo Airside');

        // Filter by status
        $responseStatus = $this->actingAs($this->admin)->get(route('administrator.perangkat-kamera.index', ['status' => 'nonaktif']));
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Kamera Cargo Airside');
        $responseStatus->assertDontSee('Kamera Scanner Alpha VIP');
    }

    public function test_spa_ajax_request_returns_table_partial(): void
    {
        CameraDevice::create([
            'nama_kamera' => 'Kamera Gate SPA',
            'kode_area'   => 'B',
            'kode_akses'  => 'CAM-SPA-99',
            'tipe_scan'   => 'masuk_keluar',
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('administrator.perangkat-kamera.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Kamera Gate SPA');
        $response->assertSee('CAM-SPA-99');
        $response->assertSee('spaPaginationWrapper');
    }

    public function test_admin_can_store_new_camera_device(): void
    {
        $response = $this->actingAs($this->admin)->post(route('administrator.perangkat-kamera.store'), [
            'nama_kamera' => 'Kamera Baru Terminal 2',
            'kode_area'   => 'C',
            'kode_akses'  => 'CAM-TERM-02',
            'tipe_scan'   => 'masuk_keluar',
            'is_active'   => 1,
        ]);

        $response->assertRedirect(route('administrator.perangkat-kamera.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('camera_devices', [
            'nama_kamera' => 'Kamera Baru Terminal 2',
            'kode_akses'  => 'CAM-TERM-02',
        ]);
    }

    public function test_admin_can_update_camera_device(): void
    {
        $device = CameraDevice::create([
            'nama_kamera' => 'Kamera Awal',
            'kode_area'   => 'A',
            'kode_akses'  => 'CAM-AWAL-01',
            'tipe_scan'   => 'masuk',
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('administrator.perangkat-kamera.update', $device->id), [
            'nama_kamera' => 'Kamera Diperbarui',
            'kode_area'   => 'P',
            'kode_akses'  => 'CAM-AWAL-01',
            'tipe_scan'   => 'masuk_keluar',
            'is_active'   => 0,
        ]);

        $response->assertRedirect(route('administrator.perangkat-kamera.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('camera_devices', [
            'id'          => $device->id,
            'nama_kamera' => 'Kamera Diperbarui',
            'kode_area'   => 'P',
            'is_active'   => 0,
        ]);
    }

    public function test_admin_can_delete_camera_device(): void
    {
        $device = CameraDevice::create([
            'nama_kamera' => 'Kamera Dihapus',
            'kode_area'   => 'A',
            'kode_akses'  => 'CAM-HAPUS-01',
            'tipe_scan'   => 'masuk',
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('administrator.perangkat-kamera.destroy', $device->id));

        $response->assertRedirect(route('administrator.perangkat-kamera.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('camera_devices', [
            'id' => $device->id,
        ]);
    }
}
