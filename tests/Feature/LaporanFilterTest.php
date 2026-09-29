<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Instansi;
use App\Models\KartuPas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LaporanFilterTest extends TestCase
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

    public function test_laporan_page_loads_with_default_year(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Kartu PAS');
        $response->assertSee('Pilih Tahun');
        $response->assertSee('Pilih Bulan');
        $response->assertSee('Semua Bulan (Januari - Desember)');
    }

    public function test_laporan_page_filters_by_year_and_month(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'KANTOR IMIGRASI',
            'kuota'         => 41,
            'is_active'     => true,
        ]);

        KartuPas::create([
            'instansi_id'     => $instansi->id,
            'nomor_kartu'     => 'I.IMG.MKQ.000666',
            'nama_pemegang'   => 'ZULHAMSYAH',
            'perusahaan'      => 'KANTOR IMIGRASI',
            'area_akses'      => 'ABCP',
            'tanggal_terbit'  => '2026-06-01',
            'tanggal_berlaku' => '2026-05-30',
            'status'          => 'kadaluarsa',
            'tipe_permohonan' => 'baru',
        ]);

        KartuPas::create([
            'instansi_id'     => $instansi->id,
            'nomor_kartu'     => 'I.IMG.MKQ.000667',
            'nama_pemegang'   => 'RONALD WARAMORI',
            'perusahaan'      => 'KANTOR IMIGRASI',
            'area_akses'      => 'AC',
            'tanggal_terbit'  => '2026-06-01',
            'tanggal_berlaku' => '2026-05-30',
            'status'          => 'aktif',
            'tipe_permohonan' => 'perpanjangan',
        ]);

        // Filter untuk bulan 6 (Juni) tahun 2026
        $response = $this->actingAs($this->admin)->get(route('administrator.laporan.index', [
            'tahun' => 2026,
            'bulan' => 6,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Rincian Kartu PAS - Bulan Juni 2026');
        $response->assertSee('ZULHAMSYAH');
        $response->assertSee('RONALD WARAMORI');
        $response->assertSee('I.IMG.MKQ.000666');
    }

    public function test_export_pdf_and_excel_accept_month_filter(): void
    {
        $responsePdf = $this->actingAs($this->admin)->get(route('administrator.laporan.export.pdf', [
            'tahun' => 2026,
            'bulan' => 6,
        ]));
        $responsePdf->assertStatus(200);

        $responseExcel = $this->actingAs($this->admin)->get(route('administrator.laporan.export.excel', [
            'tahun' => 2026,
            'bulan' => 6,
        ]));
        $responseExcel->assertStatus(200);
    }

    public function test_laporan_detail_kartu_search_and_status_filter(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'PT GARUDA INDONESIA',
            'kuota'         => 50,
            'is_active'     => true,
        ]);

        KartuPas::create([
            'instansi_id'     => $instansi->id,
            'nomor_kartu'     => 'GIA-001',
            'nama_pemegang'   => 'BUDI SANTOSO',
            'perusahaan'      => 'PT GARUDA INDONESIA',
            'jabatan'         => 'Pilot',
            'area_akses'      => 'AB',
            'tanggal_terbit'  => '2026-03-10',
            'tanggal_berlaku' => '2027-03-10',
            'status'          => 'aktif',
            'tipe_permohonan' => 'baru',
        ]);

        KartuPas::create([
            'instansi_id'     => $instansi->id,
            'nomor_kartu'     => 'GIA-002',
            'nama_pemegang'   => 'SITI AMINAH',
            'perusahaan'      => 'PT GARUDA INDONESIA',
            'jabatan'         => 'Flight Attendant',
            'area_akses'      => 'A',
            'tanggal_terbit'  => '2026-03-15',
            'tanggal_berlaku' => '2027-03-15',
            'status'          => 'tidak_aktif',
            'tipe_permohonan' => 'baru',
        ]);

        // Search test: BUDI
        $responseSearch = $this->actingAs($this->admin)->get(route('administrator.laporan.index', [
            'tahun'  => 2026,
            'search' => 'BUDI',
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('BUDI SANTOSO');
        $responseSearch->assertDontSee('SITI AMINAH');

        // Status test: nonaktif
        $responseStatus = $this->actingAs($this->admin)->get(route('administrator.laporan.index', [
            'tahun'  => 2026,
            'status' => 'nonaktif',
        ]));
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('SITI AMINAH');
        $responseStatus->assertDontSee('BUDI SANTOSO');
    }

    public function test_spa_ajax_request_returns_laporan_table_partial(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'PT LION AIR',
            'kuota'         => 30,
            'is_active'     => true,
        ]);

        KartuPas::create([
            'instansi_id'     => $instansi->id,
            'nomor_kartu'     => 'LION-777',
            'nama_pemegang'   => 'HENDRA WIJAYA',
            'perusahaan'      => 'PT LION AIR',
            'jabatan'         => 'Teknisi',
            'area_akses'      => 'ABCD',
            'tanggal_terbit'  => '2026-05-12',
            'tanggal_berlaku' => '2027-05-12',
            'status'          => 'aktif',
            'tipe_permohonan' => 'baru',
        ]);

        // Request with X-SPA header
        $response = $this->actingAs($this->admin)->get(route('administrator.laporan.index', [
            'tahun' => 2026,
            'bulan' => 5,
        ]), [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-SPA'            => 'true',
        ]);

        $response->assertStatus(200);
        // Should contain the table rows and spaKpiData, but NOT the full app layout
        $response->assertSee('LION-777');
        $response->assertSee('HENDRA WIJAYA');
        $response->assertSee('spaKpiData');
        $response->assertDontSee('<x-app-layout>');
        $response->assertDontSee('Tren Kartu PAS per Bulan');
    }
}
