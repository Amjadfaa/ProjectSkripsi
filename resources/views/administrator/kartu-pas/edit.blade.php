<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Kartu PAS</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-xl p-6 max-w-2xl">

        @if($errors->any())
            <div class="bg-red-100 border border-red-200 text-red-700 p-4 rounded-lg mb-4 text-sm">
                <strong class="font-bold">Gagal Menyimpan Perubahan!</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('administrator.kartu-pas.update', $kartuPas->id) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Kartu</label>
                    <input type="text" name="nomor_kartu" value="{{ old('nomor_kartu', $kartuPas->nomor_kartu) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    @error('nomor_kartu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Pemegang</label>
                    <input type="text" name="nama_pemegang" value="{{ old('nama_pemegang', $kartuPas->nama_pemegang) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    @error('nama_pemegang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Instansi / Perusahaan</label>
                <select name="instansi_id" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    <option value="">-- Pilih Instansi --</option>
                    @foreach($instansiList as $instansi)
                        @php $sisa = $instansi->sisa_kuota; @endphp
                        <option value="{{ $instansi->id }}"
                            {{ old('instansi_id', $kartuPas->instansi_id) == $instansi->id || old('perusahaan', $kartuPas->perusahaan) == $instansi->nama_instansi ? 'selected' : '' }}
                            {{ $sisa <= 0 && $kartuPas->instansi_id != $instansi->id ? 'disabled' : '' }}>
                            {{ $instansi->nama_instansi }} &mdash; (Sisa Kuota: {{ $sisa }} / Total: {{ $instansi->kuota }}) {{ $sisa <= 0 && $kartuPas->instansi_id != $instansi->id ? '[KUOTA HABIS]' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('instansi_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Area Akses Multi-Select -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-600">
                            Area Akses <span class="text-xs font-normal text-amber-600">(Pilih >1 Area)</span>
                        </label>
                        <button type="button" onclick="openModalArea()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-2 py-0.5 rounded text-xs shadow-sm" title="Kelola / Tambah Area Akses">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    </div>
                    <div class="border border-gray-300 rounded-lg p-2 max-h-40 overflow-y-auto bg-gray-50/50 space-y-1" id="select_area_akses">
                        @php
                            $existingAreas = array_map('trim', explode(',', old('area_akses_str', $kartuPas->area_akses ?? '')));
                        @endphp
                        @foreach($areaAksesList as $area)
                            @php $labelFormatted = $area->kode . ': ' . $area->keterangan; @endphp
                            <label class="flex items-center gap-2 p-1.5 rounded hover:bg-white border border-transparent hover:border-gray-200 cursor-pointer text-xs font-medium text-gray-700 transition" title="{{ $labelFormatted }}">
                                <input type="checkbox" name="area_akses[]" value="{{ $area->kode }}"
                                       class="rounded text-amber-600 focus:ring-amber-500"
                                       {{ in_array($area->kode, $existingAreas) ? 'checked' : '' }}>
                                <span class="font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded shrink-0">{{ $area->kode }}</span>
                                <span class="truncate">{{ $area->keterangan }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('area_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jabatan Dropdown & Modal Button -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan</label>
                    <div class="flex gap-1.5">
                        <select name="jabatan" id="select_jabatan" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($jabatanList as $jbt)
                                <option value="{{ $jbt->nama_jabatan }}" data-id="{{ $jbt->id }}" {{ old('jabatan', $kartuPas->jabatan) == $jbt->nama_jabatan ? 'selected' : '' }}>
                                    {{ $jbt->nama_jabatan }}
                                </option>
                            @endforeach
                            @if(!in_array(old('jabatan', $kartuPas->jabatan), $jabatanList->pluck('nama_jabatan')->toArray()) && $kartuPas->jabatan)
                                <option value="{{ $kartuPas->jabatan }}" selected>{{ $kartuPas->jabatan }}</option>
                            @endif
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

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Terbit</label>
                    <input type="date" name="tanggal_terbit"
                           value="{{ old('tanggal_terbit', optional($kartuPas->tanggal_terbit)->format('Y-m-d')) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    @error('tanggal_terbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Berlaku</label>
                    <input type="date" name="tanggal_berlaku"
                           value="{{ old('tanggal_berlaku', optional($kartuPas->tanggal_berlaku)->format('Y-m-d')) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    @error('tanggal_berlaku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                    <select name="status" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        <option value="aktif" {{ old('status', $kartuPas->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status', $kartuPas->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="kadaluarsa" {{ old('status', $kartuPas->status) == 'kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('administrator.kartu-pas.index') }}"
                   class="bg-gray-400 text-white px-5 py-2 rounded-lg hover:bg-gray-500 text-sm">Batal</a>
                <button type="submit"
                        class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">
                    Simpan Perubahan
                </button>
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
                    const select = document.getElementById('select_area_akses');
                    const opt = document.createElement('option');
                    opt.value = result.data.value;
                    opt.setAttribute('data-id', result.data.id);
                    opt.setAttribute('title', result.data.label);
                    opt.textContent = result.data.label.length > 60 ? result.data.label.substring(0, 60) + '...' : result.data.label;
                    opt.selected = true;
                    select.appendChild(opt);

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