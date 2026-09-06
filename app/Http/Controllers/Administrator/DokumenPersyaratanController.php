<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\DokumenPersyaratan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenPersyaratanController extends Controller
{
    /**
     * Menampilkan halaman upload dokumen persyaratan (Baru & Perpanjangan)
     */
    public function index()
    {
        $dokumenBaru = DokumenPersyaratan::firstOrCreate(
            ['kategori' => 'baru'],
            [
                'nama_dokumen' => 'Dokumen Persyaratan Pembuatan PAS Bandara (Baru)',
                'deskripsi' => 'Persyaratan dan kelengkapan berkas untuk permohonan penerbitan Kartu PAS Bandara baru.',
            ]
        );

        $dokumenPerpanjangan = DokumenPersyaratan::firstOrCreate(
            ['kategori' => 'perpanjangan'],
            [
                'nama_dokumen' => 'Dokumen Persyaratan Perpanjangan PAS Bandara',
                'deskripsi' => 'Persyaratan dan kelengkapan berkas untuk permohonan perpanjangan masa berlaku Kartu PAS Bandara.',
            ]
        );

        // Load relasi uploader jika ada
        $dokumenBaru->load('uploader');
        $dokumenPerpanjangan->load('uploader');

        return view('administrator.dokumen-persyaratan.index', compact('dokumenBaru', 'dokumenPerpanjangan'));
    }

    /**
     * Memproses upload / pembaruan dokumen persyaratan tunggal (AJAX / Standar)
     */
    public function upload(Request $request, string $kategori)
    {
        if (!in_array($kategori, ['baru', 'perpanjangan'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Kategori dokumen tidak valid.'], 400);
            }
            return redirect()->back()->with('error', 'Kategori dokumen tidak valid.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'file.required' => 'Silakan pilih file dokumen terlebih dahulu.',
            'file.file' => 'Format unggahan harus berupa file yang valid.',
            'file.mimes' => 'Format file yang diperbolehkan hanya: PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'file.max' => 'Ukuran file maksimal adalah 20 MB.',
            'deskripsi.max' => 'Catatan/deskripsi maksimal 1000 karakter.',
        ]);

        $dokumen = DokumenPersyaratan::where('kategori', $kategori)->firstOrFail();

        // Jika ada file lama, hapus dari storage disk
        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $size = $file->getSize();

        // Nama file aman tersimpan di storage
        $safeFileName = 'persyaratan_' . $kategori . '_' . time() . '.' . $extension;
        $path = $file->storeAs('dokumen-persyaratan', $safeFileName, 'public');

        $dokumen->update([
            'file_path' => $path,
            'file_name' => $originalName,
            'file_size' => $size,
            'file_type' => $extension,
            'deskripsi' => $request->filled('deskripsi') ? $request->deskripsi : $dokumen->deskripsi,
            'uploaded_by' => Auth::id(),
        ]);

        $dokumen->load('uploader');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "{$dokumen->nama_dokumen} berhasil diunggah.",
                'dokumen' => $this->formatDokumenJson($dokumen),
            ]);
        }

        return redirect()->route('administrator.dokumen-persyaratan.index')
            ->with('success', "✅ {$dokumen->nama_dokumen} berhasil diunggah.");
    }

    /**
     * Memproses upload dua dokumen persyaratan sekaligus (Batch Upload AJAX / Standar)
     */
    public function uploadBatch(Request $request)
    {
        $request->validate([
            'file_baru' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'deskripsi_baru' => 'nullable|string|max:1000',
            'file_perpanjangan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'deskripsi_perpanjangan' => 'nullable|string|max:1000',
        ], [
            'file_baru.mimes' => 'Format file PAS Baru harus berupa: PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'file_baru.max' => 'Ukuran file PAS Baru maksimal 20 MB.',
            'file_perpanjangan.mimes' => 'Format file Perpanjangan PAS harus berupa: PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'file_perpanjangan.max' => 'Ukuran file Perpanjangan PAS maksimal 20 MB.',
        ]);

        if (!$request->hasFile('file_baru') && !$request->hasFile('file_perpanjangan')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Pilih setidaknya satu file dokumen untuk diunggah.'], 422);
            }
            return redirect()->back()->with('error', 'Pilih setidaknya satu file dokumen untuk diunggah.');
        }

        $results = [];

        // Upload Dokumen Baru jika ada
        if ($request->hasFile('file_baru')) {
            $dokumenBaru = DokumenPersyaratan::firstOrCreate(['kategori' => 'baru'], [
                'nama_dokumen' => 'Dokumen Persyaratan Pembuatan PAS Bandara (Baru)',
            ]);

            if ($dokumenBaru->file_path && Storage::disk('public')->exists($dokumenBaru->file_path)) {
                Storage::disk('public')->delete($dokumenBaru->file_path);
            }

            $fileB = $request->file('file_baru');
            $extB = strtolower($fileB->getClientOriginalExtension());
            $safeB = 'persyaratan_baru_' . time() . '.' . $extB;
            $pathB = $fileB->storeAs('dokumen-persyaratan', $safeB, 'public');

            $dokumenBaru->update([
                'file_path' => $pathB,
                'file_name' => $fileB->getClientOriginalName(),
                'file_size' => $fileB->getSize(),
                'file_type' => $extB,
                'deskripsi' => $request->filled('deskripsi_baru') ? $request->deskripsi_baru : $dokumenBaru->deskripsi,
                'uploaded_by' => Auth::id(),
            ]);
            $dokumenBaru->load('uploader');
            $results['baru'] = $this->formatDokumenJson($dokumenBaru);
        }

        // Upload Dokumen Perpanjangan jika ada
        if ($request->hasFile('file_perpanjangan')) {
            $dokumenPerp = DokumenPersyaratan::firstOrCreate(['kategori' => 'perpanjangan'], [
                'nama_dokumen' => 'Dokumen Persyaratan Perpanjangan PAS Bandara',
            ]);

            if ($dokumenPerp->file_path && Storage::disk('public')->exists($dokumenPerp->file_path)) {
                Storage::disk('public')->delete($dokumenPerp->file_path);
            }

            $fileP = $request->file('file_perpanjangan');
            $extP = strtolower($fileP->getClientOriginalExtension());
            $safeP = 'persyaratan_perpanjangan_' . time() . '.' . $extP;
            $pathP = $fileP->storeAs('dokumen-persyaratan', $safeP, 'public');

            $dokumenPerp->update([
                'file_path' => $pathP,
                'file_name' => $fileP->getClientOriginalName(),
                'file_size' => $fileP->getSize(),
                'file_type' => $extP,
                'deskripsi' => $request->filled('deskripsi_perpanjangan') ? $request->deskripsi_perpanjangan : $dokumenPerp->deskripsi,
                'uploaded_by' => Auth::id(),
            ]);
            $dokumenPerp->load('uploader');
            $results['perpanjangan'] = $this->formatDokumenJson($dokumenPerp);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Dokumen persyaratan berhasil diunggah.',
                'documents' => $results,
            ]);
        }

        return redirect()->route('administrator.dokumen-persyaratan.index')
            ->with('success', 'Dokumen persyaratan berhasil diunggah.');
    }

    /**
     * Menampilkan pratinjau dokumen (PDF / Gambar) inline di browser
     */
    public function preview($id)
    {
        $dokumen = DokumenPersyaratan::findOrFail($id);

        if (!$dokumen->file_path || !Storage::disk('public')->exists($dokumen->file_path)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        $fullPath = Storage::disk('public')->path($dokumen->file_path);
        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $dokumen->file_name . '"'
        ]);
    }

    /**
     * Mengunduh dokumen persyaratan
     */
    public function download($id)
    {
        $dokumen = DokumenPersyaratan::findOrFail($id);

        if (!$dokumen->file_path || !Storage::disk('public')->exists($dokumen->file_path)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->file_name);
    }

    /**
     * Menghapus file dokumen persyaratan (AJAX / Standar)
     */
    public function destroy(Request $request, $id)
    {
        $dokumen = DokumenPersyaratan::findOrFail($id);
        $namaDokumen = $dokumen->nama_dokumen;
        $kategori = $dokumen->kategori;

        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->update([
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
            'uploaded_by' => null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "File dokumen untuk '{$namaDokumen}' berhasil dihapus.",
                'kategori' => $kategori,
                'dokumen_id' => $dokumen->id,
            ]);
        }

        return redirect()->route('administrator.dokumen-persyaratan.index')
            ->with('success', "File dokumen untuk '{$namaDokumen}' berhasil dihapus.");
    }

    /**
     * Format payload dokumen untuk response JSON
     */
    private function formatDokumenJson(DokumenPersyaratan $dokumen): array
    {
        return [
            'id' => $dokumen->id,
            'kategori' => $dokumen->kategori,
            'nama_dokumen' => $dokumen->nama_dokumen,
            'deskripsi' => $dokumen->deskripsi,
            'file_name' => $dokumen->file_name,
            'file_size' => $dokumen->file_size,
            'formatted_size' => $dokumen->formatted_size,
            'file_type' => $dokumen->file_type,
            'updated_at' => $dokumen->updated_at ? $dokumen->updated_at->format('d/m/Y H:i') : null,
            'uploader_name' => $dokumen->uploader->name ?? (Auth::user()->name ?? 'Administrator'),
            'preview_url' => route('dokumen-persyaratan.public-preview', $dokumen->id),
            'download_url' => route('dokumen-persyaratan.public-download', $dokumen->id),
            'delete_url' => route('administrator.dokumen-persyaratan.destroy', $dokumen->id),
            'is_pdf' => $dokumen->isPdf(),
            'is_image' => $dokumen->isImage(),
        ];
    }
}
