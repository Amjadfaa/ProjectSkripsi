<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-users-cog"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Manajemen Akun Operator
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">
                    Kelola akun akses operasional scanner petugas lapangan dan akun administrator sistem
                </p>
            </div>
        </div>
    </x-slot>

    {{-- ======================================================================== --}}
    {{-- TOP KPI STAT CARDS (4 BALANCED CARDS)                                     --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- 1. Total Akun Terdaftar --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Akun Terdaftar</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalUsers" class="text-2xl font-extrabold text-slate-800">{{ $totalUsers }}</span>
                    <span class="text-xs font-semibold text-slate-400">Pengguna</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2.5 flex items-center gap-1.5 font-medium">
                    <i class="fas fa-users text-blue-500 text-[11px]"></i>
                    <span>Pengguna aktif dalam sistem</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-users"></i>
            </div>
        </div>

        {{-- 2. Akun Operator --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Akun Operator</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalOperator" class="text-2xl font-extrabold text-emerald-600">{{ $totalOperator }}</span>
                    <span class="text-xs font-semibold text-slate-400">Petugas</span>
                </div>
                <p class="text-[11px] text-emerald-700 mt-2.5 font-semibold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Petugas scanner lapangan</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>

        {{-- 3. Administrator --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Administrator</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalAdmin" class="text-2xl font-extrabold text-indigo-600">{{ $totalAdmin }}</span>
                    <span class="text-xs font-semibold text-slate-400">Full Akses</span>
                </div>
                <p class="text-[11px] text-indigo-700 mt-2.5 font-semibold flex items-center gap-1.5">
                    <i class="fas fa-crown text-amber-500 text-[11px]"></i>
                    <span>Pengelola penuh sistem</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-user-gear"></i>
            </div>
        </div>

        {{-- 4. Kamera Terhubung --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between hover:shadow-sm transition-all relative overflow-hidden group">
            <div class="min-w-0 pr-2">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kamera Terhubung</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span id="kpiTotalKamera" class="text-2xl font-extrabold text-purple-600">{{ $totalKameraAssigned ?? 0 }}</span>
                    <span class="text-xs font-semibold text-slate-400">/ {{ count($allCameraDevices) }} Unit</span>
                </div>
                <p class="text-[11px] text-purple-700 mt-2.5 font-semibold flex items-center gap-1.5">
                    <i class="fas fa-video text-purple-500 text-[11px]"></i>
                    <span>Otorisasi akses aktif</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-lg shrink-0 shadow-2xs">
                <i class="fas fa-video"></i>
            </div>
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- REUSABLE TABLE CARD CONTAINER DENGAN SPA PAGINASI & FILTER               --}}
    {{-- ======================================================================== --}}
    <x-table-card title="Manajemen Akun Operator & Pengguna"
                  subtitle="Kelola akun akses operasional scanner petugas lapangan dan akun administrator sistem."
                  icon="fas fa-users-cog"
                  iconColor="bg-blue-50 text-blue-600">

        {{-- Header Actions Slot --}}
        <x-slot name="actions">
            {{-- Tombol Tambah Akun Operator (Membuka Modal Instan tanpa pindah halaman) --}}
            <button type="button" 
                    onclick="openModalTambahUser()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-user-plus text-xs"></i>
                <span>+ Tambah Akun Operator</span>
            </button>
        </x-slot>

        {{-- Filter Slot (SPA Intercepted) --}}
        <x-slot name="filters">
            <form id="filterUserForm" method="GET" action="{{ route('administrator.users.index') }}">
                <div class="flex flex-wrap items-end gap-3">
                    {{-- Pencarian Input --}}
                    <div class="flex-1 min-w-[240px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                                <i class="fas fa-magnifying-glass"></i>
                            </span>
                            <input type="text" id="userSearchInput" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama akun, email, atau unit/instansi..."
                                   class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs">
                            @if(request('search'))
                                <button type="button" onclick="clearUserSearch()" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-circle-xmark text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Filter Role Akun --}}
                    <div class="min-w-[180px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Role Akun</label>
                        <select id="filterRoleSelect" name="role" 
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs cursor-pointer">
                            <option value="">Semua Role Akun</option>
                            <option value="operator" {{ request('role') === 'operator' ? 'selected' : '' }}>Operator (Scanner)</option>
                            <option value="administrator" {{ request('role') === 'administrator' ? 'selected' : '' }}>Administrator (Sistem)</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-filter text-[11px]"></i>
                            <span>Filter</span>
                        </button>
                        <button type="button" onclick="resetUserFilter()"
                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                                title="Reset Filter">
                            <i class="fas fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </form>
        </x-slot>

        {{-- Table Viewport with Persistent Loading Overlay (SPA Replaced via AJAX) --}}
        <div class="relative">
            {{-- Shimmer Loading Overlay --}}
            <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 hidden items-center justify-center transition-all duration-200">
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/90 text-white shadow-2xl text-xs font-bold border border-slate-700 backdrop-blur-md">
                    <i class="fas fa-circle-notch fa-spin text-blue-400 text-base"></i>
                    <span>Memperbarui data akun operator...</span>
                </div>
            </div>

            {{-- Table Container (SPA HTML injected here) --}}
            <div id="tableContainer" class="transition-opacity duration-200">
                @include('administrator.users.partials.table')
            </div>
        </div>

    </x-table-card>

    {{-- ======================================================================== --}}
    {{-- 1. MODAL TAMBAH AKUN PENGGUNA / OPERATOR (MODAL COMPONENT)               --}}
    {{-- ======================================================================== --}}
    <x-modal id="modalTambahUser" maxWidth="max-w-2xl" title="Tambah Akun Pengguna / Operator" subtitle="Daftarkan akun operator scanner atau administrator baru ke sistem" icon="fas fa-user-plus" iconColor="bg-blue-50 text-blue-600 border-blue-100">
        <form id="formTambahUser" method="POST" action="{{ route('administrator.users.store') }}" class="space-y-4">
            @csrf

            {{-- Role Selection Cards --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Hak Akses / Role <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Role Operator --}}
                    <label id="tambah_labelRoleOperator" 
                           class="relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all shadow-2xs border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20">
                        <input type="radio" name="role" value="operator" id="tambah_roleOperator" checked class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base mb-1.5">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span id="tambah_checkOperator" class="text-blue-600 font-bold text-xs">
                                <i class="fas fa-circle-check text-base"></i>
                            </span>
                        </div>
                        <span class="font-bold text-xs text-slate-800">Operator (Scanner)</span>
                        <span class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                            Melakukan pemindaian QR code kartu PAS di pos/gate bandara.
                        </span>
                    </label>

                    {{-- Role Administrator --}}
                    <label id="tambah_labelRoleAdmin" 
                           class="relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all border-slate-200 hover:border-slate-300">
                        <input type="radio" name="role" value="administrator" id="tambah_roleAdmin" class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-base mb-1.5">
                                <i class="fas fa-crown text-amber-500"></i>
                            </div>
                            <span id="tambah_checkAdmin" class="text-indigo-600 font-bold text-xs hidden">
                                <i class="fas fa-circle-check text-base"></i>
                            </span>
                        </div>
                        <span class="font-bold text-xs text-slate-800">Administrator Sistem</span>
                        <span class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                            Hak akses penuh untuk mengelola seluruh data sistem.
                        </span>
                    </label>
                </div>
            </div>

            {{-- Nama Lengkap & Email --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="tambah_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Petugas <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="tambah_name" name="name" required
                               placeholder="Contoh: Budi Santoso"
                               class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>

                <div>
                    <label for="tambah_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email (Login) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="tambah_email" name="email" required
                               placeholder="Contoh: operator1@bandara.test"
                               class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>
            </div>

            {{-- Unit Kerja / Instansi --}}
            <div>
                <label for="tambah_perusahaan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Instansi / Unit Kerja <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                        <i class="fas fa-building"></i>
                    </span>
                    <input type="text" id="tambah_perusahaan" name="perusahaan" list="instansiSuggestions"
                           placeholder="Contoh: Unit Aviation Security (Avsec) / PT Angkasa Pura"
                           class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                    <datalist id="instansiSuggestions">
                        @foreach($instansiList as $inst)
                            <option value="{{ $inst->nama_instansi }}"></option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            {{-- Kata Sandi & Konfirmasi Kata Sandi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <div>
                    <label for="tambah_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi (Password) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="tambah_password" name="password" required minlength="8"
                               placeholder="Minimal 8 karakter"
                               class="w-full pl-9 pr-9 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                        <button type="button" onclick="togglePassVisibility('tambah_password', this)" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="tambah_password_confirm" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-shield-halved"></i>
                        </span>
                        <input type="password" id="tambah_password_confirm" name="password_confirmation" required minlength="8"
                               placeholder="Ulangi kata sandi"
                               class="w-full pl-9 pr-9 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                        <button type="button" onclick="togglePassVisibility('tambah_password_confirm', this)" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Penugasan Kamera (Khusus Operator) --}}
            <div id="tambah_sectionPenugasanKamera" class="space-y-2.5 pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-video text-blue-600"></i>
                            <span>Penugasan Perangkat Kamera & Kode Akses</span>
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pilih titik kamera mana saja yang diizinkan untuk diakses operator ini.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleAllCheckboxes('#tambah_sectionPenugasanKamera .camera-cb', true)" class="text-[11px] font-bold text-blue-600 hover:underline">
                            Pilih Semua
                        </button>
                        <span class="text-slate-300">&bull;</span>
                        <button type="button" onclick="toggleAllCheckboxes('#tambah_sectionPenugasanKamera .camera-cb', false)" class="text-[11px] font-semibold text-slate-500 hover:underline">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="max-h-48 overflow-y-auto space-y-2 border border-slate-200 rounded-xl p-2.5 bg-slate-50/50 custom-scrollbar">
                    @forelse($allCameraDevices as $cam)
                        <label class="flex items-center justify-between p-2.5 bg-white border border-slate-200/80 rounded-xl hover:bg-blue-50/50 cursor-pointer transition shadow-2xs">
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <input type="checkbox" name="camera_devices[]" value="{{ $cam->id }}"
                                       class="camera-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 shrink-0">
                                <div class="min-w-0 truncate">
                                    <div class="font-bold text-xs text-slate-800 truncate flex items-center gap-1.5">
                                        <span class="truncate">{{ $cam->nama_kamera }}</span>
                                        <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-1.5 py-0.2 rounded shrink-0">
                                            Area {{ $cam->kode_area }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                        Tipe: {{ strtoupper(str_replace('_', ' ', $cam->tipe_scan)) }}
                                        @if($cam->areaAkses)
                                            &bull; {{ $cam->areaAkses->keterangan }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <code class="font-mono text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 shrink-0">
                                {{ $cam->kode_akses }}
                            </code>
                        </label>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fas fa-video-slash text-xl mb-1 block"></i>
                            Belum ada perangkat kamera aktif yang didaftarkan di sistem.
                        </div>
                    @endforelse
                </div>

                {{-- Opsi Kirim Email --}}
                <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="kirim_email" value="1" checked
                               class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        <span class="text-xs font-semibold text-emerald-900 flex items-center gap-1.5">
                            <i class="fas fa-paper-plane text-emerald-600"></i>
                            <span>Kirim rincian kode akses & panduan ke email operator setelah akun dibuat</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalTambahUser')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Simpan Akun Baru</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- ======================================================================== --}}
    {{-- 2. MODAL EDIT AKUN PENGGUNA (MODAL COMPONENT)                            --}}
    {{-- ======================================================================== --}}
    <x-modal id="modalEditUser" maxWidth="max-w-2xl" title="Edit Akun Pengguna" subtitle="Perbarui profil, hak akses peran, dan kata sandi pengguna" icon="fas fa-pen-to-square" iconColor="bg-amber-50 text-amber-600 border-amber-100">
        <form id="formEditUser" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Role Selection Cards --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Pilih Hak Akses / Role <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Role Operator --}}
                    <label id="edit_labelRoleOperator" 
                           class="relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all shadow-2xs border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20">
                        <input type="radio" name="role" value="operator" id="edit_roleOperator" class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base mb-1.5">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span id="edit_checkOperator" class="text-blue-600 font-bold text-xs">
                                <i class="fas fa-circle-check text-base"></i>
                            </span>
                        </div>
                        <span class="font-bold text-xs text-slate-800">Operator (Scanner)</span>
                        <span class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                            Melakukan pemindaian QR code kartu PAS di pos/gate bandara.
                        </span>
                    </label>

                    {{-- Role Administrator --}}
                    <label id="edit_labelRoleAdmin" 
                           class="relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all border-slate-200 hover:border-slate-300">
                        <input type="radio" name="role" value="administrator" id="edit_roleAdmin" class="sr-only">
                        <div class="flex items-start justify-between">
                            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-base mb-1.5">
                                <i class="fas fa-crown text-amber-500"></i>
                            </div>
                            <span id="edit_checkAdmin" class="text-indigo-600 font-bold text-xs hidden">
                                <i class="fas fa-circle-check text-base"></i>
                            </span>
                        </div>
                        <span class="font-bold text-xs text-slate-800">Administrator Sistem</span>
                        <span class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                            Hak akses penuh untuk mengelola seluruh data sistem.
                        </span>
                    </label>
                </div>
            </div>

            {{-- Nama Lengkap & Email --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label for="edit_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Petugas <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="edit_name" name="name" required
                               class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>

                <div>
                    <label for="edit_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email (Login) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="edit_email" name="email" required
                               class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                    </div>
                </div>
            </div>

            {{-- Unit Kerja / Instansi --}}
            <div>
                <label for="edit_perusahaan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Instansi / Unit Kerja <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                        <i class="fas fa-building"></i>
                    </span>
                    <input type="text" id="edit_perusahaan" name="perusahaan" list="instansiSuggestions"
                           placeholder="Contoh: Unit Aviation Security (Avsec) / PT Angkasa Pura"
                           class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs">
                </div>
            </div>

            {{-- Kata Sandi Baru (Opsional) --}}
            <div class="p-3 bg-amber-50/60 border border-amber-200/80 rounded-2xl space-y-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-key text-amber-600 text-xs"></i>
                    <span class="text-xs font-bold text-amber-900">Ubah Kata Sandi (Opsional)</span>
                    <span class="text-[10px] text-amber-700 font-normal ml-auto">Kosongkan jika tidak ingin mengubah password</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <input type="password" id="edit_password" name="password" minlength="8"
                               placeholder="Kata sandi baru (min 8 karakter)"
                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs">
                    </div>
                    <div>
                        <input type="password" id="edit_password_confirm" name="password_confirmation" minlength="8"
                               placeholder="Ulangi kata sandi baru"
                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs">
                    </div>
                </div>
            </div>

            {{-- Penugasan Kamera (Khusus Operator) --}}
            <div id="edit_sectionPenugasanKamera" class="space-y-2.5 pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-video text-blue-600"></i>
                            <span>Penugasan Perangkat Kamera & Kode Akses</span>
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pilih titik kamera yang diizinkan untuk diakses operator ini.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleAllCheckboxes('#edit_sectionPenugasanKamera .edit-camera-cb', true)" class="text-[11px] font-bold text-blue-600 hover:underline">
                            Pilih Semua
                        </button>
                        <span class="text-slate-300">&bull;</span>
                        <button type="button" onclick="toggleAllCheckboxes('#edit_sectionPenugasanKamera .edit-camera-cb', false)" class="text-[11px] font-semibold text-slate-500 hover:underline">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="max-h-48 overflow-y-auto space-y-2 border border-slate-200 rounded-xl p-2.5 bg-slate-50/50 custom-scrollbar">
                    @forelse($allCameraDevices as $cam)
                        <label class="flex items-center justify-between p-2.5 bg-white border border-slate-200/80 rounded-xl hover:bg-blue-50/50 cursor-pointer transition shadow-2xs">
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <input type="checkbox" name="camera_devices[]" value="{{ $cam->id }}" id="edit_cam_{{ $cam->id }}"
                                       class="edit-camera-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 shrink-0">
                                <div class="min-w-0 truncate">
                                    <div class="font-bold text-xs text-slate-800 truncate flex items-center gap-1.5">
                                        <span class="truncate">{{ $cam->nama_kamera }}</span>
                                        <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-1.5 py-0.2 rounded shrink-0">
                                            Area {{ $cam->kode_area }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                        Tipe: {{ strtoupper(str_replace('_', ' ', $cam->tipe_scan)) }}
                                        @if($cam->areaAkses)
                                            &bull; {{ $cam->areaAkses->keterangan }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <code class="font-mono text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 shrink-0">
                                {{ $cam->kode_akses }}
                            </code>
                        </label>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fas fa-video-slash text-xl mb-1 block"></i>
                            Belum ada perangkat kamera aktif yang didaftarkan di sistem.
                        </div>
                    @endforelse
                </div>

                {{-- Opsi Kirim Email --}}
                <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="kirim_email" value="1"
                               class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        <span class="text-xs font-semibold text-emerald-900 flex items-center gap-1.5">
                            <i class="fas fa-paper-plane text-emerald-600"></i>
                            <span>Kirim pembaharuan rincian kode akses ke email operator</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditUser')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white text-xs font-bold shadow-md shadow-amber-500/20 transition cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Perbarui Data Akun</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- ======================================================================== --}}
    {{-- 3. MODAL ASSIGN KAMERA CEPAT (KHUSUS OPERATOR)                           --}}
    {{-- ======================================================================== --}}
    <x-modal id="modalAssignKamera" maxWidth="max-w-xl" title="Tugaskan & Kirim Kode Akses Kamera" subtitle="Atur otorisasi perangkat kamera untuk operator" icon="fas fa-key" iconColor="bg-blue-50 text-blue-600 border-blue-100">
        <form id="formAssignKamera" method="POST" action="" class="space-y-4">
            @csrf

            {{-- Operator Target Banner --}}
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs" id="assign_user_initial">
                        OP
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-xs text-slate-800 truncate" id="assign_user_name">-</h4>
                        <p class="text-[11px] text-slate-500 truncate" id="assign_user_email">-</p>
                    </div>
                </div>
                <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-2.5 py-0.5 rounded-full border border-blue-200 shrink-0">
                    Operator Scanner
                </span>
            </div>

            {{-- Checklist Kamera --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Pilih Kamera yang Boleh Diakses <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="selectAllCameras(true)" class="text-[11px] font-bold text-blue-600 hover:underline">
                            Pilih Semua
                        </button>
                        <span class="text-slate-300">&bull;</span>
                        <button type="button" onclick="selectAllCameras(false)" class="text-[11px] font-semibold text-slate-500 hover:underline">
                            Batal
                        </button>
                    </div>
                </div>

                <div class="max-h-56 overflow-y-auto space-y-2 border border-slate-200 rounded-xl p-2.5 bg-slate-50/50 custom-scrollbar" id="cameraCheckboxList">
                    @forelse($allCameraDevices as $cam)
                        <label class="flex items-center justify-between p-2.5 bg-white border border-slate-200/80 rounded-xl hover:bg-blue-50/50 cursor-pointer transition shadow-2xs">
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <input type="checkbox" name="camera_ids[]" value="{{ $cam->id }}"
                                       data-kode-akses="{{ $cam->kode_akses }}"
                                       data-nama="{{ $cam->nama_kamera }}"
                                       data-area="{{ $cam->kode_area }}"
                                       data-tipe="{{ $cam->tipe_scan }}"
                                       class="camera-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 shrink-0">
                                <div class="min-w-0 truncate">
                                    <div class="font-bold text-xs text-slate-800 truncate flex items-center gap-1.5">
                                        <span class="truncate">{{ $cam->nama_kamera }}</span>
                                        <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-1.5 py-0.2 rounded shrink-0">
                                            Area {{ $cam->kode_area }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                        Tipe: {{ strtoupper(str_replace('_', ' ', $cam->tipe_scan)) }}
                                        @if($cam->areaAkses)
                                            &bull; {{ $cam->areaAkses->keterangan }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <code class="font-mono text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 shrink-0">
                                {{ $cam->kode_akses }}
                            </code>
                        </label>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fas fa-video-slash text-xl mb-1 block"></i>
                            Belum ada perangkat kamera aktif yang didaftarkan di sistem.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Opsi Kirim Email --}}
            <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="kirim_email" value="1" checked id="checkboxKirimEmail"
                           class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <div>
                        <span class="text-xs font-bold text-emerald-900 block flex items-center gap-1.5">
                            <i class="fas fa-paper-plane text-emerald-600"></i> Kirim Rincian Kode Akses ke Email Operator
                        </span>
                        <span class="text-[11px] text-emerald-700 leading-tight block mt-0.5">
                            Sistem akan otomatis mengirimkan email resmi berisi daftar kamera dan kode akses.
                        </span>
                    </div>
                </label>
            </div>

            {{-- Pesan Tambahan --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Pesan / Catatan Tambahan <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span>
                </label>
                <textarea name="pesan_tambahan" id="pesanTambahan" rows="2"
                          placeholder="Contoh: Harap segera login dan pastikan scanner di Gate 1 aktif sebelum pukul 08:00 WIB."
                          class="w-full text-xs bg-white border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs"></textarea>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="copyWhatsAppFormat()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2.5 rounded-xl transition shadow-xs cursor-pointer"
                        title="Salin rincian kode akses dalam format chat WhatsApp">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span>Salin Format WA</span>
                </button>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="closeModal('modalAssignKamera')"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-check"></i>
                        <span>Simpan Otorisasi</span>
                    </button>
                </div>
            </div>
        </form>
    </x-modal>

    {{-- ======================================================================== --}}
    {{-- 4. REUSABLE MODAL DELETE COMPONENT                                       --}}
    {{-- ======================================================================== --}}
    <x-modal-delete id="deleteUserModal" />

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

        // Password Show/Hide Toggle
        function togglePassVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Toggle Checkboxes Helper
        function toggleAllCheckboxes(selector, status) {
            document.querySelectorAll(selector).forEach(cb => { cb.checked = status; });
        }

        // ====================================================================
        // MODAL TAMBAH USER HANDLERS
        // ====================================================================
        const tambahRoleOperator = document.getElementById('tambah_roleOperator');
        const tambahRoleAdmin = document.getElementById('tambah_roleAdmin');
        const tambahLabelRoleOperator = document.getElementById('tambah_labelRoleOperator');
        const tambahLabelRoleAdmin = document.getElementById('tambah_labelRoleAdmin');
        const tambahSectionPenugasan = document.getElementById('tambah_sectionPenugasanKamera');

        function updateTambahRoleUI() {
            if (tambahRoleOperator.checked) {
                tambahLabelRoleOperator.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all shadow-2xs border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20";
                tambahLabelRoleAdmin.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all border-slate-200 hover:border-slate-300";
                document.getElementById('tambah_checkOperator').classList.remove('hidden');
                document.getElementById('tambah_checkAdmin').classList.add('hidden');
                tambahSectionPenugasan.classList.remove('hidden');
            } else {
                tambahLabelRoleAdmin.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all shadow-2xs border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20";
                tambahLabelRoleOperator.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all border-slate-200 hover:border-slate-300";
                document.getElementById('tambah_checkOperator').classList.add('hidden');
                document.getElementById('tambah_checkAdmin').classList.remove('hidden');
                tambahSectionPenugasan.classList.add('hidden');
            }
        }

        if (tambahLabelRoleOperator && tambahLabelRoleAdmin) {
            tambahLabelRoleOperator.addEventListener('click', () => { tambahRoleOperator.checked = true; updateTambahRoleUI(); });
            tambahLabelRoleAdmin.addEventListener('click', () => { tambahRoleAdmin.checked = true; updateTambahRoleUI(); });
        }

        function openModalTambahUser() {
            document.getElementById('formTambahUser').reset();
            tambahRoleOperator.checked = true;
            updateTambahRoleUI();
            openModal('modalTambahUser');
        }

        // ====================================================================
        // MODAL EDIT USER HANDLERS
        // ====================================================================
        const editRoleOperator = document.getElementById('edit_roleOperator');
        const editRoleAdmin = document.getElementById('edit_roleAdmin');
        const editLabelRoleOperator = document.getElementById('edit_labelRoleOperator');
        const editLabelRoleAdmin = document.getElementById('edit_labelRoleAdmin');
        const editSectionPenugasan = document.getElementById('edit_sectionPenugasanKamera');

        function updateEditRoleUI() {
            if (editRoleOperator.checked) {
                editLabelRoleOperator.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all shadow-2xs border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20";
                editLabelRoleAdmin.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all border-slate-200 hover:border-slate-300";
                document.getElementById('edit_checkOperator').classList.remove('hidden');
                document.getElementById('edit_checkAdmin').classList.add('hidden');
                editSectionPenugasan.classList.remove('hidden');
            } else {
                editLabelRoleAdmin.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all shadow-2xs border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20";
                editLabelRoleOperator.className = "relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all border-slate-200 hover:border-slate-300";
                document.getElementById('edit_checkOperator').classList.add('hidden');
                document.getElementById('edit_checkAdmin').classList.remove('hidden');
                editSectionPenugasan.classList.add('hidden');
            }
        }

        if (editLabelRoleOperator && editLabelRoleAdmin) {
            editLabelRoleOperator.addEventListener('click', () => { editRoleOperator.checked = true; updateEditRoleUI(); });
            editLabelRoleAdmin.addEventListener('click', () => { editRoleAdmin.checked = true; updateEditRoleUI(); });
        }

        function openModalEditUserFromBtn(btn) {
            try {
                const raw = btn.getAttribute('data-user');
                const user = typeof raw === 'string' ? JSON.parse(raw) : raw;
                openModalEditUser(user);
            } catch (e) {
                console.error("Gagal parse data user untuk edit:", e);
            }
        }

        function openModalEditUser(user) {
            const form = document.getElementById('formEditUser');
            form.action = "{{ url('/administrator/users') }}/" + user.id;

            document.getElementById('edit_name').value = user.name || '';
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('edit_perusahaan').value = user.perusahaan || '';
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirm').value = '';

            if (user.role === 'administrator') {
                editRoleAdmin.checked = true;
            } else {
                editRoleOperator.checked = true;
            }
            updateEditRoleUI();

            // Check cameras assigned
            const assignedIds = (user.camera_devices || []).map(c => c.id);
            document.querySelectorAll('.edit-camera-cb').forEach(cb => {
                cb.checked = assignedIds.includes(parseInt(cb.value));
            });

            openModal('modalEditUser');
        }

        // ====================================================================
        // MODAL ASSIGN KAMERA CEPAT HANDLERS
        // ====================================================================
        let currentTargetUser = null;

        function openModalAssignKameraFromBtn(btn) {
            try {
                const raw = btn.getAttribute('data-user');
                const user = typeof raw === 'string' ? JSON.parse(raw) : raw;
                openModalAssignKamera(user);
            } catch (e) {
                console.error("Gagal membuka modal assign kamera:", e);
            }
        }

        function openModalAssignKamera(user) {
            currentTargetUser = user;
            const form = document.getElementById('formAssignKamera');
            form.action = "{{ url('/administrator/users') }}/" + user.id + "/assign-kamera";

            document.getElementById('assign_user_name').textContent = user.name;
            document.getElementById('assign_user_email').textContent = user.email + (user.perusahaan ? ' • ' + user.perusahaan : '');
            document.getElementById('assign_user_initial').textContent = user.name.substring(0, 2).toUpperCase();
            document.getElementById('pesanTambahan').value = '';

            const assignedIds = (user.camera_devices || []).map(c => c.id);
            document.querySelectorAll('.camera-checkbox').forEach(cb => {
                cb.checked = assignedIds.includes(parseInt(cb.value));
            });

            openModal('modalAssignKamera');
        }

        function selectAllCameras(status) {
            document.querySelectorAll('.camera-checkbox').forEach(cb => { cb.checked = status; });
        }

        function copyWhatsAppFormat() {
            if (!currentTargetUser) return;

            const selectedCbs = Array.from(document.querySelectorAll('.camera-checkbox:checked'));
            if (selectedCbs.length === 0) {
                alert('Pilih minimal satu perangkat kamera untuk menyalin format WhatsApp.');
                return;
            }

            let text = `*PEMBERITAHUAN KODE AKSES SCANNER MONPASKU*\n`;
            text += `--------------------------------------\n`;
            text += `Halo *${currentTargetUser.name}*,\n`;
            text += `Berikut adalah daftar perangkat kamera & kode akses yang ditugaskan kepada Anda:\n\n`;

            selectedCbs.forEach((cb, idx) => {
                const nama = cb.getAttribute('data-nama');
                const area = cb.getAttribute('data-area');
                const kode = cb.getAttribute('data-kode-akses');
                const tipe = cb.getAttribute('data-tipe');
                text += `${idx + 1}. *${nama}* (Area: ${area})\n`;
                text += `   • Kode Akses: \`${kode}\`\n`;
                text += `   • Tipe Scan: ${tipe.toUpperCase()}\n\n`;
            });

            const extra = document.getElementById('pesanTambahan').value.trim();
            if (extra) {
                text += `📝 *Catatan:* ${extra}\n\n`;
            }

            text += `Silakan login di sistem MONPASKU: {{ url('/operator/kamera') }}\n`;
            text += `Jaga kerahasiaan kode akses dan selamat bertugas!`;

            navigator.clipboard.writeText(text).then(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Disalin!',
                        text: 'Format WhatsApp berhasil disalin ke clipboard.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert('Format pesan WhatsApp berhasil disalin ke clipboard!');
                }
            }).catch(err => {
                alert('Gagal menyalin format pesan.');
            });
        }

        // ====================================================================
        // DELETE USER HANDLER
        // ====================================================================
        function confirmDeleteUser(id, nama, email) {
            if (typeof window.openDeleteModal === 'function') {
                window.openDeleteModal({
                    id: 'deleteUserModal',
                    title: 'Hapus Akun Pengguna?',
                    message: `Apakah Anda yakin ingin menghapus akun [${nama}] (${email})? Tindakan ini akan menghapus akses login pengguna secara permanen.`,
                    targetName: `${nama} (${email})`,
                    action: "{{ url('/administrator/users') }}/" + id,
                    btnText: 'Hapus Akun'
                });
            } else {
                if (confirm(`Hapus akun pengguna "${nama}"?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('/administrator/users') }}/" + id;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
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

        async function fetchSpaUserTable(url, pushState = true) {
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

                if (pushState) {
                    window.history.pushState(null, '', url);
                }

                // Perbarui data KPI di kartu statistik atas
                const kpiDataEl = document.getElementById('spaKpiData');
                if (kpiDataEl) {
                    const total    = kpiDataEl.getAttribute('data-total');
                    const operator = kpiDataEl.getAttribute('data-operator');
                    const admin    = kpiDataEl.getAttribute('data-admin');
                    const kamera   = kpiDataEl.getAttribute('data-kamera');

                    if (total !== null && document.getElementById('kpiTotalUsers')) {
                        document.getElementById('kpiTotalUsers').textContent = total;
                    }
                    if (operator !== null && document.getElementById('kpiTotalOperator')) {
                        document.getElementById('kpiTotalOperator').textContent = operator;
                    }
                    if (admin !== null && document.getElementById('kpiTotalAdmin')) {
                        document.getElementById('kpiTotalAdmin').textContent = admin;
                    }
                    if (kamera !== null && document.getElementById('kpiTotalKamera')) {
                        document.getElementById('kpiTotalKamera').textContent = kamera;
                    }
                }
            } catch (err) {
                console.error('SPA Navigation Error:', err);
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
                fetchSpaUserTable(link.href, true);
            }
        });

        // Intercept Submit Filter Form
        const filterForm = document.getElementById('filterUserForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const targetUrl = `${filterForm.action}?${params.toString()}`;
                fetchSpaUserTable(targetUrl, true);
            });

            // Live Change pada Dropdown Role
            const roleSelect = document.getElementById('filterRoleSelect');
            if (roleSelect) {
                roleSelect.addEventListener('change', () => {
                    filterForm.dispatchEvent(new Event('submit'));
                });
            }

            // Live Debounce pada Input Pencarian
            const searchInput = document.getElementById('userSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(filterDebounceTimer);
                    filterDebounceTimer = setTimeout(() => {
                        filterForm.dispatchEvent(new Event('submit'));
                    }, 350);
                });
            }
        }

        function clearUserSearch() {
            const input = document.getElementById('userSearchInput');
            if (input) {
                input.value = '';
                filterForm.dispatchEvent(new Event('submit'));
            }
        }

        function resetUserFilter() {
            if (filterForm) {
                filterForm.reset();
                const targetUrl = filterForm.action;
                fetchSpaUserTable(targetUrl, true);
            }
        }

        // Handle Browser Back & Forward Buttons
        window.addEventListener('popstate', function() {
            fetchSpaUserTable(window.location.href, false);
        });
    </script>
</x-app-layout>
