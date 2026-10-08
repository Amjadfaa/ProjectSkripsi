<x-app-layout>
    <x-slot name="header">
        Terminal Scanner QR — {{ $device->nama_kamera }}
    </x-slot>

    <!-- Ultra-Fast QR Scanner Libraries: html5-qrcode & jsQR -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js" type="text/javascript"></script>

    <style>
        @keyframes scanline {
            0% { top: 5%; opacity: 0.4; }
            50% { opacity: 1; }
            100% { top: 95%; opacity: 0.4; }
        }
        .scanline-beam {
            animation: scanline 2.2s ease-in-out infinite alternate;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(16, 185, 129, 0.3); }
            50% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.6); }
        }
        .valid-glow {
            animation: pulseGlow 2s infinite;
        }
        @keyframes deniedGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(239, 68, 68, 0.3); }
            50% { box-shadow: 0 0 30px rgba(239, 68, 68, 0.6); }
        }
        .denied-glow {
            animation: deniedGlow 2s infinite;
        }
    </style>

    <div class="space-y-6">

        <!-- Top Device Flight-Control Bar -->
        <div class="relative overflow-hidden rounded-3xl bg-white border border-slate-200/90 shadow-sm p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#1e3a5f] to-[#0f2744] text-white flex items-center justify-center text-xl shrink-0 shadow-md">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">{{ $device->nama_kamera }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black tracking-wider bg-slate-900 text-white uppercase shadow-xs">
                            AREA {{ $device->kode_area }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-200">
                            {{ str_replace('_', ' ', $device->tipe_scan) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> ONLINE
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap">
                        <span><i class="fas fa-map-marker-alt text-amber-500"></i> Lokasi: <strong>{{ optional($device->areaAkses)->nama_area ?? 'Area Bandara' }}</strong></span>
                        <span class="text-slate-300">&bull;</span>
                        <span>Kode Akses: <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded font-bold text-slate-700">{{ $device->kode_akses }}</code></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap justify-end">
                <!-- Arah Scan Switcher jika tipe masuk_keluar -->
                @if($device->tipe_scan === 'masuk_keluar')
                    <div class="flex items-center bg-slate-100 p-1 rounded-2xl border border-slate-200 shadow-inner">
                        <button type="button" id="btnModeMasuk" onclick="setScanMode('masuk')"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-black tracking-wider transition flex items-center gap-1.5 bg-emerald-600 text-white shadow-sm">
                            <i class="fas fa-sign-in-alt"></i> MASUK
                        </button>
                        <button type="button" id="btnModeKeluar" onclick="setScanMode('keluar')"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-black tracking-wider transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900">
                            <i class="fas fa-sign-out-alt"></i> KELUAR
                        </button>
                    </div>
                @else
                    <div class="px-3.5 py-2 rounded-2xl bg-slate-100 border border-slate-200 text-xs font-black uppercase text-slate-800 flex items-center gap-1.5">
                        <i class="fas {{ $device->tipe_scan === 'masuk' ? 'fa-sign-in-alt text-emerald-600' : 'fa-sign-out-alt text-amber-600' }}"></i>
                        <span>MODE {{ strtoupper($device->tipe_scan) }}</span>
                    </div>
                @endif

                <!-- Audio Toggle -->
                <button type="button" id="btnAudioToggle" onclick="toggleAudio()"
                        class="px-3 py-2 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition flex items-center gap-1.5">
                    <i class="fas fa-volume-up text-amber-500" id="audioIcon"></i>
                    <span id="audioText">Suara: ON</span>
                </button>

                <!-- Ganti Kamera -->
                <a href="{{ route('operator.kamera.index') }}"
                   class="px-3 py-2 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition flex items-center gap-1.5">
                    <i class="fas fa-exchange-alt text-blue-500"></i>
                    <span>Ganti Pos</span>
                </a>

                <!-- Putuskan -->
                <form action="{{ route('operator.kamera.disconnect') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan sesi kamera ini?');">
                    @csrf
                    <button type="submit"
                            class="px-3 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5">
                        <i class="fas fa-power-off"></i>
                        <span>Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Scanner Grid: Kamera Live (Kiri) & Tampilan Kartu PAS Pengguna (Kanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Kolom Kiri: Kamera Scanner & Input Barcode (5 Kolom) -->
            <div class="lg:col-span-5 space-y-4">

                <!-- Webcam Card -->
                <div class="bg-slate-950 rounded-3xl border border-slate-800 p-4 sm:p-5 shadow-2xl overflow-hidden text-white">
                    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-300">Live Camera Stream</h3>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                <i class="fas fa-bolt text-[9px]"></i> Turbo Engine
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <select id="cameraSelect" onchange="onCameraChange()" 
                                    class="hidden bg-slate-800 hover:bg-slate-700 border border-slate-600 text-slate-100 text-xs rounded-xl px-2.5 py-1.5 focus:ring-2 focus:ring-amber-400 focus:outline-none cursor-pointer max-w-[200px] z-20 relative"
                                    title="Pilih Perangkat Kamera">
                            </select>
                            <button type="button" id="toggleWebcamBtn" onclick="toggleWebcam()"
                                    class="text-xs bg-amber-400 hover:bg-amber-300 text-slate-950 px-3.5 py-1.5 rounded-xl font-extrabold transition flex items-center gap-1.5 shadow-md cursor-pointer">
                                <i class="fas fa-camera"></i> <span id="btnText">Nyalakan Kamera</span>
                            </button>
                        </div>
                    </div>

                    <!-- Viewfinder Area -->
                    <div class="relative rounded-2xl bg-black min-h-[350px] flex items-center justify-center overflow-hidden border border-slate-800">
                        <!-- HUD Corner brackets -->
                        <div class="absolute top-3 left-3 w-7 h-7 border-t-2 border-l-2 border-amber-400 rounded-tl pointer-events-none z-10 shadow-sm"></div>
                        <div class="absolute top-3 right-3 w-7 h-7 border-t-2 border-r-2 border-amber-400 rounded-tr pointer-events-none z-10 shadow-sm"></div>
                        <div class="absolute bottom-3 left-3 w-7 h-7 border-b-2 border-l-2 border-amber-400 rounded-bl pointer-events-none z-10 shadow-sm"></div>
                        <div class="absolute bottom-3 right-3 w-7 h-7 border-b-2 border-r-2 border-amber-400 rounded-br pointer-events-none z-10 shadow-sm"></div>

                        <!-- Scan Success Flash Feedback -->
                        <div id="scanSuccessFlash" class="hidden absolute inset-0 bg-emerald-500/30 z-30 pointer-events-none transition-opacity duration-200 flex items-center justify-center">
                            <div class="bg-emerald-600/90 text-white font-black text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 animate-pulse">
                                <i class="fas fa-check-circle"></i> QR TERBACA
                            </div>
                        </div>

                        <!-- Animated Scanline Laser Beam (active when webcam running) -->
                        <div id="scanlineBeam" class="hidden absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_15px_#34d399] z-20 scanline-beam pointer-events-none"></div>

                        <!-- HTML5 Webcam Element -->
                        <div id="reader" class="hidden w-full h-full"></div>

                        <!-- Placeholder jika kamera belum aktif -->
                        <div id="scannerPlaceholder" onclick="toggleWebcam()" class="flex flex-col items-center justify-center p-8 text-center cursor-pointer w-full h-full hover:bg-slate-900/60 transition group">
                            <div class="w-16 h-16 rounded-2xl bg-amber-400/10 border border-amber-400/30 text-amber-400 flex items-center justify-center text-3xl mb-3 shadow-inner group-hover:scale-110 transition-transform">
                                <i class="fas fa-camera-retro"></i>
                            </div>
                            <h4 class="text-sm font-extrabold text-white mb-1">Klik untuk Menyalakan Kamera</h4>
                            <p class="text-xs text-slate-400 max-w-xs leading-relaxed">
                                Arahkan QR Code Kartu PAS ke kamera webcam atau scan dengan scanner barcode USB
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Hardware USB Barcode Scanner / Manual Card Number Input -->
                <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-sm space-y-2">
                    <div class="flex items-center justify-between mb-1">
                        <label for="qrInput" class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <i class="fas fa-barcode text-amber-500"></i> Scanner Barcode USB / Input No. Kartu
                        </label>
                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Auto-Focus Ready
                        </span>
                    </div>

                    <form id="scanForm" onsubmit="handleManualSubmit(event)" class="relative">
                        <input type="text" id="qrInput" autocomplete="off"
                               placeholder="Scan Barcode atau ketik Nomor Kartu PAS..."
                               class="w-full bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 rounded-2xl pl-4 pr-28 py-3.5 text-sm font-mono font-bold focus:outline-none focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/20 transition-all">
                        <button type="submit"
                                class="absolute right-1.5 top-1.5 bottom-1.5 bg-[#1e3a5f] hover:bg-[#284c7b] text-white px-5 rounded-xl text-xs font-black tracking-wider transition shadow-sm hover:shadow">
                            VERIFIKASI
                        </button>
                    </form>
                    <p class="text-[11px] text-slate-400">Tekan <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono text-[10px]">Enter</kbd> untuk verifikasi otomatis setelah scan barcode USB</p>
                </div>

            </div>

            <!-- Kolom Kanan: Hasil Verifikasi & Tampilan Kartu PAS Template (7 Kolom) -->
            <div class="lg:col-span-7 space-y-4">
                <div id="resultCard" class="bg-white rounded-3xl border border-slate-200/90 shadow-md p-6 sm:p-7 min-h-[480px] flex flex-col justify-between transition-all">

                    <!-- State: Menunggu Pemindaian (IDLE) -->
                    <div id="idleState" class="flex flex-col items-center justify-center my-auto py-12 text-center text-slate-400 space-y-4">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-3xl bg-gradient-to-tr from-slate-100 to-slate-50 border-2 border-dashed border-slate-300 flex items-center justify-center text-4xl text-slate-400 shadow-inner">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-amber-400 text-slate-950 flex items-center justify-center text-xs font-bold shadow-md">
                                <i class="fas fa-qrcode"></i>
                            </span>
                        </div>
                        <div class="space-y-1 max-w-sm">
                            <h3 class="text-base font-extrabold text-slate-800">Siap Melakukan Pemindaian</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Arahkan QR Code Kartu PAS ke kamera webcam atau scan menggunakan perangkat scanner USB.
                            </p>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Pos Siaga: Area {{ $device->kode_area }} ({{ optional($device->areaAkses)->nama_area ?? 'Pos Keamanan' }})</span>
                        </div>
                    </div>

                    <!-- State: Hasil Pemindaian & Kartu PAS (RESULT CONTENT) -->
                    <div id="resultContent" class="hidden space-y-6 animate-fade-in">

                        <!-- Status Banner Header -->
                        <div id="statusBanner" class="p-4 sm:p-5 rounded-2xl flex items-center justify-between text-white shadow-lg transition-all">
                            <div class="flex items-center gap-3.5">
                                <div id="statusIconWrap" class="w-13 h-13 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-white/20 shadow-inner">
                                    <i id="statusIcon" class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h3 id="statusTitle" class="text-lg sm:text-xl font-black tracking-wide uppercase">AKSES DIIZINKAN (VALID)</h3>
                                    <p id="statusMessage" class="text-xs opacity-95 mt-0.5">Kartu PAS sah dan diizinkan masuk</p>
                                </div>
                            </div>
                            <div id="statusArahBadge" class="px-4 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-white/25 border border-white/30 shadow-sm shrink-0">
                                MASUK
                            </div>
                        </div>

                        <!-- Main Split: Visual Digital Card (Kiri) & Dossier Data (Kanan) -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

                            <!-- Visual Digital PAS Card Display (5 Kolom di desktop) -->
                            <div class="md:col-span-6 flex flex-col items-center">
                                <div class="w-full max-w-[310px]">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                            <i class="fas fa-id-card text-amber-500"></i> Kartu PAS Pengguna
                                        </span>
                                        <span id="cardTemplateBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                            PAS Template
                                        </span>
                                    </div>

                                    <!-- REAL DIGITAL CARD CONTAINER -->
                                    <div id="digitalCardMockup"
                                         class="relative w-full rounded-2xl overflow-hidden shadow-2xl border-2 border-slate-300 select-none aspect-[1/1.58] transition-all bg-slate-800">

                                        <!-- Background Template Image -->
                                        <img id="cardBgImage" src="" alt="Template Background"
                                             class="absolute inset-0 w-full h-full object-cover">

                                        <!-- 1. Pas Foto Pemegang -->
                                        <div id="cardElFoto"
                                             class="absolute rounded-xl overflow-hidden border-2 border-black/40 shadow-sm bg-white/40 flex items-center justify-center z-10">
                                            <img id="cardFotoImg" src="" alt="Foto Pemegang" class="w-full h-full object-cover hidden">
                                            <div id="cardFotoPlaceholder" class="flex flex-col items-center justify-center text-slate-800 p-2 text-center">
                                                <i class="fas fa-user text-3xl opacity-70 mb-1"></i>
                                                <span class="text-[8px] font-black uppercase tracking-wider opacity-80">Pas Foto</span>
                                            </div>
                                        </div>

                                        <!-- 2. Masa Berlaku -->
                                        <div id="cardElMasaBerlaku"
                                             class="absolute font-mono font-black tracking-tight text-center z-10 drop-shadow-sm leading-none">
                                            --
                                        </div>

                                        <!-- 3. Area Akses -->
                                        <div id="cardElAreaAkses"
                                             class="absolute font-mono font-black z-10 drop-shadow-sm flex flex-col items-center justify-center leading-none">
                                            <!-- Injected via JS -->
                                        </div>

                                        <!-- 4. Nama Pemegang -->
                                        <div id="cardElNama"
                                             class="absolute z-10 leading-tight drop-shadow-sm truncate font-black">
                                            --
                                        </div>

                                        <!-- 5. Jabatan -->
                                        <div id="cardElJabatan"
                                             class="absolute z-10 leading-tight drop-shadow-sm truncate font-semibold">
                                            --
                                        </div>

                                        <!-- 6. Instansi / Perusahaan -->
                                        <div id="cardElInstansi"
                                             class="absolute z-10 leading-tight drop-shadow-sm truncate font-semibold">
                                            --
                                        </div>

                                        <!-- 7. No Registrasi -->
                                        <div id="cardElNoRegistrasi"
                                             class="absolute font-mono z-10 leading-tight drop-shadow-sm truncate font-bold">
                                            --
                                        </div>

                                        <!-- 8. QR Code SVG on Card -->
                                        <div id="cardElQrCode"
                                             class="absolute rounded-lg bg-white p-1 shadow-md z-10 border border-black/30 flex items-center justify-center overflow-hidden">
                                            <svg class="w-full h-full text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm8-2h3v3h-3v-3zm5 0h3v3h-3v-3zm-5 5h3v3h-3v-3zm5 0h3v3h-3v-3zm2-3h3v3h-3v-3zm-7-2h2v2h-2v-2z"/>
                                            </svg>
                                        </div>

                                        <!-- OVERLAY STAMPS / WATERMARKS (Shown for Expired or Denied) -->
                                        <div id="cardStampOverlay" class="hidden absolute inset-0 z-30 flex items-center justify-center p-4">
                                            <div id="cardStampBadge" class="transform -rotate-12 border-4 px-4 py-2 uppercase font-black tracking-widest text-sm rounded-xl shadow-2xl text-center">
                                                KADALUWARSA
                                            </div>
                                        </div>

                                    </div>
                                    <div class="text-[11px] text-center text-slate-400 mt-2 font-mono">
                                        Render Template Kartu PAS &bull; Rasio 1 : 1.58
                                    </div>
                                </div>
                            </div>

                            <!-- Dossier Rincian Verifikasi Petugas (6 Kolom di desktop) -->
                            <div class="md:col-span-6 space-y-3.5">
                                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3 text-xs">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                        <div>
                                            <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Nomor Kartu PAS</span>
                                            <span id="resNomorKartu" class="font-mono text-base font-black text-slate-900">--</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Masa Berlaku</span>
                                            <span id="resTglBerlaku" class="font-semibold text-slate-800">--</span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Nama Lengkap Pemegang</span>
                                        <span id="resNama" class="text-sm font-extrabold text-slate-900 block">--</span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Perusahaan / Instansi</span>
                                            <span id="resPerusahaan" class="font-bold text-slate-800 block truncate">--</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Jabatan</span>
                                            <span id="resJabatan" class="font-bold text-slate-800 block truncate">--</span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Izin Area Akses Pemegang</span>
                                        <div id="resAreaList" class="flex flex-wrap gap-1.5 mt-1.5">
                                            <!-- Area badges -->
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-slate-400 uppercase text-[10px] font-extrabold block">Status Masa Berlaku</span>
                                        <div id="resStatusMasa" class="mt-0.5 font-bold">--</div>
                                    </div>
                                </div>

                                <!-- Catatan Sistem / Alasan Penolakan -->
                                <div id="catatanBox" class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 text-xs text-amber-900 flex items-start gap-2.5">
                                    <i class="fas fa-circle-info text-amber-600 mt-0.5 text-sm"></i>
                                    <div class="flex-1 leading-snug">
                                        <strong>Catatan Verifikasi:</strong>
                                        <span id="resCatatanText" class="ml-1 text-slate-700">--</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Footer / Scanner Status indicator -->
                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <span id="scanTimestamp">Menunggu verifikasi...</span>
                        <span class="flex items-center gap-1.5 font-bold text-emerald-600">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Scanner Aktif Siaga
                        </span>
                    </div>

                </div>
            </div>

        </div>

        <!-- Tabel 10 Pemindaian Terkini Perangkat Ini -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm">Riwayat Pemindaian Terkini di Pos Ini</h3>
                        <p class="text-[11px] text-slate-400">Daftar kartu PAS yang baru saja diverifikasi</p>
                    </div>
                </div>
                <a href="{{ route('operator.kamera.logs') }}" class="text-xs text-blue-600 font-bold hover:underline flex items-center gap-1">
                    <span>Lihat Riwayat Lengkap</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Waktu</th>
                            <th class="px-5 py-3">Nomor Kartu</th>
                            <th class="px-5 py-3">Nama Pemegang</th>
                            <th class="px-5 py-3">Perusahaan</th>
                            <th class="px-5 py-3 text-center">Arah</th>
                            <th class="px-5 py-3 text-center">Status Izin</th>
                            <th class="px-5 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="recentLogsTableBody" class="divide-y divide-slate-100">
                        @forelse($recentLogs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 font-mono text-xs text-slate-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->waktu_scan)->format('H:i:s') }}
                                </td>
                                <td class="px-5 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ $log->nomor_kartu ?? '-' }}
                                </td>
                                <td class="px-5 py-3 font-bold text-slate-900">
                                    {{ $log->nama_pemegang ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-slate-600 text-xs">
                                    {{ $log->perusahaan ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ ($log->tipe_aktivitas ?? 'masuk') === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $log->tipe_aktivitas ?? 'masuk' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    @if($log->status_akses === 'diterima')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check-circle text-[10px]"></i> VALID
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fas fa-times-circle text-[10px]"></i> DITOLAK
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-600 max-w-xs truncate" title="{{ $log->alasan ?? $log->catatan }}">
                                    {{ $log->alasan ?? $log->catatan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr id="noDataRow">
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-xs">
                                    Belum ada catatan pemindaian pada sesi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Scanner Script Logic -->
    <script>
        let currentScanMode = '{{ $device->tipe_scan === "keluar" ? "keluar" : "masuk" }}';
        let isWebcamRunning = false;
        let html5QrcodeScanner = null;
        let isProcessing = false;
        let audioEnabled = true;

        // Ultra-Fast Turbo Scanner State (Multi-Engine Pipeline)
        let turboScanActive = false;
        let turboCanvas = null;
        let turboCtx = null;
        let nativeBarcodeDetector = null;
        let availableCameras = [];
        let selectedCameraId = localStorage.getItem('monpasku_selected_camera') || null;

        // Initialize Native Hardware BarcodeDetector if supported in Chromium
        if ('BarcodeDetector' in window) {
            try {
                nativeBarcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
            } catch(e) {
                nativeBarcodeDetector = null;
            }
        }

        async function initCameraList() {
            try {
                if (typeof Html5Qrcode !== 'undefined' && Html5Qrcode.getCameras) {
                    const devices = await Html5Qrcode.getCameras();
                    if (devices && devices.length > 0) {
                        availableCameras = devices;
                        const select = document.getElementById('cameraSelect');
                        if (select) {
                            select.innerHTML = '';
                            devices.forEach((d, idx) => {
                                const opt = document.createElement('option');
                                opt.value = d.id;
                                opt.textContent = d.label || `Kamera ${idx + 1}`;
                                opt.className = 'bg-slate-900 text-white py-1';
                                if (selectedCameraId && d.id === selectedCameraId) {
                                    opt.selected = true;
                                }
                                select.appendChild(opt);
                            });

                            if (!selectedCameraId || !devices.some(d => d.id === selectedCameraId)) {
                                selectedCameraId = devices[0].id;
                                select.value = selectedCameraId;
                            }
                            select.classList.remove('hidden');
                        }
                    }
                }
            } catch(e) {
                console.warn('initCameraList warning:', e);
            }
        }
        window.addEventListener('DOMContentLoaded', () => {
            initCameraList();
            const select = document.getElementById('cameraSelect');
            if (select) {
                select.addEventListener('mousedown', (e) => e.stopPropagation());
                select.addEventListener('click', (e) => e.stopPropagation());
                select.addEventListener('focus', () => initCameraList());
            }
        });

        async function onCameraChange() {
            const select = document.getElementById('cameraSelect');
            if (select && select.value) {
                selectedCameraId = select.value;
                localStorage.setItem('monpasku_selected_camera', selectedCameraId);
                if (isWebcamRunning) {
                    await stopWebcam();
                    await startWebcamWithDevice(selectedCameraId);
                }
            }
        }

        // Web Audio API Beep Synthesizer
        let audioCtx = null;
        function getAudioContext() {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        function playBeep(freq, type, duration) {
            if (!audioEnabled) return;
            try {
                const ctx = getAudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(freq, ctx.currentTime);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + duration);
            } catch(e) {
                console.error("Audio error", e);
            }
        }

        function playSuccessSound() {
            playBeep(880, 'sine', 0.12);
            setTimeout(() => playBeep(1320, 'sine', 0.18), 120);
        }

        function playDeniedSound() {
            playBeep(260, 'sawtooth', 0.25);
            setTimeout(() => playBeep(180, 'sawtooth', 0.35), 220);
        }

        function toggleAudio() {
            audioEnabled = !audioEnabled;
            const icon = document.getElementById('audioIcon');
            const text = document.getElementById('audioText');
            if (audioEnabled) {
                icon.className = 'fas fa-volume-up text-amber-500';
                text.innerText = 'Suara: ON';
            } else {
                icon.className = 'fas fa-volume-mute text-slate-400';
                text.innerText = 'Suara: OFF';
            }
        }

        function setScanMode(mode) {
            currentScanMode = mode;
            const btnMasuk = document.getElementById('btnModeMasuk');
            const btnKeluar = document.getElementById('btnModeKeluar');
            if (!btnMasuk || !btnKeluar) return;

            if (mode === 'masuk') {
                btnMasuk.className = 'px-3.5 py-1.5 rounded-xl text-xs font-black tracking-wider transition flex items-center gap-1.5 bg-emerald-600 text-white shadow-sm';
                btnKeluar.className = 'px-3.5 py-1.5 rounded-xl text-xs font-black tracking-wider transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
            } else {
                btnKeluar.className = 'px-3.5 py-1.5 rounded-xl text-xs font-black tracking-wider transition flex items-center gap-1.5 bg-amber-600 text-white shadow-sm';
                btnMasuk.className = 'px-3.5 py-1.5 rounded-xl text-xs font-black tracking-wider transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
            }
        }

        // Auto Focus Input Box for USB Barcode Scanners
        const qrInput = document.getElementById('qrInput');
        document.addEventListener('click', function(e) {
            if (
                e.target.tagName === 'BUTTON' || 
                e.target.tagName === 'A' || 
                e.target.tagName === 'INPUT' || 
                e.target.tagName === 'SELECT' || 
                e.target.tagName === 'OPTION' || 
                e.target.closest('button') || 
                e.target.closest('select') || 
                e.target.closest('a')
            ) {
                return;
            }
            qrInput?.focus();
        });
        window.addEventListener('load', () => qrInput?.focus());

        function handleManualSubmit(e) {
            e.preventDefault();
            const val = qrInput.value.trim();
            if (val) {
                processQrCode(val);
                qrInput.value = '';
            }
        }

        // ---------------------------------------------------------------------
        // Turbo Scanning Loop (High-speed Native BarcodeDetector + jsQR)
        // ---------------------------------------------------------------------
        function startTurboScanner(videoEl) {
            if (!videoEl) return;
            turboScanActive = true;

            if (!turboCanvas) {
                turboCanvas = document.createElement('canvas');
                turboCtx = turboCanvas.getContext('2d', { willReadFrequently: true });
            }

            let lastScanTime = 0;
            const scanIntervalMs = 50; // ~20 checks per second for instantaneous reaction
            let failureStreak = 0;

            async function scanFrame(timestamp) {
                if (!turboScanActive || !isWebcamRunning) return;

                if (videoEl.readyState >= 2 && !isProcessing && (timestamp - lastScanTime >= scanIntervalMs)) {
                    lastScanTime = timestamp;

                    const vw = videoEl.videoWidth;
                    const vh = videoEl.videoHeight;

                    if (vw > 0 && vh > 0) {
                        let detectedCode = null;

                        // Priority 1: Native Hardware BarcodeDetector (Fastest ~1-3ms)
                        if (nativeBarcodeDetector) {
                            try {
                                const barcodes = await nativeBarcodeDetector.detect(videoEl);
                                if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
                                    detectedCode = barcodes[0].rawValue.trim();
                                }
                            } catch(e) {}
                        }

                        // Priority 2: Ultra-Fast jsQR Engine (~8-15ms)
                        if (!detectedCode && typeof jsQR === 'function') {
                            let targetW = vw;
                            let targetH = vh;
                            if (vw > 800) {
                                const scale = 800 / vw;
                                targetW = 800;
                                targetH = Math.round(vh * scale);
                            }

                            if (turboCanvas.width !== targetW || turboCanvas.height !== targetH) {
                                turboCanvas.width = targetW;
                                turboCanvas.height = targetH;
                            }

                            turboCtx.drawImage(videoEl, 0, 0, targetW, targetH);
                            const imgData = turboCtx.getImageData(0, 0, targetW, targetH);
                            
                            // Check dontInvert normally, or attemptBoth if streak of no detection (handles glare/contrast)
                            const attemptMode = (failureStreak > 6) ? "attemptBoth" : "dontInvert";
                            const res = jsQR(imgData.data, targetW, targetH, {
                                inversionAttempts: attemptMode
                            });

                            if (res && res.data) {
                                detectedCode = res.data.trim();
                                failureStreak = 0;
                            } else {
                                failureStreak++;
                            }
                        }

                        if (detectedCode && !isProcessing) {
                            processQrCode(detectedCode);
                        }
                    }
                }

                if (turboScanActive && isWebcamRunning) {
                    requestAnimationFrame(scanFrame);
                }
            }

            requestAnimationFrame(scanFrame);
        }

        function stopTurboScanner() {
            turboScanActive = false;
        }

        async function stopWebcam() {
            stopTurboScanner();
            const btnText = document.getElementById('btnText');
            const placeholder = document.getElementById('scannerPlaceholder');
            const readerEl = document.getElementById('reader');
            const scanline = document.getElementById('scanlineBeam');

            if (html5QrcodeScanner) {
                try {
                    await html5QrcodeScanner.stop();
                } catch(err) {
                    console.warn('Error stopping html5QrcodeScanner:', err);
                }
            }
            isWebcamRunning = false;
            if (btnText) btnText.innerText = 'Nyalakan Kamera';
            if (readerEl) {
                readerEl.classList.add('hidden');
                readerEl.innerHTML = '';
            }
            if (placeholder) placeholder.classList.remove('hidden');
            if (scanline) scanline.classList.add('hidden');
        }

        async function startWebcamWithDevice(preferredCameraId = null) {
            const btnText = document.getElementById('btnText');
            const placeholder = document.getElementById('scannerPlaceholder');
            const readerEl = document.getElementById('reader');
            const scanline = document.getElementById('scanlineBeam');

            placeholder.classList.add('hidden');
            readerEl.classList.remove('hidden');
            scanline.classList.remove('hidden');

            const formatsToSupport = (typeof Html5QrcodeSupportedFormats !== 'undefined')
                ? [ Html5QrcodeSupportedFormats.QR_CODE ]
                : [];

            html5QrcodeScanner = new Html5Qrcode("reader", {
                formatsToSupport: formatsToSupport,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                },
                verbose: false
            });

            const scanConfig = { fps: 15 };

            const targetCameraId = preferredCameraId || selectedCameraId;

            // Note: html5Qrcode.start accepts either:
            // 1. A string cameraId (e.g. from getCameras())
            // 2. OR an object with EXACTLY 1 key, e.g. { facingMode: "environment" }
            let primaryConfig;
            if (targetCameraId) {
                primaryConfig = targetCameraId;
            } else if (availableCameras && availableCameras.length > 0) {
                primaryConfig = availableCameras[0].id;
            } else {
                primaryConfig = { facingMode: "environment" };
            }

            const attemptStart = (config) => {
                return html5QrcodeScanner.start(
                    config,
                    scanConfig,
                    (decodedText) => {
                        if (!isProcessing) {
                            processQrCode(decodedText);
                        }
                    },
                    (err) => {}
                );
            };

            try {
                await attemptStart(primaryConfig);
            } catch(firstErr) {
                console.warn('Camera start primary attempt failed, trying fallback:', firstErr);
                try {
                    // Fallback to user-facing mode if environment or deviceId failed
                    await attemptStart({ facingMode: "user" });
                } catch(secondErr) {
                    console.error('All camera start attempts failed:', secondErr);
                    alert('Gagal membuka kamera webcam: ' + (secondErr?.message || secondErr));
                    await stopWebcam();
                    return;
                }
            }

            isWebcamRunning = true;
            if (btnText) btnText.innerText = 'Matikan Kamera';

            // Attach Turbo Scanner to active video stream
            setTimeout(() => {
                const videoEl = document.querySelector('#reader video');
                if (videoEl) {
                    videoEl.style.objectFit = 'cover';
                    startTurboScanner(videoEl);
                }
            }, 200);

            await initCameraList();
        }

        function toggleWebcam() {
            if (isWebcamRunning) {
                stopWebcam();
            } else {
                startWebcamWithDevice(selectedCameraId);
            }
        }

        async function processQrCode(qrData) {
            if (isProcessing) return;
            isProcessing = true;

            // Instant visual scan flash feedback
            const flash = document.getElementById('scanSuccessFlash');
            if (flash) {
                flash.classList.remove('hidden');
                setTimeout(() => flash.classList.add('hidden'), 250);
            }

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch("{{ route('scan.process') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        qr_code: qrData,
                        tipe_aktivitas: currentScanMode
                    })
                });

                const data = await response.json();
                renderScanResult(data);
            } catch (err) {
                console.error("Process error", err);
                alert("Terjadi kesalahan jaringan atau server saat memproses scan.");
            } finally {
                setTimeout(() => {
                    isProcessing = false;
                    qrInput?.focus();
                }, 1200);
            }
        }

        function renderScanResult(data) {
            const idleState = document.getElementById('idleState');
            const resultContent = document.getElementById('resultContent');
            const statusBanner = document.getElementById('statusBanner');
            const statusIcon = document.getElementById('statusIcon');
            const statusTitle = document.getElementById('statusTitle');
            const statusMessage = document.getElementById('statusMessage');
            const statusArahBadge = document.getElementById('statusArahBadge');

            const resNomorKartu = document.getElementById('resNomorKartu');
            const resNama = document.getElementById('resNama');
            const resPerusahaan = document.getElementById('resPerusahaan');
            const resJabatan = document.getElementById('resJabatan');
            const resTglBerlaku = document.getElementById('resTglBerlaku');
            const resStatusMasa = document.getElementById('resStatusMasa');
            const resAreaList = document.getElementById('resAreaList');
            const resCatatanText = document.getElementById('resCatatanText');
            const scanTimestamp = document.getElementById('scanTimestamp');

            // Card Mockup Elements
            const digitalCardMockup = document.getElementById('digitalCardMockup');
            const cardBgImage = document.getElementById('cardBgImage');
            const cardTemplateBadge = document.getElementById('cardTemplateBadge');

            const cardElFoto = document.getElementById('cardElFoto');
            const cardFotoImg = document.getElementById('cardFotoImg');
            const cardFotoPlaceholder = document.getElementById('cardFotoPlaceholder');

            const cardElMasaBerlaku = document.getElementById('cardElMasaBerlaku');
            const cardElAreaAkses = document.getElementById('cardElAreaAkses');
            const cardElNama = document.getElementById('cardElNama');
            const cardElJabatan = document.getElementById('cardElJabatan');
            const cardElInstansi = document.getElementById('cardElInstansi');
            const cardElNoRegistrasi = document.getElementById('cardElNoRegistrasi');
            const cardElQrCode = document.getElementById('cardElQrCode');

            const cardStampOverlay = document.getElementById('cardStampOverlay');
            const cardStampBadge = document.getElementById('cardStampBadge');

            idleState.classList.add('hidden');
            resultContent.classList.remove('hidden');

            const isValid = (data.success === true) || (data.status === 'diterima') || (data.status_izin === 'valid');
            const isKadaluarsa = Boolean(data.is_kadaluarsa);
            const isCooldown = data.status === 'cooldown';
            const c = data.data || data.kartu || {};
            const tpl = c.template || null;

            // Reset Card Glows & Classes
            digitalCardMockup.classList.remove('valid-glow', 'denied-glow', 'ring-4', 'ring-emerald-500', 'ring-rose-600', 'ring-amber-500');

            // Sound & Status Styles
            if (isValid) {
                playSuccessSound();
                statusBanner.className = 'p-4 sm:p-5 rounded-2xl flex items-center justify-between text-white shadow-lg bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700';
                statusIcon.className = 'fas fa-check-circle';
                statusTitle.innerText = 'AKSES DIIZINKAN (VALID)';
                digitalCardMockup.classList.add('valid-glow', 'ring-4', 'ring-emerald-500');
                cardStampOverlay.classList.add('hidden');
            } else if (isKadaluarsa) {
                playDeniedSound();
                statusBanner.className = 'p-4 sm:p-5 rounded-2xl flex items-center justify-between text-white shadow-lg bg-gradient-to-r from-rose-700 via-red-700 to-rose-900';
                statusIcon.className = 'fas fa-calendar-times';
                statusTitle.innerText = 'KARTU SUDAH KADALUARSA';
                digitalCardMockup.classList.add('denied-glow', 'ring-4', 'ring-rose-600');

                // Tampilkan Stamp Merah Kadaluarsa di atas Kartu
                cardStampOverlay.classList.remove('hidden');
                cardStampOverlay.className = 'absolute inset-0 z-30 flex items-center justify-center p-4 bg-red-950/60 backdrop-blur-[2px]';
                cardStampBadge.className = 'transform -rotate-12 border-4 border-rose-400 bg-rose-600/95 text-white font-black text-sm tracking-widest px-4 py-2 uppercase rounded-xl shadow-2xl';
                cardStampBadge.innerHTML = '<i class="fas fa-ban mr-1.5"></i> KADALUWARSA';
            } else if (isCooldown) {
                playDeniedSound();
                statusBanner.className = 'p-4 sm:p-5 rounded-2xl flex items-center justify-between text-white shadow-lg bg-gradient-to-r from-amber-600 to-orange-700';
                statusIcon.className = 'fas fa-clock';
                statusTitle.innerText = 'JEDA SCAN (ANTI-REDUNDANSI)';
                digitalCardMockup.classList.add('ring-4', 'ring-amber-500');
                cardStampOverlay.classList.add('hidden');
            } else {
                playDeniedSound();
                statusBanner.className = 'p-4 sm:p-5 rounded-2xl flex items-center justify-between text-white shadow-lg bg-gradient-to-r from-red-600 to-red-800';
                statusIcon.className = 'fas fa-times-circle';
                statusTitle.innerText = 'AKSES DITOLAK';
                digitalCardMockup.classList.add('denied-glow', 'ring-4', 'ring-rose-600');

                // Tampilkan Stamp Ditolak
                cardStampOverlay.classList.remove('hidden');
                cardStampOverlay.className = 'absolute inset-0 z-30 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-[2px]';
                cardStampBadge.className = 'transform -rotate-12 border-4 border-amber-400 bg-amber-600/95 text-white font-black text-xs tracking-wider px-3.5 py-2 uppercase rounded-xl shadow-2xl';
                cardStampBadge.innerHTML = `<i class="fas fa-triangle-exclamation mr-1.5"></i> DITOLAK DI AREA {{ $device->kode_area }}`;
            }

            statusMessage.innerText = data.message || data.pesan || data.alasan || '';
            statusArahBadge.innerText = (c.tipe_aktivitas || currentScanMode).toUpperCase();

            // Populate Dossier Text
            resNomorKartu.innerText = c.nomor_kartu || qrInput?.value || '-';
            resNama.innerText = c.nama_pemegang || '-';
            resPerusahaan.innerText = c.perusahaan || '-';
            resJabatan.innerText = c.jabatan || '-';
            resTglBerlaku.innerText = c.tanggal_berlaku || c.tanggal_berlaku_short || '-';

            if (isKadaluarsa) {
                resStatusMasa.innerHTML = '<span class="px-2 py-0.5 rounded text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">Kadaluarsa</span>';
            } else if (isValid) {
                resStatusMasa.innerHTML = '<span class="px-2 py-0.5 rounded text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">Masih Berlaku</span>';
            } else {
                resStatusMasa.innerHTML = '<span class="text-slate-400">-</span>';
            }

            // Render area list di Dossier
            resAreaList.innerHTML = '';
            const areaArray = Array.isArray(c.area_akses) ? c.area_akses : (typeof c.area_akses === 'string' ? c.area_akses.split(',').map(s=>s.trim()) : []);
            if (areaArray.length > 0) {
                areaArray.forEach(area => {
                    const isMatch = area === '{{ $device->kode_area }}';
                    const badge = document.createElement('span');
                    badge.className = isMatch 
                        ? 'px-2.5 py-0.5 rounded-md text-xs font-black bg-emerald-600 text-white shadow-xs border border-emerald-700'
                        : 'px-2 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200';
                    badge.innerText = area;
                    resAreaList.appendChild(badge);
                });
            } else {
                resAreaList.innerText = '-';
            }

            resCatatanText.innerText = data.alasan || data.keterangan || '-';
            scanTimestamp.innerText = 'Waktu Scan: ' + new Date().toLocaleTimeString('id-ID');

            // ========================================================
            // RENDER THE VISUAL DIGITAL PAS CARD WITH TEMPLATE
            // ========================================================
            if (tpl) {
                cardTemplateBadge.innerText = tpl.nama_template || 'Template PAS';
                digitalCardMockup.style.backgroundColor = tpl.warna_hex || '#2563EB';

                if (tpl.gambar_url) {
                    cardBgImage.src = tpl.gambar_url;
                    cardBgImage.classList.remove('hidden');
                } else {
                    cardBgImage.classList.add('hidden');
                }

                const pos = tpl.posisi || {};
                const textColor = tpl.warna_teks || '#FFFFFF';

                // 1. Foto
                if (pos.foto && pos.foto.visible !== false) {
                    cardElFoto.style.top = (pos.foto.top ?? 28) + '%';
                    cardElFoto.style.left = (pos.foto.left ?? 49.5) + '%';
                    cardElFoto.style.width = (pos.foto.width ?? 43.5) + '%';
                    cardElFoto.style.height = (pos.foto.height ?? 30.5) + '%';
                    cardElFoto.classList.remove('hidden');

                    const fotoSrc = c.foto_url || c.foto;
                    if (fotoSrc) {
                        cardFotoImg.src = fotoSrc;
                        cardFotoImg.classList.remove('hidden');
                        cardFotoPlaceholder.classList.add('hidden');
                    } else {
                        cardFotoImg.classList.add('hidden');
                        cardFotoPlaceholder.classList.remove('hidden');
                    }
                } else {
                    cardElFoto.classList.add('hidden');
                }

                // 2. Masa Berlaku
                if (pos.masa_berlaku && pos.masa_berlaku.visible !== false) {
                    cardElMasaBerlaku.style.top = (pos.masa_berlaku.top ?? 22.5) + '%';
                    cardElMasaBerlaku.style.left = (pos.masa_berlaku.left ?? 49.5) + '%';
                    cardElMasaBerlaku.style.width = (pos.masa_berlaku.width ?? 43.5) + '%';
                    cardElMasaBerlaku.style.fontSize = (pos.masa_berlaku.font_size ?? 11) + 'px';
                    cardElMasaBerlaku.style.color = pos.masa_berlaku.color || textColor;
                    cardElMasaBerlaku.innerText = c.tanggal_berlaku || c.tanggal_berlaku_short || '--';
                    cardElMasaBerlaku.classList.remove('hidden');
                } else {
                    cardElMasaBerlaku.classList.add('hidden');
                }

                // 3. Area Akses
                if (pos.area_akses && pos.area_akses.visible !== false) {
                    cardElAreaAkses.style.top = (pos.area_akses.top ?? 27) + '%';
                    cardElAreaAkses.style.left = (pos.area_akses.left ?? 12) + '%';
                    cardElAreaAkses.style.width = (pos.area_akses.width ?? 28) + '%';
                    cardElAreaAkses.style.fontSize = (pos.area_akses.font_size ?? 20) + 'px';
                    cardElAreaAkses.style.color = pos.area_akses.color || textColor;

                    if ((pos.area_akses.direction ?? 'vertical') === 'horizontal') {
                        cardElAreaAkses.className = 'absolute font-mono font-black z-10 drop-shadow-sm flex flex-row gap-1.5 items-center justify-center leading-none';
                    } else {
                        cardElAreaAkses.className = 'absolute font-mono font-black z-10 drop-shadow-sm flex flex-col gap-1 items-center justify-center leading-none';
                    }

                    cardElAreaAkses.innerHTML = '';
                    areaArray.forEach(code => {
                        const span = document.createElement('span');
                        span.className = 'block leading-tight drop-shadow-sm';
                        span.innerText = code;
                        cardElAreaAkses.appendChild(span);
                    });
                    cardElAreaAkses.classList.remove('hidden');
                } else {
                    cardElAreaAkses.classList.add('hidden');
                }

                // 4. Nama Pemegang
                if (pos.nama_pemegang && pos.nama_pemegang.visible !== false) {
                    cardElNama.style.top = (pos.nama_pemegang.top ?? 67.5) + '%';
                    cardElNama.style.left = (pos.nama_pemegang.left ?? 7) + '%';
                    cardElNama.style.width = (pos.nama_pemegang.width ?? 58) + '%';
                    cardElNama.style.fontSize = (pos.nama_pemegang.font_size ?? 11) + 'px';
                    cardElNama.style.color = pos.nama_pemegang.color || textColor;
                    cardElNama.innerText = c.nama_pemegang ? (pos.nama_pemegang.uppercase ? c.nama_pemegang.toUpperCase() : c.nama_pemegang) : '--';
                    cardElNama.classList.remove('hidden');
                } else {
                    cardElNama.classList.add('hidden');
                }

                // 5. Jabatan
                if (pos.jabatan && pos.jabatan.visible !== false) {
                    cardElJabatan.style.top = (pos.jabatan.top ?? 72.5) + '%';
                    cardElJabatan.style.left = (pos.jabatan.left ?? 7) + '%';
                    cardElJabatan.style.width = (pos.jabatan.width ?? 58) + '%';
                    cardElJabatan.style.fontSize = (pos.jabatan.font_size ?? 9) + 'px';
                    cardElJabatan.style.color = pos.jabatan.color || textColor;
                    cardElJabatan.innerText = c.jabatan ? (pos.jabatan.uppercase ? c.jabatan.toUpperCase() : c.jabatan) : '--';
                    cardElJabatan.classList.remove('hidden');
                } else {
                    cardElJabatan.classList.add('hidden');
                }

                // 6. Instansi
                if (pos.instansi && pos.instansi.visible !== false) {
                    cardElInstansi.style.top = (pos.instansi.top ?? 77) + '%';
                    cardElInstansi.style.left = (pos.instansi.left ?? 7) + '%';
                    cardElInstansi.style.width = (pos.instansi.width ?? 58) + '%';
                    cardElInstansi.style.fontSize = (pos.instansi.font_size ?? 9) + 'px';
                    cardElInstansi.style.color = pos.instansi.color || textColor;
                    cardElInstansi.innerText = c.perusahaan ? (pos.instansi.uppercase ? c.perusahaan.toUpperCase() : c.perusahaan) : '--';
                    cardElInstansi.classList.remove('hidden');
                } else {
                    cardElInstansi.classList.add('hidden');
                }

                // 7. No Registrasi
                if (pos.no_registrasi && pos.no_registrasi.visible !== false) {
                    cardElNoRegistrasi.style.top = (pos.no_registrasi.top ?? 81.5) + '%';
                    cardElNoRegistrasi.style.left = (pos.no_registrasi.left ?? 7) + '%';
                    cardElNoRegistrasi.style.width = (pos.no_registrasi.width ?? 58) + '%';
                    cardElNoRegistrasi.style.fontSize = (pos.no_registrasi.font_size ?? 8.5) + 'px';
                    cardElNoRegistrasi.style.color = pos.no_registrasi.color || textColor;
                    cardElNoRegistrasi.innerText = c.nomor_kartu || '--';
                    cardElNoRegistrasi.classList.remove('hidden');
                } else {
                    cardElNoRegistrasi.classList.add('hidden');
                }

                // 8. QR Code
                if (pos.qr_code && pos.qr_code.visible !== false) {
                    cardElQrCode.style.top = (pos.qr_code.top ?? 68) + '%';
                    cardElQrCode.style.left = (pos.qr_code.left ?? 68) + '%';
                    cardElQrCode.style.width = (pos.qr_code.width ?? 24) + '%';
                    cardElQrCode.style.height = (pos.qr_code.height ?? 24) + '%';
                    cardElQrCode.classList.remove('hidden');
                } else {
                    cardElQrCode.classList.add('hidden');
                }

            } else {
                // Fallback jika tidak ada template
                cardTemplateBadge.innerText = 'Standar';
                digitalCardMockup.style.backgroundColor = '#1e3a5f';
                cardBgImage.classList.add('hidden');
            }

            // Tambahkan ke riwayat tabel scan
            addLogRow(data);
        }

        function addLogRow(data) {
            const tbody = document.getElementById('recentLogsTableBody');
            const noData = document.getElementById('noDataRow');
            if (noData) noData.remove();

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/80 transition-colors animate-fade-in';

            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID');
            const isValid = (data.success === true) || (data.status === 'diterima');
            const c = data.data || data.kartu || {};

            tr.innerHTML = `
                <td class="px-5 py-3 font-mono text-xs text-slate-500 whitespace-nowrap">${timeStr}</td>
                <td class="px-5 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">${c.nomor_kartu || '-'}</td>
                <td class="px-5 py-3 font-bold text-slate-900">${c.nama_pemegang || '-'}</td>
                <td class="px-5 py-3 text-slate-600 text-xs">${c.perusahaan || '-'}</td>
                <td class="px-5 py-3 text-center whitespace-nowrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase ${currentScanMode === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                        ${c.tipe_aktivitas || currentScanMode}
                    </span>
                </td>
                <td class="px-5 py-3 text-center whitespace-nowrap">
                    ${isValid 
                        ? '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fas fa-check-circle text-[10px]"></i> VALID</span>'
                        : '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fas fa-times-circle text-[10px]"></i> DITOLAK</span>'
                    }
                </td>
                <td class="px-5 py-3 text-xs text-slate-600 max-w-xs truncate" title="${data.alasan || data.message || ''}">
                    ${data.alasan || data.message || '-'}
                </td>
            `;

            tbody.insertBefore(tr, tbody.firstChild);

            // Jaga hanya 10 baris
            if (tbody.children.length > 10) {
                tbody.removeChild(tbody.lastChild);
            }
        }
    </script>
</x-app-layout>
