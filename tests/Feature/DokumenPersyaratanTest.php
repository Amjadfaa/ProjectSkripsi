<?php

namespace Tests\Feature;

use App\Models\DokumenPersyaratan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenPersyaratanTest extends TestCase
{
    public function test_guest_cannot_access_dokumen_persyaratan(): void
    {
        $response = $this->get(route('administrator.dokumen-persyaratan.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_administrator_can_view_page(): void
    {
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $response = $this->actingAs($admin)->get(route('administrator.dokumen-persyaratan.index'));
        $response->assertStatus(200);
        $response->assertSee('Upload Dokumen Persyaratan PAS Bandara');
        $response->assertSee('Persyaratan Pembuatan PAS (Baru)');
        $response->assertSee('Persyaratan Perpanjangan PAS Bandara');
    }

    public function test_administrator_can_view_page_with_uploaded_files(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $fileBaru = UploadedFile::fake()->create('panduan_baru.pdf', 500, 'application/pdf');
        $filePerp = UploadedFile::fake()->create('panduan_perp.pdf', 300, 'application/pdf');

        $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'baru'), [
            'file' => $fileBaru,
            'deskripsi' => 'Deskripsi Baru',
        ]);
        $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'perpanjangan'), [
            'file' => $filePerp,
            'deskripsi' => 'Deskripsi Perpanjangan',
        ]);

        $dokumenBaru = DokumenPersyaratan::where('kategori', 'baru')->first();
        $dokumenPerp = DokumenPersyaratan::where('kategori', 'perpanjangan')->first();

        $response = $this->actingAs($admin)->get(route('administrator.dokumen-persyaratan.index'));
        $response->assertStatus(200);
        $response->assertSee(route('administrator.dokumen-persyaratan.preview', $dokumenBaru->id));
        $response->assertSee(route('administrator.dokumen-persyaratan.download', $dokumenBaru->id));
        $response->assertSee(route('administrator.dokumen-persyaratan.preview', $dokumenPerp->id));
        $response->assertSee(route('administrator.dokumen-persyaratan.download', $dokumenPerp->id));
    }

    public function test_administrator_can_upload_dokumen_baru(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $file = UploadedFile::fake()->create('panduan_pas_baru.pdf', 500, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'baru'), [
            'file' => $file,
            'deskripsi' => 'Panduan persyaratan lengkap penerbitan kartu PAS Baru.',
        ]);

        $response->assertRedirect(route('administrator.dokumen-persyaratan.index'));
        $response->assertSessionHas('success');

        $dokumen = DokumenPersyaratan::where('kategori', 'baru')->first();
        $this->assertNotNull($dokumen);
        $this->assertEquals('panduan_pas_baru.pdf', $dokumen->file_name);
        $this->assertTrue(Storage::disk('public')->exists($dokumen->file_path));
    }

    public function test_administrator_can_upload_dokumen_perpanjangan(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $file = UploadedFile::fake()->create('panduan_perpanjangan.pdf', 300, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'perpanjangan'), [
            'file' => $file,
            'deskripsi' => 'Panduan persyaratan perpanjangan kartu PAS Bandara.',
        ]);

        $response->assertRedirect(route('administrator.dokumen-persyaratan.index'));
        $response->assertSessionHas('success');

        $dokumen = DokumenPersyaratan::where('kategori', 'perpanjangan')->first();
        $this->assertNotNull($dokumen);
        $this->assertEquals('panduan_perpanjangan.pdf', $dokumen->file_name);
        $this->assertTrue(Storage::disk('public')->exists($dokumen->file_path));
    }

    public function test_administrator_can_download_and_preview_dokumen(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $file = UploadedFile::fake()->create('dokumen_preview.pdf', 200, 'application/pdf');
        $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'baru'), [
            'file' => $file,
        ]);

        $dokumen = DokumenPersyaratan::where('kategori', 'baru')->first();

        // Test preview
        $previewResponse = $this->actingAs($admin)->get(route('administrator.dokumen-persyaratan.preview', $dokumen->id));
        $previewResponse->assertStatus(200);

        // Test download
        $downloadResponse = $this->actingAs($admin)->get(route('administrator.dokumen-persyaratan.download', $dokumen->id));
        $downloadResponse->assertStatus(200);
    }

    public function test_administrator_can_delete_dokumen(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $file = UploadedFile::fake()->create('dokumen_hapus.pdf', 100, 'application/pdf');
        $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'baru'), [
            'file' => $file,
        ]);

        $dokumen = DokumenPersyaratan::where('kategori', 'baru')->first();
        $filePath = $dokumen->file_path;
        $this->assertTrue(Storage::disk('public')->exists($filePath));

        // Delete
        $deleteResponse = $this->actingAs($admin)->delete(route('administrator.dokumen-persyaratan.destroy', $dokumen->id));
        $deleteResponse->assertRedirect(route('administrator.dokumen-persyaratan.index'));

        $this->assertFalse(Storage::disk('public')->exists($filePath));
        $dokumen->refresh();
        $this->assertNull($dokumen->file_path);
    }

    public function test_guest_can_view_login_page_with_documents(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Dokumen Persyaratan PAS Bandara');
        $response->assertSee('Persyaratan Pembuatan PAS (Baru)');
        $response->assertSee('Persyaratan Perpanjangan PAS Bandara');
        $response->assertSee('MASUK KE SISTEM');
    }

    public function test_guest_can_download_and_preview_document_publicly(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $file = UploadedFile::fake()->create('dokumen_publik.pdf', 150, 'application/pdf');
        $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'baru'), [
            'file' => $file,
        ]);

        $dokumen = DokumenPersyaratan::where('kategori', 'baru')->first();

        // Guest logout
        auth()->logout();

        // Guest preview
        $previewResponse = $this->get(route('dokumen-persyaratan.public-preview', $dokumen->id));
        $previewResponse->assertStatus(200);

        // Guest download
        $downloadResponse = $this->get(route('dokumen-persyaratan.public-download', $dokumen->id));
        $downloadResponse->assertStatus(200);
    }

    public function test_administrator_can_upload_single_via_ajax(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $file = UploadedFile::fake()->create('ajax_single.pdf', 300, 'application/pdf');

        $response = $this->actingAs($admin)->postJson(route('administrator.dokumen-persyaratan.upload', 'baru'), [
            'file' => $file,
            'deskripsi' => 'Catatan ajax upload',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $response->assertJsonStructure([
            'status',
            'message',
            'dokumen' => ['id', 'kategori', 'file_name', 'formatted_size', 'preview_url', 'download_url'],
        ]);

        $this->assertEquals('ajax_single.pdf', $response->json('dokumen.file_name'));
    }

    public function test_administrator_can_upload_batch_two_documents_via_ajax(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $fileBaru = UploadedFile::fake()->create('batch_baru.pdf', 400, 'application/pdf');
        $filePerp = UploadedFile::fake()->create('batch_perpanjangan.docx', 250, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($admin)->postJson(route('administrator.dokumen-persyaratan.upload-batch'), [
            'file_baru' => $fileBaru,
            'deskripsi_baru' => 'Petunjuk berkas baru batch',
            'file_perpanjangan' => $filePerp,
            'deskripsi_perpanjangan' => 'Petunjuk berkas perpanjangan batch',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $response->assertJsonStructure([
            'status',
            'message',
            'documents' => [
                'baru' => ['id', 'file_name', 'file_type'],
                'perpanjangan' => ['id', 'file_name', 'file_type'],
            ],
        ]);

        $this->assertEquals('batch_baru.pdf', $response->json('documents.baru.file_name'));
        $this->assertEquals('batch_perpanjangan.docx', $response->json('documents.perpanjangan.file_name'));
    }

    public function test_administrator_can_delete_via_ajax(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'administrator')->first() ?? User::factory()->create(['role' => 'administrator']);

        $file = UploadedFile::fake()->create('to_delete.pdf', 100, 'application/pdf');
        $this->actingAs($admin)->post(route('administrator.dokumen-persyaratan.upload', 'baru'), [
            'file' => $file,
        ]);

        $dokumen = DokumenPersyaratan::where('kategori', 'baru')->first();

        $response = $this->actingAs($admin)->deleteJson(route('administrator.dokumen-persyaratan.destroy', $dokumen->id));
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'kategori' => 'baru',
        ]);

        $dokumen->refresh();
        $this->assertNull($dokumen->file_path);
    }
}
