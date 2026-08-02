<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Monitoring Kuota PAS</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 p-4 rounded-lg mb-4 text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif

    @php
        $totalKuotaAll    = $instansis->sum('kuota');
        $totalAktifAll    = $instansis->sum('kartu_aktif');
        $totalNonaktifAll = $instansis->sum('kartu_nonaktif');
        $totalSisaAll     = $instansis->sum('sisa_kuota');
        $totalPersenAll   = $totalKuotaAll > 0 ? round(min(($totalAktifAll / $totalKuotaAll) * 100, 100), 1) : 0;
    @endphp

    <!-- TOP KPI STAT CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Kuota Pemohon</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalKuotaAll) }}</h3>
                <p class="text-xs text-gray-400 mt-1">Alokasi seluruh instansi</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-boxes-stacked"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kartu PAS Aktif</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalAktifAll) }}</h3>
                <p class="text-xs text-emerald-600 mt-1 font-medium">Terpakai di lapangan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-id-card text-emerald-600"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sisa Kuota Pemohon</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalSisaAll) }}</h3>
                <p class="text-xs text-indigo-600 mt-1 font-medium">Siap untuk dialokasikan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-ticket-simple"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tingkat Penggunaan</p>
                <h3 class="text-2xl font-bold {{ $totalPersenAll >= 90 ? 'text-rose-600' : ($totalPersenAll >= 75 ? 'text-amber-500' : 'text-blue-600') }} mt-1">
                    {{ $totalPersenAll }}%
                </h3>
                <div class="w-24 bg-gray-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="{{ $totalPersenAll >= 90 ? 'bg-rose-500' : ($totalPersenAll >= 75 ? 'bg-amber-500' : 'bg-blue-600') }} h-1.5 rounded-full" style="width: {{ $totalPersenAll }}%"></div>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- BAR CHART: PERBANDINGAN KUOTA PER INSTANSI -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-blue-600"></i> Perbandingan Kuota PAS per Instansi
                    </h3>
                    <p class="text-xs text-gray-400">Total Kuota vs Kartu Aktif vs Sisa Kuota</p>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="chartKuotaBar"></canvas>
            </div>
        </div>

        <!-- DOUGHNUT CHART: DISTRIBUSI PENGGUNAAN KUOTA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-base text-gray-800 flex items-center gap-2 mb-1">
                    <i class="fas fa-chart-donut text-indigo-600"></i> Distribusi Kuota Pemohon
                </h3>
                <p class="text-xs text-gray-400 mb-4">Proporsi Status Alokasi Kuota</p>
                <div class="h-52 relative flex items-center justify-center">
                    <canvas id="chartKuotaDoughnut"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-3 text-center border-t pt-3 mt-2 text-xs">
                <div>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1"></span>
                    <span class="text-gray-500">Aktif</span>
                    <p class="font-bold text-gray-800">{{ $totalAktifAll }}</p>
                </div>
                <div>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-indigo-500 mr-1"></span>
                    <span class="text-gray-500">Sisa</span>
                    <p class="font-bold text-gray-800">{{ $totalSisaAll }}</p>
                </div>
                <div>
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-400 mr-1"></span>
                    <span class="text-gray-500">Nonaktif</span>
                    <p class="font-bold text-gray-800">{{ $totalNonaktifAll }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL MONITORING KUOTA PER INSTANSI -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                <i class="fas fa-building text-blue-600"></i> Tabel Monitoring Kuota per Instansi
            </h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg font-medium">
                Total: {{ count($instansis) }} Instansi Terdaftar
            </span>
        </div>

        @if($instansis->isEmpty())
            <p class="text-gray-500 text-center py-8">Belum ada data instansi.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-3 text-left font-semibold text-gray-700">Instansi / Perusahaan</th>
                            <th class="p-3 text-center font-semibold text-gray-700">Total Kuota</th>
                            <th class="p-3 text-center font-semibold text-gray-700">Kartu Aktif</th>
                            <th class="p-3 text-center font-semibold text-gray-700">Sisa Kuota</th>
                            <th class="p-3 text-center font-semibold text-gray-700">Nonaktif</th>
                            <th class="p-3 font-semibold text-gray-700" style="min-width: 160px;">Persentase Pemakaian</th>
                            <th class="p-3 text-center font-semibold text-gray-700">Set Kuota</th>
                            <th class="p-3 text-center font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instansis as $instansi)
                        @php
                            $persen    = $instansi->kuota > 0 ? min(($instansi->kartu_aktif / $instansi->kuota) * 100, 100) : 0;
                            $warnaBar  = $persen >= 90 ? 'bg-rose-500' : ($persen >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                            $warnaText = $persen >= 90 ? 'text-rose-600' : ($persen >= 75 ? 'text-amber-600' : 'text-emerald-600');
                        @endphp
                        <tr class="border-b hover:bg-gray-50/80 transition">
                            <td class="p-3">
                                <p class="font-bold text-gray-800">{{ $instansi->nama_instansi }}</p>
                                <p class="text-xs text-gray-400 truncate max-w-xs">{{ $instansi->alamat ?? '-' }}</p>
                            </td>
                            <td class="p-3 text-center">
                                <span class="text-base font-bold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $instansi->kuota }}</span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="text-base font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">{{ $instansi->kartu_aktif }}</span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="text-base font-bold {{ $instansi->sisa_kuota <= 0 ? 'text-rose-600 bg-rose-50' : 'text-indigo-600 bg-indigo-50' }} px-2.5 py-1 rounded-lg">
                                    {{ $instansi->sisa_kuota }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="text-base font-semibold text-gray-500">{{ $instansi->kartu_nonaktif }}</span>
                            </td>
                            <td class="p-3">
                                <div class="w-full bg-gray-100 rounded-full h-2 mb-1 overflow-hidden">
                                    <div class="{{ $warnaBar }} h-2 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-semibold {{ $warnaText }}">{{ round($persen, 1) }}%</span>
                                    <span class="text-gray-400 text-[10px]">{{ $instansi->kartu_aktif }} / {{ $instansi->kuota }}</span>
                                </div>
                            </td>
                            <td class="p-3 text-center">
                                <form method="POST" action="{{ route('administrator.monitoring-kuota.update-kuota', $instansi->id) }}" class="flex items-center gap-1 justify-center">
                                    @csrf @method('PUT')
                                    <input type="number" name="kuota" value="{{ $instansi->kuota }}"
                                           class="w-16 border-gray-300 rounded-lg text-xs text-center py-1 font-semibold focus:ring-blue-500" min="0">
                                    <button type="submit"
                                            class="bg-blue-600 text-white px-2.5 py-1 rounded-lg text-xs font-semibold hover:bg-blue-700 shadow-sm transition">
                                        Set
                                    </button>
                                </form>
                            </td>
                            <td class="p-3 text-center">
                                <!-- Tombol Detail SPA Modal -->
                                <button type="button" onclick="openModalDetailInstansi({{ $instansi->id }})"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm flex items-center gap-1.5 mx-auto">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- SPA MODAL DETAIL INSTANSI & DAFTAR KARTU PAS -->
    <div id="modalDetailInstansi" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full p-6 relative max-h-[92vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-4 border-b">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2" id="detail_nama_instansi">
                        <i class="fas fa-building text-blue-600"></i> Detail Instansi
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="detail_alamat_instansi">-</p>
                </div>
                <button type="button" onclick="closeModalDetailInstansi()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Stats Pill Summary -->
            <div class="grid grid-cols-4 gap-3 my-4">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500 font-medium">Total Kuota</p>
                    <p class="text-xl font-bold text-gray-800 mt-0.5" id="detail_total_kuota">0</p>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-emerald-700 font-medium">Kartu Aktif</p>
                    <p class="text-xl font-bold text-emerald-600 mt-0.5" id="detail_kartu_aktif">0</p>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-indigo-700 font-medium">Sisa Kuota</p>
                    <p class="text-xl font-bold text-indigo-600 mt-0.5" id="detail_sisa_kuota">0</p>
                </div>
                <div class="bg-gray-100 border border-gray-200 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-600 font-medium">Nonaktif</p>
                    <p class="text-xl font-bold text-gray-600 mt-0.5" id="detail_kartu_nonaktif">0</p>
                </div>
            </div>

            <!-- Search Filter Bar -->
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-bold text-sm text-gray-700 flex items-center gap-1.5">
                    <i class="fas fa-id-card text-gray-400"></i> Daftar Kartu PAS Terdaftar
                </h4>
                <div class="w-64">
                    <input type="text" id="inputSearchDetail" onkeyup="filterTableDetail()" placeholder="Cari nama / no. kartu..."
                           class="w-full border-gray-300 rounded-lg text-xs py-1.5 px-3">
                </div>
            </div>

            <!-- Table Container (Scrollable) -->
            <div class="overflow-y-auto grow border rounded-xl" style="max-h: 380px;">
                <table class="w-full text-left text-xs border-collapse" id="tableDetailKartu">
                    <thead class="bg-gray-100 border-b sticky top-0">
                        <tr>
                            <th class="p-2.5 font-semibold text-gray-700">No. Kartu</th>
                            <th class="p-2.5 font-semibold text-gray-700">Nama Pemegang</th>
                            <th class="p-2.5 font-semibold text-gray-700">Area Akses</th>
                            <th class="p-2.5 font-semibold text-gray-700">Masa Berlaku</th>
                            <th class="p-2.5 font-semibold text-gray-700">Status</th>
                            <th class="p-2.5 font-semibold text-gray-700">Keterangan</th>
                            <th class="p-2.5 font-semibold text-gray-700 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDetailKartu" class="divide-y divide-gray-100">
                        <!-- Loaded via AJAX -->
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-4 border-t mt-4">
                <button type="button" onclick="closeModalDetailInstansi()"
                        class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-xs font-semibold">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP NONAKTIFKAN KARTU PAS -->
    <div id="modalNonaktifkan" class="fixed inset-0 z-[70] hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 relative">
            <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-slash text-rose-600"></i> Nonaktifkan Kartu PAS
            </h3>
            <form method="POST" id="formNonaktifModal" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alasan Penonaktifan</label>
                    <select name="keterangan_nonaktif" class="block w-full border-gray-300 rounded-lg shadow-sm text-xs" required>
                        <option value="">-- Pilih Alasan --</option>
                        <option value="resign">Resign / Resign Pegawai</option>
                        <option value="pensiun">Pensiun / Masa Tugas Selesai</option>
                        <option value="meninggal">Meninggal Dunia</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="catatan_nonaktif"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-xs"
                           placeholder="Catatan detail penonaktifan...">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModalNonaktifkan()"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 text-xs font-medium">Batal</button>
                    <button type="submit"
                            class="bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700 text-xs font-bold shadow-md">Nonaktifkan Kartu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CHART.JS SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk Chart dari Laravel
            const instansiData = @json($instansis);

            const labels     = instansiData.map(i => i.nama_instansi);
            const totalKuota = instansiData.map(i => i.kuota);
            const kartuAktif = instansiData.map(i => i.kartu_aktif);
            const sisaKuota  = instansiData.map(i => i.sisa_kuota);

            // 1. BAR CHART: Perbandingan Kuota per Instansi
            const ctxBar = document.getElementById('chartKuotaBar').getContext('2d');
            new Chart(ctxBar, {
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
                        legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. DOUGHNUT CHART: Distribusi Kuota Bandara
            const ctxDoughnut = document.getElementById('chartKuotaDoughnut').getContext('2d');
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['Kartu Aktif', 'Sisa Kuota', 'Nonaktif'],
                    datasets: [{
                        data: [{{ $totalAktifAll }}, {{ $totalSisaAll }}, {{ $totalNonaktifAll }}],
                        backgroundColor: ['#10b981', '#6366f1', '#9ca3af'],
                        borderWidth: 2,
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

        // SPA MODAL DETAIL INSTANSI AJAX
        let currentDetailId = null;

        async function openModalDetailInstansi(id) {
            currentDetailId = id;
            const modal = document.getElementById('modalDetailInstansi');
            const tbody = document.getElementById('tbodyDetailKartu');
            
            modal.classList.remove('hidden');
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i> Memuat data instansi...</td></tr>';

            try {
                const response = await fetch(`{{ url('/administrator/monitoring-kuota') }}/${id}/detail-ajax`);
                const result = await response.json();

                if (result.success) {
                    const inst = result.instansi;
                    document.getElementById('detail_nama_instansi').innerHTML = `<i class="fas fa-building text-blue-600"></i> ${inst.nama_instansi}`;
                    document.getElementById('detail_alamat_instansi').textContent = inst.alamat || '-';
                    document.getElementById('detail_total_kuota').textContent = inst.kuota;
                    document.getElementById('detail_kartu_aktif').textContent = inst.kartu_aktif;
                    document.getElementById('detail_sisa_kuota').textContent = inst.sisa_kuota;
                    document.getElementById('detail_kartu_nonaktif').textContent = inst.nonaktif;

                    renderTableDetail(result.kartu_pas);
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-500">Gagal memuat data instansi.</td></tr>';
                }
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-500">Terjadi kesalahan koneksi.</td></tr>';
            }
        }

        function renderTableDetail(items) {
            const tbody = document.getElementById('tbodyDetailKartu');
            if (!items || items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400">Belum ada data kartu PAS terdaftar untuk instansi ini.</td></tr>';
                return;
            }

            let html = '';
            items.forEach(k => {
                const badgeClass = k.status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : (k.status === 'kadaluarsa' ? 'bg-rose-100 text-rose-700' : 'bg-gray-100 text-gray-700');
                const statusLabel = k.status.charAt(0).toUpperCase() + k.status.slice(1).replace('_', ' ');

                let ketHtml = '-';
                if (k.keterangan_nonaktif) {
                    ketHtml = `<span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-700">${k.keterangan_nonaktif}</span>`;
                    if (k.catatan_nonaktif) {
                        ketHtml += `<p class="text-[10px] text-gray-400 mt-0.5">${k.catatan_nonaktif}</p>`;
                    }
                }

                let btnNonaktif = '-';
                if (k.status === 'aktif') {
                    btnNonaktif = `<button onclick="openModalNonaktifkan(${k.id})" class="bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 rounded text-[11px] font-semibold shadow-sm transition">Nonaktifkan</button>`;
                }

                html += `
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="p-2.5 font-bold text-gray-800">${k.nomor_kartu}</td>
                        <td class="p-2.5 font-medium">${k.nama_pemegang}</td>
                        <td class="p-2.5 text-gray-600">${k.area_akses}</td>
                        <td class="p-2.5 text-gray-600">${k.tanggal_berlaku}</td>
                        <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[10px] font-bold ${badgeClass}">${statusLabel}</span></td>
                        <td class="p-2.5">${ketHtml}</td>
                        <td class="p-2.5 text-center">${btnNonaktif}</td>
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