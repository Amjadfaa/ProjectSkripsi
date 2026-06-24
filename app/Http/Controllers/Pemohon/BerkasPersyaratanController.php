<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\BerkasPersyaratan;
use Illuminate\Support\Facades\Storage;

class BerkasPersyaratanController extends Controller
{
    public function index()
    {
        $berkas = BerkasPersyaratan::where('is_active', true)->latest()->get();
        return view('pemohon.berkas-persyaratan.index', compact('berkas'));
    }

    public function download(int $id)
    {
        $berkas    = BerkasPersyaratan::findOrFail($id);
        $path      = storage_path('app/public/' . $berkas->file_path);
        $extension = pathinfo($berkas->file_path, PATHINFO_EXTENSION);
        $fileName  = $berkas->nama_berkas . '.' . $extension;

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($path, $fileName);
    }
}