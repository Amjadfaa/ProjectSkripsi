<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Instansi;
use App\Imports\InstansiImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstansiExportImportTest extends TestCase
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

    public function test_export_instansi_excel_route_works(): void
    {
        Instansi::create([
            'nama_instansi' => 'OTBAN',
            'kuota'         => 98,
            'is_active'     => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('administrator.instansi.export.excel'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'data-instansi-'));
    }

    public function test_download_template_excel_route_works(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.instansi.template.excel'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'template-import-instansi.xlsx'));
    }

    public function test_import_instansi_skips_duplicates_and_creates_new(): void
    {
        // Instansi yang sudah ada di sistem
        Instansi::create([
            'nama_instansi' => 'KANTOR IMIGRASI',
            'kuota'         => 20, // kuota awal 20
            'email'         => 'old@imigrasi.com',
            'is_active'     => true,
        ]);

        $import = new InstansiImport();

        // Simulasi baris excel:
        // Baris 1: Header
        // Baris 2: KANTOR IMIGRASI (sudah ada, kuota baru 999 di excel -> harus di-skip dan kuota lama tidak berubah)
        // Baris 3: SATUAN RADAR 244 MERAUKE (baru -> harus dibuat)
        // Baris 4: SATUAN RADAR 244 MERAUKE (duplikat di excel -> harus di-skip)
        $rows = new Collection([
            ['No', 'Nama Instansi', 'Kuota', 'Email', 'Telepon', 'Alamat', 'Status'],
            ['1', 'KANTOR IMIGRASI', '999', 'new@imigrasi.com', '123', 'Alamat Baru', 'Aktif'],
            ['2', 'SATUAN RADAR 244 MERAUKE', '15', 'radar@test.com', '08123', 'Merauke', 'Aktif'],
            ['3', 'SATUAN RADAR 244 MERAUKE', '15', 'radar@test.com', '08123', 'Merauke', 'Aktif'],
        ]);

        $import->collection($rows);

        $this->assertEquals(1, $import->imported);
        $this->assertEquals(2, $import->skipped);

        // Pastikan data lama KANTOR IMIGRASI tidak ditimpa
        $imigrasi = Instansi::where('nama_instansi', 'KANTOR IMIGRASI')->first();
        $this->assertEquals(20, $imigrasi->kuota);
        $this->assertEquals('old@imigrasi.com', $imigrasi->email);

        // Pastikan data baru SATUAN RADAR 244 MERAUKE tersimpan
        $radar = Instansi::where('nama_instansi', 'SATUAN RADAR 244 MERAUKE')->first();
        $this->assertNotNull($radar);
        $this->assertEquals(15, $radar->kuota);
        $this->assertEquals('radar@test.com', $radar->email);
    }
}
