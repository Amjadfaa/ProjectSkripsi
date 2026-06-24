<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use Illuminate\Support\Facades\Storage;
use App\Models\Permohonan;
use App\Models\Verifikasi;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index()
    {
        $permohonan = Permohonan::with('user')
            ->latest()
            ->get();
        return view('verifikator.permohonan.index', compact('permohonan'));
    }

    public function show($id)
    {
        $permohonan = Permohonan::with(['user', 'berkas.verifikasi'])
            ->findOrFail($id);
        return view('verifikator.permohonan.show', compact('permohonan'));
    }

    public function verifikasi(Request $request, $id)
    {
        $permohonan = Permohonan::with('berkas')->findOrFail($id);

        foreach ($permohonan->berkas as $berkas) {
            $isVerified = $request->has('berkas_' . $berkas->id);
            $catatan    = $request->input('catatan_' . $berkas->id);

            // Update atau buat data verifikasi
            Verifikasi::updateOrCreate(
                ['berkas_id' => $berkas->id],
                [
                    'verifikator_id' => auth()->id(),
                    'is_verified'    => $isVerified,
                    'catatan'        => $catatan,
                    'verified_at'    => $isVerified ? now() : null,
                ]
            );

            // Update status berkas
            $berkas->update([
                'status'  => $isVerified ? 'diverifikasi' : 'ditolak',
                'catatan' => $catatan,
            ]);
        }

        // Cek apakah semua berkas sudah diverifikasi
        $allVerified = $permohonan->berkas->every(fn($b) => $b->fresh()->status === 'diverifikasi');
        $anyRejected = $permohonan->berkas->contains(fn($b) => $b->fresh()->status === 'ditolak');

        $permohonan->update([
            'status' => $allVerified ? 'disetujui' : ($anyRejected ? 'ditolak' : 'diproses'),
        ]);

        return redirect()->route('verifikator.permohonan.show', $id)
            ->with('success', 'Verifikasi berhasil disimpan.');
    }
}