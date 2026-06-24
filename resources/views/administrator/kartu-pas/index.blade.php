<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Data Kartu PAS</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow mb-4">
        <div class="bg-blue-500 rounded-t-lg px-6 py-4 flex justify-between items-center flex-wrap gap-2">
            <h3 class="text-white font-bold text-lg">🪪 Data Kartu PAS</h3>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('administrator.import.index') }}"
                class="bg-yellow-400 text-white px-3 py-2 rounded-lg text-sm hover:bg-yellow-500">
                    <i class="fas fa-file-import mr-1"></i> Import
                </a>
                <a href="{{ route('administrator.kartu-pas.export.excel', request()->query()) }}"
                   class="bg-green-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-600">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </a>
                <a href="{{ route('administrator.kartu-pas.export.pdf', request()->query()) }}"
                   class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600">
                    <i class="fas fa-file-pdf mr-1"></i> PDF
                </a>
                <a href="{{ route('administrator.kartu-pas.tambah') }}"
                   class="bg-white text-blue-600 px-3 py-2 rounded-lg text-sm font-medium hover:bg-blue-50">
                    + Tambah Kartu
                </a>
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
                                <option value="{{ $instansi }}" {{ request('instansi') == $instansi ? 'selected' : '' }}>
                                    {{ $instansi }}
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
                    <button type="button" onclick="deleteSelected()"
                            class="bg-orange-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-orange-600 hidden"
                            id="btnDeleteSelected">
                        <i class="fas fa-trash mr-1"></i> Hapus Dipilih
                    </button>
                </form>

                <!-- Hapus Semua -->
                <form method="POST" action="{{ route('administrator.kartu-pas.destroy-all') }}"
                      onsubmit="return confirm('⚠️ PERINGATAN! Semua data kartu PAS akan dihapus permanen. Yakin?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-700">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Semua
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="px-4 py-3 text-center w-10"></th>
                        <th class="px-4 py-3 text-left text-gray-600">No.Registrasi</th>
                        <th class="px-4 py-3 text-left text-gray-600">Nama Pemegang</th>
                        <th class="px-4 py-3 text-left text-gray-600">Instansi</th>
                        <th class="px-4 py-3 text-left text-gray-600">Area Akses</th>
                        <th class="px-4 py-3 text-left text-gray-600">Jabatan</th>
                        <th class="px-4 py-3 text-left text-gray-600">Masa Berlaku</th>
                        <th class="px-4 py-3 text-left text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-gray-600">Aksi</th>
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
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <a href="{{ route('administrator.kartu-pas.edit', $kartu->id) }}"
                                   class="bg-blue-500 text-white px-3 py-1 rounded text-xs text-center hover:bg-blue-600">
                                    Perpanjangan
                                </a>
                                <a href="{{ route('administrator.kartu-pas.edit', $kartu->id) }}"
                                   class="bg-yellow-400 text-white px-3 py-1 rounded text-xs text-center hover:bg-yellow-500">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('administrator.kartu-pas.destroy', $kartu->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin hapus?')"
                                            class="w-full bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">
                                        Hapus
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

    <script>
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

            // Update check all
            const all = document.querySelectorAll('.kartu-checkbox');
            document.getElementById('checkAll').checked = count === all.length && all.length > 0;
        }

        function deleteSelected() {
            const checked = document.querySelectorAll('.kartu-checkbox:checked');
            if (checked.length === 0) return;

            if (!confirm(`Yakin ingin menghapus ${checked.length} kartu PAS yang dipilih?`)) return;

            const form    = document.getElementById('formDeleteSelected');
            const inputs  = document.getElementById('selectedInputs');
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
    </script>

</x-app-layout>