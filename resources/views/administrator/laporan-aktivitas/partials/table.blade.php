{{-- ======================================================================== --}}
{{-- TABLE PARTIAL FOR DETAIL LAPORAN AKTIVITAS PAS (SPA REUSABLE COMPONENT)  --}}
{{-- ======================================================================== --}}
<div class="w-full">
    <table class="w-full text-left text-xs border-collapse">
        <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 select-none">
                <th scope="col" class="px-3.5 py-3 text-center w-12">No</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap">Waktu Scan</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap">Area & Perangkat</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap">No. Kartu PAS</th>
                <th scope="col" class="px-3.5 py-3">Pemegang & Instansi</th>
                <th scope="col" class="px-3.5 py-3 text-center whitespace-nowrap">Aktivitas</th>
                <th scope="col" class="px-3.5 py-3 text-center whitespace-nowrap">Status Akses</th>
                <th scope="col" class="px-3.5 py-3">Keterangan / Alasan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white text-xs">
            @forelse($scanLogs as $idx => $log)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    {{-- 1. Nomor Urut --}}
                    <td class="px-3.5 py-3 text-center font-bold text-slate-400">
                        {{ $scanLogs->firstItem() + $idx }}
                    </td>

                    {{-- 2. Waktu Scan --}}
                    <td class="px-3.5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-1.5 font-bold text-slate-800 text-xs">
                            <i class="far fa-calendar text-blue-500 text-[11px]"></i>
                            <span>{{ $log->waktu_scan ? $log->waktu_scan->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-500 text-[11px] mt-0.5 font-mono">
                            <i class="far fa-clock text-slate-400 text-[10px]"></i>
                            <span>{{ $log->waktu_scan ? $log->waktu_scan->format('H:i:s') : '-' }} WIT</span>
                        </div>
                    </td>

                    {{-- 3. Area & Perangkat --}}
                    <td class="px-3.5 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                            <i class="fas fa-location-dot text-[9px]"></i>
                            <span>Area {{ $log->kode_area }}</span>
                        </span>
                        <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1 truncate max-w-[150px]" title="{{ optional($log->cameraDevice)->nama_kamera ?? 'Kamera Station' }}">
                            <i class="fas fa-video text-slate-400 text-[9px] shrink-0"></i>
                            <span class="truncate">{{ optional($log->cameraDevice)->nama_kamera ?? 'Kamera Station' }}</span>
                        </p>
                    </td>

                    {{-- 4. No. Kartu PAS --}}
                    <td class="px-3.5 py-3 whitespace-nowrap">
                        <span class="font-mono font-bold text-xs text-blue-600 bg-blue-50/80 px-2.5 py-1 rounded-lg border border-blue-100 shadow-2xs">
                            {{ $log->nomor_kartu }}
                        </span>
                    </td>

                    {{-- 5. Pemegang & Instansi --}}
                    <td class="px-3.5 py-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] shrink-0">
                                {{ strtoupper(substr($log->nama_pemegang ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 text-xs truncate max-w-[160px]" title="{{ $log->nama_pemegang ?? '-' }}">
                                    {{ $log->nama_pemegang ?? '-' }}
                                </p>
                                <p class="text-[11px] text-slate-500 truncate max-w-[160px] flex items-center gap-1" title="{{ $log->perusahaan ?? '-' }}">
                                    <i class="fas fa-building text-slate-400 text-[9px] shrink-0"></i>
                                    <span class="truncate">{{ $log->perusahaan ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- 6. Tipe Scan (Masuk/Keluar) --}}
                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                        @if($log->tipe_aktivitas === 'keluar')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                <i class="fas fa-right-from-bracket text-[9px]"></i>
                                <span>Keluar (OUT)</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                <i class="fas fa-right-to-bracket text-[9px]"></i>
                                <span>Masuk (IN)</span>
                            </span>
                        @endif
                    </td>

                    {{-- 7. Status Akses --}}
                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                        @if($log->status_akses === 'diterima')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Diterima</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Ditolak</span>
                            </span>
                        @endif
                    </td>

                    {{-- 8. Keterangan / Alasan --}}
                    <td class="px-3.5 py-3">
                        <div class="text-xs text-slate-700 font-medium max-w-[200px] truncate" title="{{ $log->alasan ?? 'Akses diizinkan' }}">
                            {{ $log->alasan ?: 'Akses diizinkan' }}
                        </div>
                        @if($log->catatan)
                            <div class="text-[10px] text-blue-700 mt-0.5 italic bg-blue-50/80 px-2 py-0.5 rounded border border-blue-100 inline-flex items-center gap-1 max-w-[200px] truncate" title="{{ $log->catatan }}">
                                <i class="fas fa-note-sticky text-[8px] shrink-0"></i>
                                <span class="truncate">{{ $log->catatan }}</span>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Tidak Ada Riwayat Aktivitas Scan</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Tidak ditemukan data scan log yang sesuai dengan rentang tanggal dan parameter filter yang dipilih.
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- FOOTER / PAGINASI REUSABLE COMPONENT --}}
<div id="spaPaginationWrapper">
    <x-pagination :paginator="$scanLogs" />
</div>

{{-- METADATA UNTUK SPA STATS UPDATE --}}
<div id="spaKpiData"
     data-total-scan="{{ $totalScan ?? '' }}"
     data-total-masuk="{{ $totalMasuk ?? '' }}"
     data-total-keluar="{{ $totalKeluar ?? '' }}"
     data-total-diterima="{{ $totalDiterima ?? '' }}"
     data-total-ditolak="{{ $totalDitolak ?? '' }}"
     class="hidden">
</div>
