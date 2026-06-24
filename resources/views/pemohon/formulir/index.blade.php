<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Download Formulir</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                @if($formulirs->isEmpty())
                    <p class="text-gray-500">Belum ada formulir tersedia.</p>
                @else
                    <table class="w-full text-left border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 border">Nama Formulir</th>
                                <th class="p-3 border">Keterangan</th>
                                <th class="p-3 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formulirs as $formulir)
                            <tr>
                                <td class="p-3 border">{{ $formulir->nama_formulir }}</td>
                                <td class="p-3 border">{{ $formulir->keterangan ?? '-' }}</td>
                                <td class="p-3 border">
                                    <a href="{{ route('pemohon.formulir.download', $formulir->id) }}"
                                       class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                        Download
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