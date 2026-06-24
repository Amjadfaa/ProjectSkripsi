<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Profil</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success') }}</div>
    @endif
    @if(session('success_password'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4">✅ {{ session('success_password') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 max-w-2xl">

        <!-- Info Profil -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-4 mb-6">
                <div style="width:64px; height:64px; background:#1e3a5f; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; color:#f0b429;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-lg text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-gray-500 text-sm">{{ auth()->user()->email }}</p>
                    <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PATCH')

                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm" required>
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm" required>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 font-medium">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- Ganti Password -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-lg text-gray-800 mb-4">🔒 Ganti Password</h3>

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')

                <div class="mb-4" style="position:relative;">
                    <label class="block font-medium text-gray-700 mb-1">Password Lama</label>
                    <input type="password" name="current_password" id="cur_pass"
                           class="block w-full border-gray-300 rounded-lg shadow-sm" required>
                    <button type="button" class="eye-btn" data-target="cur_pass" data-eye="eye_cur">
                        <i id="eye_cur" class="fa fa-eye" style="color:#888;"></i>
                    </button>
                    @error('current_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4" style="position:relative;">
                    <label class="block font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" id="new_pass"
                           class="block w-full border-gray-300 rounded-lg shadow-sm" required>
                    <button type="button" class="eye-btn" data-target="new_pass" data-eye="eye_new">
                        <i id="eye_new" class="fa fa-eye" style="color:#888;"></i>
                    </button>
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6" style="position:relative;">
                    <label class="block font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="conf_pass"
                           class="block w-full border-gray-300 rounded-lg shadow-sm" required>
                    <button type="button" class="eye-btn" data-target="conf_pass" data-eye="eye_conf">
                        <i id="eye_conf" class="fa fa-eye" style="color:#888;"></i>
                    </button>
                </div>

                <button type="submit"
                        class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 font-medium">
                    Ganti Password
                </button>
            </form>
        </div>

    </div>

    <style>
        .eye-btn {
            position: absolute;
            right: 12px;
            top: 38px;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 10;
            padding: 0;
        }
    </style>

    <script>
        document.querySelectorAll('.eye-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const field = document.getElementById(this.getAttribute('data-target'));
                const eye   = document.getElementById(this.getAttribute('data-eye'));
                if (field.type === 'password') {
                    field.type = 'text';
                    eye.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    field.type = 'password';
                    eye.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });
    </script>

</x-app-layout>