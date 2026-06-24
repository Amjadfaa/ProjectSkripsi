<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit User</h2>
    </x-slot>

    <div class="bg-white rounded-xl shadow p-6 max-w-2xl">
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('administrator.users.update', $user->id) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
                    <select name="role" id="role" class="block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                            onchange="togglePerusahaan(this.value)" required>
                        <option value="pemohon" {{ $user->role == 'pemohon' ? 'selected' : '' }}>Pemohon</option>
                        <option value="administrator" {{ $user->role == 'administrator' ? 'selected' : '' }}>Administrator</option>
                        <option value="verifikator" {{ $user->role == 'verifikator' ? 'selected' : '' }}>Verifikator</option>
                    </select>
                </div>
                <div id="fieldPerusahaan">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Perusahaan</label>
                    <input type="text" name="perusahaan" value="{{ old('perusahaan', $user->perusahaan) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Password Baru <span class="text-gray-400 text-xs">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <a href="{{ route('administrator.users.index') }}"
                   class="bg-gray-400 text-white px-5 py-2 rounded-lg hover:bg-gray-500 text-sm">Batal</a>
                <button type="submit"
                        class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 text-sm font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePerusahaan(role) {
            const field = document.getElementById('fieldPerusahaan');
            field.style.display = role === 'pemohon' ? 'block' : 'none';
        }
        togglePerusahaan(document.getElementById('role').value);
    </script>
</x-app-layout>
