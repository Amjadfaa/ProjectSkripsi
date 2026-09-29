{{-- ======================================================================== --}}
{{-- TABLE PARTIAL FOR MANAJEMEN AKUN OPERATOR & PENGGUNA (SPA REUSABLE)       --}}
{{-- ======================================================================== --}}
<div class="w-full">
    <table class="w-full text-left text-xs border-collapse">
        <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80 select-none">
                <th scope="col" class="px-3.5 py-3">Pengguna</th>
                <th scope="col" class="px-3 py-3">Email Akun</th>
                <th scope="col" class="px-3 py-3 whitespace-nowrap">Role & Akses</th>
                <th scope="col" class="px-3 py-3">Kamera Ditugaskan</th>
                <th scope="col" class="px-3 py-3">Unit / Perusahaan</th>
                <th scope="col" class="px-3 py-3 whitespace-nowrap">Tanggal Dibuat</th>
                <th scope="col" class="px-2.5 py-3 text-center whitespace-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white text-xs">
            @forelse($users as $user)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    {{-- 1. Pengguna & Avatar --}}
                    <td class="px-3.5 py-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs {{ $user->role === 'administrator' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80' : 'bg-blue-50 text-blue-700 border border-blue-200/80' }} group-hover:scale-105 transition-transform">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 truncate">
                                <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5 truncate">
                                    <span class="truncate group-hover:text-blue-600 transition-colors" title="{{ $user->name }}">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="bg-amber-50 text-amber-700 text-[9px] font-bold px-1.5 py-0.2 rounded-full border border-amber-200 shrink-0">
                                            Anda
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-mono text-slate-400">
                                    ID: #{{ $user->id }}
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- 2. Email Akun --}}
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-1.5 text-slate-700 font-medium min-w-0" title="{{ $user->email }}">
                            <i class="far fa-envelope text-slate-400 text-xs shrink-0"></i>
                            <span class="truncate max-w-[130px] lg:max-w-[160px] xl:max-w-[200px]">{{ $user->email }}</span>
                        </div>
                    </td>

                    {{-- 3. Role & Akses --}}
                    <td class="px-3 py-3 whitespace-nowrap">
                        @if($user->role === 'operator')
                            <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/90 px-2 py-0.5 rounded-full text-[10px] font-bold shadow-2xs">
                                <i class="fas fa-user-shield text-[9px] text-blue-600"></i>
                                <span>Operator Scanner</span>
                            </span>
                        @elseif($user->role === 'administrator')
                            <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200/90 px-2 py-0.5 rounded-full text-[10px] font-bold shadow-2xs">
                                <i class="fas fa-crown text-[9px] text-amber-500"></i>
                                <span>Administrator</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                {{ ucfirst($user->role) }}
                            </span>
                        @endif
                    </td>

                    {{-- 4. Kamera Ditugaskan --}}
                    <td class="px-3 py-3">
                        @if($user->role === 'administrator')
                            <span class="text-slate-400 italic text-[11px] flex items-center gap-1 whitespace-nowrap">
                                <i class="fas fa-crown text-amber-500 text-[10px]"></i>
                                <span>Full Kontrol Sistem</span>
                            </span>
                        @else
                            @if($user->cameraDevices && $user->cameraDevices->isNotEmpty())
                                <div class="flex flex-wrap gap-1 max-w-[170px]">
                                    @foreach($user->cameraDevices as $dev)
                                        <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-200/90 px-1.5 py-0.5 rounded-md text-[10px] font-medium shadow-2xs"
                                              title="{{ $dev->nama_kamera }} (Area: {{ $dev->kode_area }})">
                                            <i class="fas fa-video text-[8px] text-purple-500"></i>
                                            <code class="font-mono font-bold">{{ $dev->kode_akses }}</code>
                                            <span class="text-[9px] font-black text-purple-800 bg-purple-100 px-0.5 rounded">[{{ $dev->kode_area }}]</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] font-bold shadow-2xs whitespace-nowrap">
                                    <i class="fas fa-triangle-exclamation text-[9px]"></i>
                                    <span>Belum Ada Kamera</span>
                                </span>
                            @endif
                        @endif
                    </td>

                    {{-- 5. Unit / Perusahaan --}}
                    <td class="px-3 py-3">
                        @if($user->perusahaan)
                            <div class="flex items-center gap-1 text-slate-700 font-medium min-w-0" title="{{ $user->perusahaan }}">
                                <i class="fas fa-building text-slate-400 text-xs shrink-0"></i>
                                <span class="truncate max-w-[110px] lg:max-w-[140px]">{{ $user->perusahaan }}</span>
                            </div>
                        @else
                            <span class="text-slate-400 italic text-[11px] whitespace-nowrap">- Semua Instansi -</span>
                        @endif
                    </td>

                    {{-- 6. Tanggal Dibuat --}}
                    <td class="px-3 py-3 whitespace-nowrap text-slate-500 text-[11px]">
                        <div class="flex items-center gap-1.5">
                            <i class="far fa-calendar-alt text-slate-400 text-xs"></i>
                            <span>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </td>

                    {{-- 7. Aksi (Assign Kamera, Edit Modal, Hapus Modal) --}}
                    <td class="px-2.5 py-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            {{-- Tombol Tugaskan & Kirim Kode Akses Kamera (Khusus Operator) --}}
                            @if($user->role === 'operator')
                                <button type="button" 
                                        data-user="{{ json_encode($user) }}"
                                        onclick="openModalAssignKameraFromBtn(this)"
                                        title="Tugaskan & Kirim Kode Akses Kamera"
                                        class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 flex items-center justify-center transition shadow-2xs cursor-pointer">
                                    <i class="fas fa-key text-amber-500 text-[11px]"></i>
                                </button>
                            @endif

                            {{-- Tombol Edit Akun (Modal Instan) --}}
                            <button type="button" 
                                    data-user="{{ json_encode($user) }}"
                                    onclick="openModalEditUserFromBtn(this)"
                                    title="Edit Data Akun"
                                    class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 border border-amber-200/80 flex items-center justify-center text-xs transition shadow-2xs cursor-pointer">
                                <i class="fas fa-pen text-[11px]"></i>
                            </button>

                            {{-- Tombol Hapus Akun --}}
                            @if($user->id !== auth()->id())
                                <button type="button" 
                                        onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')"
                                        title="Hapus Akun Pengguna"
                                        class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition shadow-2xs cursor-pointer">
                                    <i class="fas fa-trash-can text-[11px]"></i>
                                </button>
                            @else
                                <span title="Akun Anda yang sedang aktif saat ini" class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center cursor-not-allowed text-xs">
                                    <i class="fas fa-lock text-[11px]"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Tidak Ada Akun Pengguna Ditemukan</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            @if(request()->hasAny(['search', 'role']))
                                Tidak ada akun yang cocok dengan kata kunci atau filter pencarian Anda.
                            @else
                                Belum ada akun operator scanner atau pengguna lain yang terdaftar.
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
    <x-pagination :paginator="$users" />
</div>

{{-- METADATA UNTUK SPA STATS UPDATE --}}
<div id="spaKpiData" 
     data-total="{{ $totalUsers }}" 
     data-operator="{{ $totalOperator }}" 
     data-admin="{{ $totalAdmin }}" 
     data-kamera="{{ $totalKameraAssigned ?? 0 }}"
     class="hidden">
</div>
