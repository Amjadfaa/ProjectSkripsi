<?php

namespace App\Http\Controllers;

use App\Models\CameraDevice;
use App\Models\KartuPas;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScanController extends Controller
{
    public function loginForm()
    {
        return view('auth.login', ['loginMode' => 'camera']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'kode_akses' => ['required', 'string'],
        ], [
            'kode_akses.required' => 'Kode akses kamera wajib diisi.',
        ]);

        $device = CameraDevice::where('kode_akses', trim($request->kode_akses))->first();

        if (!$device) {
            return redirect()->back()->withErrors(['kode_akses' => 'Kode akses perangkat tidak ditemukan!'])->withInput();
        }

        if (!$device->is_active) {
            return redirect()->back()->withErrors(['kode_akses' => 'Perangkat kamera ini sedang dinonaktifkan oleh Administrator.'])->withInput();
        }

        session([
            'camera_device_id' => $device->id,
            'camera_name'      => $device->nama_kamera,
            'camera_area'      => $device->kode_area,
            'camera_type'      => $device->tipe_scan,
        ]);

        return redirect()->route('scan.scanner')->with('success', 'Berhasil terhubung ke perangkat kamera ' . $device->nama_kamera);
    }

    public function scanner()
    {
        if (!session()->has('camera_device_id')) {
            return redirect()->route('login')->with('error', 'Silakan masukan Kode Akses Perangkat Kamera terlebih dahulu.');
        }

        $device = CameraDevice::with('areaAkses')->find(session('camera_device_id'));

        if (!$device || !$device->is_active) {
            session()->forget(['camera_device_id', 'camera_name', 'camera_area', 'camera_type']);
            return redirect()->route('login')->with('error', 'Perangkat tidak aktif atau tidak ditemukan.');
        }

        $recentLogs = ScanLog::where('camera_device_id', $device->id)
            ->latest('waktu_scan')
            ->take(10)
            ->get();

        return view('scan.scanner', compact('device', 'recentLogs'));
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        if (!session()->has('camera_device_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi perangkat kamera telah berakhir. Silakan login kembali.',
            ], 401);
        }

        $device = CameraDevice::find(session('camera_device_id'));
        if (!$device || !$device->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat kamera tidak aktif atau tidak valid.',
            ], 403);
        }

        $nomorKartu = trim($request->qr_code);
        $waktuNow   = Carbon::now();

        // Check for duplicate scan within 60 seconds at this camera device
        $recentScan = ScanLog::where('camera_device_id', $device->id)
            ->where('nomor_kartu', $nomorKartu)
            ->where('waktu_scan', '>=', Carbon::now()->subSeconds(60))
            ->latest('waktu_scan')
            ->first();

        if ($recentScan) {
            $secondsAgo = (int) Carbon::now()->diffInSeconds($recentScan->waktu_scan);
            $remaining  = max(1, 60 - $secondsAgo);
            return response()->json([
                'success' => false,
                'status'  => 'cooldown',
                'message' => 'JEDA SCAN (Anti-Redundansi 1 Menit)',
                'alasan'  => 'Kartu ini baru saja di-scan ' . $secondsAgo . 's lalu (Tunggu ' . $remaining . 's)',
                'data'    => [
                    'nomor_kartu'   => $recentScan->nomor_kartu,
                    'nama_pemegang' => $recentScan->nama_pemegang,
                    'perusahaan'    => $recentScan->perusahaan,
                    'remaining'     => $remaining,
                    'waktu'         => $waktuNow->translatedFormat('d F Y - H:i:s'),
                ]
            ]);
        }

        $kartu = KartuPas::with('instansi')->where('nomor_kartu', $nomorKartu)->first();

        if (!$kartu) {
            // Log failed scan
            ScanLog::create([
                'camera_device_id' => $device->id,
                'kode_area'        => $device->kode_area,
                'nomor_kartu'      => $nomorKartu,
                'nama_pemegang'    => '-',
                'perusahaan'       => '-',
                'status_akses'     => 'ditolak',
                'alasan'           => 'Nomor Kartu PAS tidak terdaftar',
                'waktu_scan'       => $waktuNow,
            ]);

            return response()->json([
                'success' => false,
                'status'  => 'ditolak',
                'message' => 'AKSES DITOLAK: Kartu PAS tidak terdaftar dalam sistem!',
                'alasan'  => 'Nomor Kartu PAS tidak terdaftar',
                'data'    => [
                    'nomor_kartu' => $nomorKartu,
                    'waktu'       => $waktuNow->translatedFormat('d F Y - H:i:s'),
                ]
            ]);
        }

        // Check active status
        if ($kartu->status !== 'aktif') {
            ScanLog::create([
                'camera_device_id' => $device->id,
                'kode_area'        => $device->kode_area,
                'nomor_kartu'      => $kartu->nomor_kartu,
                'nama_pemegang'    => $kartu->nama_pemegang,
                'perusahaan'       => $kartu->perusahaan,
                'status_akses'     => 'ditolak',
                'alasan'           => 'Status kartu PAS ' . strtoupper($kartu->status),
                'waktu_scan'       => $waktuNow,
            ]);

            return response()->json([
                'success' => false,
                'status'  => 'ditolak',
                'message' => 'AKSES DITOLAK: Status Kartu PAS ' . strtoupper($kartu->status) . '!',
                'alasan'  => 'Status kartu PAS ' . strtoupper($kartu->status),
                'data'    => [
                    'nomor_kartu'   => $kartu->nomor_kartu,
                    'nama_pemegang' => $kartu->nama_pemegang,
                    'perusahaan'    => $kartu->perusahaan,
                    'waktu'         => $waktuNow->translatedFormat('d F Y - H:i:s'),
                ]
            ]);
        }

        // Check expiration
        if ($kartu->tanggal_kadaluarsa && Carbon::parse($kartu->tanggal_kadaluarsa)->endOfDay()->isPast()) {
            ScanLog::create([
                'camera_device_id' => $device->id,
                'kode_area'        => $device->kode_area,
                'nomor_kartu'      => $kartu->nomor_kartu,
                'nama_pemegang'    => $kartu->nama_pemegang,
                'perusahaan'       => $kartu->perusahaan,
                'status_akses'     => 'ditolak',
                'alasan'           => 'Kartu PAS sudah Kadaluarsa (' . Carbon::parse($kartu->tanggal_kadaluarsa)->format('d/m/Y') . ')',
                'waktu_scan'       => $waktuNow,
            ]);

            return response()->json([
                'success' => false,
                'status'  => 'ditolak',
                'message' => 'AKSES DITOLAK: Masa berlaku Kartu PAS sudah habis (Kadaluarsa)!',
                'alasan'  => 'Kartu PAS Kadaluarsa',
                'data'    => [
                    'nomor_kartu'   => $kartu->nomor_kartu,
                    'nama_pemegang' => $kartu->nama_pemegang,
                    'perusahaan'    => $kartu->perusahaan,
                    'waktu'         => $waktuNow->translatedFormat('d F Y - H:i:s'),
                ]
            ]);
        }

        // Check area access
        $userAreas = array_map('trim', explode(',', $kartu->area_akses ?? ''));
        if (!in_array($device->kode_area, $userAreas)) {
            ScanLog::create([
                'camera_device_id' => $device->id,
                'kode_area'        => $device->kode_area,
                'nomor_kartu'      => $kartu->nomor_kartu,
                'nama_pemegang'    => $kartu->nama_pemegang,
                'perusahaan'       => $kartu->perusahaan,
                'status_akses'     => 'ditolak',
                'alasan'           => 'Tidak memiliki hak akses di Area ' . $device->kode_area,
                'waktu_scan'       => $waktuNow,
            ]);

            return response()->json([
                'success' => false,
                'status'  => 'ditolak',
                'message' => 'AKSES DITOLAK: Pemegang kartu tidak memiliki ijin akses di Area ' . $device->kode_area . '!',
                'alasan'  => 'Area Akses Tidak Sesuai (Akses Dibatasi)',
                'data'    => [
                    'nomor_kartu'   => $kartu->nomor_kartu,
                    'nama_pemegang' => $kartu->nama_pemegang,
                    'perusahaan'    => $kartu->perusahaan,
                    'area_dimiliki' => $kartu->area_akses,
                    'area_kamera'   => $device->kode_area,
                    'waktu'         => $waktuNow->translatedFormat('d F Y - H:i:s'),
                ]
            ]);
        }

        // ACCESS GRANTED!
        ScanLog::create([
            'camera_device_id' => $device->id,
            'kode_area'        => $device->kode_area,
            'nomor_kartu'      => $kartu->nomor_kartu,
            'nama_pemegang'    => $kartu->nama_pemegang,
            'perusahaan'       => $kartu->perusahaan,
            'status_akses'     => 'diterima',
            'alasan'           => 'Akses Diterima di Area ' . $device->kode_area,
            'waktu_scan'       => $waktuNow,
        ]);

        return response()->json([
            'success' => true,
            'status'  => 'diterima',
            'message' => 'AKSES DITERIMA DI AREA ' . $device->kode_area,
            'alasan'  => 'Valid & Diizinkan di Area ' . $device->kode_area,
            'data'    => [
                'nomor_kartu'   => $kartu->nomor_kartu,
                'nama_pemegang' => $kartu->nama_pemegang,
                'perusahaan'    => $kartu->perusahaan,
                'jabatan'       => $kartu->jabatan,
                'area_akses'    => $kartu->area_akses,
                'area_kamera'   => $device->kode_area,
                'foto'          => $kartu->foto ? asset('storage/' . $kartu->foto) : null,
                'waktu'         => $waktuNow->translatedFormat('d F Y - H:i:s'),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget(['camera_device_id', 'camera_name', 'camera_area', 'camera_type']);
        return redirect()->route('login')->with('success', 'Perangkat Kamera telah di-logout.');
    }
}
