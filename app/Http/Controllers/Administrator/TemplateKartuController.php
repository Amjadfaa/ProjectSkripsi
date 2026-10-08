<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\AreaAkses;
use App\Models\KartuPas;
use App\Models\TemplateKartu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateKartuController extends Controller
{
    /**
     * Tampilkan daftar template kartu PAS
     */
    public function index(Request $request)
    {
        $templates = TemplateKartu::orderBy('id', 'asc')->get();

        $allAreas = AreaAkses::orderBy('kode')->get()->keyBy('kode');

        // Statistik ringkas
        $totalTemplates = $templates->count();
        $totalAktif     = $templates->where('is_active', true)->count();

        // Pemetaan area ke masing-masing template dan deteksi pendobelan
        $areaAssignment = [];
        $hasOverlap     = false;
        foreach ($templates as $t) {
            if (is_array($t->area_akses)) {
                foreach ($t->area_akses as $a) {
                    $upper = strtoupper(trim($a));
                    if (isset($areaAssignment[$upper])) {
                        $hasOverlap = true;
                    }
                    $areaAssignment[$upper] = $t;
                }
            }
        }
        $totalCoveredAreas = count($areaAssignment);
        $totalAllAreas     = $allAreas->count();

        return view('administrator.template-kartu.index', compact(
            'templates',
            'allAreas',
            'areaAssignment',
            'totalTemplates',
            'totalAktif',
            'totalCoveredAreas',
            'totalAllAreas',
            'hasOverlap'
        ));
    }

    /**
     * Form tambah template kartu baru
     */
    public function create()
    {
        if (TemplateKartu::count() >= 3) {
            return redirect()->route('administrator.template-kartu.index')
                ->with('error', 'Kapasitas template kartu telah mencapai batas maksimal (3 template). Hapus salah satu template terlebih dahulu jika ingin menambahkan template baru.');
        }

        $allAreas = AreaAkses::orderBy('kode')->get();

        // Kumpulkan area yang sudah digunakan oleh template yang ada agar tidak bisa didouble
        $claimedAreas = [];
        foreach (TemplateKartu::all() as $tpl) {
            if (is_array($tpl->area_akses)) {
                foreach ($tpl->area_akses as $a) {
                    $claimedAreas[strtoupper(trim($a))] = [
                        'template_id'   => $tpl->id,
                        'template_name' => $tpl->nama_template,
                        'kode_warna'    => $tpl->kode_warna,
                        'warna_hex'     => $tpl->warna_hex,
                    ];
                }
            }
        }

        return view('administrator.template-kartu.create', compact('allAreas', 'claimedAreas'));
    }

    /**
     * Simpan template kartu baru
     */
    public function store(Request $request)
    {
        if (TemplateKartu::count() >= 3) {
            return redirect()->route('administrator.template-kartu.index')
                ->with('error', 'Gagal menambahkan template. Batas maksimal 3 template kartu telah tercapai.');
        }

        $request->validate([
            'nama_template'   => ['required', 'string', 'max:255'],
            'kode_warna'      => ['nullable', 'string', 'max:50'],
            'warna_label'     => ['nullable', 'string', 'max:100'],
            'warna_hex'       => ['nullable', 'string', 'max:10'],
            'warna_teks'      => ['nullable', 'string'],
            'gambar_template' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'], // Max 5MB
            'area_akses'      => ['nullable', 'array'],
            'area_akses.*'    => ['string', 'exists:area_akses,kode'],
            'prioritas'       => ['nullable', 'integer'],
            'is_active'       => ['nullable', 'boolean'],
            'keterangan'      => ['nullable', 'string', 'max:1000'],
            'header_title'    => ['nullable', 'string', 'max:255'],
            'subheader_title' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_template.required'   => 'Nama template kartu wajib diisi.',
            'gambar_template.required' => 'File gambar latar belakang template kartu wajib diunggah.',
            'gambar_template.image'    => 'File template harus berupa gambar (PNG, JPG, JPEG, WEBP).',
            'gambar_template.max'      => 'Ukuran file gambar maksimal 5 MB.',
        ]);

        // Validasi: Cegah pendobelan area akses yang sudah dipakai template lain
        if ($request->has('area_akses') && is_array($request->area_akses)) {
            $claimedMap = [];
            foreach (TemplateKartu::all() as $ot) {
                if (is_array($ot->area_akses)) {
                    foreach ($ot->area_akses as $a) {
                        $claimedMap[strtoupper(trim($a))] = $ot->nama_template;
                    }
                }
            }
            foreach ($request->area_akses as $code) {
                $upper = strtoupper(trim($code));
                if (isset($claimedMap[$upper])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Area Akses [{$upper}] sudah digunakan oleh '{$claimedMap[$upper]}'. Tidak boleh ada pendobelan area antar template.");
                }
            }
        }

        // Simpan file gambar template
        $imagePath = $request->file('gambar_template')->store('template_kartu', 'public');
        $isActive  = $request->has('is_active') ? $request->boolean('is_active') : true;

        $colorDefaults = [
            'kuning' => ['hex' => '#FACC15', 'teks' => '#000000', 'label' => 'Kuning (Terminal / Penumpang)'],
            'biru'   => ['hex' => '#2563EB', 'teks' => '#FFFFFF', 'label' => 'Biru (Kargo / Airside)'],
            'merah'  => ['hex' => '#EF4444', 'teks' => '#FFFFFF', 'label' => 'Merah (Apron / Vital)'],
            'hijau'  => ['hex' => '#10B981', 'teks' => '#FFFFFF', 'label' => 'Hijau (Operasional Umum)'],
            'ungu'   => ['hex' => '#8B5CF6', 'teks' => '#FFFFFF', 'label' => 'Ungu (Protokoler Khusus)'],
        ];

        $kodeWarna = strtolower(trim($request->input('kode_warna', 'kuning') ?: 'kuning'));
        $defaultInfo = $colorDefaults[$kodeWarna] ?? ['hex' => '#2563EB', 'teks' => '#FFFFFF', 'label' => ucfirst($kodeWarna)];

        $warnaHex   = $request->input('warna_hex') ?: $defaultInfo['hex'];
        $warnaTeks  = $request->input('warna_teks') ?: $defaultInfo['teks'];
        $warnaLabel = $request->input('warna_label') ?: $defaultInfo['label'];

        $posisiPengaturan = [
            'header_title'    => $request->input('header_title') ?: 'OTORITAS BANDAR UDARA WILAYAH X MERAUKE',
            'subheader_title' => $request->input('subheader_title') ?: 'BANDAR UDARA MOPAH - MERAUKE',
        ];

        TemplateKartu::create([
            'nama_template'     => $request->nama_template,
            'kode_warna'        => $kodeWarna,
            'warna_label'       => $warnaLabel,
            'warna_hex'         => $warnaHex,
            'warna_teks'        => $warnaTeks,
            'gambar_template'   => $imagePath,
            'area_akses'        => $request->input('area_akses', []),
            'prioritas'         => (int) $request->input('prioritas', 1),
            'is_default'        => false,
            'is_active'         => $isActive,
            'keterangan'        => $request->keterangan,
            'posisi_pengaturan' => $posisiPengaturan,
        ]);

        return redirect()->route('administrator.template-kartu.index')
            ->with('success', 'Template kartu PAS baru berhasil disimpan.');
    }

    /**
     * Form edit template kartu
     */
    public function edit(int $id)
    {
        $template = TemplateKartu::findOrFail($id);
        $allAreas = AreaAkses::orderBy('kode')->get();

        // Kumpulkan area yang sudah dipakai oleh template LAIN agar tidak terjadi pendobelan
        $claimedAreas = [];
        foreach (TemplateKartu::where('id', '!=', $id)->get() as $tpl) {
            if (is_array($tpl->area_akses)) {
                foreach ($tpl->area_akses as $a) {
                    $claimedAreas[strtoupper(trim($a))] = [
                        'template_id'   => $tpl->id,
                        'template_name' => $tpl->nama_template,
                        'kode_warna'    => $tpl->kode_warna,
                        'warna_hex'     => $tpl->warna_hex,
                    ];
                }
            }
        }

        return view('administrator.template-kartu.edit', compact('template', 'allAreas', 'claimedAreas'));
    }

    /**
     * Perbarui template kartu
     */
    public function update(Request $request, int $id)
    {
        $template = TemplateKartu::findOrFail($id);

        $request->validate([
            'nama_template'   => ['required', 'string', 'max:255'],
            'kode_warna'      => ['nullable', 'string', 'max:50'],
            'warna_label'     => ['nullable', 'string', 'max:100'],
            'warna_hex'       => ['nullable', 'string', 'max:10'],
            'warna_teks'      => ['nullable', 'string'],
            'gambar_template' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'area_akses'      => ['nullable', 'array'],
            'area_akses.*'    => ['string', 'exists:area_akses,kode'],
            'prioritas'       => ['nullable', 'integer'],
            'is_active'       => ['nullable', 'boolean'],
            'keterangan'      => ['nullable', 'string', 'max:1000'],
            'header_title'    => ['nullable', 'string', 'max:255'],
            'subheader_title' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_template.required' => 'Nama template kartu wajib diisi.',
            'gambar_template.image'  => 'File template harus berupa gambar (PNG, JPG, JPEG, WEBP).',
            'gambar_template.max'    => 'Ukuran file gambar maksimal 5 MB.',
        ]);

        // Validasi: Cegah pendobelan area akses dengan template lain
        if ($request->has('area_akses') && is_array($request->area_akses)) {
            $claimedMap = [];
            foreach (TemplateKartu::where('id', '!=', $id)->get() as $ot) {
                if (is_array($ot->area_akses)) {
                    foreach ($ot->area_akses as $a) {
                        $claimedMap[strtoupper(trim($a))] = $ot->nama_template;
                    }
                }
            }
            foreach ($request->area_akses as $code) {
                $upper = strtoupper(trim($code));
                if (isset($claimedMap[$upper])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Area Akses [{$upper}] sudah digunakan oleh '{$claimedMap[$upper]}'. Tidak boleh ada pendobelan area antar template.");
                }
            }
        }

        $imagePath = $template->gambar_template;

        // Jika ada unggahan gambar baru
        if ($request->hasFile('gambar_template')) {
            $protectedFiles = [
                'template_kartu/pas_kuning.png',
                'template_kartu/pas_merah.png',
                'template_kartu/pas_biru.png',
            ];

            if ($template->gambar_template && !in_array($template->gambar_template, $protectedFiles)) {
                if (Storage::disk('public')->exists($template->gambar_template)) {
                    Storage::disk('public')->delete($template->gambar_template);
                }
            }

            $imagePath = $request->file('gambar_template')->store('template_kartu', 'public');
        }

        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;

        $colorDefaults = [
            'kuning' => ['hex' => '#FACC15', 'teks' => '#000000', 'label' => 'Kuning (Terminal / Penumpang)'],
            'biru'   => ['hex' => '#2563EB', 'teks' => '#FFFFFF', 'label' => 'Biru (Kargo / Airside)'],
            'merah'  => ['hex' => '#EF4444', 'teks' => '#FFFFFF', 'label' => 'Merah (Apron / Vital)'],
            'hijau'  => ['hex' => '#10B981', 'teks' => '#FFFFFF', 'label' => 'Hijau (Operasional Umum)'],
            'ungu'   => ['hex' => '#8B5CF6', 'teks' => '#FFFFFF', 'label' => 'Ungu (Protokoler Khusus)'],
        ];

        $kodeWarna = strtolower(trim($request->input('kode_warna', $template->kode_warna) ?: $template->kode_warna));
        $defaultInfo = $colorDefaults[$kodeWarna] ?? ['hex' => $template->warna_hex, 'teks' => $template->warna_teks, 'label' => $template->warna_label];

        $warnaHex   = $request->input('warna_hex') ?: $defaultInfo['hex'];
        $warnaTeks  = $request->input('warna_teks') ?: $defaultInfo['teks'];
        $warnaLabel = $request->input('warna_label') ?: $defaultInfo['label'];

        $posisiPengaturan = [
            'header_title'    => $request->input('header_title') ?: 'OTORITAS BANDAR UDARA WILAYAH X MERAUKE',
            'subheader_title' => $request->input('subheader_title') ?: 'BANDAR UDARA MOPAH - MERAUKE',
        ];

        $template->update([
            'nama_template'     => $request->nama_template,
            'kode_warna'        => $kodeWarna,
            'warna_label'       => $warnaLabel,
            'warna_hex'         => $warnaHex,
            'warna_teks'        => $warnaTeks,
            'gambar_template'   => $imagePath,
            'area_akses'        => $request->input('area_akses', []),
            'prioritas'         => (int) $request->input('prioritas', $template->prioritas ?? 1),
            'is_default'        => false,
            'is_active'         => $isActive,
            'keterangan'        => $request->keterangan,
            'posisi_pengaturan' => $posisiPengaturan,
        ]);

        return redirect()->route('administrator.template-kartu.index')
            ->with('success', "Template kartu '{$template->nama_template}' berhasil diperbarui.");
    }

    /**
     * Hapus template kartu
     */
    public function destroy(int $id)
    {
        $template = TemplateKartu::findOrFail($id);

        if (TemplateKartu::count() <= 1) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus template kartu. Sistem membutuhkan minimal 1 template aktif.');
        }

        $protectedFiles = [
            'template_kartu/pas_kuning.png',
            'template_kartu/pas_merah.png',
            'template_kartu/pas_biru.png',
        ];

        if ($template->gambar_template && !in_array($template->gambar_template, $protectedFiles)) {
            if (Storage::disk('public')->exists($template->gambar_template)) {
                Storage::disk('public')->delete($template->gambar_template);
            }
        }

        $nama = $template->nama_template;
        $template->delete();

        return redirect()->route('administrator.template-kartu.index')
            ->with('success', "Template kartu '{$nama}' berhasil dihapus.");
    }

    /**
     * Toggle status aktif / nonaktif template kartu
     */
    public function toggleStatus(Request $request, int $id)
    {
        $template = TemplateKartu::findOrFail($id);

        $template->is_active = !$template->is_active;
        $template->save();

        $statusStr = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'is_active' => $template->is_active,
                'message'   => "Template '{$template->nama_template}' berhasil {$statusStr}.",
            ]);
        }

        return redirect()->back()
            ->with('success', "Template '{$template->nama_template}' berhasil {$statusStr}.");
    }

    /**
     * Tetapkan template (Metode legacy diarahkan ke info)
     */
    public function setDefault(int $id)
    {
        return redirect()->back()
            ->with('info', 'Sistem tidak menggunakan template default. Ketiga template (Merah, Biru, Kuning) digunakan spesifik berdasarkan area akses masing-masing tanpa pendobelan.');
    }

    /**
     * Halaman Live Mockup & Preview Kartu PAS
     */
    public function preview(Request $request, int $id)
    {
        $template = TemplateKartu::findOrFail($id);
        $allTemplates = TemplateKartu::orderBy('id', 'asc')->get();
        $sampleKartuList = KartuPas::with('instansi')->latest()->take(25)->get();

        $selectedKartuId = $request->query('kartu_pas_id');
        $selectedKartu = null;

        if ($selectedKartuId) {
            $selectedKartu = KartuPas::with('instansi')->find($selectedKartuId);
        }

        // Dummy data fallback jika tidak ada kartu dipilih
        $previewData = [
            'nomor_kartu'     => $selectedKartu ? $selectedKartu->nomor_kartu : 'PAS-2024-00129',
            'nama_pemegang'   => $selectedKartu ? $selectedKartu->nama_pemegang : 'AMJAD FAAHIM',
            'jabatan'         => $selectedKartu ? $selectedKartu->jabatan : 'AVSEC OFFICER',
            'perusahaan'      => $selectedKartu ? $selectedKartu->perusahaan : 'UPBU MOPAH MERAUKE',
            'tanggal_berlaku' => $selectedKartu && $selectedKartu->tanggal_berlaku 
                                    ? \Carbon\Carbon::parse($selectedKartu->tanggal_berlaku)->format('d M Y') 
                                    : '30 MAY 2027',
            'area_akses'      => $selectedKartu && $selectedKartu->area_akses 
                                    ? array_filter(array_map('trim', explode(',', KartuPas::normalizeAreaAkses($selectedKartu->area_akses)))) 
                                    : ($template->area_akses ?: ['A', 'B', 'C']),
            'foto'            => null,
        ];

        return view('administrator.template-kartu.preview', compact(
            'template',
            'allTemplates',
            'sampleKartuList',
            'selectedKartu',
            'previewData'
        ));
    }

    /**
     * Halaman Desainer Visual Canva-like untuk mengatur tata letak elemen data pada kartu
     */
    public function designer(Request $request, int $id)
    {
        $template = TemplateKartu::findOrFail($id);
        $allTemplates = TemplateKartu::orderBy('id', 'asc')->get();
        $sampleKartuList = KartuPas::with('instansi')->latest()->take(30)->get();

        $selectedKartuId = $request->query('kartu_pas_id');
        $selectedKartu = null;

        if ($selectedKartuId) {
            $selectedKartu = KartuPas::with('instansi')->find($selectedKartuId);
        }

        $previewData = [
            'nomor_kartu'     => $selectedKartu ? $selectedKartu->nomor_kartu : '1234/PAS-MOPAH/2026',
            'nama_pemegang'   => $selectedKartu ? $selectedKartu->nama_pemegang : 'AMJAD FAAHIM',
            'jabatan'         => $selectedKartu ? $selectedKartu->jabatan : 'AVSEC OFFICER',
            'perusahaan'      => $selectedKartu ? $selectedKartu->perusahaan : 'UPBU MOPAH MERAUKE',
            'tanggal_berlaku' => $selectedKartu && $selectedKartu->tanggal_berlaku 
                                    ? \Carbon\Carbon::parse($selectedKartu->tanggal_berlaku)->format('d M Y') 
                                    : '30 MAY 2027',
            'area_akses'      => $selectedKartu && $selectedKartu->area_akses 
                                    ? array_filter(array_map('trim', explode(',', KartuPas::normalizeAreaAkses($selectedKartu->area_akses)))) 
                                    : ($template->area_akses ?: ['A', 'B', 'C']),
            'foto'            => null,
        ];

        return view('administrator.template-kartu.designer', compact(
            'template',
            'allTemplates',
            'sampleKartuList',
            'selectedKartu',
            'previewData'
        ));
    }

    /**
     * Simpan koordinat dan tata letak dari Visual Designer (AJAX / Form)
     */
    public function saveDesigner(Request $request, int $id)
    {
        $template = TemplateKartu::findOrFail($id);

        $request->validate([
            'posisi_pengaturan' => ['required', 'array'],
            'warna_teks'        => ['nullable', 'string', 'max:20'],
        ]);

        $template->posisi_pengaturan = $request->input('posisi_pengaturan');
        if ($request->filled('warna_teks')) {
            $template->warna_teks = $request->input('warna_teks');
        }
        $template->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tata letak kartu {$template->nama_template} berhasil disimpan!",
                'posisi'  => $template->posisi,
            ]);
        }

        return redirect()->back()->with('success', "Tata letak kartu {$template->nama_template} berhasil disimpan!");
    }

    /**
     * Reset tata letak kartu kembali ke bawaan sistem
     */
    public function resetDesigner(Request $request, int $id)
    {
        $template = TemplateKartu::findOrFail($id);
        $template->posisi_pengaturan = null;
        $template->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tata letak kartu {$template->nama_template} berhasil di-reset ke posisi bawaan!",
                'posisi'  => $template->posisi,
            ]);
        }

        return redirect()->back()->with('success', "Tata letak kartu {$template->nama_template} berhasil di-reset ke bawaan!");
    }
}
