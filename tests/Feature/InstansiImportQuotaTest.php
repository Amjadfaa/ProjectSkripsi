<?php

namespace Tests\Feature;

use App\Models\Instansi;
use App\Models\User;
use App\Imports\KartuPasImport;
use App\Imports\InstansiImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class InstansiImportQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_instansi_import_reads_data_pas_format_and_updates_quota(): void
    {
        // Instansi sudah ada tapi kuotanya masih default 10
        Instansi::create([
            'nama_instansi' => 'KANTOR IMIGRASI',
            'kuota'         => 10,
            'is_active'     => true,
        ]);

        $import = new InstansiImport();

        // Simulasi sheet DATA PAS: Col 4 (E) = Nama, Col 5 (F) = Kuota
        $rows = collect([
            ['', '', '', '', 'NAMA INSTANSI', 'KUOTA', 'TERPAKAI', 'SISA'],
            ['', '', '', 1, 'KANTOR IMIGRASI', 41, 34, 7],
            ['', '', '', 2, 'UPBU MOPAH', 263, 49, 214],
        ]);

        $import->collection($rows);

        $imigrasi = Instansi::where('nama_instansi', 'KANTOR IMIGRASI')->first();
        $this->assertEquals(41, $imigrasi->kuota, 'Kuota KANTOR IMIGRASI harus diperbarui ke 41');

        $upbu = Instansi::where('nama_instansi', 'UPBU MOPAH')->first();
        $this->assertNotNull($upbu);
        $this->assertEquals(263, $upbu->kuota, 'UPBU MOPAH baru harus memiliki kuota 263');
    }

    public function test_kartu_pas_import_real_file_sets_accurate_quotas_from_scratch(): void
    {
        $realFile = 'C:\\Users\\LENOVO\\Downloads\\PAS BLN JUNI (1).xlsx';
        if (!file_exists($realFile)) {
            $this->markTestSkipped('Real file not found');
        }

        // Database benar-benar kosong dari instansi
        $this->assertEquals(0, Instansi::count());

        $import = new KartuPasImport();
        \Maatwebsite\Excel\Facades\Excel::import($import, $realFile);

        $imigrasi = Instansi::where('nama_instansi', 'KANTOR IMIGRASI')->first();
        $this->assertNotNull($imigrasi);
        $this->assertEquals(41, $imigrasi->kuota, 'Kuota KANTOR IMIGRASI setelah import kartu pas harus 41');

        $menara = Instansi::where('nama_instansi', 'PT. MENARA GRAND PAPUA')->first();
        $this->assertNotNull($menara);
        $this->assertEquals(3, $menara->kuota, 'Kuota PT. MENARA GRAND PAPUA harus 3');

        $upbu = Instansi::where('nama_instansi', 'UPBU MOPAH')->first();
        $this->assertNotNull($upbu);
        $this->assertEquals(263, $upbu->kuota, 'Kuota UPBU MOPAH harus 263');
    }
}
