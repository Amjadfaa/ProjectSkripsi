<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\KartuPas;
use Illuminate\Http\Request;


class MonitoringKuotaController extends Controller
{
    public function index()
    {
        $instansis = Instansi::where('is_active', true)
            ->withCount([
                'kartuPas as total_kartu',
                'kartuPas as kartu_aktif' => function($q) {
                    $q->where('status', 'aktif');
                },
                'kartuPas as kartu_nonaktif' => function($q) {
                    $q->where('status', '!=', 'aktif');
                },
            ])
            ->get()
            ->map(function($instansi) {
                $instansi->sisa_kuota = $instansi->kuota - $instansi->kartu_aktif;
                return $instansi;
            });

        return view('administrator.monitoring-kuota.index', compact('instansis'));
    }

    public function show(int $id)
    {
        $instansi = Instansi::findOrFail($id);
        $kartuPas = KartuPas::where(function($q) use ($instansi) {
            $q->where('instansi_id', $instansi->id)
              ->orWhere('perusahaan', $instansi->nama_instansi);
        })->latest()->get();

        return view('administrator.monitoring-kuota.show', compact('instansi', 'kartuPas'));
    }

    public function nonaktifkan(Request $request, int $id)
    {
        $request->validate([
            'keterangan_nonaktif' => ['required', 'in:resign,pensiun,meninggal,lainnya'],
            'catatan_nonaktif'    => ['nullable', 'string', 'max:255'],
        ]);

        $kartu = KartuPas::findOrFail($id);
        $kartu->update([
            'status'              => 'tidak_aktif',
            'keterangan_nonaktif' => $request->keterangan_nonaktif,
            'catatan_nonaktif'    => $request->catatan_nonaktif,
        ]);

        return redirect()->back()->with('success', 'Kartu PAS berhasil dinonaktifkan.');
    }

    public function updateKuota(Request $request, int $id)
    {
        $request->validate([
            'kuota' => ['required', 'integer', 'min:0'],
        ]);

        Instansi::findOrFail($id)->update(['kuota' => $request->kuota]);

        return redirect()->back()->with('success', 'Kuota berhasil diupdate.');
    }

    public function getDetailAjax(int $id)
    {
        $instansi = Instansi::findOrFail($id);
        $kartuPas = KartuPas::where(function($q) use ($instansi) {
            $q->where('instansi_id', $instansi->id)
              ->orWhere('perusahaan', $instansi->nama_instansi);
        })->latest()->get();

        $kartuAktif    = $kartuPas->where('status', 'aktif')->count();
        $kartuNonaktif = $kartuPas->where('status', '!=', 'aktif')->count();
        $sisaKuota     = $instansi->kuota - $kartuAktif;

        return response()->json([
            'success'  => true,
            'instansi' => [
                'id'            => $instansi->id,
                'nama_instansi' => $instansi->nama_instansi,
                'alamat'        => $instansi->alamat ?? '-',
                'kuota'         => $instansi->kuota,
                'kartu_aktif'   => $kartuAktif,
                'sisa_kuota'    => $sisaKuota,
                'nonaktif'      => $kartuNonaktif,
            ],
            'kartu_pas' => $kartuPas->map(function($k) {
                return [
                    'id'                  => $k->id,
                    'nomor_kartu'         => $k->nomor_kartu,
                    'nama_pemegang'       => $k->nama_pemegang,
                    'area_akses'          => $k->area_akses,
                    'jabatan'             => $k->jabatan ?? '-',
                    'tanggal_berlaku'     => $k->tanggal_berlaku ? $k->tanggal_berlaku->format('d/m/Y') : '-',
                    'status'              => $k->status,
                    'keterangan_nonaktif' => $k->keterangan_nonaktif ? ucfirst($k->keterangan_nonaktif) : null,
                    'catatan_nonaktif'    => $k->catatan_nonaktif,
                ];
            }),
        ]);
    }
}