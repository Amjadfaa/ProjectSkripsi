<x-app-layout>
    <x-slot name="header">
        Terminal Scanner QR — {{ $device->nama_kamera }}
    </x-slot>

    <!-- html5-qrcode library for webcam QR scanning -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="space-y-6">
        <!-- Device Info & Action Header Bar -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-[#1e3a5f] text-white flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-extrabold text-gray-900">{{ $device->nama_kamera }}</h2>
                        <span class="bg-[#1e3a5f] text-white text-xs px-2.5 py-0.5 rounded-full font-bold">
                            AREA {{ $device->kode_area }}
                        </span>
                        <span class="bg-amber-100 text-amber-800 text-[11px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">
                            {{ str_replace('_', ' ', $device->tipe_scan) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> ONLINE
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                        Lokasi Area: <strong>{{ optional($device->areaAkses)->nama_area ?? 'Area Bandara' }}</strong>
                        &bull; Kode Akses: <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $device->kode_akses }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Arah Scan Switcher jika tipe masuk_keluar -->
                @if($device->tipe_scan === 'masuk_keluar')
                    <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200">
                        <button type="button" id="btnModeMasuk" onclick="setScanMode('masuk')"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-emerald-600 text-white shadow-sm">
                            <i class="fas fa-sign-in-alt"></i> MASUK
                        </button>
                        <button type="button" id="btnModeKeluar" onclick="setScanMode('keluar')"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt"></i> KELUAR
                        </button>
                    </div>
                @else
                    <div class="px-3 py-1.5 rounded-xl bg-gray-100 border border-gray-200 text-xs font-bold uppercase text-gray-700">
                        <i class="fas {{ $device->tipe_scan === 'masuk' ? 'fa-sign-in-alt text-emerald-600' : 'fa-sign-out-alt text-amber-600' }} mr-1"></i>
                        MODE {{ strtoupper($device->tipe_scan) }}
                    </div>
                @endif

                <!-- Audio Toggle -->
                <button type="button" id="btnAudioToggle" onclick="toggleAudio()"
                        class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold border border-gray-200 transition flex items-center gap-1.5">
                    <i class="fas fa-volume-up" id="audioIcon"></i>
                    <span id="audioText">Suara: ON</span>
                </button>

                <!-- Ganti Kamera -->
                <a href="{{ route('operator.kamera.index') }}"
                   class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold border border-gray-200 transition flex items-center gap-1.5">
                    <i class="fas fa-exchange-alt"></i> Ganti Kamera
                </a>

                <!-- Putuskan -->
                <form action="{{ route('operator.kamera.disconnect') }}" method="POST" onsubmit="return confirm('Putuskan sesi kamera ini?');">
                    @csrf
                    <button type="submit"
                            class="px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold border border-red-200 transition flex items-center gap-1.5">
                        <i class="fas fa-power-off"></i> Keluar Sesi
                    </button>
                </form>
            </div>
        </div>

        <!-- Scanner Grid: Kamera (Kiri) & Hasil Verifikasi (Kanan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Kolom Kiri: Kamera Scanner & Input Barcode -->
            <div class="lg:col-span-6 space-y-4">
                <!-- Webcam Frame Card -->
                <div class="bg-gray-900 rounded-2xl border border-gray-800 p-4 shadow-xl overflow-hidden">
                    <div class="flex items-center justify-between mb-3 text-white">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-300">Live Camera Stream</h3>
                        </div>
                        <button type="button" id="toggleWebcamBtn" onclick="toggleWebcam()"
                                class="text-xs bg-[#f0b429] hover:bg-amber-400 text-[#1e3a5f] px-3.5 py-1.5 rounded-lg font-bold transition flex items-center gap-1.5 shadow">
                            <i class="fas fa-camera"></i> <span id="btnText">Nyalakan Kamera</span>
                        </button>
                    </div>

                    <!-- Viewfinder Area -->
                    <div class="relative rounded-xl bg-black min-h-[340px] flex items-center justify-center overflow-hidden border border-gray-800">
                        <!-- Corner brackets -->
                        <div class="absolute top-3 left-3 w-6 h-6 border-t-2 border-l-2 border-amber-400 rounded-tl pointer-events-none z-10"></div>
                        <div class="absolute top-3 right-3 w-6 h-6 border-t-2 border-r-2 border-amber-400 rounded-tr pointer-events-none z-10"></div>
                        <div class="absolute bottom-3 left-3 w-6 h-6 border-b-2 border-l-2 border-amber-400 rounded-bl pointer-events-none z-10"></div>
                        <div class="absolute bottom-3 right-3 w-6 h-6 border-b-2 border-r-2 border-amber-400 rounded-br pointer-events-none z-10"></div>

                        <!-- Webcam element -->
                        <div id="reader" class="hidden w-full h-full"></div>

                        <!-- Placeholder jika kamera belum aktif -->
                        <div id="scannerPlaceholder" onclick="toggleWebcam()" class="flex flex-col items-center justify-center p-8 text-center cursor-pointer w-full h-full hover:bg-gray-800/40 transition">
                            <div class="w-16 h-16 rounded-2xl bg-amber-400/10 border border-amber-400/30 text-[#f0b429] flex items-center justify-center text-2xl mb-3">
                                <i class="fas fa-camera-retro"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-200 mb-1">Klik untuk Menyalakan Kamera</h4>
                            <p class="text-xs text-gray-400 max-w-xs">Atau gunakan scanner barcode / QR scanner USB untuk verifikasi otomatis</p>
                        </div>
                    </div>
                </div>

                <!-- Manual Input / Hardware USB Scanner -->
                <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <label for="qrInput" class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center gap-1.5">
                            <i class="fas fa-barcode text-amber-500"></i> Scanner Barcode USB / Input No. Kartu
                        </label>
                        <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                            Auto Focus Ready
                        </span>
                    </div>

                    <form id="scanForm" onsubmit="handleManualSubmit(event)" class="relative">
                        <input type="text" id="qrInput" autocomplete="off" placeholder="Scan Barcode atau ketik Nomor Kartu PAS..."
                               class="w-full bg-gray-50 border border-gray-300 text-gray-900 placeholder-gray-400 rounded-xl pl-4 pr-28 py-3 text-sm font-mono font-bold focus:outline-none focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/20 transition-all">
                        <button type="submit"
                                class="absolute right-1.5 top-1.5 bottom-1.5 bg-[#1e3a5f] hover:bg-[#284c7b] text-white px-4 rounded-lg text-xs font-bold tracking-wide transition shadow">
                            VERIFIKASI
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Hasil Verifikasi Realtime & Detail Kartu -->
            <div class="lg:col-span-6 space-y-4">
                <div id="resultCard" class="bg-white rounded-2xl border border-gray-200 shadow-md p-6 min-h-[440px] flex flex-col justify-between transition-all">
                    
                    <!-- State: Menunggu Pemindaian -->
                    <div id="idleState" class="flex flex-col items-center justify-center my-auto py-12 text-center text-gray-400">
                        <div class="w-20 h-20 rounded-3xl bg-gray-50 border-2 border-dashed border-gray-300 flex items-center justify-center text-3xl text-gray-300 mb-4 animate-pulse">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-700">Siap Melakukan Pemindaian</h3>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm">
                            Arahkan QR Code Kartu PAS ke kamera atau scan menggunakan barcode scanner USB.
                        </p>
                    </div>

                    <!-- State: Hasil Pemindaian (Hidden by default) -->
                    <div id="resultContent" class="hidden space-y-5">
                        <!-- Status Banner Header -->
                        <div id="statusBanner" class="p-4 rounded-xl flex items-center justify-between text-white shadow-md">
                            <div class="flex items-center gap-3">
                                <div id="statusIconWrap" class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 bg-white/20">
                                    <i id="statusIcon" class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h3 id="statusTitle" class="text-lg font-black tracking-wide uppercase">AKSES DIIZINKAN</h3>
                                    <p id="statusMessage" class="text-xs opacity-90">Kartu PAS sah dan terdaftar</p>
                                </div>
                            </div>
                            <div id="statusArahBadge" class="px-3 py-1 rounded-full text-xs font-black uppercase bg-white/20">
                                MASUK
                            </div>
                        </div>

                        <!-- Card Detail Pemegang PAS -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <!-- Foto Pemegang (Jika ada) -->
                            <div class="sm:col-span-4 flex flex-col items-center justify-center text-center">
                                <div id="fotoWrapper" class="w-28 h-36 rounded-xl bg-gray-200 border-2 border-gray-300 overflow-hidden shadow-inner flex items-center justify-center mb-2">
                                    <img id="pemegangFoto" src="" alt="Foto Pemegang" class="w-full h-full object-cover hidden">
                                    <i id="fotoPlaceholderIcon" class="fas fa-user text-4xl text-gray-400"></i>
                                </div>
                                <span class="text-[11px] font-semibold text-gray-500">Pas Foto PAS</span>
                            </div>

                            <!-- Biodata Kartu -->
                            <div class="sm:col-span-8 space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-400 uppercase text-[10px] font-bold block">Nomor Kartu PAS</span>
                                    <span id="resNomorKartu" class="font-mono text-base font-black text-gray-900">--</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 uppercase text-[10px] font-bold block">Nama Lengkap</span>
                                    <span id="resNama" class="text-sm font-bold text-gray-800">--</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Instansi / Perusahaan</span>
                                        <span id="resPerusahaan" class="font-semibold text-gray-700">--</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Jabatan</span>
                                        <span id="resJabatan" class="font-semibold text-gray-700">--</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Masa Berlaku</span>
                                        <span id="resTglBerlaku" class="font-semibold text-gray-700">--</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 uppercase text-[10px] font-bold block">Status Masa Berlaku</span>
                                        <span id="resStatusMasa" class="font-bold">--</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-400 uppercase text-[10px] font-bold block">Izin Area Akses Kartu</span>
                                    <div id="resAreaList" class="flex flex-wrap gap-1 mt-1">
                                        <!-- Area badges -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Petugas / Quick Remark -->
                        <div id="catatanBox" class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 flex items-start gap-2">
                            <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
                            <div class="flex-1">
                                <strong>Catatan Sistem:</strong>
                                <span id="resCatatanText" class="ml-1">--</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Scanner Status indicator -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                        <span id="scanTimestamp">Menunggu verifikasi...</span>
                        <span class="flex items-center gap-1.5 font-medium text-emerald-600">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Siap Pindai
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel 10 Pemindaian Terkini Perangkat Ini -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-history text-gray-400"></i>
                    <h3 class="font-bold text-gray-800 text-sm">Pemindaian Terkini di Perangkat Ini</h3>
                </div>
                <a href="{{ route('operator.kamera.logs') }}" class="text-xs text-blue-600 font-semibold hover:underline">
                    Lihat Riwayat Lengkap &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500 border-b border-gray-100">
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
                    <tbody id="recentLogsTableBody" class="divide-y divide-gray-100">
                        @forelse($recentLogs as $log)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-5 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->waktu_scan)->format('H:i:s') }}
                                </td>
                                <td class="px-5 py-3 font-mono font-semibold text-gray-900 whitespace-nowrap">
                                    {{ $log->nomor_kartu ?? '-' }}
                                </td>
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ $log->nama_pemegang ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 text-xs">
                                    {{ $log->perusahaan ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ ($log->tipe_aktivitas ?? 'masuk') === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $log->tipe_aktivitas ?? 'masuk' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    @if($log->status_akses === 'diterima')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fas fa-check-circle text-[10px]"></i> VALID
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <i class="fas fa-times-circle text-[10px]"></i> DITOLAK
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-600 max-w-xs truncate" title="{{ $log->alasan ?? $log->catatan }}">
                                    {{ $log->alasan ?? $log->catatan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr id="noDataRow">
                                <td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">
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
                icon.className = 'fas fa-volume-up';
                text.innerText = 'Suara: ON';
            } else {
                icon.className = 'fas fa-volume-mute';
                text.innerText = 'Suara: OFF';
            }
        }

        function setScanMode(mode) {
            currentScanMode = mode;
            const btnMasuk = document.getElementById('btnModeMasuk');
            const btnKeluar = document.getElementById('btnModeKeluar');
            if (!btnMasuk || !btnKeluar) return;

            if (mode === 'masuk') {
                btnMasuk.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-emerald-600 text-white shadow-sm';
                btnKeluar.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-gray-600 hover:text-gray-900';
            } else {
                btnKeluar.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-amber-600 text-white shadow-sm';
                btnMasuk.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-gray-600 hover:text-gray-900';
            }
        }

        // Auto Focus Input Box for USB Barcode Scanners
        const qrInput = document.getElementById('qrInput');
        document.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A' && e.target.tagName !== 'INPUT') {
                qrInput.focus();
            }
        });
        window.addEventListener('load', () => qrInput.focus());

        function handleManualSubmit(e) {
            e.preventDefault();
            const val = qrInput.value.trim();
            if (val) {
                processQrCode(val);
                qrInput.value = '';
            }
        }

        function toggleWebcam() {
            const btnText = document.getElementById('btnText');
            const placeholder = document.getElementById('scannerPlaceholder');
            const readerEl = document.getElementById('reader');

            if (isWebcamRunning) {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().then(() => {
                        isWebcamRunning = false;
                        btnText.innerText = 'Nyalakan Kamera';
                        readerEl.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                    }).catch(err => console.error(err));
                }
            } else {
                placeholder.classList.add('hidden');
                readerEl.classList.remove('hidden');

                html5QrcodeScanner = new Html5Qrcode("reader");
                const scanConfig = { 
                    fps: 30, 
                    qrbox: (w, h) => ({ width: Math.floor(w * 0.9), height: Math.floor(h * 0.9) })
                };

                html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    scanConfig,
                    (decodedText) => {
                        if (!isProcessing) {
                            processQrCode(decodedText);
                        }
                    },
                    (err) => {}
                ).then(() => {
                    isWebcamRunning = true;
                    btnText.innerText = 'Matikan Kamera';
                }).catch(err => {
                    alert('Gagal membuka webcam: ' + err);
                    readerEl.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                });
            }
        }

        async function processQrCode(qrData) {
            if (isProcessing) return;
            isProcessing = true;

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
                    qrInput.focus();
                }, 1500);
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

            const pemegangFoto = document.getElementById('pemegangFoto');
            const fotoPlaceholderIcon = document.getElementById('fotoPlaceholderIcon');

            idleState.classList.add('hidden');
            resultContent.classList.remove('hidden');

            const isValid = (data.success === true) || (data.status === 'diterima') || (data.status_izin === 'valid');
            const isKadaluarsa = Boolean(data.is_kadaluarsa);
            const isCooldown = data.status === 'cooldown';

            if (isValid) {
                playSuccessSound();
                statusBanner.className = 'p-4 rounded-xl flex items-center justify-between text-white shadow-md bg-gradient-to-r from-emerald-600 to-teal-700';
                statusIcon.className = 'fas fa-check-circle';
                statusTitle.innerText = 'AKSES DIIZINKAN (VALID)';
            } else if (isKadaluarsa) {
                playDeniedSound();
                statusBanner.className = 'p-4 rounded-xl flex items-center justify-between text-white shadow-md bg-gradient-to-r from-rose-700 to-red-800';
                statusIcon.className = 'fas fa-calendar-times';
                statusTitle.innerText = 'KARTU SUDAH KADALUARSA';
            } else if (isCooldown) {
                playDeniedSound();
                statusBanner.className = 'p-4 rounded-xl flex items-center justify-between text-white shadow-md bg-gradient-to-r from-amber-600 to-orange-700';
                statusIcon.className = 'fas fa-clock';
                statusTitle.innerText = 'JEDA SCAN (ANTI-REDUNDANSI)';
            } else {
                playDeniedSound();
                statusBanner.className = 'p-4 rounded-xl flex items-center justify-between text-white shadow-md bg-gradient-to-r from-red-600 to-red-800';
                statusIcon.className = 'fas fa-times-circle';
                statusTitle.innerText = 'AKSES DITOLAK';
            }

            statusMessage.innerText = data.message || data.pesan || data.alasan || '';
            const c = data.data || data.kartu || {};
            statusArahBadge.innerText = (c.tipe_aktivitas || currentScanMode).toUpperCase();

            // Populate Card Data
            resNomorKartu.innerText = c.nomor_kartu || qrInput.value || '-';
            resNama.innerText = c.nama_pemegang || '-';
            resPerusahaan.innerText = c.perusahaan || '-';
            resJabatan.innerText = c.jabatan || '-';
            resTglBerlaku.innerText = c.tanggal_berlaku || c.tanggal_berlaku_formatted || '-';

            if (isKadaluarsa) {
                resStatusMasa.innerHTML = '<span class="text-red-600">Kadaluarsa</span>';
            } else if (isValid) {
                resStatusMasa.innerHTML = '<span class="text-emerald-600">Masih Berlaku</span>';
            } else {
                resStatusMasa.innerHTML = '<span class="text-gray-500">-</span>';
            }

            // Render area izin
            resAreaList.innerHTML = '';
            if (c.area_akses && Array.isArray(c.area_akses)) {
                c.area_akses.forEach(area => {
                    const isMatch = area === '{{ $device->kode_area }}';
                    const badge = document.createElement('span');
                    badge.className = isMatch 
                        ? 'px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300'
                        : 'px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200';
                    badge.innerText = area;
                    resAreaList.appendChild(badge);
                });
            } else {
                resAreaList.innerText = '-';
            }

            // Foto Pemegang
            if (c.foto_url) {
                pemegangFoto.src = c.foto_url;
                pemegangFoto.classList.remove('hidden');
                fotoPlaceholderIcon.classList.add('hidden');
            } else {
                pemegangFoto.classList.add('hidden');
                fotoPlaceholderIcon.classList.remove('hidden');
            }

            resCatatanText.innerText = data.alasan || data.keterangan || '-';
            scanTimestamp.innerText = 'Waktu Scan: ' + new Date().toLocaleTimeString('id-ID');

            // Tambahkan ke tabel pemindaian terkini
            addLogRow(data);
        }

        function addLogRow(data) {
            const tbody = document.getElementById('recentLogsTableBody');
            const noData = document.getElementById('noDataRow');
            if (noData) noData.remove();

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/80 transition-colors animate-fade-in';

            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID');
            const isValid = (data.success === true) || (data.status === 'diterima');
            const c = data.data || data.kartu || {};

            tr.innerHTML = `
                <td class="px-5 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">${timeStr}</td>
                <td class="px-5 py-3 font-mono font-semibold text-gray-900 whitespace-nowrap">${c.nomor_kartu || '-'}</td>
                <td class="px-5 py-3 font-medium text-gray-900">${c.nama_pemegang || '-'}</td>
                <td class="px-5 py-3 text-gray-600 text-xs">${c.perusahaan || '-'}</td>
                <td class="px-5 py-3 text-center whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase ${currentScanMode === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">
                        ${c.tipe_aktivitas || currentScanMode}
                    </span>
                </td>
                <td class="px-5 py-3 text-center whitespace-nowrap">
                    ${isValid 
                        ? '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fas fa-check-circle text-[10px]"></i> VALID</span>'
                        : '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200"><i class="fas fa-times-circle text-[10px]"></i> DITOLAK</span>'
                    }
                </td>
                <td class="px-5 py-3 text-xs text-gray-600 max-w-xs truncate" title="${data.alasan || data.message || ''}">
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
