<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Verifikasi Berkas Permohonan</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif

    <!-- Info Permohonan -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-bold text-lg text-gray-800 mb-4">📋 Informasi Permohonan</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">No. Permohonan</p>
                <p class="font-semibold text-gray-800">{{ $permohonan->nomor_permohonan }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Nama Pemohon</p>
                <p class="font-semibold text-gray-800">{{ $permohonan->nama_pemohon }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Perusahaan</p>
                <p class="font-semibold text-gray-800">{{ $permohonan->perusahaan }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Keperluan</p>
                <p class="font-semibold text-gray-800">{{ $permohonan->keperluan }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                @php
                    $badge = [
                        'menunggu'  => 'bg-yellow-100 text-yellow-700',
                        'diproses'  => 'bg-blue-100 text-blue-700',
                        'disetujui' => 'bg-green-100 text-green-700',
                        'ditolak'   => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge[$permohonan->status] }}">
                    {{ ucfirst($permohonan->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                <p class="font-semibold text-gray-800">{{ $permohonan->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Berkas yang Diupload -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-bold text-lg text-gray-800 mb-4">📁 Berkas yang Diupload Pemohon</h3>

        <div class="space-y-3 mb-6">
            @foreach($permohonan->berkas as $berkas)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div style="width:40px; height:40px; background:#eff6ff; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px;">
                            📄
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $berkas->nama_berkas }}</p>
                            <p class="text-xs text-gray-400">Diupload: {{ $berkas->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $badgeBerkas = [
                                'belum_diverifikasi' => 'bg-yellow-100 text-yellow-700',
                                'diverifikasi'       => 'bg-green-100 text-green-700',
                                'ditolak'            => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badgeBerkas[$berkas->status] }}">
                            {{ ucfirst(str_replace('_', ' ', $berkas->status)) }}
                        </span>

                        <!-- Tombol Lihat File -->
                        @php
                            $ext = pathinfo($berkas->file_path, PATHINFO_EXTENSION);
                        @endphp
                        <a href="{{ Storage::url($berkas->file_path) }}" target="_blank"
                           class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 flex items-center gap-1">
                            <i class="fas fa-eye"></i> Lihat File
                        </a>
                        <a href="{{ Storage::url($berkas->file_path) }}" download
                           class="bg-gray-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-gray-600 flex items-center gap-1">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Form Verifikasi -->
        <div class="border-t pt-6">
            <h3 class="font-bold text-lg text-gray-800 mb-4">✅ Form Verifikasi Berkas</h3>
            <form method="POST" action="{{ route('verifikator.permohonan.verifikasi', $permohonan->id) }}">
                @csrf
                <div class="space-y-4">
                    @foreach($permohonan->berkas as $berkas)
                    <div class="border rounded-lg p-4 {{ $berkas->status === 'diverifikasi' ? 'bg-green-50 border-green-200' : ($berkas->status === 'ditolak' ? 'bg-red-50 border-red-200' : 'bg-gray-50') }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                       name="berkas_{{ $berkas->id }}"
                                       id="berkas_{{ $berkas->id }}"
                                       class="w-5 h-5 accent-blue-600"
                                       {{ $berkas->verifikasi?->is_verified ? 'checked' : '' }}>
                                <label for="berkas_{{ $berkas->id }}" class="font-semibold text-gray-800 cursor-pointer">
                                    {{ $berkas->nama_berkas }}
                                </label>
                            </div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badgeBerkas[$berkas->status] }}">
                                {{ ucfirst(str_replace('_', ' ', $berkas->status)) }}
                            </span>
                        </div>
                        <div>
                            <input type="text"
                                   name="catatan_{{ $berkas->id }}"
                                   value="{{ $berkas->verifikasi?->catatan }}"
                                   placeholder="Catatan verifikasi (opsional)"
                                   class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between mt-6">
                    <a href="{{ route('verifikator.permohonan.index') }}"
                    class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 font-medium flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit"
                        class="px-8 py-3 rounded-lg font-bold text-white text-base flex items-center gap-2"
                        style="background: #1e3a5f; border: none; cursor: pointer;">
                    <i class="fas fa-check-circle"></i> Simpan Verifikasi
                </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>