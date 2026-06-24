<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Upload Berkas Persyaratan</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form method="POST" action="{{ route('administrator.berkas-persyaratan.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-1">Nama Berkas</label>
                <input type="text" name="nama_berkas" value="{{ old('nama_berkas') }}"
                       class="block w-full border-gray-300 rounded-lg shadow-sm" required>
                @error('nama_berkas') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="block w-full border-gray-300 rounded-lg shadow-sm">{{ old('keterangan') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block font-medium text-gray-700 mb-1">File (PDF/DOC/DOCX/JPG/PNG, maks 10MB)</label>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                       class="block w-full" required>
                @error('file') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('administrator.berkas-persyaratan.index') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">Batal</a>
                <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Upload</button>
            </div>
        </form>
    </div>
</x-app-layout>
