<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\CameraDevice;
use App\Models\ScanLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardOperatorController extends Controller
{
    /**
     * Menampilkan dashboard utama panel operator
     */
    public function index()
    {
        $connectedDevice = null;
        $deviceId = session('camera_device_id');

        if ($deviceId) {
            $connectedDevice = CameraDevice::with('areaAkses')->find($deviceId);
            // Jika perangkat tidak aktif lagi, bersihkan sesi
            if (!$connectedDevice || !$connectedDevice->is_active) {
                session()->forget(['camera_device_id', 'camera_name', 'camera_area', 'camera_type']);
                $connectedDevice = null;
            }
        }

        $today = Carbon::today();

        // Query query scan log
        $logQuery = ScanLog::query();
        if ($connectedDevice) {
            $logQuery->where('camera_device_id', $connectedDevice->id);
        }

        $totalScanHariIni = (clone $logQuery)->whereDate('waktu_scan', $today)->count();
        $scanBerhasilHariIni = (clone $logQuery)->whereDate('waktu_scan', $today)->where('status_akses', 'diterima')->count();
        $scanDitolakHariIni = (clone $logQuery)->whereDate('waktu_scan', $today)->where('status_akses', 'ditolak')->count();

        // Riwayat scan terbaru
        $recentLogs = (clone $logQuery)
            ->with(['cameraDevice', 'kartuPas'])
            ->latest('waktu_scan')
            ->take(8)
            ->get();

        // Jumlah perangkat kamera aktif yang tersedia di sistem
        $totalKameraAktif = CameraDevice::where('is_active', true)->count();

        // Kamera yang ditugaskan khusus untuk operator ini
        $user = auth()->user();
        $assignedCameras = $user ? $user->cameraDevices()->with('areaAkses')->where('is_active', true)->get() : collect();

        return view('operator.dashboard', compact(
            'connectedDevice',
            'totalScanHariIni',
            'scanBerhasilHariIni',
            'scanDitolakHariIni',
            'recentLogs',
            'totalKameraAktif',
            'assignedCameras'
        ));
    }
}
