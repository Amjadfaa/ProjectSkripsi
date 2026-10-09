<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Tambah Kartu PAS</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg p-6 max-w-2xl">

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('administrator.kartu-pas.simpan') }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium text-gray-700">Nomor Kartu</label>
                <input type="text" name="nomor_kartu" value="{{ old('nomor_kartu') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('nomor_kartu') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>


            <div class="mb-4">
                <label class="block font-medium text-gray-700">Nama Pemegang</label>
                <input type="text" name="nama_pemegang" value="{{ old('nama_pemegang') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('nama_pemegang') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-1">Instansi / Perusahaan</label>
                <div class="relative searchable-dropdown-wrapper" id="containerTambahInstansi">
                    <select name="instansi_id" id="tambah_instansi_id" class="hidden" required>
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($instansiList as $instansi)
                            @php $sisa = $instansi->sisa_kuota; @endphp
                            <option value="{{ $instansi->id }}"
                                {{ old('instansi_id') == $instansi->id ? 'selected' : '' }}
                                {{ $sisa <= 0 ? 'disabled' : '' }}>
                                {{ $instansi->nama_instansi }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button" 
                            data-dropdown-target="dropdownTambahInstansi"
                            onclick="toggleSearchableDropdown('dropdownTambahInstansi')"
                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer flex items-center justify-between gap-2 hover:border-gray-400">
                        <div class="flex items-center gap-2 min-w-0 pr-1">
                            <i class="fas fa-building text-gray-400 text-sm shrink-0"></i>
                            <span class="dropdown-selected-label truncate font-medium text-gray-700">
                                @php
                                    $selectedInstansi = old('instansi_id') ? $instansiList->firstWhere('id', old('instansi_id')) : null;
                                @endphp
                                {{ $selectedInstansi ? $selectedInstansi->nama_instansi : '-- Pilih Instansi --' }}
                            </span>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs shrink-0 dropdown-chevron transition-transform duration-200"></i>
                    </button>

                    <div id="dropdownTambahInstansi" 
                         class="searchable-dropdown-menu absolute left-0 top-full mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 p-2.5 hidden animate-dropdown-fade">
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
                                @php 
                                    $sisa = $instansi->sisa_kuota; 
                                    $isHabis = ($sisa <= 0); 
                                    $isSelected = old('instansi_id') == $instansi->id;
                                @endphp
                                <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between transition {{ $isHabis ? 'opacity-50 cursor-not-allowed bg-slate-50/50' : 'cursor-pointer hover:bg-slate-50' }} {{ $isSelected ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700' }}"
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
                @error('instansi_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Area Akses Multi-Select -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-600">
                            Area Akses <span class="text-xs font-normal text-blue-600">(Pilih >1 Area)</span>
                        </label>
                        <button type="button" onclick="openModalArea()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-2 py-0.5 rounded text-xs shadow-sm" title="Kelola / Tambah Area Akses">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    </div>
                    <div class="border border-gray-300 rounded-lg p-2 max-h-40 overflow-y-auto bg-gray-50/50 space-y-1" id="select_area_akses">
                        @foreach($areaAksesList as $area)
                            @php $labelFormatted = $area->kode . ': ' . $area->keterangan; @endphp
                            <label class="flex items-center gap-2 p-1.5 rounded hover:bg-white border border-transparent hover:border-gray-200 cursor-pointer text-xs font-medium text-gray-700 transition" title="{{ $labelFormatted }}">
                                <input type="checkbox" name="area_akses[]" value="{{ $area->kode }}"
                                       class="rounded text-blue-600 focus:ring-blue-500"
                                       {{ is_array(old('area_akses')) && in_array($area->kode, old('area_akses')) ? 'checked' : '' }}>
                                <span class="font-bold text-blue-800 bg-blue-100 px-1.5 py-0.5 rounded shrink-0">{{ $area->kode }}</span>
                                <span class="truncate">{{ $area->keterangan }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('area_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Jabatan Dropdown & Modal Button -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan</label>
                    <div class="flex gap-1.5 items-center">
                        <div class="relative searchable-dropdown-wrapper flex-1" id="containerTambahJabatan">
                            <select name="jabatan" id="select_jabatan" class="hidden">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatanList as $jbt)
                                    <option value="{{ $jbt->nama_jabatan }}" data-id="{{ $jbt->id }}" {{ old('jabatan') == $jbt->nama_jabatan ? 'selected' : '' }}>
                                        {{ $jbt->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" 
                                    data-dropdown-target="dropdownTambahJabatan"
                                    onclick="toggleSearchableDropdown('dropdownTambahJabatan')"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer flex items-center justify-between gap-2 hover:border-gray-400">
                                <div class="flex items-center gap-2 min-w-0 pr-1">
                                    <i class="fas fa-briefcase text-gray-400 text-xs shrink-0"></i>
                                    <span class="dropdown-selected-label truncate font-medium text-gray-700">
                                        {{ old('jabatan') ? old('jabatan') : '-- Pilih Jabatan --' }}
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs shrink-0 dropdown-chevron transition-transform duration-200"></i>
                            </button>

                            <div id="dropdownTambahJabatan" 
                                 class="searchable-dropdown-menu absolute left-0 top-full mt-1.5 w-full bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 p-2.5 hidden animate-dropdown-fade">
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
                                    <div class="searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50"
                                         data-value=""
                                         data-search-text="-- pilih jabatan -- kosong reset"
                                         data-display-name="-- Pilih Jabatan --"
                                         onclick="selectSearchableOption('select_jabatan', '', '-- Pilih Jabatan --', 'dropdownTambahJabatan')">
                                        <span class="text-slate-400 italic">-- Pilih Jabatan --</span>
                                    </div>
                                    @foreach($jabatanList as $jbt)
                                        @php $isJbtSelected = (old('jabatan') == $jbt->nama_jabatan); @endphp
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

                        <button type="button" onclick="openModalJabatan()" class="bg-green-600 hover:bg-green-700 text-white font-bold px-3 py-2 rounded-lg text-sm flex items-center justify-center shrink-0 shadow-sm" title="Kelola / Tambah Jabatan">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" onclick="deleteSelectedJabatan()" class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-2 rounded-lg text-sm flex items-center justify-center shrink-0 shadow-sm" title="Hapus Jabatan Yang Dipilih">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block font-medium text-gray-700">Tanggal Terbit</label>
                    <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit', date('Y-m-d')) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('tanggal_terbit') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Tanggal Berlaku</label>
                    <input type="date" name="tanggal_berlaku" value="{{ old('tanggal_berlaku') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('tanggal_berlaku') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Keterangan</label>
                    <select name="keterangan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Baru" {{ old('keterangan', 'Baru') == 'Baru' ? 'selected' : '' }}>Baru</option>
                        <option value="Perpanjangan" {{ old('keterangan') == 'Perpanjangan' ? 'selected' : '' }}>Perpanjangan</option>
                    </select>
                    @error('keterangan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('administrator.kartu-pas.index') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</a>
                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan Kartu PAS</button>
            </div>

        </form>
    </div>

    <!-- MODAL KELOLA / TAMBAH AREA AKSES -->
    <div id="modalAreaAkses" class="fixed inset-0 z-[70] hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-blue-600"></i> Kelola & Tambah Area Akses
                </h3>
                <button type="button" onclick="closeModalArea()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
            </div>

            <form id="formAreaAkses" onsubmit="submitAreaAkses(event)" class="mb-4">
                <div class="grid grid-cols-3 gap-2 mb-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kode (Contoh: W)</label>
                        <input type="text" id="modal_kode_akses" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="Kode" required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan Area</label>
                        <input type="text" id="modal_keterangan_akses" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="Contoh: Daerah VIP" required>
                    </div>
                </div>
                <div id="areaAksesError" class="text-red-600 text-xs mb-2 hidden"></div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 font-medium">
                        <i class="fas fa-plus mr-1"></i> Tambah Area
                    </button>
                </div>
            </form>

            <hr class="mb-3">

            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Daftar Area Akses Saat Ini</h4>
            <div class="overflow-y-auto max-h-56 border rounded-lg p-2 space-y-1.5" id="modal_area_list">
                @foreach($areaAksesList as $area)
                    <div class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded border text-sm" id="item-area-{{ $area->id }}">
                        <span class="font-medium text-gray-800">{{ $area->kode }}: {{ $area->keterangan }}</span>
                        <button type="button" onclick="deleteAreaById({{ $area->id }}, '{{ $area->kode }}')" class="text-red-500 hover:text-red-700 p-1 text-xs" title="Hapus Area Ini">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end pt-4 border-t mt-4">
                <button type="button" onclick="closeModalArea()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Selesai</button>
            </div>
        </div>
    </div>

    <!-- MODAL KELOLA / TAMBAH JABATAN -->
    <div id="modalJabatan" class="fixed inset-0 z-[70] hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 relative max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-briefcase text-green-600"></i> Kelola & Tambah Jabatan
                </h3>
                <button type="button" onclick="closeModalJabatan()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
            </div>

            <form id="formJabatan" onsubmit="submitJabatan(event)" class="mb-4">
                <div class="mb-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Jabatan Baru</label>
                    <input type="text" id="modal_nama_jabatan" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" placeholder="Contoh: General Manager" required>
                </div>
                <div id="jabatanError" class="text-red-600 text-xs mb-2 hidden"></div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-green-700 font-medium">
                        <i class="fas fa-plus mr-1"></i> Tambah Jabatan
                    </button>
                </div>
            </form>

            <hr class="mb-3">

            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Daftar Jabatan Saat Ini</h4>
            <div class="overflow-y-auto max-h-56 border rounded-lg p-2 space-y-1.5" id="modal_jabatan_list">
                @foreach($jabatanList as $jbt)
                    <div class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded border text-sm" id="item-jabatan-{{ $jbt->id }}">
                        <span class="font-medium text-gray-800">{{ $jbt->nama_jabatan }}</span>
                        <button type="button" onclick="deleteJabatanById({{ $jbt->id }}, '{{ $jbt->nama_jabatan }}')" class="text-red-500 hover:text-red-700 p-1 text-xs" title="Hapus Jabatan Ini">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end pt-4 border-t mt-4">
                <button type="button" onclick="closeModalJabatan()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Selesai</button>
            </div>
        </div>
    </div>

    <script>
        function openModalArea() {
            document.getElementById('modal_kode_akses').value = '';
            document.getElementById('modal_keterangan_akses').value = '';
            document.getElementById('areaAksesError').classList.add('hidden');
            document.getElementById('modalAreaAkses').classList.remove('hidden');
        }

        function closeModalArea() {
            document.getElementById('modalAreaAkses').classList.add('hidden');
        }

        function openModalJabatan() {
            document.getElementById('modal_nama_jabatan').value = '';
            document.getElementById('jabatanError').classList.add('hidden');
            document.getElementById('modalJabatan').classList.remove('hidden');
        }

        function closeModalJabatan() {
            document.getElementById('modalJabatan').classList.add('hidden');
        }

        async function submitAreaAkses(e) {
            e.preventDefault();
            const kode = document.getElementById('modal_kode_akses').value;
            const keterangan = document.getElementById('modal_keterangan_akses').value;
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
                    // Update Dropdown
                    const select = document.getElementById('select_area_akses');
                    const opt = document.createElement('option');
                    opt.value = result.data.value;
                    opt.setAttribute('data-id', result.data.id);
                    opt.setAttribute('title', result.data.label);
                    opt.textContent = result.data.label.length > 60 ? result.data.label.substring(0, 60) + '...' : result.data.label;
                    opt.selected = true;
                    select.appendChild(opt);

                    // Update Modal List
                    const list = document.getElementById('modal_area_list');
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'flex items-center justify-between bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded border text-sm';
                    itemDiv.id = 'item-area-' + result.data.id;
                    itemDiv.innerHTML = `
                        <span class="font-medium text-gray-800">${result.data.label}</span>
                        <button type="button" onclick="deleteAreaById(${result.data.id}, '${result.data.kode}')" class="text-red-500 hover:text-red-700 p-1 text-xs" title="Hapus Area Ini">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                    list.appendChild(itemDiv);

                    document.getElementById('modal_kode_akses').value = '';
                    document.getElementById('modal_keterangan_akses').value = '';
                    errDiv.classList.add('hidden');
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
                    // Remove from Modal List
                    const elModal = document.getElementById('item-area-' + id);
                    if (elModal) elModal.remove();

                    // Remove from Select Dropdown
                    const select = document.getElementById('select_area_akses');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].getAttribute('data-id') == id || select.options[i].value == kode) {
                            select.remove(i);
                            break;
                        }
                    }
                } else {
                    alert(result.message || 'Gagal menghapus area akses');
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi.');
            }
        }

        function deleteSelectedArea() {
            const select = document.getElementById('select_area_akses');
            const selectedOpt = select.options[select.selectedIndex];
            if (!select.value) {
                alert('Pilih Area Akses terlebih dahulu dari dropdown untuk menghapus.');
                return;
            }
            const id = selectedOpt.getAttribute('data-id');
            const kode = select.value;
            if (id) {
                deleteAreaById(id, kode);
            } else {
                alert('Data ID tidak ditemukan pada opsi terpilih.');
            }
        }

        async function submitJabatan(e) {
            e.preventDefault();
            const nama_jabatan = document.getElementById('modal_nama_jabatan').value;
            const errDiv = document.getElementById('jabatanError');

            try {
                const response = await fetch("{{ route('administrator.jabatan.store-ajax') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ nama_jabatan })
                });

                const result = await response.json();
                if (result.success) {
                    // Update Dropdown
                    const select = document.getElementById('select_jabatan');
                    const opt = document.createElement('option');
                    opt.value = result.data.value;
                    opt.setAttribute('data-id', result.data.id);
                    opt.textContent = result.data.label;
                    opt.selected = true;
                    select.appendChild(opt);

                    // Add to searchable list
                    const sList = document.getElementById('listTambahJabatan');
                    if (sList) {
                        const optDiv = document.createElement('div');
                        optDiv.className = 'searchable-option px-2.5 py-1.5 rounded-xl text-xs flex items-center justify-between cursor-pointer transition text-slate-700 hover:bg-slate-50';
                        optDiv.setAttribute('data-value', result.data.value);
                        optDiv.setAttribute('data-search-text', result.data.label);
                        optDiv.setAttribute('data-display-name', result.data.label);
                        optDiv.onclick = function() {
                            selectSearchableOption('select_jabatan', result.data.value, result.data.label, 'dropdownTambahJabatan');
                        };
                        optDiv.innerHTML = `
                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                <i class="fas fa-briefcase text-slate-400 text-xs shrink-0"></i>
                                <span class="truncate">${result.data.label}</span>
                            </div>
                        `;
                        sList.appendChild(optDiv);
                        // Auto-select newly created
                        selectSearchableOption('select_jabatan', result.data.value, result.data.label, 'dropdownTambahJabatan');
                    }

                    // Update Modal List
                    const list = document.getElementById('modal_jabatan_list');
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'flex items-center justify-between bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded border text-sm';
                    itemDiv.id = 'item-jabatan-' + result.data.id;
                    itemDiv.innerHTML = `
                        <span class="font-medium text-gray-800">${result.data.label}</span>
                        <button type="button" onclick="deleteJabatanById(${result.data.id}, '${result.data.nama_jabatan}')" class="text-red-500 hover:text-red-700 p-1 text-xs" title="Hapus Jabatan Ini">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                    list.appendChild(itemDiv);

                    document.getElementById('modal_nama_jabatan').value = '';
                    errDiv.classList.add('hidden');
                } else {
                    errDiv.textContent = result.message || 'Gagal menambahkan Jabatan';
                    errDiv.classList.remove('hidden');
                }
            } catch (err) {
                errDiv.textContent = 'Terjadi kesalahan sistem.';
                errDiv.classList.remove('hidden');
            }
        }

        async function deleteJabatanById(id, nama) {
            if (!confirm(`Apakah Anda yakin ingin menghapus Jabatan [${nama}]?`)) return;

            try {
                const response = await fetch("{{ route('administrator.jabatan.delete-ajax') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ id })
                });

                const result = await response.json();
                if (result.success) {
                    // Remove from Modal List
                    const elModal = document.getElementById('item-jabatan-' + id);
                    if (elModal) elModal.remove();

                    // Remove from Select Dropdown
                    const select = document.getElementById('select_jabatan');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].getAttribute('data-id') == id || select.options[i].value == nama) {
                            select.remove(i);
                            break;
                        }
                    }

                    // Remove from Searchable list
                    const sList = document.getElementById('listTambahJabatan');
                    if (sList) {
                        sList.querySelectorAll(`.searchable-option[data-value="${nama}"]`).forEach(el => el.remove());
                    }
                    if (select.value === nama || !select.value) {
                        select.value = '';
                        const triggerLabel = document.querySelector('[data-dropdown-target="dropdownTambahJabatan"] .dropdown-selected-label');
                        if (triggerLabel) triggerLabel.textContent = '-- Pilih Jabatan --';
                    }
                } else {
                    alert(result.message || 'Gagal menghapus jabatan');
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi.');
            }
        }

        function deleteSelectedJabatan() {
            const select = document.getElementById('select_jabatan');
            const selectedOpt = select.options[select.selectedIndex];
            if (!select.value) {
                alert('Pilih Jabatan terlebih dahulu dari dropdown untuk menghapus.');
                return;
            }
            const id = selectedOpt.getAttribute('data-id');
            const nama = select.value;
            if (id) {
                deleteJabatanById(id, nama);
            } else {
                alert('Data ID tidak ditemukan pada opsi terpilih.');
            }
        }
    </script>
</x-app-layout>
