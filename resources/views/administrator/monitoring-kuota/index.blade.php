<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs border border-indigo-100">
                <i class="fas fa-chart-pie"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Monitoring Kuota PAS
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">Pantau alokasi dan tingkat pemakaian kuota kartu PAS per instansi</p>
            </div>
        </div>
    </x-slot>

    @php
        $totalKuotaAll    = $allInstansis->sum('kuota');
        $totalAktifAll    = $allInstansis->sum('kartu_aktif');
        $totalNonaktifAll = $allInstansis->sum('kartu_nonaktif');
        $totalSisaAll     = $allInstansis->sum('sisa_kuota');
        $totalPersenAll   = $totalKuotaAll > 0 ? round(min(($totalAktifAll / $totalKuotaAll) * 100, 100), 1) : 0;
    @endphp

    {{-- KPI STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Kuota --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Kuota</p>
                <h3 id="kpiTotalKuota" class="text-2xl font-black text-slate-800 mt-1 tracking-tight">{{ number_format($totalKuotaAll) }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">Alokasi seluruh instansi</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-boxes-stacked"></i>
            </div>
        </div>

        {{-- Kartu Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kartu PAS Aktif</p>
                <h3 id="kpiTotalAktif" class="text-2xl font-black text-emerald-600 mt-1 tracking-tight">{{ number_format($totalAktifAll) }}</h3>
                <p class="text-[11px] text-emerald-500 mt-1 font-medium">Terpakai di lapangan</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-id-card"></i>
            </div>
        </div>

        {{-- Sisa Kuota --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sisa Kuota</p>
                <h3 id="kpiTotalSisa" class="text-2xl font-black text-indigo-600 mt-1 tracking-tight">{{ number_format($totalSisaAll) }}</h3>
                <p class="text-[11px] text-indigo-500 mt-1 font-medium">Siap dialokasikan</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-ticket-simple"></i>
            </div>
        </div>

        {{-- Tingkat Penggunaan --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div class="w-full">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tingkat Penggunaan</p>
                <h3 id="kpiTotalPersen" class="text-2xl font-black {{ $totalPersenAll >= 90 ? 'text-rose-600' : ($totalPersenAll >= 75 ? 'text-amber-500' : 'text-blue-600') }} mt-1 tracking-tight">
                    {{ $totalPersenAll }}%
                </h3>
                <div class="w-full max-w-[120px] bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div id="kpiProgressBar" class="{{ $totalPersenAll >= 90 ? 'bg-rose-500' : ($totalPersenAll >= 75 ? 'bg-amber-500' : 'bg-blue-600') }} h-1.5 rounded-full transition-all duration-700" style="width: {{ $totalPersenAll }}%"></div>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- BAR CHART --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-2xs">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-800 tracking-tight">Perbandingan Kuota per Instansi</h3>
                        <p class="text-[11px] text-slate-400">Total Kuota vs Kartu Aktif vs Sisa Kuota</p>
                    </div>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="chartKuotaBar"></canvas>
            </div>
        </div>

        {{-- DOUGHNUT CHART --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-2xs">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-800 tracking-tight">Distribusi Kuota</h3>
                        <p class="text-[11px] text-slate-400">Proporsi Status Alokasi</p>
                    </div>
                </div>
                <div class="h-52 relative flex items-center justify-center">
                    <canvas id="chartKuotaDoughnut"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-3 text-center border-t border-slate-100 pt-3 mt-2 text-xs">
                <div>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1"></span>
                    <span class="text-slate-500">Aktif</span>
                    <p id="doughnutStatAktif" class="font-bold text-slate-800">{{ $totalAktifAll }}</p>
                </div>
                <div>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-indigo-500 mr-1"></span>
                    <span class="text-slate-500">Sisa</span>
                    <p id="doughnutStatSisa" class="font-bold text-slate-800">{{ $totalSisaAll }}</p>
                </div>
                <div>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-slate-400 mr-1"></span>
                    <span class="text-slate-500">Nonaktif</span>
                    <p id="doughnutStatNonaktif" class="font-bold text-slate-800">{{ $totalNonaktifAll }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL MONITORING KUOTA PER INSTANSI (dengan x-table-card & SPA Partial) --}}
    <x-table-card title="Monitoring Kuota per Instansi"
                  subtitle="Kelola alokasi kuota dan pantau pemakaian kartu PAS per perusahaan"
                  icon="fas fa-building"
                  iconColor="bg-blue-50 text-blue-600">

        {{-- Actions Slot --}}
        <x-slot name="actions">
            <span class="text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl font-semibold border border-slate-200/80">
                <i class="fas fa-database mr-1 text-slate-400"></i> Total: <strong id="totalInstansiBadge" class="text-blue-600">{{ $instansis->total() }}</strong> Instansi
            </span>
        </x-slot>

        {{-- Filter Slot --}}
        <x-slot name="filters">
            <form id="filterForm" method="GET" action="{{ route('administrator.monitoring-kuota.index') }}" class="flex flex-wrap items-end gap-3">
                {{-- Search --}}
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="searchInput" name="search" value="{{ $search }}"
                               placeholder="Cari nama instansi / perusahaan..."
                               class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs">
                        <button type="button" id="btnClearSearch" onclick="clearSearchInput()"
                                class="{{ $search ? '' : 'hidden' }} absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-500 text-xs transition cursor-pointer"
                                title="Hapus pencarian">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>

                {{-- Per Page --}}
                <div class="w-28">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Per Halaman</label>
                    <select id="perPageSelect" name="per_page"
                            class="w-full py-2 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-2xs cursor-pointer">
                        @foreach([10, 15, 25, 50] as $pp)
                            <option value="{{ $pp }}" {{ request('per_page', 10) == $pp ? 'selected' : '' }}>{{ $pp }} data</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Button --}}
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-xs transition-all cursor-pointer">
                    <i class="fas fa-filter"></i> Filter
                </button>

                <button type="button" onclick="resetFilterSpa(event)"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all cursor-pointer border border-slate-200/80"
                        title="Reset Filter">
                    <i class="fas fa-rotate-left"></i> Reset
                </button>
            </form>
        </x-slot>

        <!-- Table Viewport with Persistent Loading Overlay (SPA Replaced via AJAX) -->
        <div class="relative">
            <!-- Shimmer Loading Overlay -->
            <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 hidden items-center justify-center transition-all duration-200">
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/90 text-white shadow-2xl text-xs font-bold border border-slate-700 backdrop-blur-md">
                    <i class="fas fa-circle-notch fa-spin text-blue-400 text-base"></i>
                    <span>Memperbarui data kuota...</span>
                </div>
            </div>

            <!-- Table Container (SPA HTML injected here) -->
            <div id="tableContainer" class="transition-opacity duration-200">
                @include('administrator.monitoring-kuota.partials.table')
            </div>
        </div>

    </x-table-card>

    {{-- SPA MODAL DETAIL INSTANSI & DAFTAR KARTU PAS --}}
    <div id="modalDetailInstansi" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full p-6 relative max-h-[92vh] flex flex-col border border-slate-200/80">
            {{-- Modal Header --}}
            <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shadow-2xs">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight" id="detail_nama_instansi">Detail Instansi</h3>
                        <p class="text-xs text-slate-400 mt-0.5" id="detail_alamat_instansi">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalDetailInstansi()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Stats Pills --}}
            <div class="grid grid-cols-4 gap-3 my-4">
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 text-center">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Total Kuota</p>
                    <p class="text-xl font-black text-slate-800 mt-0.5" id="detail_total_kuota">0</p>
                </div>
                <div class="bg-emerald-50/60 border border-emerald-200/80 rounded-xl p-3 text-center">
                    <p class="text-[11px] text-emerald-700 font-bold uppercase tracking-wider">Kartu Aktif</p>
                    <p class="text-xl font-black text-emerald-600 mt-0.5" id="detail_kartu_aktif">0</p>
                </div>
                <div class="bg-indigo-50/60 border border-indigo-200/80 rounded-xl p-3 text-center">
                    <p class="text-[11px] text-indigo-700 font-bold uppercase tracking-wider">Sisa Kuota</p>
                    <p class="text-xl font-black text-indigo-600 mt-0.5" id="detail_sisa_kuota">0</p>
                </div>
                <div class="bg-slate-100/60 border border-slate-200/80 rounded-xl p-3 text-center">
                    <p class="text-[11px] text-slate-600 font-bold uppercase tracking-wider">Nonaktif</p>
                    <p class="text-xl font-black text-slate-600 mt-0.5" id="detail_kartu_nonaktif">0</p>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-bold text-sm text-slate-700 flex items-center gap-1.5">
                    <i class="fas fa-id-card text-slate-400"></i> Daftar Kartu PAS Terdaftar
                </h4>
                <div class="w-64">
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-400 text-xs"><i class="fas fa-search"></i></span>
                        <input type="text" id="inputSearchDetail" onkeyup="filterTableDetail()" placeholder="Cari nama / no. kartu..."
                               class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition">
                    </div>
                </div>
            </div>

            {{-- Table Container --}}
            <div class="overflow-y-auto grow border border-slate-200/80 rounded-xl" style="max-height: 380px;">
                <table class="w-full text-left text-xs border-collapse" id="tableDetailKartu">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 sticky top-0">
                        <tr>
                            <th class="px-3 py-3 font-bold">No. Kartu</th>
                            <th class="px-3 py-3 font-bold">Nama Pemegang</th>
                            <th class="px-3 py-3 font-bold">Area Akses</th>
                            <th class="px-3 py-3 font-bold">Masa Berlaku</th>
                            <th class="px-3 py-3 font-bold">Status</th>
                            <th class="px-3 py-3 font-bold">Keterangan</th>
                            <th class="px-3 py-3 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDetailKartu" class="divide-y divide-slate-100 bg-white">
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Modal Footer --}}
            <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                <button type="button" onclick="closeModalDetailInstansi()"
                        class="px-5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL NONAKTIFKAN KARTU PAS --}}
    <div id="modalNonaktifkan" class="fixed inset-0 z-[70] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative border border-slate-200/80">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base shadow-2xs">
                    <i class="fas fa-user-slash"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-800 tracking-tight">Nonaktifkan Kartu PAS</h3>
            </div>
            <form method="POST" id="formNonaktifModal" action="" onsubmit="handleNonaktifkan(event, this)">
                @csrf
                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Alasan Penonaktifan</label>
                    <select name="keterangan_nonaktif" class="w-full border-slate-200 rounded-xl shadow-2xs text-xs focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Pilih Alasan --</option>
                        <option value="resign">Resign / Resign Pegawai</option>
                        <option value="pensiun">Pensiun / Masa Tugas Selesai</option>
                        <option value="meninggal">Meninggal Dunia</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="catatan_nonaktif"
                           class="w-full border-slate-200 rounded-xl shadow-2xs text-xs focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Catatan detail penonaktifan...">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModalNonaktifkan()"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-medium cursor-pointer transition">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs cursor-pointer transition">Nonaktifkan Kartu</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CHART.JS SCRIPT & SPA CONTROLLER --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chartKuotaBarInstance = null;
        let chartKuotaDoughnutInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk Chart — gunakan allInstansis (semua data, bukan pagination)
            const instansiData = @json($allInstansis);

            const labels     = instansiData.map(i => i.nama_instansi);
            const totalKuota = instansiData.map(i => i.kuota);
            const kartuAktif = instansiData.map(i => i.kartu_aktif);
            const sisaKuota  = instansiData.map(i => i.sisa_kuota);

            // BAR CHART
            const ctxBar = document.getElementById('chartKuotaBar').getContext('2d');
            chartKuotaBarInstance = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Kuota',
                            data: totalKuota,
                            backgroundColor: '#3b82f6',
                            borderRadius: 6,
                        },
                        {
                            label: 'Kartu Aktif',
                            data: kartuAktif,
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                        },
                        {
                            label: 'Sisa Kuota',
                            data: sisaKuota,
                            backgroundColor: '#6366f1',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11, weight: 'bold' },
                                usePointStyle: true,
                                padding: 16
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 9 },
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    }
                }
            });

            // DOUGHNUT CHART
            const ctxDoughnut = document.getElementById('chartKuotaDoughnut').getContext('2d');
            chartKuotaDoughnutInstance = new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['Kartu Aktif', 'Sisa Kuota', 'Nonaktif'],
                    datasets: [{
                        data: [{{ $totalAktifAll }}, {{ $totalSisaAll }}, {{ $totalNonaktifAll }}],
                        backgroundColor: ['#10b981', '#6366f1', '#94a3b8'],
                        borderWidth: 3,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });

        // Update Charts Data dynamically
        function updateCharts(chartData) {
            if (chartKuotaBarInstance) {
                chartKuotaBarInstance.data.labels = chartData.labels;
                chartKuotaBarInstance.data.datasets[0].data = chartData.totalKuota;
                chartKuotaBarInstance.data.datasets[1].data = chartData.kartuAktif;
                chartKuotaBarInstance.data.datasets[2].data = chartData.sisaKuota;
                chartKuotaBarInstance.update();
            }
            if (chartKuotaDoughnutInstance) {
                chartKuotaDoughnutInstance.data.datasets[0].data = [
                    chartData.totalAktifAll,
                    chartData.totalSisaAll,
                    chartData.totalNonaktifAll
                ];
                chartKuotaDoughnutInstance.update();
            }
            const elAktif = document.getElementById('doughnutStatAktif');
            const elSisa = document.getElementById('doughnutStatSisa');
            const elNonaktif = document.getElementById('doughnutStatNonaktif');
            if (elAktif) elAktif.innerText = chartData.totalAktifAll;
            if (elSisa) elSisa.innerText = chartData.totalSisaAll;
            if (elNonaktif) elNonaktif.innerText = chartData.totalNonaktifAll;
        }

        // Update KPI Cards dynamically
        function updateKpis(kpi) {
            const elTotalKuota = document.getElementById('kpiTotalKuota');
            const elTotalAktif = document.getElementById('kpiTotalAktif');
            const elTotalSisa = document.getElementById('kpiTotalSisa');
            const elTotalPersen = document.getElementById('kpiTotalPersen');
            const elProgressBar = document.getElementById('kpiProgressBar');

            if (elTotalKuota) elTotalKuota.innerText = kpi.totalKuota;
            if (elTotalAktif) elTotalAktif.innerText = kpi.totalAktif;
            if (elTotalSisa) elTotalSisa.innerText = kpi.totalSisa;
            if (elTotalPersen) {
                elTotalPersen.innerText = kpi.totalPersen + '%';
                const colorClass = kpi.totalPersen >= 90 ? 'text-rose-600' : (kpi.totalPersen >= 75 ? 'text-amber-500' : 'text-blue-600');
                elTotalPersen.className = `text-2xl font-black ${colorClass} mt-1 tracking-tight`;
            }
            if (elProgressBar) {
                elProgressBar.style.width = kpi.totalPersen + '%';
                const barBgClass = kpi.totalPersen >= 90 ? 'bg-rose-500' : (kpi.totalPersen >= 75 ? 'bg-amber-500' : 'bg-blue-600');
                elProgressBar.className = `${barBgClass} h-1.5 rounded-full transition-all duration-700`;
            }
        }

        // Loading Overlay controls
        function showTableLoading() {
            const overlay = document.getElementById('tableLoadingOverlay');
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
            const overlay = document.getElementById('tableLoadingOverlay');
            const container = document.getElementById('tableContainer');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
            if (container) {
                container.classList.remove('opacity-40', 'pointer-events-none');
            }
        }

        // SPA Navigation & Data Fetching Handler
        async function loadTableData(url, pushState = true) {
            const container = document.getElementById('tableContainer');
            if (!container) return;

            showTableLoading();

            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Network error');

                const contentType = res.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await res.json();
                    if (data.table_html) {
                        container.innerHTML = data.table_html;
                    }
                    if (data.total_instansi !== undefined) {
                        const totalBadge = document.getElementById('totalInstansiBadge');
                        if (totalBadge) totalBadge.innerText = data.total_instansi;
                    }
                    if (data.kpi) {
                        updateKpis(data.kpi);
                    }
                    if (data.chart) {
                        updateCharts(data.chart);
                    }
                } else {
                    const html = await res.text();
                    container.innerHTML = html;
                }

                if (pushState) {
                    history.pushState(null, '', url);
                }

                syncFormWithUrl(url);
            } catch (err) {
                console.error('SPA Navigation Error:', err);
                window.location.href = url;
            } finally {
                hideTableLoading();
            }
        }

        // Sync Form inputs with current URL
        function syncFormWithUrl(url) {
            try {
                const u = new URL(url, window.location.origin);
                const searchVal = u.searchParams.get('search') || '';
                const perPageVal = u.searchParams.get('per_page') || '10';

                const searchInput = document.getElementById('searchInput');
                const perPageSelect = document.getElementById('perPageSelect');
                const btnClear = document.getElementById('btnClearSearch');

                if (searchInput && searchInput.value !== searchVal) searchInput.value = searchVal;
                if (perPageSelect && perPageSelect.value !== perPageVal) perPageSelect.value = perPageVal;
                if (btnClear) {
                    if (searchVal.trim() !== '') {
                        btnClear.classList.remove('hidden');
                    } else {
                        btnClear.classList.add('hidden');
                    }
                }
            } catch(e) {}
        }

        // Intercept all SPA pagination links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[data-spa="true"], a.pagination-link');
            if (link && link.href) {
                e.preventDefault();
                loadTableData(link.href, true);
            }
        });

        // Browser Back / Forward History Navigation
        window.addEventListener('popstate', function() {
            loadTableData(window.location.href, false);
        });

        // SPA Filter Form Submit
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = new URL(filterForm.action, window.location.origin);
                const formData = new FormData(filterForm);
                for (const [key, val] of formData.entries()) {
                    if (val) {
                        url.searchParams.set(key, val);
                    }
                }
                // Reset to page 1 on new filter
                url.searchParams.delete('page');
                loadTableData(url.toString(), true);
            });
        }

        // Search Input Debounce (350ms)
        let searchDebounceTimer;
        const searchInput = document.getElementById('searchInput');
        const btnClearSearch = document.getElementById('btnClearSearch');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (btnClearSearch) {
                    if (this.value.trim() !== '') {
                        btnClearSearch.classList.remove('hidden');
                    } else {
                        btnClearSearch.classList.add('hidden');
                    }
                }

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    if (filterForm) {
                        filterForm.dispatchEvent(new Event('submit'));
                    }
                }, 350);
            });
        }

        function clearSearchInput() {
            if (searchInput) {
                searchInput.value = '';
                if (btnClearSearch) btnClearSearch.classList.add('hidden');
                if (filterForm) filterForm.dispatchEvent(new Event('submit'));
            }
        }

        // Per Page Select Change triggers SPA reload
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                if (filterForm) {
                    filterForm.dispatchEvent(new Event('submit'));
                }
            });
        }

        // Reset Filter SPA
        function resetFilterSpa(e) {
            if (e) e.preventDefault();
            if (filterForm) {
                filterForm.reset();
                if (searchInput) searchInput.value = '';
                if (perPageSelect) perPageSelect.value = '10';
                if (btnClearSearch) btnClearSearch.classList.add('hidden');
                const defaultUrl = "{{ route('administrator.monitoring-kuota.index') }}";
                loadTableData(defaultUrl, true);
            }
        }

        // Inline Toast Notification helper
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-triangle-exclamation';
            toast.className = `fixed bottom-5 right-5 z-[100] flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-xl text-xs font-bold transition-all duration-300 transform translate-y-3 opacity-0 ${bgClass}`;
            toast.innerHTML = `<i class="fas ${icon} text-sm"></i> <span>${message}</span>`;
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-3', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-3');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Inline Set Kuota AJAX Handler
        async function handleUpdateKuota(e, form) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px]"></i>';

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    showToast('Kuota berhasil diupdate', 'success');
                    loadTableData(window.location.href, false);
                } else {
                    const errData = await res.json().catch(() => ({}));
                    showToast(errData.message || 'Gagal mengupdate kuota', 'error');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            } catch (err) {
                showToast('Terjadi kesalahan koneksi', 'error');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }

        // Modal Nonaktifkan AJAX Handler
        async function handleNonaktifkan(e, form) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Menonaktifkan...';

            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    closeModalNonaktifkan();
                    showToast('Kartu PAS berhasil dinonaktifkan', 'success');
                    if (currentDetailId) {
                        openModalDetailInstansi(currentDetailId);
                    }
                    loadTableData(window.location.href, false);
                } else {
                    showToast('Gagal menonaktifkan kartu', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan koneksi', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }

        // SPA MODAL DETAIL INSTANSI AJAX
        let currentDetailId = null;

        async function openModalDetailInstansi(id) {
            currentDetailId = id;
            const modal = document.getElementById('modalDetailInstansi');
            const tbody = document.getElementById('tbodyDetailKartu');

            modal.classList.remove('hidden');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-400"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data instansi...</td></tr>';

            try {
                const response = await fetch(`{{ url('/administrator/monitoring-kuota') }}/${id}/detail-ajax`);
                const result = await response.json();

                if (result.success) {
                    const inst = result.instansi;
                    document.getElementById('detail_nama_instansi').textContent = inst.nama_instansi;
                    document.getElementById('detail_alamat_instansi').textContent = inst.alamat || '-';
                    document.getElementById('detail_total_kuota').textContent = inst.kuota;
                    document.getElementById('detail_kartu_aktif').textContent = inst.kartu_aktif;
                    document.getElementById('detail_sisa_kuota').textContent = inst.sisa_kuota;
                    document.getElementById('detail_kartu_nonaktif').textContent = inst.nonaktif;

                    renderTableDetail(result.kartu_pas);
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-rose-500">Gagal memuat data instansi.</td></tr>';
                }
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-rose-500">Terjadi kesalahan koneksi.</td></tr>';
            }
        }

        function renderTableDetail(items) {
            const tbody = document.getElementById('tbodyDetailKartu');
            if (!items || items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-slate-400">Belum ada data kartu PAS terdaftar untuk instansi ini.</td></tr>';
                return;
            }

            let html = '';
            items.forEach(k => {
                const badgeClass = k.status === 'aktif'
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80'
                    : (k.status === 'kadaluarsa' ? 'bg-rose-50 text-rose-700 border-rose-200/80' : 'bg-slate-100 text-slate-600 border-slate-200/80');
                const statusLabel = k.status.charAt(0).toUpperCase() + k.status.slice(1).replace('_', ' ');

                let ketHtml = '<span class="text-slate-400">-</span>';
                if (k.keterangan_nonaktif) {
                    ketHtml = `<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">${k.keterangan_nonaktif}</span>`;
                    if (k.catatan_nonaktif) {
                        ketHtml += `<p class="text-[10px] text-slate-400 mt-0.5">${k.catatan_nonaktif}</p>`;
                    }
                }

                let btnNonaktif = '<span class="text-slate-400">-</span>';
                if (k.status === 'aktif') {
                    btnNonaktif = `<button onclick="openModalNonaktifkan(${k.id})" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition cursor-pointer"><i class="fas fa-ban text-[9px]"></i> Nonaktifkan</button>`;
                }

                html += `
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-3 py-2.5 font-mono font-bold text-slate-800 text-xs">${k.nomor_kartu}</td>
                        <td class="px-3 py-2.5 font-semibold text-slate-700 text-xs">${k.nama_pemegang}</td>
                        <td class="px-3 py-2.5"><span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">${k.area_akses}</span></td>
                        <td class="px-3 py-2.5 text-xs text-slate-600">${k.tanggal_berlaku}</td>
                        <td class="px-3 py-2.5"><span class="px-2 py-0.5 rounded-md text-[10px] font-bold border ${badgeClass}">${statusLabel}</span></td>
                        <td class="px-3 py-2.5">${ketHtml}</td>
                        <td class="px-3 py-2.5 text-center">${btnNonaktif}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function filterTableDetail() {
            const query = document.getElementById('inputSearchDetail').value.toLowerCase();
            const rows = document.querySelectorAll('#tbodyDetailKartu tr');
            rows.forEach(tr => {
                const text = tr.textContent.toLowerCase();
                tr.style.display = text.includes(query) ? '' : 'none';
            });
        }

        function closeModalDetailInstansi() {
            document.getElementById('modalDetailInstansi').classList.add('hidden');
        }

        function openModalNonaktifkan(id) {
            document.getElementById('formNonaktifModal').action = `{{ url('/administrator/monitoring-kuota/nonaktifkan') }}/${id}`;
            document.getElementById('modalNonaktifkan').classList.remove('hidden');
        }

        function closeModalNonaktifkan() {
            document.getElementById('modalNonaktifkan').classList.add('hidden');
        }
    </script>
</x-app-layout>