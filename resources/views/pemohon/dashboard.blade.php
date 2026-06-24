<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Dashboard Pemohon</h2>
    </x-slot>

    <!-- Selamat Datang -->
    <div class="bg-white rounded-xl p-6 mb-6 border border-gray-200 shadow">
        <div class="flex items-center gap-4">
            <div style="width:56px; height:56px; background:#1e3a5f; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800; color:#f0b429;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-gray-500 text-sm mt-1">{{ auth()->user()->perusahaan ?? '-' }} &nbsp;|&nbsp; {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
            </div>
        </div>
    </div>
    <!-- Statistik -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Permohonan</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ auth()->user()->permohonan()->count() }}</p>
                </div>
                <div style="width:48px; height:48px; background:#eff6ff; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px;">
                    📋
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ auth()->user()->permohonan()->where('status', 'disetujui')->count() }}</p>
                </div>
                <div style="width:48px; height:48px; background:#f0fdf4; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px;">
                    ✅
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Menunggu</p>
                    <p class="text-3xl font-bold text-yellow-500 mt-1">{{ auth()->user()->permohonan()->where('status', 'menunggu')->count() }}</p>
                </div>
                <div style="width:48px; height:48px; background:#fefce8; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px;">
                    ⏳
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Akses Cepat -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <a href="{{ route('pemohon.berkas-persyaratan.index') }}"
           class="bg-white rounded-xl shadow p-5 hover:shadow-md transition flex items-center gap-4">
            <div style="width:52px; height:52px; background:#eff6ff; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                📥
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Berkas Persyaratan</h3>
                <p class="text-gray-500 text-sm mt-1">Download berkas persyaratan pengajuan PAS</p>
            </div>
        </a>

        <a href="{{ route('pemohon.permohonan.create') }}"
           class="bg-white rounded-xl shadow p-5 hover:shadow-md transition flex items-center gap-4">
            <div style="width:52px; height:52px; background:#f0fdf4; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                📝
            </div>
            <div>
                <h3 class="font-bold text-gray-800">Ajukan Permohonan</h3>
                <p class="text-gray-500 text-sm mt-1">Buat permohonan PAS baru</p>
            </div>
        </a>
    </div>

    <!-- Permohonan Terbaru -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800">📋 Permohonan Terbaru</h3>
            <a href="{{ route('pemohon.permohonan.index') }}"
               class="text-blue-500 text-sm hover:underline">Lihat Semua →</a>
        </div>

        @php
            $permohonan = auth()->user()->permohonan()->latest()->take(5)->get();
        @endphp

        @if($permohonan->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <p class="text-4xl mb-2">📭</p>
                <p>Belum ada permohonan.</p>
                <a href="{{ route('pemohon.permohonan.create') }}"
                   class="mt-3 inline-block bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600">
                    Ajukan Sekarang
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="pb-3 text-left text-gray-500 font-medium">No. Permohonan</th>
                        <th class="pb-3 text-left text-gray-500 font-medium">Keperluan</th>
                        <th class="pb-3 text-left text-gray-500 font-medium">Status</th>
                        <th class="pb-3 text-left text-gray-500 font-medium">Tanggal</th>
                        <th class="pb-3 text-left text-gray-500 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permohonan as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 font-medium text-gray-700">{{ $item->nomor_permohonan }}</td>
                        <td class="py-3 text-gray-600">{{ $item->keperluan }}</td>
                        <td class="py-3">
                            @php
                                $badge = [
                                    'menunggu'  => 'bg-yellow-100 text-yellow-700',
                                    'diproses'  => 'bg-blue-100 text-blue-700',
                                    'disetujui' => 'bg-green-100 text-green-700',
                                    'ditolak'   => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge[$item->status] }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-500">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="py-3">
                            <a href="{{ route('pemohon.permohonan.show', $item->id) }}"
                               class="text-blue-500 hover:underline text-xs">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</x-app-layout>