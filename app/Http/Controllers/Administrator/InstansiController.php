<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Exports\InstansiExport;
use App\Exports\InstansiTemplateExport;
use App\Imports\InstansiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    public function index()
    {
        // Sinkronkan kartu PAS yang belum terhubung ke instansi_id
        Instansi::syncUnlinkedKartuPas();

        $instansis = Instansi::withCount([
            'kartuPas as total_kartu',
            'kartuPas as kartu_aktif' => function($q) {
                $q->where('status', 'aktif');
            },
            'kartuPas as kartu_nonaktif' => function($q) {
                $q->where('status', '!=', 'aktif');
            }
        ])->latest()->get();

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

    public function exportExcel()
    {
        return Excel::download(new InstansiExport, 'data-instansi-' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new InstansiTemplateExport, 'template-import-instansi.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        try {
            $import = new InstansiImport();
            Excel::import($import, $request->file('file'));

            $msg = "Import berhasil! {$import->imported} instansi baru berhasil ditambahkan.";
            if ($import->skipped > 0) {
                $msg .= " {$import->skipped} instansi dilewati karena sudah ada di sistem.";
            }

            return redirect()->route('administrator.instansi.index')
                ->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import instansi: ' . $e->getMessage());
        }
    }
}