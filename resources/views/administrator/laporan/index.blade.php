<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Laporan Kartu PAS</h2>
    </x-slot>

    <!-- Filter & Action Header -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('administrator.laporan.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Pilih Tahun Laporan</label>
                    <select name="tahun" class="border-gray-300 rounded-lg shadow-sm text-sm font-bold text-gray-800">
                        @foreach($tahunList as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>Tahun {{ $t }}</option>
                        @endforeach
                        @if(!$tahunList->contains(date('Y')))
                            <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>Tahun {{ date('Y') }}</option>
                        @endif
                    </select>
                </div>
                <button type="submit" class="self-end bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition">
                    <i class="fas fa-filter mr-1"></i> Filter Data
                </button>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('administrator.laporan.export.excel', ['tahun' => $tahun]) }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2 transition">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('administrator.laporan.export.pdf', ['tahun' => $tahun]) }}"
                   class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2 transition">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Ringkasan KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kartu Terbit ({{ $tahun }})</p>
                <p class="text-2xl font-black text-blue-600 mt-0.5">{{ number_format($totalKartuTerbit) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-id-card"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kartu PAS Aktif</p>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ number_format($totalKartuAktif) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-amber-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kartu Kadaluarsa</p>
                <p class="text-2xl font-black text-amber-600 mt-0.5">{{ number_format($totalKadaluarsa) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-rose-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kartu Nonaktif / Blokir</p>
                <p class="text-2xl font-black text-rose-600 mt-0.5">{{ number_format($totalNonaktif) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-ban"></i>
            </div>
        </div>
    </div>

    <!-- Visual Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Main Line Chart for Kartu PAS per Bulan -->
        <div class="lg:col-span-8 bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i> Tren Kartu PAS per Bulan (Tahun {{ $tahun }})
                    </h3>
                    <p class="text-xs text-gray-500">Statistik perkembangan kartu baru terbit & diperpanjang setiap bulan</p>
                </div>
            </div>
            <div class="relative h-[280px]">
                <canvas id="chartKartuPasTrend"></canvas>
            </div>
        </div>

        <!-- Donut Chart for Distribution per Instansi -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex flex-col">
            <div class="mb-4">
                <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-600"></i> Distribusi per Instansi
                </h3>
                <p class="text-xs text-gray-500">Proporsi penerbitan Kartu PAS per perusahaan</p>
            </div>
            <div class="relative flex-1 flex items-center justify-center min-h-[220px]">
                <canvas id="chartDistribusiInstansi"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Laporan Bulanan -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="font-bold text-base text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-table text-indigo-600"></i> Detail Laporan Kartu PAS Bulanan (Tahun {{ $tahun }})
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                        <th class="p-3">Bulan</th>
                        <th class="p-3 text-center">Kartu Baru Terbit</th>
                        <th class="p-3 text-center">Kartu Diperpanjang</th>
                        <th class="p-3 text-center">Kartu Kadaluarsa</th>
                        <th class="p-3 text-center">Total Terbit / Diperbarui</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($laporanKartu as $laporan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-bold text-gray-800">
                            {{ DateTime::createFromFormat('!m', $laporan->bulan)->format('F') }} {{ $laporan->tahun }}
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                {{ number_format($laporan->kartu_baru) }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">
                                {{ number_format($laporan->kartu_diperpanjang) }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                {{ number_format($laporan->kartu_kadaluarsa) }}
                            </span>
                        </td>
                        <td class="p-3 text-center font-bold text-slate-800">
                            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-800">
                                {{ number_format($laporan->total_terbit) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-black text-slate-900 border-t-2 border-slate-300">
                        <td class="p-3">TOTAL TAHUN {{ $tahun }}</td>
                        <td class="p-3 text-center text-blue-700">{{ number_format($laporanKartu->sum('kartu_baru')) }}</td>
                        <td class="p-3 text-center text-purple-700">{{ number_format($laporanKartu->sum('kartu_diperpanjang')) }}</td>
                        <td class="p-3 text-center text-amber-700">{{ number_format($laporanKartu->sum('kartu_kadaluarsa')) }}</td>
                        <td class="p-3 text-center text-slate-900">{{ number_format($laporanKartu->sum('total_terbit')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const bulanLabel        = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
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
                        backgroundColor: 'rgba(37, 99, 235, 0.1)', 
                        borderWidth: 3,
                        tension: 0.35, 
                        fill: true 
                    },
                    { 
                        label: 'Kartu Diperpanjang', 
                        data: bulanLabel.map((_, i) => dataDiperpanjang[i+1] ?? 0), 
                        borderColor: '#9333ea', 
                        backgroundColor: 'rgba(147, 51, 234, 0.1)', 
                        borderWidth: 3,
                        tension: 0.35, 
                        fill: true 
                    },
                    { 
                        label: 'Kadaluarsa', 
                        data: bulanLabel.map((_, i) => dataKadaluarsa[i+1] ?? 0), 
                        borderColor: '#d97706', 
                        backgroundColor: 'rgba(217, 119, 6, 0.05)', 
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.35
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'top', labels: { usePointStyle: true, font: { weight: 'bold' } } } 
                }, 
                scales: { 
                    y: { beginAtZero: true, ticks: { stepSize: 1 } } 
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
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                }
            }
        });
    </script>
</x-app-layout>