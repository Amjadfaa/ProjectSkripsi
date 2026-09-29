<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-clock-rotate-left"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Laporan Aktivitas Area Akses
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">
                    Audit rekaman log pemindaian kartu PAS, status hak akses, dan aktivitas perangkat kamera bandara
                </p>
            </div>
        </div>
    </x-slot>

    {{-- ======================================================================== --}}
    {{-- FILTER TOOLBAR & EXPORT ACTIONS CARD                                     --}}
    {{-- ======================================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 mb-6">
        <form id="filterForm" method="GET" action="{{ route('administrator.laporan-aktivitas.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                {{-- 1. Filter Rentang Tanggal --}}
                <div class="lg:col-span-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Rentang Tanggal</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div class="relative">
                            <input type="date" name="start_date" id="filterStartDate" value="{{ $startDate }}" 
                                   class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer" 
                                   title="Tanggal Mulai">
                        </div>
                        <div class="relative">
                            <input type="date" name="end_date" id="filterEndDate" value="{{ $endDate }}" 
                                   class="w-full px-2.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer" 
                                   title="Tanggal Selesai">
                        </div>
                    </div>
                </div>

                {{-- 2. Filter Area Akses --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Area Akses</label>
                    <select name="kode_area" id="filterKodeArea" 
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="">Semua Area</option>
                        @foreach($areaAksesList as $area)
                            <option value="{{ $area->kode }}" {{ $kodeArea == $area->kode ? 'selected' : '' }}>
                                Area {{ $area->kode }} - {{ \Illuminate\Support\Str::limit($area->keterangan, 20) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Filter Status Akses --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Akses</label>
                    <select name="status_akses" id="filterStatusAkses" 
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="diterima" {{ $statusAkses == 'diterima' ? 'selected' : '' }}>Diterima (Berhasil)</option>
                        <option value="ditolak" {{ $statusAkses == 'ditolak' ? 'selected' : '' }}>Ditolak (Gagal)</option>
                    </select>
                </div>

                {{-- 4. Filter Tipe Scan (Masuk/Keluar) --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tipe Scan</label>
                    <select name="tipe_aktivitas" id="filterTipeAktivitas" 
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="">Semua Tipe Scan</option>
                        <option value="masuk" {{ $tipeAktivitas == 'masuk' ? 'selected' : '' }}>Scan Masuk (IN)</option>
                        <option value="keluar" {{ $tipeAktivitas == 'keluar' ? 'selected' : '' }}>Scan Keluar (OUT)</option>
                    </select>
                </div>

                {{-- 5. Quick Search Input --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" id="filterSearchInput" value="{{ $search }}" 
                               placeholder="Nama, No. Kartu, Instansi..." 
                               class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3.5 border-t border-slate-100 flex-wrap gap-3">
                {{-- Action Filter Buttons --}}
                <div class="flex items-center gap-2">
                    <button type="submit" id="btnFilterSubmit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-filter text-[11px]"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <button type="button" onclick="resetFilterSpa(event)" 
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer flex items-center gap-1.5"
                            title="Reset Filter ke Default">
                        <i class="fas fa-rotate-left text-[11px]"></i>
                        <span>Reset</span>
                    </button>
                </div>

                {{-- Export Buttons --}}
                <div class="flex items-center gap-2 shrink-0">
                    <a id="btnExportExcel" href="{{ route('administrator.laporan-aktivitas.export.excel', request()->all()) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                        <i class="fas fa-file-excel"></i>
                        <span>Export Excel</span>
                    </a>
                    <a id="btnExportPdf" href="{{ route('administrator.laporan-aktivitas.export.pdf', request()->all()) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                        <i class="fas fa-file-pdf"></i>
                        <span>Export PDF</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ======================================================================== --}}
    {{-- TOP KPI STAT CARDS (5 EXECUTIVE CARDS)                                   --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {{-- 1. Total Scan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between hover:shadow-sm transition-all group">
            <div class="min-w-0 pr-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">Total Scan</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span id="kpiTotalScan" class="text-2xl font-extrabold text-blue-600">{{ number_format($totalScan) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Log</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2 flex items-center gap-1 font-medium truncate">
                    <i class="fas fa-qrcode text-blue-500 text-[10px]"></i>
                    <span>Total riwayat scan</span>
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-qrcode"></i>
            </div>
        </div>

        {{-- 2. Scan Masuk (IN) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between hover:shadow-sm transition-all group">
            <div class="min-w-0 pr-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">Scan Masuk (IN)</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span id="kpiTotalMasuk" class="text-2xl font-extrabold text-emerald-600">{{ number_format($totalMasuk) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Log</span>
                </div>
                <p class="text-[11px] text-emerald-700 mt-2 font-semibold flex items-center gap-1 truncate">
                    <i class="fas fa-right-to-bracket text-emerald-500 text-[10px]"></i>
                    <span>Pintu kedatangan</span>
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-right-to-bracket"></i>
            </div>
        </div>

        {{-- 3. Scan Keluar (OUT) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between hover:shadow-sm transition-all group">
            <div class="min-w-0 pr-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">Scan Keluar (OUT)</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span id="kpiTotalKeluar" class="text-2xl font-extrabold text-amber-600">{{ number_format($totalKeluar) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Log</span>
                </div>
                <p class="text-[11px] text-amber-700 mt-2 font-semibold flex items-center gap-1 truncate">
                    <i class="fas fa-right-from-bracket text-amber-500 text-[10px]"></i>
                    <span>Pintu keberangkatan</span>
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-right-from-bracket"></i>
            </div>
        </div>

        {{-- 4. Akses Diterima --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between hover:shadow-sm transition-all group">
            <div class="min-w-0 pr-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">Akses Diterima</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span id="kpiTotalDiterima" class="text-2xl font-extrabold text-teal-600">{{ number_format($totalDiterima) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Log</span>
                </div>
                <p class="text-[11px] text-teal-700 mt-2 font-semibold flex items-center gap-1 truncate">
                    <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                    <span>Hak akses valid</span>
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>

        {{-- 5. Akses Ditolak --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between hover:shadow-sm transition-all group">
            <div class="min-w-0 pr-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider truncate">Akses Ditolak</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span id="kpiTotalDitolak" class="text-2xl font-extrabold text-rose-600">{{ number_format($totalDitolak) }}</span>
                    <span class="text-xs font-semibold text-slate-400">Log</span>
                </div>
                <p class="text-[11px] text-rose-700 mt-2 font-semibold flex items-center gap-1 truncate">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span>Izin akses tidak sesuai</span>
                </p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- VISUAL CHARTS ROW (TREND LINE & AREA DISTRIBUTION DONUT)                 --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        {{-- 1. Main Line Chart Tren Harian --}}
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-bold text-sm sm:text-base text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        <span>Tren Aktivitas Scan Harian</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Frekuensi perbandingan scan masuk (IN), keluar (OUT), dan akses ditolak</p>
                </div>
            </div>
            <div class="relative h-[260px] w-full">
                <canvas id="chartAktivitasTrend"></canvas>
            </div>
        </div>

        {{-- 2. Donut Chart Distribusi per Area --}}
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col">
            <div class="mb-3">
                <h3 class="font-bold text-sm sm:text-base text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                    <span>Distribusi Scan per Area</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Proporsi kepadatan lalu-lintas per titik area akses</p>
            </div>
            <div class="relative flex-1 flex items-center justify-center min-h-[220px]">
                <canvas id="chartAktivitasArea"></canvas>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- LOG TABLE CARD (REUSABLE COMPONENT DENGAN DUKUNGAN SPA)                   --}}
    {{-- ======================================================================== --}}
    <x-table-card title="Riwayat Log Aktivitas Scan Masuk / Keluar"
                  subtitle="Audit rekaman pemindaian kartu PAS, titik area, perangkat kamera, dan validasi akses"
                  icon="fas fa-clock-rotate-left"
                  iconColor="bg-blue-50 text-blue-600">

        {{-- Table Viewport with Persistent Loading Overlay (SPA Replaced via AJAX) --}}
        <div class="relative">
            {{-- Shimmer Loading Overlay --}}
            <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 hidden items-center justify-center transition-all duration-200">
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/90 text-white shadow-2xl text-xs font-bold border border-slate-700 backdrop-blur-md">
                    <i class="fas fa-circle-notch fa-spin text-blue-400 text-base"></i>
                    <span>Memperbarui data aktivitas scan...</span>
                </div>
            </div>

            {{-- Table Container (SPA HTML injected here) --}}
            <div id="tableContainer" class="transition-opacity duration-200">
                @include('administrator.laporan-aktivitas.partials.table', ['scanLogs' => $scanLogs])
            </div>
        </div>

    </x-table-card>

    {{-- ======================================================================== --}}
    {{-- CHART.JS & SEAMLESS SPA NAVIGATION JAVASCRIPT ENGINE                      --}}
    {{-- ======================================================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let areaChartInstance = null;
        let trendChartInstance = null;

        // ---------------------------------------------------------------------
        // 1. INITIALIZE CHARTS
        // ---------------------------------------------------------------------
        function initAreaChart(labels, dataValues) {
            const ctxArea = document.getElementById('chartAktivitasArea').getContext('2d');
            if (areaChartInstance) {
                areaChartInstance.destroy();
            }

            const defaultPalette = ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#64748b', '#f97316'];

            areaChartInstance = new Chart(ctxArea, {
                type: 'doughnut',
                data: {
                    labels: labels && labels.length ? labels : ['Belum Ada Data'],
                    datasets: [{
                        data: dataValues && dataValues.length ? dataValues : [0],
                        backgroundColor: defaultPalette,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } }
                    }
                }
            });
        }

        function initTrendChart(labels, masukData, keluarData, ditolakData) {
            const ctxTrend = document.getElementById('chartAktivitasTrend').getContext('2d');
            if (trendChartInstance) {
                trendChartInstance.destroy();
            }

            trendChartInstance = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: labels && labels.length ? labels : ['Hari Ini'],
                    datasets: [
                        {
                            label: 'Scan Masuk (IN)',
                            data: masukData && masukData.length ? masukData : [0],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#10b981',
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            tension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Scan Keluar (OUT)',
                            data: keluarData && keluarData.length ? keluarData : [0],
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.06)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#f59e0b',
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            tension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Akses Ditolak',
                            data: ditolakData && ditolakData.length ? ditolakData : [0],
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.04)',
                            borderWidth: 2,
                            borderDash: [4, 4],
                            pointBackgroundColor: '#ef4444',
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
        }

        // Initial Data from Blade
        const initialAreaLabels  = @json($chartDataArea->keys()->map(fn($a) => 'Area ' . $a));
        const initialAreaData    = @json($chartDataArea->values());
        const initialTrendLabels = @json($trendLabels ?? []);
        const initialTrendMasuk  = @json($trendMasuk ?? []);
        const initialTrendKeluar = @json($trendKeluar ?? []);
        const initialTrendDitolak = @json($trendDitolak ?? []);

        initAreaChart(initialAreaLabels, initialAreaData);
        initTrendChart(initialTrendLabels, initialTrendMasuk, initialTrendKeluar, initialTrendDitolak);

        // ---------------------------------------------------------------------
        // 2. SPA SEAMLESS NAVIGATION ENGINE
        // ---------------------------------------------------------------------
        let searchDebounceTimer;

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

        async function fetchSpaData(url, pushState = true) {
            const container = document.getElementById('tableContainer');
            if (!container) return;

            showTableLoading();

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-SPA': 'true'
                    }
                });

                if (!response.ok) throw new Error('Network error: ' + response.status);

                const data = await response.json();

                // 1. Update Table Partial
                if (data.table_html !== undefined) {
                    container.innerHTML = data.table_html;
                }

                // 2. Update Top KPI Cards
                if (data.totalScan !== undefined && document.getElementById('kpiTotalScan')) {
                    document.getElementById('kpiTotalScan').textContent = data.totalScan;
                }
                if (data.totalMasuk !== undefined && document.getElementById('kpiTotalMasuk')) {
                    document.getElementById('kpiTotalMasuk').textContent = data.totalMasuk;
                }
                if (data.totalKeluar !== undefined && document.getElementById('kpiTotalKeluar')) {
                    document.getElementById('kpiTotalKeluar').textContent = data.totalKeluar;
                }
                if (data.totalDiterima !== undefined && document.getElementById('kpiTotalDiterima')) {
                    document.getElementById('kpiTotalDiterima').textContent = data.totalDiterima;
                }
                if (data.totalDitolak !== undefined && document.getElementById('kpiTotalDitolak')) {
                    document.getElementById('kpiTotalDitolak').textContent = data.totalDitolak;
                }

                // 3. Update Charts
                if (data.chartLabels && data.chartValues) {
                    initAreaChart(data.chartLabels, data.chartValues);
                }
                if (data.trendLabels && data.trendMasuk && data.trendKeluar) {
                    initTrendChart(data.trendLabels, data.trendMasuk, data.trendKeluar, data.trendDitolak);
                }

                // 4. Update Export Button Links
                const urlObj = new URL(url, window.location.origin);
                const queryStr = urlObj.search;
                const btnExcel = document.getElementById('btnExportExcel');
                const btnPdf   = document.getElementById('btnExportPdf');
                if (btnExcel) btnExcel.href = `{{ route('administrator.laporan-aktivitas.export.excel') }}${queryStr}`;
                if (btnPdf)   btnPdf.href   = `{{ route('administrator.laporan-aktivitas.export.pdf') }}${queryStr}`;

                // 5. Update Browser URL History
                if (pushState) {
                    window.history.pushState(null, '', url);
                }
            } catch (err) {
                console.error('SPA Navigation Error:', err);
                window.location.href = url;
            } finally {
                hideTableLoading();
            }
        }

        // Global Event Interceptor: Clicks on Pagination Links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('#tableContainer a.pagination-link, #tableContainer a[data-spa="true"], #tableContainer a[href*="page="]');
            if (link && link.href) {
                e.preventDefault();
                fetchSpaData(link.href, true);
            }
        });

        // Event Interceptor: Filter Form Submit
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const targetUrl = `${filterForm.action}?${params.toString()}`;
                fetchSpaData(targetUrl, true);
            });

            // Live Change on Filter Dropdowns & Dates
            ['filterKodeArea', 'filterStatusAkses', 'filterTipeAktivitas'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', () => {
                        filterForm.dispatchEvent(new Event('submit'));
                    });
                }
            });

            // Live Debounce on Search Input
            const searchInput = document.getElementById('filterSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchDebounceTimer);
                    searchDebounceTimer = setTimeout(() => {
                        filterForm.dispatchEvent(new Event('submit'));
                    }, 400);
                });
            }
        }

        // Reset Filter SPA
        function resetFilterSpa(e) {
            if (e) e.preventDefault();
            if (filterForm) {
                document.getElementById('filterKodeArea').value = '';
                document.getElementById('filterStatusAkses').value = '';
                document.getElementById('filterTipeAktivitas').value = '';
                document.getElementById('filterSearchInput').value = '';
                const defaultUrl = "{{ route('administrator.laporan-aktivitas.index') }}";
                fetchSpaData(defaultUrl, true);
            }
        }

        // Handle Browser Back & Forward Navigation
        window.addEventListener('popstate', function() {
            fetchSpaData(window.location.href, false);
        });
    </script>
</x-app-layout>
