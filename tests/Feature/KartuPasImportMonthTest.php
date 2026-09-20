<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\KartuPas;
use App\Models\Instansi;
use App\Imports\KartuPasImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KartuPasImportMonthTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_kartu_pas_detects_months_january_to_june_correctly()
    {
        // Buat file Excel tiruan dengan sheet Januari sampai Juni
        $spreadsheet = new Spreadsheet();
        
        $months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI'];

        foreach ($months as $idx => $mName) {
            if ($idx === 0) {
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($mName);
            } else {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle($mName);
            }

            // Header instansi
            $sheet->setCellValue('D1', 'PT ANGKASA PURA');
            // Header kolom
            $sheet->setCellValue('D2', 'NAMA');
            $sheet->setCellValue('E2', 'NO REG');
            $sheet->setCellValue('F2', 'AREA');
            $sheet->setCellValue('G2', 'JABATAN');
            $sheet->setCellValue('H2', 'MASA BERLAKU');

            // Baris data kartu (1 kartu per bulan)
            $bulanNum = $idx + 1;
            $sheet->setCellValue('D3', "Pegawai Bulan $mName");
            $sheet->setCellValue('E3', "PAS.00$bulanNum.2026");
            $sheet->setCellValue('F3', "A, B, C");
            $sheet->setCellValue('G3', "Staff");
            $sheet->setCellValue('H3', "28 $mName 2026");
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'test_kartu_pas_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'kartu_pas_jan_jun.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $admin = User::factory()->create([
            'role' => 'administrator',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('administrator.import.kartu-pas'), [
                'file' => $uploadedFile,
            ]);

        $response->assertRedirect(route('administrator.kartu-pas.index'));
        $response->assertSessionHas('success');

        // Verifikasi bahwa 6 kartu berhasil diimport
        $this->assertEquals(6, KartuPas::count());

        // Verifikasi bahwa masing-masing kartu memiliki tanggal_terbit sesuai bulannya (1 sampai 6)
        for ($b = 1; $b <= 6; $b++) {
            $kartu = KartuPas::where('nomor_kartu', "PAS.00$b.2026")->first();
            $this->assertNotNull($kartu, "Kartu bulan $b harus ada");
            
            // Bulan tanggal_terbit HARUS $b (Januari s/d Juni)
            $this->assertEquals($b, (int)$kartu->tanggal_terbit->format('m'), "Kartu bulan $b tanggal_terbitnya harus bulan $b");
            $this->assertEquals('2026', $kartu->tanggal_terbit->format('Y'));
            
            // Bulan tanggal_berlaku HARUS $b
            $this->assertEquals($b, (int)$kartu->tanggal_berlaku->format('m'));
        }

        // Pastikan TIDAK ADA kartu yang terbaca di bulan 9 (September)
        $kartuSeptember = KartuPas::whereMonth('tanggal_terbit', 9)->count();
        $this->assertEquals(0, $kartuSeptember, 'Tidak boleh ada kartu yang terbaca di bulan 9 September');

        @unlink($tempPath);
    }

    public function test_import_kartu_pas_single_sheet_detects_months_from_dates_correctly()
    {
        // File Excel satu sheet bernama "Sheet1" dengan baris berisi tanggal bulan 1 sampai 6
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sheet1');

        $sheet->setCellValue('D1', 'PT ANGKASA PURA');
        $sheet->setCellValue('D2', 'NAMA');
        $sheet->setCellValue('E2', 'NO REG');
        $sheet->setCellValue('F2', 'AREA');
        $sheet->setCellValue('G2', 'JABATAN');
        $sheet->setCellValue('H2', 'MASA BERLAKU');

        $dates = [
            1 => '15 JANUARI 2026',
            2 => '20 FEBRUARI 2026',
            3 => '10 MARET 2026',
            4 => '25 APRIL 2026',
            5 => '30 MEI 2026',
            6 => '05 JUNI 2026',
        ];

        $rowIdx = 3;
        foreach ($dates as $b => $dateStr) {
            $sheet->setCellValue("D$rowIdx", "Karyawan Bulan $b");
            $sheet->setCellValue("E$rowIdx", "PAS.SINGLE.00$b");
            $sheet->setCellValue("F$rowIdx", "A");
            $sheet->setCellValue("G$rowIdx", "Teknisi");
            $sheet->setCellValue("H$rowIdx", $dateStr);
            $rowIdx++;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'test_single_sheet_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'kartu_pas_single.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $admin = User::factory()->create([
            'role' => 'administrator',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('administrator.import.kartu-pas'), [
                'file' => $uploadedFile,
            ]);

        $response->assertRedirect(route('administrator.kartu-pas.index'));

        // Verifikasi bahwa 6 kartu berhasil diimport dengan bulan 1..6
        for ($b = 1; $b <= 6; $b++) {
            $kartu = KartuPas::where('nomor_kartu', "PAS.SINGLE.00$b")->first();
            $this->assertNotNull($kartu);
            $this->assertEquals($b, (int)$kartu->tanggal_terbit->format('m'), "Kartu $b harus memiliki tanggal_terbit di bulan $b");
            $this->assertEquals('2026', $kartu->tanggal_terbit->format('Y'));
        }

        $this->assertEquals(0, KartuPas::whereMonth('tanggal_terbit', 9)->count());

        @unlink($tempPath);
    }
}
