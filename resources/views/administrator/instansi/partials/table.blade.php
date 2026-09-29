<div class="w-full">
    <x-table>
        <x-slot name="header">
            <th class="px-4 py-3.5 font-bold">Instansi / Perusahaan</th>
            <th class="px-4 py-3.5 font-bold text-center">Kuota PAS</th>
            <th class="px-4 py-3.5 font-bold text-center">Terpakai</th>
            <th class="px-4 py-3.5 font-bold text-center">Sisa Kuota</th>
            <th class="px-4 py-3.5 font-bold">Kontak</th>
            <th class="px-4 py-3.5 font-bold text-center">Status</th>
            <th class="px-4 py-3.5 font-bold text-center">Aksi</th>
        </x-slot>

        @forelse($instansis as $instansi)
            @php
                $terpakai = $instansi->kartu_terpakai ?? ($instansi->total_kartu ?? 0);
                $aktif    = $instansi->kartu_aktif ?? 0;
                $nonaktif = $instansi->kartu_nonaktif ?? 0;
                $sisa     = max(0, $instansi->kuota - $terpakai);
                $isHabis  = $sisa <= 0;
            @endphp
            <tr class="hover:bg-slate-50/80 transition-colors {{ $isHabis ? 'bg-rose-50/20' : '' }}">
                {{-- Nama Instansi & Alamat --}}
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-xs shrink-0 font-bold shadow-2xs">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm leading-tight truncate max-w-[260px]" title="{{ $instansi->nama_instansi }}">
                                {{ $instansi->nama_instansi }}
                            </p>
                            <p class="text-[11px] text-slate-400 truncate max-w-[240px] mt-0.5" title="{{ $instansi->alamat ?? '-' }}">
                                {{ $instansi->alamat ?? '-' }}
                            </p>
                        </div>
                    </div>
                </td>

                {{-- Kuota PAS --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center min-w-[38px] px-2.5 py-1 rounded-lg text-xs font-black text-slate-800 bg-slate-100 border border-slate-200/80 shadow-2xs">
                        {{ $instansi->kuota }} Kartu
                    </span>
                </td>

                {{-- Terpakai --}}
                <td class="px-4 py-3.5 text-center">
                    <div class="inline-flex flex-col items-center">
                        <span class="inline-flex items-center justify-center min-w-[36px] px-2.5 py-1 rounded-lg text-xs font-black text-purple-700 bg-purple-50 border border-purple-200/80 shadow-2xs">
                            {{ $terpakai }} Kartu
                        </span>
                        @if($terpakai > 0)
                            <span class="text-[10px] text-slate-400 font-medium mt-1">
                                {{ $aktif }} aktif{{ $nonaktif > 0 ? ', ' . $nonaktif . ' kadaluarsa/nonaktif' : '' }}
                            </span>
                        @endif
                    </div>
                </td>

                {{-- Sisa Kuota --}}
                <td class="px-4 py-3.5 text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black border shadow-2xs {{ $isHabis ? 'text-rose-700 bg-rose-50 border-rose-200/80' : 'text-emerald-700 bg-emerald-50 border-emerald-200/80' }}">
                        <span>{{ $sisa }} Kartu</span>
                        @if($isHabis)
                            <span class="text-[10px] uppercase font-extrabold text-rose-600">[HABIS]</span>
                        @endif
                    </span>
                </td>

                {{-- Kontak --}}
                <td class="px-4 py-3.5">
                    @if($instansi->email || $instansi->telepon)
                        <div class="space-y-0.5">
                            @if($instansi->email)
                                <div class="flex items-center gap-1.5 text-xs text-slate-600 truncate max-w-[190px]" title="{{ $instansi->email }}">
                                    <i class="fas fa-envelope text-slate-400 text-[10px] shrink-0"></i>
                                    <span class="truncate">{{ $instansi->email }}</span>
                                </div>
                            @endif
                            @if($instansi->telepon)
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-mono">
                                    <i class="fas fa-phone text-slate-400 text-[10px] shrink-0"></i>
                                    <span>{{ $instansi->telepon }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="text-slate-400 text-xs">-</span>
                    @endif
                </td>

                {{-- Status --}}
                <td class="px-4 py-3.5 text-center">
                    @if($instansi->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200/80 shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            Nonaktif
                        </span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td class="px-4 py-3.5 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <button type="button" onclick='openModalEditInstansi(@json($instansi))'
                                class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200/80 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer"
                                title="Edit Instansi">
                            <i class="fas fa-pen-to-square"></i>
                        </button>
                        <button type="button" onclick="confirmDeleteInstansi({{ $instansi->id }}, '{{ addslashes($instansi->nama_instansi) }}')"
                                class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer"
                                title="Hapus Instansi">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-slate-500">
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
