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
    public function index(Request $request)
    {
        // Sinkronkan kartu PAS yang belum terhubung ke instansi_id
        Instansi::syncUnlinkedKartuPas();

        $query = Instansi::withCount([
            'kartuPas as total_kartu',
            'kartuPas as kartu_aktif' => function($q) {
                $q->where('status', 'aktif');
            },
            'kartuPas as kartu_nonaktif' => function($q) {
                $q->whereIn('status', ['tidak_aktif', 'nonaktif', 'kadaluarsa']);
            },
            'kartuPas as kartu_terpakai' => function($q) {
                $q->where('status', '!=', 'tidak_aktif');
            },
        ]);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_instansi', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }

        // Summary counts for KPI cards
        $totalInstansiAll = Instansi::count();
        $totalAktifAll    = Instansi::where('is_active', true)->count();
        $totalKuotaAll    = Instansi::sum('kuota');
        $totalTerpakaiAll = \App\Models\KartuPas::where('status', '!=', 'tidak_aktif')->count();

        $perPage = $request->input('per_page', 10);
        $instansis = $query->orderBy('nama_instansi')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('administrator.instansi.partials.table', compact('instansis'));
        }

        return view('administrator.instansi.index', compact(
            'instansis',
            'totalInstansiAll',
            'totalAktifAll',
            'totalKuotaAll',
            'totalTerpakaiAll'
        ));
    }

    public function create()
    {
        return view('administrator.instansi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_instansi' => ['required', 'string', 'unique:instansis,nama_instansi', 'max:255'],
            'alamat'        => ['nullable', 'string'],
            'telepon'       => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email'],
            'kuota'         => ['nullable', 'integer', 'min:0'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = 1;
        }

        if (!isset($validated['kuota']) || $validated['kuota'] === null) {
            $validated['kuota'] = 0;
        }

        Instansi::create($validated);

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

        $validated = $request->validate([
            'nama_instansi' => ['required', 'string', 'unique:instansis,nama_instansi,' . $id, 'max:255'],
            'alamat'        => ['nullable', 'string'],
            'telepon'       => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email'],
            'kuota'         => ['nullable', 'integer', 'min:0'],
            'is_active'     => ['required'],
        ]);

        if (!isset($validated['kuota']) || $validated['kuota'] === null) {
            $validated['kuota'] = 0;
        }

        $instansi->update($validated);

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