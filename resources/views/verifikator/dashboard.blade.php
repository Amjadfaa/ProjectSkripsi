<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dashboard Verifikator</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Statistik -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-3xl font-bold text-blue-600">{{ App\Models\Permohonan::count() }}</p>
                    <p class="text-gray-500 mt-1">Total Permohonan</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ App\Models\Permohonan::where('status', 'menunggu')->count() }}</p>
                    <p class="text-gray-500 mt-1">Menunggu Verifikasi</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ App\Models\Permohonan::where('status', 'disetujui')->count() }}</p>
                    <p class="text-gray-500 mt-1">Disetujui</p>
                </div>
            </div>

            <!-- Menu -->
            <div class="grid grid-cols-1 gap-4">
                <a href="{{ route('verifikator.permohonan.index') }}"
                   class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
                    <h3 class="font-semibold text-lg text-blue-600">📋 Verifikasi Permohonan</h3>
                    <p class="text-gray-500 mt-1">Lihat dan verifikasi berkas permohonan masuk</p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>