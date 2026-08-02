<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Laporan Aktivitas Area Akses</h2>
    </x-slot>

    <!-- Filter Card Header -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
        <form method="GET" action="{{ route('administrator.laporan-aktivitas.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                </div>
                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                </div>
                <!-- Filter Area Akses -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Area Akses</label>
                    <select name="kode_area" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        <option value="">-- Semua Area --</option>
                        @foreach($areaAksesList as $area)
                            <option value="{{ $area->kode }}" {{ $kodeArea == $area->kode ? 'selected' : '' }}>
                                Area {{ $area->kode }} - {{ \Illuminate\Support\Str::limit($area->keterangan, 25) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Filter Status Akses -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Status Akses</label>
                    <select name="status_akses" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="diterima" {{ $statusAkses == 'diterima' ? 'selected' : '' }}>Diterima (Berhasil)</option>
                        <option value="ditolak" {{ $statusAkses == 'ditolak' ? 'selected' : '' }}>Ditolak (Gagal)</option>
                    </select>
                </div>
                <!-- Search Input -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Cari Nama / No. Kartu</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Kata kunci..." class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t flex-wrap gap-2">
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm transition flex items-center gap-1.5">
                        <i class="fas fa-search"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('administrator.laporan-aktivitas.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Reset
                    </a>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('administrator.laporan-aktivitas.export.excel', request()->all()) }}"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2 transition">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('administrator.laporan-aktivitas.export.pdf', request()->all()) }}"
                       class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2 transition">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Ringkasan KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Scan Aktivitas</p>
                <p class="text-2xl font-black text-blue-600 mt-0.5">{{ number_format($totalScan) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-walking"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Akses Diterima (Berhasil)</p>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ number_format($totalDiterima) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-rose-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Akses Ditolak (Pelanggaran)</p>
                <p class="text-2xl font-black text-rose-600 mt-0.5">{{ number_format($totalDitolak) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Tingkat Keberhasilan</p>
                <p class="text-2xl font-black text-purple-600 mt-0.5">
                    {{ $totalScan > 0 ? number_format(($totalDiterima / $totalScan) * 100, 1) : 0 }}%
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>

    <!-- Chart Row & Log Table -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        
        <!-- Log Table -->
        <div class="lg:col-span-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                    <i class="fas fa-list-alt text-blue-600"></i> Riwayat Aktivitas Scan Masuk / Keluar
                </h3>
                <span class="text-xs text-gray-500">Menampilkan {{ $scanLogs->firstItem() ?? 0 }} - {{ $scanLogs->lastItem() ?? 0 }} dari {{ $scanLogs->total() }} data</span>
            </div>

            @if($scanLogs->isEmpty())
                <p class="text-gray-500 text-center py-12">Tidak ada data aktivitas scan ditemukan untuk kriteria filter ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                                <th class="p-3">Waktu Scan</th>
                                <th class="p-3">Perangkat Kamera & Area</th>
                                <th class="p-3">No. Kartu PAS</th>
                                <th class="p-3">Pemegang & Perusahaan</th>
                                <th class="p-3 text-center">Status</th>
                                <th class="p-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($scanLogs as $log)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3 font-mono text-xs text-gray-600">
                                    <div class="font-bold text-gray-800">{{ $log->waktu_scan->format('d/m/Y') }}</div>
                                    <div>{{ $log->waktu_scan->format('H:i:s') }} WITA</div>
                                </td>
                                <td class="p-3">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-bold mr-1">Area {{ $log->kode_area }}</span>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ optional($log->cameraDevice)->nama_kamera ?? 'Kamera Station' }}</p>
                                </td>
                                <td class="p-3 font-mono font-bold text-purple-700">
                                    {{ $log->nomor_kartu }}
                                </td>
                                <td class="p-3">
                                    <p class="font-bold text-gray-800">{{ $log->nama_pemegang }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->perusahaan }}</p>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-extrabold uppercase {{ $log->status_akses === 'diterima' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-rose-100 text-rose-700 border border-rose-300' }}">
                                        {{ $log->status_akses }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    {{ $log->alasan }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $scanLogs->links() }}
                </div>
            @endif
        </div>

        <!-- Chart Side Column -->
        <div class="lg:col-span-4 bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex flex-col">
            <div class="mb-4">
                <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-600"></i> Distribusi Scan per Area
                </h3>
                <p class="text-xs text-gray-500">Frekuensi lintas lalu-lintas per titik area akses</p>
            </div>
            <div class="relative flex-1 flex items-center justify-center min-h-[250px]">
                <canvas id="chartAktivitasArea"></canvas>
            </div>
        </div>

    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const areaLabels = @json($chartDataArea->keys());
        const areaTotals = @json($chartDataArea->values());

        const ctxArea = document.getElementById('chartAktivitasArea').getContext('2d');
        new Chart(ctxArea, {
            type: 'doughnut',
            data: {
                labels: areaLabels.map(a => 'Area ' + a),
                datasets: [{
                    data: areaTotals,
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
    </script>
</x-app-layout>
