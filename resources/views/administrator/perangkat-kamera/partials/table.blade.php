{{-- ======================================================================== --}}
{{-- TABLE PARTIAL FOR PERANGKAT KAMERA (SPA REUSABLE COMPONENT)              --}}
{{-- ======================================================================== --}}
<div class="w-full">
    <table class="w-full text-left text-xs border-collapse">
        <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 select-none">
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap">Perangkat Kamera</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap">Area Akses Ditugaskan</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap">Kode Akses Login</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap">Tipe Scan</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap text-center">Status</th>
                <th scope="col" class="px-3.5 py-3 whitespace-nowrap text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white text-xs">
            @forelse($devices as $device)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    {{-- 1. Nama Perangkat Kamera & ID --}}
                    <td class="px-3.5 py-3 font-bold text-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-xs shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                <i class="fas fa-camera"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800 text-xs group-hover:text-blue-600 transition-colors truncate max-w-[200px]">
                                    {{ $device->nama_kamera }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.2 rounded">
                                        ID: #CAM-{{ $device->id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- 2. Area Akses Ditugaskan --}}
                    <td class="px-3.5 py-3">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-xs px-2 py-0.5 rounded-lg bg-blue-600 text-white shadow-2xs shrink-0">
                                {{ $device->kode_area }}
                            </span>
                            <span class="font-semibold text-slate-700 text-xs truncate max-w-xs" title="{{ optional($device->areaAkses)->keterangan }}">
                                {{ optional($device->areaAkses)->keterangan ?? 'Area ' . $device->kode_area }}
                            </span>
                        </div>
                    </td>

                    {{-- 3. Kode Akses Login (dengan fitur Salin Kode) --}}
                    <td class="px-3.5 py-3 whitespace-nowrap">
                        <div class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200/80 border border-slate-200/80 px-2 py-0.5 rounded-lg transition">
                            <i class="fas fa-key text-purple-600 text-[10px]"></i>
                            <code class="font-mono font-bold text-xs text-purple-800 select-all tracking-wide">
                                {{ $device->kode_akses }}
                            </code>
                            <button type="button" 
                                    onclick="copyToClipboard('{{ $device->kode_akses }}', this)" 
                                    class="text-slate-400 hover:text-purple-700 p-0.5 ml-0.5 transition"
                                    title="Salin Kode Akses">
                                <i class="fas fa-copy text-[10px]"></i>
                            </button>
                        </div>
                    </td>

                    {{-- 4. Tipe Scan --}}
                    <td class="px-3.5 py-3 whitespace-nowrap">
                        @php
                            $tipeInfo = match($device->tipe_scan) {
                                'masuk' => ['label' => 'Masuk Saja', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'icon' => 'fa-arrow-right-to-bracket'],
                                'keluar' => ['label' => 'Keluar Saja', 'color' => 'bg-amber-50 text-amber-700 border-amber-200', 'icon' => 'fa-arrow-right-from-bracket'],
                                default => ['label' => 'Masuk & Keluar', 'color' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fa-right-left'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[10px] font-bold border shadow-2xs {{ $tipeInfo['color'] }}">
                            <i class="fas {{ $tipeInfo['icon'] }} text-[9px]"></i>
                            <span>{{ $tipeInfo['label'] }}</span>
                        </span>
                    </td>

                    {{-- 5. Status Aktif / Nonaktif --}}
                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                        @if($device->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Aktif</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>Nonaktif</span>
                            </span>
                        @endif
                    </td>

                    {{-- 6. Aksi (Edit & Hapus) --}}
                    <td class="px-3.5 py-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Tombol Edit Modal --}}
                            <button type="button" 
                                    data-device="{{ json_encode($device) }}"
                                    onclick="openModalEditKameraFromBtn(this)"
                                    class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200/80 flex items-center justify-center text-xs transition shadow-2xs cursor-pointer"
                                    title="Edit Konfigurasi Perangkat">
                                <i class="fas fa-pen text-[11px]"></i>
                            </button>

                            {{-- Tombol Hapus Modal --}}
                            <button type="button" 
                                    onclick="confirmDeleteKamera({{ $device->id }}, '{{ addslashes($device->nama_kamera) }}')"
                                    class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition shadow-2xs cursor-pointer"
                                    title="Hapus Perangkat Kamera">
                                <i class="fas fa-trash-can text-[11px]"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                            <i class="fas fa-video-slash"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Tidak Ada Data Perangkat Kamera</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            @if(request()->hasAny(['search', 'area', 'status', 'tipe_scan']))
                                Tidak ditemukan perangkat kamera yang sesuai dengan kriteria filter pencarian Anda.
                            @else
                                Belum ada perangkat kamera scan yang didaftarkan ke sistem bandara.
                            @endif
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- FOOTER / PAGINASI REUSABLE COMPONENT --}}
<div id="spaPaginationWrapper">
    <x-pagination :paginator="$devices" />
</div>

{{-- METADATA UNTUK SPA STATS UPDATE --}}
<div id="spaKpiData" 
     data-total="{{ $totalDevices }}" 
     data-aktif="{{ $totalAktif }}" 
     data-area="{{ $totalAreaUsed }}" 
     data-all="{{ $totalAreaAll }}"
     class="hidden">
</div>
