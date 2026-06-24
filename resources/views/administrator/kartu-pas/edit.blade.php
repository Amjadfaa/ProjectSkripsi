<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Kartu PAS</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-xl p-6 max-w-2xl">

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                <ul>
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
                <label class="block text-sm font-medium text-gray-600 mb-1">Email Pemegang</label>
                <input type="email" name="email" value="{{ old('email', $kartuPas->email) }}"
                    placeholder="Email untuk notifikasi"
                    class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Instansi / Perusahaan</label>
                    <select name="perusahaan" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        <option value="">-- Pilih Instansi --</option>
                        @foreach($instansiList as $instansi)
                            <option value="{{ $instansi->nama_instansi }}"
                                {{ old('perusahaan', $kartuPas->perusahaan) == $instansi->nama_instansi ? 'selected' : '' }}>
                                {{ $instansi->nama_instansi }}
                            </option>
                        @endforeach
                    </select>
                    @error('perusahaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $kartuPas->jabatan) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Area Akses</label>
                <input type="text" name="area_akses" value="{{ old('area_akses', $kartuPas->area_akses) }}"
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                @error('area_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Terbit</label>
                    <input type="date" name="tanggal_terbit"
                           value="{{ old('tanggal_terbit', $kartuPas->tanggal_terbit->format('Y-m-d')) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    @error('tanggal_terbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Berlaku</label>
                    <input type="date" name="tanggal_berlaku"
                           value="{{ old('tanggal_berlaku', $kartuPas->tanggal_berlaku->format('Y-m-d')) }}"
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
</x-app-layout>