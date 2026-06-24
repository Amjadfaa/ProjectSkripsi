<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dashboard Monitoring Kartu PAS Bandara</h2>
    </x-slot>

    @php
        $totalKartu        = App\Models\KartuPas::count();
        $kartuKadaluarsa   = App\Models\KartuPas::where('status', 'kadaluarsa')->count();
        $kartuTidakAktif   = App\Models\KartuPas::where('status', 'tidak_aktif')->count();
        $kartuAkanBerakhir = App\Models\KartuPas::where('status', 'aktif')
            ->whereBetween('tanggal_berlaku', [now(), now()->addDays(30)])->count();
        $kartuHampirKadaluarsa = App\Models\KartuPas::where('status', 'aktif')
            ->whereBetween('tanggal_berlaku', [now(), now()->addDays(30)])
            ->orderBy('tanggal_berlaku')->get();
    @endphp

    <!-- Selamat Datang -->
    <div class="rounded-xl p-5 mb-6 text-white flex items-center justify-between"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);">
        <div class="flex items-center gap-4">
            <div style="width:52px; height:52px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:800;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-blue-200 text-sm">Administrator &nbsp;|&nbsp; {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
            </div>
        </div>
        <span style="font-size:52px; opacity:0.2;">✈️</span>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Kartu PAS</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalKartu }}</p>
            <p class="text-xs text-gray-400 mt-1">Seluruh kartu terdaftar</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Kartu Aktif</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalKartuAktif }}</p>
            <p class="text-xs text-gray-400 mt-1">Masih berlaku</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Kartu Kadaluarsa</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $kartuKadaluarsa }}</p>
            <p class="text-xs text-gray-400 mt-1">Perlu perpanjangan</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Akan Berakhir</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $kartuAkanBerakhir }}</p>
            <p class="text-xs text-gray-400 mt-1">Dalam 30 hari</p>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Pemohon</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ $totalPemohon }}</p>
            <p class="text-xs text-gray-400 mt-1">User terdaftar</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-400">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Permohonan</p>
            <p class="text-3xl font-bold text-blue-500 mt-1">{{ $totalPermohonan }}</p>
            <p class="text-xs text-gray-400 mt-1">Semua permohonan</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-400">
            <p class="text-xs text-gray-500 uppercase font-semibold">Disetujui</p>
            <p class="text-3xl font-bold text-green-500 mt-1">{{ $totalDisetujui }}</p>
            <p class="text-xs text-gray-400 mt-1">Permohonan disetujui</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-gray-400">
            <p class="text-xs text-gray-500 uppercase font-semibold">Tidak Aktif</p>
            <p class="text-3xl font-bold text-gray-600 mt-1">{{ $kartuTidakAktif }}</p>
            <p class="text-xs text-gray-400 mt-1">Resign/Pensiun/dll</p>
        </div>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-2 gap-6 mb-6">

        <!-- Grafik Status Kartu PAS (Donut) -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-bold text-gray-800 mb-4">🪪 Status Kartu PAS</h3>
            <canvas id="chartStatusKartu" height="220"></canvas>
        </div>

        <!-- Grafik Permohonan Bulanan (Bar) -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-bold text-gray-800 mb-4">📋 Permohonan per Bulan {{ date('Y') }}</h3>
            <canvas id="chartPermohonan" height="220"></canvas>
        </div>

    </div>

    <!-- Grafik Kartu PAS per Instansi (Bar Horizontal) -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-4">🏢 Kartu PAS Aktif per Instansi</h3>
        <canvas id="chartInstansi" height="120"></canvas>
    </div>

    <!-- Peringatan Kartu Akan Berakhir -->
    @if($kartuHampirKadaluarsa->isNotEmpty())
    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">
        <h3 class="font-bold text-red-700 mb-3 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i> Peringatan: Kartu PAS Akan Berakhir
        </h3>
        <div class="space-y-2">
            @foreach($kartuHampirKadaluarsa as $kartu)
            @php $sisaHari = now()->diffInDays($kartu->tanggal_berlaku); @endphp
            <div class="flex justify-between items-center bg-white rounded-lg px-4 py-2 border {{ $sisaHari <= 7 ? 'border-red-300' : 'border-yellow-300' }}">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $kartu->nama_pemegang }} <span class="text-gray-400 text-xs">({{ $kartu->nomor_kartu }})</span></p>
                    <p class="text-xs text-gray-500">{{ $kartu->perusahaan }} &nbsp;|&nbsp; {{ $kartu->area_akses }}</p>
                </div>
                <div class="text-right">
                    <span class="font-bold text-sm {{ $sisaHari <= 7 ? 'text-red-600' : 'text-yellow-600' }}">{{ $sisaHari }} Hari</span>
                    <p class="text-xs text-gray-400">{{ $kartu->tanggal_berlaku->format('d/m/Y') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dari Laravel
        const kartuAktif     = {{ $totalKartuAktif }};
        const kartuKadaluarsa = {{ $kartuKadaluarsa }};
        const kartuTidakAktif = {{ $kartuTidakAktif }};

        // 1. Donut Chart - Status Kartu PAS
        new Chart(document.getElementById('chartStatusKartu'), {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Kadaluarsa', 'Tidak Aktif'],
                datasets: [{
                    data: [kartuAktif, kartuKadaluarsa, kartuTidakAktif],
                    backgroundColor: ['#22c55e', '#ef4444', '#94a3b8'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 2. Bar Chart - Permohonan per Bulan
        const bulanLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const dataPermohonan = @json($laporanBulanan->pluck('total_permohonan', 'bulan'));
        const dataDisetujui  = @json($laporanBulanan->pluck('disetujui', 'bulan'));

        const permohonanData = bulanLabel.map((_, i) => dataPermohonan[i + 1] ?? 0);
        const disetujuiData  = bulanLabel.map((_, i) => dataDisetujui[i + 1] ?? 0);

        new Chart(document.getElementById('chartPermohonan'), {
            type: 'bar',
            data: {
                labels: bulanLabel,
                datasets: [
                    {
                        label: 'Total Permohonan',
                        data: permohonanData,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                    },
                    {
                        label: 'Disetujui',
                        data: disetujuiData,
                        backgroundColor: '#22c55e',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // 3. Bar Horizontal - Kartu PAS per Instansi
        @php
            $kartuPerInstansi = App\Models\KartuPas::where('status', 'aktif')
                ->selectRaw('perusahaan, COUNT(*) as total')
                ->groupBy('perusahaan')
                ->orderByDesc('total')
                ->get();
        @endphp

        new Chart(document.getElementById('chartInstansi'), {
            type: 'bar',
            data: {
                labels: @json($kartuPerInstansi->pluck('perusahaan')),
                datasets: [{
                    label: 'Kartu Aktif',
                    data: @json($kartuPerInstansi->pluck('total')),
                    backgroundColor: '#1e3a5f',
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    </script>

</x-app-layout>