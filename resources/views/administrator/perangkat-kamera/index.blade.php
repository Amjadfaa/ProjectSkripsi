<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Pengaturan Perangkat Kamera Scan</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-xl p-6">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-2">
            <div>
                <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                    <i class="fas fa-video text-blue-600"></i> Pengaturan Kode Akses Kamera per Area
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Atur kode akses login unik untuk masing-masing titik kamera di area bandara</p>
            </div>
            <button type="button" onclick="openModalTambahKamera()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-semibold text-sm shadow-sm flex items-center gap-1.5 transition">
                <i class="fas fa-plus"></i> Tambah Perangkat Kamera
            </button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 p-4 rounded-lg mb-4 text-sm font-medium">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-100 border border-rose-200 text-rose-700 p-4 rounded-lg mb-4 text-sm">
                <strong class="font-bold">Gagal Menyimpan Data!</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($devices->isEmpty())
            <p class="text-gray-500 py-8 text-center">Belum ada perangkat kamera yang didaftarkan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-3 font-semibold text-gray-700">Nama Perangkat Kamera</th>
                            <th class="p-3 font-semibold text-gray-700">Area Akses Ditugaskan</th>
                            <th class="p-3 font-semibold text-gray-700">Kode Akses Login</th>
                            <th class="p-3 font-semibold text-gray-700">Tipe Scan</th>
                            <th class="p-3 font-semibold text-gray-700">Status</th>
                            <th class="p-3 font-semibold text-gray-700 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $device)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-bold text-gray-800">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $device->nama_kamera }}</p>
                                        <p class="text-[11px] text-gray-400">ID: #CAM-{{ $device->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 font-semibold text-blue-700">
                                <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs font-bold mr-1">{{ $device->kode_area }}</span>
                                {{ optional($device->areaAkses)->keterangan ?? '-' }}
                            </td>
                            <td class="p-3">
                                <code class="bg-gray-100 border border-gray-200 px-2.5 py-1 rounded text-xs font-mono font-bold text-purple-700">
                                    {{ $device->kode_akses }}
                                </code>
                            </td>
                            <td class="p-3 text-xs capitalize text-gray-600">
                                {{ str_replace('_', ' ', $device->tipe_scan) }}
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $device->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $device->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick='openModalEditKamera(@json($device))'
                                            class="bg-amber-500 hover:bg-amber-600 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                            title="Edit Perangkat">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form id="formDeleteKamera-{{ $device->id }}" method="POST" action="{{ route('administrator.perangkat-kamera.destroy', $device->id) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDeleteKamera({{ $device->id }}, '{{ $device->nama_kamera }}')"
                                                class="bg-rose-600 hover:bg-rose-700 text-white w-8 h-8 rounded-lg text-xs flex items-center justify-center transition shadow-sm"
                                                title="Hapus Perangkat">
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

    <!-- MODAL TAMBAH PERANGKAT KAMERA -->
    <div id="modalTambahKamera" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative max-h-[95vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-camera text-blue-600"></i> Tambah Perangkat Kamera Baru
                </h3>
                <button type="button" onclick="closeModalTambahKamera()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('administrator.perangkat-kamera.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Perangkat Kamera</label>
                    <input type="text" name="nama_kamera" value="{{ old('nama_kamera') }}"
                           placeholder="Contoh: Kamera Gate 1 - Kedatangan"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold text-gray-700">Area Akses Ditugaskan</label>
                            <button type="button" onclick="openModalAreaAkses()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-2 py-0.5 rounded text-xs shadow-sm flex items-center gap-1 transition" title="Kelola / Tambah Area Akses Baru">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                        <div class="flex gap-1.5">
                            <select name="kode_area" id="select_kode_area" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                                <option value="">-- Pilih Area Akses --</option>
                                @foreach($areaAksesList as $area)
                                    <option value="{{ $area->kode }}" data-id="{{ $area->id }}" {{ old('kode_area') == $area->kode ? 'selected' : '' }}>
                                        {{ $area->kode }}: {{ \Illuminate\Support\Str::limit($area->keterangan, 35) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openModalAreaAkses()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-2 rounded-lg text-xs flex items-center justify-center shrink-0 shadow-sm transition" title="Tambah Area Akses Baru">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Akses Login</label>
                        <input type="text" name="kode_akses" value="{{ old('kode_akses') }}"
                               placeholder="Contoh: CAM-AREA-A"
                               class="block w-full border-gray-300 rounded-lg shadow-sm text-sm font-mono font-bold" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Scan</label>
                        <select name="tipe_scan" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="masuk_keluar" {{ old('tipe_scan', 'masuk_keluar') == 'masuk_keluar' ? 'selected' : '' }}>Masuk & Keluar</option>
                            <option value="masuk" {{ old('tipe_scan') == 'masuk' ? 'selected' : '' }}>Masuk Saja</option>
                            <option value="keluar" {{ old('tipe_scan') == 'keluar' ? 'selected' : '' }}>Keluar Saja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Perangkat</label>
                        <select name="is_active" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModalTambahKamera()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-bold shadow-md">
                        Simpan Perangkat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PERANGKAT KAMERA -->
    <div id="modalEditKamera" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative max-h-[95vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-4 border-b mb-4">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-edit text-amber-500"></i> Edit Perangkat Kamera
                </h3>
                <button type="button" onclick="closeModalEditKamera()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="formEditKamera" method="POST" action="">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Perangkat Kamera</label>
                    <input type="text" id="edit_nama_kamera" name="nama_kamera" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold text-gray-700">Area Akses Ditugaskan</label>
                            <button type="button" onclick="openModalAreaAkses()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-2 py-0.5 rounded text-xs shadow-sm flex items-center gap-1 transition" title="Kelola / Tambah Area Akses Baru">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                        <div class="flex gap-1.5">
                            <select id="edit_kode_area" name="kode_area" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                                <option value="">-- Pilih Area Akses --</option>
                                @foreach($areaAksesList as $area)
                                    <option value="{{ $area->kode }}" data-id="{{ $area->id }}">
                                        {{ $area->kode }}: {{ \Illuminate\Support\Str::limit($area->keterangan, 35) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openModalAreaAkses()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-2 rounded-lg text-xs flex items-center justify-center shrink-0 shadow-sm transition" title="Tambah Area Akses Baru">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Akses Login</label>
                        <input type="text" id="edit_kode_akses" name="kode_akses" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm font-mono font-bold" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Scan</label>
                        <select id="edit_tipe_scan" name="tipe_scan" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="masuk_keluar">Masuk & Keluar</option>
                            <option value="masuk">Masuk Saja</option>
                            <option value="keluar">Keluar Saja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Perangkat</label>
                        <select id="edit_is_active" name="is_active" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModalEditKamera()"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit"
                            class="bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600 text-sm font-bold shadow-md">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KELOLA / TAMBAH AREA AKSES -->
    <div id="modalAreaAkses" class="fixed inset-0 z-[70] hidden bg-gray-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-blue-600"></i> Kelola & Tambah Area Akses
                </h3>
                <button type="button" onclick="closeModalAreaAkses()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="formAreaAkses" onsubmit="submitAreaAkses(event)" class="mb-4">
                <div class="grid grid-cols-3 gap-2 mb-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Kode (Contoh: W)</label>
                        <input type="text" id="modal_kode_akses" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm uppercase font-bold" placeholder="Kode" required>
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
                <button type="button" onclick="closeModalAreaAkses()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 font-medium">Selesai</button>
            </div>
        </div>
    </div>

    <script>
        function openModalTambahKamera() {
            document.getElementById('modalTambahKamera').classList.remove('hidden');
        }

        function closeModalTambahKamera() {
            document.getElementById('modalTambahKamera').classList.add('hidden');
        }

        function openModalEditKamera(device) {
            const form = document.getElementById('formEditKamera');
            form.action = "{{ url('/administrator/perangkat-kamera') }}/" + device.id;

            document.getElementById('edit_nama_kamera').value = device.nama_kamera || '';
            document.getElementById('edit_kode_area').value   = device.kode_area || '';
            document.getElementById('edit_kode_akses').value  = device.kode_akses || '';
            document.getElementById('edit_tipe_scan').value   = device.tipe_scan || 'masuk_keluar';
            document.getElementById('edit_is_active').value   = device.is_active ? '1' : '0';

            document.getElementById('modalEditKamera').classList.remove('hidden');
        }

        function closeModalEditKamera() {
            document.getElementById('modalEditKamera').classList.add('hidden');
        }

        function confirmDeleteKamera(id, nama) {
            SwalConfirm('Hapus Perangkat Kamera?', `Yakin ingin menghapus [${nama}]? Kamera ini tidak akan bisa login untuk scan QR lagi!`)
            .then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formDeleteKamera-' + id).submit();
                }
            });
        }

        function openModalAreaAkses() {
            document.getElementById('modal_kode_akses').value = '';
            document.getElementById('modal_keterangan_akses').value = '';
            document.getElementById('areaAksesError').classList.add('hidden');
            document.getElementById('modalAreaAkses').classList.remove('hidden');
        }

        function closeModalAreaAkses() {
            document.getElementById('modalAreaAkses').classList.add('hidden');
        }

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
                    const labelDisplay = `${result.data.kode}: ${result.data.keterangan}`;

                    // Update Dropdown in Tambah Modal
                    const selectTambah = document.getElementById('select_kode_area');
                    if (selectTambah) {
                        const opt = document.createElement('option');
                        opt.value = result.data.kode;
                        opt.setAttribute('data-id', result.data.id);
                        opt.textContent = labelDisplay;
                        opt.selected = true;
                        selectTambah.appendChild(opt);
                    }

                    // Update Dropdown in Edit Modal
                    const selectEdit = document.getElementById('edit_kode_area');
                    if (selectEdit) {
                        const optEdit = document.createElement('option');
                        optEdit.value = result.data.kode;
                        optEdit.setAttribute('data-id', result.data.id);
                        optEdit.textContent = labelDisplay;
                        selectEdit.appendChild(optEdit);
                    }

                    // Update Modal List
                    const list = document.getElementById('modal_area_list');
                    if (list) {
                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'flex items-center justify-between bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded border text-sm';
                        itemDiv.id = 'item-area-' + result.data.id;
                        itemDiv.innerHTML = `
                            <span class="font-medium text-gray-800">${result.data.kode}: ${result.data.keterangan}</span>
                            <button type="button" onclick="deleteAreaById(${result.data.id}, '${result.data.kode}')" class="text-red-500 hover:text-red-700 p-1 text-xs" title="Hapus Area Ini">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
                        list.appendChild(itemDiv);
                    }

                    document.getElementById('modal_kode_akses').value = '';
                    document.getElementById('modal_keterangan_akses').value = '';
                    errDiv.classList.add('hidden');
                    closeModalAreaAkses();
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

                    // Remove from Select in Tambah Modal
                    const selectTambah = document.getElementById('select_kode_area');
                    if (selectTambah) {
                        for (let i = 0; i < selectTambah.options.length; i++) {
                            if (selectTambah.options[i].getAttribute('data-id') == id || selectTambah.options[i].value == kode) {
                                selectTambah.remove(i);
                                break;
                            }
                        }
                    }

                    // Remove from Select in Edit Modal
                    const selectEdit = document.getElementById('edit_kode_area');
                    if (selectEdit) {
                        for (let i = 0; i < selectEdit.options.length; i++) {
                            if (selectEdit.options[i].getAttribute('data-id') == id || selectEdit.options[i].value == kode) {
                                selectEdit.remove(i);
                                break;
                            }
                        }
                    }
                } else {
                    alert(result.message || 'Gagal menghapus area akses');
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi.');
            }
        }
    </script>
</x-app-layout>
