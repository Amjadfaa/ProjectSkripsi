<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manajemen User</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">❌ {{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg text-gray-800">Daftar User</h3>
            <a href="{{ route('administrator.users.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">
                + Tambah User
            </a>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr style="background: #1e3a5f; color: white;">
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Perusahaan</th>
                    <th class="px-4 py-3 text-left">Terdaftar</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div style="width:32px; height:32px; background:#1e3a5f; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#f0b429; flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-medium">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        @php
                            $roleBadge = [
                                'administrator' => 'bg-purple-100 text-purple-700',
                                'verifikator'   => 'bg-blue-100 text-blue-700',
                                'pemohon'       => 'bg-green-100 text-green-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $roleBadge[$user->role] }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->perusahaan ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('administrator.users.edit', $user->id) }}"
                               class="bg-yellow-400 text-white px-3 py-1 rounded text-xs hover:bg-yellow-500">Edit</a>
                            @if($user->id !== auth()->id() && $user->role === 'pemohon')
                            <form method="POST" action="{{ route('administrator.users.destroy', $user->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus user ini?')"
                                        class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
