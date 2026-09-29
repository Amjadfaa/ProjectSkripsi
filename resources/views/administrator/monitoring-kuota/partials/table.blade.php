<div class="w-full">
    <x-table>
        <x-slot name="header">
            <th class="px-4 py-3.5 font-bold">Instansi / Perusahaan</th>
            <th class="px-4 py-3.5 font-bold text-center">Total Kuota</th>
            <th class="px-4 py-3.5 font-bold text-center">Kartu Aktif</th>
            <th class="px-4 py-3.5 font-bold text-center">Sisa Kuota</th>
            <th class="px-4 py-3.5 font-bold text-center">Nonaktif</th>
            <th class="px-4 py-3.5 font-bold" style="min-width: 180px;">Persentase Pemakaian</th>
            <th class="px-4 py-3.5 font-bold text-center">Set Kuota</th>
            <th class="px-4 py-3.5 font-bold text-center">Aksi</th>
        </x-slot>

        @forelse($instansis as $instansi)
            @php
                $persen    = $instansi->kuota > 0 ? min(($instansi->kartu_aktif / $instansi->kuota) * 100, 100) : 0;
                $warnaBar  = $persen >= 90 ? 'bg-rose-500' : ($persen >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                $warnaText = $persen >= 90 ? 'text-rose-600' : ($persen >= 75 ? 'text-amber-600' : 'text-emerald-600');
                $warnaBg   = $persen >= 90 ? 'bg-rose-50/50' : '';
            @endphp
            <tr class="hover:bg-slate-50/80 transition-colors {{ $warnaBg }}">
                {{-- Instansi --}}
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-xs shrink-0 font-bold shadow-2xs">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm leading-tight truncate max-w-[250px]">{{ $instansi->nama_instansi }}</p>
                            <p class="text-[11px] text-slate-400 truncate max-w-[220px]">{{ $instansi->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </td>

                {{-- Total Kuota --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center min-w-[36px] px-2.5 py-1 rounded-lg text-sm font-black text-slate-800 bg-slate-100 border border-slate-200/80 shadow-2xs">
                        {{ $instansi->kuota }}
                    </span>
                </td>

                {{-- Kartu Aktif --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center min-w-[36px] px-2.5 py-1 rounded-lg text-sm font-black text-emerald-700 bg-emerald-50 border border-emerald-200/80 shadow-2xs">
                        {{ $instansi->kartu_aktif }}
                    </span>
                </td>

                {{-- Sisa Kuota --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center min-w-[36px] px-2.5 py-1 rounded-lg text-sm font-black {{ $instansi->sisa_kuota <= 0 ? 'text-rose-700 bg-rose-50 border-rose-200/80' : 'text-indigo-700 bg-indigo-50 border-indigo-200/80' }} border shadow-2xs">
                        {{ $instansi->sisa_kuota }}
                    </span>
                </td>

                {{-- Nonaktif --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="text-sm font-semibold text-slate-500">{{ $instansi->kartu_nonaktif }}</span>
                </td>

                {{-- Persentase Pemakaian --}}
                <td class="px-4 py-3.5">
                    <div class="w-full">
                        <div class="w-full bg-slate-100 rounded-full h-2 mb-1.5 overflow-hidden">
                            <div class="{{ $warnaBar }} h-2 rounded-full transition-all duration-700" style="width: {{ $persen }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold {{ $warnaText }}">{{ round($persen, 1) }}%</span>
                            <span class="text-slate-400 text-[10px] font-mono">{{ $instansi->kartu_aktif }}/{{ $instansi->kuota }}</span>
                        </div>
                    </div>
                </td>

                {{-- Set Kuota --}}
                <td class="px-4 py-3.5 text-center">
                    <form method="POST" action="{{ route('administrator.monitoring-kuota.update-kuota', $instansi->id) }}"
                          class="form-update-kuota flex items-center gap-1.5 justify-center"
                          onsubmit="handleUpdateKuota(event, this)">
                        @csrf @method('PUT')
                        <input type="number" name="kuota" value="{{ $instansi->kuota }}"
                               class="w-16 border-slate-200 rounded-lg text-xs text-center py-1.5 font-bold focus:ring-blue-500 focus:border-blue-500 shadow-2xs" min="0">
                        <button type="submit"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition-all cursor-pointer">
                            <i class="fas fa-check text-[10px]"></i> Set
                        </button>
                    </form>
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-3.5 text-center">
                    <button type="button" onclick="openModalDetailInstansi({{ $instansi->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-all cursor-pointer">
                        <i class="fas fa-eye text-[10px]"></i> Detail
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-2">
                            <i class="fas fa-building"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Tidak ada data instansi ditemukan</p>
                        <p class="text-xs text-slate-400 mt-0.5">Silakan sesuaikan kriteria pencarian atau filter Anda.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-table>

    {{-- Reusable Component Pagination --}}
    <x-pagination :paginator="$instansis" />
</div>
