<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detail Permohonan</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="mb-6">
                    <p><span class="font-semibold">No. Permohonan:</span> {{ $permohonan->nomor_permohonan }}</p>
                    <p><span class="font-semibold">Keperluan:</span> {{ $permohonan->keperluan }}</p>
                    <p><span class="font-semibold">Perusahaan:</span> {{ $permohonan->perusahaan }}</p>
                    <p><span class="font-semibold">Status:</span> {{ ucfirst($permohonan->status) }}</p>
                    @if($permohonan->catatan)
                        <p><span class="font-semibold">Catatan:</span> {{ $permohonan->catatan }}</p>
                    @endif
                </div>

                <h3 class="font-semibold text-lg mb-3">Berkas yang Diupload</h3>
                <table class="w-full text-left border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border">Nama Berkas</th>
                            <th class="p-3 border">Status Verifikasi</th>
                            <th class="p-3 border">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permohonan->berkas as $berkas)
                        <tr>
                            <td class="p-3 border">{{ $berkas->nama_berkas }}</td>
                            <td class="p-3 border">
                                @php
                                    $badge = [
                                        'belum_diverifikasi' => 'bg-yellow-100 text-yellow-700',
                                        'diverifikasi'       => 'bg-green-100 text-green-700',
                                        'ditolak'            => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded text-sm {{ $badge[$berkas->status] }}">
                                    {{ ucfirst(str_replace('_', ' ', $berkas->status)) }}
                                </span>
                            </td>
                            <td class="p-3 border">{{ $berkas->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    <a href="{{ route('pemohon.permohonan.index') }}"
                       class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>