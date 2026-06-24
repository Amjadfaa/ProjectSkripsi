<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Ajukan Permohonan</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('pemohon.permohonan.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Keperluan</label>
                        <input type="text" name="keperluan" value="{{ old('keperluan') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('keperluan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4" id="berkas-container">
                        <label class="block font-medium text-gray-700 mb-2">Upload Berkas Persyaratan</label>
                        <div class="berkas-item border rounded p-3 mb-2">
                            <input type="text" name="nama_berkas[]" placeholder="Nama Berkas (contoh: KTP)"
                                   class="block w-full border-gray-300 rounded-md shadow-sm mb-2" required>
                            <input type="file" name="berkas[]" accept=".pdf,.jpg,.jpeg,.png"
                                   class="block w-full" required>
                        </div>
                    </div>

                    <button type="button" onclick="tambahBerkas()"
                            class="mb-4 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                        + Tambah Berkas
                    </button>

                    <div class="flex justify-end">
                        <a href="{{ route('pemohon.permohonan.index') }}"
                           class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 mr-2">Batal</a>
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Ajukan Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function tambahBerkas() {
            const container = document.getElementById('berkas-container');
            const div = document.createElement('div');
            div.className = 'berkas-item border rounded p-3 mb-2';
            div.innerHTML = `
                <input type="text" name="nama_berkas[]" placeholder="Nama Berkas"
                       class="block w-full border-gray-300 rounded-md shadow-sm mb-2" required>
                <input type="file" name="berkas[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="block w-full" required>
                <button type="button" onclick="this.parentElement.remove()"
                        class="mt-2 text-red-500 text-sm">Hapus</button>
            `;
            container.appendChild(div);
        }
    </script>
</x-app-layout>