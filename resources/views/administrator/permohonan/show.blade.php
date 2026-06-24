<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detail Permohonan</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h3 class="font-semibold text-lg mb-3">Informasi Permohonan</h3>
        <div class="grid grid-cols-2 gap-4">
            <p><span class="font-medium">No. Permohonan:</span> {{ $permohonan->nomor_permohonan }}</p>
            <p><span class="font-medium">Nama Pemohon:</span> {{ $permohonan->nama_pemohon }}</p>
            <p><span class="font-medium">Perusahaan:</span> {{ $permohonan->perusahaan }}</p>
            <p><span class="font-medium">Keperluan:</span> {{ $permohonan->keperluan }}</p>
            <p><span class="font-medium">Status:</span> {{ ucfirst($permohonan->status) }}</p>
            <p><span class="font-medium">Tanggal:</span> {{ $permohonan->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h3 class="font-semibold text-lg mb-3">Berkas & Status Verifikasi</h3>
        <table class="w-full text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">Nama Berkas</th>
                    <th class="p-3 border">Status</th>
                    <th class="p-3 border">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permohonan->berkas as $berkas)
                <tr>
                    <td class="p-3 border">{{ $berkas->nama_berkas }}</td>
                    <td class="p-3 border">{{ ucfirst(str_replace('_', ' ', $berkas->status)) }}</td>
                    <td class="p-3 border">{{ $berkas->catatan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($permohonan->kartuPas)
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h3 class="font-semibold text-lg mb-3">Data Kartu PAS</h3>
        <div class="grid grid-cols-2 gap-4">
            <p><span class="font-medium">No. Kartu:</span> {{ $permohonan->kartuPas->nomor_kartu }}</p>
            <p><span class="font-medium">Berlaku s/d:</span> {{ $permohonan->kartuPas->tanggal_berlaku->format('d/m/Y') }}</p>
            <p><span class="font-medium">Area Akses:</span> {{ $permohonan->kartuPas->area_akses }}</p>
            <p><span class="font-medium">Status:</span> {{ ucfirst(str_replace('_', ' ', $permohonan->kartuPas->status)) }}</p>
        </div>
    </div>
    @endif

    <a href="{{ route('administrator.permohonan.index') }}"
       class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Kembali</a>
</x-app-layout>