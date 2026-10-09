<div class="w-full">
    <x-table>
        <x-slot name="header">
            <th class="px-4 py-3.5 font-bold">Instansi / Perusahaan</th>
            <th class="px-4 py-3.5 font-bold text-center">Total Kuota</th>
            <th class="px-4 py-3.5 font-bold text-center">Kuota Terpakai</th>
            <th class="px-4 py-3.5 font-bold text-center">Sisa Kuota</th>
            <th class="px-4 py-3.5 font-bold text-center">Nonaktif</th>
            <th class="px-4 py-3.5 font-bold" style="min-width: 180px;">Persentase Pemakaian</th>
            <th class="px-4 py-3.5 font-bold text-center">Set Kuota</th>
            <th class="px-4 py-3.5 font-bold text-center">Aksi</th>
        </x-slot>

        @forelse($instansis as $instansi)
            @php
                $persen    = $instansi->kuota > 0 ? min(($instansi->kartu_terpakai / $instansi->kuota) * 100, 100) : 0;
                $warnaBar  = $persen >= 90 ? 'bg-rose-500' : ($persen >= 75 ? 'bg-amber-500' : 'bg-blue-600');
                $warnaText = $persen >= 90 ? 'text-rose-600' : ($persen >= 75 ? 'text-amber-600' : 'text-blue-600');
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

                {{-- Kuota Terpakai (Kartu Aktif + Kadaluarsa) --}}
                <td class="px-4 py-3.5 text-center">
                    <div class="inline-flex flex-col items-center">
                        <span class="inline-flex items-center justify-center min-w-[36px] px-2.5 py-1 rounded-lg text-sm font-black {{ $instansi->kartu_terpakai > 0 ? 'text-amber-800 bg-amber-50 border-amber-200/90' : 'text-slate-400 bg-slate-50 border-slate-200/60' }} border shadow-2xs">
                            {{ $instansi->kartu_terpakai }}
                        </span>
                        @if($instansi->kartu_terpakai > 0)
                            <div class="flex items-center justify-center gap-1 mt-1 text-[10px] whitespace-nowrap">
                                <span class="text-emerald-700 font-bold" title="Kartu Masa Berlaku Aktif"><i class="fas fa-circle-check text-[9px] text-emerald-600"></i> {{ $instansi->kartu_aktif }} aktif</span>
                                @if($instansi->kartu_kadaluarsa > 0)
                                    <span class="text-slate-300">•</span>
                                    <span class="text-amber-700 font-bold" title="Kartu Kadaluarsa (Tetap memegang kuota sampai diperpanjang atau dinonaktifkan)"><i class="fas fa-clock text-[9px] text-amber-600"></i> {{ $instansi->kartu_kadaluarsa }} exp</span>
                                @endif
                            </div>
                        @else
                            <span class="text-[10px] text-slate-400 mt-1 whitespace-nowrap">0 terpakai</span>
                        @endif
                    </div>
                </td>

                {{-- Sisa Kuota --}}
                <td class="px-4 py-3.5 text-center">
                    <div class="inline-flex flex-col items-center">
                        <span class="inline-flex items-center justify-center min-w-[36px] px-2.5 py-1 rounded-lg text-sm font-black {{ $instansi->sisa_kuota <= 0 ? 'text-rose-700 bg-rose-50 border-rose-200/80' : 'text-indigo-700 bg-indigo-50 border-indigo-200/80' }} border shadow-2xs">
                            {{ $instansi->sisa_kuota }}
                        </span>
                        <span class="text-[10px] font-semibold {{ $instansi->sisa_kuota <= 0 ? 'text-rose-500' : 'text-slate-400' }} mt-1 whitespace-nowrap">
                            {{ $instansi->sisa_kuota <= 0 ? 'Habis' : 'Slot sisa' }}
                        </span>
                    </div>
                </td>

                {{-- Nonaktif (Kuota Dicabut) --}}
                <td class="px-4 py-3.5 text-center">
                    <div class="inline-flex flex-col items-center" title="Pegawai resign / pensiun (kuota telah dicabut & dikembalikan ke sisa kuota)">
                        <span class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200/70 shadow-2xs">
                            {{ $instansi->kartu_nonaktif }}
                        </span>
                        <span class="text-[9px] text-slate-400 mt-1 whitespace-nowrap">Dicabut</span>
                    </div>
                </td>

                {{-- Persentase Pemakaian --}}
                <td class="px-4 py-3.5">
                    <div class="w-full">
                        <div class="w-full bg-slate-100 rounded-full h-2 mb-1.5 overflow-hidden">
                            <div class="{{ $warnaBar }} h-2 rounded-full transition-all duration-700" style="width: {{ $persen }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold {{ $warnaText }}">{{ round($persen, 1) }}%</span>
                            <span class="text-slate-400 text-[10px] font-mono" title="{{ $instansi->kartu_terpakai }} kuota terpakai dari total {{ $instansi->kuota }} kuota">{{ $instansi->kartu_terpakai }}/{{ $instansi->kuota }}</span>
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
