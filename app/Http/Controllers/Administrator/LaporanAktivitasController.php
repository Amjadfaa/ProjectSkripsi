<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\ScanLog;
use App\Models\AreaAkses;
use App\Models\CameraDevice;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAktivitasExport;
use Illuminate\Http\Request;

class LaporanAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $startDate    = $request->get('start_date', date('Y-m-01'));
        $endDate      = $request->get('end_date', date('Y-m-d'));
        $kodeArea     = $request->get('kode_area');
        $statusAkses  = $request->get('status_akses');
        $cameraId     = $request->get('camera_device_id');
        $search       = $request->get('search');

        $query = ScanLog::with('cameraDevice')
            ->whereDate('waktu_scan', '>=', $startDate)
            ->whereDate('waktu_scan', '<=', $endDate);

        if (!empty($kodeArea)) {
            $query->where('kode_area', $kodeArea);
        }

        if (!empty($statusAkses)) {
            $query->where('status_akses', $statusAkses);
        }

        if (!empty($cameraId)) {
            $query->where('camera_device_id', $cameraId);
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_kartu', 'like', "%{$search}%")
                  ->orWhere('nama_pemegang', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        // Summary KPI
        $totalScan    = (clone $query)->count();
        $totalDiterima = (clone $query)->where('status_akses', 'diterima')->count();
        $totalDitolak  = (clone $query)->where('status_akses', 'ditolak')->count();

        // Paginated log data
        $scanLogs = $query->latest('waktu_scan')->paginate(15)->withQueryString();

        // Dropdown filter master lists
        $areaAksesList = AreaAkses::orderBy('kode')->get();
        $cameraDevices = CameraDevice::orderBy('nama_kamera')->get();

        // Distribution data for Chart
        $chartDataArea = ScanLog::selectRaw('kode_area, count(*) as total')
            ->whereDate('waktu_scan', '>=', $startDate)
            ->whereDate('waktu_scan', '<=', $endDate)
            ->groupBy('kode_area')
            ->pluck('total', 'kode_area');

        return view('administrator.laporan-aktivitas.index', compact(
            'scanLogs', 'startDate', 'endDate', 'kodeArea', 'statusAkses', 'cameraId', 'search',
            'totalScan', 'totalDiterima', 'totalDitolak',
            'areaAksesList', 'cameraDevices', 'chartDataArea'
        ));
    }

    public function exportPdf(Request $request)
    {
        $startDate   = $request->get('start_date', date('Y-m-01'));
        $endDate     = $request->get('end_date', date('Y-m-d'));
        $kodeArea    = $request->get('kode_area');
        $statusAkses = $request->get('status_akses');

        $query = ScanLog::with('cameraDevice')
            ->whereDate('waktu_scan', '>=', $startDate)
            ->whereDate('waktu_scan', '<=', $endDate);

        if (!empty($kodeArea)) {
            $query->where('kode_area', $kodeArea);
        }

        if (!empty($statusAkses)) {
            $query->where('status_akses', $statusAkses);
        }

        $logs = $query->latest('waktu_scan')->get();

        $pdf = Pdf::loadView('administrator.laporan-aktivitas.pdf', compact('logs', 'startDate', 'endDate', 'kodeArea', 'statusAkses'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-aktivitas-area-' . date('Ymd', strtotime($startDate)) . '-sd-' . date('Ymd', strtotime($endDate)) . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate   = $request->get('start_date', date('Y-m-01'));
        $endDate     = $request->get('end_date', date('Y-m-d'));
        $kodeArea    = $request->get('kode_area');
        $statusAkses = $request->get('status_akses');

        return Excel::download(
            new LaporanAktivitasExport($startDate, $endDate, $kodeArea, $statusAkses),
            'laporan-aktivitas-scan.xlsx'
        );
    }
}
