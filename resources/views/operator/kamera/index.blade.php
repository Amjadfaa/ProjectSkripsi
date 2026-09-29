<x-app-layout>
    <x-slot name="header">
        Akses & Koneksi Kamera Scanner
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between gap-3 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="text-sm font-semibold">{{ session('success') }}</div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-xs p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center justify-between gap-3 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm shrink-0 shadow-xs">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="text-sm font-semibold">{{ session('error') }}</div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-xs p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Hero Header Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0f172a] via-[#1e3a5f] to-[#112948] text-white p-6 sm:p-8 shadow-xl border border-slate-700/60">
            <!-- Background Glow & Radar Visuals -->
            <div class="absolute -right-10 -top-10 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-12 bottom-0 opacity-10 pointer-events-none">
                <i class="fas fa-video text-9xl"></i>
            </div>
            <div class="absolute right-48 -bottom-10 opacity-5 pointer-events-none">
                <i class="fas fa-shield-halved text-8xl"></i>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2.5 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/15 border border-amber-400/30 text-amber-300 text-xs font-bold uppercase tracking-wider">
                        <i class="fas fa-shield-halved text-amber-400"></i> Terminal Akses Pos Scanner
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                        Koneksi & Akses Perangkat Kamera
                    </h1>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Pilih pos gerbang pemeriksaan bandara yang telah ditugaskan kepada Anda atau hubungkan manual menggunakan kode otorisasi resmi untuk memulai pemindaian QR PAS Bandara.
                    </p>
                </div>

                <!-- Operator Card Status Pill -->
                <div class="flex items-center gap-3.5 bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl shrink-0 shadow-inner">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-400 to-amber-500 text-slate-950 flex items-center justify-center font-black text-lg shadow-md shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-[150px]">
                        <div class="text-[11px] text-amber-300 font-bold uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Petugas Aktif
                        </div>
                        <div class="text-sm font-bold text-white leading-tight truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[11px] text-slate-300 mt-0.5 flex items-center gap-1">
                            <i class="fas fa-id-badge text-slate-400 text-[10px]"></i>
                            <span>{{ $availableDevices->count() }} Pos Kamera Diizinkan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kamera Sedang Terhubung (Active Session Console) -->
        @if($connectedDevice)
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-950/90 via-teal-900/85 to-[#064e3b] p-6 text-white shadow-xl border border-emerald-500/30 backdrop-blur-xl">
                <!-- Glowing corner effect -->
                <div class="absolute -right-8 -top-8 w-48 h-48 bg-emerald-400/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-2xl shrink-0 shadow-lg shadow-emerald-900/40 border border-white/20">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 text-[11px] font-bold uppercase tracking-wider">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                </span>
                                Sesi Kamera Sedang Aktif
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">{{ $connectedDevice->nama_kamera }}</h2>
                            <div class="flex flex-wrap items-center gap-2 text-xs text-emerald-200 pt-0.5">
                                <span class="bg-white/15 px-2.5 py-0.5 rounded-lg font-bold border border-white/20">
                                    <i class="fas fa-map-marker-alt text-amber-400 mr-1"></i> Area {{ $connectedDevice->kode_area }}
                                    @if($connectedDevice->areaAkses)
                                        — {{ $connectedDevice->areaAkses->nama_area }}
                                    @endif
                                </span>
                                <span class="bg-white/15 px-2.5 py-0.5 rounded-lg font-bold uppercase border border-white/20">
                                    <i class="fas fa-arrows-split-up-and-left text-teal-300 mr-1"></i> {{ strtoupper(str_replace('_', ' ', $connectedDevice->tipe_scan)) }}
                                </span>
                                <span class="bg-white/10 px-2 py-0.5 rounded-lg font-mono text-[11px] border border-white/10">
                                    KODE: <strong>{{ $connectedDevice->kode_akses }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('operator.kamera.scanner') }}"
                           class="bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-extrabold px-6 py-3 rounded-2xl text-sm shadow-lg shadow-amber-500/30 transition-all hover:scale-[1.03] flex items-center gap-2.5">
                            <i class="fas fa-qrcode text-base"></i>
                            <span>Buka Live Scanner</span>
                        </a>
                        <form action="{{ route('operator.kamera.disconnect') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memutuskan sesi kamera ini?');">
                            @csrf
                            <button type="submit"
                                    class="bg-white/10 hover:bg-rose-600/80 text-white font-semibold px-4 py-3 rounded-2xl text-sm transition-all border border-white/20 hover:border-rose-400/40 flex items-center gap-2"
                                    title="Putuskan Sesi Kamera">
                                <i class="fas fa-unlink text-xs"></i>
                                <span>Putuskan</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Kolom Kiri: Daftar Kamera Ditugaskan (8 Kolom) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-7">
                    <!-- Section Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 mb-5 border-b border-slate-100 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center text-lg shrink-0">
                                <i class="fas fa-video"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-base sm:text-lg text-slate-800">
                                    Pos Kamera Ditugaskan untuk Anda
                                </h3>
                                <p class="text-xs text-slate-500">Pilih pos pemeriksaan untuk terhubung langsung ke live scanner</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-check-shield text-[10px]"></i> {{ $availableDevices->count() }} Pos Siap Pakai
                            </span>
                        </div>
                    </div>

                    <!-- Grid Kartu Kamera -->
                    @if($availableDevices->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($availableDevices as $dev)
                                @php
                                    $isCurrent = $connectedDevice && $connectedDevice->id === $dev->id;
                                @endphp
                                <div class="relative group rounded-2xl border transition-all duration-200 p-5 flex flex-col justify-between gap-4 {{ $isCurrent ? 'bg-gradient-to-br from-emerald-50/70 to-teal-50/40 border-emerald-300 ring-2 ring-emerald-500/40 shadow-sm' : 'bg-white border-slate-200 hover:border-blue-400 hover:shadow-md' }}">
                                    <!-- Status Active Indicator Top Right -->
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-10 h-10 rounded-xl {{ $isCurrent ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 group-hover:bg-blue-50 group-hover:text-blue-700' }} flex items-center justify-center text-base transition-colors shrink-0">
                                                <i class="fas fa-camera"></i>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold tracking-wider bg-slate-900 text-white uppercase">
                                                        AREA {{ $dev->kode_area }}
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">
                                                        {{ str_replace('_', ' ', $dev->tipe_scan) }}
                                                    </span>
                                                </div>
                                                <h4 class="font-extrabold text-slate-900 text-sm mt-1 leading-snug group-hover:text-blue-700 transition-colors">
                                                    {{ $dev->nama_kamera }}
                                                </h4>
                                            </div>
                                        </div>

                                        @if($isCurrent)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-600 text-white flex items-center gap-1 shadow-xs shrink-0">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span> Aktif
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 flex items-center gap-1 shrink-0">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Siaga
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Area & Lokasi Details -->
                                    <div class="space-y-1.5 text-xs text-slate-500 pt-1 border-t border-slate-100">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-slate-400">Lokasi:</span>
                                            <span class="font-semibold text-slate-700 text-right truncate max-w-[180px]">
                                                {{ optional($dev->areaAkses)->nama_area ?? 'Area Akses Bandara' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-slate-400">Kode Akses:</span>
                                            <div class="flex items-center gap-1.5">
                                                <code class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px] border border-slate-200">
                                                    {{ $dev->kode_akses }}
                                                </code>
                                                <button type="button" onclick="salinKodeAkses('{{ $dev->kode_akses }}')"
                                                        class="text-slate-400 hover:text-blue-600 p-1 transition"
                                                        title="Salin Kode Akses">
                                                    <i class="far fa-copy text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Action -->
                                    <div class="pt-2">
                                        @if($isCurrent)
                                            <a href="{{ route('operator.kamera.scanner') }}"
                                               class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm transition hover:shadow-md">
                                                <i class="fas fa-qrcode"></i> Buka Live Scanner
                                            </a>
                                        @else
                                            <form action="{{ route('operator.kamera.connect') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="kode_akses" value="{{ $dev->kode_akses }}">
                                                <button type="submit"
                                                        class="w-full bg-[#1e3a5f] hover:bg-[#284c7b] text-white font-bold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm transition hover:shadow-md hover:scale-[1.01]">
                                                    <i class="fas fa-plug text-amber-400 text-xs"></i>
                                                    <span>Hubungkan Pos Ini</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- State Kosong yang Ramah & Menarik -->
                        <div class="p-8 sm:p-12 text-center rounded-2xl bg-gradient-to-b from-slate-50 to-white border-2 border-dashed border-slate-200">
                            <div class="w-16 h-16 rounded-3xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-3xl mx-auto mb-4 shadow-sm">
                                <i class="fas fa-shield-cat"></i>
                            </div>
                            <h4 class="text-base font-bold text-slate-800">Belum Ada Penugasan Pos Kamera</h4>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1.5 max-w-md mx-auto leading-relaxed">
                                Akun Anda (<strong>{{ auth()->user()->name }}</strong>) belum diberikan otorisasi pos kamera scanner oleh Administrator sistem.
                            </p>

                            <!-- Panduan Otorisasi -->
                            <div class="mt-6 max-w-lg mx-auto bg-amber-50/70 border border-amber-200 rounded-2xl p-4 text-left">
                                <div class="flex items-center gap-2 text-xs font-bold text-amber-900 mb-2">
                                    <i class="fas fa-circle-info text-amber-600"></i>
                                    <span>Langkah Aktivasi Pos Scanner:</span>
                                </div>
                                <ol class="text-xs text-amber-950 space-y-1.5 list-decimal list-inside leading-relaxed">
                                    <li>Hubungi Administrator atau Supervisor Keamanan Bandara.</li>
                                    <li>Administrator akan membuka menu <strong>Manajemen Pengguna</strong>.</li>
                                    <li>Pilih nama akun Anda lalu centang pos kamera scanner yang ditugaskan.</li>
                                    <li>Setelah disimpan, pos kamera akan seketika tampil di halaman ini.</li>
                                </ol>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Form Hubungkan Manual & Panduan Petugas (4 Kolom) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Form Hubungkan Kamera (Manual Kode Akses) -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-base shrink-0">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-800">Hubungkan Kamera</h3>
                            <p class="text-xs text-slate-500">Hubungkan manual via kode akses pos</p>
                        </div>
                    </div>

                    <form action="{{ route('operator.kamera.connect') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="kode_akses" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                                Kode Akses Kamera <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <i class="fas fa-shield-halved"></i>
                                </span>
                                <input type="text"
                                       name="kode_akses"
                                       id="kode_akses"
                                       value="{{ old('kode_akses') }}"
                                       required
                                       placeholder="Contoh: CAM-AREA-A"
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/20 font-mono text-sm tracking-wider uppercase text-slate-900 transition-all @error('kode_akses') border-rose-500 @enderror"
                                       autocomplete="off">
                            </div>
                            @error('kode_akses')
                                <p class="text-rose-600 text-xs mt-1.5 flex items-center gap-1 font-medium">
                                    <i class="fas fa-exclamation-circle text-[11px]"></i> {{ $message }}
                                </p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Masukkan kode identifikasi unik perangkat kamera pos</p>
                        </div>

                        <button type="submit"
                                class="w-full bg-[#1e3a5f] hover:bg-[#284c7b] text-white font-extrabold py-3 px-4 rounded-xl shadow-md transition-all hover:shadow-lg hover:scale-[1.01] flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-plug text-amber-400"></i>
                            <span>Hubungkan & Buka Scanner</span>
                        </button>
                    </form>
                </div>

                <!-- Card Panduan Operasional Keamanan -->
                <div class="bg-gradient-to-br from-slate-900 to-[#1e3a5f] text-white rounded-3xl p-6 shadow-md border border-slate-700/50 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-white/10">
                        <i class="fas fa-clipboard-check text-amber-400 text-base"></i>
                        <h4 class="font-extrabold text-sm tracking-wide text-white">SOP Verifikasi Petugas</h4>
                    </div>

                    <div class="space-y-3 text-xs leading-relaxed text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">1</span>
                            <p><strong class="text-white">Pilih Pos Gerbang</strong>: Pastikan Anda terhubung ke titik pos kamera sesuai jadwal tugas Anda.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">2</span>
                            <p><strong class="text-white">Arah Scan Sesuai</strong>: Atur mode <span class="text-amber-300 font-semibold">MASUK</span> atau <span class="text-amber-300 font-semibold">KELUAR</span> pada terminal scanner.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">3</span>
                            <p><strong class="text-white">Pencocokan Visual</strong>: Cocokkan foto wajah dan data pemegang kartu di layar dengan pembawa kartu PAS fisik.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">4</span>
                            <p><strong class="text-white">Waspada Status Merah</strong>: Tahan pembawa kartu jika QR menghasilkan status <span class="text-rose-400 font-semibold">Ditolak / Kadaluwarsa</span>.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Script Salin Kode Akses -->
    <script>
        function salinKodeAkses(kode) {
            const input = document.getElementById('kode_akses');
            if (input) {
                input.value = kode;
                input.focus();
                // Highlight input briefly
                input.classList.add('ring-2', 'ring-amber-400');
                setTimeout(() => input.classList.remove('ring-2', 'ring-amber-400'), 1500);
            }
            navigator.clipboard.writeText(kode).then(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Kode Akses Disalin!',
                        text: `Kode pos [${kode}] telah disalin dan dimasukkan ke formulir koneksi.`,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        }
    </script>
</x-app-layout>
