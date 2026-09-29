<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\AreaAkses;
use App\Models\CameraDevice;
use Illuminate\Http\Request;

class CameraDeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = CameraDevice::with('areaAkses');

        // Pencarian Nama Kamera, Kode Akses, atau Kode Area
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_kamera', 'like', "%{$search}%")
                  ->orWhere('kode_akses', 'like', "%{$search}%")
                  ->orWhere('kode_area', 'like', "%{$search}%");
            });
        }

        // Filter Area
        if ($request->filled('area')) {
            $query->where('kode_area', $request->area);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        // Filter Tipe Scan
        if ($request->filled('tipe_scan')) {
            $query->where('tipe_scan', $request->tipe_scan);
        }

        $perPage = (int) $request->input('per_page', 8);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 8;
        }

        $devices       = $query->latest('id')->paginate($perPage)->withQueryString();
        $areaAksesList = AreaAkses::orderBy('kode')->get();

        // Statistik Cepat untuk KPI Cards
        $totalDevices  = CameraDevice::count();
        $totalAktif    = CameraDevice::where('is_active', true)->count();
        $totalAreaUsed = CameraDevice::distinct('kode_area')->count('kode_area');
        $totalAreaAll  = $areaAksesList->count();

        // Respon Partial untuk SPA Pagination & Filtering
        if ($request->ajax()) {
            return view('administrator.perangkat-kamera.partials.table', compact(
                'devices',
                'areaAksesList',
                'totalDevices',
                'totalAktif',
                'totalAreaUsed',
                'totalAreaAll'
            ));
        }

        return view('administrator.perangkat-kamera.index', compact(
            'devices',
            'areaAksesList',
            'totalDevices',
            'totalAktif',
            'totalAreaUsed',
            'totalAreaAll'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kamera' => ['required', 'string', 'max:255'],
            'kode_area'   => ['required', 'string', 'exists:area_akses,kode'],
            'kode_akses'  => ['required', 'string', 'unique:camera_devices', 'max:255'],
            'tipe_scan'   => ['required', 'in:masuk,keluar,masuk_keluar'],
            'is_active'   => ['required', 'boolean'],
        ]);

        CameraDevice::create($request->all());

        return redirect()->route('administrator.perangkat-kamera.index')
            ->with('success', 'Perangkat kamera berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $device = CameraDevice::findOrFail($id);

        $request->validate([
            'nama_kamera' => ['required', 'string', 'max:255'],
            'kode_area'   => ['required', 'string', 'exists:area_akses,kode'],
            'kode_akses'  => ['required', 'string', 'unique:camera_devices,kode_akses,' . $id, 'max:255'],
            'tipe_scan'   => ['required', 'in:masuk,keluar,masuk_keluar'],
            'is_active'   => ['required', 'boolean'],
        ]);

        $device->update($request->all());

        return redirect()->route('administrator.perangkat-kamera.index')
            ->with('success', 'Perangkat kamera berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        CameraDevice::findOrFail($id)->delete();

        return redirect()->route('administrator.perangkat-kamera.index')
            ->with('success', 'Perangkat kamera berhasil dihapus.');
    }
}
