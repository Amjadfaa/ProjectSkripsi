<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Data Instansi</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                <i class="fas fa-building text-blue-600"></i> Daftar Instansi / Perusahaan
            </h3>
            <button type="button" onclick="openModalTambahInstansi()"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-semibold text-sm shadow-sm flex items-center gap-1.5 cursor-pointer transition">
                <i class="fas fa-plus"></i> Tambah Instansi
            </button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 p-4 rounded-lg mb-4 text-sm font-medium">
                ✅ {{ session('success') }}
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
                            $terpakai = $instansi->kartu_pas_count ?? $instansi->kartuPas()->count();
                            $sisa     = $instansi->kuota - $terpakai;
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-semibold text-gray-800">{{ $instansi->nama_instansi }}</td>
                            <td class="p-3 font-medium text-blue-600">{{ $instansi->kuota }} Kartu</td>
                            <td class="p-3 font-medium text-purple-600">{{ $terpakai }} Kartu</td>
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
    <div id="modalTambahInstansi" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
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
    <div id="modalEditInstansi" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
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

    <script>
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
    </script>
</x-app-layout>
