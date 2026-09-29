<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-palette"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Template Kartu PAS
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">
                    Kelola desain latar belakang, indikator warna, dan alokasi eksklusif zona area akses bandara
                </p>
            </div>
        </div>
    </x-slot>

    {{-- ======================================================================== --}}
    {{-- HERO BANNER CARD (PERSIS SEPERTI DASHBOARD)                              --}}
    {{-- ======================================================================== --}}
    <div class="relative overflow-hidden rounded-2xl mb-6 text-white p-5 sm:p-7 shadow-lg border border-slate-800/40"
         style="background: linear-gradient(135deg, #090e1a 0%, #0f172a 40%, #1e3a8a 100%);">
        
        {{-- Decorative Ambient Light & Aviation Watermarks (Sama Seperti Dashboard) --}}
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <i class="fas fa-id-card text-[200px]"></i>
        </div>
        <div class="absolute right-1/4 -top-12 opacity-10 pointer-events-none">
            <i class="fas fa-shield-halved text-[160px]"></i>
        </div>
        <div class="absolute top-0 right-1/3 w-72 h-36 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2.5 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-blue-400/20 text-blue-200 border border-blue-300/30 text-[10.5px] font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    <span>Standarisasi Desain & Zonasi Kartu PAS Bandara</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    Template Kartu PAS Bandara (Maksimal 3 Template)
                </h2>
                <p class="text-blue-100/75 text-xs sm:text-sm leading-relaxed font-normal">
                    Kelola desain latar belakang kartu resmi, indikator warna bandara, dan pemetaan hak akses area terbatas. Sistem menerapkan validasi <strong>Zero Overlap</strong> tanpa template default, menjamin setiap zona bandara hanya dapat diakses dengan warna kartu yang ditentukan.
                </p>

                {{-- Badges info --}}
                <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 text-blue-100 border border-white/15">
                        <i class="fas fa-layer-group text-blue-300"></i> Maks. 3 Template Kartu
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 text-blue-100 border border-white/15">
                        <i class="fas fa-shield-halved text-emerald-300"></i> Zero Overlap (Bebas Duplikasi Area)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/10 text-blue-100 border border-white/15">
                        <i class="fas fa-map-location-dot text-amber-300"></i> 14 Zona Bandara Terpetakan
                    </span>
                </div>
            </div>

            {{-- Right CTA / Status Summary --}}
            <div class="flex flex-col sm:flex-row lg:flex-col items-start lg:items-end justify-between gap-3 shrink-0">
                {{-- Quick Status Pill --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 text-xs">
                    <span class="text-blue-100/80">Kapasitas Slot:</span>
                    <span class="font-bold {{ $totalTemplates >= 3 ? 'text-amber-300' : 'text-emerald-300' }}">
                        <i class="fas {{ $totalTemplates >= 3 ? 'fa-lock' : 'fa-circle-check' }} mr-1"></i>
                        {{ $totalTemplates }} / 3 Template Terpasang
                    </span>
                </div>

                {{-- Action Button --}}
                @if($totalTemplates >= 3)
                    <button type="button" disabled
                            title="Kapasitas template kartu telah mencapai batas maksimal (3 template). Hapus salah satu template terlebih dahulu jika ingin menambahkan template baru."
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-white/10 text-slate-400 border border-white/15 cursor-not-allowed">
                        <i class="fas fa-lock text-amber-400"></i>
                        <span>Kapasitas Penuh (3/3)</span>
                    </button>
                @else
                    <a href="{{ route('administrator.template-kartu.create') }}"
                       class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-xs px-4 py-2.5 rounded-xl shadow-md flex items-center gap-2 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                        <i class="fas fa-plus-circle text-slate-900"></i>
                        <span>Upload Template Baru</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- TIGA KARTU TEMPLATE PAS (KONSISTEN DENGAN DOKUMEN PERSYARATAN)           --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @forelse($templates as $template)
            @php
                $stripeGradient = match($template->kode_warna) {
                    'merah' => 'from-rose-500 via-red-600 to-rose-700',
                    'biru'  => 'from-blue-600 via-indigo-600 to-blue-700',
                    'kuning'=> 'from-amber-400 via-yellow-500 to-amber-600',
                    default => 'from-slate-600 to-slate-800',
                };
                $numberBox = match($template->kode_warna) {
                    'merah' => 'bg-rose-50 text-rose-600 border-rose-200/80',
                    'biru'  => 'bg-blue-50 text-blue-600 border-blue-200/80',
                    'kuning'=> 'bg-amber-50 text-amber-700 border-amber-200/80',
                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                };
                $badgeTag = match($template->kode_warna) {
                    'merah' => 'text-rose-700 bg-rose-50 border-rose-200/80',
                    'biru'  => 'text-blue-700 bg-blue-50 border-blue-200/80',
                    'kuning'=> 'text-amber-800 bg-amber-50 border-amber-200/80',
                    default => 'text-slate-700 bg-slate-50 border-slate-200',
                };
                $areaChipClass = match($template->kode_warna) {
                    'merah' => 'bg-rose-50 text-rose-700 border-rose-200/80 hover:bg-rose-100',
                    'biru'  => 'bg-blue-50 text-blue-700 border-blue-200/80 hover:bg-blue-100',
                    'kuning'=> 'bg-amber-50 text-amber-800 border-amber-200/80 hover:bg-amber-100',
                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                };
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col transition hover:shadow-md group">
                {{-- Top Stripe --}}
                <div class="h-1.5 bg-gradient-to-r {{ $stripeGradient }}"></div>

                {{-- Card Header --}}
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl border font-black text-sm flex items-center justify-center shrink-0 shadow-2xs {{ $numberBox }}">
                            {{ $loop->iteration }}
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md border {{ $badgeTag }}">
                                PAS {{ strtoupper($template->kode_warna) }}
                            </span>
                            <h2 class="font-bold text-sm text-slate-800 mt-0.5 truncate tracking-tight">
                                {{ $template->nama_template }}
                            </h2>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div>
                        @if($template->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between gap-4">
                    {{-- Container Gambar Latar Belakang Template --}}
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-3 flex flex-col items-center justify-center relative overflow-hidden group/img cursor-pointer"
                         onclick="window.location.href='{{ route('administrator.template-kartu.designer', $template->id) }}'">
                        <div class="w-36 sm:w-40 aspect-[1/1.55] rounded-xl overflow-hidden shadow-sm border border-slate-200/80 bg-white relative transition-transform duration-300 group-hover/img:scale-105 flex items-center justify-center">
                            @if($template->gambar_template)
                                <img src="{{ $template->gambar_url }}" alt="{{ $template->nama_template }}" class="w-full h-full object-cover">
                                
                                {{-- Dynamic Data Layers Rendered According to $template->posisi --}}
                                @php $pos = $template->posisi; @endphp
                                
                                {{-- Foto Box --}}
                                @if($pos['foto']['visible'] ?? true)
                                    <div class="absolute rounded bg-white/50 border border-black/30 flex items-center justify-center text-slate-700 overflow-hidden pointer-events-none"
                                         style="top: {{ $pos['foto']['top'] }}%; left: {{ $pos['foto']['left'] }}%; width: {{ $pos['foto']['width'] }}%; height: {{ $pos['foto']['height'] }}%;">
                                        <i class="fas fa-user text-[10px] opacity-70"></i>
                                    </div>
                                @endif

                                {{-- Masa Berlaku --}}
                                @if($pos['masa_berlaku']['visible'] ?? true)
                                    <div class="absolute font-mono font-black pointer-events-none text-center leading-none"
                                         style="top: {{ $pos['masa_berlaku']['top'] }}%; left: {{ $pos['masa_berlaku']['left'] }}%; width: {{ $pos['masa_berlaku']['width'] }}%; font-size: 5px; color: {{ $pos['masa_berlaku']['color'] }};">
                                        30 MAY 2027
                                    </div>
                                @endif

                                {{-- Area Akses --}}
                                @if($pos['area_akses']['visible'] ?? true)
                                    <div class="absolute font-mono font-black pointer-events-none flex flex-col items-center gap-0.5 leading-none"
                                         style="top: {{ $pos['area_akses']['top'] }}%; left: {{ $pos['area_akses']['left'] }}%; width: {{ $pos['area_akses']['width'] }}%; font-size: 9px; color: {{ $pos['area_akses']['color'] }};">
                                        @foreach(array_slice($template->area_akses ?? ['A', 'B', 'C'], 0, 4) as $l)
                                            <span>{{ $l }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Nama Pemegang --}}
                                @if($pos['nama_pemegang']['visible'] ?? true)
                                    <div class="absolute font-black truncate pointer-events-none leading-none"
                                         style="top: {{ $pos['nama_pemegang']['top'] }}%; left: {{ $pos['nama_pemegang']['left'] }}%; width: {{ $pos['nama_pemegang']['width'] }}%; font-size: 5.5px; color: {{ $pos['nama_pemegang']['color'] }};">
                                        AMJAD FAAHIM
                                    </div>
                                @endif

                                {{-- Jabatan --}}
                                @if($pos['jabatan']['visible'] ?? true)
                                    <div class="absolute truncate pointer-events-none leading-none"
                                         style="top: {{ $pos['jabatan']['top'] }}%; left: {{ $pos['jabatan']['left'] }}%; width: {{ $pos['jabatan']['width'] }}%; font-size: 4.5px; color: {{ $pos['jabatan']['color'] }};">
                                        AVSEC OFFICER
                                    </div>
                                @endif

                                {{-- Instansi --}}
                                @if($pos['instansi']['visible'] ?? true)
                                    <div class="absolute truncate pointer-events-none leading-none"
                                         style="top: {{ $pos['instansi']['top'] }}%; left: {{ $pos['instansi']['left'] }}%; width: {{ $pos['instansi']['width'] }}%; font-size: 4.5px; color: {{ $pos['instansi']['color'] }};">
                                        UPBU MOPAH MERAUKE
                                    </div>
                                @endif

                                {{-- No Registrasi --}}
                                @if($pos['no_registrasi']['visible'] ?? true)
                                    <div class="absolute font-mono truncate pointer-events-none leading-none"
                                         style="top: {{ $pos['no_registrasi']['top'] }}%; left: {{ $pos['no_registrasi']['left'] }}%; width: {{ $pos['no_registrasi']['width'] }}%; font-size: 4px; color: {{ $pos['no_registrasi']['color'] }};">
                                        1234/PAS-MOPAH/2026
                                    </div>
                                @endif

                                {{-- QR Code --}}
                                @if($pos['qr_code']['visible'] ?? true)
                                    <div class="absolute rounded bg-white p-0.5 flex items-center justify-center pointer-events-none shadow-2xs"
                                         style="top: {{ $pos['qr_code']['top'] }}%; left: {{ $pos['qr_code']['left'] }}%; width: {{ $pos['qr_code']['width'] }}%; height: {{ $pos['qr_code']['height'] }}%;">
                                        <svg class="w-full h-full text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm8-2h3v3h-3v-3zm5 0h3v3h-3v-3zm-5 5h3v3h-3v-3zm5 0h3v3h-3v-3zm2-3h3v3h-3v-3zm-7-2h2v2h-2v-2z"/>
                                        </svg>
                                    </div>
                                @endif
                            @else
                                <div class="text-slate-300 text-center p-3">
                                    <i class="fas fa-id-card text-4xl mb-1"></i>
                                    <p class="text-[10px]">Gambar Template</p>
                                </div>
                            @endif
                        </div>
                        <span class="absolute inset-0 bg-black/40 rounded-2xl opacity-0 group-hover/img:opacity-100 flex items-center justify-center text-white text-xs font-semibold gap-1.5 transition-opacity">
                            <i class="fas fa-wand-magic-sparkles text-amber-300"></i>
                            <span>Buka Desainer Tata Letak</span>
                        </span>
                    </div>

                    {{-- Informasi Warna --}}
                    <div class="flex items-center justify-between text-xs bg-slate-50/70 px-3.5 py-2.5 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full border border-black/10 shadow-2xs shrink-0" 
                                  style="background-color: {{ $template->warna_hex }};"></span>
                            <span class="font-bold text-slate-800 capitalize">{{ $template->kode_warna }}</span>
                            <code class="text-[10px] text-slate-400 font-mono font-medium">{{ strtoupper($template->warna_hex) }}</code>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200/80 shadow-2xs">
                            {{ count($template->area_akses ?? []) }} Zona Akses
                        </span>
                    </div>

                    {{-- Area Akses Terkait --}}
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-2">
                            <span>Area Akses Terkait:</span>
                            <span class="text-[11px] font-semibold text-slate-400">
                                {{ count($template->area_akses ?? []) }} Zona Terdaftar
                            </span>
                        </div>
                        @if(is_array($template->area_akses) && count($template->area_akses) > 0)
                            <div class="flex flex-wrap items-center gap-1.5">
                                @foreach($template->area_akses as $areaCode)
                                    @php $areaObj = $allAreas->get($areaCode); @endphp
                                    <span class="inline-flex items-center justify-center font-mono font-bold text-[11px] px-2.5 py-1 rounded-lg border shadow-2xs transition-colors cursor-default {{ $areaChipClass }}"
                                          title="{{ $areaObj ? $areaObj->keterangan : 'Area ' . $areaCode }}">
                                        {{ $areaCode }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Belum ada area akses dialokasikan</p>
                        @endif
                    </div>

                    {{-- File Actions Toolbar --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('administrator.template-kartu.designer', $template->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition cursor-pointer"
                               title="Atur posisi data di atas background kartu (Canva Editor)">
                                <i class="fas fa-wand-magic-sparkles text-amber-300"></i>
                                <span>Desainer</span>
                            </a>
                            <a href="{{ route('administrator.template-kartu.preview', $template->id) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white shadow-xs transition cursor-pointer">
                                <i class="fas fa-eye text-indigo-300"></i>
                                <span>Mockup</span>
                            </a>
                            <a href="{{ route('administrator.template-kartu.edit', $template->id) }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs transition">
                                <i class="fas fa-pen text-amber-600"></i>
                                <span>Edit</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <form method="POST" action="{{ route('administrator.template-kartu.toggle-status', $template->id) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" 
                                        class="w-8 h-8 rounded-xl {{ $template->is_active ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border-emerald-200/80' : 'bg-slate-100 hover:bg-slate-200 text-slate-500 border-slate-200' }} border flex items-center justify-center text-xs transition shadow-2xs cursor-pointer"
                                        title="{{ $template->is_active ? 'Nonaktifkan Template' : 'Aktifkan Template' }}">
                                    <i class="fas {{ $template->is_active ? 'fa-toggle-on text-base' : 'fa-toggle-off text-base' }}"></i>
                                </button>
                            </form>

                            <button type="button" 
                                    onclick="confirmDeleteTemplate({{ $template->id }}, '{{ addslashes($template->nama_template) }}')"
                                    class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/80 flex items-center justify-center text-xs transition shadow-2xs cursor-pointer"
                                    title="Hapus Template">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-2xl border border-slate-200/80 p-12 text-center text-slate-400">
                <i class="fas fa-id-card text-5xl mb-3 text-slate-300"></i>
                <p class="font-bold text-slate-700 text-base">Belum Ada Template Kartu Terdaftar</p>
                <p class="text-xs text-slate-400 mt-1">Silakan upload template kartu baru melalui tombol di atas.</p>
            </div>
        @endforelse
    </div>

    {{-- ======================================================================== --}}
    {{-- PANEL PEMETAAN 14 ZONA AREA AKSES (ZERO OVERLAP)                         --}}
    {{-- ======================================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mb-6">
        <div class="h-1.5 bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600"></div>

        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-slate-800 tracking-tight">
                        Peta Distribusi 14 Zona Area Akses Bandara (Zero Overlap)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Setiap area akses terbatas dialokasikan secara eksklusif ke satu warna kartu tanpa pendobelan izin.
                    </p>
                </div>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs self-start sm:self-auto">
                <i class="fas fa-circle-check text-[11px]"></i>
                <span>100% Bebas Konflik Area</span>
            </span>
        </div>

        <div class="p-5 sm:p-6 bg-slate-50/40">
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($allAreas as $kode => $area)
                    @php
                        $assignedTpl = $areaAssignment[strtoupper($kode)] ?? null;
                        $cardBorder = match(optional($assignedTpl)->kode_warna) {
                            'merah' => 'border-rose-200/90 bg-white hover:bg-rose-50/40 hover:border-rose-300',
                            'biru'  => 'border-blue-200/90 bg-white hover:bg-blue-50/40 hover:border-blue-300',
                            'kuning'=> 'border-amber-200/90 bg-white hover:bg-amber-50/40 hover:border-amber-300',
                            default => 'border-slate-200 bg-white hover:bg-slate-50',
                        };
                        $badgeColor = match(optional($assignedTpl)->kode_warna) {
                            'merah' => 'bg-rose-600 text-white',
                            'biru'  => 'bg-blue-600 text-white',
                            'kuning'=> 'bg-amber-500 text-slate-900',
                            default => 'bg-slate-300 text-slate-700',
                        };
                    @endphp
                    <div class="p-3 rounded-2xl border {{ $cardBorder }} transition-all flex flex-col justify-between shadow-2xs group bg-white">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-mono font-black text-xs px-2 py-0.5 rounded-lg {{ $badgeColor }} shadow-2xs">
                                {{ $area->kode }}
                            </span>
                            @if($assignedTpl)
                                <span class="w-2.5 h-2.5 rounded-full shadow-2xs" style="background-color: {{ $assignedTpl->warna_hex }};"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                            @endif
                        </div>
                        <p class="text-xs font-bold text-slate-800 line-clamp-1 group-hover:text-blue-600 transition-colors" title="{{ $area->keterangan }}">
                            {{ $area->keterangan }}
                        </p>
                        <p class="text-[11px] text-slate-500 font-medium mt-1 truncate">
                            @if($assignedTpl)
                                <span class="font-bold capitalize text-slate-700">PAS {{ $assignedTpl->kode_warna }}</span>
                            @else
                                <span class="text-slate-400 italic">Belum Ada</span>
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- MODAL PRATINJAU GAMBAR TEMPLATE (LIGHTBOX)                               --}}
    {{-- ======================================================================== --}}
    <x-modal id="modalTemplatePreview" maxWidth="max-w-md" title="Pratinjau Desain Template" subtitle="Tampilan visual latar belakang kartu PAS resolusi asli" icon="fas fa-image" iconColor="bg-indigo-50 text-indigo-600 border-indigo-200">
        <div class="flex flex-col items-center justify-center p-2">
            <div class="w-full max-w-[280px] aspect-[1/1.55] rounded-2xl overflow-hidden shadow-2xl border border-slate-200 bg-slate-900 flex items-center justify-center">
                <img id="previewModalImage" src="" alt="Pratinjau Template" class="w-full h-full object-contain">
            </div>
            <p id="previewModalTitle" class="mt-4 font-bold text-slate-800 text-center text-sm"></p>
        </div>
        <x-slot name="footer">
            <button type="button" onclick="closeModal('modalTemplatePreview')"
                    class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                Tutup
            </button>
        </x-slot>
    </x-modal>

    {{-- ======================================================================== --}}
    {{-- REUSABLE MODAL DELETE COMPONENT                                          --}}
    {{-- ======================================================================== --}}
    <x-modal-delete id="deleteTemplateModal" />

    {{-- ======================================================================== --}}
    {{-- JAVASCRIPT HANDLERS                                                      --}}
    {{-- ======================================================================== --}}
    <script>
        function confirmDeleteTemplate(id, name) {
            if (typeof window.openDeleteModal === 'function') {
                window.openDeleteModal({
                    id: 'deleteTemplateModal',
                    title: 'Hapus Template Kartu PAS',
                    message: 'Apakah Anda yakin ingin menghapus template kartu ini? Kartu PAS yang telah diterbitkan tidak akan terpengaruh, namun template tidak akan dapat dipilih lagi.',
                    targetName: name,
                    action: "{{ url('/administrator/template-kartu') }}/" + id,
                    btnText: 'Hapus Template'
                });
            } else {
                if (confirm(`Hapus template kartu "${name}"?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('/administrator/template-kartu') }}/" + id;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }

        function openPreviewModal(imgSrc, title) {
            document.getElementById('previewModalImage').src = imgSrc;
            document.getElementById('previewModalTitle').textContent = title;
            openModal('modalTemplatePreview');
        }
    </script>
</x-app-layout>
