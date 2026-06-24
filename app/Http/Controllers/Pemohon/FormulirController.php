<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Formulir;
use Illuminate\Support\Facades\Storage;

class FormulirController extends Controller
{
    public function index()
    {
        $formulirs = Formulir::where('is_active', true)->get();
        return view('pemohon.formulir.index', compact('formulirs'));
    }

    public function download($id)
    {
        $formulir = Formulir::findOrFail($id);
        return Storage::download($formulir->file_path, $formulir->nama_formulir);
    }
}