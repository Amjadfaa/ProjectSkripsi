<x-app-layout>
    <x-slot name="header">
        Akses & Koneksi Kamera Scanner
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-600 text-lg flex-shrink-0"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-600 text-lg flex-shrink-0"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Card Kamera Sedang Terhubung (Jika Ada) -->
        @if($connectedDevice)
            <div class="bg-gradient-to-r from-emerald-500 via-teal-600 to-emerald-700 rounded-2xl p-6 text-white shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm border border-white/30 flex items-center justify-center text-2xl flex-shrink-0">
                            <i class="fas fa-video"></i>
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-white text-xs font-semibold uppercase tracking-wider mb-1">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                Kamera Aktif Terhubung
                            </div>
                            <h2 class="text-xl font-bold">{{ $connectedDevice->nama_kamera }}</h2>
                            <p class="text-emerald-100 text-xs mt-0.5">
                                Kode Area: <strong>{{ $connectedDevice->kode_area }}</strong>
                                @if($connectedDevice->areaAkses)
                                    — {{ $connectedDevice->areaAkses->nama_area }}
                                @endif
                                &bull; Tipe Scan: <strong>{{ strtoupper($connectedDevice->tipe_scan) }}</strong>
                                &bull; Kode Akses: <span class="font-mono bg-white/20 px-1.5 py-0.5 rounded">{{ $connectedDevice->kode_akses }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('operator.kamera.scanner') }}"
                           class="bg-white text-emerald-800 hover:bg-emerald-50 font-bold px-5 py-2.5 rounded-xl text-sm shadow transition-all hover:scale-[1.02] flex items-center gap-2">
                            <i class="fas fa-qrcode text-emerald-600"></i>
                            <span>Buka Live Scanner</span>
                        </a>
                        <form action="{{ route('operator.kamera.disconnect') }}" method="POST" onsubmit="return confirm('Yakin ingin memutuskan koneksi kamera ini?');">
                            @csrf
                            <button type="submit"
                                    class="bg-emerald-800/60 hover:bg-red-600 text-white font-medium px-4 py-2.5 rounded-xl text-sm transition-colors border border-white/20 flex items-center gap-1.5"
                                    title="Putuskan Kamera">
                                <i class="fas fa-unlink"></i> Putuskan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Form Input Kode Akses (Kolom Kiri) -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-7">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-lg">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Hubungkan Kamera</h3>
                            <p class="text-xs text-gray-500">Masukkan kode akses perangkat kamera</p>
                        </div>
                    </div>

                    <form action="{{ route('operator.kamera.connect') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="kode_akses" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">
                                Kode Akses Kamera <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <input type="text"
                                       name="kode_akses"
                                       id="kode_akses"
                                       value="{{ old('kode_akses') }}"
                                       required
                                       placeholder="Contoh: CAM-GATE-01"
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/20 font-mono text-sm tracking-wider uppercase text-gray-900 transition-all @error('kode_akses') border-red-500 @enderror"
                                       autocomplete="off"
                                       autofocus>
                            </div>
                            @error('kode_akses')
                                <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[11px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-3.5 text-xs text-blue-800 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-blue-900">
                                <i class="fas fa-info-circle text-blue-600"></i> Panduan Otorisasi Akses:
                            </div>
                            <p class="text-blue-700 leading-relaxed">
                                Anda hanya dapat menghubungkan perangkat kamera yang telah <strong>ditugaskan oleh Administrator</strong> kepada akun Anda. Pilih dari daftar tugas di samping atau ketik kode akses resmi Anda.
                            </p>
                        </div>

                        <button type="submit"
                                class="w-full bg-[#1e3a5f] hover:bg-[#284c7b] text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all hover:shadow-lg flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-plug text-amber-400"></i>
                            <span>Hubungkan & Buka Scanner</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Perangkat Kamera Ditugaskan (Kolom Kanan) -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-video text-blue-600"></i>
                            <h3 class="font-bold text-gray-800 text-sm">Kamera & Kode Akses Ditugaskan untuk Anda</h3>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full font-bold">
                            {{ $availableDevices->count() }} Diizinkan
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                        @forelse($availableDevices as $dev)
                            @php
                                $isCurrent = $connectedDevice && $connectedDevice->id === $dev->id;
                            @endphp
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/80 transition-colors {{ $isCurrent ? 'bg-emerald-50/30' : '' }}">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $dev->nama_kamera }}</h4>
                                        @if($isCurrent)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                <i class="fas fa-check text-[9px]"></i> Aktif Saat Ini
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-[11px] font-semibold border border-blue-100">
                                            <i class="fas fa-map-marker-alt text-blue-500"></i>
                                            Area {{ $dev->kode_area }}
                                            @if($dev->areaAkses)
                                                ({{ $dev->areaAkses->keterangan }})
                                            @endif
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[11px] font-medium uppercase">
                                            {{ str_replace('_', ' ', $dev->tipe_scan) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-gray-600 pt-1">
                                        <span>Kode Akses:</span>
                                        <code class="text-purple-800 bg-purple-50 px-2 py-0.5 rounded font-mono font-bold border border-purple-200">{{ $dev->kode_akses }}</code>
                                        <button type="button" onclick="salinKodeAkses('{{ $dev->kode_akses }}')"
                                                class="text-gray-400 hover:text-purple-600 transition-colors text-xs"
                                                title="Salin Kode Akses & Masukkan ke Form">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex-shrink-0 flex items-center gap-2">
                                    @if($isCurrent)
                                        <a href="{{ route('operator.kamera.scanner') }}"
                                           class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                                            <i class="fas fa-qrcode"></i> Buka Scanner
                                        </a>
                                    @else
                                        <form action="{{ route('operator.kamera.connect') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="kode_akses" value="{{ $dev->kode_akses }}">
                                            <button type="submit"
                                                    class="bg-[#1e3a5f] hover:bg-[#284c7b] text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                                                <i class="fas fa-plug text-amber-400"></i> Hubungkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400">
                                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto mb-3 border border-amber-200">
                                    <i class="fas fa-key"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-700">Belum Ada Penugasan Kamera</h4>
                                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto leading-relaxed">
                                    Akun Anda belum memiliki izin akses titik kamera dari Administrator. Silakan hubungi Administrator sistem untuk mendapatkan penugasan pos kamera scanner.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function salinKodeAkses(kode) {
            const input = document.getElementById('kode_akses');
            if (input) {
                input.value = kode;
                input.focus();
            }
            navigator.clipboard.writeText(kode).then(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Kode Disalin!',
                        text: `Kode akses [${kode}] telah disalin dan dimasukkan ke formulir koneksi.`,
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
