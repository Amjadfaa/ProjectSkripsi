<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('administrator.template-kartu.index') }}" 
                   class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition shadow-2xs"
                   title="Kembali ke Daftar Template">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $template->warna_hex }};"></span>
                        <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                            Desainer Tata Letak Kartu PAS: {{ $template->nama_template }}
                        </h1>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                            PAS {{ strtoupper($template->kode_warna) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Klik dan geser (drag & drop) elemen data di atas canvas kartu untuk mengatur posisi secara visual.
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2">
                <button type="button" 
                        onclick="resetToDefaultLayout()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200 shadow-2xs transition cursor-pointer">
                    <i class="fas fa-rotate-left"></i>
                    <span>Reset Bawaan</span>
                </button>
                <a href="{{ route('administrator.template-kartu.preview', $template->id) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-2xs transition cursor-pointer">
                    <i class="fas fa-eye text-indigo-500"></i>
                    <span>Pratinjau Hasil</span>
                </a>
                <button type="button" 
                        id="btnSaveLayout"
                        onclick="saveCardLayout()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-floppy-disk"></i>
                    <span>Simpan Tata Letak</span>
                </button>
            </div>
        </div>
    </x-slot>

    {{-- Sub Header: Template Switcher & Live Data Selector --}}
    <div class="mb-5 bg-white rounded-2xl border border-slate-200/80 p-3 px-4 shadow-xs flex flex-wrap items-center justify-between gap-3">
        {{-- Template Switcher Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Template:</span>
            @foreach($allTemplates as $tpl)
                <a href="{{ route('administrator.template-kartu.designer', $tpl->id) }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold border transition {{ $tpl->id === $template->id ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200' }}">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $tpl->warna_hex }};"></span>
                    <span class="capitalize">PAS {{ $tpl->kode_warna }}</span>
                </a>
            @endforeach
        </div>

        {{-- Sample Data Dropdown --}}
        <div class="flex items-center gap-2">
            <label for="kartuPasSelector" class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">
                <i class="fas fa-user-check text-blue-600 mr-1"></i> Data Pemohon:
            </label>
            <select id="kartuPasSelector" 
                    onchange="changeSampleCardData(this.value)"
                    class="text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="default">-- Data Uji Coba (Default) --</option>
                @foreach($sampleKartuList as $k)
                    <option value="{{ $k->id }}" 
                            data-nama="{{ $k->nama_pemegang }}"
                            data-nomor="{{ $k->nomor_kartu }}"
                            data-instansi="{{ $k->perusahaan }}"
                            data-jabatan="{{ $k->jabatan ?? '-' }}"
                            data-berlaku="{{ $k->tanggal_berlaku ? $k->tanggal_berlaku->format('d M Y') : '30 MAY 2027' }}"
                            data-area="{{ $k->area_akses ?? 'A, B, C' }}"
                            data-foto="{{ $k->foto ? asset('storage/' . $k->foto) : '' }}"
                            {{ $selectedKartu && $selectedKartu->id === $k->id ? 'selected' : '' }}>
                        {{ $k->nama_pemegang }} - {{ $k->nomor_kartu }} ({{ $k->perusahaan }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Main Workspace Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ========================================================================= --}}
        {{-- KOLOM KIRI: CANVAS WORKSPACE (CANVA STUDIO)                               --}}
        {{-- ========================================================================= --}}
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col items-center">
            
            {{-- Toolbar Atas Canvas --}}
            <div class="w-full bg-slate-900 text-white rounded-t-2xl px-4 py-2.5 flex items-center justify-between text-xs font-medium border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-slate-300">
                        <i class="fas fa-crop-simple text-blue-400"></i>
                        <span>Canvas ID-Card: <strong>416 &times; 642 px</strong></span>
                    </span>
                    <span class="text-slate-600">|</span>
                    <span class="text-slate-400 hidden sm:inline">Rasio CR-80 Standar Pas Bandara</span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="activeElementLabel" class="text-slate-400 italic text-[11px]">
                        Klik elemen untuk mulai menggeser
                    </span>
                </div>
            </div>

            {{-- Board Stage --}}
            <div class="w-full bg-slate-800/95 p-6 sm:p-10 rounded-b-2xl shadow-xl flex items-center justify-center relative overflow-hidden select-none border border-slate-700/60"
                 id="studioWorkspace">
                
                {{-- Grid pattern background --}}
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

                {{-- CARD CANVAS CONTAINER (Aspect ratio ~1 : 1.543, 380px x 586px) --}}
                <div id="cardCanvas" 
                     class="relative w-[360px] sm:w-[380px] h-[555px] sm:h-[586px] rounded-2xl shadow-2xl overflow-hidden bg-white border-2 border-slate-400/80 cursor-default"
                     style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">

                    {{-- Background Image Template --}}
                    @if($template->gambar_url)
                        <img id="canvasBgImage" 
                             src="{{ $template->gambar_url }}" 
                             alt="{{ $template->nama_template }}" 
                             class="absolute inset-0 w-full h-full object-cover pointer-events-none z-0">
                    @else
                        <div class="absolute inset-0 bg-slate-200 flex items-center justify-center text-slate-400">
                            <span>Background Template Kosong</span>
                        </div>
                    @endif

                    {{-- ============================================================= --}}
                    {{-- DRAGGABLE LAYERS OVERLAY                                      --}}
                    {{-- ============================================================= --}}

                    {{-- 1. LAYER FOTO PEMOHON --}}
                    <div id="layer_foto" 
                         class="draggable-layer absolute cursor-move transition-shadow z-20 group"
                         onclick="selectLayer('foto', event)">
                        <div class="layer-handle-box w-full h-full rounded-xl overflow-hidden border-2 border-transparent relative bg-white/40 backdrop-blur-[1px] shadow-sm flex items-center justify-center">
                            <img id="display_foto_img" 
                                 src="{{ asset('images/default-avatar.png') }}" 
                                 alt="Pas Foto" 
                                 class="w-full h-full object-cover hidden">
                            <div id="display_foto_placeholder" class="text-center p-2 text-slate-800">
                                <i class="fas fa-user text-3xl opacity-60"></i>
                                <span class="block text-[9px] font-black uppercase tracking-wider mt-1 opacity-75">Pas Foto</span>
                            </div>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            Pas Foto
                        </span>
                    </div>

                    {{-- 2. LAYER MASA BERLAKU --}}
                    <div id="layer_masa_berlaku" 
                         class="draggable-layer absolute cursor-move z-20 group text-center"
                         onclick="selectLayer('masa_berlaku', event)">
                        <div class="layer-handle-box px-1 py-0.5 rounded border border-transparent whitespace-nowrap">
                            <span id="display_masa_berlaku" class="font-mono font-black drop-shadow-sm tracking-tight leading-none block">
                                {{ $previewData['tanggal_berlaku'] }}
                            </span>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            Masa Berlaku
                        </span>
                    </div>

                    {{-- 3. LAYER AREA AKSES --}}
                    <div id="layer_area_akses" 
                         class="draggable-layer absolute cursor-move z-20 group"
                         onclick="selectLayer('area_akses', event)">
                        <div class="layer-handle-box px-1.5 py-1 rounded border border-transparent flex flex-col items-center justify-center leading-none">
                            <div id="display_area_akses_container" class="flex flex-col items-center gap-1 font-black font-mono drop-shadow-sm">
                                @foreach($previewData['area_akses'] as $letter)
                                    <span class="area-chip block leading-tight">{{ $letter }}</span>
                                @endforeach
                            </div>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            Area Akses
                        </span>
                    </div>

                    {{-- 4. LAYER NAMA PEMEGANG --}}
                    <div id="layer_nama_pemegang" 
                         class="draggable-layer absolute cursor-move z-20 group"
                         onclick="selectLayer('nama_pemegang', event)">
                        <div class="layer-handle-box px-1 py-0.5 rounded border border-transparent">
                            <p id="display_nama_pemegang" class="font-black leading-tight drop-shadow-sm truncate">
                                {{ $previewData['nama_pemegang'] }}
                            </p>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            Nama Pemegang
                        </span>
                    </div>

                    {{-- 5. LAYER JABATAN --}}
                    <div id="layer_jabatan" 
                         class="draggable-layer absolute cursor-move z-20 group"
                         onclick="selectLayer('jabatan', event)">
                        <div class="layer-handle-box px-1 py-0.5 rounded border border-transparent">
                            <p id="display_jabatan" class="leading-tight drop-shadow-sm truncate">
                                {{ $previewData['jabatan'] }}
                            </p>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            Jabatan
                        </span>
                    </div>

                    {{-- 6. LAYER INSTANSI / PERUSAHAAN --}}
                    <div id="layer_instansi" 
                         class="draggable-layer absolute cursor-move z-20 group"
                         onclick="selectLayer('instansi', event)">
                        <div class="layer-handle-box px-1 py-0.5 rounded border border-transparent">
                            <p id="display_instansi" class="leading-tight drop-shadow-sm truncate">
                                {{ $previewData['perusahaan'] }}
                            </p>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            Instansi / Perusahaan
                        </span>
                    </div>

                    {{-- 7. LAYER NO. REGISTRASI / KARTU --}}
                    <div id="layer_no_registrasi" 
                         class="draggable-layer absolute cursor-move z-20 group"
                         onclick="selectLayer('no_registrasi', event)">
                        <div class="layer-handle-box px-1 py-0.5 rounded border border-transparent">
                            <p id="display_no_registrasi" class="font-mono leading-tight drop-shadow-sm truncate">
                                {{ $previewData['nomor_kartu'] }}
                            </p>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            No. Registrasi
                        </span>
                    </div>

                    {{-- 8. LAYER QR CODE --}}
                    <div id="layer_qr_code" 
                         class="draggable-layer absolute cursor-move z-20 group"
                         onclick="selectLayer('qr_code', event)">
                        <div class="layer-handle-box w-full h-full rounded-lg bg-white p-1 shadow-sm border border-transparent flex items-center justify-center overflow-hidden">
                            <svg class="w-full h-full text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm8-2h3v3h-3v-3zm5 0h3v3h-3v-3zm-5 5h3v3h-3v-3zm5 0h3v3h-3v-3zm2-3h3v3h-3v-3zm-7-2h2v2h-2v-2z"/>
                            </svg>
                        </div>
                        <span class="layer-badge absolute -top-5 left-0 bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                            QR Code
                        </span>
                    </div>

                </div>

            </div>

            {{-- Tips Keyboard --}}
            <div class="mt-3 text-xs text-slate-400 flex items-center gap-4">
                <span><i class="fas fa-keyboard text-slate-400 mr-1"></i> Gunakan tombol panah keyboard (&uarr; &darr; &larr; &rarr;) untuk menggeser elemen terpilih secara presisi.</span>
            </div>

        </div>

        {{-- ========================================================================= --}}
        {{-- KOLOM KANAN: INSPECTOR & CONTROLS PANEL                                   --}}
        {{-- ========================================================================= --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">
            
            {{-- Card 1: Layers Quick Selector --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-600"></i>
                        <span>Daftar Elemen (Layers)</span>
                    </h3>
                    <span class="text-[11px] font-semibold text-slate-400">8 Elemen</span>
                </div>

                <div class="grid grid-cols-2 gap-1.5">
                    @php
                        $layerButtons = [
                            ['key' => 'foto', 'label' => 'Pas Foto', 'icon' => 'fa-image'],
                            ['key' => 'area_akses', 'label' => 'Area Akses', 'icon' => 'fa-location-dot'],
                            ['key' => 'masa_berlaku', 'label' => 'Masa Berlaku', 'icon' => 'fa-calendar-days'],
                            ['key' => 'nama_pemegang', 'label' => 'Nama Pemegang', 'icon' => 'fa-user'],
                            ['key' => 'jabatan', 'label' => 'Jabatan', 'icon' => 'fa-id-badge'],
                            ['key' => 'instansi', 'label' => 'Instansi', 'icon' => 'fa-building'],
                            ['key' => 'no_registrasi', 'label' => 'No. Registrasi', 'icon' => 'fa-hashtag'],
                            ['key' => 'qr_code', 'label' => 'QR Code', 'icon' => 'fa-qrcode'],
                        ];
                    @endphp
                    @foreach($layerButtons as $btn)
                        <button type="button" 
                                id="layerBtn_{{ $btn['key'] }}"
                                onclick="selectLayer('{{ $btn['key'] }}')"
                                class="layer-pill flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold border transition text-left cursor-pointer bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200">
                            <i class="fas {{ $btn['icon'] }} text-slate-400 w-4"></i>
                            <span class="truncate">{{ $btn['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Card 2: Element Inspector (Controls) --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span id="inspectorIcon" class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                            <i class="fas fa-sliders"></i>
                        </span>
                        <div>
                            <h3 id="inspectorTitle" class="font-bold text-sm text-slate-800 leading-tight">
                                Pengaturan Elemen
                            </h3>
                            <p id="inspectorSubtitle" class="text-[11px] text-slate-400">
                                Pilih salah satu elemen di canvas
                            </p>
                        </div>
                    </div>

                    {{-- Visibility Toggle --}}
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-600">
                        <input type="checkbox" id="ctrl_visible" onchange="updateCurrentLayerProperty('visible', this.checked)" class="rounded text-blue-600 focus:ring-blue-500">
                        <span>Tampilkan</span>
                    </label>
                </div>

                {{-- Form Controls --}}
                <div class="space-y-4">
                    
                    {{-- Position X and Y Sliders --}}
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                            <label for="ctrl_left">Posisi Horizontal (X / Kiri %):</label>
                            <span id="val_left" class="font-mono text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded text-[11px]">0%</span>
                        </div>
                        <input type="range" id="ctrl_left" min="0" max="100" step="0.5" 
                               oninput="updateCurrentLayerPosition('left', parseFloat(this.value))"
                               class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                            <label for="ctrl_top">Posisi Vertikal (Y / Atas %):</label>
                            <span id="val_top" class="font-mono text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded text-[11px]">0%</span>
                        </div>
                        <input type="range" id="ctrl_top" min="0" max="100" step="0.5" 
                               oninput="updateCurrentLayerPosition('top', parseFloat(this.value))"
                               class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                    </div>

                    {{-- Dimension Width / Height (for box elements) --}}
                    <div id="section_dimension" class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                                <label for="ctrl_width">Lebar (%):</label>
                                <span id="val_width" class="font-mono text-slate-500 text-[11px]">0%</span>
                            </div>
                            <input type="range" id="ctrl_width" min="5" max="100" step="0.5" 
                                   oninput="updateCurrentLayerProperty('width', parseFloat(this.value))"
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        </div>
                        <div id="group_height">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                                <label for="ctrl_height">Tinggi (%):</label>
                                <span id="val_height" class="font-mono text-slate-500 text-[11px]">0%</span>
                            </div>
                            <input type="range" id="ctrl_height" min="5" max="100" step="0.5" 
                                   oninput="updateCurrentLayerProperty('height', parseFloat(this.value))"
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        </div>
                    </div>

                    {{-- Typography Controls (for text elements) --}}
                    <div id="section_typography" class="space-y-3 pt-2 border-t border-slate-100">
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                                <label for="ctrl_font_size">Ukuran Huruf (Font Size):</label>
                                <span id="val_font_size" class="font-mono text-slate-700 text-[11px]">12px</span>
                            </div>
                            <input type="range" id="ctrl_font_size" min="7" max="36" step="0.5" 
                                   oninput="updateCurrentLayerProperty('font_size', parseFloat(this.value))"
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        </div>

                        {{-- Text Color Picker & Presets --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Warna Teks:</label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="ctrl_color" 
                                       onchange="updateCurrentLayerProperty('color', this.value)"
                                       class="w-8 h-8 rounded-lg border border-slate-300 cursor-pointer p-0.5">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="updateCurrentLayerProperty('color', '#FFFFFF')" class="w-6 h-6 rounded-md bg-white border border-slate-300 shadow-2xs cursor-pointer" title="Putih"></button>
                                    <button type="button" onclick="updateCurrentLayerProperty('color', '#000000')" class="w-6 h-6 rounded-md bg-black border border-slate-800 shadow-2xs cursor-pointer" title="Hitam"></button>
                                    <button type="button" onclick="updateCurrentLayerProperty('color', '#FACC15')" class="w-6 h-6 rounded-md bg-yellow-400 border border-yellow-500 shadow-2xs cursor-pointer" title="Kuning"></button>
                                    <button type="button" onclick="updateCurrentLayerProperty('color', '#EF4444')" class="w-6 h-6 rounded-md bg-red-500 border border-red-600 shadow-2xs cursor-pointer" title="Merah"></button>
                                    <button type="button" onclick="updateCurrentLayerProperty('color', '#1E3A8A')" class="w-6 h-6 rounded-md bg-blue-900 border border-blue-950 shadow-2xs cursor-pointer" title="Biru Tua"></button>
                                </div>
                            </div>
                        </div>

                        {{-- Style Toggles: Bold, Uppercase, Alignment --}}
                        <div class="flex flex-wrap items-center gap-4 pt-1">
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700">
                                <input type="checkbox" id="ctrl_bold" onchange="updateCurrentLayerProperty('bold', this.checked)" class="rounded text-blue-600">
                                <span>Tebal (Bold)</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700">
                                <input type="checkbox" id="ctrl_uppercase" onchange="updateCurrentLayerProperty('uppercase', this.checked)" class="rounded text-blue-600">
                                <span>KAPITAL (Upper)</span>
                            </label>
                        </div>

                        {{-- Direction (Khusus Area Akses: Vertikal / Horizontal) --}}
                        <div id="group_direction" class="hidden">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Susunan Huruf Area:</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="updateCurrentLayerProperty('direction', 'vertical')" id="btn_dir_vertical" class="px-2.5 py-1.5 rounded-lg border text-xs font-bold text-center cursor-pointer">
                                    <i class="fas fa-arrows-up-down mr-1"></i> Vertikal
                                </button>
                                <button type="button" onclick="updateCurrentLayerProperty('direction', 'horizontal')" id="btn_dir_horizontal" class="px-2.5 py-1.5 rounded-lg border text-xs font-bold text-center cursor-pointer">
                                    <i class="fas fa-arrows-left-right mr-1"></i> Horizontal
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- Nudge Buttons (Tombol Geser Halus) --}}
                    <div class="pt-3 border-t border-slate-100">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Geser Presisi (0.5%):</label>
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" onclick="nudgeElement(-0.5, 0)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <div class="flex flex-col gap-2">
                                <button type="button" onclick="nudgeElement(0, -0.5)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button type="button" onclick="nudgeElement(0, 0.5)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                            </div>
                            <button type="button" onclick="nudgeElement(0.5, 0)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs cursor-pointer">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Card 3: Informasi File Template & Ganti Gambar --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wider mb-2">Background Aktif:</h4>
                <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <div class="w-12 h-16 rounded-lg bg-white border overflow-hidden shadow-2xs shrink-0">
                        <img src="{{ $template->gambar_url }}" class="w-full h-full object-cover" alt="Background">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $template->nama_template }}</p>
                        <p class="text-[10px] text-slate-400 font-mono truncate">{{ $template->gambar_template }}</p>
                        <a href="{{ route('administrator.template-kartu.edit', $template->id) }}" 
                           class="inline-block mt-1 text-[11px] font-bold text-blue-600 hover:underline">
                            <i class="fas fa-upload mr-1"></i> Unggah Background Baru
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- ========================================================================= --}}
    {{-- JAVASCRIPT: CANVA-LIKE DRAG & DROP ENGINE                                 --}}
    {{-- ========================================================================= --}}
    <script>
        // 1. Data Konfigurasi Posisi (Dimuat dari Backend Model)
        const templateId = {{ $template->id }};
        const defaultPositions = @json(\App\Models\TemplateKartu::getDefaultPosisiPengaturan($template->kode_warna));
        let currentPositions = @json($template->posisi);
        let selectedLayerKey = 'foto'; // Default selected

        // Dragging state
        let isDragging = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let elemStartLeft = 0;
        let elemStartTop = 0;

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            applyAllPositionsToCanvas();
            selectLayer('foto');
            initDragAndDrop();
            initKeyboardShortcuts();
        });

        // 2. Terapkan Semua Posisi ke Elemen di Canvas
        function applyAllPositionsToCanvas() {
            for (const [key, cfg] of Object.entries(currentPositions)) {
                const el = document.getElementById('layer_' + key);
                if (!el) continue;

                // Terapkan posisi persentase
                el.style.left = cfg.left + '%';
                el.style.top = cfg.top + '%';

                if (cfg.width !== undefined) {
                    el.style.width = cfg.width + '%';
                }
                if (cfg.height !== undefined) {
                    el.style.height = cfg.height + '%';
                }

                // Visibility
                el.style.display = cfg.visible === false ? 'none' : 'block';

                // Terapkan styling teks
                const textElem = el.querySelector('p, span:not(.layer-badge), .area-chip');
                if (textElem) {
                    if (cfg.font_size) textElem.style.fontSize = cfg.font_size + 'px';
                    if (cfg.color) textElem.style.color = cfg.color;
                    if (cfg.bold !== undefined) textElem.style.fontWeight = cfg.bold ? '900' : 'normal';
                    if (cfg.uppercase !== undefined) textElem.style.textTransform = cfg.uppercase ? 'uppercase' : 'none';
                    if (cfg.align) el.style.textAlign = cfg.align;
                }

                // Khusus direction untuk area akses
                if (key === 'area_akses') {
                    const container = document.getElementById('display_area_akses_container');
                    if (container) {
                        if (cfg.direction === 'horizontal') {
                            container.className = 'flex flex-row items-center gap-1.5 font-black font-mono drop-shadow-sm';
                        } else {
                            container.className = 'flex flex-col items-center gap-1 font-black font-mono drop-shadow-sm';
                        }
                    }
                }
            }
        }

        // 3. Memilih Layer yang Aktif untuk Diedit
        function selectLayer(key, event) {
            if (event) event.stopPropagation();

            selectedLayerKey = key;

            // Update UI Canvas: berikan border aktif
            document.querySelectorAll('.draggable-layer').forEach(layer => {
                const handle = layer.querySelector('.layer-handle-box');
                if (handle) {
                    handle.classList.remove('border-blue-500', 'ring-2', 'ring-blue-400', 'bg-blue-500/10');
                    handle.classList.add('border-transparent');
                }
            });

            const activeEl = document.getElementById('layer_' + key);
            if (activeEl) {
                const handle = activeEl.querySelector('.layer-handle-box');
                if (handle) {
                    handle.classList.remove('border-transparent');
                    handle.classList.add('border-blue-500', 'ring-2', 'ring-blue-400', 'bg-blue-500/10');
                }
            }

            // Update Layer Button di Sidebar
            document.querySelectorAll('.layer-pill').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                btn.classList.add('bg-slate-50', 'text-slate-700', 'border-slate-200');
            });
            const activeBtn = document.getElementById('layerBtn_' + key);
            if (activeBtn) {
                activeBtn.classList.remove('bg-slate-50', 'text-slate-700', 'border-slate-200');
                activeBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
            }

            // Update Inspector Form Controls
            populateInspector(key);
        }

        // 4. Isi Form Inspector dengan Properti Layer Terpilih
        function populateInspector(key) {
            const cfg = currentPositions[key] || {};
            const titles = {
                'foto': 'Pas Foto Pemohon',
                'area_akses': 'Kode Area Akses',
                'masa_berlaku': 'Tanggal Masa Berlaku',
                'nama_pemegang': 'Nama Pemegang',
                'jabatan': 'Jabatan Pemegang',
                'instansi': 'Instansi / Perusahaan',
                'no_registrasi': 'Nomor Registrasi / Kartu',
                'qr_code': 'QR Code Verifikasi'
            };

            document.getElementById('inspectorTitle').innerText = titles[key] || key;
            document.getElementById('inspectorSubtitle').innerText = 'Layer: ' + key;
            document.getElementById('activeElementLabel').innerHTML = 'Elemen Aktif: <strong class="text-blue-400">' + (titles[key] || key) + '</strong>';

            // Controls X, Y
            document.getElementById('ctrl_left').value = cfg.left ?? 0;
            document.getElementById('val_left').innerText = (cfg.left ?? 0) + '%';
            document.getElementById('ctrl_top').value = cfg.top ?? 0;
            document.getElementById('val_top').innerText = (cfg.top ?? 0) + '%';

            // Visibility
            document.getElementById('ctrl_visible').checked = cfg.visible !== false;

            // Dimensions (Width/Height)
            const dimSection = document.getElementById('section_dimension');
            const heightGroup = document.getElementById('group_height');
            if (cfg.width !== undefined) {
                dimSection.classList.remove('hidden');
                document.getElementById('ctrl_width').value = cfg.width;
                document.getElementById('val_width').innerText = cfg.width + '%';

                if (cfg.height !== undefined) {
                    heightGroup.classList.remove('hidden');
                    document.getElementById('ctrl_height').value = cfg.height;
                    document.getElementById('val_height').innerText = cfg.height + '%';
                } else {
                    heightGroup.classList.add('hidden');
                }
            } else {
                dimSection.classList.add('hidden');
            }

            // Typography Section
            const typoSection = document.getElementById('section_typography');
            const dirGroup = document.getElementById('group_direction');

            if (key === 'foto' || key === 'qr_code') {
                typoSection.classList.add('hidden');
            } else {
                typoSection.classList.remove('hidden');
                document.getElementById('ctrl_font_size').value = cfg.font_size || 12;
                document.getElementById('val_font_size').innerText = (cfg.font_size || 12) + 'px';
                document.getElementById('ctrl_color').value = cfg.color || '#FFFFFF';
                document.getElementById('ctrl_bold').checked = !!cfg.bold;
                document.getElementById('ctrl_uppercase').checked = !!cfg.uppercase;

                if (key === 'area_akses') {
                    dirGroup.classList.remove('hidden');
                    const isVert = (cfg.direction || 'vertical') === 'vertical';
                    document.getElementById('btn_dir_vertical').className = isVert ? 'px-2.5 py-1.5 rounded-lg border text-xs font-bold text-center bg-blue-600 text-white border-blue-600 cursor-pointer' : 'px-2.5 py-1.5 rounded-lg border text-xs font-bold text-center bg-slate-50 text-slate-700 border-slate-200 cursor-pointer';
                    document.getElementById('btn_dir_horizontal').className = !isVert ? 'px-2.5 py-1.5 rounded-lg border text-xs font-bold text-center bg-blue-600 text-white border-blue-600 cursor-pointer' : 'px-2.5 py-1.5 rounded-lg border text-xs font-bold text-center bg-slate-50 text-slate-700 border-slate-200 cursor-pointer';
                } else {
                    dirGroup.classList.add('hidden');
                }
            }
        }

        // 5. Update Posisi X/Y dari Slider
        function updateCurrentLayerPosition(prop, val) {
            if (!currentPositions[selectedLayerKey]) currentPositions[selectedLayerKey] = {};
            currentPositions[selectedLayerKey][prop] = Math.round(val * 10) / 10;

            document.getElementById('val_' + prop).innerText = currentPositions[selectedLayerKey][prop] + '%';
            applyAllPositionsToCanvas();
        }

        // 6. Update Properti Lain (Font, Warna, Bold, dll)
        function updateCurrentLayerProperty(prop, val) {
            if (!currentPositions[selectedLayerKey]) currentPositions[selectedLayerKey] = {};
            currentPositions[selectedLayerKey][prop] = val;

            if (prop === 'font_size') {
                document.getElementById('val_font_size').innerText = val + 'px';
            } else if (prop === 'width') {
                document.getElementById('val_width').innerText = val + '%';
            } else if (prop === 'height') {
                document.getElementById('val_height').innerText = val + '%';
            }

            applyAllPositionsToCanvas();
            populateInspector(selectedLayerKey);
        }

        // 7. Geser Halus Menggunakan Nudge Buttons atau Tombol Panah Keyboard
        function nudgeElement(dx, dy) {
            if (!selectedLayerKey) return;
            const cfg = currentPositions[selectedLayerKey];
            if (!cfg) return;

            let newLeft = Math.min(100, Math.max(0, (cfg.left || 0) + dx));
            let newTop = Math.min(100, Math.max(0, (cfg.top || 0) + dy));

            cfg.left = Math.round(newLeft * 10) / 10;
            cfg.top = Math.round(newTop * 10) / 10;

            document.getElementById('ctrl_left').value = cfg.left;
            document.getElementById('val_left').innerText = cfg.left + '%';
            document.getElementById('ctrl_top').value = cfg.top;
            document.getElementById('val_top').innerText = cfg.top + '%';

            applyAllPositionsToCanvas();
        }

        // 8. Event Listener Drag & Drop pada Canvas
        function initDragAndDrop() {
            const canvas = document.getElementById('cardCanvas');

            document.querySelectorAll('.draggable-layer').forEach(layer => {
                layer.addEventListener('mousedown', (e) => {
                    const key = layer.id.replace('layer_', '');
                    selectLayer(key, e);

                    isDragging = true;
                    dragStartX = e.clientX;
                    dragStartY = e.clientY;

                    const cfg = currentPositions[key];
                    elemStartLeft = cfg.left || 0;
                    elemStartTop = cfg.top || 0;

                    e.preventDefault();
                });
            });

            window.addEventListener('mousemove', (e) => {
                if (!isDragging || !selectedLayerKey) return;

                const canvasRect = canvas.getBoundingClientRect();
                const deltaXPixels = e.clientX - dragStartX;
                const deltaYPixels = e.clientY - dragStartY;

                // Konversi pixel ke persentase (%)
                const deltaXPercent = (deltaXPixels / canvasRect.width) * 100;
                const deltaYPercent = (deltaYPixels / canvasRect.height) * 100;

                let newLeft = Math.min(98, Math.max(0, elemStartLeft + deltaXPercent));
                let newTop = Math.min(98, Math.max(0, elemStartTop + deltaYPercent));

                newLeft = Math.round(newLeft * 10) / 10;
                newTop = Math.round(newTop * 10) / 10;

                currentPositions[selectedLayerKey].left = newLeft;
                currentPositions[selectedLayerKey].top = newTop;

                // Update input di sidebar
                document.getElementById('ctrl_left').value = newLeft;
                document.getElementById('val_left').innerText = newLeft + '%';
                document.getElementById('ctrl_top').value = newTop;
                document.getElementById('val_top').innerText = newTop + '%';

                applyAllPositionsToCanvas();
            });

            window.addEventListener('mouseup', () => {
                isDragging = false;
            });
        }

        // 9. Tombol Pintas Keyboard (&uarr; &darr; &larr; &rarr;)
        function initKeyboardShortcuts() {
            window.addEventListener('keydown', (e) => {
                if (['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    nudgeElement(e.shiftKey ? -2 : -0.5, 0);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    nudgeElement(e.shiftKey ? 2 : 0.5, 0);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    nudgeElement(e.shiftKey ? -2 : -0.5, 0);
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    nudgeElement(e.shiftKey ? 2 : 0.5, 0);
                }
            });
        }

        // 10. Ganti Data Sampel dengan Data Riil Kartu PAS Pemohon
        function changeSampleCardData(val) {
            const select = document.getElementById('kartuPasSelector');
            const selectedOpt = select.options[select.selectedIndex];

            if (val === 'default') {
                document.getElementById('display_nama_pemegang').innerText = '{{ $previewData['nama_pemegang'] }}';
                document.getElementById('display_no_registrasi').innerText = '{{ $previewData['nomor_kartu'] }}';
                document.getElementById('display_instansi').innerText = '{{ $previewData['perusahaan'] }}';
                document.getElementById('display_jabatan').innerText = '{{ $previewData['jabatan'] }}';
                document.getElementById('display_masa_berlaku').innerText = '{{ $previewData['tanggal_berlaku'] }}';
                
                document.getElementById('display_foto_img').classList.add('hidden');
                document.getElementById('display_foto_placeholder').classList.remove('hidden');
                return;
            }

            document.getElementById('display_nama_pemegang').innerText = selectedOpt.getAttribute('data-nama') || '-';
            document.getElementById('display_no_registrasi').innerText = selectedOpt.getAttribute('data-nomor') || '-';
            document.getElementById('display_instansi').innerText = selectedOpt.getAttribute('data-instansi') || '-';
            document.getElementById('display_jabatan').innerText = selectedOpt.getAttribute('data-jabatan') || '-';
            document.getElementById('display_masa_berlaku').innerText = selectedOpt.getAttribute('data-berlaku') || '-';

            // Area Akses
            const rawArea = selectedOpt.getAttribute('data-area') || '';
            const areaArr = rawArea.split(',').map(s => s.trim()).filter(Boolean);
            const container = document.getElementById('display_area_akses_container');
            if (container) {
                container.innerHTML = areaArr.map(letter => `<span class="area-chip block leading-tight">${letter}</span>`).join('');
            }

            // Foto
            const fotoUrl = selectedOpt.getAttribute('data-foto');
            const fotoImg = document.getElementById('display_foto_img');
            const fotoPlaceholder = document.getElementById('display_foto_placeholder');
            if (fotoUrl) {
                fotoImg.src = fotoUrl;
                fotoImg.classList.remove('hidden');
                fotoPlaceholder.classList.add('hidden');
            } else {
                fotoImg.classList.add('hidden');
                fotoPlaceholder.classList.remove('hidden');
            }

            applyAllPositionsToCanvas();
        }

        // 11. Reset Layout ke Nilai Bawaan
        function resetToDefaultLayout() {
            if (!confirm('Kembalikan seluruh tata letak elemen kartu ke posisi bawaan?')) return;

            currentPositions = JSON.parse(JSON.stringify(defaultPositions));
            applyAllPositionsToCanvas();
            selectLayer(selectedLayerKey);

            // Kirim ke backend
            fetch(`{{ route('administrator.template-kartu.designer.reset', $template->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                showToastNotification('Tata letak berhasil dikembalikan ke posisi bawaan!', 'success');
            })
            .catch(err => {
                showToastNotification('Posisi di-reset secara lokal.', 'info');
            });
        }

        // 12. Simpan Tata Letak ke Database
        function saveCardLayout() {
            const btn = document.getElementById('btnSaveLayout');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            fetch(`{{ route('administrator.template-kartu.designer.save', $template->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    posisi_pengaturan: currentPositions
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Tersimpan!';
                setTimeout(() => { btn.innerHTML = originalText; }, 2000);

                showToastNotification(data.message || 'Tata letak kartu berhasil disimpan!', 'success');
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                showToastNotification('Gagal menyimpan tata letak kartu.', 'error');
            });
        }

        // Helper Toast Notification
        function showToastNotification(message, type = 'success') {
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-emerald-600' : (type === 'error' ? 'bg-rose-600' : 'bg-blue-600');
            const iconClass = type === 'success' ? 'fa-circle-check' : (type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info');

            toast.className = `fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl text-white text-xs font-bold shadow-xl transition-all duration-300 transform translate-y-4 opacity-0 ${bgClass}`;
            toast.innerHTML = `<i class="fas ${iconClass} text-base"></i> <span>${message}</span>`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
            }, 50);

            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }
    </script>
</x-app-layout>
