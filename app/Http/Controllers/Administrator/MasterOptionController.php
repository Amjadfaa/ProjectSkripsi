<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\AreaAkses;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class MasterOptionController extends Controller
{
    public function storeAreaAkses(Request $request)
    {
        $request->validate([
            'kode'       => ['required', 'string', 'max:10', 'unique:area_akses,kode'],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        $kodeClean = strtoupper(trim($request->kode));
        $area = AreaAkses::create([
            'kode'       => $kodeClean,
            'keterangan' => trim($request->keterangan),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Area Akses berhasil ditambahkan.',
            'data'       => [
                'id'         => $area->id,
                'kode'       => $area->kode,
                'keterangan' => $area->keterangan,
                'value'      => $area->kode,
                'label'      => $area->kode . ': ' . $area->keterangan,
            ],
        ]);
    }

    public function deleteAreaAkses(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:area_akses,id'],
        ]);

        $area = AreaAkses::findOrFail($request->id);
        $kode = $area->kode;
        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Area Akses berhasil dihapus.',
            'id'      => $request->id,
            'kode'    => $kode,
        ]);
    }

    public function storeJabatan(Request $request)
    {
        $request->validate([
            'nama_jabatan' => ['required', 'string', 'max:255', 'unique:jabatans,nama_jabatan'],
        ]);

        $namaClean = trim($request->nama_jabatan);
        $jabatan = Jabatan::create([
            'nama_jabatan' => $namaClean,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil ditambahkan.',
            'data'    => [
                'id'           => $jabatan->id,
                'nama_jabatan' => $jabatan->nama_jabatan,
                'value'        => $jabatan->nama_jabatan,
                'label'        => $jabatan->nama_jabatan,
            ],
        ]);
    }

    public function deleteJabatan(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:jabatans,id'],
        ]);

        $jabatan = Jabatan::findOrFail($request->id);
        $nama = $jabatan->nama_jabatan;
        $jabatan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil dihapus.',
            'id'      => $request->id,
            'nama'    => $nama,
        ]);
    }
}
