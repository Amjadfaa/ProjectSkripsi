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

        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
            {{ $scanLogs->links() }}
        </div>
    @endif
</div>
