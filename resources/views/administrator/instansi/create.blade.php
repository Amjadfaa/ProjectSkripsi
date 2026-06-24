<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Tambah Instansi</h2>
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

        <form method="POST" action="{{ route('administrator.instansi.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Instansi</label>
                    <input type="text" name="nama_instansi" value="{{ old('nama_instansi') }}"
                           placeholder="Contoh: PT. Garuda Indonesia"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                    @error('nama_instansi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Telepon</label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}"
                           placeholder="Contoh: 08123456789"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    @error('telepon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Email Instansi</label>
                <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="Email untuk notifikasi"
                            class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
                <textarea name="alamat" rows="2"
                          placeholder="Masukkan alamat instansi"
                          class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">{{ old('alamat') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-1">Kuota PAS</label>
                <input type="number" name="kuota" value="{{ old('kuota', 0) }}" min="0"
                       class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('administrator.instansi.index') }}"
                   class="bg-gray-400 text-white px-5 py-2 rounded-lg hover:bg-gray-500 text-sm">Batal</a>
                <button type="submit"
                        class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</x-app-layout>