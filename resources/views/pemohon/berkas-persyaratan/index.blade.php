<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Berkas Persyaratan</h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-lg mb-4">Daftar Berkas Persyaratan</h3>

        @if($berkas->isEmpty())
            <div class="text-center py-10 text-gray-400">
                <p class="text-4xl mb-2">📂</p>
                <p>Belum ada berkas persyaratan tersedia.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($berkas as $item)
                <div class="border rounded-lg p-4 flex justify-between items-center hover:bg-gray-50">
                    <div>
                        <p class="font-semibold text-gray-800">📄 {{ $item->nama_berkas }}</p>
                        @if($item->keterangan)
                            <p class="text-sm text-gray-500 mt-1">{{ $item->keterangan }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">Diupload: {{ $item->created_at->format('d/m/Y') }}</p>
                    </div>
                    <a href="{{ route('pemohon.berkas-persyaratan.download', $item->id) }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 flex items-center gap-2">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
