<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\CameraDevice;
use App\Models\ScanLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KameraOperatorController extends Controller
{
    /**
     * Menampilkan halaman pemilihan & input kode akses kamera
     */
    public function index()
    {
        $connectedDevice = null;
        $deviceId = session('camera_device_id');

        if ($deviceId) {
            $connectedDevice = CameraDevice::with('areaAkses')->find($deviceId);
            if (!$connectedDevice || !$connectedDevice->is_active) {
                session()->forget(['camera_device_id', 'camera_name', 'camera_area', 'camera_type']);
                $connectedDevice = null;
            }
        }

        $user = auth()->user();

        // Jika operator, ambil hanya perangkat kamera aktif yang ditugaskan kepadanya
        if ($user && $user->role === 'operator') {
            $availableDevices = $user->cameraDevices()
                ->with('areaAkses')
                ->where('is_active', true)
                ->orderBy('nama_kamera')
                ->get();
        } else {
            // Administrator melihat seluruh kamera
            $availableDevices = CameraDevice::with('areaAkses')
                ->where('is_active', true)
                ->orderBy('nama_kamera')
                ->get();
        }

        return view('operator.kamera.index', compact('connectedDevice', 'availableDevices'));
    }

    /**
     * Menghubungkan sesi operator ke perangkat kamera melalui kode akses
     */
    public function connect(Request $request)
    {
        $request->validate([
            'kode_akses' => 'required|string',
        ], [
            'kode_akses.required' => 'Kode akses perangkat kamera wajib diisi.',
        ]);

        $kodeAkses = trim($request->kode_akses);
        $device = CameraDevice::where('kode_akses', $kodeAkses)->first();

        if (!$device) {
            return redirect()->back()
                ->withErrors(['kode_akses' => "Perangkat dengan kode akses '{$kodeAkses}' tidak ditemukan."])
                ->withInput();
        }

        if (!$device->is_active) {
            return redirect()->back()
                ->withErrors(['kode_akses' => "Perangkat kamera '{$device->nama_kamera}' saat ini dinonaktifkan oleh Administrator."])
                ->withInput();
        }

        // Cek otorisasi penugasan jika pengguna adalah Operator
        $user = auth()->user();
        if ($user && $user->role === 'operator') {
            $isAssigned = $user->cameraDevices()->where('camera_devices.id', $device->id)->exists();
            if (!$isAssigned) {
                return redirect()->back()
                    ->withErrors(['kode_akses' => "Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses kamera '{$device->nama_kamera}' ({$device->kode_akses}). Perangkat ini belum ditugaskan oleh Administrator kepada akun Anda."])
                    ->withInput();
            }
        }

        // Set session kamera
        session([
            'camera_device_id' => $device->id,
            'camera_name'      => $device->nama_kamera,
            'camera_area'      => $device->kode_area,
            'camera_type'      => $device->tipe_scan,
        ]);

        return redirect()->route('operator.kamera.scanner')
            ->with('success', "✅ Berhasil terhubung ke perangkat kamera '{$device->nama_kamera}' ({$device->kode_area}).");
    }

    /**
     * Menampilkan live scanner QR kamera di dalam panel operator
     */
    public function scanner()
    {
        if (!session()->has('camera_device_id')) {
            return redirect()->route('operator.kamera.index')
                ->with('error', 'Silakan masukkan Kode Akses atau hubungkan perangkat kamera terlebih dahulu.');
        }

        $device = CameraDevice::with('areaAkses')->find(session('camera_device_id'));

        if (!$device || !$device->is_active) {
            session()->forget(['camera_device_id', 'camera_name', 'camera_area', 'camera_type']);
            return redirect()->route('operator.kamera.index')
                ->with('error', 'Perangkat kamera tidak aktif atau tidak ditemukan.');
        }

        $recentLogs = ScanLog::where('camera_device_id', $device->id)
            ->latest('waktu_scan')
            ->take(10)
            ->get();

        return view('operator.kamera.scanner', compact('device', 'recentLogs'));
    }

    /**
     * Memutuskan sesi koneksi kamera
     */
    public function disconnect()
    {
        $namaKamera = session('camera_name', 'Perangkat Kamera');
        session()->forget(['camera_device_id', 'camera_name', 'camera_area', 'camera_type']);

        return redirect()->route('operator.kamera.index')
            ->with('success', "Koneksi dengan '{$namaKamera}' telah diputuskan.");
    }

    /**
     * Menampilkan log pemindaian di panel operator
     */
    public function logs(Request $request)
    {
        $deviceId = session('camera_device_id');
        $query = ScanLog::with(['cameraDevice.areaAkses', 'kartuPas']);

        if ($deviceId) {
            $query->where('camera_device_id', $deviceId);
        }

        // Hitung statistik untuk KPI Cards
        $statsBase = ScanLog::query();
        if ($deviceId) {
            $statsBase->where('camera_device_id', $deviceId);
        }

        $stats = [
            'total'    => (clone $statsBase)->count(),
            'valid'    => (clone $statsBase)->where('status_akses', 'diterima')->count(),
            'ditolak'  => (clone $statsBase)->where('status_akses', 'ditolak')->count(),
            'hari_ini' => (clone $statsBase)->whereDate('waktu_scan', Carbon::today())->count(),
        ];

        if ($request->filled('tanggal')) {
            $query->whereDate('waktu_scan', $request->tanggal);
        }

        if ($request->filled('status')) {
            if ($request->status === 'valid' || $request->status === 'diterima') {
                $query->where('status_akses', 'diterima');
            } elseif ($request->status === 'ditolak') {
                $query->where('status_akses', 'ditolak');
            }
        }

        if ($request->filled('tipe_aktivitas') && in_array($request->tipe_aktivitas, ['masuk', 'keluar'])) {
            $query->where('tipe_aktivitas', $request->tipe_aktivitas);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kartu', 'LIKE', "%{$search}%")
                  ->orWhere('nama_pemegang', 'LIKE', "%{$search}%")
                  ->orWhere('perusahaan', 'LIKE', "%{$search}%");
            });
        }

        $logs = $query->latest('waktu_scan')->paginate(15)->withQueryString();
        $connectedDevice = $deviceId ? CameraDevice::with('areaAkses')->find($deviceId) : null;

        return view('operator.kamera.logs', compact('logs', 'connectedDevice', 'stats'));
    }
}
