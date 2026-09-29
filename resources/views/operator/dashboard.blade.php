<x-app-layout>
    <x-slot name="header">
        Dashboard Operator
    </x-slot>

    <div class="space-y-6">

        <!-- ======================================================================== -->
        <!-- 1. EXECUTIVE HERO BANNER (Warna Seragam: Deep Navy Aviation Gradient)    -->
        <!-- ======================================================================== -->
        <div class="relative overflow-hidden rounded-3xl text-white p-6 sm:p-8 shadow-xl border border-slate-800/40"
             style="background: linear-gradient(135deg, #090e1a 0%, #0f172a 40%, #1e3a8a 100%);">
            
            <!-- Background Decorative HUD & Aviation Watermarks -->
            <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
                <i class="fas fa-video text-[200px] text-white"></i>
            </div>
            <div class="absolute right-1/4 -top-10 opacity-10 pointer-events-none">
                <i class="fas fa-shield-alt text-[160px] text-white"></i>
            </div>
            <div class="absolute top-0 right-1/3 w-80 h-40 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

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
                            <span class="bg-amber-400/20 text-amber-300 border border-amber-400/30 text-[10.5px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-shield-alt text-amber-400"></i> Panel Kontrol Operator Keamanan
                            </span>
                            @if($connectedDevice)
                                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10.5px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> {{ $connectedDevice->nama_kamera }} (Area {{ $connectedDevice->kode_area }})
                                </span>
                            @endif
                        </div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-white">
                            Selamat Datang, <span class="text-amber-400">{{ auth()->user()->name }}</span>!
                        </h1>
                        <p class="text-blue-100/80 text-xs sm:text-sm mt-1 max-w-xl leading-relaxed">
                            Monitor akses masuk/keluar area bandara secara real-time, kelola perangkat scanner, dan pastikan kepatuhan kartu izin PAS Bandara.
                        </p>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center gap-3 flex-wrap shrink-0">
                    @if($connectedDevice)
                        <a href="{{ route('operator.kamera.scanner') }}"
                           class="bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black px-5 py-3 rounded-2xl shadow-lg shadow-amber-500/20 transition-all hover:scale-[1.02] text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-qrcode text-base"></i>
                            <span>Buka Live Scanner</span>
                        </a>
                        <a href="{{ route('operator.kamera.logs') }}"
                           class="bg-white/10 hover:bg-white/20 text-white font-bold px-4 py-3 rounded-2xl border border-white/15 transition-all text-xs flex items-center gap-2">
                            <i class="fas fa-history text-slate-300"></i>
                            <span>Log Pemindaian</span>
                        </a>
                    @else
                        <a href="{{ route('operator.kamera.index') }}"
                           class="bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black px-5 py-3 rounded-2xl shadow-lg shadow-amber-500/20 transition-all hover:scale-[1.02] text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-plug text-base"></i>
                            <span>Hubungkan Kamera Pos</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- ======================================================================== -->
        <!-- 2. STATUS SESI POS KAMERA TERHUBUNG                                      -->
        <!-- ======================================================================== -->
        @if($connectedDevice)
            <div class="bg-white rounded-3xl p-5 border border-emerald-200/90 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xl shrink-0 shadow-2xs">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-black uppercase tracking-wider text-emerald-600 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                Kamera Terhubung
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-slate-900 text-white">
                                AREA {{ $connectedDevice->kode_area }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-200">
                                {{ str_replace('_', ' ', $connectedDevice->tipe_scan) }}
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight mt-1">
                            {{ $connectedDevice->nama_kamera }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2 flex-wrap">
                            <span><i class="fas fa-map-marker-alt text-amber-500 mr-1"></i> Lokasi: <strong>{{ optional($connectedDevice->areaAkses)->nama_area ?? 'Area Bandara' }}</strong></span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Kode Akses: <code class="font-mono font-bold text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">{{ $connectedDevice->kode_akses }}</code></span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('operator.kamera.scanner') }}"
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs px-4 py-2.5 rounded-xl transition shadow-xs">
                        <i class="fas fa-qrcode"></i>
                        <span>Live Scanner</span>
                    </a>
                    <form action="{{ route('operator.kamera.disconnect') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan koneksi kamera ini?');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 font-bold text-xs px-3.5 py-2.5 rounded-xl transition border border-slate-200 hover:border-rose-200">
                            <i class="fas fa-unlink"></i>
                            <span>Putuskan</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-amber-50 to-orange-50/70 rounded-3xl p-5 border border-amber-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-amber-100 text-amber-700 border border-amber-300 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <span class="text-xs font-black uppercase tracking-wider text-amber-700">Perangkat Kamera Belum Terhubung</span>
                        <h3 class="text-base font-black text-slate-900 mt-0.5">Sesi scanner saat ini belum memiliki kamera yang dipilih</h3>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Hubungkan perangkat kamera menggunakan pos yang ditugaskan di bawah untuk memulai pemindaian QR.
                        </p>
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('operator.kamera.index') }}"
                       class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#284c7b] text-white font-extrabold text-xs px-4 py-2.5 rounded-xl transition shadow-sm">
                        <i class="fas fa-key text-amber-400"></i>
                        <span>Pilih Kamera Pos</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- ======================================================================== -->
        <!-- 3. STATISTIK KPI AKTIVITAS HARI INI                                      -->
        <!-- ======================================================================== -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Total Scan Hari Ini -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-slate-300 transition">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1e3a5f] border border-blue-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Scan Hari Ini</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalScanHariIni) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pemindaian terproses</p>
                </div>
            </div>

            <!-- Scan Berhasil / Valid -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-emerald-200 transition">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 block">Akses Diizinkan</span>
                    <h3 class="text-xl sm:text-2xl font-black text-emerald-700 tracking-tight">{{ number_format($scanBerhasilHariIni) }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-0.5">
                        @if($totalScanHariIni > 0)
                            {{ round(($scanBerhasilHariIni / $totalScanHariIni) * 100, 1) }}% rasio valid
                        @else
                            Izin terverifikasi
                        @endif
                    </p>
                </div>
            </div>

            <!-- Scan Ditolak / Kadaluarsa -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-rose-200 transition">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-700 block">Akses Ditolak</span>
                    <h3 class="text-xl sm:text-2xl font-black text-rose-700 tracking-tight">{{ number_format($scanDitolakHariIni) }}</h3>
                    <p class="text-[11px] text-rose-600 font-medium mt-0.5">Kadaluarsa / Area ditolak</p>
                </div>
            </div>

            <!-- Perangkat Kamera Aktif di Sistem -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-amber-200 transition">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700 block">Kamera Aktif</span>
                    <h3 class="text-xl sm:text-2xl font-black text-amber-700 tracking-tight">{{ number_format($totalKameraAktif) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $assignedCameras->count() }} pos diotorisasi</p>
                </div>
            </div>

        </div>

        <!-- ======================================================================== -->
        <!-- 4. DAFTAR TITIK POS KAMERA DITUGASKAN (Rapi, Luas, Terstruktur)          -->
        <!-- ======================================================================== -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1e3a5f] border border-blue-100 flex items-center justify-center text-base shrink-0">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight">Daftar Titik Pos Kamera Ditugaskan</h3>
                        <p class="text-xs text-slate-400">Titik perangkat kamera yang telah diotorisasi Administrator untuk akun Anda</p>
                    </div>
                </div>
                <span class="text-xs font-black px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 self-start sm:self-auto">
                    {{ $assignedCameras->count() }} Kamera Diizinkan
                </span>
            </div>

            <!-- Grid 1-2-3 Kolom (Spacious & Rapi, Tidak Terhimpit) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-1">
                @forelse($assignedCameras as $cam)
                    @php 
                        $isCurrent = $connectedDevice && $connectedDevice->id === $cam->id; 
                    @endphp
                    <div class="rounded-2xl p-5 transition-all flex flex-col justify-between gap-4 {{ $isCurrent ? 'border-2 border-emerald-500 bg-gradient-to-br from-emerald-50/70 to-teal-50/30 shadow-md ring-4 ring-emerald-500/10' : 'border border-slate-200 bg-white hover:border-slate-300 hover:shadow-sm' }}">
                        
                        <!-- Top: Icon, Badges & Status -->
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl {{ $isCurrent ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center text-sm shrink-0">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-black text-sm text-slate-900 leading-snug truncate" title="{{ $cam->nama_kamera }}">
                                            {{ $cam->nama_kamera }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 truncate flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-map-marker-alt text-amber-500 text-[10px]"></i>
                                            <span>{{ optional($cam->areaAkses)->nama_area ?? 'Area Bandara' }}</span>
                                        </p>
                                    </div>
                                </div>

                                @if($isCurrent)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-600 text-white shadow-xs shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> AKTIF
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> SIAGA
                                    </span>
                                @endif
                            </div>

                            <!-- Area & Scan Type Badges -->
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <span class="bg-slate-900 text-white text-[10px] font-black px-2.5 py-0.5 rounded-md">
                                    AREA {{ $cam->kode_area }}
                                </span>
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-900 border border-amber-200">
                                    {{ str_replace('_', ' ', $cam->tipe_scan) }}
                                </span>
                            </div>

                            <!-- Kode Akses Box -->
                            <div class="mt-3 text-xs text-slate-600 flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                                <span class="text-[11px] text-slate-400 font-bold">Kode Akses:</span>
                                <code class="font-mono font-black text-[#1e3a5f] text-xs bg-white px-2 py-0.5 rounded border border-slate-200 tracking-wider">
                                    {{ $cam->kode_akses }}
                                </code>
                            </div>
                        </div>

                        <!-- Bottom: Action Button -->
                        <div class="pt-2 border-t border-slate-100">
                            @if($isCurrent)
                                <a href="{{ route('operator.kamera.scanner') }}"
                                   class="w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black py-2.5 px-4 rounded-xl transition shadow-xs flex items-center justify-center gap-2">
                                    <i class="fas fa-qrcode text-xs"></i>
                                    <span>Buka Live Scanner &rarr;</span>
                                </a>
                            @else
                                <form action="{{ route('operator.kamera.connect') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="kode_akses" value="{{ $cam->kode_akses }}">
                                    <button type="submit"
                                            class="w-full text-center bg-[#1e3a5f] hover:bg-[#284c7b] text-white text-xs font-black py-2.5 px-4 rounded-xl transition shadow-xs flex items-center justify-center gap-2">
                                        <i class="fas fa-plug text-amber-400 text-xs"></i>
                                        <span>Hubungkan Kamera</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2 shadow-inner">
                            <i class="fas fa-video-slash"></i>
                        </div>
                        <p class="text-xs font-extrabold text-slate-700">Belum Ada Kamera Ditugaskan</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Silakan hubungi Administrator untuk memperoleh penugasan izin akses kamera.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ======================================================================== -->
        <!-- 5. RIWAYAT PEMINDAIAN TERKINI                                            -->
        <!-- ======================================================================== -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-extrabold text-slate-800 text-sm">Riwayat Pemindaian Terkini</h2>
                            @if($connectedDevice)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-slate-900 text-white">
                                    {{ $connectedDevice->nama_kamera }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-400">Aktivitas kartu PAS yang baru saja diverifikasi</p>
                    </div>
                </div>
                <a href="{{ route('operator.kamera.logs') }}"
                   class="text-xs font-black text-[#1e3a5f] hover:text-blue-800 hover:underline flex items-center gap-1.5 self-start sm:self-auto">
                    <span>Lihat Seluruh Log</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Compact Table (No Horizontal Scroll) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-black uppercase text-slate-500 border-b border-slate-100 tracking-wider">
                        <tr>
                            <th class="px-3.5 py-3 w-[90px]">Waktu</th>
                            <th class="px-3.5 py-3 w-[135px]">No. Kartu</th>
                            <th class="px-3.5 py-3 min-w-[180px]">Pemegang & Instansi</th>
                            <th class="px-3.5 py-3 w-[150px]">Pos & Area</th>
                            <th class="px-3.5 py-3 text-center w-[80px]">Arah</th>
                            <th class="px-3.5 py-3 text-center w-[95px]">Status</th>
                            <th class="px-3.5 py-3 min-w-[140px]">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentLogs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                
                                <!-- 1. Waktu Scan -->
                                <td class="px-3.5 py-3 font-mono text-xs whitespace-nowrap">
                                    <div class="font-black text-slate-900 text-xs tracking-tight">
                                        {{ \Carbon\Carbon::parse($log->waktu_scan)->format('H:i:s') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-sans mt-0.5 font-medium">
                                        {{ \Carbon\Carbon::parse($log->waktu_scan)->format('d/m/Y') }}
                                    </div>
                                </td>

                                <!-- 2. Nomor Kartu PAS -->
                                <td class="px-3.5 py-3 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded-lg bg-slate-100 border border-slate-200/90 font-mono text-xs font-black text-slate-900 shadow-2xs">
                                        {{ $log->nomor_kartu ?? '-' }}
                                    </span>
                                </td>

                                <!-- 3. Pemegang & Instansi -->
                                <td class="px-3.5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-slate-200 to-slate-100 border border-slate-300 text-slate-700 flex items-center justify-center font-bold text-[11px] shrink-0 overflow-hidden shadow-2xs">
                                            @if($log->kartuPas && $log->kartuPas->foto)
                                                <img src="{{ asset('storage/' . $log->kartuPas->foto) }}" alt="Foto" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($log->nama_pemegang ?? 'P', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0 max-w-[220px]">
                                            <div class="font-extrabold text-slate-900 group-hover:text-[#1e3a5f] transition-colors leading-tight truncate text-xs">
                                                {{ $log->nama_pemegang ?? '-' }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 truncate mt-0.5 leading-tight font-medium" title="{{ $log->perusahaan }}">
                                                {{ $log->perusahaan ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 4. Pos Perangkat & Area -->
                                <td class="px-3.5 py-3 text-xs">
                                    <div class="font-extrabold text-slate-800 truncate max-w-[140px] leading-tight" title="{{ $log->nama_kamera ?? optional($log->cameraDevice)->nama_kamera ?? '-' }}">
                                        {{ $log->nama_kamera ?? optional($log->cameraDevice)->nama_kamera ?? '-' }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black uppercase bg-slate-900 text-white shadow-2xs">
                                            Area {{ $log->kode_area ?? optional($log->cameraDevice)->kode_area ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 5. Arah Akses -->
                                <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                    @if(($log->tipe_aktivitas ?? 'masuk') === 'masuk')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fas fa-sign-in-alt text-[9px]"></i> MASUK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                            <i class="fas fa-sign-out-alt text-[9px]"></i> KELUAR
                                        </span>
                                    @endif
                                </td>

                                <!-- 6. Status Akses -->
                                <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                    @if($log->status_akses === 'diterima')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> VALID
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                            <i class="fas fa-times-circle text-[9px]"></i> DITOLAK
                                        </span>
                                    @endif
                                </td>

                                <!-- 7. Keterangan / Alasan -->
                                <td class="px-3.5 py-3 text-xs text-slate-600">
                                    <div class="truncate max-w-[180px] xl:max-w-xs leading-tight" title="{{ $log->alasan ?? $log->catatan }}">
                                        @if($log->status_akses === 'diterima')
                                            <span class="text-emerald-700 font-semibold">{{ $log->alasan ?? 'Akses diizinkan' }}</span>
                                        @else
                                            <span class="text-rose-700 font-bold">{{ $log->alasan ?? 'Akses ditolak' }}</span>
                                        @endif
                                    </div>
                                    @if($log->catatan)
                                        <div class="text-[10px] text-slate-400 italic truncate max-w-[180px] mt-0.5" title="{{ $log->catatan }}">
                                            <i class="fas fa-sticky-note text-amber-500 mr-0.5"></i> {{ $log->catatan }}
                                        </div>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2 shadow-inner">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h4 class="text-xs font-extrabold text-slate-700">Belum Ada Pemindaian Tercatat Hari Ini</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Buka live scanner untuk mulai memindai QR code kartu PAS.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</x-app-layout>
