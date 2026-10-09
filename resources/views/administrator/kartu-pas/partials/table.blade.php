<div class="w-full">
    <table class="w-full text-left text-xs sm:text-sm border-collapse">
        <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 select-none">
                <th class="px-3 py-3.5 text-center w-10 shrink-0">
                    <input type="checkbox" id="checkAll" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer"
                           onchange="toggleCheckAll(this)">
                </th>
                <th class="px-3 py-3.5 font-bold w-[135px] whitespace-nowrap">No. Registrasi</th>
                <th class="px-3 py-3.5 font-bold w-[220px]">Nama Pemegang</th>
                <th class="px-3 py-3.5 font-bold w-[190px]">Instansi</th>
                <th class="px-2 py-3.5 font-bold w-[80px] text-center">Area</th>
                <th class="px-3 py-3.5 font-bold w-[170px]">Jabatan</th>
                <th class="px-3 py-3.5 font-bold w-[115px] whitespace-nowrap">Masa Berlaku</th>
                <th class="px-2 py-3.5 font-bold w-[100px] text-center whitespace-nowrap">Status</th>
                <th class="px-2 py-3.5 font-bold w-[105px] text-center whitespace-nowrap">Keterangan</th>
                <th class="px-3 py-3.5 font-bold w-[175px] text-center whitespace-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($kartuPas as $kartu)
                @php
                    $isExpired = $kartu->status === 'kadaluarsa';
                    $isNonaktif = $kartu->status === 'tidak_aktif';
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors {{ $isExpired ? 'bg-rose-50/20' : ($isNonaktif ? 'bg-slate-100/40 opacity-75' : '') }}">
                    <!-- Checkbox -->
                    <td class="px-3 py-3.5 text-center">
                        <input type="checkbox" class="kartu-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer"
                               value="{{ $kartu->id }}" onchange="updateSelectedCount()">
                    </td>

                    <!-- No Registrasi -->
                    <td class="px-3 py-3.5 whitespace-nowrap">
                        <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100/90 border border-slate-200/80 px-2 py-1 rounded-md tracking-wider">
                            {{ $kartu->nomor_kartu }}
                        </span>
                    </td>

                    <!-- Nama Pemegang -->
                    <td class="px-3 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                {{ strtoupper(substr($kartu->nama_pemegang, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 text-xs sm:text-sm leading-tight truncate" title="{{ $kartu->nama_pemegang }}">
                                    {{ $kartu->nama_pemegang }}
                                </div>
                                @if($kartu->email)
                                    <div class="text-[11px] text-slate-400 truncate mt-0.5" title="{{ $kartu->email }}">
                                        {{ $kartu->email }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- Instansi -->
                    <td class="px-3 py-3.5">
                        <div class="text-xs font-semibold text-slate-700 leading-snug line-clamp-2" title="{{ $kartu->perusahaan }}">
                            {{ $kartu->perusahaan }}
                        </div>
                    </td>

                    <!-- Area Akses -->
                    <td class="px-2 py-3.5 text-center">
                        <div class="flex flex-wrap items-center justify-center gap-1">
                            @php
                                $normArea = \App\Models\KartuPas::normalizeAreaAkses($kartu->area_akses ?? '');
                                $areas = array_filter(array_map('trim', explode(',', $normArea)));
                            @endphp
                            @forelse($areas as $area)
                                <span class="px-2 py-0.5 rounded-md font-bold text-[10.5px] tracking-wider bg-blue-50 text-blue-700 border border-blue-200/70 inline-block shadow-2xs">
                                    {{ $area }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400">-</span>
                            @endforelse
                        </div>
                    </td>

                    <!-- Jabatan -->
                    <td class="px-3 py-3.5">
                        <div class="text-xs text-slate-600 font-medium leading-snug line-clamp-2" title="{{ $kartu->jabatan ?? '-' }}">
                            {{ $kartu->jabatan ?? '-' }}
                        </div>
                    </td>

                    <!-- Masa Berlaku -->
                    <td class="px-3 py-3.5 whitespace-nowrap">
                        <div class="font-semibold text-slate-800 font-mono text-xs leading-tight">
                            {{ $kartu->tanggal_berlaku ? $kartu->tanggal_berlaku->format('d M Y') : '-' }}
                        </div>
                        @if($kartu->tanggal_berlaku)
                            @php $diff = (int) now()->diffInDays($kartu->tanggal_berlaku, false); @endphp
                            @if($diff < 0)
                                <div class="text-[10px] text-rose-600 font-bold flex items-center gap-1 mt-1 leading-none">
                                    <i class="fas fa-triangle-exclamation text-[9px]"></i>
                                    <span>Lewat {{ abs($diff) }} hr</span>
                                </div>
                            @elseif($diff <= 30)
                                <div class="text-[10px] text-amber-600 font-bold flex items-center gap-1 mt-1 leading-none">
                                    <i class="fas fa-clock text-[9px]"></i>
                                    <span>Sisa {{ $diff }} hr</span>
                                </div>
                            @else
                                <div class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1 mt-1 leading-none">
                                    <i class="fas fa-check text-[9px]"></i>
                                    <span>Berlaku</span>
                                </div>
                            @endif
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-2 py-3.5 whitespace-nowrap text-center">
                        @if($kartu->status === 'aktif')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Aktif
                            </span>
                        @elseif($kartu->status === 'kadaluarsa')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                Kadaluarsa
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs" title="{{ $kartu->keterangan_nonaktif ? 'Alasan: ' . ucfirst($kartu->keterangan_nonaktif) : 'Tidak Aktif' }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    <!-- Keterangan (Baru / Perpanjangan) -->
                    <td class="px-2 py-3.5 whitespace-nowrap text-center">
                        @php
                            $ket = $kartu->keterangan ?? ucfirst($kartu->tipe_permohonan ?? 'Baru');
                            $isPerpanjangan = str_contains(strtolower($ket), 'perpanjang');
                        @endphp
                        @if($isPerpanjangan)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                                <i class="fas fa-rotate text-[10px]"></i>
                                {{ $ket }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 shadow-2xs">
                                <i class="fas fa-plus text-[10px]"></i>
                                {{ $ket }}
                            </span>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="px-3 py-3.5 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <!-- QR Code -->
                            <a href="{{ route('administrator.kartu-pas.qrcode', $kartu->id) }}"
                               class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200/80 flex items-center justify-center text-xs transition-colors shadow-2xs"
                               title="Unduh QR Code">
                                <i class="fas fa-qrcode"></i>
                            </a>

                            <!-- Perpanjangan -->
                            <button type="button" data-kartu="{{ json_encode($kartu) }}" onclick="openModalPerpanjanganFromBtn(this)"
                                    class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-200/80 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer"
                                    title="Perpanjang Masa Berlaku">
                                <i class="fas fa-calendar-plus"></i>
                            </button>

                            <!-- Nonaktifkan / Aktifkan -->
                            @if($kartu->status === 'tidak_aktif')
                                <button type="button" onclick="confirmReaktifkan({{ $kartu->id }}, '{{ $kartu->nomor_kartu }}', '{{ addslashes($kartu->nama_pemegang) }}')"
                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-500 hover:text-emerald-700 border border-slate-300 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer"
                                        title="Kartu Nonaktif ({{ ucfirst($kartu->keterangan_nonaktif ?? 'Resign/Pensiun') }}). Klik untuk aktifkan kembali">
                                    <i class="fas fa-user-check"></i>
                                </button>
                            @else
                                <button type="button" onclick="openModalNonaktifkan({{ $kartu->id }}, '{{ $kartu->nomor_kartu }}', '{{ addslashes($kartu->nama_pemegang) }}', '{{ addslashes($kartu->perusahaan) }}')"
                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer"
                                        title="Nonaktifkan Kartu (Pensiun / Resign / Bebaskan Kuota)">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                            @endif

                            <!-- Edit -->
                            <button type="button" data-kartu="{{ json_encode($kartu) }}" onclick="openModalEditKartuFromBtn(this)"
                                    class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200/80 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer"
                                    title="Edit Data Kartu">
                                <i class="fas fa-pen-to-square"></i>
                            </button>

                            <!-- Hapus -->
                            <button type="button" onclick="confirmSingleDelete({{ $kartu->id }}, '{{ $kartu->nomor_kartu }}')"
                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition-colors shadow-2xs cursor-pointer"
                                    title="Hapus Kartu">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-2">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <p class="font-bold text-slate-700 text-sm">Tidak ada data kartu PAS ditemukan</p>
                            <p class="text-xs text-slate-400 mt-0.5">Silakan sesuaikan kriteria pencarian atau filter Anda.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Reusable Component Pagination -->
    <x-pagination :paginator="$kartuPas" />
</div>
