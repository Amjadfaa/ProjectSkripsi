<?php

namespace Tests\Feature;

use App\Models\CameraDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $operatorUser;
    protected CameraDevice $cameraDevice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'role' => 'administrator',
        ]);

        $this->operatorUser = User::factory()->create([
            'name' => 'Operator Test',
            'email' => 'operator@test.com',
            'role' => 'operator',
        ]);

        $this->cameraDevice = CameraDevice::create([
            'nama_kamera' => 'Kamera Gate 1',
            'kode_area' => '01',
            'tipe_scan' => 'masuk',
            'kode_akses' => 'GATE-01-SEC',
            'is_active' => true,
        ]);
    }

    public function test_operator_is_redirected_to_operator_dashboard_after_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'operator@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($this->operatorUser);
        $response->assertRedirect(route('operator.dashboard'));
    }

    public function test_operator_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->operatorUser)->get(route('operator.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Operator');
        $response->assertSee('Operator Test');
    }

    public function test_operator_can_access_kamera_index(): void
    {
        $response = $this->actingAs($this->operatorUser)->get(route('operator.kamera.index'));

        $response->assertStatus(200);
        $response->assertSee('Hubungkan Kamera');
        $response->assertSee('Kamera Gate 1');
        $response->assertSee('GATE-01-SEC');
    }

    public function test_operator_can_connect_camera_with_valid_access_code(): void
    {
        $response = $this->actingAs($this->operatorUser)->post(route('operator.kamera.connect'), [
            'kode_akses' => 'GATE-01-SEC',
        ]);

        $response->assertRedirect(route('operator.kamera.scanner'));
        $this->assertEquals($this->cameraDevice->id, session('camera_device_id'));
        $this->assertEquals('Kamera Gate 1', session('camera_name'));
        $this->assertEquals('01', session('camera_area'));
    }

    public function test_operator_cannot_connect_with_invalid_access_code(): void
    {
        $response = $this->actingAs($this->operatorUser)->post(route('operator.kamera.connect'), [
            'kode_akses' => 'INVALID-CODE-XYZ',
        ]);

        $response->assertSessionHasErrors(['kode_akses']);
        $this->assertFalse(session()->has('camera_device_id'));
    }

    public function test_scanner_redirects_to_kamera_index_if_not_connected(): void
    {
        $response = $this->actingAs($this->operatorUser)->get(route('operator.kamera.scanner'));

        $response->assertRedirect(route('operator.kamera.index'));
        $response->assertSessionHas('error');
    }

    public function test_operator_can_access_scanner_when_connected(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->withSession([
                'camera_device_id' => $this->cameraDevice->id,
                'camera_name' => $this->cameraDevice->nama_kamera,
                'camera_area' => $this->cameraDevice->kode_area,
                'camera_type' => $this->cameraDevice->tipe_scan,
            ])
            ->get(route('operator.kamera.scanner'));

        $response->assertStatus(200);
        $response->assertSee('Kamera Gate 1');
        $response->assertSee('Terminal Scanner QR');
    }

    public function test_operator_can_disconnect_camera(): void
    {
        $response = $this->actingAs($this->operatorUser)
            ->withSession([
                'camera_device_id' => $this->cameraDevice->id,
                'camera_name' => $this->cameraDevice->nama_kamera,
            ])
            ->post(route('operator.kamera.disconnect'));

        $response->assertRedirect(route('operator.kamera.index'));
        $this->assertFalse(session()->has('camera_device_id'));
    }

    public function test_operator_can_view_logs(): void
    {
        $response = $this->actingAs($this->operatorUser)->get(route('operator.kamera.logs'));

        $response->assertStatus(200);
        $response->assertSee('Log Pemindaian Scanner');
    }

    public function test_operator_cannot_access_administrator_dashboard(): void
    {
        $response = $this->actingAs($this->operatorUser)->get(route('administrator.dashboard'));

        $response->assertStatus(403);
    }

    public function test_administrator_cannot_access_operator_panel(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('operator.dashboard'));

        $response->assertStatus(403);
    }
}
