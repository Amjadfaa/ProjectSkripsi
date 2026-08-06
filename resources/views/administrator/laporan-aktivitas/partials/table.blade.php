<div>
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-extrabold text-base text-gray-800 flex items-center gap-2">
            <i class="fas fa-list-alt text-blue-600"></i> Riwayat Aktivitas Scan Masuk / Keluar
        </h3>
        <span class="text-xs text-gray-500 font-medium">
            Halaman {{ $scanLogs->currentPage() }} dari {{ $scanLogs->lastPage() }}
        </span>
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

        <!-- Custom Rapi & Elegant Indonesian Pagination Bar -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
            <div class="text-xs text-gray-500 font-medium">
                Menampilkan <span class="font-bold text-gray-800">{{ $scanLogs->firstItem() ?? 0 }}</span> s/d <span class="font-bold text-gray-800">{{ $scanLogs->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-blue-600">{{ $scanLogs->total() }}</span> data
            </div>

            @if($scanLogs->hasPages())
                <div class="inline-flex items-center space-x-1 rounded-xl bg-gray-100/90 p-1 border border-gray-200/80">
                    {{-- Previous Page Link --}}
                    @if ($scanLogs->onFirstPage())
                        <span class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-400 cursor-not-allowed select-none">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                        </span>
                    @else
                        <a href="{{ $scanLogs->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-700 hover:bg-white hover:text-blue-600 hover:shadow-sm transition">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                        </a>
                    @endif

                    {{-- Page Links --}}
                    @foreach ($scanLogs->getUrlRange(1, $scanLogs->lastPage()) as $page => $url)
                        @if ($page == $scanLogs->currentPage())
                            <span class="px-3 py-1.5 rounded-lg text-xs font-black bg-blue-600 text-white shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 hover:bg-white hover:text-blue-600 hover:shadow-sm transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($scanLogs->hasMorePages())
                        <a href="{{ $scanLogs->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-700 hover:bg-white hover:text-blue-600 hover:shadow-sm transition">
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    @else
                        <span class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-400 cursor-not-allowed select-none">
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
