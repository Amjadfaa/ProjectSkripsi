<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-building"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Data Instansi
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">Kelola profil instansi, perusahaan mitra, dan alokasi batas kuota kartu PAS</p>
            </div>
        </div>
    </x-slot>

    {{-- KPI STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Instansi --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Instansi</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1 tracking-tight">{{ number_format($totalInstansiAll) }}</h3>
                <p class="text-[11px] text-slate-400 mt-1">Terdaftar dalam sistem</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-building"></i>
            </div>
        </div>

        {{-- Instansi Aktif --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instansi Aktif</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1 tracking-tight">{{ number_format($totalAktifAll) }}</h3>
                <p class="text-[11px] text-emerald-500 mt-1 font-medium">Status operasional aktif</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>

        {{-- Total Kuota PAS --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Kuota PAS</p>
                <h3 class="text-2xl font-black text-indigo-600 mt-1 tracking-tight">{{ number_format($totalKuotaAll) }}</h3>
                <p class="text-[11px] text-indigo-500 mt-1 font-medium">Akumulasi seluruh kuota</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-boxes-stacked"></i>
            </div>
        </div>

        {{-- Kartu Terpakai --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between group hover:shadow-sm transition-shadow">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kartu Terpakai</p>
                <h3 class="text-2xl font-black text-purple-600 mt-1 tracking-tight">{{ number_format($totalTerpakaiAll) }}</h3>
                <p class="text-[11px] text-purple-500 mt-1 font-medium">Kartu berstatus aktif & terbit</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:scale-110 transition-transform">
                <i class="fas fa-id-card"></i>
            </div>
        </div>
    </div>

    {{-- REUSABLE TABLE CARD COMPONENT --}}
    <x-table-card title="Daftar Instansi / Perusahaan"
                  subtitle="Kelola profil perusahaan mitra dan alokasi kuota kartu PAS bandara"
                  icon="fas fa-building"
                  iconColor="bg-blue-50 text-blue-600">

        {{-- Header Actions Slot --}}
        <x-slot name="actions">
            <!-- Tombol Import Excel -->
            <button type="button" onclick="openModal('modalImportInstansi')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-file-import"></i>
                <span>Import Excel</span>
            </button>

            <!-- Tombol Export Excel -->
            <a href="{{ route('administrator.instansi.export.excel') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer"
               title="Unduh Data Instansi format Excel">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>

            <!-- Tombol Tambah Instansi -->
            <button type="button" onclick="openModalTambahInstansi()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Instansi</span>
            </button>
        </x-slot>

        {{-- Filters Slot --}}
        <x-slot name="filters">
            <form id="filterForm" method="GET" action="{{ route('administrator.instansi.index') }}" class="flex flex-wrap items-end gap-3">
                <!-- Search Input -->
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama instansi, email, telepon..."
                               class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs">
                        <button type="button" id="btnClearSearch" onclick="clearSearchInput()"
                                class="{{ request('search') ? '' : 'hidden' }} absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-500 text-xs transition cursor-pointer"
                                title="Hapus pencarian">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Status -->
                <div class="min-w-[140px]">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                    <select id="statusFilter" name="status"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-2xs cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="w-28">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Per Halaman</label>
                    <select id="perPageSelect" name="per_page"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-2xs cursor-pointer">
                        @foreach([10, 15, 25, 50] as $pp)
                            <option value="{{ $pp }}" {{ request('per_page', 10) == $pp ? 'selected' : '' }}>{{ $pp }} data</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-xs transition-all cursor-pointer">
                        <i class="fas fa-filter text-[11px]"></i> Filter
                    </button>
                    <button type="button" onclick="resetFilterSpa(event)"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all cursor-pointer border border-slate-200/80"
                            title="Reset Filter">
                        <i class="fas fa-rotate-left"></i> Reset
                    </button>
                </div>
            </form>
        </x-slot>

        <!-- Table Viewport with Persistent Loading Overlay (SPA Replaced via AJAX) -->
        <div class="relative">
            <!-- Shimmer Loading Overlay -->
            <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 hidden items-center justify-center transition-all duration-200">
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/90 text-white shadow-2xl text-xs font-bold border border-slate-700 backdrop-blur-md">
                    <i class="fas fa-circle-notch fa-spin text-blue-400 text-base"></i>
                    <span>Memperbarui data instansi...</span>
                </div>
            </div>

            <!-- Table Container (SPA HTML injected here) -->
            <div id="tableContainer" class="transition-opacity duration-200">
                @include('administrator.instansi.partials.table')
            </div>
        </div>

    </x-table-card>

    <!-- ======================================================================== -->
    {{-- REUSABLE MODALS (USING x-modal)                                        --}}
    <!-- ======================================================================== -->

    {{-- 1. MODAL TAMBAH INSTANSI --}}
    <x-modal id="modalTambahInstansi"
             title="Tambah Instansi Baru"
             subtitle="Daftarkan profil instansi / perusahaan mitra baru"
             icon="fas fa-building"
             iconColor="bg-blue-50 text-blue-600 border-blue-100"
             maxWidth="max-w-lg">
        <form method="POST" action="{{ route('administrator.instansi.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Instansi / Perusahaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}"
                           placeholder="Contoh: PT Angkasa Pura Indonesia"
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs" required>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kuota Kartu PAS <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuota" value="{{ old('kuota', 10) }}" min="0"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Status <span class="text-rose-500">*</span></label>
                        <select name="is_active" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs cursor-pointer" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Email (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="email@instansi.com"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Telepon (Opsional)</label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}"
                               placeholder="0812xxxxxxxx"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Alamat Kantor (Opsional)</label>
                    <textarea name="alamat" rows="3" placeholder="Alamat lengkap instansi..."
                              class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" onclick="closeModal('modalTambahInstansi')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200/90 shadow-2xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 transition cursor-pointer">
                    <i class="fas fa-save mr-1"></i> Simpan Instansi
                </button>
            </x-slot>
        </form>
    </x-modal>

    {{-- 2. MODAL EDIT INSTANSI --}}
    <x-modal id="modalEditInstansi"
             title="Edit Data Instansi"
             subtitle="Perbarui profil instansi dan kuota kartu PAS"
             icon="fas fa-pen-to-square"
             iconColor="bg-amber-50 text-amber-600 border-amber-100"
             maxWidth="max-w-lg">
        <form id="formEditInstansi" method="POST" action="">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Instansi / Perusahaan <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_nama_instansi" name="nama_instansi"
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs" required>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kuota Kartu PAS <span class="text-rose-500">*</span></label>
                        <input type="number" id="edit_kuota" name="kuota" min="0"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Status <span class="text-rose-500">*</span></label>
                        <select id="edit_is_active" name="is_active" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs cursor-pointer" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Email (Opsional)</label>
                        <input type="email" id="edit_email" name="email"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Telepon (Opsional)</label>
                        <input type="text" id="edit_telepon" name="telepon"
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Alamat Kantor (Opsional)</label>
                    <textarea id="edit_alamat" name="alamat" rows="3"
                              class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition shadow-2xs"></textarea>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" onclick="closeModal('modalEditInstansi')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200/90 shadow-2xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/20 transition cursor-pointer">
                    <i class="fas fa-check mr-1"></i> Simpan Perubahan
                </button>
            </x-slot>
        </form>
    </x-modal>

    {{-- 3. MODAL IMPORT INSTANSI --}}
    <x-modal id="modalImportInstansi"
             title="Import Data Instansi"
             subtitle="Upload file Excel (.xlsx / .xls) untuk import data massal"
             icon="fas fa-file-import"
             iconColor="bg-amber-50 text-amber-600 border-amber-100"
             maxWidth="max-w-xl">
        <form method="POST" action="{{ route('administrator.instansi.import.excel') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-3.5">
                <!-- Info Anti Duplikasi -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 flex items-center gap-2.5">
                    <i class="fas fa-shield-alt text-amber-600 text-sm shrink-0"></i>
                    <div>
                        <strong class="font-bold">Anti-Duplikasi:</strong>
                        Jika instansi sudah ada di sistem, data lama <strong>tidak akan ditimpa</strong> dan baris tersebut akan <strong>dilewati (di-skip)</strong> secara otomatis.
                    </div>
                </div>

                <!-- Template Download Banner -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center justify-between flex-wrap gap-2">
                    <div class="text-xs text-blue-900">
                        <p class="font-bold flex items-center gap-1.5">
                            <i class="fas fa-file-excel text-emerald-600"></i> Format File Excel
                        </p>
                        <p class="text-blue-700 text-[11px] mt-0.5">Disarankan menggunakan template resmi agar kolom sesuai.</p>
                    </div>
                    <a href="{{ route('administrator.instansi.template.excel') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 hover:text-blue-900 bg-white px-3 py-1.5 rounded-lg border border-blue-300 shadow-2xs hover:bg-blue-50 transition">
                        <i class="fas fa-download text-emerald-600"></i> Unduh Template (.xlsx)
                    </a>
                </div>

                <!-- Detail Susunan Kolom -->
                <details class="text-xs text-slate-600 bg-slate-50 rounded-xl border border-slate-200 overflow-hidden">
                    <summary class="font-semibold text-slate-700 px-3.5 py-2 cursor-pointer hover:bg-slate-100 flex items-center justify-between select-none">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-list-ul text-blue-500"></i> Lihat Susunan Kolom Excel (7 Kolom)
                        </span>
                        <span class="text-slate-400 text-[11px]">Buka / Tutup ▾</span>
                    </summary>
                    <div class="p-3 border-t border-slate-200 bg-white overflow-x-auto">
                        <table class="w-full text-xs text-slate-700">
                            <thead class="text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th class="py-1 text-left font-semibold">Kolom</th>
                                    <th class="py-1 text-left font-semibold">Field</th>
                                    <th class="py-1 text-left font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr><td class="py-1 font-bold text-slate-700">Kolom A</td><td>No</td><td class="text-slate-400">Nomor urut (Opsional)</td></tr>
                                <tr><td class="py-1 font-bold text-blue-600">Kolom B</td><td class="font-semibold">Nama Instansi</td><td class="text-emerald-600 font-medium">Wajib diisi</td></tr>
                                <tr><td class="py-1 font-bold text-slate-700">Kolom C</td><td>Kuota PAS</td><td class="text-slate-400">Default: 10</td></tr>
                                <tr><td class="py-1 font-bold text-slate-700">Kolom D</td><td>Email</td><td class="text-slate-400">Opsional</td></tr>
                                <tr><td class="py-1 font-bold text-slate-700">Kolom E</td><td>Nomor Telepon</td><td class="text-slate-400">Opsional</td></tr>
                                <tr><td class="py-1 font-bold text-slate-700">Kolom F</td><td>Alamat</td><td class="text-slate-400">Opsional</td></tr>
                                <tr><td class="py-1 font-bold text-slate-700">Kolom G</td><td>Status</td><td class="text-slate-400">Aktif / Nonaktif</td></tr>
                            </tbody>
                        </table>
                    </div>
                </details>

                <!-- Dropzone File Upload -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih File Excel (.xlsx / .xls)</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl py-5 px-5 text-center hover:border-amber-500 transition cursor-pointer bg-slate-50 hover:bg-amber-50/20"
                         onclick="document.getElementById('import_instansi_file').click()">
                        <input type="file" name="file" id="import_instansi_file" accept=".xlsx,.xls"
                               class="hidden" onchange="showModalImportInstansiFileName(this)" required>
                        <i class="fas fa-cloud-arrow-up text-3xl text-amber-500 mb-1.5"></i>
                        <p class="text-slate-700 font-medium text-xs">Klik untuk memilih file Excel dari komputer</p>
                        <p class="text-slate-400 text-[11px] mt-0.5">Format: .xlsx atau .xls (Ukuran maks. 10MB)</p>
                        <p id="modalImportInstansiFileName" class="text-emerald-600 text-xs mt-2 font-semibold"></p>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" onclick="closeModal('modalImportInstansi')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200/90 shadow-2xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/20 transition cursor-pointer">
                    <i class="fas fa-upload mr-1"></i> Import Sekarang
                </button>
            </x-slot>
        </form>
    </x-modal>

    {{-- 4. REUSABLE MODAL DELETE --}}
    <x-modal-delete id="globalDeleteModal" />

    <!-- ======================================================================== -->
    {{-- SPA JAVASCRIPT & MODAL HANDLERS                                         --}}
    <!-- ======================================================================== -->
    <script>
        // Modal Helpers (Vanilla JS instant toggle)
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

        function openModalTambahInstansi() {
            openModal('modalTambahInstansi');
        }

        function openModalEditInstansi(instansi) {
            const form = document.getElementById('formEditInstansi');
            form.action = "{{ url('/administrator/instansi') }}/" + instansi.id;

            document.getElementById('edit_nama_instansi').value = instansi.nama_instansi || '';
            document.getElementById('edit_kuota').value         = instansi.kuota ?? 0;
            document.getElementById('edit_is_active').value     = instansi.is_active ? '1' : '0';
            document.getElementById('edit_email').value         = instansi.email || '';
            document.getElementById('edit_telepon').value       = instansi.telepon || '';
            document.getElementById('edit_alamat').value        = instansi.alamat || '';

            openModal('modalEditInstansi');
        }

        function showModalImportInstansiFileName(input) {
            const fileName = input.files[0]?.name ?? '';
            document.getElementById('modalImportInstansiFileName').textContent = fileName ? '✅ File Dipilih: ' + fileName : '';
        }

        function confirmDeleteInstansi(id, nama) {
            window.openDeleteModal({
                id: 'globalDeleteModal',
                action: `{{ url('/administrator/instansi') }}/${id}`,
                title: 'Hapus Data Instansi?',
                message: 'Apakah Anda yakin ingin menghapus data instansi ini? Seluruh data relasi terkait instansi ini akan terpengaruh.',
                targetName: nama,
                btnText: 'Ya, Hapus Instansi'
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.app-modal:not(.hidden)');
                openModals.forEach(m => closeModal(m.id));
            }
        });

        // ---------------------------------------------------------------------
        // SPA TABLE NAVIGATION & FILTERING
        // ---------------------------------------------------------------------
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

        async function loadTableData(url, pushState = true) {
            const container = document.getElementById('tableContainer');
            if (!container) return;

            showTableLoading();

            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!res.ok) throw new Error('Network error');

                const html = await res.text();
                container.innerHTML = html;

                if (pushState) {
                    history.pushState(null, '', url);
                }

                syncFormWithUrl(url);
            } catch (err) {
                console.error('SPA Navigation Error:', err);
                window.location.href = url;
            } finally {
                hideTableLoading();
            }
        }

        function syncFormWithUrl(url) {
            try {
                const u = new URL(url, window.location.origin);
                const searchVal = u.searchParams.get('search') || '';
                const statusVal = u.searchParams.get('status') || '';
                const perPageVal = u.searchParams.get('per_page') || '10';

                const searchInput = document.getElementById('searchInput');
                const statusFilter = document.getElementById('statusFilter');
                const perPageSelect = document.getElementById('perPageSelect');
                const btnClear = document.getElementById('btnClearSearch');

                if (searchInput && searchInput.value !== searchVal) searchInput.value = searchVal;
                if (statusFilter && statusFilter.value !== statusVal) statusFilter.value = statusVal;
                if (perPageSelect && perPageSelect.value !== perPageVal) perPageSelect.value = perPageVal;

                if (btnClear) {
                    if (searchVal.trim() !== '') {
                        btnClear.classList.remove('hidden');
                    } else {
                        btnClear.classList.add('hidden');
                    }
                }
            } catch(e) {}
        }

        // Intercept all SPA pagination links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[data-spa="true"], a.pagination-link');
            if (link && link.href) {
                e.preventDefault();
                loadTableData(link.href, true);
            }
        });

        // Browser Back / Forward
        window.addEventListener('popstate', function() {
            loadTableData(window.location.href, false);
        });

        // SPA Filter Form Submit
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = new URL(filterForm.action, window.location.origin);
                const formData = new FormData(filterForm);
                for (const [key, val] of formData.entries()) {
                    if (val) {
                        url.searchParams.set(key, val);
                    }
                }
                url.searchParams.delete('page');
                loadTableData(url.toString(), true);
            });
        }

        // Search Input Debounce (350ms)
        let searchDebounceTimer;
        const searchInput = document.getElementById('searchInput');
        const btnClearSearch = document.getElementById('btnClearSearch');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (btnClearSearch) {
                    if (this.value.trim() !== '') {
                        btnClearSearch.classList.remove('hidden');
                    } else {
                        btnClearSearch.classList.add('hidden');
                    }
                }

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    if (filterForm) {
                        filterForm.dispatchEvent(new Event('submit'));
                    }
                }, 350);
            });
        }

        function clearSearchInput() {
            if (searchInput) {
                searchInput.value = '';
                if (btnClearSearch) btnClearSearch.classList.add('hidden');
                if (filterForm) filterForm.dispatchEvent(new Event('submit'));
            }
        }

        // Instant Filter dropdown changes
        document.querySelectorAll('#filterForm select').forEach(select => {
            select.addEventListener('change', () => {
                if (filterForm) filterForm.dispatchEvent(new Event('submit'));
            });
        });

        function resetFilterSpa(e) {
            if (e) e.preventDefault();
            if (filterForm) {
                filterForm.reset();
                if (searchInput) searchInput.value = '';
                const perPageSelect = document.getElementById('perPageSelect');
                if (perPageSelect) perPageSelect.value = '10';
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) statusFilter.value = '';
                if (btnClearSearch) btnClearSearch.classList.add('hidden');
                const baseUrl = "{{ route('administrator.instansi.index') }}";
                loadTableData(baseUrl, true);
            }
        }
    </script>
</x-app-layout>
