<?php

namespace Tests\Feature;

use App\Models\AreaAkses;
use App\Models\KartuPas;
use App\Models\TemplateKartu;
use App\Models\User;
use Database\Seeders\AreaAksesSeeder;
use Database\Seeders\TemplateKartuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TemplateKartuTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AreaAksesSeeder::class);
        $this->seed(TemplateKartuSeeder::class);

        $this->admin = User::create([
            'name'     => 'Administrator',
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

    public function test_guest_cannot_access_template_kartu(): void
    {
        $response = $this->get(route('administrator.template-kartu.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_operator_cannot_access_template_kartu(): void
    {
        $response = $this->actingAs($this->operator)->get(route('administrator.template-kartu.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_template_kartu_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('administrator.template-kartu.index'));

        $response->assertStatus(200);
        $response->assertSee('Template Kartu PAS');
        $response->assertSee('Template PAS Kuning');
        $response->assertSee('Template PAS Merah');
        $response->assertSee('Template PAS Biru');
    }

    public function test_admin_cannot_exceed_max_three_templates(): void
    {
        // Pastikan saat ada 3 template, create page dilarang
        $this->assertEquals(3, TemplateKartu::count());
        $response = $this->actingAs($this->admin)->get(route('administrator.template-kartu.create'));
        $response->assertRedirect(route('administrator.template-kartu.index'));
        $response->assertSessionHas('error');

        // Pastikan store juga dilarang
        Storage::fake('public');
        $file = UploadedFile::fake()->image('custom_pas.png', 400, 600);
        $storeResponse = $this->actingAs($this->admin)->post(route('administrator.template-kartu.store'), [
            'nama_template'   => 'Template Ke-4',
            'kode_warna'      => 'ungu',
            'warna_hex'       => '#8B5CF6',
            'warna_teks'      => '#FFFFFF',
            'gambar_template' => $file,
            'prioritas'       => 15,
        ]);
        $storeResponse->assertRedirect(route('administrator.template-kartu.index'));
        $storeResponse->assertSessionHas('error');
        $this->assertEquals(3, TemplateKartu::count());
    }

    public function test_admin_can_view_create_page(): void
    {
        // Hapus 1 template agar kapasitas tersedia (< 3)
        TemplateKartu::latest('id')->first()->delete();

        $response = $this->actingAs($this->admin)->get(route('administrator.template-kartu.create'));

        $response->assertStatus(200);
        $response->assertSee('Template Kartu PAS');
        $response->assertSee('Tema Warna Kartu');
    }

    public function test_admin_can_store_new_template_kartu(): void
    {
        // Hapus 1 template (Biru, area F, G, U) agar kapasitas tersedia (< 3)
        TemplateKartu::where('kode_warna', 'biru')->first()->delete();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('custom_pas.png', 400, 600);

        // Gunakan area yang bebas (F, G) yang sebelumnya dimiliki oleh Biru
        $response = $this->actingAs($this->admin)->post(route('administrator.template-kartu.store'), [
            'nama_template'   => 'Template PAS Hijau VIP',
            'kode_warna'      => 'hijau',
            'warna_label'     => 'Hijau (VIP / Khusus)',
            'warna_hex'       => '#10B981',
            'warna_teks'      => '#FFFFFF',
            'gambar_template' => $file,
            'area_akses'      => ['F', 'G'],
            'prioritas'       => 20,
            'is_active'       => 1,
            'keterangan'      => 'Template kartu khusus tamu VIP dan protokoler.',
        ]);

        $response->assertRedirect(route('administrator.template-kartu.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('template_kartus', [
            'nama_template' => 'Template PAS Hijau VIP',
            'kode_warna'    => 'hijau',
            'prioritas'     => 20,
        ]);

        $created = TemplateKartu::where('nama_template', 'Template PAS Hijau VIP')->first();
        $this->assertNotNull($created);
        Storage::disk('public')->assertExists($created->gambar_template);
    }

    public function test_admin_cannot_store_with_overlapping_area_akses(): void
    {
        // Hapus 1 template agar kuota tersedia
        TemplateKartu::where('kode_warna', 'biru')->first()->delete();

        Storage::fake('public');
        $file = UploadedFile::fake()->image('custom_pas.png', 400, 600);

        // Mencoba menggunakan area 'A' yang sudah diklaim oleh Template Kuning
        $response = $this->from(route('administrator.template-kartu.create'))
            ->actingAs($this->admin)
            ->post(route('administrator.template-kartu.store'), [
                'nama_template'   => 'Template PAS Konflik',
                'kode_warna'      => 'ungu',
                'warna_hex'       => '#8B5CF6',
                'warna_teks'      => '#FFFFFF',
                'gambar_template' => $file,
                'area_akses'      => ['A'], // Area A milik Kuning
                'prioritas'       => 15,
            ]);

        $response->assertRedirect(route('administrator.template-kartu.create'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_update_template_kartu(): void
    {
        $template = TemplateKartu::where('kode_warna', 'kuning')->firstOrFail();

        // Update dengan area yang sah (subset area miliknya sendiri tanpa tumpang tindih)
        $response = $this->actingAs($this->admin)->put(route('administrator.template-kartu.update', $template->id), [
            'nama_template' => 'Template PAS Kuning Diperbarui',
            'kode_warna'    => 'kuning',
            'warna_label'   => 'Kuning (Terminal / Penumpang)',
            'warna_hex'     => '#FACC15',
            'warna_teks'      => '#000000',
            'area_akses'    => ['A', 'B'],
            'prioritas'     => 2,
            'is_active'     => 1,
            'keterangan'    => 'Keterangan diperbarui',
        ]);

        $response->assertRedirect(route('administrator.template-kartu.index'));
        $this->assertDatabaseHas('template_kartus', [
            'id'            => $template->id,
            'nama_template' => 'Template PAS Kuning Diperbarui',
            'prioritas'     => 2,
        ]);
    }

    public function test_admin_cannot_update_with_overlapping_area_akses(): void
    {
        $template = TemplateKartu::where('kode_warna', 'kuning')->firstOrFail();

        // Mencoba menambahkan area 'P' yang diklaim oleh Merah
        $response = $this->from(route('administrator.template-kartu.edit', $template->id))
            ->actingAs($this->admin)
            ->put(route('administrator.template-kartu.update', $template->id), [
                'nama_template' => 'Template PAS Kuning',
                'kode_warna'    => 'kuning',
                'warna_hex'     => '#FACC15',
                'warna_teks'    => '#000000',
                'area_akses'    => ['A', 'P'], // Area P milik Merah
                'prioritas'     => 1,
            ]);

        $response->assertRedirect(route('administrator.template-kartu.edit', $template->id));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_toggle_template_status(): void
    {
        $template = TemplateKartu::where('kode_warna', 'merah')->firstOrFail();
        $initialStatus = $template->is_active;

        $response = $this->actingAs($this->admin)->patch(route('administrator.template-kartu.toggle-status', $template->id));
        $response->assertSessionHas('success');

        $template->refresh();
        $this->assertEquals(!$initialStatus, $template->is_active);
    }

    public function test_admin_set_default_template_redirects_with_info(): void
    {
        $merah = TemplateKartu::where('kode_warna', 'merah')->firstOrFail();

        $response = $this->actingAs($this->admin)->patch(route('administrator.template-kartu.set-default', $merah->id));
        $response->assertSessionHas('info');
    }

    public function test_admin_can_view_preview_mockup(): void
    {
        $template = TemplateKartu::firstOrFail();

        $response = $this->actingAs($this->admin)->get(route('administrator.template-kartu.preview', $template->id));

        $response->assertStatus(200);
        $response->assertSee('Pratinjau');
        $response->assertSee($template->nama_template);
    }

    public function test_template_resolution_by_area(): void
    {
        // 1. Kartu dengan Area A, B, C -> harus resolusi ke Kuning
        $kartuKuning = new KartuPas([
            'nomor_kartu'   => 'PAS-TEST-01',
            'nama_pemegang' => 'Budi Santoso',
            'area_akses'    => 'A, B, C',
        ]);
        $resolvedKuning = TemplateKartu::resolveTemplateForKartu($kartuKuning);
        $this->assertNotNull($resolvedKuning);
        $this->assertEquals('kuning', $resolvedKuning->kode_warna);

        // 2. Kartu dengan Area P -> harus resolusi ke Merah (Apron)
        $kartuMerah = new KartuPas([
            'nomor_kartu'   => 'PAS-TEST-02',
            'nama_pemegang' => 'Joko Widodo',
            'area_akses'    => 'P',
        ]);
        $resolvedMerah = TemplateKartu::resolveTemplateForKartu($kartuMerah);
        $this->assertNotNull($resolvedMerah);
        $this->assertEquals('merah', $resolvedMerah->kode_warna);

        // 3. Kartu dengan Area F, G -> harus resolusi ke Biru (Kargo)
        $kartuBiru = new KartuPas([
            'nomor_kartu'   => 'PAS-TEST-03',
            'nama_pemegang' => 'Siti Aisyah',
            'area_akses'    => 'F, G',
        ]);
        $resolvedBiru = TemplateKartu::resolveTemplateForKartu($kartuBiru);
        $this->assertNotNull($resolvedBiru);
        $this->assertEquals('biru', $resolvedBiru->kode_warna);

        // 4. Kartu dengan area P, I -> harus resolusi ke Merah
        $kartuApron = new KartuPas([
            'nomor_kartu'   => 'PAS-TEST-04',
            'nama_pemegang' => 'Amjad Faahim',
            'area_akses'    => 'P, I',
        ]);
        $resolvedApron = TemplateKartu::resolveTemplateForKartu($kartuApron);
        $this->assertNotNull($resolvedApron);
        $this->assertEquals('merah', $resolvedApron->kode_warna);

        // 5. Kartu dengan area yang tidak terdaftar di template manapun -> Fallback ke template aktif
        $kartuUnknown = new KartuPas([
            'nomor_kartu'   => 'PAS-TEST-05',
            'nama_pemegang' => 'Unknown Area Person',
            'area_akses'    => 'Z99',
        ]);
        $resolvedFallback = TemplateKartu::resolveTemplateForKartu($kartuUnknown);
        $this->assertNotNull($resolvedFallback);
        $this->assertTrue($resolvedFallback->is_active);
    }

    public function test_admin_can_view_visual_designer(): void
    {
        $template = TemplateKartu::firstOrFail();

        $response = $this->actingAs($this->admin)->get(route('administrator.template-kartu.designer', $template->id));

        $response->assertStatus(200);
        $response->assertSee('Desainer Tata Letak');
        $response->assertSee($template->nama_template);
        $response->assertSee('Daftar Elemen (Layers)');
    }

    public function test_admin_can_save_visual_designer_layout(): void
    {
        $template = TemplateKartu::firstOrFail();

        $customLayout = [
            'foto'          => ['top' => 30.0, 'left' => 50.0, 'width' => 40.0, 'height' => 35.0, 'visible' => true],
            'nama_pemegang' => ['top' => 70.0, 'left' => 10.0, 'font_size' => 14, 'color' => '#FFFFFF', 'bold' => true],
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('administrator.template-kartu.designer.save', $template->id), [
                'posisi_pengaturan' => $customLayout,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $template->refresh();
        $this->assertEquals(30.0, $template->posisi['foto']['top']);
        $this->assertEquals(14, $template->posisi['nama_pemegang']['font_size']);
    }

    public function test_admin_can_reset_visual_designer_layout(): void
    {
        $template = TemplateKartu::firstOrFail();
        $template->posisi_pengaturan = ['foto' => ['top' => 99.0]];
        $template->save();

        $response = $this->actingAs($this->admin)
            ->postJson(route('administrator.template-kartu.designer.reset', $template->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $template->refresh();
        $this->assertNull($template->posisi_pengaturan);
        // Default top should be restored
        $this->assertEquals(28.0, $template->posisi['foto']['top']);
    }
}
