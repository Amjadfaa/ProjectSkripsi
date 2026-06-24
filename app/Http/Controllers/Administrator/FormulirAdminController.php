<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Formulir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormulirAdminController extends Controller
{
    public function index()
    {
        $formulirs = Formulir::latest()->get();
        return view('administrator.formulir.index', compact('formulirs'));
    }

    public function create()
    {
        return view('administrator.formulir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_formulir' => ['required', 'string', 'max:255'],
            'keterangan'    => ['nullable', 'string'],
            'file'          => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $path = $request->file('file')->store('formulir', 'public');

        Formulir::create([
            'nama_formulir' => $request->nama_formulir,
            'keterangan'    => $request->keterangan,
            'file_path'     => $path,
            'is_active'     => true,
        ]);

        return redirect()->route('administrator.formulir.index')
            ->with('success', 'Formulir berhasil diupload.');
    }

    public function destroy(int $id)
    {
        $formulir = Formulir::findOrFail($id);
        Storage::disk('public')->delete($formulir->file_path);
        $formulir->delete();

        return redirect()->route('administrator.formulir.index')
            ->with('success', 'Formulir berhasil dihapus.');
    }
}