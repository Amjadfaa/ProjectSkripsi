<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-[#1e3a5f] flex items-center gap-2">
            <i class="fas fa-user-edit text-[#f0b429]"></i>
            <span>Edit Akun Pengguna</span>
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <!-- Header & Back Navigation -->
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base shadow-sm shrink-0">
                        <i class="fas fa-user-edit"></i>
                    </span>
                    <span>Edit Akun: {{ $user->name }}</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">Perbarui informasi kredensial, hak akses akun, atau atur ulang kata sandi.</p>
            </div>
            <a href="{{ route('administrator.users.index') }}"
               class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs px-3.5 py-2.5 rounded-xl shadow-sm transition-all shrink-0">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl mb-6 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-xs mb-2 text-rose-700">
                    <i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan pengisian formulir:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 text-rose-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Form Header Banner -->
            <div class="p-6 bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white flex items-center justify-between">
                <div>
                    <span class="bg-amber-400/20 text-amber-300 border border-amber-400/30 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                        Perbarui Akun #{{ $user->id }}
                    </span>
                    <h3 class="text-lg font-black mt-1">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-300">{{ $user->email }} &bull; Role saat ini: <strong class="text-amber-400">{{ ucfirst($user->role) }}</strong></p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-xl text-amber-400">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <form method="POST" action="{{ route('administrator.users.update', $user->id) }}" class="p-6 sm:p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- ROLE SELECTION CARDS -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Hak Akses / Role <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Role Operator -->
                        <label class="relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition-all hover:shadow-sm"
                               id="labelRoleOperator">
                            <input type="radio" name="role" value="operator" id="roleOperator"
                                   {{ old('role', $user->role) === 'operator' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg mb-2">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <span class="badge-check text-blue-600 font-bold text-xs" style="display: {{ old('role', $user->role) === 'operator' ? 'block' : 'none' }};">
                                    <i class="fas fa-check-circle text-base"></i>
                                </span>
                            </div>
                            <span class="font-bold text-sm text-slate-800">Operator (Scanner)</span>
                            <span class="text-xs text-slate-500 mt-1">
                                Bertugas melakukan pemindaian QR code kartu PAS bandara dan monitoring gerbang akses.
                            </span>
                            <span class="mt-2 text-[10px] font-semibold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded self-start">
                                Petugas Lapangan
                            </span>
                        </label>

                        <!-- Role Administrator -->
                        <label class="relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition-all hover:shadow-sm"
                               id="labelRoleAdmin">
                            <input type="radio" name="role" value="administrator" id="roleAdmin"
                                   {{ old('role', $user->role) === 'administrator' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex items-start justify-between">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg mb-2">
                                    <i class="fas fa-crown text-amber-500"></i>
                                </div>
                                <span class="badge-check text-indigo-600 font-bold text-xs" style="display: {{ old('role', $user->role) === 'administrator' ? 'block' : 'none' }};">
                                    <i class="fas fa-check-circle text-base"></i>
                                </span>
                            </div>
                            <span class="font-bold text-sm text-slate-800">Administrator Sistem</span>
                            <span class="text-xs text-slate-500 mt-1">
                                Pengelola penuh seluruh modul dan pengaturan sistem MONPASKU.
                            </span>
                            <span class="mt-2 text-[10px] font-semibold text-indigo-700 bg-indigo-100/70 px-2 py-0.5 rounded self-start">
                                Full Kontrol
                            </span>
                        </label>
                    </div>
                </div>

                <!-- NAME & EMAIL -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-user text-xs"></i>
                            </div>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email (Login) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-envelope text-xs"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-800">
                        </div>
                    </div>
                </div>

                <!-- PERUSAHAAN / UNIT KERJA -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Instansi / Unit Kerja <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-building text-xs"></i>
                        </div>
                        <input type="text" name="perusahaan" id="inputPerusahaan" list="instansiSuggestions"
                               value="{{ old('perusahaan', $user->perusahaan) }}"
                               placeholder="Contoh: Unit Aviation Security (Avsec) / PT Angkasa Pura"
                               class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-800">
                        <datalist id="instansiSuggestions">
                            @foreach($instansiList as $instansi)
                                <option value="{{ $instansi->nama_instansi }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <!-- CHANGE PASSWORD (OPTIONAL) -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-key text-amber-500 text-xs"></i>
                        <h4 class="font-bold text-xs text-slate-700 uppercase tracking-wider">Ubah Kata Sandi (Opsional)</h4>
                        <span class="text-[11px] text-slate-400">Kosongkan kolom di bawah jika tidak ingin mengganti kata sandi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Kata Sandi Baru
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-lock text-xs"></i>
                                </div>
                                <input type="password" name="password" id="inputPasswordEdit" minlength="8"
                                       placeholder="Minimal 8 karakter baru"
                                       class="w-full pl-10 pr-10 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-800">
                                <button type="button" onclick="togglePass('inputPasswordEdit', this)"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-shield-alt text-xs"></i>
                                </div>
                                <input type="password" name="password_confirmation" id="inputPasswordConfirmEdit" minlength="8"
                                       placeholder="Ulangi kata sandi baru"
                                       class="w-full pl-10 pr-10 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium text-slate-800">
                                <button type="button" onclick="togglePass('inputPasswordConfirmEdit', this)"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PENUGASAN KAMERA & KODE AKSES (KHUSUS OPERATOR) -->
                @php
                    $assignedIds = old('camera_devices', $user->cameraDevices->pluck('id')->toArray());
                @endphp
                <div id="sectionPenugasanKameraEdit" class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-video text-blue-600"></i>
                                <span>Penugasan Perangkat Kamera & Kode Akses (Operator)</span>
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Tentukan titik kamera mana saja yang diizinkan untuk diakses oleh operator ini.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleAllEditCameras(true)" class="text-[11px] font-semibold text-blue-600 hover:underline">
                                Pilih Semua
                            </button>
                            <span class="text-slate-300">&bull;</span>
                            <button type="button" onclick="toggleAllEditCameras(false)" class="text-[11px] font-semibold text-slate-500 hover:underline">
                                Batal Semua
                            </button>
                        </div>
                    </div>

                    <div class="max-h-56 overflow-y-auto space-y-2 border border-slate-200 rounded-xl p-2.5 bg-slate-50/50">
                        @forelse($cameraDevices as $cam)
                            <label class="flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-lg hover:bg-blue-50/40 cursor-pointer transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="camera_devices[]" value="{{ $cam->id }}"
                                           {{ in_array($cam->id, $assignedIds) ? 'checked' : '' }}
                                           class="edit-camera-cb rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                    <div>
                                        <div class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                            <span>{{ $cam->nama_kamera }}</span>
                                            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.2 rounded">
                                                Area {{ $cam->kode_area }}
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-0.5">
                                            Tipe: {{ strtoupper(str_replace('_', ' ', $cam->tipe_scan)) }}
                                            @if($cam->areaAkses)
                                                &bull; {{ $cam->areaAkses->keterangan }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <code class="font-mono text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 shrink-0">
                                    {{ $cam->kode_akses }}
                                </code>
                            </label>
                        @empty
                            <div class="p-6 text-center text-slate-400 text-xs">
                                <i class="fas fa-video-slash text-xl mb-1 block"></i>
                                Belum ada perangkat kamera aktif yang didaftarkan di sistem.
                            </div>
                        @endforelse
                    </div>

                    <!-- Centang Kirim Email -->
                    <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="kirim_email" value="1"
                                   class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span class="text-xs font-semibold text-emerald-900">
                                <i class="fas fa-paper-plane text-emerald-600 mr-1"></i> Kirim pembaruan kode akses & panduan ke email operator setelah disimpan
                            </span>
                        </label>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                    <a href="{{ route('administrator.users.index') }}"
                       class="text-xs font-semibold text-slate-500 hover:text-slate-700">
                        Batal
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md hover:shadow-lg flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Handle Role Selector UI & Show/Hide Camera Assignment
        const roleOperator = document.getElementById('roleOperator');
        const roleAdmin = document.getElementById('roleAdmin');
        const labelRoleOperator = document.getElementById('labelRoleOperator');
        const labelRoleAdmin = document.getElementById('labelRoleAdmin');
        const sectionPenugasanKameraEdit = document.getElementById('sectionPenugasanKameraEdit');

        function updateRoleSelection() {
            if (roleOperator.checked) {
                labelRoleOperator.className = "relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition-all shadow-sm border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20";
                labelRoleAdmin.className = "relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition-all hover:shadow-sm border-slate-200 hover:border-slate-300";
                labelRoleOperator.querySelector('.badge-check').style.display = 'block';
                labelRoleAdmin.querySelector('.badge-check').style.display = 'none';
                if (sectionPenugasanKameraEdit) sectionPenugasanKameraEdit.style.display = 'block';
            } else {
                labelRoleAdmin.className = "relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition-all shadow-sm border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20";
                labelRoleOperator.className = "relative flex flex-col p-4 border-2 rounded-2xl cursor-pointer transition-all hover:shadow-sm border-slate-200 hover:border-slate-300";
                labelRoleOperator.querySelector('.badge-check').style.display = 'none';
                labelRoleAdmin.querySelector('.badge-check').style.display = 'block';
                if (sectionPenugasanKameraEdit) sectionPenugasanKameraEdit.style.display = 'none';
            }
        }

        function toggleAllEditCameras(status) {
            document.querySelectorAll('.edit-camera-cb').forEach(cb => { cb.checked = status; });
        }

        roleOperator.addEventListener('change', updateRoleSelection);
        roleAdmin.addEventListener('change', updateRoleSelection);
        labelRoleOperator.addEventListener('click', () => { roleOperator.checked = true; updateRoleSelection(); });
        labelRoleAdmin.addEventListener('click', () => { roleAdmin.checked = true; updateRoleSelection(); });
        updateRoleSelection();
    </script>
</x-app-layout>
