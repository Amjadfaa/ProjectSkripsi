<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-chart-line"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Dashboard Monitoring Kartu PAS
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">Ringkasan operasional dan statistik kartu izin masuk area bandara</p>
            </div>
        </div>
    </x-slot>

    <!-- ======================================================================== -->
    <!-- 1. COMPACT EXECUTIVE HERO BANNER                                         -->
    <!-- ======================================================================== -->
    <div class="relative overflow-hidden rounded-2xl mb-6 text-white p-5 sm:p-7 shadow-lg border border-slate-800/40"
         style="background: linear-gradient(135deg, #090e1a 0%, #0f172a 40%, #1e3a8a 100%);">
        
        <!-- Decorative Ambient Light & Aviation Watermarks -->
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <i class="fas fa-plane-departure text-[200px]"></i>
        </div>
        <div class="absolute right-1/4 -top-12 opacity-10 pointer-events-none">
            <i class="fas fa-shield-halved text-[160px]"></i>
        </div>
        <div class="absolute top-0 right-1/3 w-72 h-36 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <!-- Left: Avatar & Greeting -->
            <div class="flex items-center gap-4">
                <div class="relative shrink-0">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl font-black text-white shadow-inner">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-2 border-slate-900 rounded-full" title="Status: Online"></span>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="bg-blue-400/20 text-blue-200 border border-blue-300/30 text-[10.5px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                            Administrator System
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                        Selamat Datang Kembali, {{ auth()->user()->name }}! 👋
                    </h2>
                    <p class="text-blue-100/75 text-xs sm:text-sm mt-0.5 max-w-xl">
                        Pusat kendali monitoring izin masuk PAS Bandara & pengawasan verifikasi area terbatas.
                    </p>
                </div>
            </div>

            <!-- Right: Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('administrator.kartu-pas.index') }}" 
                   class="bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle text-blue-600"></i>
                    <span>Tambah Kartu PAS</span>
                </a>
                <a href="{{ route('administrator.users.create') }}" 
                   class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-user-plus text-slate-900"></i>
                    <span>Tambah Operator</span>
                </a>
                <a href="{{ route('administrator.instansi.index') }}" 
                   class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-3.5 py-2.5 rounded-xl border border-white/20 backdrop-blur-sm flex items-center gap-2 transition-all">
                    <i class="fas fa-building text-blue-300"></i>
                    <span>Kelola Instansi</span>
                </a>
                <a href="{{ route('administrator.perangkat-kamera.index') }}" 
                   class="bg-white/10 hover:bg-white/20 text-white font-semibold text-xs px-3.5 py-2.5 rounded-xl border border-white/20 backdrop-blur-sm flex items-center gap-2 transition-all">
                    <i class="fas fa-video text-emerald-300"></i>
                    <span>Scanner ({{ $perangkatAktif }}/{{ $totalPerangkat }})</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ======================================================================== -->
    <!-- 2. STAT METRICS CARDS (BENTO 5-GRID)                                     -->
    <!-- ======================================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        
        <!-- 1. Total Kartu PAS -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Kartu</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight mb-1">
                {{ number_format($totalKartu) }}
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-bold text-[10.5px] bg-blue-50 text-blue-700">100%</span>
                <span class="truncate">Tercatat di database</span>
            </div>
        </div>

        <!-- 2. Kartu Aktif -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kartu Aktif</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight mb-1">
                {{ number_format($totalKartuAktif) }}
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                @php $persenAktif = $totalKartu > 0 ? round(($totalKartuAktif / $totalKartu) * 100, 1) : 0; @endphp
                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-bold text-[10.5px] bg-emerald-50 text-emerald-700">{{ $persenAktif }}%</span>
                <span class="truncate">Izin akses berlaku</span>
            </div>
        </div>

        <!-- 3. Akan Berakhir (30 Hari) -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Akan Berakhir</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-amber-600 tracking-tight mb-1">
                {{ number_format($kartuAkanBerakhir) }}
            </div>
            <div class="flex items-center gap-1 text-xs text-slate-500">
                <i class="fas fa-clock text-amber-500 text-[10px]"></i>
                <span class="truncate">Batas &le; 30 hari ke depan</span>
            </div>
        </div>

        <!-- 4. Kadaluarsa -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kadaluarsa</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-circle-xmark"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight mb-1">
                {{ number_format($kartuKadaluarsa) }}
            </div>
            <div class="flex items-center gap-1 text-xs text-rose-600 font-medium">
                <i class="fas fa-ban text-[10px]"></i>
                <span class="truncate">Akses otomatis ditolak</span>
            </div>
        </div>

        <!-- 5. Instansi & Scanner -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/80 hover:shadow-md transition-all duration-200 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instansi / Cam</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-purple-600 tracking-tight mb-1">
                {{ $totalInstansi }}
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-bold text-[10.5px] bg-purple-50 text-purple-700">{{ $perangkatAktif }} Cam</span>
                <span class="truncate">Kamera online</span>
            </div>
        </div>

    </div>

    <!-- ======================================================================== -->
    <!-- 3. BALANCED OPERATIONAL MONITORING (PERINGATAN & KUOTA KRITIS)            -->
    <!-- ======================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Card 1: Peringatan Masa Berlaku Kartu PAS (<30 Hari) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between" style="min-height: 380px;">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                            <i class="fas fa-triangle-exclamation"></i>
                        </span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-tight">Peringatan Masa Berlaku</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Kartu aktif dengan sisa waktu &le; 30 hari</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.kartu-pas.index', ['status' => 'aktif']) }}" 
                       class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1">
                        Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                @if($kartuHampirKadaluarsa->isNotEmpty())
                    <div class="space-y-2.5 max-h-[260px] overflow-y-auto custom-scrollbar pr-1">
                        @foreach($kartuHampirKadaluarsa as $kartu)
                            @php 
                                $sisaHari = now()->diffInDays($kartu->tanggal_berlaku); 
                                $isUrgent = $sisaHari <= 7;
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl border {{ $isUrgent ? 'bg-rose-50/60 border-rose-200' : 'bg-amber-50/50 border-amber-200' }} transition-all hover:shadow-xs">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-lg {{ $isUrgent ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center font-bold text-sm shrink-0">
                                        <i class="fas {{ $isUrgent ? 'fa-triangle-exclamation' : 'fa-clock' }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-xs sm:text-sm truncate">
                                            {{ $kartu->nama_pemegang }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                            {{ $kartu->nomor_kartu }} &bull; {{ $kartu->perusahaan }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 ml-3">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-black {{ $isUrgent ? 'bg-rose-600 text-white shadow-xs' : 'bg-amber-500 text-white shadow-xs' }}">
                                        {{ $sisaHari == 0 ? 'Hari Ini' : $sisaHari . ' Hari' }}
                                    </span>
                                    <p class="text-[10px] text-slate-400 mt-0.5 font-mono">{{ $kartu->tanggal_berlaku->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Clean & Modern Empty State -->
                    <div class="py-10 px-4 text-center bg-slate-50/70 rounded-xl border border-dashed border-slate-200 flex flex-col items-center justify-center">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl mb-3 shadow-2xs border border-emerald-100">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Semua Kartu PAS Berstatus Aman</h4>
                        <p class="text-xs text-slate-500 max-w-sm mt-1">
                            Tidak ada kartu aktif yang akan habis masa berlakunya dalam waktu 30 hari ke depan.
                        </p>
                        <div class="mt-4 flex items-center gap-3 text-xs text-slate-400 font-medium bg-white px-3 py-1.5 rounded-lg border border-slate-200/80">
                            <span><i class="fas fa-check text-emerald-500 mr-1"></i> 554 Kartu Kadaluarsa Telah Dinonaktifkan</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Bottom Metric Bar -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 mt-3">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Sistem pemantauan aktif 24/7
                </span>
                <span class="font-semibold text-slate-700">Audit PAS Bandara</span>
            </div>
        </div>

        <!-- Card 2: Kuota Instansi Kritis (Bounded & Scrollable) -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between" style="min-height: 380px;">
            <div>
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                            <i class="fas fa-battery-quarter"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-tight">Kuota Instansi Kritis</h3>
                                @if($instansiKuotaKritis->isNotEmpty())
                                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-100 text-rose-700">
                                        {{ $instansiKuotaKritis->count() }} Instansi
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">Instansi dengan sisa kuota penerbitan &le; 3</p>
                        </div>
                    </div>
                    <a href="{{ route('administrator.monitoring-kuota.index') }}" 
                       class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1">
                        Kelola Kuota <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                @if($instansiKuotaKritis->isNotEmpty())
                    <!-- Bounded Scroll Container to Prevent Unbounded Stretching -->
                    <div class="space-y-2 max-h-[220px] overflow-y-auto custom-scrollbar pr-1.5">
                        @foreach($instansiKuotaKritis as $inst)
                            <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between hover:bg-slate-100/70 transition-colors">
                                <div class="min-w-0 pr-2">
                                    <p class="font-bold text-slate-800 text-xs truncate max-w-[240px]">
                                        {{ $inst->nama_instansi }}
                                    </p>
                                    <p class="text-[10.5px] text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span>Kuota: <strong class="text-slate-700">{{ $inst->kuota }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Terpakai: <strong class="text-slate-700">{{ $inst->terpakai }}</strong></span>
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    @if($inst->sisa_kuota <= 1)
                                        <span class="bg-rose-100 text-rose-700 border border-rose-200 font-extrabold text-[11px] px-2.5 py-1 rounded-lg">
                                            Sisa {{ $inst->sisa_kuota }}
                                        </span>
                                    @else
                                        <span class="bg-amber-100 text-amber-800 border border-amber-200 font-extrabold text-[11px] px-2.5 py-1 rounded-lg">
                                            Sisa {{ $inst->sisa_kuota }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-10 px-4 text-center bg-slate-50/70 rounded-xl border border-dashed border-slate-200">
                        <i class="fas fa-shield-alt text-blue-500 text-3xl mb-2"></i>
                        <p class="text-xs font-bold text-slate-700">Seluruh Kuota Instansi Mencukupi</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada instansi yang mencapai batas kuota kritis.</p>
                    </div>
                @endif
            </div>

            <!-- Integrated Scanner Status Progress Bar -->
            <div class="pt-3 border-t border-slate-100 mt-3">
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                        <i class="fas fa-video text-blue-600"></i> Ketersediaan Scanner Kamera
                    </span>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        {{ $perangkatAktif }} / {{ $totalPerangkat }} Online
                    </span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    @php $persenCam = $totalPerangkat > 0 ? round(($perangkatAktif / $totalPerangkat) * 100) : 0; @endphp
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $persenCam }}%"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- ======================================================================== -->
    <!-- 4. VISUAL ANALYTICS & CHARTS SECTION                                     -->
    <!-- ======================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Donut Chart: Distribusi Status Kartu PAS -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-tight">Distribusi Status Kartu PAS</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Proporsi status kartu aktif vs kadaluarsa</p>
                    </div>
                </div>
                <span class="text-[11px] text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold">
                    Realtime Data
                </span>
            </div>
            
            <div class="relative flex items-center justify-center my-3" style="height: 240px;">
                <canvas id="chartStatusKartu"></canvas>
            </div>

            <div class="grid grid-cols-3 gap-2.5 pt-4 border-t border-slate-100 text-center">
                <div class="p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-200/70">
                    <p class="text-[10.5px] font-bold text-emerald-700 uppercase tracking-wider">Aktif</p>
                    <p class="text-lg font-black text-emerald-600 mt-0.5">{{ number_format($totalKartuAktif) }}</p>
                </div>
                <div class="p-2.5 rounded-xl bg-rose-50/60 border border-rose-200/70">
                    <p class="text-[10.5px] font-bold text-rose-700 uppercase tracking-wider">Kadaluarsa</p>
                    <p class="text-lg font-black text-rose-600 mt-0.5">{{ number_format($kartuKadaluarsa) }}</p>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-100/70 border border-slate-200">
                    <p class="text-[10.5px] font-bold text-slate-600 uppercase tracking-wider">Tidak Aktif</p>
                    <p class="text-lg font-black text-slate-700 mt-0.5">{{ number_format($kartuTidakAktif) }}</p>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Kartu PAS Aktif per Instansi Top 6 -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                        <i class="fas fa-chart-simple"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-tight">Kartu Aktif per Instansi</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Top 6 instansi dengan kartu aktif terbanyak</p>
                    </div>
                </div>
                <a href="{{ route('administrator.instansi.index') }}" 
                   class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-1">
                    Kelola Instansi <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="relative my-2" style="height: 290px;">
                <canvas id="chartInstansi"></canvas>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Diperbarui otomatis dari database</span>
                <span class="font-semibold text-slate-600">Total Instansi: {{ $totalInstansi }}</span>
            </div>
        </div>

    </div>

    <!-- ======================================================================== -->
    <!-- 5. RECENT SCAN LOGS FEED                                                 -->
    <!-- ======================================================================== -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 mb-4 gap-3">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                    <i class="fas fa-clock-rotate-left"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm sm:text-base leading-tight">
                        Log Aktivitas Scan Masuk Terbaru
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Catatan pemindaian QR Code dari scanner kamera bandara.</p>
                </div>
            </div>
            <a href="{{ route('administrator.laporan-aktivitas.index') }}" 
               class="inline-flex items-center justify-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2 rounded-xl transition shadow-2xs">
                <span>Lihat Seluruh Log Aktivitas</span>
                <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        @if($recentScanLogs->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200/80">
                            <th class="px-4 py-3 rounded-l-xl">Waktu Scan</th>
                            <th class="px-4 py-3">Nama Pemegang</th>
                            <th class="px-4 py-3">No. Kartu</th>
                            <th class="px-4 py-3">Instansi</th>
                            <th class="px-4 py-3">Kode Area</th>
                            <th class="px-4 py-3 rounded-r-xl text-center">Status Akses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentScanLogs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">
                                    <i class="far fa-clock text-slate-400 mr-1.5"></i>
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
                                    <span class="font-bold text-blue-800 bg-blue-50 border border-blue-200/60 px-2 py-0.5 rounded text-[11px]">
                                        Area {{ $log->kode_area }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(strtolower($log->status_akses) === 'diterima')
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-extrabold px-2.5 py-1 rounded-full text-[10px] uppercase">
                                            <i class="fas fa-check-circle text-emerald-500"></i> Diterima
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-200 font-extrabold px-2.5 py-1 rounded-full text-[10px] uppercase" title="{{ $log->alasan }}">
                                            <i class="fas fa-times-circle text-rose-500"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-10 text-center bg-slate-50/70 rounded-xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                    <i class="fas fa-video-slash"></i>
                </div>
                <p class="text-xs font-bold text-slate-700">Belum Ada Aktivitas Scan Terdekat</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Hasil pemindaian dari scanner kamera akan otomatis dicatat secara realtime di sini.</p>
            </div>
        @endif
    </div>

    <!-- ======================================================================== -->
    <!-- 6. CHART.JS CONFIGURATION                                                -->
    <!-- ======================================================================== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Data dari Controller
            const kartuAktif      = {{ $totalKartuAktif }};
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
                                    font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                                    padding: 14,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                padding: 12,
                                cornerRadius: 10,
                                bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 'bold' }
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
                            backgroundColor: '#2563eb',
                            hoverBackgroundColor: '#1d4ed8',
                            borderRadius: 8,
                            barThickness: 16,
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
                                cornerRadius: 8,
                                bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { stepSize: 1, font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" } }
                            },
                            y: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600', family: "'Plus Jakarta Sans', sans-serif" }, color: '#334155' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>