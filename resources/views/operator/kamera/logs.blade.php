<x-app-layout>
    <x-slot name="header">
        Log Pemindaian Scanner
    </x-slot>

    <div class="space-y-6">

        <!-- Top Device / Session Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-white border border-slate-200/90 shadow-sm p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#1e3a5f] to-[#0f2744] text-white flex items-center justify-center text-2xl shrink-0 shadow-md">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Riwayat Pemindaian Kartu PAS</h2>
                        @if($connectedDevice)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black tracking-wider bg-slate-900 text-white uppercase shadow-xs">
                                AREA {{ $connectedDevice->kode_area }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-200">
                                {{ str_replace('_', ' ', $connectedDevice->tipe_scan) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> ONLINE
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                Seluruh Kamera
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap">
                        @if($connectedDevice)
                            <span>Menampilkan aktivitas pemindaian untuk kamera: <strong>{{ $connectedDevice->nama_kamera }}</strong> ({{ $connectedDevice->kode_area }})</span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Lokasi: <strong>{{ optional($connectedDevice->areaAkses)->nama_area ?? 'Area Bandara' }}</strong></span>
                        @else
                            <span>Menampilkan seluruh arsip log pemindaian di sistem dari berbagai pos gate bandara</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                @if($connectedDevice)
                    <a href="{{ route('operator.kamera.scanner') }}"
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs px-4 py-2.5 rounded-2xl shadow-sm transition-all hover:scale-[1.02]">
                        <i class="fas fa-qrcode text-sm"></i>
                        <span>Buka Live Scanner</span>
                    </a>
                    <a href="{{ route('operator.kamera.index') }}"
                       class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-3.5 py-2.5 rounded-2xl transition">
                        <i class="fas fa-exchange-alt text-blue-500"></i>
                        <span>Ganti Pos</span>
                    </a>
                @else
                    <a href="{{ route('operator.kamera.index') }}"
                       class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#284c7b] text-white font-bold text-xs px-4 py-2.5 rounded-2xl shadow-sm transition">
                        <i class="fas fa-video text-sm"></i>
                        <span>Pilih Kamera Pos</span>
                    </a>
                @endif
                <button type="button" onclick="window.location.reload()"
                        class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition"
                        title="Segarkan Log">
                    <i class="fas fa-sync-alt text-xs"></i>
                </button>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Pemindaian -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-slate-300 transition">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1e3a5f] border border-blue-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Pemindaian</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ number_format($stats['total'] ?? 0) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Seluruh riwayat tersimpan</p>
                </div>
            </div>

            <!-- Akses Valid / Diterima -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-emerald-200 transition">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 block">Izin Diterima</span>
                    <h3 class="text-xl sm:text-2xl font-black text-emerald-700 tracking-tight">{{ number_format($stats['valid'] ?? 0) }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-0.5">
                        @if(($stats['total'] ?? 0) > 0)
                            {{ round((($stats['valid'] ?? 0) / $stats['total']) * 100, 1) }}% dari total
                        @else
                            Akses valid
                        @endif
                    </p>
                </div>
            </div>

            <!-- Akses Ditolak -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-rose-200 transition">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-700 block">Izin Ditolak</span>
                    <h3 class="text-xl sm:text-2xl font-black text-rose-700 tracking-tight">{{ number_format($stats['ditolak'] ?? 0) }}</h3>
                    <p class="text-[11px] text-rose-600 font-medium mt-0.5">Kadaluarsa / Salah Area</p>
                </div>
            </div>

            <!-- Hari Ini -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center gap-4 hover:border-amber-200 transition">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700 block">Pemindaian Hari Ini</span>
                    <h3 class="text-xl sm:text-2xl font-black text-amber-700 tracking-tight">{{ number_format($stats['hari_ini'] ?? 0) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ \Carbon\Carbon::today()->translatedFormat('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Console Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-5 sm:p-6 space-y-4">
            
            <!-- Quick Filter Chips -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mr-1 shrink-0">
                    <i class="fas fa-filter text-amber-500 mr-1"></i> Filter Cepat:
                </span>
                <a href="{{ route('operator.kamera.logs') }}"
                   class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ !request()->hasAny(['status', 'tipe_aktivitas', 'tanggal']) ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </a>
                <a href="{{ route('operator.kamera.logs', array_merge(request()->except('status', 'page'), ['status' => 'valid'])) }}"
                   class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ request('status') === 'valid' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/60' }}">
                    <i class="fas fa-check-circle text-[10px] mr-1"></i> Hanya Valid
                </a>
                <a href="{{ route('operator.kamera.logs', array_merge(request()->except('status', 'page'), ['status' => 'ditolak'])) }}"
                   class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ request('status') === 'ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60' }}">
                    <i class="fas fa-times-circle text-[10px] mr-1"></i> Hanya Ditolak
                </a>
                <a href="{{ route('operator.kamera.logs', array_merge(request()->except('tipe_aktivitas', 'page'), ['tipe_aktivitas' => 'masuk'])) }}"
                   class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ request('tipe_aktivitas') === 'masuk' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/60' }}">
                    <i class="fas fa-sign-in-alt text-[10px] mr-1"></i> Mode Masuk
                </a>
                <a href="{{ route('operator.kamera.logs', array_merge(request()->except('tipe_aktivitas', 'page'), ['tipe_aktivitas' => 'keluar'])) }}"
                   class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ request('tipe_aktivitas') === 'keluar' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60' }}">
                    <i class="fas fa-sign-out-alt text-[10px] mr-1"></i> Mode Keluar
                </a>
                <a href="{{ route('operator.kamera.logs', array_merge(request()->except('tanggal', 'page'), ['tanggal' => \Carbon\Carbon::today()->format('Y-m-d')])) }}"
                   class="px-3 py-1.5 rounded-xl font-bold transition shrink-0 {{ request('tanggal') === \Carbon\Carbon::today()->format('Y-m-d') ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <i class="fas fa-calendar-day text-[10px] mr-1"></i> Hari Ini
                </a>
            </div>

            <!-- Detailed Search Form -->
            <form method="GET" action="{{ route('operator.kamera.logs') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-3 border-t border-slate-100 items-end">
                
                <!-- Keyword Search (4 cols) -->
                <div class="lg:col-span-4">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Cari Pemegang / Kartu / PT
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Nama / No. Kartu / Perusahaan..."
                               class="w-full pl-9 pr-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-2xl bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-[#1e3a5f]/20 focus:border-[#1e3a5f] transition-all">
                    </div>
                </div>

                <!-- Tanggal Pemindaian (3 cols) -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tanggal Pemindaian
                    </label>
                    <div class="relative">
                        <input type="date"
                               name="tanggal"
                               value="{{ request('tanggal') }}"
                               class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-2xl bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-[#1e3a5f]/20 focus:border-[#1e3a5f] transition-all">
                    </div>
                </div>

                <!-- Arah Akses (2 cols) -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Arah Akses
                    </label>
                    <select name="tipe_aktivitas"
                            class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-2xl bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-[#1e3a5f]/20 focus:border-[#1e3a5f] transition-all">
                        <option value="">Semua Arah</option>
                        <option value="masuk" {{ request('tipe_aktivitas') === 'masuk' ? 'selected' : '' }}>Masuk</option>
                        <option value="keluar" {{ request('tipe_aktivitas') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>

                <!-- Status Izin (2 cols) -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                        Status Izin
                    </label>
                    <select name="status"
                            class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-2xl bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-[#1e3a5f]/20 focus:border-[#1e3a5f] transition-all">
                        <option value="">Semua Status</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid (Diterima)</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Action Button (1 col) -->
                <div class="lg:col-span-1 flex items-center gap-1.5">
                    <button type="submit"
                            class="w-full bg-[#1e3a5f] hover:bg-[#284c7b] text-white text-xs font-black py-2.5 px-3 rounded-2xl transition-all shadow-sm hover:shadow flex items-center justify-center gap-1.5"
                            title="Terapkan Filter">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->hasAny(['search', 'tanggal', 'status', 'tipe_aktivitas']))
                        <a href="{{ route('operator.kamera.logs') }}"
                           class="p-2.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-2xl transition flex items-center justify-center"
                           title="Reset Semua Filter">
                            <i class="fas fa-undo text-xs"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- Tabel Log Pemindaian Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Daftar Rekaman Pemindaian</h3>
                        <p class="text-[11px] text-slate-400">
                            Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ number_format($logs->total()) }} log pemindaian
                        </p>
                    </div>
                </div>

                @if(request()->hasAny(['search', 'tanggal', 'status', 'tipe_aktivitas']))
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                        <i class="fas fa-info-circle text-amber-600"></i>
                        <span>Filter Aktif Diterapkan</span>
                        <a href="{{ route('operator.kamera.logs') }}" class="ml-1 text-rose-600 hover:underline font-extrabold">&times; Hapus</a>
                    </div>
                @endif
            </div>

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
                            <th class="px-3 py-3 text-center w-[50px]">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
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

                                <!-- 3. Pemegang & Instansi (Combined for optimal width) -->
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

                                <!-- 8. Tombol Aksi Detail Modal -->
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <button type="button"
                                            onclick="openDetailModal({{ json_encode([
                                                'id'             => $log->id,
                                                'nomor_kartu'    => $log->nomor_kartu ?? '-',
                                                'nama_pemegang'  => $log->nama_pemegang ?? '-',
                                                'perusahaan'     => $log->perusahaan ?? '-',
                                                'jabatan'        => optional($log->kartuPas)->jabatan ?? '-',
                                                'foto_url'       => (optional($log->kartuPas)->foto) ? asset('storage/' . $log->kartuPas->foto) : null,
                                                'area_akses'     => optional($log->kartuPas)->area_akses ?? $log->kode_area,
                                                'nama_kamera'    => $log->nama_kamera ?? optional($log->cameraDevice)->nama_kamera ?? '-',
                                                'kode_area'      => $log->kode_area ?? '-',
                                                'tipe_aktivitas' => strtoupper($log->tipe_aktivitas ?? 'MASUK'),
                                                'status_akses'   => $log->status_akses,
                                                'alasan'         => $log->alasan ?? '-',
                                                'catatan'        => $log->catatan ?? '',
                                                'waktu_scan'     => \Carbon\Carbon::parse($log->waktu_scan)->translatedFormat('l, d F Y - H:i:s') . ' WIT',
                                            ]) }})"
                                            class="p-1.5 rounded-xl bg-slate-100 hover:bg-[#1e3a5f] hover:text-white text-slate-600 transition shadow-2xs"
                                            title="Lihat Detail Verifikasi">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3 shadow-inner">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-slate-700">Tidak Ada Log Pemindaian Ditemukan</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Tidak ada catatan scan yang cocok dengan filter saat ini. Coba sesuaikan kata kunci pencarian, status, atau rentang tanggal.
                                    </p>
                                    @if(request()->hasAny(['search', 'tanggal', 'status', 'tipe_aktivitas']))
                                        <a href="{{ route('operator.kamera.logs') }}"
                                           class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-sm hover:bg-slate-800 transition">
                                            <i class="fas fa-undo"></i> Reset Filter
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif

        </div>

    </div>

    <!-- Modal Detail Log Pemindaian -->
    <div id="detailModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden animate-scale-up">
            
            <!-- Modal Header -->
            <div id="modalHeader" class="p-5 flex items-center justify-between text-white bg-gradient-to-r from-[#1e3a5f] to-[#0f2744]">
                <div class="flex items-center gap-3">
                    <div id="modalStatusIconWrap" class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg">
                        <i id="modalStatusIcon" class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="text-base font-black uppercase tracking-tight">Rincian Log Pemindaian</h3>
                        <p id="modalSubtitle" class="text-xs text-white/80">Detail verifikasi akses scanner</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 text-xs">
                
                <!-- Identitas Pemegang -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 flex items-center gap-4">
                    <div id="modalFotoWrapper" class="w-16 h-16 rounded-2xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center text-2xl text-slate-400 shrink-0 shadow-sm">
                        <img id="modalFotoImg" src="" alt="Foto" class="w-full h-full object-cover hidden">
                        <i id="modalFotoPlaceholder" class="fas fa-user"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span id="modalNoKartu" class="font-mono text-xs font-black text-slate-900 bg-white px-2 py-0.5 rounded-md border border-slate-200 inline-block mb-1">--</span>
                        <h4 id="modalNamaPemegang" class="text-sm font-black text-slate-900 truncate">--</h4>
                        <p id="modalPerusahaan" class="text-xs text-slate-600 truncate mt-0.5">--</p>
                        <p id="modalJabatan" class="text-[11px] text-slate-400 truncate">--</p>
                    </div>
                </div>

                <!-- Meta Informasi Pemindaian -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Waktu Pemindaian</span>
                        <span id="modalWaktu" class="font-bold text-slate-900 block mt-0.5">--</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Arah & Pos Gerbang</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span id="modalArah" class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-slate-200 text-slate-800">--</span>
                            <span id="modalArea" class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-slate-900 text-white">--</span>
                        </div>
                    </div>
                </div>

                <!-- Pos Kamera & Izin Area -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 uppercase text-[10px] font-extrabold">Nama Kamera:</span>
                        <span id="modalNamaKamera" class="font-bold text-slate-800">--</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 uppercase text-[10px] font-extrabold">Area Izin Kartu:</span>
                        <div id="modalAreaList" class="flex items-center gap-1">--</div>
                    </div>
                </div>

                <!-- Hasil Verifikasi & Alasan -->
                <div id="modalAlasanBox" class="p-3.5 rounded-xl border text-xs">
                    <span class="uppercase text-[10px] font-extrabold block mb-1">Hasil Verifikasi Sistem:</span>
                    <p id="modalAlasanText" class="font-bold leading-relaxed">--</p>
                </div>

                <!-- Catatan Petugas (Interactive Update) -->
                <div class="space-y-1.5 pt-1">
                    <label for="modalCatatanInput" class="block text-slate-600 font-extrabold uppercase text-[10px] tracking-wider">
                        <i class="fas fa-sticky-note text-amber-500 mr-1"></i> Catatan Petugas:
                    </label>
                    <textarea id="modalCatatanInput" rows="2"
                              placeholder="Tambahkan catatan khusus untuk log ini jika ada insiden..."
                              class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-[#1e3a5f] focus:ring-1 focus:ring-[#1e3a5f]"></textarea>
                    <div class="flex items-center justify-between mt-1">
                        <span id="catatanSaveStatus" class="text-[11px] text-emerald-600 font-bold hidden">
                            <i class="fas fa-check"></i> Catatan disimpan
                        </span>
                        <button type="button" onclick="saveModalCatatan()"
                                class="ml-auto px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                            Simpan Catatan
                        </button>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                <button type="button" onclick="closeDetailModal()"
                        class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-extrabold text-xs transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- Script for Modal & Note Saving -->
    <script>
        let currentDetailId = null;

        function openDetailModal(log) {
            currentDetailId = log.id;

            const modal = document.getElementById('detailModal');
            const modalHeader = document.getElementById('modalHeader');
            const modalStatusIconWrap = document.getElementById('modalStatusIconWrap');
            const modalStatusIcon = document.getElementById('modalStatusIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalAlasanBox = document.getElementById('modalAlasanBox');
            const modalAlasanText = document.getElementById('modalAlasanText');

            const modalNoKartu = document.getElementById('modalNoKartu');
            const modalNamaPemegang = document.getElementById('modalNamaPemegang');
            const modalPerusahaan = document.getElementById('modalPerusahaan');
            const modalJabatan = document.getElementById('modalJabatan');
            const modalWaktu = document.getElementById('modalWaktu');
            const modalArah = document.getElementById('modalArah');
            const modalArea = document.getElementById('modalArea');
            const modalNamaKamera = document.getElementById('modalNamaKamera');
            const modalAreaList = document.getElementById('modalAreaList');
            const modalFotoImg = document.getElementById('modalFotoImg');
            const modalFotoPlaceholder = document.getElementById('modalFotoPlaceholder');
            const modalCatatanInput = document.getElementById('modalCatatanInput');
            const catatanSaveStatus = document.getElementById('catatanSaveStatus');

            catatanSaveStatus.classList.add('hidden');
            modalCatatanInput.value = log.catatan || '';

            // Populate Content
            modalNoKartu.innerText = log.nomor_kartu;
            modalNamaPemegang.innerText = log.nama_pemegang;
            modalPerusahaan.innerText = log.perusahaan;
            modalJabatan.innerText = log.jabatan || '-';
            modalWaktu.innerText = log.waktu_scan;
            modalArah.innerText = log.tipe_aktivitas;
            modalArea.innerText = 'Area ' + log.kode_area;
            modalNamaKamera.innerText = log.nama_kamera;
            modalAlasanText.innerText = log.alasan;

            // Foto
            if (log.foto_url) {
                modalFotoImg.src = log.foto_url;
                modalFotoImg.classList.remove('hidden');
                modalFotoPlaceholder.classList.add('hidden');
            } else {
                modalFotoImg.classList.add('hidden');
                modalFotoPlaceholder.classList.remove('hidden');
            }

            // Area list
            modalAreaList.innerHTML = '';
            const areas = (log.area_akses || '').split(',').map(s => s.trim()).filter(Boolean);
            if (areas.length > 0) {
                areas.forEach(a => {
                    const span = document.createElement('span');
                    span.className = 'px-1.5 py-0.5 rounded text-[10px] font-black bg-slate-200 text-slate-800';
                    span.innerText = a;
                    modalAreaList.appendChild(span);
                });
            } else {
                modalAreaList.innerText = '-';
            }

            // Theme by status
            const isValid = log.status_akses === 'diterima';
            if (isValid) {
                modalHeader.className = 'p-5 flex items-center justify-between text-white bg-gradient-to-r from-emerald-600 to-teal-700';
                modalStatusIcon.className = 'fas fa-check-circle';
                modalTitle.innerText = 'AKSES VALID (DITERIMA)';
                modalAlasanBox.className = 'p-3.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900';
            } else {
                modalHeader.className = 'p-5 flex items-center justify-between text-white bg-gradient-to-r from-rose-700 to-red-800';
                modalStatusIcon.className = 'fas fa-times-circle';
                modalTitle.innerText = 'AKSES DITOLAK';
                modalAlasanBox.className = 'p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-900';
            }

            modal.classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            currentDetailId = null;
        }

        async function saveModalCatatan() {
            if (!currentDetailId) return;

            const catatan = document.getElementById('modalCatatanInput').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const statusEl = document.getElementById('catatanSaveStatus');

            try {
                const response = await fetch(`/scan/catatan/${currentDetailId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ catatan: catatan })
                });

                const res = await response.json();
                if (res.success) {
                    statusEl.classList.remove('hidden');
                    setTimeout(() => statusEl.classList.add('hidden'), 3000);
                } else {
                    alert('Gagal menyimpan catatan: ' + (res.message || 'Terjadi kesalahan'));
                }
            } catch (err) {
                console.error("Save catatan error", err);
                alert('Gagal menghubungi server.');
            }
        }

        // Close on ESC or click outside
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDetailModal();
        });
        document.getElementById('detailModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeDetailModal();
        });
    </script>
</x-app-layout>
