
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Daftar Permohonan Masuk</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                @if($permohonan->isEmpty())
                    <p class="text-gray-500">Belum ada permohonan masuk.</p>
                @else
                    <table class="w-full text-left border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 border">No. Permohonan</th>
                                <th class="p-3 border">Nama Pemohon</th>
                                <th class="p-3 border">Perusahaan</th>
                                <th class="p-3 border">Keperluan</th>
                                <th class="p-3 border">Status</th>
                                <th class="p-3 border">Tanggal</th>
                                <th class="p-3 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permohonan as $item)
                            <tr>
                                <td class="p-3 border">{{ $item->nomor_permohonan }}</td>
                                <td class="p-3 border">{{ $item->nama_pemohon }}</td>
                                <td class="p-3 border">{{ $item->perusahaan }}</td>
                                <td class="p-3 border">{{ $item->keperluan }}</td>
                                <td class="p-3 border">
                                    @php
                                        $badge = [
                                            'menunggu'  => 'bg-yellow-100 text-yellow-700',
                                            'diproses'  => 'bg-blue-100 text-blue-700',
                                            'disetujui' => 'bg-green-100 text-green-700',
                                            'ditolak'   => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-sm {{ $badge[$item->status] }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="p-3 border">{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="p-3 border">
                                    <a href="{{ route('verifikator.permohonan.show', $item->id) }}"
                                       class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                        Verifikasi
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>