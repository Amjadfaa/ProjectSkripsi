<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Import Data Kartu PAS</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">❌ {{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 max-w-2xl">
        <h3 class="font-bold text-lg text-gray-800 mb-2">📥 Upload File Excel</h3>
        <p class="text-sm text-gray-500 mb-6">
            Upload file Excel data kartu PAS. Sistem akan otomatis membaca data dari setiap sheet bulan.
        </p>

        <!-- Format -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="font-semibold text-blue-800 text-sm mb-2">📋 Format Kolom yang Didukung:</p>
            <table class="w-full text-xs text-blue-700">
                <thead>
                    <tr class="border-b border-blue-200">
                        <th class="py-1 text-left">Kolom</th>
                        <th class="py-1 text-left">Isi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="py-1 font-medium">Kolom D</td><td>Nama Pemegang</td></tr>
                    <tr><td class="py-1 font-medium">Kolom E</td><td>No. Registrasi / Nomor Kartu</td></tr>
                    <tr><td class="py-1 font-medium">Kolom F</td><td>Area Akses</td></tr>
                    <tr><td class="py-1 font-medium">Kolom G</td><td>Jabatan</td></tr>
                    <tr><td class="py-1 font-medium">Kolom H</td><td>Masa Berlaku (contoh: 30 MEI 2026)</td></tr>
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('administrator.import.kartu-pas') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-2">File Excel (.xlsx / .xls)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition cursor-pointer"
                     onclick="document.getElementById('file').click()">
                    <input type="file" name="file" id="file" accept=".xlsx,.xls"
                           class="hidden" onchange="showFileName(this)" required>
                    <p class="text-4xl mb-2">📊</p>
                    <p class="text-gray-500 text-sm">Klik untuk pilih file Excel</p>
                    <p class="text-gray-400 text-xs mt-1">.xlsx atau .xls (maks. 10MB)</p>
                    <p id="fileName" class="text-blue-600 text-sm mt-3 font-medium"></p>
                </div>
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('administrator.kartu-pas.index') }}"
                   class="bg-gray-400 text-white px-5 py-2 rounded-lg hover:bg-gray-500 text-sm">Batal</a>
                <button type="submit"
                        class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">
                    <i class="fas fa-upload mr-1"></i> Import Sekarang
                </button>
            </div>
        </form>
    </div>

    <script>
        function showFileName(input) {
            const fileName = input.files[0]?.name ?? '';
            document.getElementById('fileName').textContent = fileName ? '✅ ' + fileName : '';
        }
    </script>
</x-app-layout>
