<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-id-card"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Data Kartu PAS Bandara
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">Kelola izin masuk, masa berlaku, dan hak akses area terbatas bandara</p>
            </div>
        </div>
    </x-slot>

    <!-- ======================================================================== -->
    <!-- REUSABLE TABLE CARD CONTAINER                                            -->
    <!-- ======================================================================== -->
    <x-table-card title="Database Kartu PAS"
                  subtitle="Pencatatan dan monitoring status seluruh kartu PAS bandara"
                  icon="fas fa-id-card"
                  iconColor="bg-blue-50 text-blue-600">
        
        <!-- Header Actions Slot -->
        <x-slot name="actions">
            <!-- Tombol Import Data (Modal) -->
            <button type="button" onclick="openModal('modalImportKartu')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-file-import"></i>
                <span>Import Data</span>
            </button>

            <!-- Dropdown Unduh Data Kartu PAS (Excel / PDF) -->
            <div class="relative inline-block text-left" id="downloadDropdownContainer">
                <button type="button" onclick="toggleDownloadDropdown(event)"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer">
                    <i class="fas fa-download"></i>
                    <span>Unduh Data</span>
                    <i class="fas fa-chevron-down text-[10px] ml-0.5"></i>
                </button>
                <div id="downloadDropdownMenu" 
                     class="hidden absolute right-0 mt-2 w-56 rounded-2xl shadow-2xl bg-white border border-slate-100 ring-1 ring-black ring-opacity-5 z-50 divide-y divide-slate-100 overflow-hidden animate-dropdown-fade-in">
                    <div class="py-1">
                        <a id="btnExportExcel" href="{{ route('administrator.kartu-pas.export.excel', request()->query()) }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition">
                            <i class="fas fa-file-excel text-emerald-600 text-sm"></i>
                            <span>Unduh Format Excel (.xlsx)</span>
                        </a>
                        <a id="btnExportPdf" href="{{ route('administrator.kartu-pas.export.pdf', request()->query()) }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-rose-50 hover:text-rose-700 transition">
                            <i class="fas fa-file-pdf text-rose-600 text-sm"></i>
                            <span>Unduh Format PDF (.pdf)</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tombol Tambah Kartu PAS (Modal) -->
            <button type="button" onclick="openModalTambahKartu()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <i class="fas fa-plus-circle"></i>
                <span>Tambah Kartu PAS</span>
            </button>
        </x-slot>

        <!-- Filter Slot -->
        <x-slot name="filters">
            <form id="filterForm" method="GET" action="{{ route('administrator.kartu-pas.index') }}">
                <div class="flex flex-wrap items-end gap-3">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-[220px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative flex-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none">
                                <i class="fas fa-magnifying-glass"></i>
                            </span>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama pemegang, nomor registrasi..."
                                   style="padding-left: 2.25rem !important;"
                                   class="w-full pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs">
                            @if(request('search'))
                                <button type="button" onclick="clearSearchInput()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-circle-xmark text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Filter Instansi (Searchable Dropdown) -->
                    <div class="min-w-[200px] sm:min-w-[240px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Instansi</label>
                        <div class="relative searchable-dropdown-wrapper" id="containerFilterInstansi">
                            <!-- Native select kept hidden for form submission & SPA compatibility -->
                            <select id="filterInstansi" name="instansi" class="hidden">
                                <option value="">Semua Instansi</option>
                                @foreach($instansiList as $instansi)
                                    <option value="{{ $instansi->nama_instansi }}" {{ request('instansi') == $instansi->nama_instansi ? 'selected' : '' }}>
                                        {{ $instansi->nama_instansi }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Trigger Button -->
                            <button type="button" 
                                    data-dropdown-target="dropdownFilterInstansi"
                                    onclick="toggleSearchableDropdown('dropdownFilterInstansi')"
                                    class="w-full pl-3 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs cursor-pointer flex items-center justify-between gap-2 hover:border-slate-300">
                                <div class="flex items-center gap-2 min-w-0 pr-1">
                                    <i class="fas fa-building text-slate-400 text-xs shrink-0"></i>
                                    <span class="dropdown-selected-label truncate font-medium text-slate-700">
                                        {{ request('instansi') ?: 'Semua Instansi' }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 dropdown-chevron transition-transform duration-200"></i>
                            </button>

                            <!-- Dropdown Menu Panel with Real-time Search -->
                            <div id="dropdownFilterInstansi" 
                                 class="searchable-dropdown-menu absolute left-0 top-full mt-1.5 w-72 sm:w-80 bg-white border border-slate-200/90 rounded-2xl shadow-2xl z-50 p-2.5 hidden animate-dropdown-fade">
                                <!-- Search Input with Clear Button -->
                                <div class="searchable-input-box mb-2">
                                    <i class="fas fa-search searchable-search-icon"></i>
                                    <input type="text" 
                                           oninput="filterSearchableOptions('dropdownFilterInstansi', this.value)"
                                           placeholder="Cari nama instansi..." 
                                           autocomplete="off"
                                           class="searchable-input"
                                           style="padding-left: 2.25rem !important; padding-right: 2rem !important;">
                                    <button type="button" 
                                            onclick="clearSearchableInput('dropdownFilterInstansi')"
                                            class="searchable-clear-btn hidden"
                                            title="Hapus pencarian">
                                        <i class="fas fa-times-circle text-xs"></i>
                                    </button>
                                </div>

                                <!-- Options List Container (Compact & Scrollable) -->
                                <div class="searchable-list-scroll space-y-0.5" id="listFilterInstansi">
                                    <!-- Default Option: Semua Instansi -->
                                    <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition {{ !request('instansi') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                                         data-value=""
                                         data-search-text="semua instansi"
                                         data-display-name="Semua Instansi"
                                         onclick="selectSearchableOption('filterInstansi', '', 'Semua Instansi', 'dropdownFilterInstansi')">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-layer-group text-slate-400 text-xs"></i>
                                            <span>Semua Instansi</span>
                                        </div>
                                        @if(!request('instansi'))
                                            <i class="fas fa-check text-blue-600 text-xs shrink-0 check-icon"></i>
                                        @endif
                                    </div>

                                    <div class="my-1 border-t border-slate-100"></div>

                                    <!-- Instansi Options -->
                                    @foreach($instansiList as $instansi)
                                        @php $isSelected = (request('instansi') == $instansi->nama_instansi); @endphp
                                        <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition {{ $isSelected ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                                             data-value="{{ $instansi->nama_instansi }}"
                                             data-search-text="{{ $instansi->nama_instansi }}"
                                             data-display-name="{{ $instansi->nama_instansi }}"
                                             onclick="selectSearchableOption('filterInstansi', '{{ addslashes($instansi->nama_instansi) }}', '{{ addslashes($instansi->nama_instansi) }}', 'dropdownFilterInstansi')">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <i class="fas fa-building text-slate-400 text-xs shrink-0"></i>
                                                <span class="truncate">{{ $instansi->nama_instansi }}</span>
                                            </div>
                                            @if($isSelected)
                                                <i class="fas fa-check text-blue-600 text-xs shrink-0 check-icon"></i>
                                            @endif
                                        </div>
                                    @endforeach

                                    <!-- Empty Search Result State -->
                                    <div class="searchable-empty py-6 text-center text-slate-400 text-xs hidden">
                                        <i class="fas fa-search-minus text-sm text-slate-300 block mb-1"></i>
                                        Tidak ada instansi yang cocok
                                    </div>
                                </div>

                                <!-- Dropdown Summary Footer -->
                                <div class="px-2 pt-2 mt-1 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 font-medium">
                                    <span>{{ count($instansiList) }} Instansi</span>
                                    <span>Ketik untuk memfilter</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Status -->
                    <div class="min-w-[140px]">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Status</label>
                        <select id="filterStatus" name="status" 
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="kadaluarsa" {{ request('status') == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-filter text-[11px]"></i>
                            <span>Filter</span>
                        </button>
                        <button type="button" onclick="resetFilter(event)"
                                class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                                title="Reset Filter">
                            <i class="fas fa-rotate-left mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </x-slot>

        <!-- Bulk Action Slot -->
        <x-slot name="bulkActions">
            <div class="flex items-center gap-2.5">
                <span class="font-bold text-blue-900 flex items-center gap-1.5">
                    <i class="fas fa-check-double text-blue-600"></i>
                    <span>Tindakan Massal</span>
                </span>
                <span id="selectedCountBadge" class="hidden px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-600 text-white shadow-2xs">
                    0 dipilih
                </span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Hapus yang dipilih -->
                <form id="formDeleteSelected" method="POST" action="{{ route('administrator.kartu-pas.destroy-selected') }}">
                    @csrf @method('DELETE')
                    <div id="selectedInputs"></div>
                    <button type="button" onclick="confirmDeleteSelected()"
                            id="btnDeleteSelected"
                            class="hidden items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition cursor-pointer">
                        <i class="fas fa-trash-can"></i>
                        <span>Hapus Dipilih</span>
                    </button>
                </form>

                <!-- Hapus Semua -->
                <form id="formDeleteAll" method="POST" action="{{ route('administrator.kartu-pas.destroy-all') }}">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDeleteAll()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 transition cursor-pointer">
                        <i class="fas fa-trash-alt"></i>
                        <span>Hapus Seluruh Data</span>
                    </button>
                </form>
            </div>
        </x-slot>

        <!-- Table Viewport with Persistent Loading Overlay (SPA Replaced via AJAX) -->
        <div class="relative">
            <!-- Shimmer Loading Overlay (persistent across SPA table updates) -->
            <div id="tableLoadingOverlay" class="absolute inset-0 bg-white/75 backdrop-blur-[2px] z-20 hidden items-center justify-center transition-all duration-200">
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-slate-900/90 text-white shadow-2xl text-xs font-bold border border-slate-700 backdrop-blur-md">
                    <i class="fas fa-circle-notch fa-spin text-blue-400 text-base"></i>
                    <span>Memperbarui data kartu PAS...</span>
                </div>
            </div>

            <!-- Table Container (SPA HTML injected here) -->
            <div id="tableContainer" class="transition-opacity duration-200">
                @include('administrator.kartu-pas.partials.table')
            </div>
        </div>

    </x-table-card>

    <!-- ======================================================================== -->
    {{-- REUSABLE MODALS (USING x-modal)                                        --}}
    <!-- ======================================================================== -->

    <!-- 1. MODAL IMPORT EXCEL -->
    <x-modal id="modalImportKartu" 
             title="Import Data Kartu PAS"
             subtitle="Upload file Excel (.xlsx / .xls) untuk import data massal"
             icon="fas fa-file-import"
             iconColor="bg-amber-50 text-amber-600 border-amber-200"
             maxWidth="max-w-2xl">
        <p class="text-xs text-slate-500 mb-4">
            Sistem otomatis memetakan kolom dari file Excel Anda. Pastikan susunan kolom sesuai dengan petunjuk berikut:
        </p>

        <!-- Format Info Box -->
        <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-3.5 mb-4">
            <p class="font-bold text-blue-800 text-[11px] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <i class="fas fa-circle-info"></i> Format Kolom Excel:
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs text-blue-950 font-medium">
                <div class="bg-white p-2 rounded-lg border border-blue-100">
                    <span class="block font-black text-blue-600">Kolom D</span> Nama Pemegang
                </div>
                <div class="bg-white p-2 rounded-lg border border-blue-100">
                    <span class="block font-black text-blue-600">Kolom E</span> No. Registrasi
                </div>
                <div class="bg-white p-2 rounded-lg border border-blue-100">
                    <span class="block font-black text-blue-600">Kolom F</span> Kode Area Akses
                </div>
                <div class="bg-white p-2 rounded-lg border border-blue-100">
                    <span class="block font-black text-blue-600">Kolom G</span> Jabatan
                </div>
                <div class="bg-white p-2 rounded-lg border border-blue-100">
                    <span class="block font-black text-blue-600">Kolom H</span> Masa Berlaku
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('administrator.import.kartu-pas') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih File Spreadsheet</label>
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-amber-500 transition cursor-pointer bg-slate-50 hover:bg-amber-50/20"
                     onclick="document.getElementById('import_file_input').click()">
                    <input type="file" name="file" id="import_file_input" accept=".xlsx,.xls"
                           class="hidden" onchange="showModalImportFileName(this)" required>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 mx-auto flex items-center justify-center text-xl mb-2">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <p class="text-slate-700 font-semibold text-xs">Klik di sini untuk memilih file Excel</p>
                    <p class="text-slate-400 text-[11px] mt-0.5">Format file yang didukung: .xlsx atau .xls (Maks. 10MB)</p>
                    <p id="modalImportFileName" class="text-emerald-600 text-xs mt-2.5 font-bold"></p>
                </div>
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalImportKartu')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-md shadow-amber-500/20 flex items-center gap-1.5 transition cursor-pointer">
                    <i class="fas fa-upload"></i>
                    <span>Proses Import</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- 2. MODAL TAMBAH KARTU PAS -->
    <x-modal id="modalTambahKartu"
             title="Tambah Kartu PAS Baru"
             subtitle="Isi rincian data pemegang dan masa berlaku kartu izin masuk"
             icon="fas fa-id-card"
             iconColor="bg-blue-50 text-blue-600 border-blue-200"
             maxWidth="max-w-3xl">
        <form id="formTambahKartu" method="POST" action="{{ route('administrator.kartu-pas.simpan') }}">
            @csrf
            <input type="hidden" name="_form_source" value="tambah">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Registrasi / No. Kartu <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambah_nomor_kartu" name="nomor_kartu" value="{{ old('_form_source') === 'tambah' ? old('nomor_kartu') : '' }}"
                           placeholder="Contoh: B.MPH.MKQ.000532"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 font-mono focus:ring-2 focus:ring-blue-500" required>
                    @if(old('_form_source') === 'tambah') @error('nomor_kartu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror @endif
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Pemegang <span class="text-rose-500">*</span></label>
                    <input type="text" id="tambah_nama_pemegang" name="nama_pemegang" value="{{ old('_form_source') === 'tambah' ? old('nama_pemegang') : '' }}"
                           placeholder="Nama lengkap sesuai identitas"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500" required>
                    @if(old('_form_source') === 'tambah') @error('nama_pemegang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror @endif
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1">Instansi / Perusahaan <span class="text-rose-500">*</span></label>
                <div class="relative searchable-dropdown-wrapper" id="containerTambahInstansi">
                    <select id="tambah_instansi_id" name="instansi_id" class="hidden" required>
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($instansiList as $instansi)
                            @php $sisa = $instansi->sisa_kuota; @endphp
                            <option value="{{ $instansi->id }}" {{ $sisa <= 0 ? 'disabled' : '' }}>
                                {{ $instansi->nama_instansi }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button" 
                            data-dropdown-target="dropdownTambahInstansi"
                            onclick="toggleSearchableDropdown('dropdownTambahInstansi')"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all shadow-2xs cursor-pointer flex items-center justify-between gap-2 hover:border-slate-300">
                        <div class="flex items-center gap-2 min-w-0 pr-1">
                            <i class="fas fa-building text-slate-400 text-xs shrink-0"></i>
                            <span class="dropdown-selected-label truncate font-medium text-slate-700">-- Pilih Instansi --</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 dropdown-chevron transition-transform duration-200"></i>
                    </button>

                    <div id="dropdownTambahInstansi" 
                         class="searchable-dropdown-menu absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200/90 rounded-2xl shadow-2xl z-50 p-2.5 hidden animate-dropdown-fade">
                        <div class="searchable-input-box mb-2">
                            <i class="fas fa-search searchable-search-icon"></i>
                            <input type="text" 
                                   oninput="filterSearchableOptions('dropdownTambahInstansi', this.value)"
                                   placeholder="Cari nama instansi..." 
                                   autocomplete="off"
                                   class="searchable-input"
                                   style="padding-left: 2.25rem !important; padding-right: 2rem !important;">
                            <button type="button" 
                                    onclick="clearSearchableInput('dropdownTambahInstansi')"
                                    class="searchable-clear-btn hidden"
                                    title="Hapus pencarian">
                                <i class="fas fa-times-circle text-xs"></i>
                            </button>
                        </div>
                        <div class="searchable-list-scroll space-y-0.5" id="listTambahInstansi">
                            @foreach($instansiList as $instansi)
                                @php $sisa = $instansi->sisa_kuota; $isHabis = ($sisa <= 0); @endphp
                                <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between transition {{ $isHabis ? 'opacity-50 cursor-not-allowed bg-slate-50/50' : 'cursor-pointer text-slate-700 hover:bg-slate-50' }}"
                                     data-value="{{ $instansi->id }}"
                                     data-search-text="{{ $instansi->nama_instansi }}"
                                     data-display-name="{{ $instansi->nama_instansi }}"
                                     @if(!$isHabis) onclick="selectSearchableOption('tambah_instansi_id', '{{ $instansi->id }}', '{{ addslashes($instansi->nama_instansi) }}', 'dropdownTambahInstansi')" @endif>
                                    <div class="flex items-center gap-2 min-w-0 pr-2">
                                        <i class="fas fa-building text-slate-400 text-xs shrink-0"></i>
                                        <span class="truncate">{{ $instansi->nama_instansi }}</span>
                                    </div>
                                    <div class="shrink-0 text-[10px]">
                                        @if($isHabis)
                                            <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 font-bold border border-rose-200/80">HABIS</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-medium">Sisa: {{ $sisa }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div class="searchable-empty py-4 text-center text-slate-400 text-xs hidden">
                                <i class="fas fa-search-minus mr-1"></i> Tidak ada instansi yang cocok
                            </div>
                        </div>
                    </div>
                </div>
                @if(old('_form_source') === 'tambah') @error('instansi_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <!-- Area Akses -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Hak Akses Area <span class="text-[11px] font-normal text-blue-600">(Pilih Area)</span>
                        </label>
                        <button type="button" onclick="openModal('modalAreaAkses')" 
                                class="text-[11px] font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <i class="fas fa-plus-circle"></i> Tambah Area
                        </button>
                    </div>
                    <div class="border border-slate-200 rounded-xl p-2.5 max-h-36 overflow-y-auto bg-slate-50/60 space-y-1.5 custom-scrollbar" id="container_area_tambah">
                        @foreach($areaAksesList as $area)
                            <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer text-xs font-medium text-slate-700 transition">
                                <input type="checkbox" name="area_akses[]" value="{{ $area->kode }}"
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 checkbox-area-tambah"
                                       {{ old('_form_source') === 'tambah' && is_array(old('area_akses')) && in_array($area->kode, old('area_akses')) ? 'checked' : '' }}>
                                <span class="font-bold text-blue-800 bg-blue-100 px-1.5 py-0.5 rounded text-[10px]">{{ $area->kode }}</span>
                                <span class="truncate">{{ $area->keterangan }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if(old('_form_source') === 'tambah') @error('area_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror @endif
                </div>

                <!-- Jabatan -->
                <!-- Jabatan (Searchable Dropdown) -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700">Jabatan</label>
                        <button type="button" onclick="openModal('modalJabatan')" 
                                class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-plus-circle"></i> Tambah Jabatan
                        </button>
                    </div>
                    <div class="flex gap-1.5 items-center">
                        <div class="relative searchable-dropdown-wrapper flex-1" id="containerTambahJabatan">
                            <!-- Native select hidden for form submission -->
                            <select id="select_jabatan" name="jabatan" class="hidden">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatanList as $jbt)
                                    <option value="{{ $jbt->nama_jabatan }}" data-id="{{ $jbt->id }}" {{ old('_form_source') === 'tambah' && old('jabatan') == $jbt->nama_jabatan ? 'selected' : '' }}>
                                        {{ $jbt->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Trigger Button -->
                            <button type="button" 
                                    data-dropdown-target="dropdownTambahJabatan"
                                    onclick="toggleSearchableDropdown('dropdownTambahJabatan')"
                                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 transition-all shadow-2xs cursor-pointer flex items-center justify-between gap-2 hover:border-slate-300">
                                <div class="flex items-center gap-2 min-w-0 pr-1">
                                    <i class="fas fa-briefcase text-slate-400 text-xs shrink-0"></i>
                                    <span class="dropdown-selected-label truncate font-medium text-slate-700">
                                        {{ old('_form_source') === 'tambah' && old('jabatan') ? old('jabatan') : '-- Pilih Jabatan --' }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 dropdown-chevron transition-transform duration-200"></i>
                            </button>

                            <!-- Dropdown Menu with Search -->
                            <div id="dropdownTambahJabatan" 
                                 class="searchable-dropdown-menu absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200/90 rounded-2xl shadow-2xl z-50 p-2.5 hidden animate-dropdown-fade">
                                <div class="searchable-input-box mb-2">
                                    <i class="fas fa-search searchable-search-icon"></i>
                                    <input type="text" 
                                           oninput="filterSearchableOptions('dropdownTambahJabatan', this.value)"
                                           placeholder="Cari nama jabatan..." 
                                           autocomplete="off"
                                           class="searchable-input"
                                           style="padding-left: 2.25rem !important; padding-right: 2rem !important;">
                                    <button type="button" 
                                            onclick="clearSearchableInput('dropdownTambahJabatan')"
                                            class="searchable-clear-btn hidden"
                                            title="Hapus pencarian">
                                        <i class="fas fa-times-circle text-xs"></i>
                                    </button>
                                </div>

                                <div class="searchable-list-scroll space-y-0.5" id="listTambahJabatan">
                                    <!-- Default Option -->
                                    <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50"
                                         data-value=""
                                         data-search-text="-- pilih jabatan -- kosong reset"
                                         data-display-name="-- Pilih Jabatan --"
                                         onclick="selectSearchableOption('select_jabatan', '', '-- Pilih Jabatan --', 'dropdownTambahJabatan')">
                                        <span class="text-slate-400 italic">-- Pilih Jabatan --</span>
                                    </div>

                                    @foreach($jabatanList as $jbt)
                                        @php $isJbtSelected = (old('_form_source') === 'tambah' && old('jabatan') == $jbt->nama_jabatan); @endphp
                                        <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition {{ $isJbtSelected ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                                             data-value="{{ $jbt->nama_jabatan }}"
                                             data-search-text="{{ $jbt->nama_jabatan }}"
                                             data-display-name="{{ $jbt->nama_jabatan }}"
                                             onclick="selectSearchableOption('select_jabatan', '{{ addslashes($jbt->nama_jabatan) }}', '{{ addslashes($jbt->nama_jabatan) }}', 'dropdownTambahJabatan')">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <i class="fas fa-briefcase text-slate-400 text-xs shrink-0"></i>
                                                <span class="truncate">{{ $jbt->nama_jabatan }}</span>
                                            </div>
                                            @if($isJbtSelected)
                                                <i class="fas fa-check text-blue-600 text-xs shrink-0 check-icon"></i>
                                            @endif
                                        </div>
                                    @endforeach

                                    <div class="searchable-empty py-4 text-center text-slate-400 text-xs hidden">
                                        <i class="fas fa-search-minus mr-1"></i> Tidak ada jabatan yang cocok
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="deleteSelectedJabatan()" 
                                class="px-2.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition shrink-0 cursor-pointer" 
                                title="Hapus Jabatan Terpilih">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                    @if(old('_form_source') === 'tambah') @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Terbit <span class="text-rose-500">*</span></label>
                    <input type="date" id="tambah_tanggal_terbit" name="tanggal_terbit" value="{{ old('_form_source') === 'tambah' ? old('tanggal_terbit', date('Y-m-d')) : date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 font-mono" required>
                    @if(old('_form_source') === 'tambah') @error('tanggal_terbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror @endif
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Berlaku / Kadaluarsa <span class="text-rose-500">*</span></label>
                    <input type="date" id="tambah_tanggal_berlaku" name="tanggal_berlaku" value="{{ old('_form_source') === 'tambah' ? old('tanggal_berlaku') : '' }}"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 font-mono" required>
                    @if(old('_form_source') === 'tambah') @error('tanggal_berlaku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror @endif
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalTambahKartu')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 flex items-center gap-1.5 transition cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Simpan Kartu PAS</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- 3. MODAL EDIT KARTU PAS -->
    <x-modal id="modalEditKartu"
             title="Edit Data Kartu PAS"
             subtitle="Perbarui identitas pemegang atau masa berlaku kartu izin masuk"
             icon="fas fa-pen-to-square"
             iconColor="bg-amber-50 text-amber-600 border-amber-200"
             maxWidth="max-w-3xl">
        <form id="formEditKartu" method="POST" action="">
            @csrf @method('PUT')
            <input type="hidden" name="_form_source" value="edit">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Registrasi</label>
                    <input type="text" id="edit_nomor_kartu" name="nomor_kartu" 
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 font-mono focus:ring-2 focus:ring-amber-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemegang</label>
                    <input type="text" id="edit_nama_pemegang" name="nama_pemegang" 
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1">Instansi / Perusahaan <span class="text-rose-500">*</span></label>
                <div class="relative searchable-dropdown-wrapper" id="containerEditInstansi">
                    <select id="edit_instansi_id" name="instansi_id" class="hidden" required>
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($instansiList as $instansi)
                            <option value="{{ $instansi->id }}">{{ $instansi->nama_instansi }}</option>
                        @endforeach
                    </select>

                    <button type="button" 
                            data-dropdown-target="dropdownEditInstansi"
                            onclick="toggleSearchableDropdown('dropdownEditInstansi')"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 transition-all shadow-2xs cursor-pointer flex items-center justify-between gap-2 hover:border-slate-300">
                        <div class="flex items-center gap-2 min-w-0 pr-1">
                            <i class="fas fa-building text-slate-400 text-xs shrink-0"></i>
                            <span class="dropdown-selected-label truncate font-medium text-slate-700">-- Pilih Instansi --</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 dropdown-chevron transition-transform duration-200"></i>
                    </button>

                    <div id="dropdownEditInstansi" 
                         class="searchable-dropdown-menu absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200/90 rounded-2xl shadow-2xl z-50 p-2.5 hidden animate-dropdown-fade">
                        <div class="searchable-input-box mb-2">
                            <i class="fas fa-search searchable-search-icon"></i>
                            <input type="text" 
                                   oninput="filterSearchableOptions('dropdownEditInstansi', this.value)"
                                   placeholder="Cari nama instansi..." 
                                   autocomplete="off"
                                   class="searchable-input"
                                   style="padding-left: 2.25rem !important; padding-right: 2rem !important;">
                            <button type="button" 
                                    onclick="clearSearchableInput('dropdownEditInstansi')"
                                    class="searchable-clear-btn hidden"
                                    title="Hapus pencarian">
                                <i class="fas fa-times-circle text-xs"></i>
                            </button>
                        </div>
                        <div class="searchable-list-scroll space-y-0.5" id="listEditInstansi">
                            @foreach($instansiList as $instansi)
                                <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50"
                                     data-value="{{ $instansi->id }}"
                                     data-search-text="{{ $instansi->nama_instansi }}"
                                     data-display-name="{{ $instansi->nama_instansi }}"
                                     onclick="selectSearchableOption('edit_instansi_id', '{{ $instansi->id }}', '{{ addslashes($instansi->nama_instansi) }}', 'dropdownEditInstansi')">
                                    <div class="flex items-center gap-2 min-w-0 pr-2">
                                        <i class="fas fa-building text-slate-400 text-xs shrink-0"></i>
                                        <span class="truncate">{{ $instansi->nama_instansi }}</span>
                                    </div>
                                </div>
                            @endforeach
                            <div class="searchable-empty py-4 text-center text-slate-400 text-xs hidden">
                                <i class="fas fa-search-minus mr-1"></i> Tidak ada instansi yang cocok
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Area Akses Terpilih</label>
                    <div class="border border-slate-200 rounded-xl p-2.5 max-h-36 overflow-y-auto bg-slate-50/60 space-y-1 custom-scrollbar" id="container_area_edit">
                        @foreach($areaAksesList as $area)
                            <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer text-xs font-medium text-slate-700 transition">
                                <input type="checkbox" name="area_akses[]" value="{{ $area->kode }}"
                                       class="rounded border-slate-300 text-amber-600 focus:ring-amber-500 checkbox-area-edit">
                                <span class="font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded text-[10px]">{{ $area->kode }}</span>
                                <span class="truncate">{{ $area->keterangan }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Jabatan (Searchable Dropdown) -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-700">Jabatan</label>
                        <button type="button" onclick="openModal('modalJabatan')" 
                                class="text-[11px] font-bold text-amber-600 hover:text-amber-800 flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-plus-circle"></i> Tambah Jabatan
                        </button>
                    </div>
                    <div class="relative searchable-dropdown-wrapper" id="containerEditJabatan">
                        <select id="edit_jabatan" name="jabatan" class="hidden">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatanList as $jbt)
                                <option value="{{ $jbt->nama_jabatan }}">{{ $jbt->nama_jabatan }}</option>
                            @endforeach
                        </select>

                        <button type="button" 
                                data-dropdown-target="dropdownEditJabatan"
                                onclick="toggleSearchableDropdown('dropdownEditJabatan')"
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-amber-500 transition-all shadow-2xs cursor-pointer flex items-center justify-between gap-2 hover:border-slate-300">
                            <div class="flex items-center gap-2 min-w-0 pr-1">
                                <i class="fas fa-briefcase text-slate-400 text-xs shrink-0"></i>
                                <span class="dropdown-selected-label truncate font-medium text-slate-700">-- Pilih Jabatan --</span>
                            </div>
                            <i class="fas fa-chevron-down text-slate-400 text-[10px] shrink-0 dropdown-chevron transition-transform duration-200"></i>
                        </button>

                        <div id="dropdownEditJabatan" 
                             class="searchable-dropdown-menu absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200/90 rounded-2xl shadow-2xl z-50 p-2.5 hidden animate-dropdown-fade">
                            <div class="searchable-input-box mb-2">
                                <i class="fas fa-search searchable-search-icon"></i>
                                <input type="text" 
                                       oninput="filterSearchableOptions('dropdownEditJabatan', this.value)"
                                       placeholder="Cari nama jabatan..." 
                                       autocomplete="off"
                                       class="searchable-input"
                                       style="padding-left: 2.25rem !important; padding-right: 2rem !important;">
                                <button type="button" 
                                        onclick="clearSearchableInput('dropdownEditJabatan')"
                                        class="searchable-clear-btn hidden"
                                        title="Hapus pencarian">
                                    <i class="fas fa-times-circle text-xs"></i>
                                </button>
                            </div>
                            <div class="searchable-list-scroll space-y-0.5" id="listEditJabatan">
                                <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50"
                                     data-value=""
                                     data-search-text="-- pilih jabatan -- kosong reset"
                                     data-display-name="-- Pilih Jabatan --"
                                     onclick="selectSearchableOption('edit_jabatan', '', '-- Pilih Jabatan --', 'dropdownEditJabatan')">
                                    <span class="text-slate-400 italic">-- Pilih Jabatan --</span>
                                </div>
                                @foreach($jabatanList as $jbt)
                                    <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50"
                                         data-value="{{ $jbt->nama_jabatan }}"
                                         data-search-text="{{ $jbt->nama_jabatan }}"
                                         data-display-name="{{ $jbt->nama_jabatan }}"
                                         onclick="selectSearchableOption('edit_jabatan', '{{ addslashes($jbt->nama_jabatan) }}', '{{ addslashes($jbt->nama_jabatan) }}', 'dropdownEditJabatan')">
                                        <div class="flex items-center gap-2 min-w-0 pr-2">
                                            <i class="fas fa-briefcase text-slate-400 text-xs shrink-0"></i>
                                            <span class="truncate">{{ $jbt->nama_jabatan }}</span>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="searchable-empty py-4 text-center text-slate-400 text-xs hidden">
                                    <i class="fas fa-search-minus mr-1"></i> Tidak ada jabatan yang cocok
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Terbit <span class="text-rose-500">*</span></label>
                    <input type="date" id="edit_tanggal_terbit" name="tanggal_terbit" 
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 font-mono focus:ring-2 focus:ring-amber-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Berlaku <span class="text-rose-500">*</span></label>
                    <input type="date" id="edit_tanggal_berlaku" name="tanggal_berlaku" 
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 font-mono focus:ring-2 focus:ring-amber-500" required>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditKartu')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-md shadow-amber-500/20 flex items-center gap-1.5 transition cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- 4. MODAL PERPANJANGAN KARTU PAS -->
    <x-modal id="modalPerpanjanganKartu"
             title="Perpanjang Masa Berlaku Kartu"
             subtitle="Perpanjang tanggal kadaluarsa kartu izin PAS"
             icon="fas fa-calendar-plus"
             iconColor="bg-emerald-50 text-emerald-600 border-emerald-200"
             maxWidth="max-w-lg">
        <!-- Kartu Ringkasan Data -->
        <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4 mb-4 text-xs space-y-1.5">
            <div class="flex justify-between">
                <span class="text-slate-500">Nomor Registrasi:</span>
                <span id="perp_nomor_display" class="font-bold font-mono text-slate-800"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Nama Pemegang:</span>
                <span id="perp_nama_display" class="font-bold text-slate-800"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Instansi:</span>
                <span id="perp_instansi_display" class="font-medium text-slate-700"></span>
            </div>
            <div class="flex justify-between border-t border-emerald-200/60 pt-2 mt-2">
                <span class="text-slate-500">Kadaluarsa Saat Ini:</span>
                <span id="perp_lama_display" class="font-bold text-rose-600 font-mono"></span>
            </div>
        </div>

        <form id="formPerpanjanganKartu" method="POST" action="">
            @csrf @method('PUT')

            <!-- Hidden Input Fields -->
            <input type="hidden" id="perp_nomor_kartu" name="nomor_kartu">
            <input type="hidden" id="perp_email" name="email">
            <input type="hidden" id="perp_nama_pemegang" name="nama_pemegang">
            <input type="hidden" id="perp_instansi_id" name="instansi_id">
            <input type="hidden" id="perp_area_akses" name="area_akses">
            <input type="hidden" id="perp_jabatan" name="jabatan">
            <input type="hidden" id="perp_tanggal_terbit" name="tanggal_terbit">
            <input type="hidden" name="status" value="aktif">

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1">Masa Berlaku Baru (Tanggal Kadaluarsa) <span class="text-rose-500">*</span></label>
                <input type="date" id="perp_tanggal_berlaku" name="tanggal_berlaku" 
                       class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 font-mono focus:ring-2 focus:ring-emerald-500" required>
                
                <!-- Tombol Shortcut Tambah Waktu -->
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="setPerpanjangTahun(1)" 
                            class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                        + 1 Tahun
                    </button>
                    <button type="button" onclick="setPerpanjangTahun(2)" 
                            class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition">
                        + 2 Tahun
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalPerpanjanganKartu')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Simpan Perpanjangan</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- 5. MODAL AREA AKSES -->
    <x-modal id="modalAreaAkses"
             title="Kelola & Tambah Area Akses"
             subtitle="Daftar zona / area terbatas bandara yang dapat diakses"
             icon="fas fa-layer-group"
             iconColor="bg-blue-50 text-blue-600 border-blue-200"
             maxWidth="max-w-lg"
             zIndex="z-[70]">
        <form id="formAreaAkses" onsubmit="submitAreaAkses(event)" class="mb-4">
            <div class="grid grid-cols-3 gap-2 mb-2">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Kode Area</label>
                    <input type="text" id="modal_kode_akses" 
                           class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl text-xs text-slate-800 font-bold uppercase focus:ring-2 focus:ring-blue-500" 
                           placeholder="Contoh: W" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Keterangan Wilayah Area</label>
                    <input type="text" id="modal_keterangan_akses" 
                           class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-blue-500" 
                           placeholder="Contoh: Ruang VIP / Apron" required>
                </div>
            </div>
            <div id="areaAksesError" class="text-rose-600 text-[11px] mb-2 hidden font-semibold"></div>
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Area
                </button>
            </div>
        </form>

        <hr class="border-slate-100 my-3">

        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Daftar Area Terdaftar</h4>
        <div class="overflow-y-auto max-h-48 border border-slate-200 rounded-xl p-2 space-y-1.5 custom-scrollbar" id="modal_area_list">
            @foreach($areaAksesList as $area)
                <div class="flex items-center justify-between bg-slate-50 hover:bg-slate-100/80 px-3 py-1.5 rounded-lg border border-slate-200/70 text-xs transition" id="item-area-{{ $area->id }}">
                    <span class="font-semibold text-slate-800">
                        <strong class="text-blue-600 font-bold">{{ $area->kode }}:</strong> {{ $area->keterangan }}
                    </span>
                    <button type="button" onclick="deleteAreaById({{ $area->id }}, '{{ $area->kode }}')" 
                            class="text-rose-500 hover:text-rose-700 p-1 text-xs" title="Hapus Area">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end pt-3 border-t border-slate-100 mt-4">
            <button type="button" onclick="closeModal('modalAreaAkses')" 
                    class="px-4 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                Selesai
            </button>
        </div>
    </x-modal>

    <!-- 6. MODAL JABATAN -->
    <x-modal id="modalJabatan"
             title="Kelola & Tambah Jabatan"
             subtitle="Daftar referensi posisi/jabatan personil penerima kartu PAS"
             icon="fas fa-briefcase"
             iconColor="bg-emerald-50 text-emerald-600 border-emerald-200"
             maxWidth="max-w-lg"
             zIndex="z-[70]">
        <form id="formJabatan" onsubmit="submitJabatan(event)" class="mb-4">
            <div class="mb-2">
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Jabatan Baru</label>
                <input type="text" id="modal_nama_jabatan" 
                       class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500" 
                       placeholder="Contoh: Aviation Security Officer" required>
            </div>
            <div id="jabatanError" class="text-rose-600 text-[11px] mb-2 hidden font-semibold"></div>
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                    <i class="fas fa-plus mr-1"></i> Tambah Jabatan
                </button>
            </div>
        </form>

        <hr class="border-slate-100 my-3">

        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Daftar Jabatan Terdaftar</h4>
        <div class="overflow-y-auto max-h-48 border border-slate-200 rounded-xl p-2 space-y-1.5 custom-scrollbar" id="modal_jabatan_list">
            @foreach($jabatanList as $jbt)
                <div class="flex items-center justify-between bg-slate-50 hover:bg-slate-100/80 px-3 py-1.5 rounded-lg border border-slate-200/70 text-xs transition" id="item-jabatan-{{ $jbt->id }}">
                    <span class="font-semibold text-slate-800">{{ $jbt->nama_jabatan }}</span>
                    <button type="button" onclick="deleteJabatanById({{ $jbt->id }}, '{{ $jbt->nama_jabatan }}')" 
                            class="text-rose-500 hover:text-rose-700 p-1 text-xs" title="Hapus Jabatan">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end pt-3 border-t border-slate-100 mt-4">
            <button type="button" onclick="closeModal('modalJabatan')" 
                    class="px-4 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                Selesai
            </button>
        </div>
    </x-modal>

    <!-- 7. MODAL NONAKTIFKAN KARTU PAS -->
    <x-modal id="modalNonaktifkanKartu"
             title="Nonaktifkan Kartu PAS"
             subtitle="Nonaktifkan kartu pemegang yang pensiun / resign untuk membebaskan kuota"
             icon="fas fa-user-slash"
             iconColor="bg-rose-50 text-rose-600 border-rose-200"
             maxWidth="max-w-md">
        <form id="formNonaktifkanKartu" method="POST" action="">
            @csrf
            <div class="p-3 bg-amber-50 border border-amber-200/80 rounded-xl mb-4 text-xs text-amber-900 leading-relaxed">
                <div class="flex items-start gap-2.5">
                    <i class="fas fa-circle-exclamation text-amber-600 mt-0.5 shrink-0 text-sm"></i>
                    <div>
                        Menonaktifkan kartu untuk <span class="font-bold text-slate-900" id="nonaktif_nama_pemegang">-</span> (<span class="font-mono font-bold text-slate-900" id="nonaktif_nomor_kartu">-</span>) dari <span class="font-bold text-slate-900" id="nonaktif_instansi">-</span> akan mengubah status menjadi <strong>Tidak Aktif</strong> dan <strong>mengembalikan 1 slot kuota</strong> ke instansi tersebut.
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Alasan Penonaktifan <span class="text-rose-500">*</span>
                </label>
                <select name="keterangan_nonaktif" id="nonaktif_keterangan" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-rose-500" required>
                    <option value="">-- Pilih Alasan --</option>
                    <option value="resign">Resign / Berhenti Bekerja</option>
                    <option value="pensiun">Pensiun / Masa Tugas Selesai</option>
                    <option value="meninggal">Meninggal Dunia</option>
                    <option value="lainnya">Lainnya / Tidak Digunakan</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Catatan Tambahan (Opsional)
                </label>
                <input type="text" name="catatan_nonaktif" id="nonaktif_catatan"
                       placeholder="Contoh: SK Pensiun No. 042 / Pindah ke unit luar"
                       class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-rose-500">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalNonaktifkanKartu')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/20 flex items-center gap-1.5 transition cursor-pointer">
                    <i class="fas fa-user-slash"></i>
                    <span>Nonaktifkan Kartu</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- 8. MODAL AKTIFKAN KEMBALI KARTU PAS -->
    <x-modal id="modalAktifkanKembaliKartu"
             title="Aktifkan Kembali Kartu PAS"
             subtitle="Konfirmasi pengaktifan kembali kartu yang sebelumnya dinonaktifkan"
             icon="fas fa-user-check"
             iconColor="bg-emerald-50 text-emerald-600 border-emerald-200"
             maxWidth="max-w-md">
        <form id="formAktifkanKembaliKartu" method="POST" action="">
            @csrf
            
            <div class="text-center pt-1 pb-2">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 ring-4 ring-emerald-50/60 flex items-center justify-center mx-auto mb-3 shadow-inner text-xl">
                    <i class="fas fa-id-card-clip"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">
                    Aktifkan kembali kartu PAS ini?
                </h4>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Kartu akan dipulihkan statusnya menjadi aktif (atau kadaluarsa jika masa berlaku sudah lewat).
                </p>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 mt-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700">
                    <span class="font-mono font-bold text-slate-900" id="reaktif_nomor_kartu">-</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="font-bold text-slate-900" id="reaktif_nama_pemegang">-</span>
                </div>
            </div>

            <!-- Warning Notice Box -->
            <div class="p-3 bg-amber-50 border border-amber-200/80 rounded-xl my-4 text-xs text-amber-900 leading-relaxed">
                <div class="flex items-start gap-2.5">
                    <i class="fas fa-circle-exclamation text-amber-600 mt-0.5 shrink-0 text-sm"></i>
                    <div>
                        <strong class="font-bold text-amber-800">Perhatian:</strong> Kuota instansi terkait akan kembali <strong>terpakai 1 slot</strong> jika kuota masih tersedia.
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalAktifkanKembaliKartu')"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition cursor-pointer">
                    <i class="fas fa-user-check"></i>
                    <span>Ya, Aktifkan Kembali</span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- ======================================================================== -->
    <!-- SPA JAVASCRIPT & EVENT HANDLERS                                          -->
    <!-- ======================================================================== -->
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
                const openModals = document.querySelectorAll('[id^="modal"]:not(.hidden)');
                openModals.forEach(m => closeModal(m.id));
            }
        });

        // Dropdown Unduh Data Handler
        function toggleDownloadDropdown(e) {
            e.stopPropagation();
            document.getElementById('downloadDropdownMenu').classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('downloadDropdownContainer');
            const menu = document.getElementById('downloadDropdownMenu');
            if (menu && !menu.classList.contains('hidden') && container && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        function showModalImportFileName(input) {
            const fileName = input.files[0]?.name ?? '';
            document.getElementById('modalImportFileName').textContent = fileName ? '✅ File Dipilih: ' + fileName : '';
        }

        // ---------------------------------------------------------------------
        // SPA Table AJAX Navigation & Filter System
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

                // Sync export links with current query string
                syncExportLinks(url);

                // Reset selection state on page change
                updateSelectedCount();
            } catch (err) {
                console.error('SPA Navigation Error:', err);
                window.location.href = url;
            } finally {
                hideTableLoading();
            }
        }

        function syncExportLinks(url) {
            try {
                const currentUrl = new URL(url, window.location.origin);
                const excelBtn   = document.getElementById('btnExportExcel');
                const pdfBtn     = document.getElementById('btnExportPdf');
                if (excelBtn) {
                    const excelUrl = new URL("{{ route('administrator.kartu-pas.export.excel') }}", window.location.origin);
                    currentUrl.searchParams.forEach((val, key) => excelUrl.searchParams.set(key, val));
                    excelBtn.href = excelUrl.toString();
                }
                if (pdfBtn) {
                    const pdfUrl = new URL("{{ route('administrator.kartu-pas.export.pdf') }}", window.location.origin);
                    currentUrl.searchParams.forEach((val, key) => pdfUrl.searchParams.set(key, val));
                    pdfBtn.href = pdfUrl.toString();
                }
            } catch(e) {}
        }

        // Intercept all SPA pagination links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[data-spa="true"]');
            if (link && link.href) {
                e.preventDefault();
                loadTableData(link.href, true);
            }
        });

        // History Back / Forward navigation
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
                    if (val) url.searchParams.set(key, val);
                }
                loadTableData(url.toString(), true);
            });
        }

        // Search Input Debounce (350ms)
        let searchDebounceTimer;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    if (filterForm) filterForm.dispatchEvent(new Event('submit'));
                }, 350);
            });
        }

        // Instant Filter dropdown change
        document.querySelectorAll('#filterForm select').forEach(select => {
            select.addEventListener('change', () => {
                if (filterForm) filterForm.dispatchEvent(new Event('submit'));
            });
        });

        function clearSearchInput() {
            if (searchInput) {
                searchInput.value = '';
                if (filterForm) filterForm.dispatchEvent(new Event('submit'));
            }
        }

        function resetFilter(e) {
            if (e) e.preventDefault();
            if (filterForm) {
                filterForm.reset();
                const filterLabel = document.querySelector('[data-dropdown-target="dropdownFilterInstansi"] .dropdown-selected-label');
                if (filterLabel) filterLabel.textContent = 'Semua Instansi';
                if (document.getElementById('dropdownFilterInstansi')) {
                    document.querySelectorAll('#dropdownFilterInstansi .searchable-option').forEach(opt => {
                        const isAll = opt.getAttribute('data-value') === '';
                        if (isAll) {
                            opt.classList.add('bg-blue-50', 'text-blue-700', 'font-bold');
                            if (!opt.querySelector('.check-icon')) {
                                const c = document.createElement('i');
                                c.className = 'fas fa-check text-blue-600 text-xs shrink-0 check-icon';
                                opt.appendChild(c);
                            }
                        } else {
                            opt.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
                            const c = opt.querySelector('.check-icon');
                            if (c) c.remove();
                        }
                    });
                }
                const baseUrl = "{{ route('administrator.kartu-pas.index') }}";
                loadTableData(baseUrl, true);
            }
        }

        // ---------------------------------------------------------------------
        // Checkbox & Bulk Actions
        // ---------------------------------------------------------------------
        function toggleCheckAll(source) {
            const checkboxes = document.querySelectorAll('.kartu-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.kartu-checkbox:checked');
            const total    = document.querySelectorAll('.kartu-checkbox');
            const badge    = document.getElementById('selectedCountBadge');
            const btnDel   = document.getElementById('btnDeleteSelected');
            const checkAll = document.getElementById('checkAll');

            if (checkAll && total.length > 0) {
                checkAll.checked = selected.length === total.length;
            }

            if (selected.length > 0) {
                if (badge) {
                    badge.textContent = `${selected.length} dipilih`;
                    badge.classList.remove('hidden');
                }
                if (btnDel) {
                    btnDel.classList.remove('hidden');
                    btnDel.classList.add('inline-flex');
                }
            } else {
                if (badge) badge.classList.add('hidden');
                if (btnDel) {
                    btnDel.classList.add('hidden');
                    btnDel.classList.remove('inline-flex');
                }
            }
        }

        function confirmDeleteSelected() {
            const selected = document.querySelectorAll('.kartu-checkbox:checked');
            if (selected.length === 0) return;

            const extraInputs = [];
            selected.forEach(cb => {
                extraInputs.push({ name: 'ids[]', value: cb.value });
            });

            openDeleteModal({
                action: "{{ route('administrator.kartu-pas.destroy-selected') }}",
                title: 'Hapus Kartu Terpilih?',
                message: `Anda akan menghapus ${selected.length} data kartu PAS yang dipilih. Tindakan ini tidak dapat dibatalkan!`,
                targetName: `${selected.length} Kartu Terpilih`,
                btnText: 'Hapus Terpilih',
                extraInputs: extraInputs
            });
        }

        function confirmDeleteAll() {
            openDeleteModal({
                action: "{{ route('administrator.kartu-pas.destroy-all') }}",
                title: 'Hapus Seluruh Data Kartu PAS?',
                message: 'PERINGATAN KRITIS: Seluruh data kartu PAS pada basis data akan dihapus permanen!',
                targetName: 'SEMUA DATA KARTU PAS',
                btnText: 'Kosongkan Seluruh Data'
            });
        }

        function confirmSingleDelete(id, nomorKartu) {
            openDeleteModal({
                action: `{{ url('/administrator/kartu-pas') }}/${id}`,
                title: 'Hapus Data Kartu PAS?',
                message: 'Apakah Anda yakin ingin menghapus data kartu izin masuk ini?',
                targetName: `No. Registrasi: ${nomorKartu}`,
                btnText: 'Ya, Hapus Kartu'
            });
        }

        // ---------------------------------------------------------------------
        // Modal Tambah, Edit & Perpanjangan Handlers
        // ---------------------------------------------------------------------
        function openModalTambahKartu() {
            const form = document.getElementById('formTambahKartu');
            if (form) {
                form.reset();
                document.querySelectorAll('#container_area_tambah .checkbox-area-tambah').forEach(cb => cb.checked = false);
                const selectJabatan = document.getElementById('select_jabatan');
                if (selectJabatan) selectJabatan.value = '';
                const dateInput = document.getElementById('tambah_tanggal_terbit');
                if (dateInput) dateInput.value = new Date().toISOString().substring(0, 10);
                const expInput = document.getElementById('tambah_tanggal_berlaku');
                if (expInput) {
                    let nextYear = new Date();
                    nextYear.setFullYear(nextYear.getFullYear() + 1);
                    expInput.value = nextYear.toISOString().substring(0, 10);
                }

                // Reset Instansi Searchable Dropdown
                document.getElementById('tambah_instansi_id').value = '';
                const tambahLabel = document.querySelector('[data-dropdown-target="dropdownTambahInstansi"] .dropdown-selected-label');
                if (tambahLabel) tambahLabel.textContent = '-- Pilih Instansi --';
                if (document.getElementById('dropdownTambahInstansi')) {
                    document.querySelectorAll('#dropdownTambahInstansi .searchable-option').forEach(opt => {
                        opt.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
                        const c = opt.querySelector('.check-icon');
                        if (c) c.remove();
                    });
                }

                // Reset Jabatan Searchable Dropdown
                const selJabatan = document.getElementById('select_jabatan');
                if (selJabatan) selJabatan.value = '';
                const tambahJabLabel = document.querySelector('[data-dropdown-target="dropdownTambahJabatan"] .dropdown-selected-label');
                if (tambahJabLabel) tambahJabLabel.textContent = '-- Pilih Jabatan --';
                if (document.getElementById('dropdownTambahJabatan')) {
                    document.querySelectorAll('#dropdownTambahJabatan .searchable-option').forEach(opt => {
                        opt.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
                        const c = opt.querySelector('.check-icon');
                        if (c) c.remove();
                    });
                }
            }
            openModal('modalTambahKartu');
        }

        function openModalEditKartuFromBtn(btn) {
            try {
                const kartu = JSON.parse(btn.getAttribute('data-kartu'));
                openModalEditKartu(kartu);
            } catch(e) {
                console.error('Gagal membaca data kartu:', e);
            }
        }

        function openModalPerpanjanganFromBtn(btn) {
            try {
                const kartu = JSON.parse(btn.getAttribute('data-kartu'));
                openModalPerpanjangan(kartu);
            } catch(e) {
                console.error('Gagal membaca data kartu:', e);
            }
        }

        function openModalEditKartu(kartu) {
            const form = document.getElementById('formEditKartu');
            form.action = "{{ url('/administrator/kartu-pas') }}/" + kartu.id;

            document.getElementById('edit_nomor_kartu').value   = kartu.nomor_kartu || '';
            document.getElementById('edit_nama_pemegang').value = kartu.nama_pemegang || '';

            let editInstId = kartu.instansi_id || '';
            if (!editInstId && kartu.perusahaan) {
                const matched = (@json($instansiList) || []).find(i => (i.nama_instansi || '').trim().toLowerCase() === (kartu.perusahaan || '').trim().toLowerCase());
                if (matched) editInstId = matched.id;
            }
            document.getElementById('edit_instansi_id').value = editInstId;
            const editLabel = document.querySelector('[data-dropdown-target="dropdownEditInstansi"] .dropdown-selected-label');
            if (editLabel) {
                const matchingOpt = document.querySelector(`#dropdownEditInstansi .searchable-option[data-value="${editInstId}"]`);
                editLabel.textContent = matchingOpt ? matchingOpt.getAttribute('data-display-name') : (kartu.perusahaan || '-- Pilih Instansi --');
            }
            if (document.getElementById('dropdownEditInstansi')) {
                document.querySelectorAll('#dropdownEditInstansi .searchable-option').forEach(opt => {
                    const isSelected = String(opt.getAttribute('data-value')) === String(editInstId);
                    if (isSelected) {
                        opt.classList.add('bg-blue-50', 'text-blue-700', 'font-bold');
                        if (!opt.querySelector('.check-icon')) {
                            const c = document.createElement('i');
                            c.className = 'fas fa-check text-blue-600 text-xs shrink-0 check-icon';
                            opt.appendChild(c);
                        }
                    } else {
                        opt.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
                        const c = opt.querySelector('.check-icon');
                        if (c) c.remove();
                    }
                });
            }
            
            let selectedAreas = [];
            let rawArea = kartu.area_akses || '';
            if (!rawArea.includes(',') && rawArea.replace(/[^A-Za-z0-9]/g, '').length > 1) {
                selectedAreas = rawArea.replace(/[^A-Za-z0-9]/g, '').toUpperCase().split('');
            } else {
                selectedAreas = rawArea.split(',').map(s => s.trim().toUpperCase()).filter(Boolean);
            }
            document.querySelectorAll('#container_area_edit .checkbox-area-edit').forEach(cb => {
                cb.checked = selectedAreas.includes(cb.value.toUpperCase());
            });

            // Sinkronkan Jabatan Searchable Dropdown di modal edit
            const editJabatanVal = kartu.jabatan || '';
            const selectJabatan = document.getElementById('edit_jabatan');
            if (editJabatanVal) {
                const hasOpt = Array.from(selectJabatan.options).some(o => o.value.trim().toLowerCase() === editJabatanVal.trim().toLowerCase());
                if (!hasOpt) {
                    const opt = new Option(editJabatanVal, editJabatanVal, true, true);
                    selectJabatan.add(opt);

                    // Juga tambahkan option ke searchable list dropdown
                    const listEditJab = document.getElementById('listEditJabatan');
                    if (listEditJab) {
                        const optDiv = document.createElement('div');
                        optDiv.className = 'searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50';
                        optDiv.setAttribute('data-value', editJabatanVal);
                        optDiv.setAttribute('data-search-text', editJabatanVal);
                        optDiv.setAttribute('data-display-name', editJabatanVal);
                        optDiv.onclick = function() {
                            selectSearchableOption('edit_jabatan', editJabatanVal, editJabatanVal, 'dropdownEditJabatan');
                        };
                        optDiv.innerHTML = `
                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                <i class="fas fa-briefcase text-slate-400 text-xs shrink-0"></i>
                                <span class="truncate">${editJabatanVal}</span>
                            </div>
                        `;
                        listEditJab.appendChild(optDiv);
                    }
                }
                selectJabatan.value = editJabatanVal;
            } else {
                selectJabatan.value = '';
            }

            const editJabLabel = document.querySelector('[data-dropdown-target="dropdownEditJabatan"] .dropdown-selected-label');
            if (editJabLabel) {
                editJabLabel.textContent = editJabatanVal || '-- Pilih Jabatan --';
            }
            if (document.getElementById('dropdownEditJabatan')) {
                document.querySelectorAll('#dropdownEditJabatan .searchable-option').forEach(opt => {
                    const isSelected = String(opt.getAttribute('data-value')) === String(editJabatanVal);
                    if (isSelected) {
                        opt.classList.add('bg-blue-50', 'text-blue-700', 'font-bold');
                        if (!opt.querySelector('.check-icon')) {
                            const c = document.createElement('i');
                            c.className = 'fas fa-check text-blue-600 text-xs shrink-0 check-icon';
                            opt.appendChild(c);
                        }
                    } else {
                        opt.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
                        const c = opt.querySelector('.check-icon');
                        if (c) c.remove();
                    }
                });
            }
            
            if (kartu.tanggal_terbit) {
                document.getElementById('edit_tanggal_terbit').value = kartu.tanggal_terbit.substring(0, 10);
            }
            if (kartu.tanggal_berlaku) {
                document.getElementById('edit_tanggal_berlaku').value = kartu.tanggal_berlaku.substring(0, 10);
            }

            openModal('modalEditKartu');
        }

        function openModalPerpanjangan(kartu) {
            const form = document.getElementById('formPerpanjanganKartu');
            form.action = "{{ url('/administrator/kartu-pas') }}/" + kartu.id;

            document.getElementById('perp_nomor_display').textContent   = kartu.nomor_kartu || '-';
            document.getElementById('perp_nama_display').textContent    = kartu.nama_pemegang || '-';
            document.getElementById('perp_instansi_display').textContent= kartu.perusahaan || '-';
            document.getElementById('perp_lama_display').textContent    = kartu.tanggal_berlaku ? kartu.tanggal_berlaku.substring(0, 10) : '-';

            document.getElementById('perp_nomor_kartu').value    = kartu.nomor_kartu || '';
            document.getElementById('perp_email').value          = kartu.email || '';
            document.getElementById('perp_nama_pemegang').value  = kartu.nama_pemegang || '';
            document.getElementById('perp_instansi_id').value    = kartu.instansi_id || '';
            document.getElementById('perp_area_akses').value     = kartu.area_akses || '';
            document.getElementById('perp_jabatan').value        = kartu.jabatan || '';
            document.getElementById('perp_tanggal_terbit').value = kartu.tanggal_terbit ? kartu.tanggal_terbit.substring(0, 10) : '';

            // Default: 1 tahun dari hari ini atau tanggal berlaku
            let baseDate = new Date();
            if (kartu.tanggal_berlaku) {
                const currentExp = new Date(kartu.tanggal_berlaku);
                if (currentExp > baseDate) baseDate = currentExp;
            }
            baseDate.setFullYear(baseDate.getFullYear() + 1);
            document.getElementById('perp_tanggal_berlaku').value = baseDate.toISOString().substring(0, 10);

            openModal('modalPerpanjanganKartu');
        }

        function setPerpanjangTahun(years) {
            const input = document.getElementById('perp_tanggal_berlaku');
            let d = new Date();
            d.setFullYear(d.getFullYear() + years);
            input.value = d.toISOString().substring(0, 10);
        }

        function openModalNonaktifkan(id, nomorKartu, namaPemegang, instansi) {
            const form = document.getElementById('formNonaktifkanKartu');
            form.action = "{{ url('/administrator/kartu-pas') }}/" + id + "/nonaktifkan";

            document.getElementById('nonaktif_nama_pemegang').textContent = namaPemegang || '-';
            document.getElementById('nonaktif_nomor_kartu').textContent  = nomorKartu || '-';
            document.getElementById('nonaktif_instansi').textContent     = instansi || '-';
            document.getElementById('nonaktif_keterangan').value         = '';
            document.getElementById('nonaktif_catatan').value            = '';

            openModal('modalNonaktifkanKartu');
        }

        function confirmReaktifkan(id, nomorKartu, namaPemegang) {
            const form = document.getElementById('formAktifkanKembaliKartu');
            form.action = "{{ url('/administrator/kartu-pas') }}/" + id + "/aktifkan";
            document.getElementById('reaktif_nomor_kartu').textContent = nomorKartu || '-';
            document.getElementById('reaktif_nama_pemegang').textContent = namaPemegang || '-';
            openModal('modalAktifkanKembaliKartu');
        }

        // ---------------------------------------------------------------------
        // Area Akses & Jabatan AJAX Handlers
        // ---------------------------------------------------------------------
        async function submitAreaAkses(e) {
            e.preventDefault();
            const kode = document.getElementById('modal_kode_akses').value.trim();
            const keterangan = document.getElementById('modal_keterangan_akses').value.trim();
            const err  = document.getElementById('areaAksesError');
            err.classList.add('hidden');

            try {
                const res = await fetch("{{ route('administrator.area-akses.store-ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ kode, keterangan })
                });

                const data = await res.json();
                if (data.success) {
                    // Append to list
                    const list = document.getElementById('modal_area_list');
                    const div = document.createElement('div');
                    div.id = `item-area-${data.data.id}`;
                    div.className = 'flex items-center justify-between bg-slate-50 hover:bg-slate-100/80 px-3 py-1.5 rounded-lg border border-slate-200/70 text-xs transition';
                    div.innerHTML = `
                        <span class="font-semibold text-slate-800">
                            <strong class="text-blue-600 font-bold">${data.data.kode}:</strong> ${data.data.keterangan}
                        </span>
                        <button type="button" onclick="deleteAreaById(${data.data.id}, '${data.data.kode}')" class="text-rose-500 hover:text-rose-700 p-1 text-xs">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                    list.appendChild(div);

                    // Append to form checkboxes
                    ['container_area_tambah', 'container_area_edit'].forEach(contId => {
                        const cont = document.getElementById(contId);
                        if (cont) {
                            const lbl = document.createElement('label');
                            lbl.className = 'flex items-center gap-2 p-1.5 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer text-xs font-medium text-slate-700 transition';
                            lbl.innerHTML = `
                                <input type="checkbox" name="area_akses[]" value="${data.data.kode}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="font-bold text-blue-800 bg-blue-100 px-1.5 py-0.5 rounded text-[10px]">${data.data.kode}</span>
                                <span class="truncate">${data.data.keterangan}</span>
                            `;
                            cont.appendChild(lbl);
                        }
                    });

                    document.getElementById('modal_kode_akses').value = '';
                    document.getElementById('modal_keterangan_akses').value = '';
                } else {
                    err.textContent = data.message || 'Gagal menambahkan area';
                    err.classList.remove('hidden');
                }
            } catch (error) {
                err.textContent = 'Terjadi kesalahan sistem';
                err.classList.remove('hidden');
            }
        }

        async function deleteAreaById(id, kode) {
            if (!confirm(`Hapus area ${kode}?`)) return;

            try {
                const res = await fetch("{{ route('administrator.area-akses.delete-ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                if (data.success) {
                    const item = document.getElementById(`item-area-${id}`);
                    if (item) item.remove();
                }
            } catch(e) {}
        }

        async function submitJabatan(e) {
            e.preventDefault();
            const nama_jabatan = document.getElementById('modal_nama_jabatan').value.trim();
            const err  = document.getElementById('jabatanError');
            err.classList.add('hidden');

            try {
                const res = await fetch("{{ route('administrator.jabatan.store-ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ nama_jabatan })
                });

                const data = await res.json();
                if (data.success) {
                    const list = document.getElementById('modal_jabatan_list');
                    const div = document.createElement('div');
                    div.id = `item-jabatan-${data.data.id}`;
                    div.className = 'flex items-center justify-between bg-slate-50 hover:bg-slate-100/80 px-3 py-1.5 rounded-lg border border-slate-200/70 text-xs transition';
                    div.innerHTML = `
                        <span class="font-semibold text-slate-800">${data.data.nama_jabatan}</span>
                        <button type="button" onclick="deleteJabatanById(${data.data.id}, '${data.data.nama_jabatan}')" class="text-rose-500 hover:text-rose-700 p-1 text-xs">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                    list.appendChild(div);

                    // Add option to hidden selects
                    ['select_jabatan', 'edit_jabatan'].forEach(selId => {
                        const sel = document.getElementById(selId);
                        if (sel) {
                            const opt = document.createElement('option');
                            opt.value = data.data.nama_jabatan;
                            opt.textContent = data.data.nama_jabatan;
                            opt.dataset.id = data.data.id;
                            sel.appendChild(opt);
                        }
                    });

                    // Add option to searchable dropdown lists
                    [
                        { listId: 'listTambahJabatan', selectId: 'select_jabatan', dropdownId: 'dropdownTambahJabatan' },
                        { listId: 'listEditJabatan', selectId: 'edit_jabatan', dropdownId: 'dropdownEditJabatan' }
                    ].forEach(cfg => {
                        const list = document.getElementById(cfg.listId);
                        if (list) {
                            const optDiv = document.createElement('div');
                            optDiv.className = 'searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50';
                            optDiv.setAttribute('data-value', data.data.nama_jabatan);
                            optDiv.setAttribute('data-search-text', data.data.nama_jabatan);
                            optDiv.setAttribute('data-display-name', data.data.nama_jabatan);
                            optDiv.onclick = function() {
                                selectSearchableOption(cfg.selectId, data.data.nama_jabatan, data.data.nama_jabatan, cfg.dropdownId);
                            };
                            optDiv.innerHTML = `
                                <div class="flex items-center gap-2 min-w-0 pr-2">
                                    <i class="fas fa-briefcase text-slate-400 text-xs shrink-0"></i>
                                    <span class="truncate">${data.data.nama_jabatan}</span>
                                </div>
                            `;
                            list.appendChild(optDiv);
                        }
                    });

                    document.getElementById('modal_nama_jabatan').value = '';
                } else {
                    err.textContent = data.message || 'Gagal menambahkan jabatan';
                    err.classList.remove('hidden');
                }
            } catch (error) {
                err.textContent = 'Terjadi kesalahan sistem';
                err.classList.remove('hidden');
            }
        }

        async function deleteJabatanById(id, nama) {
            if (!confirm(`Hapus jabatan ${nama}?`)) return;

            try {
                const res = await fetch("{{ route('administrator.jabatan.delete-ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                if (data.success) {
                    const item = document.getElementById(`item-jabatan-${id}`);
                    if (item) item.remove();

                    // Hapus dari dropdown searchable options
                    document.querySelectorAll(`.searchable-option[data-value="${nama}"]`).forEach(el => el.remove());

                    // Hapus dari hidden select options dan reset jika sedang dipilih
                    ['select_jabatan', 'edit_jabatan'].forEach(selId => {
                        const sel = document.getElementById(selId);
                        if (sel) {
                            Array.from(sel.options).forEach(opt => {
                                if (opt.value === nama) opt.remove();
                            });
                            if (sel.value === nama) {
                                sel.value = '';
                                const isTambah = selId === 'select_jabatan';
                                const trigger = document.querySelector(`[data-dropdown-target="${isTambah ? 'dropdownTambahJabatan' : 'dropdownEditJabatan'}"] .dropdown-selected-label`);
                                if (trigger) trigger.textContent = '-- Pilih Jabatan --';
                            }
                        }
                    });
                }
            } catch(e) {}
        }

        function deleteSelectedJabatan() {
            const sel = document.getElementById('select_jabatan');
            const val = sel ? sel.value : '';
            if (!val) {
                alert('Pilih jabatan yang ingin dihapus terlebih dahulu.');
                return;
            }
            const selectedOpt = sel.options[sel.selectedIndex];
            let id = selectedOpt ? selectedOpt.dataset.id : null;
            if (!id) {
                document.querySelectorAll('#modal_jabatan_list > div').forEach(div => {
                    if (div.textContent.trim().includes(val)) {
                        id = div.id.replace('item-jabatan-', '');
                    }
                });
            }
            deleteJabatanById(id, val);
        }

        // ---------------------------------------------------------------------
        // Searchable Dropdown Helper (Generic & Reusable)
        // ---------------------------------------------------------------------
        function toggleSearchableDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;

            const isHidden = dropdown.classList.contains('hidden');

            // Close all other open searchable dropdowns
            document.querySelectorAll('.searchable-dropdown-menu:not(.hidden)').forEach(d => {
                if (d.id !== dropdownId) closeSearchableDropdown(d.id);
            });

            if (isHidden) {
                dropdown.classList.remove('hidden');
                const trigger = document.querySelector(`[data-dropdown-target="${dropdownId}"]`);
                if (trigger) {
                    const chev = trigger.querySelector('.dropdown-chevron');
                    if (chev) chev.classList.add('rotate-180');
                }
                const searchInput = dropdown.querySelector('.searchable-input');
                if (searchInput) {
                    searchInput.value = '';
                    filterSearchableOptions(dropdownId, '');
                    setTimeout(() => searchInput.focus(), 50);
                }
            } else {
                closeSearchableDropdown(dropdownId);
            }
        }

        function closeSearchableDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;
            dropdown.classList.add('hidden');
            const trigger = document.querySelector(`[data-dropdown-target="${dropdownId}"]`);
            if (trigger) {
                const chev = trigger.querySelector('.dropdown-chevron');
                if (chev) chev.classList.remove('rotate-180');
            }
        }

        function filterSearchableOptions(dropdownId, query) {
            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;

            const q = query.trim().toLowerCase();
            const items = dropdown.querySelectorAll('.searchable-option');
            let matchCount = 0;

            items.forEach(item => {
                const text = (item.getAttribute('data-search-text') || item.textContent).toLowerCase();
                if (!q || text.includes(q)) {
                    item.classList.remove('hidden');
                    matchCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            const emptyState = dropdown.querySelector('.searchable-empty');
            if (emptyState) {
                if (matchCount === 0) {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            }

            const clearBtn = dropdown.querySelector('.searchable-clear-btn');
            if (clearBtn) {
                if (q) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }
        }

        function clearSearchableInput(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;
            const searchInput = dropdown.querySelector('.searchable-input');
            if (searchInput) {
                searchInput.value = '';
                filterSearchableOptions(dropdownId, '');
                searchInput.focus();
            }
        }

        function selectSearchableOption(selectId, value, label, dropdownId) {
            const selectEl = document.getElementById(selectId);
            if (selectEl) {
                selectEl.value = value;
                selectEl.dispatchEvent(new Event('change', { bubbles: true }));
            }

            const trigger = document.querySelector(`[data-dropdown-target="${dropdownId}"]`);
            if (trigger) {
                const labelSpan = trigger.querySelector('.dropdown-selected-label');
                if (labelSpan) labelSpan.textContent = label;
            }

            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.querySelectorAll('.searchable-option').forEach(opt => {
                    const isSelected = String(opt.getAttribute('data-value')) === String(value);
                    if (isSelected) {
                        opt.classList.add('bg-blue-50', 'text-blue-700', 'font-bold');
                        opt.classList.remove('text-slate-700', 'hover:bg-slate-50');
                        let check = opt.querySelector('.check-icon');
                        if (!check) {
                            check = document.createElement('i');
                            check.className = 'fas fa-check text-blue-600 text-xs shrink-0 check-icon';
                            opt.appendChild(check);
                        }
                    } else {
                        opt.classList.remove('bg-blue-50', 'text-blue-700', 'font-bold');
                        opt.classList.add('text-slate-700', 'hover:bg-slate-50');
                        const check = opt.querySelector('.check-icon');
                        if (check) check.remove();
                    }
                });
            }

            closeSearchableDropdown(dropdownId);
        }

        // Global click listener to close searchable dropdowns when clicked outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.searchable-dropdown-wrapper')) {
                document.querySelectorAll('.searchable-dropdown-menu:not(.hidden)').forEach(d => {
                    closeSearchableDropdown(d.id);
                });
            }
        });
    </script>
</x-app-layout>