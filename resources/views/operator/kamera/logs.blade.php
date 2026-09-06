<x-app-layout>
    <x-slot name="header">
        Log Pemindaian Scanner
    </x-slot>

    <div class="space-y-6">
        <!-- Header & Filter Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Riwayat Pemindaian Kartu PAS</h2>
                        <p class="text-xs text-gray-500">
                            @if($connectedDevice)
                                Menampilkan aktivitas pemindaian untuk kamera: <strong>{{ $connectedDevice->nama_kamera }}</strong> ({{ $connectedDevice->kode_area }})
                            @else
                                Menampilkan seluruh aktivitas pemindaian di sistem
                            @endif
                        </p>
                    </div>
                </div>

                @if($connectedDevice)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('operator.kamera.scanner') }}"
                           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs px-3.5 py-2 rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-qrcode"></i> Buka Scanner
                        </a>
                    </div>
                @endif
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('operator.kamera.logs') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-gray-100">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Cari Pemegang / Kartu / PT</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Nama / No. Kartu / PT..."
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#1e3a5f] focus:border-[#1e3a5f]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Pemindaian</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ request('tanggal') }}"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#1e3a5f] focus:border-[#1e3a5f]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Status Izin</label>
                    <select name="status"
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#1e3a5f] focus:border-[#1e3a5f]">
                        <option value="">Semua Status</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Hanya Valid</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Hanya Ditolak</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 bg-[#1e3a5f] hover:bg-[#284c7b] text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'tanggal', 'status']))
                        <a href="{{ route('operator.kamera.logs') }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold py-2 px-3 rounded-lg transition-colors"
                           title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Log Pemindaian -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3.5">Waktu Scan</th>
                            <th class="px-5 py-3.5">Nomor Kartu</th>
                            <th class="px-5 py-3.5">Nama Pemegang</th>
                            <th class="px-5 py-3.5">Perusahaan</th>
                            <th class="px-5 py-3.5">Perangkat / Area</th>
                            <th class="px-5 py-3.5 text-center">Arah</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-gray-600 whitespace-nowrap">
                                    <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($log->waktu_scan)->format('H:i:s') }}</div>
                                    <div class="text-[11px] text-gray-400">{{ \Carbon\Carbon::parse($log->waktu_scan)->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-5 py-3.5 font-mono font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $log->nomor_kartu ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-gray-900">
                                    {{ $log->nama_pemegang ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-600">
                                    {{ $log->perusahaan ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                                    <div class="font-semibold text-gray-800">{{ $log->nama_kamera ?? optional($log->cameraDevice)->nama_kamera ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-400">Area: {{ $log->kode_area ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase {{ ($log->tipe_aktivitas ?? 'masuk') === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $log->tipe_aktivitas ?? 'masuk' }}
                                    </span>
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
                                <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                    <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-2 block"></i>
                                    <p class="font-medium text-gray-500">Tidak ada log pemindaian ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Coba sesuaikan filter pencarian atau tanggal.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
