<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\KartuPas;
use App\Models\Permohonan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanBulananExport;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $totalKartu          = KartuPas::count();
        $totalKartuAktif     = KartuPas::where('status', 'aktif')->count();
        $kartuKadaluarsa     = KartuPas::where('status', 'kadaluarsa')->count();
        $kartuTidakAktif     = KartuPas::where('status', 'tidak_aktif')->count();
        $kartuAkanBerakhir   = KartuPas::where('status', 'aktif')
            ->whereBetween('tanggal_berlaku', [now(), now()->addDays(30)])->count();

        $kartuHampirKadaluarsa = KartuPas::where('status', 'aktif')
            ->whereBetween('tanggal_berlaku', [now(), now()->addDays(30)])
            ->orderBy('tanggal_berlaku')
            ->limit(5)
            ->get();

        $totalInstansi       = Instansi::count();
        $instansiKuotaKritis = Instansi::where('is_active', true)
            ->get()
            ->filter(fn($instansi) => $instansi->sisa_kuota <= 3);

        $totalPerangkat      = CameraDevice::count();
        $perangkatAktif      = CameraDevice::where('is_active', true)->count();

        $recentScanLogs      = ScanLog::latest('waktu_scan')->limit(6)->get();

        $kartuPerInstansi    = KartuPas::where('status', 'aktif')
            ->selectRaw('perusahaan, COUNT(*) as total')
            ->groupBy('perusahaan')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $laporanBulanan      = $this->getLaporanBulanan(date('Y'));

        return view('administrator.dashboard', compact(
            'totalKartu',
            'totalKartuAktif',
            'kartuKadaluarsa',
            'kartuTidakAktif',
            'kartuAkanBerakhir',
            'kartuHampirKadaluarsa',
            'totalInstansi',
            'instansiKuotaKritis',
            'totalPerangkat',
            'perangkatAktif',
            'recentScanLogs',
            'kartuPerInstansi',
            'laporanBulanan'
        ));
    }

    public function exportLaporanPdf(Request $request)
    {
        $tahun          = $request->tahun ?? date('Y');
        $laporanBulanan = $this->getLaporanBulanan($tahun);

        $pdf = Pdf::loadView('administrator.laporan.pdf', compact('laporanBulanan', 'tahun'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-bulanan-' . $tahun . '.pdf');
    }

    public function exportLaporanExcel(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        return Excel::download(new LaporanBulananExport($tahun), 'laporan-bulanan-' . $tahun . '.xlsx');
    }

    private function getLaporanBulanan($tahun)
    {
        $laporanBulanan = collect();

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $totalPermohonanBulan = Permohonan::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)->count();

            $disetujuiBulan = Permohonan::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)->where('status', 'disetujui')->count();

            $ditolakBulan = Permohonan::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)->where('status', 'ditolak')->count();

            $kartuBaruBulan = KartuPas::whereYear('tanggal_terbit', $tahun)
                ->whereMonth('tanggal_terbit', $bulan)->count();

            $kartuKadaluarsaBulan = KartuPas::whereYear('tanggal_berlaku', $tahun)
                ->whereMonth('tanggal_berlaku', $bulan)->where('status', 'kadaluarsa')->count();

            $kartuDiperpanjangBulan = KartuPas::whereYear('updated_at', $tahun)
                ->whereMonth('updated_at', $bulan)->where('status', 'aktif')
                ->whereYear('tanggal_terbit', '!=', $tahun)->count();

            if ($totalPermohonanBulan > 0 || $kartuBaruBulan > 0 || $kartuKadaluarsaBulan > 0) {
                $laporanBulanan->push((object)[
                    'bulan'              => $bulan,
                    'tahun'              => $tahun,
                    'total_permohonan'   => $totalPermohonanBulan,
                    'disetujui'          => $disetujuiBulan,
                    'ditolak'            => $ditolakBulan,
                    'kartu_baru'         => $kartuBaruBulan,
                    'kartu_kadaluarsa'   => $kartuKadaluarsaBulan,
                    'kartu_diperpanjang' => $kartuDiperpanjangBulan,
                ]);
            }
        }

        return $laporanBulanan;
    }
}