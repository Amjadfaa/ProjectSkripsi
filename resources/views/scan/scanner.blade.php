<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terminal Scanner QR PAS - {{ $device->nama_kamera }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- html5-qrcode library for webcam QR scanning -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 50% 0%, #1e293b 0%, #0f172a 60%, #090d16 100%);
            color: #f8fafc;
            min-height: 100vh;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(51, 65, 85, 0.6);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        .scan-viewfinder {
            position: relative;
            border-radius: 16px;
            background: #020617;
            border: 2px dashed rgba(59, 130, 246, 0.35);
            overflow: hidden;
        }
        #reader {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
        }
        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 14px;
        }
        #reader__scan_region {
            background: transparent !important;
        }
        #reader__dashboard {
            display: none !important;
        }
        
        .status-idle {
            background: rgba(15, 23, 42, 0.6);
            border: 2px solid rgba(51, 65, 85, 0.5);
        }
        .status-granted {
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.9) 0%, rgba(2, 44, 34, 0.95) 100%);
            border: 2px solid #10b981;
            box-shadow: 0 0 40px rgba(16, 185, 129, 0.35);
            animation: pulseGreen 1.5s infinite;
        }
        .status-denied {
            background: linear-gradient(135deg, rgba(127, 29, 29, 0.9) 0%, rgba(69, 10, 10, 0.95) 100%);
            border: 2px solid #ef4444;
            box-shadow: 0 0 40px rgba(239, 68, 68, 0.35);
            animation: pulseRed 1.5s infinite;
        }
        .status-cooldown {
            background: linear-gradient(135deg, rgba(120, 53, 15, 0.9) 0%, rgba(69, 26, 3, 0.95) 100%);
            border: 2px solid #f59e0b;
            box-shadow: 0 0 40px rgba(245, 158, 11, 0.35);
            animation: pulseAmber 1.5s infinite;
        }
        @keyframes pulseGreen {
            0%, 100% { box-shadow: 0 0 25px rgba(16, 185, 129, 0.3); }
            50% { box-shadow: 0 0 50px rgba(16, 185, 129, 0.65); }
        }
        @keyframes pulseRed {
            0%, 100% { box-shadow: 0 0 25px rgba(239, 68, 68, 0.3); }
            50% { box-shadow: 0 0 50px rgba(239, 68, 68, 0.65); }
        }
        @keyframes pulseAmber {
            0%, 100% { box-shadow: 0 0 25px rgba(245, 158, 11, 0.3); }
            50% { box-shadow: 0 0 50px rgba(245, 158, 11, 0.65); }
        }

        /* Viewfinder corner brackets */
        .corner-tl { position: absolute; top: 12px; left: 12px; width: 24px; height: 24px; border-top: 3px solid #3b82f6; border-left: 3px solid #3b82f6; border-top-left-radius: 6px; z-index: 15; pointer-events: none; }
        .corner-tr { position: absolute; top: 12px; right: 12px; width: 24px; height: 24px; border-top: 3px solid #3b82f6; border-right: 3px solid #3b82f6; border-top-right-radius: 6px; z-index: 15; pointer-events: none; }
        .corner-bl { position: absolute; bottom: 12px; left: 12px; width: 24px; height: 24px; border-bottom: 3px solid #3b82f6; border-left: 3px solid #3b82f6; border-bottom-left-radius: 6px; z-index: 15; pointer-events: none; }
        .corner-br { position: absolute; bottom: 12px; right: 12px; width: 24px; height: 24px; border-bottom: 3px solid #3b82f6; border-right: 3px solid #3b82f6; border-bottom-right-radius: 6px; z-index: 15; pointer-events: none; }

        /* Custom Scrollbar for Scan History */
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.6); border-radius: 8px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 8px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <!-- Topbar Header -->
    <header class="bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80 px-6 py-3.5 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-40">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-xl font-extrabold shadow-lg shadow-blue-500/20 shrink-0">
                <i class="fas fa-video"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-base font-extrabold text-white tracking-wide">{{ $device->nama_kamera }}</h1>
                    <span class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[11px] px-2.5 py-0.5 rounded-full font-bold shadow-sm">
                        AREA {{ $device->kode_area }}
                    </span>
                    <span class="bg-slate-800 text-amber-400 border border-slate-700 text-[10px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">
                        SCAN {{ str_replace('_', ' ', $device->tipe_scan) }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    Lokasi: <span class="text-slate-200 font-medium">{{ optional($device->areaAkses)->keterangan ?? 'Area Terbatas Bandara' }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Digital Clock Ticker -->
            <div class="hidden sm:flex flex-col items-end text-right border-r border-slate-800 pr-4">
                <span id="liveClock" class="text-sm font-mono font-bold text-blue-400 leading-tight">00:00:00</span>
                <span id="liveDate" class="text-[10px] text-slate-400 font-medium">--</span>
            </div>

            <!-- Active Status Tag -->
            <div class="flex items-center gap-2 bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 text-xs px-3 py-1.5 rounded-xl font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shadow-sm shadow-emerald-400"></span>
                PERANGKAT AKTIF (60 FPS)
            </div>

            <!-- Logout -->
            <form method="POST" action="{{ route('scan.logout') }}">
                @csrf
                <button type="submit" class="bg-slate-800 hover:bg-rose-950 hover:text-rose-300 hover:border-rose-700/50 text-slate-300 px-3.5 py-1.5 rounded-xl text-xs font-bold border border-slate-700 transition flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-power-off"></i> Keluar
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 p-4 lg:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 max-w-[1600px] w-full mx-auto items-stretch">

        <!-- Left Column: Camera Scanner & Manual Input -->
        <div class="lg:col-span-5 flex flex-col h-full gap-5">
            
            <!-- Live WebCam Card -->
            <div class="glass-card rounded-2xl p-5 flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3.5 shrink-0">
                    <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                        <i class="fas fa-qrcode text-blue-400 text-sm"></i> Kamera Barcode Live Scanner (60 FPS)
                    </h2>
                    <button type="button" id="toggleWebcamBtn" onclick="toggleWebcam()"
                            class="text-xs bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-4 py-1.5 rounded-xl font-bold shadow-md shadow-blue-600/30 transition flex items-center gap-1.5">
                        <i class="fas fa-camera"></i> <span id="btnText">Start Webcam</span>
                    </button>
                </div>

                <!-- Viewfinder Box -->
                <div class="scan-viewfinder flex-1 min-h-[380px] flex items-center justify-center relative w-full">
                    
                    <div class="corner-tl"></div>
                    <div class="corner-tr"></div>
                    <div class="corner-bl"></div>
                    <div class="corner-br"></div>

                    <!-- Webcam stream container -->
                    <div id="reader" class="hidden"></div>

                    <!-- Centered Placeholder -->
                    <div id="scannerPlaceholder" onclick="toggleWebcam()" class="flex flex-col items-center justify-center p-6 text-center z-10 cursor-pointer w-full h-full hover:bg-slate-900/40 transition">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600/10 border border-blue-500/30 text-blue-400 flex items-center justify-center text-2xl mb-3 shadow-inner">
                            <i class="fas fa-camera-retro"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-200 mb-1">Klik untuk Mengaktifkan Kamera 60 FPS</h4>
                        <p class="text-xs text-slate-400 max-w-[250px]">Atau gunakan Hardware Barcode / QR Scanner USB otomatis</p>
                    </div>

                </div>
            </div>

            <!-- Hardware Barcode Scanner / Manual Input Form -->
            <div class="glass-card rounded-2xl p-5 shrink-0">
                <div class="flex items-center justify-between mb-2.5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                        <i class="fas fa-barcode text-amber-400"></i> Input Manual / Scanner USB
                    </h3>
                    <span class="text-[10px] text-emerald-400 font-bold bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-800/50 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> AUTO-FOCUS READY
                    </span>
                </div>

                <form id="scanForm" onsubmit="handleManualSubmit(event)" class="relative">
                    <input type="text" id="qrInput" autocomplete="off" placeholder="Scan Barcode / Ketik No. Kartu PAS..."
                           class="w-full bg-slate-950/90 border border-slate-700/80 text-white placeholder-slate-500 rounded-xl pl-4 pr-28 py-3 text-sm font-mono font-bold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-inner">
                    <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 bg-blue-600 hover:bg-blue-500 text-white px-4 rounded-lg text-xs font-extrabold tracking-wide transition shadow-md">
                        VERIFIKASI
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Column: Verification Result & Log History -->
        <div class="lg:col-span-7 flex flex-col h-full gap-5">

            <!-- Result Card Panel -->
            <div id="resultPanel" class="status-idle rounded-2xl p-6 transition-all duration-300 min-h-[250px] flex flex-col justify-center relative overflow-hidden shrink-0">
                
                <!-- Idle State -->
                <div id="idleState" class="text-center text-slate-400 py-8 flex flex-col items-center justify-center">
                    <div class="w-20 h-20 rounded-3xl bg-slate-800/60 border border-slate-700/60 text-slate-500 flex items-center justify-center text-3xl mb-3 shadow-xl">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3 class="text-base font-black tracking-wide text-slate-200">SIAP MEMINDAI KARTU PAS</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-md">Dekatkan QR Code Kartu PAS ke depan kamera atau tembakkan sinar barcode scanner ke kartu</p>
                </div>

                <!-- Result Content State -->
                <div id="resultContent" class="hidden">
                    <!-- Status Header Banner -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-white/15 mb-4">
                        <div class="flex items-center gap-3.5">
                            <div id="resultIconBg" class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl font-black shrink-0 shadow-lg">
                                <i id="resultIcon" class="fas"></i>
                            </div>
                            <div>
                                <h2 id="resultTitle" class="text-2xl font-black tracking-wider uppercase leading-none"></h2>
                                <p id="resultSubtitle" class="text-xs opacity-90 font-semibold mt-1"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span id="resultTime" class="text-xs font-mono font-bold bg-black/40 px-3 py-1.5 rounded-xl border border-white/10 block"></span>
                        </div>
                    </div>

                    <!-- Holder Details Cards -->
                    <div id="resultDetails" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div class="bg-black/25 p-3.5 rounded-xl border border-white/10">
                            <span class="text-[11px] opacity-70 block font-semibold uppercase tracking-wider text-slate-300">Nama Pemegang</span>
                            <span id="resNama" class="font-extrabold text-white text-base block mt-0.5"></span>
                        </div>
                        <div class="bg-black/25 p-3.5 rounded-xl border border-white/10">
                            <span class="text-[11px] opacity-70 block font-semibold uppercase tracking-wider text-slate-300">Instansi / Perusahaan</span>
                            <span id="resPerusahaan" class="font-extrabold text-white text-base block mt-0.5"></span>
                        </div>
                        <div class="bg-black/25 p-3.5 rounded-xl border border-white/10">
                            <span class="text-[11px] opacity-70 block font-semibold uppercase tracking-wider text-slate-300">Nomor Kartu PAS</span>
                            <span id="resNoKartu" class="font-mono font-black text-amber-300 text-sm block mt-0.5"></span>
                        </div>
                        <div class="bg-black/25 p-3.5 rounded-xl border border-white/10">
                            <span class="text-[11px] opacity-70 block font-semibold uppercase tracking-wider text-slate-300">Area Akses Terdaftar</span>
                            <span id="resAreaAkses" class="font-bold text-blue-300 text-sm block mt-0.5"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Recent Scan Activity Table (Scrollable Section) -->
            <div class="glass-card rounded-2xl p-5 flex-1 flex flex-col min-h-0">
                <div class="flex items-center justify-between mb-3.5 shrink-0">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                        <i class="fas fa-history text-slate-400"></i> Riwayat Scan Terbaru (Perangkat Ini)
                    </h3>
                    <span class="text-[11px] text-slate-400 font-medium">Scrollable Log</span>
                </div>

                <!-- Scrollable Table Box -->
                <div class="flex-1 overflow-y-auto custom-scroll border border-slate-800/80 rounded-xl min-h-[220px] max-h-[340px]">
                    <table class="w-full text-left text-xs">
                        <thead class="sticky top-0 bg-slate-950/95 backdrop-blur border-b border-slate-800 z-10">
                            <tr class="text-slate-400 uppercase tracking-wider text-[10px]">
                                <th class="p-3 font-bold">Waktu</th>
                                <th class="p-3 font-bold">No. Kartu</th>
                                <th class="p-3 font-bold">Nama Pemegang</th>
                                <th class="p-3 font-bold">Status Akses</th>
                                <th class="p-3 font-bold">Keterangan / Alasan</th>
                            </tr>
                        </thead>
                        <tbody id="logTableBody" class="divide-y divide-slate-800/50">
                            @forelse($recentLogs as $log)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="p-3 text-slate-400 font-mono font-semibold">{{ $log->waktu_scan->format('H:i:s') }}</td>
                                <td class="p-3 font-mono font-bold text-amber-400">{{ $log->nomor_kartu }}</td>
                                <td class="p-3 text-slate-200 font-bold">{{ $log->nama_pemegang }}</td>
                                <td class="p-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $log->status_akses === 'diterima' ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-700/50' : 'bg-rose-950/80 text-rose-400 border border-rose-700/50' }}">
                                        {{ $log->status_akses }}
                                    </span>
                                </td>
                                <td class="p-3 text-slate-400 font-medium">{{ $log->alasan }}</td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="5" class="py-8 text-center text-slate-500 font-medium">Belum ada riwayat scan pada sesi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>

    <script>
        let html5QrcodeScanner = null;
        let isWebcamRunning = false;
        let isProcessing = false;
        let cooldownTimerInterval = null;
        const lastScanTimes = {}; // Anti-redundancy cooldown map: { cardNo: timestamp }

        // Digital Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                            now.getMinutes().toString().padStart(2, '0') + ':' + 
                            now.getSeconds().toString().padStart(2, '0');
            const dateOptions = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            const dateStr = now.toLocaleDateString('id-ID', dateOptions);

            const clockEl = document.getElementById('liveClock');
            const dateEl = document.getElementById('liveDate');
            if (clockEl) clockEl.innerText = timeStr;
            if (dateEl) dateEl.innerText = dateStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Web Audio API Sound Synthesizer (Only for Granted Access)
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        
        function playSuccessSound() {
            try {
                if (audioCtx.state === 'suspended') audioCtx.resume();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.1); // A5
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.4);
            } catch(e) {}
        }

        // Auto Focus Input Box for USB Barcode Scanners
        const qrInput = document.getElementById('qrInput');
        document.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A' && e.target.tagName !== 'INPUT') {
                qrInput.focus();
            }
        });
        window.onload = () => qrInput.focus();

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
                        btnText.innerText = 'Start Webcam';
                        readerEl.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                    }).catch(err => {
                        console.error("Stop failed", err);
                    });
                }
            } else {
                placeholder.classList.add('hidden');
                readerEl.classList.remove('hidden');

                html5QrcodeScanner = new Html5Qrcode("reader");

                // High speed 60 FPS & 95% full-view scanning configuration
                const scanConfig = { 
                    fps: 60, 
                    qrbox: (viewfinderWidth, viewfinderHeight) => ({
                        width: Math.floor(viewfinderWidth * 0.95),
                        height: Math.floor(viewfinderHeight * 0.95)
                    }),
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                };

                html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    scanConfig,
                    (decodedText) => {
                        if (!isProcessing) {
                            processQrCode(decodedText);
                        }
                    },
                    (errorMessage) => {}
                ).then(() => {
                    isWebcamRunning = true;
                    btnText.innerText = 'Stop Webcam';
                }).catch(err => {
                    alert('Gagal membuka webcam 60 FPS: ' + err);
                    readerEl.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                });
            }
        }

        function startCooldownCountdown(cleanCode, remainingSec) {
            if (cooldownTimerInterval) clearInterval(cooldownTimerInterval);

            let secLeft = remainingSec;

            function updateText() {
                const sub = document.getElementById('resultSubtitle');
                const areaEl = document.getElementById('resAreaAkses');
                if (secLeft > 0) {
                    if (sub) sub.innerText = `Kartu [${cleanCode}] baru di-scan. Mohon tunggu ${secLeft} detik lagi untuk scan ulang.`;
                    if (areaEl) areaEl.innerText = `Harap tunggu ${secLeft} detik (Anti-Redundansi 1 Menit)`;
                } else {
                    clearInterval(cooldownTimerInterval);
                    cooldownTimerInterval = null;
                    if (sub) sub.innerText = `Kartu [${cleanCode}] sekarang sudah dapat di-scan kembali.`;
                    if (areaEl) areaEl.innerText = `Siap Scan Ulang (Jeda 1 Menit Selesai)`;
                }
            }

            updateText();

            cooldownTimerInterval = setInterval(() => {
                secLeft--;
                if (secLeft <= 0) {
                    updateText();
                    clearInterval(cooldownTimerInterval);
                    cooldownTimerInterval = null;
                } else {
                    updateText();
                }
            }, 1000);
        }

        function processQrCode(qrCode) {
            if (isProcessing) return;

            const cleanCode = qrCode.trim();
            const now = Date.now();
            const lastTime = lastScanTimes[cleanCode];

            // Client-Side Anti-Redundancy Cooldown (60 Seconds)
            if (lastTime && (now - lastTime < 60000)) {
                const remainingSec = Math.ceil((60000 - (now - lastTime)) / 1000);
                displayResult({
                    status: 'cooldown',
                    message: 'JEDA SCAN (Anti-Redundansi 1 Menit)',
                    alasan: 'Kartu [' + cleanCode + '] baru di-scan.',
                    remaining: remainingSec,
                    data: {
                        nomor_kartu: cleanCode,
                        waktu: new Date().toLocaleTimeString('id-ID')
                    }
                });
                return;
            }

            isProcessing = true;

            fetch("{{ route('scan.process') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: cleanCode })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'cooldown') {
                    // Update client cooldown tracker
                    lastScanTimes[cleanCode] = Date.now();
                } else if (data.status === 'diterima' || data.status === 'ditolak') {
                    lastScanTimes[cleanCode] = Date.now();
                    prependLogTable(data);
                }
                
                displayResult(data);
                setTimeout(() => { isProcessing = false; }, 600);
            })
            .catch(err => {
                console.error(err);
                isProcessing = false;
            });
        }

        function displayResult(res) {
            const panel = document.getElementById('resultPanel');
            const idle = document.getElementById('idleState');
            const content = document.getElementById('resultContent');

            idle.classList.add('hidden');
            content.classList.remove('hidden');

            const iconBg = document.getElementById('resultIconBg');
            const icon = document.getElementById('resultIcon');
            const title = document.getElementById('resultTitle');
            const sub = document.getElementById('resultSubtitle');
            const time = document.getElementById('resultTime');

            time.innerText = (res.data && res.data.waktu) ? res.data.waktu : new Date().toLocaleTimeString('id-ID');

            if (res.status === 'diterima') {
                playSuccessSound();
                if (cooldownTimerInterval) { clearInterval(cooldownTimerInterval); cooldownTimerInterval = null; }

                panel.className = 'status-granted rounded-2xl p-6 transition-all duration-300 text-white relative overflow-hidden';
                iconBg.className = 'w-14 h-14 rounded-2xl bg-emerald-400/20 text-emerald-300 flex items-center justify-center text-3xl font-black border border-emerald-400/40 shadow-inner';
                icon.className = 'fas fa-check-circle text-emerald-400';
                title.innerText = 'AKSES DITERIMA DI AREA ' + (res.data && res.data.area_kamera ? res.data.area_kamera : '{{ $device->kode_area }}');
                sub.innerText = 'Kartu PAS Valid & Diizinkan di Area ' + (res.data && res.data.area_kamera ? res.data.area_kamera : '{{ $device->kode_area }}');

                document.getElementById('resNama').innerText = res.data.nama_pemegang || '-';
                document.getElementById('resPerusahaan').innerText = res.data.perusahaan || '-';
                document.getElementById('resNoKartu').innerText = res.data.nomor_kartu || '-';
                document.getElementById('resAreaAkses').innerText = res.data.area_akses || '-';
            } else if (res.status === 'cooldown') {
                // NO SOUND on cooldown (user requested)
                panel.className = 'status-cooldown rounded-2xl p-6 transition-all duration-300 text-white relative overflow-hidden';
                iconBg.className = 'w-14 h-14 rounded-2xl bg-amber-400/20 text-amber-300 flex items-center justify-center text-3xl font-black border border-amber-400/40 shadow-inner';
                icon.className = 'fas fa-hourglass-half text-amber-400';
                title.innerText = 'JEDA SCAN KARTU (1 MENIT)';

                document.getElementById('resNama').innerText = (res.data && res.data.nama_pemegang) ? res.data.nama_pemegang : '-';
                document.getElementById('resPerusahaan').innerText = (res.data && res.data.perusahaan) ? res.data.perusahaan : '-';
                document.getElementById('resNoKartu').innerText = (res.data && res.data.nomor_kartu) ? res.data.nomor_kartu : '-';
                
                const rem = res.remaining || (res.data && res.data.remaining ? res.data.remaining : 60);
                startCooldownCountdown(res.data ? res.data.nomor_kartu : '', rem);
            } else {
                if (cooldownTimerInterval) { clearInterval(cooldownTimerInterval); cooldownTimerInterval = null; }

                panel.className = 'status-denied rounded-2xl p-6 transition-all duration-300 text-white relative overflow-hidden';
                iconBg.className = 'w-14 h-14 rounded-2xl bg-rose-400/20 text-rose-300 flex items-center justify-center text-3xl font-black border border-rose-400/40 shadow-inner';
                icon.className = 'fas fa-times-circle text-rose-400';
                title.innerText = 'AKSES DITOLAK!';
                sub.innerText = res.alasan || 'Tidak Diizinkan';

                document.getElementById('resNama').innerText = (res.data && res.data.nama_pemegang) ? res.data.nama_pemegang : '-';
                document.getElementById('resPerusahaan').innerText = (res.data && res.data.perusahaan) ? res.data.perusahaan : '-';
                document.getElementById('resNoKartu').innerText = (res.data && res.data.nomor_kartu) ? res.data.nomor_kartu : (res.data ? res.data.nomor_kartu : '-');
                document.getElementById('resAreaAkses').innerText = (res.data && res.data.area_dimiliki) ? 'Milik: ' + res.data.area_dimiliki + ' (Kamera: Area ' + res.data.area_kamera + ')' : '-';
            }
        }

        function prependLogTable(res) {
            // Ignore cooldown entries in history table
            if (res.status === 'cooldown') return;

            const tbody = document.getElementById('logTableBody');
            const empty = document.getElementById('emptyRow');
            if (empty) empty.remove();

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-800/40 transition animate-pulse';

            const now = new Date();
            const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ':' + now.getSeconds().toString().padStart(2, '0');

            const isGranted = (res.status === 'diterima');
            const statusBadge = isGranted 
                ? '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-950/80 text-emerald-400 border border-emerald-700/50">diterima</span>'
                : '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-950/80 text-rose-400 border border-rose-700/50">ditolak</span>';

            const noKartu = (res.data && res.data.nomor_kartu) ? res.data.nomor_kartu : '-';
            const nama = (res.data && res.data.nama_pemegang) ? res.data.nama_pemegang : '-';

            tr.innerHTML = `
                <td class="p-3 text-slate-400 font-mono font-semibold">${timeStr}</td>
                <td class="p-3 font-mono font-bold text-amber-400">${noKartu}</td>
                <td class="p-3 text-slate-200 font-bold">${nama}</td>
                <td class="p-3">${statusBadge}</td>
                <td class="p-3 text-slate-400 font-medium">${res.alasan || '-'}</td>
            `;

            // Insert new scan at the VERY TOP of the table
            tbody.insertBefore(tr, tbody.firstChild);
        }
    </script>

</body>
</html>
