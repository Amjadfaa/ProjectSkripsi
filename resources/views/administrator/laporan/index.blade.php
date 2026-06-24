<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Laporan Bulanan</h2>
    </x-slot>

    <!-- Filter Tahun -->
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('administrator.laporan.index') }}" class="flex items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Pilih Tahun</label>
                <select name="tahun" class="border-gray-300 rounded-lg shadow-sm text-sm">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                    <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600">
                Tampilkan
            </button>
            <div class="flex gap-2 ml-auto">
                <a href="{{ route('administrator.laporan.export.excel', ['tahun' => $tahun]) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('administrator.laporan.export.pdf', ['tahun' => $tahun]) }}"
                   class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Ringkasan KPI -->
    <div class="grid grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-blue-500">
            <p class="text-2xl font-bold text-blue-600">{{ $laporanBulanan->sum('total_permohonan') }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Permohonan</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-green-500">
            <p class="text-2xl font-bold text-green-600">{{ $laporanBulanan->sum('disetujui') }}</p>
            <p class="text-xs text-gray-500 mt-1">Disetujui</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-red-500">
            <p class="text-2xl font-bold text-red-600">{{ $laporanBulanan->sum('ditolak') }}</p>
            <p class="text-xs text-gray-500 mt-1">Ditolak</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-purple-500">
            <p class="text-2xl font-bold text-purple-600">{{ $laporanBulanan->sum('kartu_baru') }}</p>
            <p class="text-xs text-gray-500 mt-1">Kartu Baru</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-600">{{ $laporanBulanan->sum('kartu_kadaluarsa') }}</p>
            <p class="text-xs text-gray-500 mt-1">Kadaluarsa</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center border-t-4 border-teal-500">
            <p class="text-2xl font-bold text-teal-600">{{ $laporanBulanan->sum('kartu_diperpanjang') }}</p>
            <p class="text-xs text-gray-500 mt-1">Diperpanjang</p>
        </div>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-bold text-gray-800 mb-4">📋 Permohonan per Bulan</h3>
            <canvas id="chartPermohonan" height="250"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-bold text-gray-800 mb-4">🪪 Kartu PAS per Bulan</h3>
            <canvas id="chartKartu" height="250"></canvas>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-bold text-lg text-gray-800 mb-4">📊 Detail Laporan Bulanan {{ $tahun }}</h3>

        @if($laporanBulanan->isEmpty())
            <p class="text-gray-500 text-center py-8">Belum ada data laporan untuk tahun {{ $tahun }}.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background: #1e3a5f; color: white;">
                        <th class="px-4 py-3 text-left">Bulan</th>
                        <th class="px-4 py-3 text-center">Total Permohonan</th>
                        <th class="px-4 py-3 text-center">Disetujui</th>
                        <th class="px-4 py-3 text-center">Ditolak</th>
                        <th class="px-4 py-3 text-center">Kartu Baru</th>
                        <th class="px-4 py-3 text-center">Kadaluarsa</th>
                        <th class="px-4 py-3 text-center">Diperpanjang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporanBulanan as $laporan)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-gray-800">
                            {{ DateTime::createFromFormat('!m', $laporan->bulan)->format('F') }} {{ $laporan->tahun }}
                        </td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $laporan->total_permohonan }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $laporan->disetujui }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $laporan->ditolak }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">{{ $laporan->kartu_baru }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">{{ $laporan->kartu_kadaluarsa }}</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-700">{{ $laporan->kartu_diperpanjang }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-center">{{ $laporanBulanan->sum('total_permohonan') }}</td>
                        <td class="px-4 py-3 text-center">{{ $laporanBulanan->sum('disetujui') }}</td>
                        <td class="px-4 py-3 text-center">{{ $laporanBulanan->sum('ditolak') }}</td>
                        <td class="px-4 py-3 text-center">{{ $laporanBulanan->sum('kartu_baru') }}</td>
                        <td class="px-4 py-3 text-center">{{ $laporanBulanan->sum('kartu_kadaluarsa') }}</td>
                        <td class="px-4 py-3 text-center">{{ $laporanBulanan->sum('kartu_diperpanjang') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const bulanLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const dataPermohonan   = @json($laporanBulanan->pluck('total_permohonan', 'bulan'));
        const dataDisetujui    = @json($laporanBulanan->pluck('disetujui', 'bulan'));
        const dataKartuBaru    = @json($laporanBulanan->pluck('kartu_baru', 'bulan'));
        const dataKadaluarsa   = @json($laporanBulanan->pluck('kartu_kadaluarsa', 'bulan'));

        new Chart(document.getElementById('chartPermohonan'), {
            type: 'bar',
            data: {
                labels: bulanLabel,
                datasets: [
                    { label: 'Total Permohonan', data: bulanLabel.map((_, i) => dataPermohonan[i+1] ?? 0), backgroundColor: '#3b82f6', borderRadius: 6 },
                    { label: 'Disetujui', data: bulanLabel.map((_, i) => dataDisetujui[i+1] ?? 0), backgroundColor: '#22c55e', borderRadius: 6 },
                ]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        new Chart(document.getElementById('chartKartu'), {
            type: 'line',
            data: {
                labels: bulanLabel,
                datasets: [
                    { label: 'Kartu Baru', data: bulanLabel.map((_, i) => dataKartuBaru[i+1] ?? 0), borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.1)', tension: 0.4, fill: true },
                    { label: 'Kadaluarsa', data: bulanLabel.map((_, i) => dataKadaluarsa[i+1] ?? 0), borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', tension: 0.4, fill: true },
                ]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    </script>

</x-app-layout>