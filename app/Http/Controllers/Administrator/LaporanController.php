<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\KartuPas;
use App\Models\Permohonan;
use App\Exports\LaporanBulananExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun          = $request->tahun ?? date('Y');
        $laporanBulanan = $this->getLaporanBulanan($tahun);

        // Daftar tahun yang tersedia
        $tahunList = Permohonan::selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('administrator.laporan.index', compact('laporanBulanan', 'tahun', 'tahunList'));
    }

    public function exportPdf(Request $request)
    {
        $tahun          = $request->tahun ?? date('Y');
        $laporanBulanan = $this->getLaporanBulanan($tahun);

        $pdf = Pdf::loadView('administrator.laporan.pdf', compact('laporanBulanan', 'tahun'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-bulanan-' . $tahun . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        return Excel::download(new LaporanBulananExport($tahun), 'laporan-bulanan-' . $tahun . '.xlsx');
    }

    private function getLaporanBulanan($tahun)
    {
        $laporanBulanan = collect();

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $totalPermohonan   = Permohonan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->count();
            $disetujui         = Permohonan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->where('status', 'disetujui')->count();
            $ditolak           = Permohonan::whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->where('status', 'ditolak')->count();
            $kartuBaru         = KartuPas::whereYear('tanggal_terbit', $tahun)->whereMonth('tanggal_terbit', $bulan)->count();
            $kartuKadaluarsa   = KartuPas::whereYear('tanggal_berlaku', $tahun)->whereMonth('tanggal_berlaku', $bulan)->where('status', 'kadaluarsa')->count();
            $kartuDiperpanjang = KartuPas::whereYear('updated_at', $tahun)->whereMonth('updated_at', $bulan)->where('status', 'aktif')->whereYear('tanggal_terbit', '!=', $tahun)->count();

            if ($totalPermohonan > 0 || $kartuBaru > 0 || $kartuKadaluarsa > 0) {
                $laporanBulanan->push((object)[
                    'bulan'              => $bulan,
                    'tahun'              => $tahun,
                    'total_permohonan'   => $totalPermohonan,
                    'disetujui'          => $disetujui,
                    'ditolak'            => $ditolak,
                    'kartu_baru'         => $kartuBaru,
                    'kartu_kadaluarsa'   => $kartuKadaluarsa,
                    'kartu_diperpanjang' => $kartuDiperpanjang,
                ]);
            }
        }

        return $laporanBulanan;
    }
}