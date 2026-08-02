<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\KartuPas;
use App\Models\Instansi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KartuPasExport;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');

        // Daftar tahun yang tersedia dari data Kartu PAS
        $tahunList = KartuPas::selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($tahunList->isEmpty()) {
            $tahunList = collect([date('Y')]);
        }

        $laporanKartu = $this->getLaporanKartuPas($tahun);

        // Ringkasan KPI Total
        $totalKartuTerbit   = KartuPas::whereYear('tanggal_terbit', $tahun)->count();
        $totalKartuAktif    = KartuPas::where('status', 'aktif')->count();
        $totalKadaluarsa    = KartuPas::where('status', 'kadaluarsa')->count();
        $totalNonaktif      = KartuPas::where('status', 'nonaktif')->count();

        // Distribusi Kartu per Instansi untuk Chart
        $distribusiInstansi = Instansi::withCount(['kartuPas' => function($q) use ($tahun) {
            $q->whereYear('tanggal_terbit', $tahun);
        }])->get();

        return view('administrator.laporan.index', compact(
            'laporanKartu', 'tahun', 'tahunList',
            'totalKartuTerbit', 'totalKartuAktif', 'totalKadaluarsa', 'totalNonaktif',
            'distribusiInstansi'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tahun        = $request->tahun ?? date('Y');
        $laporanKartu = $this->getLaporanKartuPas($tahun);

        $pdf = Pdf::loadView('administrator.laporan.pdf', compact('laporanKartu', 'tahun'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kartu-pas-' . $tahun . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new KartuPasExport, 'laporan-kartu-pas.xlsx');
    }

    private function getLaporanKartuPas($tahun)
    {
        $laporanBulanan = collect();

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $kartuBaru         = KartuPas::whereYear('tanggal_terbit', $tahun)->whereMonth('tanggal_terbit', $bulan)->count();
            $kartuKadaluarsa   = KartuPas::whereYear('tanggal_berlaku', $tahun)->whereMonth('tanggal_berlaku', $bulan)->where('status', 'kadaluarsa')->count();
            $kartuDiperpanjang = KartuPas::whereYear('updated_at', $tahun)->whereMonth('updated_at', $bulan)->where('status', 'aktif')->whereYear('tanggal_terbit', '!=', $tahun)->count();
            $totalTerbitBulan  = $kartuBaru + $kartuDiperpanjang;

            $laporanBulanan->push((object)[
                'bulan'              => $bulan,
                'tahun'              => $tahun,
                'kartu_baru'         => $kartuBaru,
                'kartu_kadaluarsa'   => $kartuKadaluarsa,
                'kartu_diperpanjang' => $kartuDiperpanjang,
                'total_terbit'       => $totalTerbitBulan,
            ]);
        }

        return $laporanBulanan;
    }
}