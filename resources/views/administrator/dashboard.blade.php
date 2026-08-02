<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i> Dashboard Monitoring Kartu PAS
            </h2>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                <i class="fas fa-clock text-blue-600"></i>
                <span>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <!-- HERO WELCOME BANNER -->
    <div class="relative overflow-hidden rounded-2xl shadow-xl mb-6 text-white p-6 sm:p-8"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);">
        
        <!-- Background Decorative Ornaments -->
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <i class="fas fa-plane-departure text-[220px]"></i>
        </div>
        <div class="absolute right-1/3 -top-10 opacity-10 pointer-events-none">
            <i class="fas fa-id-card text-[180px]"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl font-black text-white shadow-inner">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-2 border-slate-900 rounded-full" title="Status: Online"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="bg-blue-400/20 text-blue-200 border border-blue-300/30 text-[11px] font-semibold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                            Administrator System
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Selamat Datang Kembali, {{ auth()->user()->name }}! 👋
                    </h1>
                    <p class="text-blue-100/80 text-sm mt-1">
                        Sistem Monitoring PAS Bandara & Control Hub Hak Akses Area Terbatas.
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap gap-2.5 shrink-0">
                <a href="{{ route('administrator.kartu-pas.index') }}" 
                   class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle text-blue-600"></i> Tambah Kartu PAS
                </a>
                <a href="{{ route('administrator.instansi.index') }}" 
                   class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl border border-white/20 backdrop-blur-sm flex items-center gap-2 transition-all">
                    <i class="fas fa-building text-blue-300"></i> Kelola Instansi
                </a>
                <a href="{{ route('administrator.perangkat-kamera.index') }}" 
                   class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-4 py-2.5 rounded-xl border border-white/20 backdrop-blur-sm flex items-center gap-2 transition-all">
                    <i class="fas fa-camera text-blue-300"></i> Scanner Area
                </a>
            </div>
        </div>
    </div>

    <!-- STAT METRICS CARDS GRID (5 CARDS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        
        <!-- 1. Total Kartu PAS -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kartu</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-800 tracking-tight mb-1">{{ number_format($totalKartu) }}</div>
            <div class="flex items-center gap-1 text-xs text-slate-500">
                <span class="font-semibold text-blue-600">100%</span> terdaftar di database
            </div>
        </div>

        <!-- 2. Kartu Aktif -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kartu Aktif</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-600 tracking-tight mb-1">{{ number_format($totalKartuAktif) }}</div>
            <div class="flex items-center gap-1 text-xs text-slate-500">
                @php $persenAktif = $totalKartu > 0 ? round(($totalKartuAktif / $totalKartu) * 100) : 0; @endphp
                <span class="font-bold text-emerald-600">{{ $persenAktif }}%</span> berstatus aktif
            </div>
        </div>

        <!-- 3. Akan Berakhir (30 Hari) -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akan Berakhir</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-amber-600 tracking-tight mb-1">{{ number_format($kartuAkanBerakhir) }}</div>
            <div class="flex items-center gap-1 text-xs text-slate-500">
                Kadaluarsa &le; 30 hari
            </div>
        </div>

        <!-- 4. Kadaluarsa -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kadaluarsa</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-rose-600 tracking-tight mb-1">{{ number_format($kartuKadaluarsa) }}</div>
            <div class="flex items-center gap-1 text-xs text-slate-500">
                Perlu diperpanjang
            </div>
        </div>

        <!-- 5. Instansi & Scanner -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Instansi / Cam</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-purple-600 tracking-tight mb-1">{{ $totalInstansi }}</div>
            <div class="flex items-center gap-1 text-xs text-slate-500">
                <span class="font-semibold text-purple-600">{{ $perangkatAktif }}</span> scanner online
            </div>
        </div>

    </div>

    <!-- WARNING ALERTS & QUICK STATUS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Peringatan Masa Berlaku Kartu PAS (<30 Hari) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b mb-4">
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-500"></i> Peringatan Masa Berlaku Kartu PAS
                    </h3>
                    <a href="{{ route('administrator.kartu-pas.index', ['status' => 'aktif']) }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                        Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                @if($kartuHampirKadaluarsa->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($kartuHampirKadaluarsa as $kartu)
                            @php 
                                $sisaHari = now()->diffInDays($kartu->tanggal_berlaku); 
                                $isUrgent = $sisaHari <= 7;
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl border {{ $isUrgent ? 'bg-rose-50/60 border-rose-200' : 'bg-amber-50/50 border-amber-200' }} transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg {{ $isUrgent ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center font-bold text-sm shrink-0">
                                        <i class="fas {{ $isUrgent ? 'fa-exclamation-circle' : 'fa-clock' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm leading-tight">
                                            {{ $kartu->nama_pemegang }}
                                            <span class="text-xs font-normal text-slate-500">({{ $kartu->nomor_kartu }})</span>
                                        </p>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $kartu->perusahaan }} &bull; Area: <span class="font-semibold text-slate-700">{{ $kartu->area_akses }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $isUrgent ? 'bg-rose-600 text-white shadow-sm' : 'bg-amber-500 text-white shadow-sm' }}">
                                        {{ $sisaHari == 0 ? 'Hari Ini' : $sisaHari . ' Hari' }}
                                    </span>
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $kartu->tanggal_berlaku->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <i class="fas fa-check-circle text-emerald-500 text-3xl mb-2"></i>
                        <p class="text-sm font-semibold text-slate-700">Semua Kartu PAS Berstatus Aman</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada kartu aktif yang akan habis masa berlaku dalam 30 hari ke depan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Instansi Kuota Kritis & Perangkat Scanner -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b mb-4">
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <i class="fas fa-battery-quarter text-rose-500"></i> Kuota Instansi Kritis
                    </h3>
                    <a href="{{ route('administrator.monitoring-kuota.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                        Detail <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

                @if($instansiKuotaKritis->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($instansiKuotaKritis as $inst)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ $inst->nama_instansi }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Kuota: {{ $inst->kuota }} | Terpakai: {{ $inst->terpakai }}</p>
                                </div>
                                <span class="bg-rose-100 text-rose-700 font-extrabold text-xs px-2.5 py-1 rounded-lg">
                                    Sisa {{ $inst->sisa_kuota }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-6 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 mb-4">
                        <i class="fas fa-shield-alt text-blue-500 text-2xl mb-1"></i>
                        <p class="text-xs font-semibold text-slate-700">Kuota Instansi Masih Cukup</p>
                    </div>
                @endif

                <!-- Perangkat Scanner Status Widget -->
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-camera text-blue-600"></i> Scanner Perangkat
                        </span>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            {{ $perangkatAktif }} / {{ $totalPerangkat }} Aktif
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        @php $persenCam = $totalPerangkat > 0 ? round(($perangkatAktif / $totalPerangkat) * 100) : 0; @endphp
                        <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $persenCam }}%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- CHARTS SECTION GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Donut Chart: Distribusi Status Kartu PAS -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b mb-4">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fas fa-pie-chart text-blue-600"></i> Distribusi Status Kartu PAS
                </h3>
                <span class="text-xs text-slate-400 font-medium">Realtime Data</span>
            </div>
            
            <div class="relative flex items-center justify-center my-2" style="height: 240px;">
                <canvas id="chartStatusKartu"></canvas>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-4 border-t text-center">
                <div class="p-2 rounded-xl bg-emerald-50/50 border border-emerald-100">
                    <p class="text-[11px] font-semibold text-slate-500">Aktif</p>
                    <p class="text-lg font-bold text-emerald-600">{{ number_format($totalKartuAktif) }}</p>
                </div>
                <div class="p-2 rounded-xl bg-rose-50/50 border border-rose-100">
                    <p class="text-[11px] font-semibold text-slate-500">Kadaluarsa</p>
                    <p class="text-lg font-bold text-rose-600">{{ number_format($kartuKadaluarsa) }}</p>
                </div>
                <div class="p-2 rounded-xl bg-slate-100/70 border border-slate-200">
                    <p class="text-[11px] font-semibold text-slate-500">Tidak Aktif</p>
                    <p class="text-lg font-bold text-slate-600">{{ number_format($kartuTidakAktif) }}</p>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Kartu PAS Aktif per Instansi -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b mb-4">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fas fa-bar-chart text-indigo-600"></i> Kartu PAS Aktif per Instansi Top 6
                </h3>
                <a href="{{ route('administrator.instansi.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Kelola Instansi
                </a>
            </div>

            <div class="relative my-2" style="height: 280px;">
                <canvas id="chartInstansi"></canvas>
            </div>
        </div>

    </div>

    <!-- RECENT SCAN LOGS FEED -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-6">
        <div class="flex items-center justify-between pb-4 border-b mb-4">
            <div>
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fas fa-stream text-blue-600"></i> Log Aktivitas Scan Masuk Terbaru
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan realtime scan QR Code dari perangkat kamera bandara.</p>
            </div>
            <a href="{{ route('administrator.laporan-aktivitas.index') }}" 
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2 rounded-xl transition">
                Lihat Seluruh Log Aktivitas <i class="fas fa-chevron-right text-[10px] ml-1"></i>
            </a>
        </div>

        @if($recentScanLogs->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b">
                            <th class="px-4 py-3 rounded-l-lg">Waktu Scan</th>
                            <th class="px-4 py-3">Nama Pemegang</th>
                            <th class="px-4 py-3">No. Kartu</th>
                            <th class="px-4 py-3">Instansi</th>
                            <th class="px-4 py-3">Kode Area</th>
                            <th class="px-4 py-3 rounded-r-lg text-center">Status Akses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentScanLogs as $log)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">
                                    <i class="far fa-clock text-slate-400 mr-1"></i>
                                    {{ $log->waktu_scan ? $log->waktu_scan->format('d/m/Y H:i:s') : '-' }}
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    {{ $log->nama_pemegang }}
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-600">
                                    {{ $log->nomor_kartu }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $log->perusahaan }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-blue-800 bg-blue-100 px-2 py-0.5 rounded text-[11px]">
                                        Area {{ $log->kode_area }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(strtolower($log->status_akses) === 'diterima')
                                        <span class="bg-emerald-100 text-emerald-800 font-extrabold px-2.5 py-1 rounded-full text-[10px] uppercase">
                                            <i class="fas fa-check-circle mr-1"></i> Diterima
                                        </span>
                                    @else
                                        <span class="bg-rose-100 text-rose-800 font-extrabold px-2.5 py-1 rounded-full text-[10px] uppercase" title="{{ $log->alasan }}">
                                            <i class="fas fa-times-circle mr-1"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                <i class="fas fa-video-slash text-slate-300 text-3xl mb-2"></i>
                <p class="text-xs font-semibold text-slate-600">Belum Ada Aktivitas Scan Terdekat</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Hasil pemindaian dari scanner kamera akan otomatis muncul di sini.</p>
            </div>
        @endif
    </div>

    <!-- CHART.JS INTEGRATION -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Data dari Controller
            const kartuAktif     = {{ $totalKartuAktif }};
            const kartuKadaluarsa = {{ $kartuKadaluarsa }};
            const kartuTidakAktif = {{ $kartuTidakAktif }};

            // 1. Donut Chart - Status Kartu PAS
            const ctxDonut = document.getElementById('chartStatusKartu');
            if (ctxDonut) {
                new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Kartu Aktif', 'Kadaluarsa', 'Tidak Aktif'],
                        datasets: [{
                            data: [kartuAktif, kartuKadaluarsa, kartuTidakAktif],
                            backgroundColor: ['#10b981', '#f43f5e', '#94a3b8'],
                            hoverBackgroundColor: ['#059669', '#e11d48', '#64748b'],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    font: { family: 'sans-serif', size: 12, weight: '600' },
                                    padding: 16,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                padding: 12,
                                cornerRadius: 8,
                                bodyFont: { size: 13, weight: 'bold' }
                            }
                        }
                    }
                });
            }

            // 2. Bar Horizontal - Kartu PAS per Instansi
            const ctxBar = document.getElementById('chartInstansi');
            if (ctxBar) {
                const labelsInstansi = @json($kartuPerInstansi->pluck('perusahaan'));
                const dataInstansi   = @json($kartuPerInstansi->pluck('total'));

                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labelsInstansi,
                        datasets: [{
                            label: 'Jumlah Kartu Aktif',
                            data: dataInstansi,
                            backgroundColor: '#3b82f6',
                            hoverBackgroundColor: '#1d4ed8',
                            borderRadius: 8,
                            barThickness: 18,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { stepSize: 1, font: { size: 11 } }
                            },
                            y: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600' }, color: '#334155' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>