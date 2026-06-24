<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    public function index()
    {
        $instansis = Instansi::latest()->get();
        return view('administrator.instansi.index', compact('instansis'));
    }

    public function create()
    {
        return view('administrator.instansi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_instansi' => ['required', 'string', 'unique:instansis', 'max:255'],
            'alamat'        => ['nullable', 'string'],
            'telepon'       => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email'],
            'kuota'         => ['nullable', 'integer', 'min:0'],
        ]);

        Instansi::create($request->all());

        return redirect()->route('administrator.instansi.index')
            ->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $instansi = Instansi::findOrFail($id);
        return view('administrator.instansi.edit', compact('instansi'));
    }

    public function update(Request $request, int $id)
    {
        $instansi = Instansi::findOrFail($id);

        $request->validate([
            'nama_instansi' => ['required', 'string', 'unique:instansis,nama_instansi,' . $id, 'max:255'],
            'alamat'        => ['nullable', 'string'],
            'telepon'       => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email'],
            'kuota'         => ['nullable', 'integer', 'min:0'],
            'is_active'     => ['required'],
        ]);

        $instansi->update($request->all());

        return redirect()->route('administrator.instansi.index')
            ->with('success', 'Instansi berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        Instansi::findOrFail($id)->delete();

        return redirect()->route('administrator.instansi.index')
            ->with('success', 'Instansi berhasil dihapus.');
    }
}