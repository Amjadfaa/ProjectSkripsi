<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\KartuPas;
use App\Models\Instansi;
use App\Exports\KartuPasExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class KartuPasController extends Controller
{
    public function index(Request $request)
    {
        // Update status kadaluarsa otomatis
        KartuPas::where('status', 'aktif')
            ->where('tanggal_berlaku', '<', now())
            ->update(['status' => 'kadaluarsa']);

        $query = KartuPas::latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_pemegang', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_kartu', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('instansi')) {
            $query->where('perusahaan', $request->instansi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kartuPas     = $query->paginate(10)->appends($request->query());
        $instansiList = KartuPas::distinct()->pluck('perusahaan')->filter()->values();

        return view('administrator.kartu-pas.index', compact('kartuPas', 'instansiList'));
    }

    public function tambah()
    {
        $instansiList = Instansi::where('is_active', true)->get();
        return view('administrator.kartu-pas.tambah', compact('instansiList'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'nomor_kartu'     => ['required', 'string', 'unique:kartu_pas', 'max:255'],
            'nama_pemegang'   => ['required', 'string', 'max:255'],
            'perusahaan'      => ['required', 'string', 'max:255'],
            'area_akses'      => ['required', 'string', 'max:255'],
            'tanggal_terbit'  => ['required', 'date'],
            'tanggal_berlaku' => ['required', 'date', 'after:tanggal_terbit'],
        ]);

        KartuPas::create([
            'permohonan_id'   => null,
            'nomor_kartu'     => $request->nomor_kartu,
            'nama_pemegang'   => $request->nama_pemegang,
            'perusahaan'      => $request->perusahaan,
            'area_akses'      => $request->area_akses,
            'tanggal_terbit'  => $request->tanggal_terbit,
            'tanggal_berlaku' => $request->tanggal_berlaku,
            'status'          => 'aktif',
        ]);

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Kartu PAS berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $kartuPas     = KartuPas::findOrFail($id);
        $instansiList = Instansi::where('is_active', true)->get();
        return view('administrator.kartu-pas.edit', compact('kartuPas', 'instansiList'));
    }

    public function update(Request $request, int $id)
    {
        $kartuPas = KartuPas::findOrFail($id);

        $request->validate([
            'nomor_kartu'     => ['required', 'string', 'unique:kartu_pas,nomor_kartu,' . $id],
            'nama_pemegang'   => ['required', 'string'],
            'perusahaan'      => ['required', 'string'],
            'area_akses'      => ['required', 'string'],
            'tanggal_terbit'  => ['required', 'date'],
            'tanggal_berlaku' => ['required', 'date', 'after:tanggal_terbit'],
            'status'          => ['required', 'in:aktif,tidak_aktif,kadaluarsa'],
        ]);

        $kartuPas->update($request->all());

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Kartu PAS berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        KartuPas::findOrFail($id)->delete();

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Kartu PAS berhasil dihapus.');
    }

    public function destroyAll()
    {
        KartuPas::truncate();

        return redirect()->route('administrator.kartu-pas.index')
            ->with('success', 'Semua data kartu PAS berhasil dihapus.');
    }

    public function destroySelected(Request $request)
    {
    $request->validate([
        'ids'   => ['required', 'array'],
        'ids.*' => ['integer'],
    ]);

    KartuPas::whereIn('id', $request->ids)->delete();

    return redirect()->route('administrator.kartu-pas.index')
        ->with('success', count($request->ids) . ' kartu PAS berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search', 'instansi', 'status']);
        return Excel::download(new KartuPasExport($filters), 'laporan-kartu-pas-' . date('Ymd') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = KartuPas::latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_pemegang', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_kartu', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('instansi')) {
            $query->where('perusahaan', $request->instansi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kartuPas = $query->get();
        $pdf = Pdf::loadView('administrator.kartu-pas.pdf', compact('kartuPas'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kartu-pas-' . date('Ymd') . '.pdf');
    }
}