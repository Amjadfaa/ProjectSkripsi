<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Data Kartu PAS</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow mb-4">
        <div class="bg-blue-500 rounded-t-lg px-6 py-4 flex justify-between items-center flex-wrap gap-2">
            <h3 class="text-white font-bold text-lg">🪪 Data Kartu PAS</h3>
            <div class="flex gap-2 flex-wrap items-center">
                <!-- Tombol Import Data (Modal) -->
                <button type="button" onclick="openModalImport()"
                        class="bg-amber-500 hover:bg-amber-600 text-white px-3.5 py-2 rounded-lg text-sm font-medium shadow-sm flex items-center gap-1.5 transition cursor-pointer">
                    <i class="fas fa-file-import"></i> Import Data
                </button>

                <!-- Dropdown Unduh Data Kartu PAS (Excel / PDF) -->
                <div class="relative inline-block text-left" id="downloadDropdownContainer">
                    <button type="button" onclick="toggleDownloadDropdown(event)"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-sm font-medium shadow-sm flex items-center gap-1.5 transition cursor-pointer">
                        <i class="fas fa-download"></i> Unduh Data <i class="fas fa-chevron-down text-xs ml-0.5"></i>
                    </button>
                    <div id="downloadDropdownMenu" class="hidden absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 z-50 divide-y divide-gray-100 overflow-hidden">
                        <div class="py-1">
                            <a href="{{ route('administrator.kartu-pas.export.excel', request()->query()) }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                <i class="fas fa-file-excel text-emerald-600 text-base"></i> Unduh Format Excel (.xlsx)
                            </a>
                            <a href="{{ route('administrator.kartu-pas.export.pdf', request()->query()) }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-rose-50 hover:text-rose-700 transition">
                                <i class="fas fa-file-pdf text-rose-600 text-base"></i> Unduh Format PDF (.pdf)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tombol Tambah Kartu PAS (Modal) -->
                <button type="button" onclick="openModalTambahKartu()"
                        class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-50 shadow-sm cursor-pointer flex items-center gap-1.5 transition">
                    <i class="fas fa-plus"></i> Tambah Kartu PAS
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="px-6 py-4 border-b">
            <form method="GET" action="{{ route('administrator.kartu-pas.index') }}">
                <div class="flex gap-4 items-end flex-wrap">
                    <div class="flex-1" style="min-width:200px;">
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama, nomor kartu..."
                                   class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Filter Instansi</label>
                        <select name="instansi" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Semua Instansi</option>
                            @foreach($instansiList as $instansi)
                                <option value="{{ $instansi->nama_instansi }}" {{ request('instansi') == $instansi->nama_instansi ? 'selected' : '' }}>
                                    {{ $instansi->nama_instansi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Filter Status</label>
                        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="kadaluarsa" {{ request('status') == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600">Filter</button>
                        <a href="{{ route('administrator.kartu-pas.index') }}"
                           class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Aksi Massal -->
        <div class="px-6 py-3 bg-gray-50 border-b flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="checkAll" class="w-4 h-4"
                       onchange="toggleCheckAll(this)">
                <label for="checkAll" class="text-sm text-gray-600">Pilih Semua</label>
                <span id="selectedCount" class="text-xs text-gray-400"></span>
            </div>
            <div class="flex gap-2">
                <!-- Hapus yang dipilih -->
                <form id="formDeleteSelected" method="POST"
                      action="{{ route('administrator.kartu-pas.destroy-selected') }}">
                    @csrf @method('DELETE')
                    <div id="selectedInputs"></div>
                    <button type="button" onclick="confirmDeleteSelected()"
                            class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-orange-600 hidden flex items-center gap-1 shadow-sm"
                            id="btnDeleteSelected">
                        <i class="fas fa-trash"></i> Hapus Dipilih
                    </button>
                </form>

                <!-- Hapus Semua -->
                <form id="formDeleteAll" method="POST" action="{{ route('administrator.kartu-pas.destroy-all') }}">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDeleteAll()"
                            class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-red-700 flex items-center gap-1 shadow-sm">
                        <i class="fas fa-trash-alt"></i> Hapus Semua
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="px-4 py-3 text-center w-10"></th>
                        <th class="px-4 py-3 text-left text-gray-600">No.Registrasi</th>
                        <th class="px-4 py-3 text-left text-gray-600">Nama Pemegang</th>
                        <th class="px-4 py-3 text-left text-gray-600">Instansi</th>
                        <th class="px-4 py-3 text-left text-gray-600">Area Akses</th>
                        <th class="px-4 py-3 text-left text-gray-600">Jabatan</th>
                        <th class="px-4 py-3 text-left text-gray-600">Masa Berlaku</th>
                        <th class="px-4 py-3 text-left text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kartuPas as $kartu)
                    <tr class="border-b hover:bg-gray-50 {{ $kartu->status === 'kadaluarsa' ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="kartu-checkbox w-4 h-4"
                                   value="{{ $kartu->id }}" onchange="updateSelectedCount()">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-700">{{ $kartu->nomor_kartu }}</td>
                        <td class="px-4 py-3">{{ $kartu->nama_pemegang }}</td>
                        <td class="px-4 py-3">{{ $kartu->perusahaan }}</td>
                        <td class="px-4 py-3">{{ $kartu->area_akses }}</td>
                        <td class="px-4 py-3">{{ $kartu->jabatan ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $kartu->tanggal_berlaku->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = [
                                    'aktif'       => 'bg-green-100 text-green-700',
                                    'tidak_aktif' => 'bg-gray-100 text-gray-700',
                                    'kadaluarsa'  => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $badge[$kartu->status] }}">
                                {{ ucfirst(str_replace('_', ' ', $kartu->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Download QR Code (Icon) -->
                                <a href="{{ route('administrator.kartu-pas.qrcode', $kartu->id) }}"
                                   class="bg-indigo-600 hover:bg-indigo-700 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                   title="Unduh QR Code (No. Reg: {{ $kartu->nomor_kartu }})">
                                    <i class="fas fa-qrcode"></i>
                                </a>

                                <!-- Tombol Perpanjangan (Icon) -->
                                <button type="button" onclick='openModalPerpanjangan(@json($kartu))'
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                        title="Perpanjang Masa Berlaku Kartu PAS">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                                
                                <!-- Tombol Edit (Icon) -->
                                <button type="button" onclick='openModalEditKartu(@json($kartu))'
                                        class="bg-amber-500 hover:bg-amber-600 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                        title="Edit Data Kartu PAS">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Tombol Hapus (Icon) -->
                                <form id="formDeleteItem-{{ $kartu->id }}" method="POST" action="{{ route('administrator.kartu-pas.destroy', $kartu->id) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmSingleDelete({{ $kartu->id }}, '{{ $kartu->nomor_kartu }}')"
                                            class="bg-rose-600 hover:bg-rose-700 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                            title="Hapus Kartu PAS">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">Belum ada data kartu PAS.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4">
            {{ $kartuPas->links() }}
        </div>
    </div>

    <!-- MODAL BESAR IMPORT DATA KARTU PAS -->
    <div id="modalImportKartu" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6 relative max-h-[95vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-import text-amber-500"></i> Import Data Kartu PAS (File Excel)
                </h3>
                <button type="button" onclick="closeModalImport()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Upload file Excel data kartu PAS. Sistem akan otomatis membaca data dari setiap sheet bulan.
            </p>

            <!-- Format Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                <p class="font-semibold text-blue-800 text-xs uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i class="fas fa-info-circle"></i> Format Kolom yang Didukung:
                </p>
                <table class="w-full text-xs text-blue-900">
                    <thead>
                        <tr class="border-b border-blue-200 font-semibold">
                            <th class="py-1 text-left">Kolom Excel</th>
                            <th class="py-1 text-left">Deskripsi Field</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-100">
                        <tr><td class="py-1 font-bold">Kolom D</td><td>Nama Pemegang</td></tr>
                        <tr><td class="py-1 font-bold">Kolom E</td><td>No. Registrasi / Nomor Kartu PAS</td></tr>
                        <tr><td class="py-1 font-bold">Kolom F</td><td>Kode Area Akses (Contoh: A, B, C)</td></tr>
                        <tr><td class="py-1 font-bold">Kolom G</td><td>Jabatan</td></tr>
                        <tr><td class="py-1 font-bold">Kolom H</td><td>Masa Berlaku (Contoh: 30 MEI 2026)</td></tr>
                    </tbody>
                </table>
            </div>

            <form method="POST" action="{{ route('administrator.import.kartu-pas') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File Excel (.xlsx / .xls)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-amber-500 transition cursor-pointer bg-gray-50 hover:bg-amber-50/30"
                         onclick="document.getElementById('import_file_input').click()">
                        <input type="file" name="file" id="import_file_input" accept=".xlsx,.xls"
                               class="hidden" onchange="showModalImportFileName(this)" required>
                        <i class="fas fa-cloud-upload-alt text-4xl text-amber-500 mb-2"></i>
                        <p class="text-gray-700 font-medium text-sm">Klik untuk memilih file Excel</p>
                        <p class="text-gray-400 text-xs mt-1">Format file: .xlsx atau .xls (Ukuran maks. 10MB)</p>
                        <p id="modalImportFileName" class="text-emerald-600 text-sm mt-3 font-semibold"></p>
                    </div>
                    @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModalImport()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600 text-sm font-bold shadow-md flex items-center gap-1.5">
                        <i class="fas fa-upload"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL BESAR TAMBAH KARTU PAS -->
    <div id="modalTambahKartu" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full p-6 relative max-h-[95vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-id-card text-blue-600"></i> Form Tambah Kartu PAS Baru
                </h3>
                <button type="button" onclick="closeModalTambahKartu()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border border-red-200 text-red-700 p-4 rounded-lg mb-4 text-sm">
                    <strong class="font-bold">Gagal Menyimpan Data!</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('administrator.kartu-pas.simpan') }}">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Kartu / Registrasi</label>
                        <input type="text" name="nomor_kartu" value="{{ old('nomor_kartu') }}"
                               placeholder="Contoh: PAS-2026-001"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        @error('nomor_kartu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap Pemegang</label>
                        <input type="text" name="nama_pemegang" value="{{ old('nama_pemegang') }}"
                               placeholder="Nama lengkap pemegang"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        @error('nama_pemegang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Instansi / Perusahaan</label>
                    <select name="instansi_id" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($instansiList as $instansi)
                            @php $sisa = $instansi->sisa_kuota; @endphp
                            <option value="{{ $instansi->id }}"
                                {{ old('instansi_id') == $instansi->id ? 'selected' : '' }}
                                {{ $sisa <= 0 ? 'disabled' : '' }}>
                                {{ $instansi->nama_instansi }} &mdash; (Sisa Kuota: {{ $sisa }} / Total: {{ $instansi->kuota }}) {{ $sisa <= 0 ? '[KUOTA HABIS]' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('instansi_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <!-- Area Akses Multi-Select -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold text-gray-700">
                                Area Akses <span class="text-xs font-normal text-blue-600">(Pilih >1 Area)</span>
                            </label>
                            <button type="button" onclick="openModalArea()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-2 py-0.5 rounded text-xs shadow-sm" title="Kelola / Tambah Area Akses">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                        <div class="border border-gray-300 rounded-lg p-2 max-h-40 overflow-y-auto bg-gray-50/50 space-y-1" id="container_area_tambah">
                            @foreach($areaAksesList as $area)
                                @php $labelFormatted = $area->kode . ': ' . $area->keterangan; @endphp
                                <label class="flex items-center gap-2 p-1.5 rounded hover:bg-white border border-transparent hover:border-gray-200 cursor-pointer text-xs font-medium text-gray-700 transition" title="{{ $labelFormatted }}">
                                    <input type="checkbox" name="area_akses[]" value="{{ $area->kode }}"
                                           class="rounded text-blue-600 focus:ring-blue-500 checkbox-area-tambah"
                                           {{ is_array(old('area_akses')) && in_array($area->kode, old('area_akses')) ? 'checked' : '' }}>
                                    <span class="font-bold text-blue-800 bg-blue-100 px-1.5 py-0.5 rounded shrink-0">{{ $area->kode }}</span>
                                    <span class="truncate">{{ $area->keterangan }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('area_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
                        <div class="flex gap-1.5">
                            <select name="jabatan" id="select_jabatan" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatanList as $jbt)
                                    <option value="{{ $jbt->nama_jabatan }}" data-id="{{ $jbt->id }}" {{ old('jabatan') == $jbt->nama_jabatan ? 'selected' : '' }}>
                                        {{ $jbt->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>
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

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Terbit</label>
                        <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit', date('Y-m-d')) }}"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        @error('tanggal_terbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Berlaku (Kadaluarsa)</label>
                        <input type="date" name="tanggal_berlaku" value="{{ old('tanggal_berlaku') }}"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        @error('tanggal_berlaku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModalTambahKartu()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-bold shadow-md">
                        Simpan Kartu PAS
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- MODAL EDIT KARTU PAS (SPA) -->
    <div id="modalEditKartu" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full p-6 relative max-h-[95vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-amber-500"></i> Edit Data Kartu PAS
                </h3>
                <button type="button" onclick="closeModalEditKartu()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="formEditKartu" method="POST" action="">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Kartu / Registrasi</label>
                        <input type="text" id="edit_nomor_kartu" name="nomor_kartu" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pemegang</label>
                        <input type="text" id="edit_nama_pemegang" name="nama_pemegang" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Instansi / Perusahaan</label>
                    <select id="edit_instansi_id" name="instansi_id" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($instansiList as $instansi)
                            <option value="{{ $instansi->id }}">
                                {{ $instansi->nama_instansi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <!-- Area Akses Multi-Select Edit -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold text-gray-700">
                                Area Akses <span class="text-xs font-normal text-amber-600">(Pilih >1 Area)</span>
                            </label>
                        </div>
                        <div class="border border-gray-300 rounded-lg p-2 max-h-40 overflow-y-auto bg-gray-50/50 space-y-1" id="container_area_edit">
                            @foreach($areaAksesList as $area)
                                @php $labelFormatted = $area->kode . ': ' . $area->keterangan; @endphp
                                <label class="flex items-center gap-2 p-1.5 rounded hover:bg-white border border-transparent hover:border-gray-200 cursor-pointer text-xs font-medium text-gray-700 transition" title="{{ $labelFormatted }}">
                                    <input type="checkbox" name="area_akses[]" value="{{ $area->kode }}"
                                           class="rounded text-amber-600 focus:ring-amber-500 checkbox-area-edit">
                                    <span class="font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded shrink-0">{{ $area->kode }}</span>
                                    <span class="truncate">{{ $area->keterangan }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
                        <select id="edit_jabatan" name="jabatan" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatanList as $jbt)
                                <option value="{{ $jbt->nama_jabatan }}">{{ $jbt->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Terbit</label>
                        <input type="date" id="edit_tanggal_terbit" name="tanggal_terbit" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Berlaku</label>
                        <input type="date" id="edit_tanggal_berlaku" name="tanggal_berlaku" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                        <select id="edit_status" name="status" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                            <option value="kadaluarsa">Kadaluarsa</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModalEditKartu()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600 text-sm font-bold shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL PERPANJANGAN KARTU PAS (SPA) -->
    <div id="modalPerpanjanganKartu" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-calendar-plus text-emerald-600"></i> Perpanjangan Masa Berlaku Kartu PAS
                </h3>
                <button type="button" onclick="closeModalPerpanjangan()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Ringkasan Kartu -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4 text-sm space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-gray-500">Nomor Registrasi:</span>
                    <span id="perp_nomor_display" class="font-bold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Pemegang:</span>
                    <span id="perp_nama_display" class="font-semibold text-gray-800"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Instansi:</span>
                    <span id="perp_instansi_display" class="font-medium text-gray-700"></span>
                </div>
                <div class="flex justify-between border-t pt-1.5 mt-1.5">
                    <span class="text-gray-500">Kadaluarsa Saat Ini:</span>
                    <span id="perp_lama_display" class="font-bold text-rose-600"></span>
                </div>
            </div>

            <form id="formPerpanjanganKartu" method="POST" action="">
                @csrf @method('PUT')

                <!-- Hidden Input Fields to Preserve Existing Card Data -->
                <input type="hidden" id="perp_nomor_kartu" name="nomor_kartu">
                <input type="hidden" id="perp_email" name="email">
                <input type="hidden" id="perp_nama_pemegang" name="nama_pemegang">
                <input type="hidden" id="perp_instansi_id" name="instansi_id">
                <input type="hidden" id="perp_area_akses" name="area_akses">
                <input type="hidden" id="perp_jabatan" name="jabatan">
                <input type="hidden" id="perp_tanggal_terbit" name="tanggal_terbit">
                <input type="hidden" name="status" value="aktif">

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Berlaku Baru (Masa Kadaluarsa)</label>
                    <input type="date" id="perp_tanggal_berlaku" name="tanggal_berlaku" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="setPerpanjangTahun(1)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold border">
                            + 1 Tahun
                        </button>
                        <button type="button" onclick="setPerpanjangTahun(2)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-semibold border">
                            + 2 Tahun
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t mt-4">
                    <button type="button" onclick="closeModalPerpanjangan()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 text-sm font-bold shadow-md">
                        Proses Perpanjangan
                    </button>
                </div>
            </form>
        </div>
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

        // Modal Import Kartu PAS Handler
        function openModalImport() {
            document.getElementById('modalImportFileName').textContent = '';
            document.getElementById('import_file_input').value = '';
            document.getElementById('modalImportKartu').classList.remove('hidden');
        }

        function closeModalImport() {
            document.getElementById('modalImportKartu').classList.add('hidden');
        }

        function showModalImportFileName(input) {
            const fileName = input.files[0]?.name ?? '';
            document.getElementById('modalImportFileName').textContent = fileName ? '✅ File Dipilih: ' + fileName : '';
        }

        // Modal Tambah Kartu PAS Handler
        function openModalTambahKartu() {
            document.getElementById('modalTambahKartu').classList.remove('hidden');
        }

        function closeModalTambahKartu() {
            document.getElementById('modalTambahKartu').classList.add('hidden');
        }

        // Modal Edit Kartu PAS Handler
        function openModalEditKartu(kartu) {
            const form = document.getElementById('formEditKartu');
            form.action = "{{ url('/administrator/kartu-pas') }}/" + kartu.id;

            document.getElementById('edit_nomor_kartu').value   = kartu.nomor_kartu || '';
            if (document.getElementById('edit_email')) {
                document.getElementById('edit_email').value     = kartu.email || '';
            }
            document.getElementById('edit_nama_pemegang').value = kartu.nama_pemegang || '';
            document.getElementById('edit_instansi_id').value   = kartu.instansi_id || '';
            
            // Set checkboxes for multi-select Area Akses
            let selectedAreas = (kartu.area_akses || '').split(',').map(s => s.trim());
            document.querySelectorAll('#container_area_edit .checkbox-area-edit').forEach(cb => {
                cb.checked = selectedAreas.includes(cb.value);
            });

            document.getElementById('edit_jabatan').value      = kartu.jabatan || '';
            
            if (kartu.tanggal_terbit) {
                document.getElementById('edit_tanggal_terbit').value = kartu.tanggal_terbit.substring(0, 10);
            }
            if (kartu.tanggal_berlaku) {
                document.getElementById('edit_tanggal_berlaku').value = kartu.tanggal_berlaku.substring(0, 10);
            }
            document.getElementById('edit_status').value = kartu.status || 'aktif';

            document.getElementById('modalEditKartu').classList.remove('hidden');
        }

        function closeModalEditKartu() {
            document.getElementById('modalEditKartu').classList.add('hidden');
        }

        // Modal Perpanjangan Kartu PAS Handler
        function openModalPerpanjangan(kartu) {
            const form = document.getElementById('formPerpanjanganKartu');
            form.action = "{{ url('/administrator/kartu-pas') }}/" + kartu.id;

            document.getElementById('perp_nomor_display').textContent   = kartu.nomor_kartu || '-';
            document.getElementById('perp_nama_display').textContent    = kartu.nama_pemegang || '-';
            document.getElementById('perp_instansi_display').textContent= kartu.perusahaan || '-';
            
            const tglBisa = kartu.tanggal_berlaku ? kartu.tanggal_berlaku.substring(0, 10) : '-';
            document.getElementById('perp_lama_display').textContent    = tglBisa;

            // Fill hidden values
            document.getElementById('perp_nomor_kartu').value   = kartu.nomor_kartu;
            document.getElementById('perp_email').value         = kartu.email || '';
            document.getElementById('perp_nama_pemegang').value = kartu.nama_pemegang;
            document.getElementById('perp_instansi_id').value   = kartu.instansi_id;
            document.getElementById('perp_area_akses').value   = kartu.area_akses;
            document.getElementById('perp_jabatan').value      = kartu.jabatan || '';
            if (kartu.tanggal_terbit) {
                document.getElementById('perp_tanggal_terbit').value = kartu.tanggal_terbit.substring(0, 10);
            }

            // Default new expiration: Today + 1 Year
            setPerpanjangTahun(1);

            document.getElementById('modalPerpanjanganKartu').classList.remove('hidden');
        }

        function closeModalPerpanjangan() {
            document.getElementById('modalPerpanjanganKartu').classList.add('hidden');
        }

        function setPerpanjangTahun(years) {
            const now = new Date();
            now.setFullYear(now.getFullYear() + years);
            const yyyy = now.getFullYear();
            const mm   = String(now.getMonth() + 1).padStart(2, '0');
            const dd   = String(now.getDate()).padStart(2, '0');
            document.getElementById('perp_tanggal_berlaku').value = `${yyyy}-${mm}-${dd}`;
        }

        // Open modal otomatis jika ada error validasi dari form tambah
        @if($errors->any())
            openModalTambahKartu();
        @endif

        // Modal Area Akses Handler
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

        // Table Checkbox Handler
        function toggleCheckAll(checkbox) {
            document.querySelectorAll('.kartu-checkbox').forEach(cb => {
                cb.checked = checkbox.checked;
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.kartu-checkbox:checked');
            const count   = checked.length;
            const btn     = document.getElementById('btnDeleteSelected');
            const span    = document.getElementById('selectedCount');

            span.textContent = count > 0 ? `(${count} dipilih)` : '';
            btn.classList.toggle('hidden', count === 0);

            const all = document.querySelectorAll('.kartu-checkbox');
            document.getElementById('checkAll').checked = count === all.length && all.length > 0;
        }

        // SweetAlert2 Delete Confirmations
        function confirmSingleDelete(id, nomorKartu) {
            SwalConfirm('Hapus Kartu PAS?', `Kartu nomor [${nomorKartu}] akan dihapus secara permanen!`)
            .then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteItem-' + id).submit();
                }
            });
        }

        function confirmDeleteSelected() {
            const checked = document.querySelectorAll('.kartu-checkbox:checked');
            if (checked.length === 0) return;

            SwalConfirm('Hapus Kartu Dipilih?', `Yakin ingin menghapus ${checked.length} kartu PAS yang dipilih?`)
            .then((result) => {
                if (result.isConfirmed) {
                    const form   = document.getElementById('formDeleteSelected');
                    const inputs = document.getElementById('selectedInputs');
                    inputs.innerHTML = '';

                    checked.forEach(cb => {
                        const input = document.createElement('input');
                        input.type  = 'hidden';
                        input.name  = 'ids[]';
                        input.value = cb.value;
                        inputs.appendChild(input);
                    });

                    form.submit();
                }
            });
        }

        function confirmDeleteAll() {
            SwalConfirm('⚠️ PERINGATAN KRUSIAL!', 'Semua data kartu PAS di database akan dihapus permanen!', 'Ya, Hapus Semua!')
            .then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteAll').submit();
                }
            });
        }

        // AJAX SPA Submissions
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
                    // Update Multi-select Containers
                    const cTambah = document.getElementById('container_area_tambah');
                    if (cTambah) {
                        const lbl = document.createElement('label');
                        lbl.className = 'flex items-center gap-2 p-1.5 rounded hover:bg-white border border-transparent hover:border-gray-200 cursor-pointer text-xs font-medium text-gray-700 transition';
                        lbl.title = result.data.label;
                        lbl.innerHTML = `<input type="checkbox" name="area_akses[]" value="${result.data.kode}" class="rounded text-blue-600 focus:ring-blue-500 checkbox-area-tambah" checked>
                            <span class="font-bold text-blue-800 bg-blue-100 px-1.5 py-0.5 rounded shrink-0">${result.data.kode}</span>
                            <span class="truncate">${result.data.keterangan}</span>`;
                        cTambah.appendChild(lbl);
                    }

                    const cEdit = document.getElementById('container_area_edit');
                    if (cEdit) {
                        const lbl = document.createElement('label');
                        lbl.className = 'flex items-center gap-2 p-1.5 rounded hover:bg-white border border-transparent hover:border-gray-200 cursor-pointer text-xs font-medium text-gray-700 transition';
                        lbl.title = result.data.label;
                        lbl.innerHTML = `<input type="checkbox" name="area_akses[]" value="${result.data.kode}" class="rounded text-amber-600 focus:ring-amber-500 checkbox-area-edit">
                            <span class="font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded shrink-0">${result.data.kode}</span>
                            <span class="truncate">${result.data.keterangan}</span>`;
                        cEdit.appendChild(lbl);
                    }

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

                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
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
            const res = await SwalConfirm('Hapus Area Akses?', `Yakin ingin menghapus Area Akses [${kode}]?`);
            if (!res.isConfirmed) return;

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

                    const select = document.getElementById('select_area_akses');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].getAttribute('data-id') == id || select.options[i].value == kode) {
                            select.remove(i);
                            break;
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire('Gagal!', result.message || 'Gagal menghapus area akses', 'error');
                }
            } catch (err) {
                Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
            }
        }

        function deleteSelectedArea() {
            const select = document.getElementById('select_area_akses');
            const selectedOpt = select.options[select.selectedIndex];
            if (!select.value) {
                Swal.fire('Pilih Area Akses', 'Silakan pilih Area Akses dari dropdown terlebih dahulu.', 'info');
                return;
            }
            const id = selectedOpt.getAttribute('data-id');
            const kode = select.value;
            if (id) {
                deleteAreaById(id, kode);
            } else {
                Swal.fire('Info', 'Data ID bawaan tidak dapat dihapus dari dropdown.', 'info');
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
                    const select = document.getElementById('select_jabatan');
                    const opt = document.createElement('option');
                    opt.value = result.data.value;
                    opt.setAttribute('data-id', result.data.id);
                    opt.textContent = result.data.label;
                    opt.selected = true;
                    select.appendChild(opt);

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

                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
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
            const res = await SwalConfirm('Hapus Jabatan?', `Yakin ingin menghapus Jabatan [${nama}]?`);
            if (!res.isConfirmed) return;

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
                    const elModal = document.getElementById('item-jabatan-' + id);
                    if (elModal) elModal.remove();

                    const select = document.getElementById('select_jabatan');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].getAttribute('data-id') == id || select.options[i].value == nama) {
                            select.remove(i);
                            break;
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire('Gagal!', result.message || 'Gagal menghapus jabatan', 'error');
                }
            } catch (err) {
                Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
            }
        }

        function deleteSelectedJabatan() {
            const select = document.getElementById('select_jabatan');
            const selectedOpt = select.options[select.selectedIndex];
            if (!select.value) {
                Swal.fire('Pilih Jabatan', 'Silakan pilih Jabatan dari dropdown terlebih dahulu.', 'info');
                return;
            }
            const id = selectedOpt.getAttribute('data-id');
            const nama = select.value;
            if (id) {
                deleteJabatanById(id, nama);
            } else {
                Swal.fire('Info', 'Data ID bawaan tidak dapat dihapus dari dropdown.', 'info');
            }
        }
    </script>

</x-app-layout>