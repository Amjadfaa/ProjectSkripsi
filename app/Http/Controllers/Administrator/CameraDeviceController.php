<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\AreaAkses;
use App\Models\CameraDevice;
use Illuminate\Http\Request;

class CameraDeviceController extends Controller
{
    public function index()
    {
        $devices       = CameraDevice::with('areaAkses')->latest()->get();
        $areaAksesList = AreaAkses::orderBy('kode')->get();

        return view('administrator.perangkat-kamera.index', compact('devices', 'areaAksesList'));
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
