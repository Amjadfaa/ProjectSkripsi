<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs border border-blue-100">
                <i class="fas fa-user-gear"></i>
            </span>
            <div>
                <h1 class="font-bold text-base sm:text-lg text-slate-800 tracking-tight leading-tight">
                    Pengaturan Profil & Keamanan
                </h1>
                <p class="text-[11px] text-slate-500 font-normal leading-none mt-0.5 hidden sm:block">
                    Kelola data identitas akun, alamat email resmi, dan pembaruan kata sandi
                </p>
            </div>
        </div>
    </x-slot>

    {{-- ======================================================================== --}}
    {{-- ALERT NOTIFICATIONS                                                      --}}
    {{-- ======================================================================== --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 flex items-center justify-between shadow-xs animate-fadeIn">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-sm">
                    <i class="fas fa-circle-check"></i>
                </span>
                <div>
                    <p class="text-xs font-bold">{{ session('success') }}</p>
                    <p class="text-[11px] text-emerald-600 mt-0.5">Informasi profil akun Anda telah berhasil diperbarui.</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 text-sm p-1">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('success_password'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 flex items-center justify-between shadow-xs animate-fadeIn">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-sm">
                    <i class="fas fa-key"></i>
                </span>
                <div>
                    <p class="text-xs font-bold">{{ session('success_password') }}</p>
                    <p class="text-[11px] text-emerald-600 mt-0.5">Kata sandi akun Anda berhasil diperbarui. Gunakan password baru saat login berikutnya.</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 text-sm p-1">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 flex items-start justify-between shadow-xs animate-fadeIn">
            <div class="flex items-start gap-3">
                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 text-sm mt-0.5">
                    <i class="fas fa-triangle-exclamation"></i>
                </span>
                <div>
                    <p class="text-xs font-bold">Terjadi Kesalahan</p>
                    <ul class="text-[11px] text-rose-600 mt-1 list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-700 text-sm p-1">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ======================================================================== --}}
    {{-- PROFILE MAIN GRID (2 COLUMNS)                                            --}}
    {{-- ======================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT COLUMN: PROFILE IDENTITY CARD & SECURITY TIPS (4 COLS) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- 1. Profile Summary Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                {{-- Decorative Header Banner --}}
                <div class="h-24 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 relative">
                    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>
                </div>

                {{-- Avatar & Identity --}}
                <div class="px-5 pb-6 text-center relative pt-0">
                    <div class="-mt-12 mb-3 inline-block relative">
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-700 to-indigo-800 text-white flex items-center justify-center font-black text-3xl shadow-lg border-4 border-white ring-2 ring-slate-100">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white" title="Status: Aktif"></span>
                    </div>

                    <h2 class="font-bold text-slate-800 text-base sm:text-lg leading-snug">
                        {{ auth()->user()->name }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">
                        {{ auth()->user()->email }}
                    </p>

                    <div class="mt-3 flex items-center justify-center gap-2">
                        @if(auth()->user()->role === 'administrator')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                <i class="fas fa-shield-halved text-[10px]"></i>
                                <span>Administrator Sistem</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 shadow-2xs">
                                <i class="fas fa-user-tie text-[10px]"></i>
                                <span>Operator Bandara</span>
                            </span>
                        @endif
                    </div>

                    {{-- Metadata Details --}}
                    <div class="mt-6 pt-5 border-t border-slate-100 text-left space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                                <i class="fas fa-building text-slate-400 text-[10px]"></i>
                                <span>Instansi / Unit:</span>
                            </span>
                            <span class="font-bold text-slate-700 truncate max-w-[150px]" title="{{ auth()->user()->perusahaan ?: 'Otoritas Bandara (UPBU)' }}">
                                {{ auth()->user()->perusahaan ?: 'Otoritas Bandara (UPBU)' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                                <i class="fas fa-circle-check text-emerald-500 text-[10px]"></i>
                                <span>Status Akun:</span>
                            </span>
                            <span class="font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Aktif</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                                <i class="fas fa-calendar text-slate-400 text-[10px]"></i>
                                <span>Terdaftar Sejak:</span>
                            </span>
                            <span class="font-semibold text-slate-700">
                                {{ auth()->user()->created_at ? auth()->user()->created_at->format('d/m/Y') : '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-medium flex items-center gap-1.5">
                                <i class="fas fa-clock-rotate-left text-slate-400 text-[10px]"></i>
                                <span>Terakhir Diubah:</span>
                            </span>
                            <span class="font-semibold text-slate-500">
                                {{ auth()->user()->updated_at ? auth()->user()->updated_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Security Guidelines Card --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-xs relative overflow-hidden">
                <div class="flex items-center gap-2 font-bold text-xs text-blue-400 mb-3">
                    <i class="fas fa-shield-halved text-sm"></i>
                    <span>Pedoman Keamanan Sandi</span>
                </div>
                <ul class="text-[11px] text-slate-300 space-y-2 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-blue-400 text-[10px] mt-0.5 shrink-0"></i>
                        <span>Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-blue-400 text-[10px] mt-0.5 shrink-0"></i>
                        <span>Jangan membagikan kredensial password kepada orang lain.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-blue-400 text-[10px] mt-0.5 shrink-0"></i>
                        <span>Perbarui kata sandi secara berkala demi keamanan sistem.</span>
                    </li>
                </ul>
            </div>

        </div>

        {{-- RIGHT COLUMN: EDIT FORMS (8 COLS) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- FORM 1: INFORMASI DATA DIRI --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center gap-3 bg-white">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 tracking-tight leading-tight">
                            Informasi Profil
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Perbarui nama lengkap dan alamat email yang Anda gunakan untuk masuk ke aplikasi
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="p-5 sm:p-6 space-y-4">
                    @csrf 
                    @method('PATCH')

                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 text-xs">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs"
                                   placeholder="Masukkan nama lengkap Anda">
                        </div>
                        @error('name') 
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                                <i class="fas fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </p> 
                        @enderror
                    </div>

                    {{-- Alamat Email --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Alamat Email Resmi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 text-xs">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-2xs"
                                   placeholder="nama@monitoring-pas.test">
                        </div>
                        @error('email') 
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                                <i class="fas fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </p> 
                        @enderror
                    </div>

                    {{-- Role Pengguna (Readonly) --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                            Hak Akses / Peran Akun
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 text-xs">
                                <i class="fas fa-user-shield"></i>
                            </span>
                            <input type="text" value="{{ ucfirst(auth()->user()->role) }}" disabled
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 cursor-not-allowed">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Hak akses sistem dikelola langsung oleh struktur otoritas bandara.</p>
                    </div>

                    {{-- Tombol Simpan Profil --}}
                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer flex items-center gap-2">
                            <i class="fas fa-floppy-disk"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- FORM 2: GANTI KATA SANDI --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center gap-3 bg-white">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 tracking-tight leading-tight">
                            Ganti Kata Sandi
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Perbarui kata sandi akun secara berkala untuk menjaga keamanan data akses
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="p-5 sm:p-6 space-y-4">
                    @csrf 
                    @method('PUT')

                    {{-- Password Saat Ini --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Password Saat Ini (Lama) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 text-xs">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="password" name="current_password" id="cur_pass" required
                                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs"
                                   placeholder="Masukkan password yang saat ini digunakan">
                            <button type="button" onclick="togglePasswordVisibility('cur_pass', this)" 
                                    class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 transition p-0.5 cursor-pointer"
                                    title="Tampilkan/Sembunyikan Password">
                                <i class="far fa-eye text-xs"></i>
                            </button>
                        </div>
                        @error('current_password') 
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                                <i class="fas fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </p> 
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 text-xs">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="new_pass" required
                                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs"
                                   placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePasswordVisibility('new_pass', this)" 
                                    class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 transition p-0.5 cursor-pointer"
                                    title="Tampilkan/Sembunyikan Password">
                                <i class="far fa-eye text-xs"></i>
                            </button>
                        </div>
                        @error('password') 
                            <p class="text-rose-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                                <i class="fas fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </p> 
                        @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 text-xs">
                                <i class="fas fa-shield-check"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="conf_pass" required
                                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs"
                                   placeholder="Ketik ulang password baru Anda">
                            <button type="button" onclick="togglePasswordVisibility('conf_pass', this)" 
                                    class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 transition p-0.5 cursor-pointer"
                                    title="Tampilkan/Sembunyikan Password">
                                <i class="far fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Ganti Password --}}
                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition-all transform hover:-translate-y-0.5 cursor-pointer flex items-center gap-2">
                            <i class="fas fa-shield-halved text-amber-400"></i>
                            <span>Perbarui Password</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

    {{-- ======================================================================== --}}
    {{-- INTERACTIVE PASSWORD TOGGLE SCRIPT                                       --}}
    {{-- ======================================================================== --}}
    <script>
        function togglePasswordVisibility(fieldId, btnElement) {
            const input = document.getElementById(fieldId);
            const icon  = btnElement.querySelector('i');
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash', 'text-blue-600');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash', 'text-blue-600');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</x-app-layout>