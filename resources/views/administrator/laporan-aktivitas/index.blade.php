<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-history text-blue-600"></i> Laporan Aktivitas Area Akses
        </h2>
    </x-slot>

    <!-- Filter Card Header -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
        <form method="GET" action="{{ route('administrator.laporan-aktivitas.index') }}" class="space-y-4">
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
                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Tipe Scan (Masuk/Keluar)</label>
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
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('administrator.laporan-aktivitas.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3.5 py-2 rounded-lg text-xs font-semibold transition">
                        Reset
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('administrator.laporan-aktivitas.export.excel', request()->all()) }}"
                       class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('administrator.laporan-aktivitas.export.pdf', request()->all()) }}"
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
                <p class="text-2xl font-black text-blue-600 mt-0.5">{{ number_format($totalScan) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-walking"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Scan Masuk (IN)</p>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ number_format($totalMasuk) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-sign-in-alt"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-amber-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Scan Keluar (OUT)</p>
                <p class="text-2xl font-black text-amber-600 mt-0.5">{{ number_format($totalKeluar) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-sign-out-alt"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-teal-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Akses Diterima</p>
                <p class="text-2xl font-black text-teal-600 mt-0.5">{{ number_format($totalDiterima) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-rose-500 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Akses Ditolak</p>
                <p class="text-2xl font-black text-rose-600 mt-0.5">{{ number_format($totalDitolak) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- Chart Row & Log Table -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        
        <!-- Log Table -->
        <div class="lg:col-span-8 bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-base text-gray-800 flex items-center gap-2">
                        <i class="fas fa-list-alt text-blue-600"></i> Riwayat Aktivitas Scan Masuk / Keluar
                    </h3>
                    <span class="text-xs text-gray-500 font-medium">Menampilkan {{ $scanLogs->firstItem() ?? 0 }} - {{ $scanLogs->lastItem() ?? 0 }} dari {{ $scanLogs->total() }} data</span>
                </div>

                @if($scanLogs->isEmpty())
                    <p class="text-gray-500 text-center py-12">Tidak ada data aktivitas scan ditemukan untuk kriteria filter ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-800 text-white uppercase tracking-wider text-[11px]">
                                    <th class="p-3">Waktu Scan</th>
                                    <th class="p-3">Perangkat Kamera & Area</th>
                                    <th class="p-3">No. Kartu PAS</th>
                                    <th class="p-3">Pemegang & Perusahaan</th>
                                    <th class="p-3 text-center">Aktivitas</th>
                                    <th class="p-3 text-center">Status</th>
                                    <th class="p-3">Keterangan / Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($scanLogs as $log)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-3 font-mono text-xs text-gray-600">
                                        <div class="font-bold text-gray-800 flex items-center gap-1">
                                            <i class="far fa-calendar-alt text-blue-500 text-[11px]"></i>
                                            {{ $log->waktu_scan->translatedFormat('l, d M Y') }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1">
                                            <i class="far fa-clock text-gray-400 text-[10px]"></i>
                                            {{ $log->waktu_scan->format('H:i:s') }} WIT
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[11px] font-bold inline-block mb-0.5">Area {{ $log->kode_area }}</span>
                                        <p class="text-[11px] text-gray-500">{{ optional($log->cameraDevice)->nama_kamera ?? 'Kamera Station' }}</p>
                                    </td>
                                    <td class="p-3 font-mono font-bold text-purple-700">
                                        {{ $log->nomor_kartu }}
                                    </td>
                                    <td class="p-3">
                                        <p class="font-bold text-gray-800 text-xs">{{ $log->nama_pemegang }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $log->perusahaan }}</p>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 rounded text-[10px] font-extrabold uppercase {{ $log->tipe_aktivitas === 'keluar' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300' }}">
                                            <i class="fas {{ $log->tipe_aktivitas === 'keluar' ? 'fa-sign-out-alt' : 'fa-sign-in-alt' }} mr-0.5"></i>
                                            {{ $log->tipe_aktivitas ?: 'masuk' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $log->status_akses === 'diterima' ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-rose-100 text-rose-700 border border-rose-300' }}">
                                            {{ $log->status_akses }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs text-gray-600">
                                        <div class="font-medium text-gray-800">{{ $log->alasan }}</div>
                                        @if($log->catatan)
                                            <div class="text-[11px] text-blue-700 mt-1 italic bg-blue-50/80 px-2 py-1 rounded border border-blue-200/80 inline-block">
                                                <i class="fas fa-sticky-note mr-1"></i> Catatan: {{ $log->catatan }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if(!$scanLogs->isEmpty())
                <div class="mt-4 pt-3 border-t border-gray-100">
                    {{ $scanLogs->links() }}
                </div>
            @endif
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
