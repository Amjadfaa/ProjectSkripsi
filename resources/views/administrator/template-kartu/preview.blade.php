<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Pratinjau — {{ $template->nama_template }}</h2>
    </x-slot>

    <div class="space-y-6">

        {{-- Back & Template Switcher --}}
        <div class="flex items-center justify-between flex-wrap gap-3">
            <a href="{{ route('administrator.template-kartu.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Daftar Template
            </a>

            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-600">Ganti Template:</label>
                <select onchange="window.location.href = this.value;"
                        class="border-gray-300 rounded-lg shadow-sm text-xs font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($allTemplates as $tpl)
                        <option value="{{ route('administrator.template-kartu.preview', $tpl->id) }}" {{ $tpl->id === $template->id ? 'selected' : '' }}>
                            {{ $tpl->nama_template }} ({{ strtoupper($tpl->kode_warna) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Toolbar Simulasi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="font-bold text-base text-gray-800 flex items-center gap-2">
                        <i class="fas fa-sliders-h text-blue-600"></i> Uji Coba Data Pemegang
                    </h3>
                    <p class="text-xs text-gray-500">Pilih data kartu riil atau gunakan data simulasi</p>
                </div>

                <form method="GET" action="{{ route('administrator.template-kartu.preview', $template->id) }}" class="flex items-center gap-2 flex-wrap">
                    <select name="kartu_pas_id" onchange="this.form.submit()"
                            class="border-gray-300 rounded-lg shadow-sm text-xs font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Data Simulasi Standar --</option>
                        @foreach($sampleKartuList as $k)
                            <option value="{{ $k->id }}" {{ request('kartu_pas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nomor_kartu }} — {{ $k->nama_pemegang }} ({{ $k->perusahaan }}) [Area: {{ $k->area_akses }}]
                            </option>
                        @endforeach
                    </select>

                    @if(request('kartu_pas_id'))
                        <a href="{{ route('administrator.template-kartu.preview', $template->id) }}"
                           class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-bold transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Mockup Kartu & Detail --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Kolom Kiri: Kartu PAS Mockup --}}
            <div class="lg:col-span-6 flex flex-col items-center">
                <div class="w-full max-w-[380px]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                            <i class="fas fa-id-card text-amber-500"></i> Mockup Kartu PAS
                        </span>
                        <span class="text-[11px] font-mono text-gray-400">Rasio 1 : 1.58</span>
                    </div>

                    {{-- CARD CONTAINER --}}
                    <div id="cardMockup"
                         class="relative w-full rounded-2xl overflow-hidden shadow-2xl border-2 border-gray-200 select-none aspect-[1/1.58] transition-all"
                         style="background-color: {{ $template->warna_hex }};">

                        @if($template->gambar_template && file_exists(public_path('storage/' . $template->gambar_template)))
                            <img src="{{ asset('storage/' . $template->gambar_template) }}"
                                 alt="Background Template"
                                 class="absolute inset-0 w-full h-full object-cover">
                        @endif

                        @php $pos = $template->posisi; @endphp
                        
                        {{-- 1. FOTO PEMOHON --}}
                        @if($pos['foto']['visible'] ?? true)
                            <div class="absolute rounded-xl overflow-hidden border-2 border-black/40 shadow-sm bg-white/50 flex items-center justify-center z-10"
                                 style="top: {{ $pos['foto']['top'] }}%; left: {{ $pos['foto']['left'] }}%; width: {{ $pos['foto']['width'] }}%; height: {{ $pos['foto']['height'] }}%;">
                                @if($previewData['foto'])
                                    <img src="{{ $previewData['foto'] }}" alt="Foto Pemegang" class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-800 p-2 text-center">
                                        <i class="fas fa-user text-3xl opacity-70 mb-1"></i>
                                        <span class="text-[9px] font-black uppercase tracking-wider opacity-80">Pas Foto</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- 2. MASA BERLAKU --}}
                        @if($pos['masa_berlaku']['visible'] ?? true)
                            <div class="absolute font-mono font-black tracking-tight text-center z-10 drop-shadow-sm leading-none"
                                 style="top: {{ $pos['masa_berlaku']['top'] }}%; left: {{ $pos['masa_berlaku']['left'] }}%; width: {{ $pos['masa_berlaku']['width'] }}%; font-size: {{ $pos['masa_berlaku']['font_size'] ?? 11 }}px; color: {{ $pos['masa_berlaku']['color'] ?? '#FFFFFF' }};">
                                {{ $previewData['tanggal_berlaku'] }}
                            </div>
                        @endif

                        {{-- 3. AREA AKSES --}}
                        @if($pos['area_akses']['visible'] ?? true)
                            <div class="absolute font-mono font-black z-10 drop-shadow-sm flex {{ ($pos['area_akses']['direction'] ?? 'vertical') === 'horizontal' ? 'flex-row gap-1.5' : 'flex-col gap-1' }} items-center justify-center leading-none"
                                 style="top: {{ $pos['area_akses']['top'] }}%; left: {{ $pos['area_akses']['left'] }}%; width: {{ $pos['area_akses']['width'] }}%; font-size: {{ $pos['area_akses']['font_size'] ?? 20 }}px; color: {{ $pos['area_akses']['color'] ?? '#FFFFFF' }};">
                                @foreach($previewData['area_akses'] as $letter)
                                    <span class="block leading-tight drop-shadow-sm">{{ $letter }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- 4. NAMA PEMEGANG --}}
                        @if($pos['nama_pemegang']['visible'] ?? true)
                            <div class="absolute z-10 leading-tight drop-shadow-sm truncate {{ ($pos['nama_pemegang']['bold'] ?? true) ? 'font-black' : 'font-normal' }}"
                                 style="top: {{ $pos['nama_pemegang']['top'] }}%; left: {{ $pos['nama_pemegang']['left'] }}%; width: {{ $pos['nama_pemegang']['width'] }}%; font-size: {{ $pos['nama_pemegang']['font_size'] ?? 11 }}px; color: {{ $pos['nama_pemegang']['color'] ?? '#FFFFFF' }}; {{ ($pos['nama_pemegang']['uppercase'] ?? true) ? 'text-transform: uppercase;' : '' }}">
                                {{ $previewData['nama_pemegang'] }}
                            </div>
                        @endif

                        {{-- 5. JABATAN --}}
                        @if($pos['jabatan']['visible'] ?? true)
                            <div class="absolute z-10 leading-tight drop-shadow-sm truncate {{ ($pos['jabatan']['bold'] ?? false) ? 'font-black' : 'font-normal' }}"
                                 style="top: {{ $pos['jabatan']['top'] }}%; left: {{ $pos['jabatan']['left'] }}%; width: {{ $pos['jabatan']['width'] }}%; font-size: {{ $pos['jabatan']['font_size'] ?? 9 }}px; color: {{ $pos['jabatan']['color'] ?? '#FFFFFF' }}; {{ ($pos['jabatan']['uppercase'] ?? true) ? 'text-transform: uppercase;' : '' }}">
                                {{ $previewData['jabatan'] }}
                            </div>
                        @endif

                        {{-- 6. INSTANSI / PERUSAHAAN --}}
                        @if($pos['instansi']['visible'] ?? true)
                            <div class="absolute z-10 leading-tight drop-shadow-sm truncate {{ ($pos['instansi']['bold'] ?? false) ? 'font-black' : 'font-normal' }}"
                                 style="top: {{ $pos['instansi']['top'] }}%; left: {{ $pos['instansi']['left'] }}%; width: {{ $pos['instansi']['width'] }}%; font-size: {{ $pos['instansi']['font_size'] ?? 9 }}px; color: {{ $pos['instansi']['color'] ?? '#FFFFFF' }}; {{ ($pos['instansi']['uppercase'] ?? true) ? 'text-transform: uppercase;' : '' }}">
                                {{ $previewData['perusahaan'] }}
                            </div>
                        @endif

                        {{-- 7. NO REGISTRASI --}}
                        @if($pos['no_registrasi']['visible'] ?? true)
                            <div class="absolute font-mono z-10 leading-tight drop-shadow-sm truncate {{ ($pos['no_registrasi']['bold'] ?? false) ? 'font-black' : 'font-normal' }}"
                                 style="top: {{ $pos['no_registrasi']['top'] }}%; left: {{ $pos['no_registrasi']['left'] }}%; width: {{ $pos['no_registrasi']['width'] }}%; font-size: {{ $pos['no_registrasi']['font_size'] ?? 8.5 }}px; color: {{ $pos['no_registrasi']['color'] ?? '#FFFFFF' }};">
                                {{ $previewData['nomor_kartu'] }}
                            </div>
                        @endif

                        {{-- 8. QR CODE --}}
                        @if($pos['qr_code']['visible'] ?? true)
                            <div class="absolute rounded-lg bg-white p-1 shadow-md z-10 border border-black/30 flex items-center justify-center overflow-hidden"
                                 style="top: {{ $pos['qr_code']['top'] }}%; left: {{ $pos['qr_code']['left'] }}%; width: {{ $pos['qr_code']['width'] }}%; height: {{ $pos['qr_code']['height'] }}%;">
                                <svg class="w-full h-full text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm8-2h3v3h-3v-3zm5 0h3v3h-3v-3zm-5 5h3v3h-3v-3zm5 0h3v3h-3v-3zm2-3h3v3h-3v-3zm-7-2h2v2h-2v-2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Detail Informasi --}}
            <div class="lg:col-span-6 space-y-6">

                {{-- Spesifikasi Template --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-base text-gray-800 flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                        <i class="fas fa-cogs text-blue-600"></i> Detail Spesifikasi
                    </h3>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-lg bg-gray-50">
                            <span class="text-gray-400 font-semibold block uppercase text-[10px]">Nama Template</span>
                            <span class="font-bold text-gray-800 text-sm mt-0.5 block">{{ $template->nama_template }}</span>
                        </div>
                        <div class="p-3 rounded-lg bg-gray-50">
                            <span class="text-gray-400 font-semibold block uppercase text-[10px]">Tema Warna</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $template->warna_hex }};"></span>
                                <span class="font-bold text-gray-800">{{ $template->warna_label }} ({{ $template->warna_hex }})</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-lg bg-gray-50">
                            <span class="text-gray-400 font-semibold block uppercase text-[10px]">Status Template</span>
                            <span class="font-bold {{ $template->is_active ? 'text-emerald-600' : 'text-slate-500' }} text-sm mt-0.5 block">
                                {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    {{-- Area Akses --}}
                    <div class="pt-3 mt-3 border-t border-gray-100">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block mb-2">Area Akses:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @if(!empty($template->area_akses))
                                @foreach($template->area_akses as $kode)
                                    <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-gray-100 text-gray-700">Area {{ $kode }}</span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400 italic">Belum ada area khusus yang dialokasikan.</span>
                            @endif
                        </div>
                    </div>

                    {{-- Aksi Cepat --}}
                    <div class="pt-3 mt-3 border-t border-gray-100 flex flex-wrap items-center gap-2">
                        <a href="{{ route('administrator.template-kartu.designer', $template->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-bold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-wand-magic-sparkles text-amber-300"></i> Buka Desainer (Canva)
                        </a>
                        <a href="{{ route('administrator.template-kartu.edit', $template->id) }}"
                           class="bg-amber-500 hover:bg-amber-600 text-white py-2 px-4 rounded-lg text-sm font-bold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-edit"></i> Edit Template
                        </a>
                        <a href="{{ route('administrator.template-kartu.index') }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition">
                            Kembali
                        </a>
                    </div>
                </div>

                {{-- Gambar Template Asli --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-base text-gray-800 flex items-center gap-2 mb-3 pb-3 border-b border-gray-100">
                        <i class="fas fa-image text-gray-500"></i> File Gambar Template
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded text-[11px]">{{ $template->gambar_template }}</code>
                    </p>
                    <div class="p-4 bg-slate-900 rounded-xl flex items-center justify-center max-h-[260px] overflow-hidden">
                        <img src="{{ asset('storage/' . $template->gambar_template) }}" alt="Raw Template" class="max-h-[220px] object-contain">
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
