<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Permohonan Saya</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif

    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex justify-between mb-4">
            <h3 class="font-semibold text-lg">Daftar Permohonan</h3>
            <a href="{{ route('pemohon.permohonan.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                + Ajukan Permohonan
            </a>
        </div>

        @if($permohonan->isEmpty())
            <p class="text-gray-500">Belum ada permohonan.</p>
        @else
            <table class="w-full text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border">No. Permohonan</th>
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
                            <div class="flex gap-2">
                                <a href="{{ route('pemohon.permohonan.show', $item->id) }}"
                                   class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 text-sm">
                                    Detail
                                </a>
                                <form method="POST" action="{{ route('pemohon.permohonan.destroy', $item->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus permohonan ini?')"
                                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>