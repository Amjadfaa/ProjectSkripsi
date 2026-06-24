<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Data Instansi</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex justify-between mb-4">
            <h3 class="font-semibold text-lg">Daftar Instansi</h3>
            <a href="{{ route('administrator.instansi.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Tambah Instansi</a>
        </div>

        @if($instansis->isEmpty())
            <p class="text-gray-500">Belum ada data instansi.</p>
        @else
            <table class="w-full text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border">Nama Instansi</th>
                        <th class="p-3 border">Alamat</th>
                        <th class="p-3 border">Telepon</th>
                        <th class="p-3 border">Status</th>
                        <th class="p-3 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instansis as $instansi)
                    <tr>
                        <td class="p-3 border">{{ $instansi->nama_instansi }}</td>
                        <td class="p-3 border">{{ $instansi->alamat ?? '-' }}</td>
                        <td class="p-3 border">{{ $instansi->telepon ?? '-' }}</td>
                        <td class="p-3 border">
                            <span class="px-2 py-1 rounded text-sm {{ $instansi->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $instansi->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-3 border flex gap-2">
                            <a href="{{ route('administrator.instansi.edit', $instansi->id) }}"
                               class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</a>
                            <form method="POST" action="{{ route('administrator.instansi.destroy', $instansi->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus?')"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
