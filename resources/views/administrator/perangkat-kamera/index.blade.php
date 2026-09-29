<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-video"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Pengaturan Perangkat Kamera Scan
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">
                    Kelola kode akses login unik dan penugasan area operasional scanner operator bandara
                </p>
            </div>
        </div>
    </x-slot>

    {{-- ======================================================================== --}}
    {{-- TOP KPI STAT CARDS (4 BALANCED CARDS)                                     --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- 1. Total Perangkat Kamera --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Kamera</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalDevices" class="text-2xl font-extrabold text-slate-800">{{ $totalDevices }}</span>
                    <span class="text-xs font-semibold text-slate-400">Unit Kamera</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2.5 flex items-center gap-1.5 font-medium">
                    <i class="fas fa-video text-blue-500 text-[11px]"></i>
                    <span>Titik scan terpasang</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-video"></i>
            </div>
        </div>

        {{-- 2. Kamera Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kamera Aktif</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalAktif" class="text-2xl font-extrabold text-emerald-600">{{ $totalAktif }}</span>
                    <span class="text-xs font-semibold text-slate-400">/ {{ $totalDevices }} Unit</span>
                </div>
                <p class="text-[11px] text-emerald-700 mt-2.5 font-semibold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Siap untuk scan QR</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>

        {{-- 3. Titik Area Terpasang --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Cakupan Area Gerbang</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalArea" class="text-2xl font-extrabold text-purple-600">{{ $totalAreaUsed }}</span>
                    <span class="text-xs font-semibold text-slate-400">/ {{ $totalAreaAll }} Zona Area</span>
                </div>
                @php $persenArea = $totalAreaAll > 0 ? round(($totalAreaUsed / $totalAreaAll) * 100) : 0; @endphp
                <p class="text-[11px] text-purple-700 mt-2.5 font-semibold flex items-center gap-1.5">
                    <i class="fas fa-map-location-dot text-purple-500 text-[11px]"></i>
                    <span id="kpiPersenArea">{{ $persenArea }}% Area Memiliki Titik Kamera</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-map-location-dot"></i>
            </div>
        </div>

        {{-- 4. Kredensial Akses Mandiri --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Keamanan Kredensial</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-xl font-extrabold text-indigo-600">Kode Unik</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2.5 flex items-center gap-1.5 font-medium">
                    <i class="fas fa-key text-indigo-500 text-[11px]"></i>
                    <span>Login terisolasi per gate</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-shield-halved"></i>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- REUSABLE TABLE CARD CONTAINER DENGAN SPA PAGINASI & FILTER               --}}
    {{-- ======================================================================== --}}
    <x-table-card title="Pengaturan Perangkat Kamera Scan"
                  subtitle="Konfigurasi titik kamera gerbang, penugasan kode area, dan kredensial login scanner operator"
                  icon="fas fa-video"
                  iconColor="bg-blue-50 text-blue-600">

        {{-- Action Buttons Slot --}}
        <x-slot name="actions">
            {{-- Tombol Kelola Area Akses --}}
            <button type="button" 
                    onclick="openModalAreaAkses()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-layer-group text-blue-600"></i>
                <span>Kelola Area Akses</span>
            </button>

            {{-- Tombol Tambah Perangkat Kamera --}}
            <button type="button" 
                    onclick="openModalTambahKamera()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Perangkat Kamera</span>
            </button>
        </x-slot>

        {{-- Filter Slot (SPA Intercepted) --}}
        <x-slot name="filters">
            <form id="filterKameraForm" method="GET" action="{{ route('administrator.perangkat-kamera.index') }}">
                <div class="flex flex-wrap items-end gap-3">
                    {{-- Pencarian Input --}}
                    <div class="flex-1 min-w-[220px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                                <i class="fas fa-magnifying-glass"></i>
                            </span>
                            <input type="text" id="kameraSearchInput" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama kamera, kode akses, atau area..."
                                   class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs">
                            @if(request('search'))
                                <button type="button" onclick="clearKameraSearch()" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-circle-xmark text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Filter Area Akses --}}
                    <div class="min-w-[170px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Area</label>
                        <select id="filterAreaSelect" name="area" 
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs cursor-pointer">
                            <option value="">Semua Area</option>
                            @foreach($areaAksesList as $area)
                                <option value="{{ $area->kode }}" {{ request('area') == $area->kode ? 'selected' : '' }}>
                                    [{{ $area->kode }}] {{ \Illuminate\Support\Str::limit($area->keterangan, 22) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div class="min-w-[130px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                        <select id="filterStatusSelect" name="status" 
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    {{-- Filter Tipe Scan --}}
                    <div class="min-w-[140px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tipe Scan</label>
                        <select id="filterTipeScanSelect" name="tipe_scan" 
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs cursor-pointer">
                            <option value="">Semua Tipe</option>
                            <option value="masuk_keluar" {{ request('tipe_scan') == 'masuk_keluar' ? 'selected' : '' }}>Masuk & Keluar</option>
                            <option value="masuk" {{ request('tipe_scan') == 'masuk' ? 'selected' : '' }}>Masuk Saja</option>
                            <option value="keluar" {{ request('tipe_scan') == 'keluar' ? 'selected' : '' }}>Keluar Saja</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-filter text-[11px]"></i>
                            <span>Filter</span>
                        </button>
                        <button type="button" onclick="resetKameraFilter()"
                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                                title="Reset Filter">
                            <i class="fas fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </form>
        </x-slot>

        <!-- Table Viewport with Persistent Loading Overlay (SPA Replaced via AJAX) -->
        <div class="relative">
            <!-- Shimmer Loading Overlay -->
            <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 hidden items-center justify-center transition-all duration-200">
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/90 text-white shadow-2xl text-xs font-bold border border-slate-700 backdrop-blur-md">
                    <i class="fas fa-circle-notch fa-spin text-blue-400 text-base"></i>
                    <span>Memperbarui data perangkat kamera...</span>
                </div>
            </div>

            <!-- Table Container (SPA HTML injected here) -->
            <div id="tableContainer" class="transition-opacity duration-200">
                @include('administrator.perangkat-kamera.partials.table')
            </div>
        </div>

    </x-table-card>

    {{-- ======================================================================== --}}
    {{-- MODAL TAMBAH PERANGKAT KAMERA (REUSABLE COMPONENT)                       --}}
    {{-- ======================================================================== --}}
    <x-modal id="modalTambahKamera" maxWidth="max-w-lg" title="Tambah Perangkat Kamera Baru" subtitle="Daftarkan titik kamera baru untuk gerbang izin masuk bandara" icon="fas fa-video" iconColor="bg-blue-50 text-blue-600 border-blue-100">
        <form id="formTambahKamera" method="POST" action="{{ route('administrator.perangkat-kamera.store') }}" class="space-y-4">
            @csrf

            {{-- Nama Perangkat Kamera --}}
            <div>
                <label for="tambah_nama_kamera" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Perangkat Kamera <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="tambah_nama_kamera" name="nama_kamera" value="{{ old('nama_kamera') }}"
                       placeholder="Contoh: Kamera Gate 1 - Kedatangan"
                       required
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
            </div>

            {{-- Area Akses Ditugaskan & Kode Akses Login --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="select_kode_area" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Area Akses <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="openModalAreaAkses()" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition">
                            + Kelola Area
                        </button>
                    </div>
                    <select name="kode_area" id="select_kode_area" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="">-- Pilih Area --</option>
                        @foreach($areaAksesList as $area)
                            <option value="{{ $area->kode }}" data-id="{{ $area->id }}" {{ old('kode_area') == $area->kode ? 'selected' : '' }}>
                                [{{ $area->kode }}] {{ \Illuminate\Support\Str::limit($area->keterangan, 24) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="tambah_kode_akses" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Kode Akses Login <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="generateRandomAccessCode('tambah_kode_akses')" class="text-[11px] font-bold text-purple-600 hover:text-purple-800 transition" title="Buat kode acak otomatis">
                            <i class="fas fa-wand-magic-sparkles text-[10px]"></i> Acak
                        </button>
                    </div>
                    <input type="text" id="tambah_kode_akses" name="kode_akses" value="{{ old('kode_akses') }}"
                           placeholder="CAM-GATE-01"
                           required
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold text-purple-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition shadow-2xs uppercase">
                </div>
            </div>

            {{-- Tipe Scan & Status Perangkat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="tambah_tipe_scan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipe Scan <span class="text-rose-500">*</span>
                    </label>
                    <select name="tipe_scan" id="tambah_tipe_scan" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="masuk_keluar" {{ old('tipe_scan', 'masuk_keluar') == 'masuk_keluar' ? 'selected' : '' }}>Masuk & Keluar</option>
                        <option value="masuk" {{ old('tipe_scan') == 'masuk' ? 'selected' : '' }}>Masuk Saja</option>
                        <option value="keluar" {{ old('tipe_scan') == 'keluar' ? 'selected' : '' }}>Keluar Saja</option>
                    </select>
                </div>

                <div>
                    <label for="tambah_is_active" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Perangkat <span class="text-rose-500">*</span>
                    </label>
                    <select name="is_active" id="tambah_is_active" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalTambahKamera')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perangkat</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- ======================================================================== --}}
    {{-- MODAL EDIT PERANGKAT KAMERA (REUSABLE COMPONENT)                         --}}
    {{-- ======================================================================== --}}
    <x-modal id="modalEditKamera" maxWidth="max-w-lg" title="Edit Perangkat Kamera" subtitle="Perbarui konfigurasi titik kamera dan kredensial akses" icon="fas fa-pen-to-square" iconColor="bg-amber-50 text-amber-600 border-amber-100">
        <form id="formEditKamera" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama Perangkat Kamera --}}
            <div>
                <label for="edit_nama_kamera" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Perangkat Kamera <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit_nama_kamera" name="nama_kamera" required
                       class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
            </div>

            {{-- Area Akses Ditugaskan & Kode Akses Login --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="edit_kode_area" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Area Akses <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit_kode_area" name="kode_area" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="">-- Pilih Area --</option>
                        @foreach($areaAksesList as $area)
                            <option value="{{ $area->kode }}" data-id="{{ $area->id }}">
                                [{{ $area->kode }}] {{ \Illuminate\Support\Str::limit($area->keterangan, 24) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="edit_kode_akses" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Kode Akses Login <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="generateRandomAccessCode('edit_kode_akses')" class="text-[11px] font-bold text-purple-600 hover:text-purple-800 transition" title="Buat kode acak baru">
                            <i class="fas fa-wand-magic-sparkles text-[10px]"></i> Acak
                        </button>
                    </div>
                    <input type="text" id="edit_kode_akses" name="kode_akses" required
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold text-purple-800 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition shadow-2xs uppercase">
                </div>
            </div>

            {{-- Tipe Scan & Status Perangkat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="edit_tipe_scan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipe Scan <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit_tipe_scan" name="tipe_scan" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="masuk_keluar">Masuk & Keluar</option>
                        <option value="masuk">Masuk Saja</option>
                        <option value="keluar">Keluar Saja</option>
                    </select>
                </div>

                <div>
                    <label for="edit_is_active" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Perangkat <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit_is_active" name="is_active" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs cursor-pointer">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditKamera')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-md shadow-amber-500/20 transition cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- ======================================================================== --}}
    {{-- MODAL KELOLA & TAMBAH AREA AKSES (REUSABLE COMPONENT)                     --}}
    {{-- ======================================================================== --}}
    <x-modal id="modalAreaAkses" maxWidth="max-w-md" title="Kelola & Tambah Area Akses" subtitle="Tambah atau hapus master zona area izin masuk bandara" icon="fas fa-layer-group" iconColor="bg-purple-50 text-purple-600 border-purple-100" zIndex="z-[70]">
        <div class="space-y-4">
            {{-- Form Tambah Area Cepat via AJAX --}}
            <form id="formAreaAkses" onsubmit="submitAreaAkses(event)" class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-200/80 space-y-3">
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kode (Contoh: W)</label>
                        <input type="text" id="modal_kode_akses" 
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-black uppercase text-slate-800" 
                               placeholder="W" required maxlength="10">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Keterangan Area</label>
                        <input type="text" id="modal_keterangan_akses" 
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800" 
                               placeholder="Contoh: Daerah Ruang VIP" required>
                    </div>
                </div>

                <div id="areaAksesError" class="text-rose-600 text-[11px] font-medium hidden"></div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-2xs transition flex items-center gap-1.5">
                        <i class="fas fa-plus text-[10px]"></i>
                        <span>Tambah Area</span>
                    </button>
                </div>
            </form>

            {{-- List Area Akses Saat Ini --}}
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Daftar Area Akses Saat Ini</p>
                <div class="overflow-y-auto max-h-52 border border-slate-200/80 rounded-xl divide-y divide-slate-100 bg-white" id="modal_area_list">
                    @foreach($areaAksesList as $area)
                        <div class="flex items-center justify-between px-3 py-2 hover:bg-slate-50 text-xs transition" id="item-area-{{ $area->id }}">
                            <div class="flex items-center gap-2 truncate pr-2">
                                <span class="font-mono font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] shrink-0">
                                    {{ $area->kode }}
                                </span>
                                <span class="font-medium text-slate-800 truncate" title="{{ $area->keterangan }}">
                                    {{ $area->keterangan }}
                                </span>
                            </div>
                            <button type="button" onclick="deleteAreaById({{ $area->id }}, '{{ $area->kode }}')" 
                                    class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center text-xs transition shrink-0" 
                                    title="Hapus Area Ini">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalAreaAkses')" 
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Selesai
                </button>
            </div>
        </div>
    </x-modal>

    {{-- ======================================================================== --}}
    {{-- REUSABLE MODAL DELETE COMPONENT                                          --}}
    {{-- ======================================================================== --}}
    <x-modal-delete id="deleteKameraModal" />

    {{-- ======================================================================== --}}
    {{-- SPA PAGINATION, LIVE FILTER, AND MODAL SCRIPTS                           --}}
    {{-- ======================================================================== --}}
    <script>
        // Modal Helpers (Vanilla JS - 60fps instant toggle)
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Escape Key Modal Listener
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.app-modal:not(.hidden)');
                openModals.forEach(m => closeModal(m.id));
            }
        });

        // Modal Handlers
        function openModalTambahKamera() {
            if (!document.getElementById('tambah_kode_akses').value) {
                generateRandomAccessCode('tambah_kode_akses');
            }
            openModal('modalTambahKamera');
        }

        function openModalEditKameraFromBtn(btn) {
            try {
                const raw = btn.getAttribute('data-device');
                const device = typeof raw === 'string' ? JSON.parse(raw) : raw;
                openModalEditKamera(device);
            } catch (e) {
                console.error("Gagal membuka modal edit kamera:", e);
            }
        }

        function openModalEditKamera(device) {
            const form = document.getElementById('formEditKamera');
            if (form) {
                form.action = "{{ url('/administrator/perangkat-kamera') }}/" + device.id;
            }

            const inputNama = document.getElementById('edit_nama_kamera');
            const selectArea = document.getElementById('edit_kode_area');
            const inputAkses = document.getElementById('edit_kode_akses');
            const selectScan = document.getElementById('edit_tipe_scan');
            const selectActive = document.getElementById('edit_is_active');

            if (inputNama) inputNama.value = device.nama_kamera || '';
            if (selectArea) selectArea.value = device.kode_area || '';
            if (inputAkses) inputAkses.value = device.kode_akses || '';
            if (selectScan) selectScan.value = device.tipe_scan || 'masuk_keluar';
            if (selectActive) selectActive.value = device.is_active ? '1' : '0';

            openModal('modalEditKamera');
        }

        function confirmDeleteKamera(id, nama) {
            if (typeof window.openDeleteModal === 'function') {
                window.openDeleteModal({
                    id: 'deleteKameraModal',
                    title: 'Hapus Perangkat Kamera?',
                    message: `Apakah Anda yakin ingin menghapus kamera [${nama}]? Perangkat ini tidak akan dapat login untuk melakukan scan QR lagi.`,
                    targetName: nama,
                    action: "{{ url('/administrator/perangkat-kamera') }}/" + id,
                    btnText: 'Hapus Kamera'
                });
            } else {
                if (confirm(`Hapus perangkat kamera "${nama}"?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('/administrator/perangkat-kamera') }}/" + id;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }

        function openModalAreaAkses() {
            document.getElementById('modal_kode_akses').value = '';
            document.getElementById('modal_keterangan_akses').value = '';
            document.getElementById('areaAksesError').classList.add('hidden');
            openModal('modalAreaAkses');
        }

        // Random Access Code Generator
        function generateRandomAccessCode(targetInputId) {
            const prefixes = ['CAM', 'GATE', 'SCAN'];
            const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            const generated = `${prefix}-${randomNum}`;
            const input = document.getElementById(targetInputId);
            if (input) {
                input.value = generated;
            }
        }

        // Copy Code to Clipboard Helper
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-emerald-600 text-[11px]"></i>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 1800);
            }).catch(() => {
                const tempInput = document.createElement('input');
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
            });
        }

        // ====================================================================
        // SPA ENGINE: SEAMLESS PAGINATION & LIVE FILTERING WITH LOADING OVERLAY
        // ====================================================================
        let filterDebounceTimer;

        function showTableLoading() {
            const overlay   = document.getElementById('tableLoadingOverlay');
            const container = document.getElementById('tableContainer');
            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }
            if (container) {
                container.classList.add('opacity-40', 'pointer-events-none');
            }
        }

        function hideTableLoading() {
            const overlay   = document.getElementById('tableLoadingOverlay');
            const container = document.getElementById('tableContainer');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }
            if (container) {
                container.classList.remove('opacity-40', 'pointer-events-none');
            }
        }

        async function fetchSpaKameraTable(url, pushState = true) {
            const container = document.getElementById('tableContainer');
            if (!container) return;

            showTableLoading();

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SPA': 'true'
                    }
                });

                if (!response.ok) throw new Error('Network error');

                const html = await response.text();
                container.innerHTML = html;

                // Update URL di browser address bar tanpa reload
                if (pushState) {
                    window.history.pushState(null, '', url);
                }

                // Perbarui data KPI di kartu statistik atas
                const kpiDataEl = document.getElementById('spaKpiData');
                if (kpiDataEl) {
                    const total = kpiDataEl.getAttribute('data-total');
                    const aktif = kpiDataEl.getAttribute('data-aktif');
                    const area  = kpiDataEl.getAttribute('data-area');
                    const all   = kpiDataEl.getAttribute('data-all');

                    if (total !== null && document.getElementById('kpiTotalDevices')) {
                        document.getElementById('kpiTotalDevices').textContent = total;
                    }
                    if (aktif !== null && document.getElementById('kpiTotalAktif')) {
                        document.getElementById('kpiTotalAktif').textContent = aktif;
                    }
                    if (area !== null && document.getElementById('kpiTotalArea')) {
                        document.getElementById('kpiTotalArea').textContent = area;
                        const pct = all > 0 ? Math.round((area / all) * 100) : 0;
                        const pctEl = document.getElementById('kpiPersenArea');
                        if (pctEl) pctEl.textContent = `${pct}% Area Memiliki Titik Kamera`;
                    }
                }
            } catch (err) {
                console.error('SPA Navigation Error:', err);
                // Fallback graceful ke browser navigation jika fetch gagal
                window.location.href = url;
            } finally {
                hideTableLoading();
            }
        }

        // Intercept Klik Paginasi Secara Global untuk SPA
        document.addEventListener('click', function(e) {
            const link = e.target.closest('#tableContainer a.pagination-link, #tableContainer a[data-spa="true"], #tableContainer a[href*="page="]');
            if (link && link.href) {
                e.preventDefault();
                fetchSpaKameraTable(link.href, true);
            }
        });

        // Intercept Submit Filter Form
        const filterForm = document.getElementById('filterKameraForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const targetUrl = `${filterForm.action}?${params.toString()}`;
                fetchSpaKameraTable(targetUrl, true);
            });

            // Live Change Trigger pada Dropdowns
            ['filterAreaSelect', 'filterStatusSelect', 'filterTipeScanSelect'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', () => {
                        filterForm.dispatchEvent(new Event('submit'));
                    });
                }
            });

            // Live Debounce Search Input
            const searchInput = document.getElementById('kameraSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(filterDebounceTimer);
                    filterDebounceTimer = setTimeout(() => {
                        filterForm.dispatchEvent(new Event('submit'));
                    }, 350);
                });
            }
        }

        function clearKameraSearch() {
            const input = document.getElementById('kameraSearchInput');
            if (input) {
                input.value = '';
                filterForm.dispatchEvent(new Event('submit'));
            }
        }

        function resetKameraFilter() {
            if (filterForm) {
                filterForm.reset();
                const targetUrl = filterForm.action;
                fetchSpaKameraTable(targetUrl, true);
            }
        }

        // Handle Browser Back & Forward Buttons
        window.addEventListener('popstate', function() {
            fetchSpaKameraTable(window.location.href, false);
        });

        // ====================================================================
        // AJAX AREA AKSES CRUD HANDLERS
        // ====================================================================
        async function submitAreaAkses(e) {
            e.preventDefault();
            const kode = document.getElementById('modal_kode_akses').value.trim().toUpperCase();
            const keterangan = document.getElementById('modal_keterangan_akses').value.trim();
            const errDiv = document.getElementById('areaAksesError');

            try {
                const response = await fetch("{{ route('administrator.area-akses.store-ajax') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ kode, keterangan })
                });

                const result = await response.json();
                if (result.success) {
                    const labelDisplay = `[${result.data.kode}] ${result.data.keterangan}`;

                    // Update Dropdowns
                    ['select_kode_area', 'edit_kode_area', 'filterAreaSelect'].forEach(selectId => {
                        const select = document.getElementById(selectId);
                        if (select) {
                            const opt = document.createElement('option');
                            opt.value = result.data.kode;
                            opt.setAttribute('data-id', result.data.id);
                            opt.textContent = labelDisplay;
                            if (selectId === 'select_kode_area') opt.selected = true;
                            select.appendChild(opt);
                        }
                    });

                    // Update Area List in Modal
                    const list = document.getElementById('modal_area_list');
                    if (list) {
                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'flex items-center justify-between px-3 py-2 hover:bg-slate-50 text-xs transition';
                        itemDiv.id = 'item-area-' + result.data.id;
                        itemDiv.innerHTML = `
                            <div class="flex items-center gap-2 truncate pr-2">
                                <span class="font-mono font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] shrink-0">${result.data.kode}</span>
                                <span class="font-medium text-slate-800 truncate">${result.data.keterangan}</span>
                            </div>
                            <button type="button" onclick="deleteAreaById(${result.data.id}, '${result.data.kode}')" class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center text-xs transition shrink-0" title="Hapus Area Ini">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        `;
                        list.appendChild(itemDiv);
                    }

                    document.getElementById('modal_kode_akses').value = '';
                    document.getElementById('modal_keterangan_akses').value = '';
                    errDiv.classList.add('hidden');
                    closeModal('modalAreaAkses');
                } else {
                    errDiv.textContent = result.message || 'Gagal menambahkan Area Akses';
                    errDiv.classList.remove('hidden');
                }
            } catch (err) {
                errDiv.textContent = 'Terjadi kesalahan sistem.';
                errDiv.classList.remove('hidden');
            }
        }

        async function deleteAreaById(id, kode) {
            if (!confirm(`Apakah Anda yakin ingin menghapus Area Akses [${kode}]?`)) return;

            try {
                const response = await fetch("{{ route('administrator.area-akses.delete-ajax') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ id })
                });

                const result = await response.json();
                if (result.success) {
                    const elModal = document.getElementById('item-area-' + id);
                    if (elModal) elModal.remove();

                    // Remove from Selects
                    ['select_kode_area', 'edit_kode_area', 'filterAreaSelect'].forEach(selectId => {
                        const select = document.getElementById(selectId);
                        if (select) {
                            for (let i = 0; i < select.options.length; i++) {
                                if (select.options[i].getAttribute('data-id') == id || select.options[i].value == kode) {
                                    select.remove(i);
                                    break;
                                }
                            }
                        }
                    });
                } else {
                    alert(result.message || 'Gagal menghapus area akses');
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi.');
            }
        }
    </script>
</x-app-layout>
