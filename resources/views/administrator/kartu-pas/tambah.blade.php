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
                <label class="block text-sm font-medium text-gray-600 mb-1">Email Pemegang</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="Email untuk notifikasi"
                    class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block font-medium text-gray-700">Nama Pemegang</label>
                <input type="text" name="nama_pemegang" value="{{ old('nama_pemegang') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('nama_pemegang') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-700">Instansi / Perusahaan</label>
                <input type="text" name="perusahaan" value="{{ old('perusahaan') }}"
                    placeholder="Masukkan nama instansi/perusahaan"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @error('perusahaan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Area Akses</label>
                    <input type="text" name="area_akses" value="{{ old('area_akses') }}"
                        placeholder="Contoh: Terminal, Apron"
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    @error('area_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                        placeholder="Contoh: Manager, Staff"
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    @error('jabatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
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
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('administrator.kartu-pas.index') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</a>
                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan Kartu PAS</button>
            </div>

        </form>
    </div>
</x-app-layout>
