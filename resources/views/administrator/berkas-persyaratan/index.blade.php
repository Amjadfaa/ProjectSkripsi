<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Berkas Persyaratan</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-lg">Daftar Berkas Persyaratan</h3>
            <a href="{{ route('administrator.berkas-persyaratan.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">
                + Upload Berkas
            </a>
        </div>

        @if($berkas->isEmpty())
            <p class="text-gray-500">Belum ada berkas persyaratan.</p>
        @else
            <table class="w-full text-left border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border">Nama Berkas</th>
                        <th class="p-3 border">Keterangan</th>
                        <th class="p-3 border">Status</th>
                        <th class="p-3 border">Tanggal Upload</th>
                        <th class="p-3 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($berkas as $item)
                    <tr>
                        <td class="p-3 border">{{ $item->nama_berkas }}</td>
                        <td class="p-3 border">{{ $item->keterangan ?? '-' }}</td>
                        <td class="p-3 border">
                            <span class="px-2 py-1 rounded text-sm {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-3 border">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="p-3 border">
                            <div class="flex gap-2">
                                <a href="{{ Storage::url($item->file_path) }}" target="_blank"
                                   class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                    Lihat
                                </a>
                                <form method="POST" action="{{ route('administrator.berkas-persyaratan.toggle', $item->id) }}">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="px-3 py-1 rounded text-sm {{ $item->is_active ? 'bg-yellow-400 text-white hover:bg-yellow-500' : 'bg-green-500 text-white hover:bg-green-600' }}">
                                        {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('administrator.berkas-persyaratan.destroy', $item->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin hapus?')"
                                            class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
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
