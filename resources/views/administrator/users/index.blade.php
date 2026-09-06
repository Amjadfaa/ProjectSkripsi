<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-[#1e3a5f] flex items-center gap-2">
            <i class="fas fa-users-cog text-[#f0b429]"></i>
            <span>Manajemen Akun Operator</span>
        </h2>
    </x-slot>

    <!-- PAGE HEADER & ACTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base shadow-sm shrink-0">
                    <i class="fas fa-users-cog"></i>
                </span>
                <span>Manajemen Akun Operator & Pengguna</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola akun akses operasional scanner petugas lapangan dan akun administrator sistem.</p>
        </div>
        <a href="{{ route('administrator.users.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-3 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 shrink-0 self-start sm:self-auto">
            <i class="fas fa-user-plus text-sm"></i>
            <span>+ Tambah Akun Operator</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-5 flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-2.5 text-sm font-medium">
                <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl mb-5 flex items-center justify-between shadow-sm animate-fade-in">
            <div class="flex items-center gap-2.5 text-sm font-medium">
                <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- STATS OVERVIEW CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <!-- Total Operator -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group hover:border-blue-300 transition-all">
            <div class="relative z-10">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Akun Operator</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-slate-800">{{ $totalOperator }}</span>
                    <span class="text-[11px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Petugas Scanner</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Akses kamera & monitoring lapangan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>

        <!-- Total Admin -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group hover:border-indigo-300 transition-all">
            <div class="relative z-10">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Administrator</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-slate-800">{{ $totalAdmin }}</span>
                    <span class="text-[11px] font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Full Akses</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Pengelola penuh sistem MONPASKU</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner group-hover:bg-indigo-600 group-hover:text-white transition-all">
                <i class="fas fa-user-cog"></i>
            </div>
        </div>

        <!-- Total Pengguna -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between relative overflow-hidden group hover:border-amber-300 transition-all">
            <div class="relative z-10">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Akun Terdaftar</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-slate-800">{{ $totalUsers }}</span>
                    <span class="text-[11px] font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Keseluruhan</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Total pengguna dalam basis data</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner group-hover:bg-amber-600 group-hover:text-white transition-all">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH CARD -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm mb-6">
        <form method="GET" action="{{ route('administrator.users.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama akun, email, atau unit/instansi..."
                       class="w-full pl-9 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder-slate-400">
            </div>

            <div class="w-full md:w-52">
                <select name="role" class="w-full py-2.5 px-3 text-xs bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-slate-700">
                    <option value="">Semua Role Akun</option>
                    <option value="operator" {{ request('role') === 'operator' ? 'selected' : '' }}>Operator (Scanner)</option>
                    <option value="administrator" {{ request('role') === 'administrator' ? 'selected' : '' }}>Administrator (Sistem)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition-all flex items-center gap-1.5">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('administrator.users.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs px-3.5 py-2.5 rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- MAIN DATA TABLE CARD -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-sm sm:text-base text-slate-800">Daftar Akun Pengguna & Operator</h3>
                <p class="text-xs text-slate-400">Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} akun</p>
            </div>
            <a href="{{ route('administrator.users.create') }}" 
               class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all self-start sm:self-auto">
                <i class="fas fa-plus text-[10px]"></i> Tambah Akun Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-700 font-semibold uppercase tracking-wider text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Email Akun</th>
                        <th class="px-5 py-3.5">Role & Akses</th>
                        <th class="px-5 py-3.5">Kamera Ditugaskan</th>
                        <th class="px-5 py-3.5">Unit / Perusahaan</th>
                        <th class="px-5 py-3.5">Tanggal Dibuat</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Pengguna -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 shadow-sm {{ $user->role === 'administrator' ? 'bg-indigo-100 text-indigo-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 flex items-center gap-2">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="bg-amber-100 text-amber-800 text-[10px] font-semibold px-2 py-0.5 rounded-md border border-amber-200">
                                                    Anda
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-slate-400">ID: #{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-5 py-4 text-slate-700 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <i class="far fa-envelope text-slate-400 text-xs"></i>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </td>

                            <!-- Role & Akses -->
                            <td class="px-5 py-4">
                                @if($user->role === 'operator')
                                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200/80 px-2.5 py-1 rounded-lg text-xs font-semibold">
                                        <i class="fas fa-user-shield text-[10px]"></i> Operator Scanner
                                    </span>
                                @elseif($user->role === 'administrator')
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200/80 px-2.5 py-1 rounded-lg text-xs font-semibold">
                                        <i class="fas fa-crown text-[10px] text-amber-500"></i> Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-xs font-medium">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kamera Ditugaskan -->
                            <td class="px-5 py-4">
                                @if($user->role === 'administrator')
                                    <span class="text-slate-400 italic text-[11px] flex items-center gap-1">
                                        <i class="fas fa-crown text-amber-500 text-[10px]"></i> Full Kontrol Sistem
                                    </span>
                                @else
                                    @if($user->cameraDevices && $user->cameraDevices->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach($user->cameraDevices as $dev)
                                                <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-200 px-2 py-0.5 rounded-lg text-[11px] font-medium"
                                                      title="{{ $dev->nama_kamera }} (Area: {{ $dev->kode_area }})">
                                                    <i class="fas fa-video text-[9px] text-purple-500"></i>
                                                    <span class="font-mono font-bold">{{ $dev->kode_akses }}</span>
                                                    <span class="text-[10px] text-purple-600 font-semibold">[{{ $dev->kode_area }}]</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200/80 px-2 py-0.5 rounded-lg text-[11px] font-medium">
                                            <i class="fas fa-exclamation-triangle text-[9px]"></i> Belum Ada Kamera
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <!-- Perusahaan / Unit -->
                            <td class="px-5 py-4">
                                @if($user->perusahaan)
                                    <div class="flex items-center gap-1.5 text-slate-700">
                                        <i class="fas fa-building text-slate-400 text-xs"></i>
                                        <span>{{ $user->perusahaan }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">- Semua Instansi -</span>
                                @endif
                            </td>

                            <!-- Terdaftar -->
                            <td class="px-5 py-4 text-slate-500 text-[11px]">
                                <div class="flex items-center gap-1.5">
                                    <i class="far fa-calendar-alt text-slate-400"></i>
                                    <span>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Tugaskan & Kirim Kode Akses (Khusus Operator) -->
                                    @if($user->role === 'operator')
                                        <button type="button" onclick='openModalAssignKamera(@json($user))'
                                                title="Tugaskan & Kirim Kode Akses Kamera"
                                                class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 flex items-center justify-center transition-all shadow-sm">
                                            <i class="fas fa-key text-amber-500 text-xs"></i>
                                        </button>
                                    @endif

                                    <!-- Edit -->
                                    <a href="{{ route('administrator.users.edit', $user->id) }}"
                                       title="Edit Akun"
                                       class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/60 flex items-center justify-center transition-all">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    <!-- Hapus -->
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('administrator.users.destroy', $user->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ addslashes($user->name) }} ({{ $user->email }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Akun"
                                                    class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/60 flex items-center justify-center transition-all">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span title="Akun Anda yang sedang aktif" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center cursor-not-allowed">
                                            <i class="fas fa-lock text-xs"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-3">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <h4 class="font-bold text-slate-700 text-sm">Tidak Ada Akun Ditemukan</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                        @if(request('search') || request('role'))
                                            Tidak ada akun yang cocok dengan kata kunci atau filter pencarian Anda.
                                        @else
                                            Belum ada akun pengguna tambahan yang terdaftar.
                                        @endif
                                    </p>
                                    @if(request('search') || request('role'))
                                        <a href="{{ route('administrator.users.index') }}" class="mt-3 text-xs font-semibold text-blue-600 hover:underline">
                                            Reset Filter Pencarian
                                        </a>
                                    @else
                                        <a href="{{ route('administrator.users.create') }}" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all">
                                            + Tambah Akun Baru
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL TUGASKAN & KIRIM KODE AKSES KAMERA KE OPERATOR -->
    <div id="modalAssignKamera" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 sm:p-7 relative my-8 max-h-[90vh] flex flex-col animate-fade-in border border-slate-100">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center text-base">
                        <i class="fas fa-key text-amber-500"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Tugaskan & Kirim Kode Akses Kamera</h3>
                        <p class="text-xs text-slate-500">Atur otorisasi perangkat kamera untuk operator</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalAssignKamera()" class="text-slate-400 hover:text-slate-600 text-lg p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form id="formAssignKamera" method="POST" action="" class="flex-1 overflow-y-auto space-y-4 pr-1">
                @csrf

                <!-- Target Operator Banner -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs" id="assign_user_initial">
                            OP
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-slate-800" id="assign_user_name">-</h4>
                            <p class="text-[11px] text-slate-500" id="assign_user_email">-</p>
                        </div>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-blue-200">
                        Operator Scanner
                    </span>
                </div>

                <!-- Checklist Kamera -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilih Perangkat Kamera yang Boleh Diakses <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="selectAllCameras(true)" class="text-[11px] font-semibold text-blue-600 hover:underline">
                                Pilih Semua
                            </button>
                            <span class="text-slate-300">&bull;</span>
                            <button type="button" onclick="selectAllCameras(false)" class="text-[11px] font-semibold text-slate-500 hover:underline">
                                Batal Semua
                            </button>
                        </div>
                    </div>

                    <div class="max-h-60 overflow-y-auto space-y-2 border border-slate-200 rounded-xl p-2.5 bg-slate-50/50" id="cameraCheckboxList">
                        @forelse($allCameraDevices as $cam)
                            <label class="flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-lg hover:bg-blue-50/40 cursor-pointer transition-colors group">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="camera_ids[]" value="{{ $cam->id }}"
                                           data-kode-akses="{{ $cam->kode_akses }}"
                                           data-nama="{{ $cam->nama_kamera }}"
                                           data-area="{{ $cam->kode_area }}"
                                           data-tipe="{{ $cam->tipe_scan }}"
                                           class="camera-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
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
                </div>

                <!-- Opsi Kirim Email -->
                <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-xl space-y-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="kirim_email" value="1" checked id="checkboxKirimEmail"
                               class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 mt-0.5">
                        <div>
                            <span class="text-xs font-bold text-emerald-900 block flex items-center gap-1.5">
                                <i class="fas fa-paper-plane text-emerald-600"></i> Kirim Rincian Kode Akses ke Email Operator
                            </span>
                            <span class="text-[11px] text-emerald-700 leading-relaxed block mt-0.5">
                                Sistem akan otomatis mengirimkan email resmi berisi daftar kamera, kode akses, dan panduan penggunaan scanner ke alamat email akun operator ini.
                            </span>
                        </div>
                    </label>
                </div>

                <!-- Catatan Tambahan (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Pesan / Catatan Tambahan <span class="text-slate-400 font-normal text-[11px]">(Opsional, ikut dikirim dalam email)</span>
                    </label>
                    <textarea name="pesan_tambahan" id="pesanTambahan" rows="2"
                              placeholder="Contoh: Harap segera login dan pastikan koneksi scanner di Gate 1 telah aktif sebelum pukul 08:00 WIB."
                              class="w-full text-xs bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
                </div>

                <!-- Action Footer -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100 shrink-0">
                    <button type="button" onclick="copyWhatsAppFormat()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2.5 rounded-xl transition-all shadow-sm"
                            title="Salin rincian kode akses dalam format chat WhatsApp">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>Salin Format WhatsApp</span>
                    </button>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" onclick="closeModalAssignKamera()"
                                class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs px-4 py-2.5 rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition-all shadow-md flex items-center gap-1.5">
                            <i class="fas fa-check"></i>
                            <span>Simpan & Terapkan Otorisasi</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentTargetUser = null;

        function openModalAssignKamera(user) {
            currentTargetUser = user;
            const form = document.getElementById('formAssignKamera');
            form.action = "{{ url('/administrator/users') }}/" + user.id + "/assign-kamera";

            document.getElementById('assign_user_name').textContent = user.name;
            document.getElementById('assign_user_email').textContent = user.email + (user.perusahaan ? ' • ' + user.perusahaan : '');
            document.getElementById('assign_user_initial').textContent = user.name.substring(0, 2).toUpperCase();
            document.getElementById('pesanTambahan').value = '';

            // Centang kamera yang saat ini sudah ditugaskan kepada user
            const assignedIds = (user.camera_devices || []).map(c => c.id);
            const checkboxes = document.querySelectorAll('.camera-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = assignedIds.includes(parseInt(cb.value));
            });

            document.getElementById('modalAssignKamera').classList.remove('hidden');
        }

        function closeModalAssignKamera() {
            document.getElementById('modalAssignKamera').classList.add('hidden');
        }

        function selectAllCameras(status) {
            const checkboxes = document.querySelectorAll('.camera-checkbox');
            checkboxes.forEach(cb => { cb.checked = status; });
        }

        function copyWhatsAppFormat() {
            if (!currentTargetUser) return;

            const selectedCbs = Array.from(document.querySelectorAll('.camera-checkbox:checked'));
            if (selectedCbs.length === 0) {
                alert('Pilih minimal satu perangkat kamera untuk menyalin format WhatsApp.');
                return;
            }

            let text = `*PEMBERITAHUAN KODE AKSES SCANNER MONPASKU*\n`;
            text += `--------------------------------------\n`;
            text += `Halo *${currentTargetUser.name}*,\n`;
            text += `Berikut adalah daftar perangkat kamera & kode akses yang ditugaskan kepada Anda:\n\n`;

            selectedCbs.forEach((cb, idx) => {
                const nama = cb.getAttribute('data-nama');
                const area = cb.getAttribute('data-area');
                const kode = cb.getAttribute('data-kode-akses');
                const tipe = cb.getAttribute('data-tipe');
                text += `${idx + 1}. *${nama}* (Area: ${area})\n`;
                text += `   • Kode Akses: \`${kode}\`\n`;
                text += `   • Tipe Scan: ${tipe.toUpperCase()}\n\n`;
            });

            const extra = document.getElementById('pesanTambahan').value.trim();
            if (extra) {
                text += `📝 *Catatan:* ${extra}\n\n`;
            }

            text += `Silakan login di sistem MONPASKU: {{ url('/operator/kamera') }}\n`;
            text += `Jaga kerahasiaan kode akses dan selamat bertugas!`;

            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disalin!',
                    text: 'Format pesan WhatsApp berhasil disalin ke clipboard. Anda dapat langsung menempelkannya (paste) di chat WhatsApp.',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }).catch(err => {
                alert('Gagal menyalin format pesan.');
            });
        }
    </script>
</x-app-layout>
