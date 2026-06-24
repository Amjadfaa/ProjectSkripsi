<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\BerkasPersyaratan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BerkasPersyaratanController extends Controller
{
    public function index()
    {
        $berkas = BerkasPersyaratan::latest()->get();
        return view('administrator.berkas-persyaratan.index', compact('berkas'));
    }

    public function create()
    {
        return view('administrator.berkas-persyaratan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_berkas' => ['required', 'string', 'max:255'],
            'keterangan'  => ['nullable', 'string'],
            'file'        => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = $request->file('file')->store('berkas-persyaratan', 'public');

        BerkasPersyaratan::create([
            'nama_berkas' => $request->nama_berkas,
            'keterangan'  => $request->keterangan,
            'file_path'   => $path,
            'is_active'   => true,
        ]);

        return redirect()->route('administrator.berkas-persyaratan.index')
            ->with('success', 'Berkas persyaratan berhasil diupload.');
    }

    public function toggle(int $id)
    {
        $berkas = BerkasPersyaratan::findOrFail($id);
        $berkas->update(['is_active' => !$berkas->is_active]);

        return redirect()->back()
            ->with('success', 'Status berkas berhasil diubah.');
    }

    public function destroy(int $id)
    {
        $berkas = BerkasPersyaratan::findOrFail($id);
        Storage::disk('public')->delete($berkas->file_path);
        $berkas->delete();

        return redirect()->route('administrator.berkas-persyaratan.index')
            ->with('success', 'Berkas persyaratan berhasil dihapus.');
    }
}