<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-chart-pie"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Laporan Kartu PAS
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">
                    Statistik rekapitulasi, grafik tren penerbitan, dan audit detail riwayat kartu PAS bandara
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $isBulanFilter = (!empty($bulan) && $bulan !== 'all');
        $namaBulanSelected = $isBulanFilter && isset($namaBulanList[(int)$bulan]) ? $namaBulanList[(int)$bulan] : null;
        $periodeLabel = $isBulanFilter ? "$namaBulanSelected $tahun" : "Tahun $tahun";
    @endphp

    {{-- ======================================================================== --}}
    {{-- FILTER TAHUN & BULAN + EXPORT TOOLBAR                                     --}}
    {{-- ======================================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 mb-6">
        <form method="GET" action="{{ route('administrator.laporan.index') }}" class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div class="flex flex-wrap items-end gap-3">
                {{-- Pilih Tahun --}}
                <div class="min-w-[150px]">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Tahun</label>
                    <select name="tahun" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                        @endforeach
                        @if(!$tahunList->contains(date('Y')))
                            <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>Tahun {{ date('Y') }}</option>
                        @endif
                    </select>
                </div>

                {{-- Pilih Bulan --}}
                <div class="min-w-[210px]">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Bulan</label>
                    <select name="bulan" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="all" {{ (!$isBulanFilter) ? 'selected' : '' }}>Semua Bulan (Januari - Desember)</option>
                        @foreach($namaBulanList as $num => $namaBln)
                            <option value="{{ $num }}" {{ ($isBulanFilter && (int)$bulan === $num) ? 'selected' : '' }}>{{ $namaBln }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Filter --}}
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-filter text-[11px]"></i>
                    <span>Filter Data</span>
                </button>

                @if($isBulanFilter)
                    <a href="{{ route('administrator.laporan.index', ['tahun' => $tahun, 'bulan' => 'all']) }}"
                       class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition flex items-center gap-1.5"
                       title="Kembali ke semua bulan">
                        <i class="fas fa-rotate-left text-[11px]"></i>
                        <span>Reset Bulan</span>
                    </a>
                @endif
            </div>

            {{-- Tombol Export --}}
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('administrator.laporan.export.excel', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
                <a href="{{ route('administrator.laporan.export.pdf', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export PDF</span>
                </a>
            </div>
        </form>
    </div>

    {{-- ======================================================================== --}}
    {{-- TOP KPI STAT CARDS (4 BALANCED CARDS)                                     --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- 1. Kartu Terbit --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kartu Terbit ({{ $periodeLabel }})</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalTerbit" class="text-2xl font-extrabold text-blue-600">{{ number_format($totalKartuTerbit) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Kartu</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2.5 flex items-center gap-1.5 font-medium">
                    <i class="fas fa-id-card text-blue-500 text-[11px]"></i>
                    <span>Permohonan terverifikasi</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-id-card"></i>
            </div>
        </div>

        {{-- 2. Kartu PAS Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kartu PAS Aktif</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalAktif" class="text-2xl font-extrabold text-emerald-600">{{ number_format($totalKartuAktif) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Kartu</span>
                </div>
                <p class="text-[11px] text-emerald-700 mt-2.5 font-semibold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Hak akses berlaku</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>

        {{-- 3. Kartu Kadaluarsa --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kartu Kadaluarsa</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalKadaluarsa" class="text-2xl font-extrabold text-amber-600">{{ number_format($totalKadaluarsa) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Kartu</span>
                </div>
                <p class="text-[11px] text-amber-700 mt-2.5 font-semibold flex items-center gap-1.5">
                    <i class="fas fa-clock-rotate-left text-amber-500 text-[11px]"></i>
                    <span>Masa berlaku habis</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        {{-- 4. Kartu Nonaktif / Blokir --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kartu Nonaktif / Blokir</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalNonaktif" class="text-2xl font-extrabold text-rose-600">{{ number_format($totalNonaktif) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Kartu</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2.5 flex items-center gap-1.5 font-medium">
                    <i class="fas fa-ban text-rose-500 text-[11px]"></i>
                    <span>Akses dinonaktifkan</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-ban"></i>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- VISUAL CHARTS ROW (LINE TREND & DONUT DISTRIBUTION)                      --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        {{-- 1. Main Line Chart Tren Kartu PAS --}}
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-bold text-sm sm:text-base text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        <span>Tren Kartu PAS per Bulan (Tahun {{ $tahun }})</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Statistik perkembangan kartu baru terbit & diperpanjang setiap bulan</p>
                </div>
            </div>
            <div class="relative h-[270px] w-full">
                <canvas id="chartKartuPasTrend"></canvas>
            </div>
        </div>

        {{-- 2. Donut Chart Distribusi per Instansi --}}
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="font-bold text-sm sm:text-base text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                    <span>Distribusi per Instansi ({{ $periodeLabel }})</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Proporsi penerbitan Kartu PAS per perusahaan</p>
            </div>
            <div class="relative flex-1 flex items-center justify-center min-h-[220px]">
                <canvas id="chartDistribusiInstansi"></canvas>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- REKAPITULASI LAPORAN BULANAN (12 BULAN)                                   --}}
    {{-- ======================================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-sm sm:text-base text-slate-800 flex items-center gap-2">
                    <i class="fas fa-calendar-days text-indigo-600"></i>
                    <span>Rekapitulasi Laporan Kartu PAS Bulanan (Tahun {{ $tahun }})</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Klik tombol filter bulan di tabel untuk melihat rincian kartu pada bulan tersebut</p>
            </div>
        </div>

        <div class="w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 select-none">
                        <th class="px-3.5 py-3">Bulan</th>
                        <th class="px-3.5 py-3 text-center">Kartu Baru Terbit</th>
                        <th class="px-3.5 py-3 text-center">Kartu Diperpanjang</th>
                        <th class="px-3.5 py-3 text-center">Kartu Kadaluarsa</th>
                        <th class="px-3.5 py-3 text-center">Total Terbit / Diperbarui</th>
                        <th class="px-3.5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs">
                    @foreach($laporanKartu as $laporan)
                        @php
                            $isSelectedRow = ($isBulanFilter && (int)$bulan === (int)$laporan->bulan);
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition {{ $isSelectedRow ? 'bg-blue-50/80 font-bold border-l-4 border-blue-600' : '' }}">
                            <td class="px-3.5 py-3 font-bold text-slate-800 flex items-center gap-2">
                                <span>{{ $namaBulanList[$laporan->bulan] ?? "Bulan $laporan->bulan" }} {{ $laporan->tahun }}</span>
                                @if($isSelectedRow)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-blue-600 text-white uppercase tracking-wider shadow-2xs">Terpilih</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                    {{ number_format($laporan->kartu_baru) }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                                    {{ number_format($laporan->kartu_diperpanjang) }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                    {{ number_format($laporan->kartu_kadaluarsa) }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-center font-bold text-slate-800">
                                <span class="px-3 py-0.5 rounded-full text-xs font-black bg-slate-100 text-slate-800 border border-slate-200 shadow-2xs">
                                    {{ number_format($laporan->total_terbit) }}
                                </span>
                            </td>
                            <td class="px-3.5 py-3 text-center">
                                <a href="{{ route('administrator.laporan.index', ['tahun' => $tahun, 'bulan' => $laporan->bulan]) }}"
                                   class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-lg font-bold transition shadow-2xs {{ $isSelectedRow ? 'bg-blue-600 text-white' : 'bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200' }}">
                                    <i class="fas fa-eye text-[10px]"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50/90 font-black text-slate-800 border-t-2 border-slate-200/80">
                        <td class="px-3.5 py-3">TOTAL TAHUN {{ $tahun }}</td>
                        <td class="px-3.5 py-3 text-center text-blue-700">{{ number_format($laporanKartu->sum('kartu_baru')) }}</td>
                        <td class="px-3.5 py-3 text-center text-purple-700">{{ number_format($laporanKartu->sum('kartu_diperpanjang')) }}</td>
                        <td class="px-3.5 py-3 text-center text-amber-700">{{ number_format($laporanKartu->sum('kartu_kadaluarsa')) }}</td>
                        <td class="px-3.5 py-3 text-center text-slate-900">{{ number_format($laporanKartu->sum('total_terbit')) }}</td>
                        <td class="px-3.5 py-3 text-center">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- RINCIAN KARTU PAS DETAIL TABLE (REUSABLE COMPONENT DENGAN SPA)            --}}
    {{-- ======================================================================== --}}
    <x-table-card :title="'Rincian Kartu PAS - ' . ($isBulanFilter ? 'Bulan ' . $namaBulanSelected . ' ' . $tahun : 'Tahun ' . $tahun)"
                  :subtitle="'Daftar rincian kartu PAS yang diterbitkan pada periode ' . $periodeLabel . ' (Total: ' . $detailKartuPas->total() . ' kartu)'"
                  icon="fas fa-id-card-clip"
                  iconColor="bg-blue-50 text-blue-600">

        {{-- Filter Slot --}}
        <x-slot name="filters">
            <form id="filterDetailForm" method="GET" action="{{ route('administrator.laporan.index') }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">

                <div class="flex flex-wrap items-end gap-3">
                    {{-- Pencarian Input --}}
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian Kartu</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                                <i class="fas fa-magnifying-glass"></i>
                            </span>
                            <input type="text" id="detailSearchInput" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nomor kartu, nama pemegang, perusahaan, jabatan..."
                                   class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs">
                            @if(request('search'))
                                <button type="button" onclick="clearDetailSearch()" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-circle-xmark text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Filter Status --}}
                    <div class="min-w-[150px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Kartu</label>
                        <select id="filterStatusSelect" name="status" 
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="kadaluarsa" {{ request('status') === 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-filter text-[11px]"></i>
                            <span>Filter</span>
                        </button>
                        <button type="button" onclick="resetDetailFilter()"
                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                                title="Reset Filter Rincian">
                            <i class="fas fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </form>
        </x-slot>

        {{-- Table Viewport with Persistent Loading Overlay (SPA Replaced via AJAX) --}}
        <div class="relative">
            {{-- Shimmer Loading Overlay --}}
            <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 hidden items-center justify-center transition-all duration-200">
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/90 text-white shadow-2xl text-xs font-bold border border-slate-700 backdrop-blur-md">
                    <i class="fas fa-circle-notch fa-spin text-blue-400 text-base"></i>
                    <span>Memperbarui data laporan kartu PAS...</span>
                </div>
            </div>

            {{-- Table Container (SPA HTML injected here) --}}
            <div id="tableContainer" class="transition-opacity duration-200">
                @include('administrator.laporan.partials.table')
            </div>
        </div>

    </x-table-card>

    {{-- ======================================================================== --}}
    {{-- SPA PAGINATION & CHART SCRIPTS                                           --}}
    {{-- ======================================================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ---------------------------------------------------------------------
        // CHARTS INITIALIZATION
        // ---------------------------------------------------------------------
        const bulanLabel       = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const dataKartuBaru    = @json($laporanKartu->pluck('kartu_baru', 'bulan'));
        const dataDiperpanjang = @json($laporanKartu->pluck('kartu_diperpanjang', 'bulan'));
        const dataKadaluarsa   = @json($laporanKartu->pluck('kartu_kadaluarsa', 'bulan'));

        // Line Chart for Trend
        const ctxTrend = document.getElementById('chartKartuPasTrend').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: bulanLabel,
                datasets: [
                    { 
                        label: 'Kartu Baru Terbit', 
                        data: bulanLabel.map((_, i) => dataKartuBaru[i+1] ?? 0), 
                        borderColor: '#2563eb', 
                        backgroundColor: 'rgba(37, 99, 235, 0.08)', 
                        borderWidth: 3,
                        pointBackgroundColor: '#2563eb',
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        tension: 0.35, 
                        fill: true 
                    },
                    { 
                        label: 'Kartu Diperpanjang', 
                        data: bulanLabel.map((_, i) => dataDiperpanjang[i+1] ?? 0), 
                        borderColor: '#9333ea', 
                        backgroundColor: 'rgba(147, 51, 234, 0.06)', 
                        borderWidth: 3,
                        pointBackgroundColor: '#9333ea',
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        tension: 0.35, 
                        fill: true 
                    },
                    { 
                        label: 'Kadaluarsa', 
                        data: bulanLabel.map((_, i) => dataKadaluarsa[i+1] ?? 0), 
                        borderColor: '#d97706', 
                        backgroundColor: 'rgba(217, 119, 6, 0.04)', 
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointBackgroundColor: '#d97706',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.35
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { weight: 'bold', size: 11 } } } 
                }, 
                scales: { 
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                } 
            }
        });

        // Donut Chart for Instansi Distribution
        const instansiNames  = @json($distribusiInstansi->pluck('nama_instansi'));
        const instansiCounts = @json($distribusiInstansi->pluck('kartu_pas_count'));

        const ctxDonut = document.getElementById('chartDistribusiInstansi').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: instansiNames,
                datasets: [{
                    data: instansiCounts,
                    backgroundColor: [
                        '#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', 
                        '#06b6d4', '#84cc16', '#64748b', '#f97316'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                }
            }
        });

        // ---------------------------------------------------------------------
        // SPA TABLE ENGINE: SEAMLESS PAGINATION & LIVE FILTERING
        // ---------------------------------------------------------------------
        let filterDebounceTimer;

        function showTableLoading() {
            const overlay   = document.getElementById('tableLoadingOverlay');
            const container = document.getElementById('tableContainer');
            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }
            if (container) {
                container.classList.add('opacity-40', 'pointer-events-none');
            }
        }

        function hideTableLoading() {
            const overlay   = document.getElementById('tableLoadingOverlay');
            const container = document.getElementById('tableContainer');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
            if (container) {
                container.classList.remove('opacity-40', 'pointer-events-none');
            }
        }

        async function fetchSpaLaporanTable(url, pushState = true) {
            const container = document.getElementById('tableContainer');
            if (!container) return;

            showTableLoading();

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SPA': 'true'
                    }
                });

                if (!response.ok) throw new Error('Network error');

                const html = await response.text();
                container.innerHTML = html;

                if (pushState) {
                    window.history.pushState(null, '', url);
                }

                // Perbarui data KPI di kartu statistik atas jika ada
                const kpiDataEl = document.getElementById('spaKpiData');
                if (kpiDataEl) {
                    const terbit     = kpiDataEl.getAttribute('data-terbit');
                    const aktif      = kpiDataEl.getAttribute('data-aktif');
                    const kadaluarsa = kpiDataEl.getAttribute('data-kadaluarsa');
                    const nonaktif   = kpiDataEl.getAttribute('data-nonaktif');

                    if (terbit !== null && document.getElementById('kpiTotalTerbit')) {
                        document.getElementById('kpiTotalTerbit').textContent = Number(terbit).toLocaleString();
                    }
                    if (aktif !== null && document.getElementById('kpiTotalAktif')) {
                        document.getElementById('kpiTotalAktif').textContent = Number(aktif).toLocaleString();
                    }
                    if (kadaluarsa !== null && document.getElementById('kpiTotalKadaluarsa')) {
                        document.getElementById('kpiTotalKadaluarsa').textContent = Number(kadaluarsa).toLocaleString();
                    }
                    if (nonaktif !== null && document.getElementById('kpiTotalNonaktif')) {
                        document.getElementById('kpiTotalNonaktif').textContent = Number(nonaktif).toLocaleString();
                    }
                }
            } catch (err) {
                console.error('SPA Navigation Error:', err);
                window.location.href = url;
            } finally {
                hideTableLoading();
            }
        }

        // Intercept Klik Paginasi Secara Global untuk SPA
        document.addEventListener('click', function(e) {
            const link = e.target.closest('#tableContainer a.pagination-link, #tableContainer a[data-spa="true"], #tableContainer a[href*="page="]');
            if (link && link.href) {
                e.preventDefault();
                fetchSpaLaporanTable(link.href, true);
            }
        });

        // Intercept Submit Filter Rincian Detail
        const filterDetailForm = document.getElementById('filterDetailForm');
        if (filterDetailForm) {
            filterDetailForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(filterDetailForm);
                const params = new URLSearchParams(formData);
                const targetUrl = `${filterDetailForm.action}?${params.toString()}`;
                fetchSpaLaporanTable(targetUrl, true);
            });

            // Live Change pada Dropdown Status
            const statusSelect = document.getElementById('filterStatusSelect');
            if (statusSelect) {
                statusSelect.addEventListener('change', () => {
                    filterDetailForm.dispatchEvent(new Event('submit'));
                });
            }

            // Live Debounce pada Input Pencarian
            const searchInput = document.getElementById('detailSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(filterDebounceTimer);
                    filterDebounceTimer = setTimeout(() => {
                        filterDetailForm.dispatchEvent(new Event('submit'));
                    }, 350);
                });
            }
        }

        function clearDetailSearch() {
            const input = document.getElementById('detailSearchInput');
            if (input) {
                input.value = '';
                filterDetailForm.dispatchEvent(new Event('submit'));
            }
        }

        function resetDetailFilter() {
            if (filterDetailForm) {
                document.getElementById('detailSearchInput').value = '';
                document.getElementById('filterStatusSelect').value = '';
                const formData = new FormData(filterDetailForm);
                const params = new URLSearchParams(formData);
                const targetUrl = `${filterDetailForm.action}?${params.toString()}`;
                fetchSpaLaporanTable(targetUrl, true);
            }
        }

        // Handle Browser Back & Forward Buttons
        window.addEventListener('popstate', function() {
            fetchSpaLaporanTable(window.location.href, false);
        });
    </script>
</x-app-layout>