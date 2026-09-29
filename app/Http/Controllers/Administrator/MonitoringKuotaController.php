<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\KartuPas;
use Illuminate\Http\Request;


class MonitoringKuotaController extends Controller
{
    public function index(Request $request)
    {
        Instansi::syncUnlinkedKartuPas();

        $baseQuery = Instansi::where('is_active', true)
            ->withCount([
                'kartuPas as total_kartu',
                'kartuPas as kartu_aktif' => function($q) {
                    $q->where('status', 'aktif');
                },
                'kartuPas as kartu_kadaluarsa' => function($q) {
                    $q->where('status', 'kadaluarsa');
                },
                'kartuPas as kartu_nonaktif' => function($q) {
                    $q->where('status', 'tidak_aktif');
                },
                'kartuPas as kartu_terpakai' => function($q) {
                    $q->where('status', '!=', 'tidak_aktif');
                },
            ]);

        // Search filter
        $search = $request->input('search');
        if ($search) {
            $baseQuery->where('nama_instansi', 'like', "%{$search}%");
        }

        // Ambil semua data (tanpa pagination) untuk KPI & chart
        $allInstansis = (clone $baseQuery)->get()->map(function($instansi) {
            $instansi->sisa_kuota = max(0, $instansi->kuota - $instansi->kartu_terpakai);
            return $instansi;
        });

        // Paginate untuk tabel
        $perPage = $request->input('per_page', 10);
        $instansis = $baseQuery->orderBy('nama_instansi')
            ->paginate($perPage)
            ->through(function($instansi) {
                $instansi->sisa_kuota = max(0, $instansi->kuota - $instansi->kartu_terpakai);
                return $instansi;
            })
            ->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'table_html'     => view('administrator.monitoring-kuota.partials.table', compact('instansis'))->render(),
                'total_instansi' => $instansis->total(),
                'kpi' => [
                    'totalKuota'    => number_format($allInstansis->sum('kuota')),
                    'totalAktif'    => number_format($allInstansis->sum('kartu_aktif')),
                    'totalSisa'     => number_format($allInstansis->sum('sisa_kuota')),
                    'totalPersen'   => $allInstansis->sum('kuota') > 0 ? round(min(($allInstansis->sum('kartu_aktif') / $allInstansis->sum('kuota')) * 100, 100), 1) : 0,
                    'totalNonaktif' => number_format($allInstansis->sum('kartu_nonaktif')),
                ],
                'chart' => [
                    'labels'          => $allInstansis->pluck('nama_instansi')->values()->toArray(),
                    'totalKuota'      => $allInstansis->pluck('kuota')->values()->toArray(),
                    'kartuAktif'      => $allInstansis->pluck('kartu_aktif')->values()->toArray(),
                    'sisaKuota'       => $allInstansis->pluck('sisa_kuota')->values()->toArray(),
                    'totalAktifAll'   => (int) $allInstansis->sum('kartu_aktif'),
                    'totalSisaAll'    => (int) $allInstansis->sum('sisa_kuota'),
                    'totalNonaktifAll' => (int) $allInstansis->sum('kartu_nonaktif'),
                ]
            ]);
        }

        return view('administrator.monitoring-kuota.index', compact('instansis', 'allInstansis', 'search'));
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kartu PAS berhasil dinonaktifkan.'
            ]);
        }

        return redirect()->back()->with('success', 'Kartu PAS berhasil dinonaktifkan.');
    }

    public function updateKuota(Request $request, int $id)
    {
        $request->validate([
            'kuota' => ['required', 'integer', 'min:0'],
        ]);

        Instansi::findOrFail($id)->update(['kuota' => $request->kuota]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kuota berhasil diupdate.'
            ]);
        }

        return redirect()->back()->with('success', 'Kuota berhasil diupdate.');
    }

    public function getDetailAjax(int $id)
    {
        $instansi = Instansi::findOrFail($id);
        $kartuPas = KartuPas::where(function($q) use ($instansi) {
            $q->where('instansi_id', $instansi->id)
              ->orWhere('perusahaan', $instansi->nama_instansi);
        })->latest()->get();

        $kartuAktif      = $kartuPas->where('status', 'aktif')->count();
        $kartuKadaluarsa = $kartuPas->where('status', 'kadaluarsa')->count();
        $kartuNonaktif   = $kartuPas->where('status', 'tidak_aktif')->count();
        $kartuTerpakai   = $kartuAktif + $kartuKadaluarsa;
        $sisaKuota       = max(0, $instansi->kuota - $kartuTerpakai);

        return response()->json([
            'success'  => true,
            'instansi' => [
                'id'               => $instansi->id,
                'nama_instansi'    => $instansi->nama_instansi,
                'alamat'           => $instansi->alamat ?? '-',
                'kuota'            => $instansi->kuota,
                'kartu_aktif'      => $kartuAktif,
                'kartu_kadaluarsa' => $kartuKadaluarsa,
                'kartu_terpakai'   => $kartuTerpakai,
                'sisa_kuota'       => $sisaKuota,
                'nonaktif'         => $kartuNonaktif,
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