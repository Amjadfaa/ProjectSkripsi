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
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', 'all');

        // Daftar tahun yang tersedia dari data Kartu PAS
        $tahunList = KartuPas::selectRaw('YEAR(tanggal_terbit) as tahun')
            ->whereNotNull('tanggal_terbit')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        if ($tahunList->isEmpty()) {
            $tahunList = collect([date('Y')]);
        }

        // Laporan bulanan untuk tabel ringkasan & grafik tren
        $laporanKartu = $this->getLaporanKartuPas($tahun);

        // Ringkasan KPI Total (disinkronkan dengan filter tahun dan bulan)
        $kpiQuery = KartuPas::whereYear('tanggal_terbit', $tahun);
        if ($bulan !== 'all' && !empty($bulan)) {
            $kpiQuery->whereMonth('tanggal_terbit', (int)$bulan);
        }

        $totalKartuTerbit   = (clone $kpiQuery)->count();
        $totalKartuAktif    = (clone $kpiQuery)->where('status', 'aktif')->count();
        $totalKadaluarsa    = (clone $kpiQuery)->where('status', 'kadaluarsa')->count();
        $totalNonaktif      = (clone $kpiQuery)->whereIn('status', ['nonaktif', 'tidak_aktif'])->count();

        // Distribusi Kartu per Instansi untuk Chart
        $distribusiInstansiQuery = Instansi::withCount(['kartuPas' => function($q) use ($tahun, $bulan) {
            $q->whereYear('tanggal_terbit', $tahun);
            if ($bulan !== 'all' && !empty($bulan)) {
                $q->whereMonth('tanggal_terbit', (int)$bulan);
            }
        }])->having('kartu_pas_count', '>', 0)->get();

        if ($distribusiInstansiQuery->isEmpty()) {
            $distribusiInstansi = Instansi::withCount(['kartuPas' => function($q) use ($tahun) {
                $q->whereYear('tanggal_terbit', $tahun);
            }])->limit(10)->get();
        } else {
            $distribusiInstansi = $distribusiInstansiQuery;
        }

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $isBulanFilter = (!empty($bulan) && $bulan !== 'all');
        $namaBulanSelected = $isBulanFilter && isset($namaBulanList[(int)$bulan]) ? $namaBulanList[(int)$bulan] : null;

        // Ambil daftar kartu pas detail untuk periode terpilih (mendukung pencarian & filter status)
        $detailQuery = KartuPas::whereYear('tanggal_terbit', $tahun)->with('instansi');
        if ($isBulanFilter) {
            $detailQuery->whereMonth('tanggal_terbit', (int)$bulan);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $detailQuery->where(function ($q) use ($search) {
                $q->where('nomor_kartu', 'like', "%{$search}%")
                  ->orWhere('nama_pemegang', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if (in_array($request->status, ['nonaktif', 'tidak_aktif'])) {
                $detailQuery->whereIn('status', ['nonaktif', 'tidak_aktif']);
            } else {
                $detailQuery->where('status', $request->status);
            }
        }

        $detailKartuPas = $detailQuery->latest('id')->paginate(15)->withQueryString();

        // Respon partial via AJAX untuk paginasi SPA
        if ($request->ajax() || $request->wantsJson() || $request->header('X-SPA')) {
            return view('administrator.laporan.partials.table', compact(
                'detailKartuPas', 'tahun', 'bulan', 'isBulanFilter', 'namaBulanSelected',
                'totalKartuTerbit', 'totalKartuAktif', 'totalKadaluarsa', 'totalNonaktif'
            ));
        }

        return view('administrator.laporan.index', compact(
            'laporanKartu', 'tahun', 'bulan', 'tahunList', 'namaBulanList', 'namaBulanSelected', 'isBulanFilter',
            'totalKartuTerbit', 'totalKartuAktif', 'totalKadaluarsa', 'totalNonaktif',
            'distribusiInstansi', 'detailKartuPas'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tahun        = $request->input('tahun', date('Y'));
        $bulan        = $request->input('bulan', 'all');
        $laporanKartu = $this->getLaporanKartuPas($tahun);

        $detailKartu = null;
        if ($bulan !== 'all' && !empty($bulan)) {
            $detailKartu = KartuPas::whereYear('tanggal_terbit', $tahun)
                ->whereMonth('tanggal_terbit', (int)$bulan)
                ->with('instansi')
                ->latest('id')
                ->get();
        }

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = ($bulan !== 'all' && !empty($bulan) && isset($namaBulanList[(int)$bulan])) ? $namaBulanList[(int)$bulan] : null;

        $pdf = Pdf::loadView('administrator.laporan.pdf', compact('laporanKartu', 'tahun', 'bulan', 'namaBulan', 'detailKartu'))
            ->setPaper('a4', 'landscape');

        $filename = 'laporan-kartu-pas-' . $tahun . ($namaBulan ? '-' . strtolower($namaBulan) : '') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', 'all');

        $filters = [
            'tahun' => $tahun,
        ];
        if ($bulan !== 'all' && !empty($bulan)) {
            $filters['bulan'] = $bulan;
        }

        $filename = 'laporan-kartu-pas-' . $tahun . ($bulan !== 'all' ? '-bulan-' . $bulan : '') . '.xlsx';
        return Excel::download(new KartuPasExport($filters), $filename);
    }

    private function getLaporanKartuPas($tahun)
    {
        $laporanBulanan = collect();

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $baseQuery = KartuPas::whereYear('tanggal_terbit', $tahun)->whereMonth('tanggal_terbit', $bulan);

            $kartuBaru = (clone $baseQuery)
                ->where(function($q) {
                    $q->where('tipe_permohonan', 'baru')
                      ->orWhereNull('tipe_permohonan');
                })
                ->count();

            $kartuDiperpanjang = (clone $baseQuery)
                ->where('tipe_permohonan', 'perpanjangan')
                ->count();

            // Kadaluarsa: kartu yang diterbitkan pada bulan ini dan statusnya kadaluarsa
            $kartuKadaluarsa = (clone $baseQuery)
                ->where('status', 'kadaluarsa')
                ->count();

            $totalTerbitBulan = $kartuBaru + $kartuDiperpanjang;

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