<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Instansi;
use App\Models\KartuPas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstansiKuotaTerpakaiTest extends TestCase
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

    public function test_instansi_terpakai_counts_registered_cards_correctly(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'KANTOR IMIGRASI',
            'kuota'         => 10,
            'is_active'     => true,
        ]);

        // Buat kartu dengan instansi_id null (seperti hasil import atau seeder lama)
        KartuPas::create([
            'nomor_kartu'     => 'I.IMG.MKQ.000666',
            'nama_pemegang'   => 'ZULHAMSYAH',
            'perusahaan'      => 'KANTOR IMIGRASI',
            'area_akses'      => 'ABCP',
            'tanggal_terbit'  => now()->subMonths(6),
            'tanggal_berlaku' => now()->subMonths(2),
            'status'          => 'kadaluarsa',
        ]);

        KartuPas::create([
            'nomor_kartu'     => 'I.IMG.MKQ.000667',
            'nama_pemegang'   => 'RONALD WARAMORI',
            'perusahaan'      => 'KANTOR IMIGRASI',
            'area_akses'      => 'AC',
            'tanggal_terbit'  => now()->subMonths(1),
            'tanggal_berlaku' => now()->addMonths(5),
            'status'          => 'aktif',
        ]);

        // Verifikasi sinkronisasi otomatis
        Instansi::syncUnlinkedKartuPas();

        $kartu1 = KartuPas::where('nomor_kartu', 'I.IMG.MKQ.000666')->first();
        $kartu2 = KartuPas::where('nomor_kartu', 'I.IMG.MKQ.000667')->first();

        $this->assertEquals($instansi->id, $kartu1->instansi_id);
        $this->assertEquals($instansi->id, $kartu2->instansi_id);

        // Akses halaman data instansi
        $response = $this->actingAs($this->admin)->get(route('administrator.instansi.index'));

        $response->assertStatus(200);
        $response->assertSee('KANTOR IMIGRASI');
        $response->assertSee('2 Kartu');
        $response->assertSee('1 aktif');
        $response->assertSee('1 kadaluarsa/nonaktif');
        $response->assertSee('8 Kartu'); // Sisa kuota: 10 - 2 = 8
    }

    public function test_new_kartu_pas_without_instansi_id_auto_links_on_saving(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'SATUAN RADAR 244 MERAUKE',
            'kuota'         => 15,
            'is_active'     => true,
        ]);

        $kartu = KartuPas::create([
            'nomor_kartu'     => 'RADAR.001',
            'nama_pemegang'   => 'Budi Radar',
            'perusahaan'      => 'SATUAN RADAR 244 MERAUKE',
            'area_akses'      => 'A',
            'tanggal_terbit'  => now(),
            'tanggal_berlaku' => now()->addYear(),
            'status'          => 'aktif',
        ]);

        $this->assertEquals($instansi->id, $kartu->fresh()->instansi_id);
    }
}
