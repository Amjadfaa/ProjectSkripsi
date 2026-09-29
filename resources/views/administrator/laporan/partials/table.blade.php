{{-- ======================================================================== --}}
{{-- TABLE PARTIAL FOR DETAIL LAPORAN KARTU PAS (SPA REUSABLE COMPONENT)       --}}
{{-- ======================================================================== --}}
<div class="w-full">
    <table class="w-full text-left text-xs border-collapse">
        <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 select-none">
                <th scope="col" class="px-3 py-3 text-center w-10">No</th>
                <th scope="col" class="px-3 py-3 whitespace-nowrap">No. Kartu PAS</th>
                <th scope="col" class="px-3 py-3">Nama Pemegang</th>
                <th scope="col" class="px-3 py-3">Instansi / Perusahaan</th>
                <th scope="col" class="px-3 py-3">Jabatan</th>
                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">Area Akses</th>
                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">Tgl Terbit</th>
                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">Masa Berlaku</th>
                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">Tipe</th>
                <th scope="col" class="px-3 py-3 text-center whitespace-nowrap">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white text-xs">
            @forelse($detailKartuPas as $idx => $kartu)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    {{-- 1. Nomor Baris --}}
                    <td class="px-3 py-3 text-center font-bold text-slate-400">
                        {{ $detailKartuPas->firstItem() + $idx }}
                    </td>

                    {{-- 2. Nomor Kartu PAS --}}
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="font-mono font-bold text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-100">
                            {{ $kartu->nomor_kartu }}
                        </span>
                    </td>

                    {{-- 3. Nama Pemegang --}}
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-[10px] shrink-0">
                                {{ strtoupper(substr($kartu->nama_pemegang, 0, 1)) }}
                            </div>
                            <span class="font-bold text-slate-800 text-xs truncate max-w-[140px]" title="{{ $kartu->nama_pemegang }}">
                                {{ $kartu->nama_pemegang }}
                            </span>
                        </div>
                    </td>

                    {{-- 4. Instansi / Perusahaan --}}
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-1.5 text-slate-700 min-w-0" title="{{ $kartu->perusahaan ?? ($kartu->instansi->nama_instansi ?? '-') }}">
                            <i class="fas fa-building text-slate-400 text-[10px] shrink-0"></i>
                            <span class="truncate max-w-[130px]">
                                {{ $kartu->perusahaan ?? ($kartu->instansi->nama_instansi ?? '-') }}
                            </span>
                        </div>
                    </td>

                    {{-- 5. Jabatan --}}
                    <td class="px-3 py-3">
                        <span class="text-slate-600 truncate block max-w-[110px]" title="{{ $kartu->jabatan ?? '-' }}">
                            {{ $kartu->jabatan ?? '-' }}
                        </span>
                    </td>

                    {{-- 6. Area Akses --}}
                    <td class="px-3 py-3 text-center whitespace-nowrap">
                        <span class="font-mono font-black text-[11px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $kartu->area_akses ?? '-' }}
                        </span>
                    </td>

                    {{-- 7. Tanggal Terbit --}}
                    <td class="px-3 py-3 text-center text-slate-500 whitespace-nowrap text-[11px]">
                        {{ $kartu->tanggal_terbit ? $kartu->tanggal_terbit->format('d/m/Y') : '-' }}
                    </td>

                    {{-- 8. Masa Berlaku --}}
                    <td class="px-3 py-3 text-center whitespace-nowrap text-[11px] font-semibold {{ ($kartu->status === 'kadaluarsa' || ($kartu->tanggal_berlaku && $kartu->tanggal_berlaku->isPast())) ? 'text-rose-600' : 'text-emerald-700' }}">
                        {{ $kartu->tanggal_berlaku ? $kartu->tanggal_berlaku->format('d/m/Y') : '-' }}
                    </td>

                    {{-- 9. Tipe Permohonan --}}
                    <td class="px-3 py-3 text-center whitespace-nowrap">
                        @if(strtolower($kartu->tipe_permohonan ?? '') === 'perpanjangan')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                                <i class="fas fa-arrows-rotate text-[8px]"></i>
                                <span>Perpanjangan</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                <i class="fas fa-plus text-[8px]"></i>
                                <span>Baru</span>
                            </span>
                        @endif
                    </td>

                    {{-- 10. Status Kartu --}}
                    <td class="px-3 py-3 text-center whitespace-nowrap">
                        @if($kartu->status === 'aktif')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Aktif</span>
                            </span>
                        @elseif($kartu->status === 'kadaluarsa')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>Kadaluarsa</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Nonaktif</span>
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                            <i class="fas fa-id-card-clip"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Tidak Ada Rincian Kartu PAS</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Tidak ditemukan kartu PAS yang diterbitkan pada periode filter ini.
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- FOOTER / PAGINASI REUSABLE COMPONENT --}}
<div id="spaPaginationWrapper">
    <x-pagination :paginator="$detailKartuPas" />
</div>

{{-- METADATA UNTUK SPA STATS UPDATE --}}
<div id="spaKpiData" 
     data-terbit="{{ $totalKartuTerbit }}" 
     data-aktif="{{ $totalKartuAktif }}" 
     data-kadaluarsa="{{ $totalKadaluarsa }}" 
     data-nonaktif="{{ $totalNonaktif }}"
     class="hidden">
</div>
