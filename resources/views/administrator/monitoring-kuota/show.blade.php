<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detail Kuota - {{ $instansi->nama_instansi }}</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif

    <!-- Info Instansi -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="grid grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-800">{{ $instansi->kuota }}</p>
                <p class="text-sm text-gray-500">Total Kuota</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600">{{ $kartuPas->where('status', 'aktif')->count() }}</p>
                <p class="text-sm text-gray-500">Kartu Aktif</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $instansi->kuota - $kartuPas->where('status', 'aktif')->count() }}</p>
                <p class="text-sm text-gray-500">Sisa Kuota</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-500">{{ $kartuPas->where('status', '!=', 'aktif')->count() }}</p>
                <p class="text-sm text-gray-500">Nonaktif</p>
            </div>
        </div>
    </div>

    <!-- Daftar Kartu PAS -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg text-gray-800">Daftar Kartu PAS</h3>
            <a href="{{ route('administrator.monitoring-kuota.index') }}"
               class="text-blue-500 text-sm hover:underline">← Kembali</a>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3 text-left">No. Kartu</th>
                    <th class="p-3 text-left">Nama Pemegang</th>
                    <th class="p-3 text-left">Area Akses</th>
                    <th class="p-3 text-left">Masa Berlaku</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Keterangan</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kartuPas as $kartu)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $kartu->nomor_kartu }}</td>
                    <td class="p-3">{{ $kartu->nama_pemegang }}</td>
                    <td class="p-3">{{ $kartu->area_akses }}</td>
                    <td class="p-3">{{ $kartu->tanggal_berlaku->format('d/m/Y') }}</td>
                    <td class="p-3">
                        @php
                            $badge = [
                                'aktif'       => 'bg-green-100 text-green-700',
                                'tidak_aktif' => 'bg-gray-100 text-gray-700',
                                'kadaluarsa'  => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge[$kartu->status] }}">
                            {{ ucfirst(str_replace('_', ' ', $kartu->status)) }}
                        </span>
                    </td>
                    <td class="p-3">
                        @if($kartu->keterangan_nonaktif)
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                {{ ucfirst($kartu->keterangan_nonaktif) }}
                            </span>
                            @if($kartu->catatan_nonaktif)
                                <p class="text-xs text-gray-400 mt-1">{{ $kartu->catatan_nonaktif }}</p>
                            @endif
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($kartu->status === 'aktif')
                        <button onclick="openModal({{ $kartu->id }})"
                                class="bg-red-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-red-600">
                            Nonaktifkan
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-400">Belum ada data kartu PAS.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Nonaktifkan -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
            <h3 class="font-bold text-lg text-gray-800 mb-4">⚠️ Nonaktifkan Kartu PAS</h3>
            <form method="POST" id="formNonaktif">
                @csrf
                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">Keterangan</label>
                    <select name="keterangan_nonaktif" class="block w-full border-gray-300 rounded-lg shadow-sm" required>
                        <option value="">-- Pilih Keterangan --</option>
                        <option value="resign">Resign</option>
                        <option value="pensiun">Pensiun</option>
                        <option value="meninggal">Meninggal</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan_nonaktif"
                           class="block w-full border-gray-300 rounded-lg shadow-sm"
                           placeholder="Tambahkan catatan...">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                            class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">Batal</button>
                    <button type="submit"
                            class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Nonaktifkan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById('formNonaktif').action = '/administrator/monitoring-kuota/nonaktifkan/' + id;
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
    </script>

</x-app-layout>
