<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Monitoring Kuota PAS</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-lg text-gray-800">📊 Kuota PAS per Instansi</h3>
        </div>

        @if($instansis->isEmpty())
            <p class="text-gray-500 text-center py-8">Belum ada data instansi.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="p-3 text-left text-gray-600">Instansi</th>
                        <th class="p-3 text-center text-gray-600">Total Kuota</th>
                        <th class="p-3 text-center text-gray-600">Kartu Aktif</th>
                        <th class="p-3 text-center text-gray-600">Sisa Kuota</th>
                        <th class="p-3 text-center text-gray-600">Nonaktif</th>
                        <th class="p-3 text-center text-gray-600">Pemakaian</th>
                        <th class="p-3 text-center text-gray-600">Set Kuota</th>
                        <th class="p-3 text-center text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($instansis as $instansi)
                    @php
                        $persen    = $instansi->kuota > 0 ? min(($instansi->kartu_aktif / $instansi->kuota) * 100, 100) : 0;
                        $warnaBar  = $persen >= 90 ? 'bg-red-500' : ($persen >= 70 ? 'bg-yellow-400' : 'bg-green-500');
                        $warnaText = $persen >= 90 ? 'text-red-600' : ($persen >= 70 ? 'text-yellow-600' : 'text-green-600');
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">
                            <p class="font-semibold text-gray-800">{{ $instansi->nama_instansi }}</p>
                            <p class="text-xs text-gray-400">{{ $instansi->alamat ?? '-' }}</p>
                        </td>
                        <td class="p-3 text-center">
                            <span class="text-xl font-bold text-gray-800">{{ $instansi->kuota }}</span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="text-xl font-bold text-green-600">{{ $instansi->kartu_aktif }}</span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="text-xl font-bold {{ $instansi->sisa_kuota < 0 ? 'text-red-600' : 'text-blue-600' }}">
                                {{ $instansi->sisa_kuota }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="text-xl font-bold text-gray-500">{{ $instansi->kartu_nonaktif }}</span>
                        </td>
                        <td class="p-3" style="min-width: 150px;">
                            <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                                <div class="{{ $warnaBar }} h-2 rounded-full" style="width: {{ $persen }}%"></div>
                            </div>
                            <p class="text-xs text-center font-medium {{ $warnaText }}">{{ round($persen) }}%</p>
                        </td>
                        <td class="p-3 text-center">
                            <form method="POST" action="{{ route('administrator.monitoring-kuota.update-kuota', $instansi->id) }}" class="flex items-center gap-1 justify-center">
                                @csrf @method('PUT')
                                <input type="number" name="kuota" value="{{ $instansi->kuota }}"
                                       class="w-16 border-gray-300 rounded-lg text-sm text-center" min="0">
                                <button type="submit"
                                        class="bg-blue-500 text-white px-2 py-1 rounded-lg text-xs hover:bg-blue-600">
                                    Set
                                </button>
                            </form>
                        </td>
                        <td class="p-3 text-center">
                            <a href="{{ route('administrator.monitoring-kuota.show', $instansi->id) }}"
                               class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs hover:bg-gray-200">
                                Detail →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>