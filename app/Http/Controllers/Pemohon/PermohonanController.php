<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PermohonanController extends Controller
{
    public function index()
    {
        $permohonan = Permohonan::where('user_id', auth()->id())
            ->latest()
            ->get();
        return view('pemohon.permohonan.index', compact('permohonan'));
    }

    public function create()
    {
        return view('pemohon.permohonan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'keperluan'        => ['required', 'string', 'max:255'],
            'berkas.*' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'nama_berkas.*'    => ['required', 'string', 'max:255'],
        ]);

        $nomor = 'PAS-' . date('Ymd') . '-' . str_pad(Permohonan::count() + 1, 4, '0', STR_PAD_LEFT);

        $permohonan = Permohonan::create([
            'user_id'          => auth()->id(),
            'nomor_permohonan' => $nomor,
            'nama_pemohon'     => auth()->user()->name,
            'perusahaan'       => auth()->user()->perusahaan,
            'keperluan'        => $request->keperluan,
            'status'           => 'menunggu',
        ]);

        if ($request->hasFile('berkas')) {
            foreach ($request->file('berkas') as $key => $file) {
                $path = $file->store('berkas/' . $permohonan->id, 'public');
                Berkas::create([
                    'permohonan_id' => $permohonan->id,
                    'nama_berkas'   => $request->nama_berkas[$key],
                    'file_path'     => $path,
                    'status'        => 'belum_diverifikasi',
                ]);
            }
        }

        return redirect()->route('pemohon.permohonan.index')
            ->with('success', 'Permohonan berhasil diajukan dengan nomor ' . $nomor);
    }

    public function show(int $id)
    {
        $permohonan = Permohonan::with(['berkas.verifikasi'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        return view('pemohon.permohonan.show', compact('permohonan'));
    }

    public function destroy(int $id)
    {
        $permohonan = Permohonan::where('user_id', auth()->id())->findOrFail($id);

        // Hapus berkas terkait dari storage
        foreach ($permohonan->berkas as $berkas) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($berkas->file_path);
        }

        $permohonan->delete();

        return redirect()->route('pemohon.permohonan.index')
            ->with('success', 'Permohonan berhasil dihapus.');
    }
}