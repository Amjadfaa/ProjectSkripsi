<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Data Instansi</h2>
    </x-slot>

    <style>
        .modal-backdrop-clean {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.70);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-backdrop-clean.hidden {
            display: none !important;
        }
        .modal-scroll-smooth {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }
        .modal-scroll-smooth::-webkit-scrollbar {
            width: 6px;
        }
        .modal-scroll-smooth::-webkit-scrollbar-track {
            background: transparent;
        }
        .modal-scroll-smooth::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        .modal-scroll-smooth::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                <i class="fas fa-building text-blue-600"></i> Daftar Instansi / Perusahaan
            </h3>
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Tombol Import Excel -->
                <button type="button" onclick="openModalImportInstansi()"
                   class="bg-amber-500 hover:bg-amber-600 text-white px-3.5 py-2 rounded-lg font-semibold text-sm shadow-sm flex items-center gap-1.5 cursor-pointer transition">
                    <i class="fas fa-file-import"></i> Import Excel
                </button>

                <!-- Tombol Export Excel -->
                <a href="{{ route('administrator.instansi.export.excel') }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg font-semibold text-sm shadow-sm flex items-center gap-1.5 cursor-pointer transition"
                   title="Unduh Data Instansi format Excel">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>

                <!-- Tombol Tambah Instansi -->
                <button type="button" onclick="openModalTambahInstansi()"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-semibold text-sm shadow-sm flex items-center gap-1.5 cursor-pointer transition">
                    <i class="fas fa-plus"></i> Tambah Instansi
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 p-4 rounded-lg mb-4 text-sm font-medium">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 p-4 rounded-lg mb-4 text-sm font-medium">
                ⚠️ {{ session('error') }}
            </div>
        @endif

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

        @if($instansis->isEmpty())
            <p class="text-gray-500 py-6 text-center">Belum ada data instansi.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-3 font-semibold text-gray-700">Nama Instansi</th>
                            <th class="p-3 font-semibold text-gray-700">Kuota PAS</th>
                            <th class="p-3 font-semibold text-gray-700">Terpakai</th>
                            <th class="p-3 font-semibold text-gray-700">Sisa Kuota</th>
                            <th class="p-3 font-semibold text-gray-700">Email</th>
                            <th class="p-3 font-semibold text-gray-700">Telepon</th>
                            <th class="p-3 font-semibold text-gray-700">Status</th>
                            <th class="p-3 font-semibold text-gray-700 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instansis as $instansi)
                        @php
                            $terpakai = $instansi->total_kartu ?? $instansi->kartu_pas_count ?? $instansi->kartuPas()->count();
                            $aktif    = $instansi->kartu_aktif ?? $instansi->kartuPas()->where('status', 'aktif')->count();
                            $nonaktif = $instansi->kartu_nonaktif ?? $instansi->kartuPas()->where('status', '!=', 'aktif')->count();
                            $sisa     = max(0, $instansi->kuota - $terpakai);
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-semibold text-gray-800">{{ $instansi->nama_instansi }}</td>
                            <td class="p-3 font-medium text-blue-600">{{ $instansi->kuota }} Kartu</td>
                            <td class="p-3 font-medium text-purple-600">
                                <span>{{ $terpakai }} Kartu</span>
                                @if($terpakai > 0)
                                    <span class="block text-[11px] text-gray-400 font-normal">
                                        ({{ $aktif }} aktif{{ $nonaktif > 0 ? ', ' . $nonaktif . ' kadaluarsa/nonaktif' : '' }})
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 font-medium {{ $sisa <= 0 ? 'text-red-600 font-bold' : 'text-emerald-600' }}">
                                {{ $sisa }} Kartu {{ $sisa <= 0 ? '[HABIS]' : '' }}
                            </td>
                            <td class="p-3 text-gray-600">{{ $instansi->email ?? '-' }}</td>
                            <td class="p-3 text-gray-600">{{ $instansi->telepon ?? '-' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $instansi->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $instansi->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Edit Icon -->
                                    <button type="button" onclick='openModalEditInstansi(@json($instansi))'
                                            class="bg-amber-500 hover:bg-amber-600 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                            title="Edit Instansi">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Tombol Hapus Icon -->
                                    <form id="formDeleteInstansi-{{ $instansi->id }}" method="POST" action="{{ route('administrator.instansi.destroy', $instansi->id) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDeleteInstansi({{ $instansi->id }}, '{{ $instansi->nama_instansi }}')"
                                                class="bg-rose-600 hover:bg-rose-700 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                                title="Hapus Instansi">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- MODAL BESAR TAMBAH INSTANSI -->
    <div id="modalTambahInstansi" class="modal-backdrop-clean hidden" onclick="if(event.target === this) closeModalTambahInstansi()">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative max-h-[95vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-building text-blue-600"></i> Form Tambah Instansi Baru
                </h3>
                <button type="button" onclick="closeModalTambahInstansi()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('administrator.instansi.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Instansi / Perusahaan</label>
                    <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}"
                           placeholder="Contoh: PT Angkasa Pura Indonesia"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kuota Kartu PAS</label>
                        <input type="number" name="kuota" value="{{ old('kuota', 10) }}" min="0"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Instansi</label>
                        <select name="is_active" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="email@instansi.com"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon (Opsional)</label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}"
                               placeholder="0812xxxxxxxx"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Kantor (Opsional)</label>
                    <textarea name="alamat" rows="3" placeholder="Alamat lengkap instansi..."
                              class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">{{ old('alamat') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModalTambahInstansi()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-bold shadow-md">
                        Simpan Instansi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT INSTANSI (SPA) -->
    <div id="modalEditInstansi" class="modal-backdrop-clean hidden" onclick="if(event.target === this) closeModalEditInstansi()">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative max-h-[95vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-amber-500"></i> Edit Data Instansi
                </h3>
                <button type="button" onclick="closeModalEditInstansi()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="formEditInstansi" method="POST" action="">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Instansi / Perusahaan</label>
                    <input type="text" id="edit_nama_instansi" name="nama_instansi" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kuota Kartu PAS</label>
                        <input type="number" id="edit_kuota" name="kuota" min="0" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Instansi</label>
                        <select id="edit_is_active" name="is_active" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" id="edit_email" name="email" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" id="edit_telepon" name="telepon" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Kantor</label>
                    <textarea id="edit_alamat" name="alamat" rows="3" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModalEditInstansi()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600 text-sm font-bold shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL IMPORT DATA INSTANSI -->
    <div id="modalImportInstansi" class="modal-backdrop-clean hidden" onclick="if(event.target === this) closeModalImportInstansi()">
        <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full flex flex-col max-h-[90vh] overflow-hidden">
            <!-- Header Modal (Tetap di atas) -->
            <div class="flex justify-between items-center px-6 py-3.5 border-b bg-gray-50/80 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-import text-amber-500"></i> Import Data Instansi (File Excel)
                </h3>
                <button type="button" onclick="closeModalImportInstansi()" class="text-gray-400 hover:text-gray-600 transition p-1">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Form & Konten Modal (Scroll Halus & Ringan) -->
            <form method="POST" action="{{ route('administrator.instansi.import.excel') }}" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                
                <div class="flex-1 overflow-y-auto p-5 space-y-3.5 modal-scroll-smooth" style="overscroll-behavior: contain; -webkit-overflow-scrolling: touch;">
                    <!-- Ketentuan Duplikasi Ringkas -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 flex items-center gap-2.5">
                        <i class="fas fa-shield-alt text-amber-600 text-sm flex-shrink-0"></i>
                        <div>
                            <strong class="font-bold">Anti-Duplikasi:</strong>
                            Jika instansi sudah ada di sistem, data lama <strong>tidak akan ditimpa</strong> dan baris tersebut akan <strong>dilewati (di-skip)</strong> secara otomatis.
                        </div>
                    </div>

                    <!-- Banner Template Excel -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center justify-between flex-wrap gap-2">
                        <div class="text-xs text-blue-900">
                            <p class="font-bold flex items-center gap-1.5">
                                <i class="fas fa-file-excel text-emerald-600"></i> Format File Excel
                            </p>
                            <p class="text-blue-700 text-[11px] mt-0.5">Disarankan menggunakan template resmi agar kolom sesuai.</p>
                        </div>
                        <a href="{{ route('administrator.instansi.template.excel') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 hover:text-blue-900 bg-white px-3 py-1.5 rounded-lg border border-blue-300 shadow-sm hover:bg-blue-50 transition">
                            <i class="fas fa-download text-emerald-600"></i> Unduh Template (.xlsx)
                        </a>
                    </div>

                    <!-- Detail Rincian Kolom (Collapsible / Ringan) -->
                    <details class="text-xs text-gray-600 bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                        <summary class="font-semibold text-gray-700 px-3.5 py-2 cursor-pointer hover:bg-gray-100 flex items-center justify-between select-none">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-list-ul text-blue-500"></i> Lihat Susunan Kolom Excel (7 Kolom)
                            </span>
                            <span class="text-gray-400 text-[11px]">Buka / Tutup ▾</span>
                        </summary>
                        <div class="p-3 border-t bg-white">
                            <table class="w-full text-xs text-gray-700">
                                <thead class="text-gray-500 border-b">
                                    <tr>
                                        <th class="py-1 text-left font-semibold">Kolom</th>
                                        <th class="py-1 text-left font-semibold">Field</th>
                                        <th class="py-1 text-left font-semibold">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr><td class="py-1 font-bold text-gray-700">Kolom A</td><td>No</td><td class="text-gray-500">Nomor urut (Opsional)</td></tr>
                                    <tr><td class="py-1 font-bold text-blue-600">Kolom B</td><td class="font-semibold">Nama Instansi</td><td class="text-emerald-600 font-medium">Wajib diisi</td></tr>
                                    <tr><td class="py-1 font-bold text-gray-700">Kolom C</td><td>Kuota PAS</td><td class="text-gray-500">Default: 10</td></tr>
                                    <tr><td class="py-1 font-bold text-gray-700">Kolom D</td><td>Email</td><td class="text-gray-500">Opsional</td></tr>
                                    <tr><td class="py-1 font-bold text-gray-700">Kolom E</td><td>Nomor Telepon</td><td class="text-gray-500">Opsional</td></tr>
                                    <tr><td class="py-1 font-bold text-gray-700">Kolom F</td><td>Alamat</td><td class="text-gray-500">Opsional</td></tr>
                                    <tr><td class="py-1 font-bold text-gray-700">Kolom G</td><td>Status</td><td class="text-gray-500">Aktif / Nonaktif</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </details>

                    <!-- Dropzone Pemilih File (Ringkas) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih File Excel (.xlsx / .xls)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl py-4 px-5 text-center hover:border-amber-500 transition cursor-pointer bg-gray-50 hover:bg-amber-50/20"
                             onclick="document.getElementById('import_instansi_file').click()">
                            <input type="file" name="file" id="import_instansi_file" accept=".xlsx,.xls"
                                   class="hidden" onchange="showModalImportInstansiFileName(this)" required>
                            <i class="fas fa-cloud-upload-alt text-3xl text-amber-500 mb-1"></i>
                            <p class="text-gray-700 font-medium text-xs">Klik untuk memilih file Excel dari komputer</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Format: .xlsx atau .xls (Ukuran maks. 10MB)</p>
                            <p id="modalImportInstansiFileName" class="text-emerald-600 text-xs mt-2 font-semibold"></p>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal (Selalu terlihat di bawah, tanpa perlu scroll) -->
                <div class="flex justify-end gap-2.5 px-6 py-3 border-t bg-gray-50 flex-shrink-0">
                    <button type="button" onclick="closeModalImportInstansi()"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="bg-amber-500 text-white px-5 py-2 rounded-lg hover:bg-amber-600 text-sm font-bold shadow-sm flex items-center gap-1.5 transition">
                        <i class="fas fa-upload"></i> Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Import Instansi
        function openModalImportInstansi() {
            document.getElementById('modalImportInstansiFileName').textContent = '';
            document.getElementById('import_instansi_file').value = '';
            document.getElementById('modalImportInstansi').classList.remove('hidden');
        }

        function closeModalImportInstansi() {
            document.getElementById('modalImportInstansi').classList.add('hidden');
        }

        function showModalImportInstansiFileName(input) {
            const fileName = input.files[0]?.name ?? '';
            document.getElementById('modalImportInstansiFileName').textContent = fileName ? '✅ File Dipilih: ' + fileName : '';
        }
        function openModalTambahInstansi() {
            document.getElementById('modalTambahInstansi').classList.remove('hidden');
        }

        function closeModalTambahInstansi() {
            document.getElementById('modalTambahInstansi').classList.add('hidden');
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

            document.getElementById('modalEditInstansi').classList.remove('hidden');
        }

        function closeModalEditInstansi() {
            document.getElementById('modalEditInstansi').classList.add('hidden');
        }

        function confirmDeleteInstansi(id, nama) {
            SwalConfirm('Hapus Instansi?', `Semua data relasi untuk instansi [${nama}] mungkin terpengaruh. Yakin hapus?`)
            .then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteInstansi-' + id).submit();
                }
            });
        }

        // Tutup modal jika tombol Escape ditekan
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalImportInstansi();
                closeModalTambahInstansi();
                closeModalEditInstansi();
            }
        });
    </script>
</x-app-layout>
