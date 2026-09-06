<x-app-layout>
    <x-slot name="header">
        Dashboard Operator
    </x-slot>

    <div class="space-y-6">
        <!-- Hero / Welcome Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#1e3a5f] via-[#244774] to-[#1a3353] p-6 sm:p-8 text-white shadow-lg">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 border border-amber-400/30 text-amber-300 text-xs font-semibold uppercase tracking-wider mb-3">
                        <i class="fas fa-shield-alt"></i> Panel Kontrol Operator Keamanan
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                        Selamat Datang, <span class="text-[#f0b429]">{{ auth()->user()->name }}</span>!
                    </h1>
                    <p class="text-slate-300 text-sm mt-1 max-w-2xl">
                        Monitor akses masuk/keluar area bandara secara real-time, kelola perangkat scanner, dan pastikan kepatuhan kartu izin PAS Bandara.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($connectedDevice)
                        <a href="{{ route('operator.kamera.scanner') }}"
                           class="inline-flex items-center gap-2 bg-[#f0b429] hover:bg-amber-400 text-[#1e3a5f] font-bold px-5 py-3 rounded-xl shadow-md transition-all hover:scale-[1.02] text-sm">
                            <i class="fas fa-qrcode text-base"></i>
                            <span>Buka Live Scanner</span>
                        </a>
                    @else
                        <a href="{{ route('operator.kamera.index') }}"
                           class="inline-flex items-center gap-2 bg-[#f0b429] hover:bg-amber-400 text-[#1e3a5f] font-bold px-5 py-3 rounded-xl shadow-md transition-all hover:scale-[1.02] text-sm">
                            <i class="fas fa-key text-base"></i>
                            <span>Masukkan Kode Akses</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Background decorative shape -->
            <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
                <i class="fas fa-video text-9xl text-white"></i>
            </div>
        </div>

        <!-- Status Kamera Aktif -->
        @if($connectedDevice)
            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                Kamera Terhubung
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ strtoupper($connectedDevice->tipe_scan) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mt-0.5">{{ $connectedDevice->nama_kamera }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Area Akses: <strong>{{ $connectedDevice->kode_area }}</strong>
                            @if($connectedDevice->areaAkses)
                                — {{ $connectedDevice->areaAkses->nama_area }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('operator.kamera.scanner') }}"
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-camera"></i> Live Scanner
                    </a>
                    <form action="{{ route('operator.kamera.disconnect') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan koneksi kamera ini?');">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 font-semibold text-xs px-3 py-2.5 rounded-lg transition-colors border border-gray-200 hover:border-red-200">
                            <i class="fas fa-unlink"></i> Putuskan
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-5 border border-amber-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 border border-amber-300 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Perangkat Kamera Belum Terhubung</span>
                        <h3 class="text-base font-bold text-gray-800 mt-0.5">Sesi scanner saat ini belum memiliki kamera yang dipilih</h3>
                        <p class="text-xs text-gray-600 mt-0.5">
                            Hubungkan perangkat kamera menggunakan <strong>Kode Akses Kamera</strong> yang ditugaskan Administrator untuk memulai pemindaian QR.
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('operator.kamera.index') }}"
                       class="inline-flex items-center gap-2 bg-[#1e3a5f] hover:bg-[#2a4d7c] text-white font-semibold text-xs px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-key text-amber-400"></i> Hubungkan Kamera
                    </a>
                </div>
            </div>
        @endif

        <!-- Card Kamera yang Ditugaskan untuk Operator Ini -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-800">Daftar Titik Pos Kamera Ditugaskan</h3>
                        <p class="text-xs text-gray-500">Perangkat kamera yang telah diotorisasi Administrator untuk akun Anda</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto">
                    {{ $assignedCameras->count() }} Kamera Diizinkan
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-4">
                @forelse($assignedCameras as $cam)
                    @php $isCurrent = $connectedDevice && $connectedDevice->id === $cam->id; @endphp
                    <div class="p-3.5 rounded-xl border {{ $isCurrent ? 'border-emerald-300 bg-emerald-50/40' : 'border-gray-200 bg-gray-50/40 hover:bg-gray-50' }} transition-all flex flex-col justify-between gap-3">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-bold text-xs text-gray-900 truncate" title="{{ $cam->nama_kamera }}">{{ $cam->nama_kamera }}</h4>
                                @if($isCurrent)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">
                                        Aktif
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 mt-1.5">
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.2 rounded">
                                    Area {{ $cam->kode_area }}
                                </span>
                                <span class="text-[10px] text-gray-500 uppercase">
                                    {{ str_replace('_', ' ', $cam->tipe_scan) }}
                                </span>
                            </div>
                            <div class="mt-2 text-xs text-gray-600 font-mono">
                                Kode: <strong class="text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200">{{ $cam->kode_akses }}</strong>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between">
                            @if($isCurrent)
                                <a href="{{ route('operator.kamera.scanner') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                                    <i class="fas fa-qrcode"></i> Buka Live Scanner &rarr;
                                </a>
                            @else
                                <form action="{{ route('operator.kamera.connect') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="kode_akses" value="{{ $cam->kode_akses }}">
                                    <button type="submit" class="w-full text-center bg-[#1e3a5f] hover:bg-[#284c7b] text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition-colors flex items-center justify-center gap-1.5">
                                        <i class="fas fa-plug text-amber-400 text-[10px]"></i> Hubungkan Kamera
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-6 text-center text-gray-400">
                        <i class="fas fa-key text-2xl mb-1 text-gray-300 block"></i>
                        <p class="text-xs font-medium text-gray-600">Belum ada kamera yang ditugaskan kepada Anda</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Silakan hubungi Administrator untuk memperoleh izin akses kamera.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Statistik KPI Hari Ini -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Scan Hari Ini -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Scan Hari Ini</p>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($totalScanHariIni) }}</h3>
                        <p class="text-[11px] text-gray-500 mt-1">Aktivitas pemindaian</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                        <i class="fas fa-qrcode"></i>
                    </div>
                </div>
            </div>

            <!-- Scan Berhasil / Valid -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Akses Diizinkan</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($scanBerhasilHariIni) }}</h3>
                        <p class="text-[11px] text-gray-500 mt-1">Status izin valid</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Scan Ditolak / Tidak Valid -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Akses Ditolak</p>
                        <h3 class="text-2xl font-extrabold text-red-600 mt-1">{{ number_format($scanDitolakHariIni) }}</h3>
                        <p class="text-[11px] text-gray-500 mt-1">Kadaluarsa / Salah area</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Kamera Aktif Tersedia -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Kamera Aktif</p>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($totalKameraAktif) }}</h3>
                        <p class="text-[11px] text-gray-500 mt-1">Siap terhubung di sistem</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                        <i class="fas fa-video"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pemindaian Terkini -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                <div class="flex items-center gap-2">
                    <i class="fas fa-history text-gray-400"></i>
                    <h2 class="font-bold text-gray-800 text-base">Riwayat Pemindaian Terkini</h2>
                    @if($connectedDevice)
                        <span class="text-xs text-gray-500">({{ $connectedDevice->nama_kamera }})</span>
                    @endif
                </div>
                <a href="{{ route('operator.kamera.logs') }}"
                   class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                    <span>Lihat Seluruh Log</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3">Waktu</th>
                            <th class="px-5 py-3">Nomor Kartu</th>
                            <th class="px-5 py-3">Nama Pemegang</th>
                            <th class="px-5 py-3">Perusahaan</th>
                            <th class="px-5 py-3">Perangkat / Area</th>
                            <th class="px-5 py-3 text-center">Status Izin</th>
                            <th class="px-5 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentLogs as $log)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->waktu_scan)->format('H:i:s') }}
                                    <span class="text-[10px] text-gray-400 block">{{ \Carbon\Carbon::parse($log->waktu_scan)->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-mono font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $log->nomor_kartu ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">
                                    {{ $log->nama_pemegang ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-600">
                                    {{ $log->perusahaan ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500">
                                    <div class="font-semibold text-gray-800">{{ $log->nama_kamera ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $log->kode_area ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($log->status_akses === 'diterima')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check-circle text-[10px]"></i> VALID
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <i class="fas fa-times-circle text-[10px]"></i> DITOLAK
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-600 max-w-xs truncate" title="{{ $log->alasan ?? $log->catatan }}">
                                    {{ $log->alasan ?? $log->catatan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                    <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-2 block"></i>
                                    <p class="font-medium text-gray-500">Belum ada pemindaian tercatat hari ini</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Buka live scanner untuk mulai memindai QR code kartu PAS.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
