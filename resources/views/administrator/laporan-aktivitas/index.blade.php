<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-history text-blue-600"></i> Laporan Aktivitas Area Akses
        </h2>
    </x-slot>

    <!-- Filter Card Header -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
        <form id="filterForm" method="GET" action="{{ route('administrator.laporan-aktivitas.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Filter Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Rentang Tanggal</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-gray-300 rounded-lg shadow-sm text-xs px-2 py-2 focus:ring-blue-500 focus:border-blue-500" title="Tanggal Mulai">
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-gray-300 rounded-lg shadow-sm text-xs px-2 py-2 focus:ring-blue-500 focus:border-blue-500" title="Tanggal Selesai">
                    </div>
                </div>

                <!-- Filter Area Akses -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Area Akses</label>
                    <select name="kode_area" class="w-full border-gray-300 rounded-lg shadow-sm text-xs p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Semua Area --</option>
                        @foreach($areaAksesList as $area)
                            <option value="{{ $area->kode }}" {{ $kodeArea == $area->kode ? 'selected' : '' }}>
                                Area {{ $area->kode }} - {{ \Illuminate\Support\Str::limit($area->keterangan, 20) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Akses -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Status Akses</label>
                    <select name="status_akses" class="w-full border-gray-300 rounded-lg shadow-sm text-xs p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Semua Status --</option>
                        <option value="diterima" {{ $statusAkses == 'diterima' ? 'selected' : '' }}>Diterima (Berhasil)</option>
                        <option value="ditolak" {{ $statusAkses == 'ditolak' ? 'selected' : '' }}>Ditolak (Gagal)</option>
                    </select>
                </div>

                <!-- Filter Tipe Aktivitas (Masuk/Keluar) -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Tipe Scan</label>
                    <select name="tipe_aktivitas" class="w-full border-gray-300 rounded-lg shadow-sm text-xs p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Semua Tipe Scan --</option>
                        <option value="masuk" {{ $tipeAktivitas == 'masuk' ? 'selected' : '' }}>Scan Masuk (IN)</option>
                        <option value="keluar" {{ $tipeAktivitas == 'keluar' ? 'selected' : '' }}>Scan Keluar (OUT)</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Pencarian</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama / No. Kartu..." class="w-full border-gray-300 rounded-lg shadow-sm text-xs p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-gray-100 flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <button type="submit" id="btnFilterSubmit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('administrator.laporan-aktivitas.index') }}" onclick="resetFilterSpa(event)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3.5 py-2 rounded-lg text-xs font-semibold transition">
                        Reset
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <a id="btnExportExcel" href="{{ route('administrator.laporan-aktivitas.export.excel', request()->all()) }}"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a id="btnExportPdf" href="{{ route('administrator.laporan-aktivitas.export.pdf', request()->all()) }}"
                       class="bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-lg text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Ringkasan KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Total Scan</p>
                <p id="kpiTotalScan" class="text-2xl font-black text-blue-600 mt-0.5">{{ number_format($totalScan) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-walking"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Scan Masuk (IN)</p>
                <p id="kpiTotalMasuk" class="text-2xl font-black text-emerald-600 mt-0.5">{{ number_format($totalMasuk) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-sign-in-alt"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-amber-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Scan Keluar (OUT)</p>
                <p id="kpiTotalKeluar" class="text-2xl font-black text-amber-600 mt-0.5">{{ number_format($totalKeluar) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-sign-out-alt"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-teal-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Akses Diterima</p>
                <p id="kpiTotalDiterima" class="text-2xl font-black text-teal-600 mt-0.5">{{ number_format($totalDiterima) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-rose-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Akses Ditolak</p>
                <p id="kpiTotalDitolak" class="text-2xl font-black text-rose-600 mt-0.5">{{ number_format($totalDitolak) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- Chart Row & Log Table -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        
        <!-- Log Table (SPA Container) -->
        <div id="tableContainer" class="lg:col-span-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col justify-between relative">
            @include('administrator.laporan-aktivitas.partials.table', ['scanLogs' => $scanLogs])
        </div>

        <!-- Chart Side Column -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex flex-col">
            <div class="mb-4">
                <h3 class="font-extrabold text-base text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-600"></i> Distribusi Scan per Area
                </h3>
                <p class="text-xs text-gray-500">Frekuensi lintas lalu-lintas per titik area akses</p>
            </div>
            <div class="relative flex-1 flex items-center justify-center min-h-[260px]">
                <canvas id="chartAktivitasArea"></canvas>
            </div>
        </div>

    </div>

    <!-- Chart.js Integration & SPA AJAX Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let areaChart = null;

        function initChart(labels, dataValues) {
            const ctxArea = document.getElementById('chartAktivitasArea').getContext('2d');
            if (areaChart) {
                areaChart.destroy();
            }

            areaChart = new Chart(ctxArea, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: [
                            '#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    }
                }
            });
        }

        // Initialize Chart on first load
        const initialLabels = @json($chartDataArea->keys()->map(fn($a) => 'Area ' . $a));
        const initialData = @json($chartDataArea->values());
        initChart(initialLabels, initialData);

        // SPA AJAX Navigation & Pagination Handler
        function fetchSpaData(url) {
            const container = document.getElementById('tableContainer');
            if (container) {
                container.style.opacity = '0.5';
            }

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (container) {
                    container.innerHTML = data.table_html;
                    container.style.opacity = '1';
                }

                // Update KPI Cards
                if (data.totalScan !== undefined) document.getElementById('kpiTotalScan').innerText = data.totalScan;
                if (data.totalMasuk !== undefined) document.getElementById('kpiTotalMasuk').innerText = data.totalMasuk;
                if (data.totalKeluar !== undefined) document.getElementById('kpiTotalKeluar').innerText = data.totalKeluar;
                if (data.totalDiterima !== undefined) document.getElementById('kpiTotalDiterima').innerText = data.totalDiterima;
                if (data.totalDitolak !== undefined) document.getElementById('kpiTotalDitolak').innerText = data.totalDitolak;

                // Update Chart
                if (data.chartLabels && data.chartValues) {
                    initChart(data.chartLabels, data.chartValues);
                }

                // Update Export Links
                const urlObj = new URL(url, window.location.origin);
                const queryStr = urlObj.search;
                const btnExcel = document.getElementById('btnExportExcel');
                const btnPdf = document.getElementById('btnExportPdf');
                if (btnExcel) btnExcel.href = `{{ route('administrator.laporan-aktivitas.export.excel') }}${queryStr}`;
                if (btnPdf) btnPdf.href = `{{ route('administrator.laporan-aktivitas.export.pdf') }}${queryStr}`;

                // Update Browser URL without reload
                window.history.pushState(null, '', url);
            })
            .catch(err => {
                if (container) container.style.opacity = '1';
                console.error('SPA Fetch Error:', err);
            });
        }

        // SPA Filter Form Submit Event Listener
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            const targetUrl = `${this.action}?${params.toString()}`;
            fetchSpaData(targetUrl);
        });

        // SPA Pagination Clicks Listener
        document.getElementById('tableContainer').addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && (link.href.includes('page=') || link.href.includes('laporan-aktivitas'))) {
                e.preventDefault();
                fetchSpaData(link.href);
            }
        });

        // Reset Filter SPA
        function resetFilterSpa(e) {
            e.preventDefault();
            const form = document.getElementById('filterForm');
            if (form) {
                form.reset();
                const defaultUrl = "{{ route('administrator.laporan-aktivitas.index') }}";
                fetchSpaData(defaultUrl);
            }
        }

        // Handle Browser Back/Forward buttons
        window.addEventListener('popstate', function() {
            fetchSpaData(window.location.href);
        });
    </script>
</x-app-layout>
