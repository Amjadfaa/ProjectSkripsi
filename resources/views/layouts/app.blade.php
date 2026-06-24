<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MONPASKU') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f1f5f9; }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1e3a5f 0%, #1a3353 100%);
            min-height: 100vh;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .sidebar-logo {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-logo img { width: 40px; height: 40px; object-fit: contain; }
        .sidebar-logo span { font-size: 1.1rem; font-weight: 800; color: white; }
        .sidebar-logo .role-badge {
            font-size: 10px;
            background: rgba(240,180,41,0.2);
            color: #f0b429;
            border: 1px solid rgba(240,180,41,0.3);
            padding: 2px 8px;
            border-radius: 10px;
            margin-top: 2px;
            display: inline-block;
        }
        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
            min-height: 0;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .nav-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            padding: 8px 12px 4px;
            font-weight: 600;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.2s;
        }
        .nav-item:hover { background: rgba(255,255,255,0.1); color: white; }
        .nav-item.active { background: #f0b429; color: #1e3a5f; font-weight: 700; }
        .nav-item.active i { color: #1e3a5f; }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; color: rgba(255,255,255,0.5); }
        .nav-item:hover i { color: white; }

        /* User Bottom */
        .sidebar-user { padding: 16px; border-top: 1px solid rgba(255,255,255,0.1); }
        .user-info { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .user-avatar {
            width: 36px; height: 36px;
            background: #f0b429;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 14px; color: #1e3a5f; flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-email { font-size: 11px; color: rgba(255,255,255,0.5); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .logout-btn {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 8px 12px; border-radius: 8px;
            background: rgba(239,68,68,0.15); color: #fca5a5;
            font-size: 13px; font-weight: 500; border: none; cursor: pointer;
            transition: background 0.2s; text-align: left;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.25); }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 250px);
        }

        /* Topbar */
        .topbar {
            background: white;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .topbar-title { font-size: 16px; font-weight: 700; color: #1e3a5f; }
        .topbar-date { font-size: 12px; color: #94a3b8; }

        /* Notifikasi Bell */
        .notif-bell { position: relative; margin-right: 16px; }
        .notif-bell i { font-size: 18px; color: #64748b; cursor: pointer; }
        .notif-badge {
            position: absolute; top: -6px; right: -6px;
            background: #ef4444; color: white;
            font-size: 9px; font-weight: 700;
            width: 16px; height: 16px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .notif-dropdown {
            display: none;
            position: absolute;
            right: -10px; top: 30px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            z-index: 200;
            overflow-x: hidden;
        }
        .notif-dropdown.show { display: block; }
        .notif-header { padding: 12px 16px; font-weight: 700; font-size: 13px; color: #1e3a5f; border-bottom: 1px solid #f1f5f9; }
        .notif-item { padding: 10px 16px; border-bottom: 1px solid #f8fafc; font-size: 12px; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item .notif-name { font-weight: 600; color: #1e293b; }
        .notif-item .notif-detail { color: #94a3b8; margin-top: 2px; }
        .notif-item.danger { border-left: 3px solid #ef4444; }
        .notif-item.warning { border-left: 3px solid #f0b429; }
        .notif-empty { padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; }

        /* Page Content */
        .page-content { padding: 24px 28px; flex: 1; }

        /* Overlay */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }
        .overlay.show { display: block; }

        /* Hamburger */
        .hamburger { display: none !important; }

        /* Responsive - Tablet & Mobile */
    @media (max-width: 1024px) {
    .sidebar { transform: translateX(-250px); }
    .sidebar.open { transform: translateX(0); }
    .main-content { margin-left: 0 !important; width: 100% !important; }
    .hamburger {
        display: flex !important;
        flex-direction: column;
        gap: 5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        margin-right: 10px;
    }
    .hamburger span { width: 22px; height: 2px; background: #1e3a5f; display: block; border-radius: 2px; }
    }

    @media (max-width: 768px) {
        .topbar { padding: 10px 14px; }
        .topbar-title { font-size: 13px; }
        .page-content { padding: 12px; }
        .topbar-date span#tanggal { display: none; }
        .notif-dropdown { width: 260px; right: -60px; }
    }

    @media (max-width: 480px) {
        .topbar { padding: 8px 10px; }
        .topbar-title { font-size: 12px; }
        .page-content { padding: 8px; }
        .topbar-date { font-size: 11px; }
        .notif-dropdown { width: 240px; right: -80px; }
    }

    @media (min-width: 1025px) {
        .hamburger { display: none !important; }
    }
    
    /* Grid Responsive */
    @media (max-width: 1024px) {
        .grid.grid-cols-4 { grid-template-columns: repeat(2, 1fr) !important; }
        .grid.grid-cols-3 { grid-template-columns: repeat(2, 1fr) !important; }
    }

    @media (max-width: 640px) {
        .grid.grid-cols-4 { grid-template-columns: repeat(1, 1fr) !important; }
        .grid.grid-cols-3 { grid-template-columns: repeat(1, 1fr) !important; }
        .grid.grid-cols-2 { grid-template-columns: repeat(1, 1fr) !important; }
        table { display: block; overflow-x: auto; white-space: nowrap; }
        .flex.gap-4 { flex-wrap: wrap; }
        .flex.gap-2 { flex-wrap: wrap; }
    }
    </style>
</head>
<body>

@auth
<!-- Overlay Mobile -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<div style="display: flex; min-height: 100vh; width: 100%;">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">

        <!-- Logo -->
        <div class="sidebar-logo">
            <img src="{{ asset('images/pesawat.png') }}" alt="Logo">
            <div>
                <span><span style="color:#f0b429;">MONPAS</span>KU</span>
                <div class="role-badge">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">

            @if(auth()->user()->role === 'pemohon')
                <div class="nav-label">Menu Utama</div>
                <a href="{{ route('pemohon.dashboard') }}"
                   class="nav-item {{ request()->routeIs('pemohon.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <div class="nav-label">Layanan</div>
                <a href="{{ route('pemohon.permohonan.index') }}"
                   class="nav-item {{ request()->routeIs('pemohon.permohonan.*') ? 'active' : '' }}">
                    <i class="fas fa-folder-open"></i> Permohonan Saya
                </a>
                <a href="{{ route('pemohon.berkas-persyaratan.index') }}"
                   class="nav-item {{ request()->routeIs('pemohon.berkas-persyaratan.*') ? 'active' : '' }}">
                    <i class="fas fa-file-download"></i> Berkas Persyaratan
                </a>
                <div class="nav-label">Akun</div>
                <a href="{{ route('profile.edit') }}"
                   class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> Edit Profil
                </a>
            @endif

            @if(auth()->user()->role === 'administrator')
                <div class="nav-label">Menu Utama</div>
                <a href="{{ route('administrator.dashboard') }}"
                   class="nav-item {{ request()->routeIs('administrator.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <div class="nav-label">Manajemen</div>
                <a href="{{ route('administrator.permohonan.index') }}"
                   class="nav-item {{ request()->routeIs('administrator.permohonan.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Permohonan
                </a>
                <a href="{{ route('administrator.kartu-pas.index') }}"
                   class="nav-item {{ request()->routeIs('administrator.kartu-pas.*') ? 'active' : '' }}">
                    <i class="fas fa-id-card"></i> Kartu PAS
                </a>
                <a href="{{ route('administrator.monitoring-kuota.index') }}"
                   class="nav-item {{ request()->routeIs('administrator.monitoring-kuota.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Monitoring Kuota
                </a>
                <a href="{{ route('administrator.instansi.index') }}"
                   class="nav-item {{ request()->routeIs('administrator.instansi.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Instansi
                </a>
                <div class="nav-label">Dokumen</div>
                <a href="{{ route('administrator.berkas-persyaratan.index') }}"
                   class="nav-item {{ request()->routeIs('administrator.berkas-persyaratan.*') ? 'active' : '' }}">
                    <i class="fas fa-file-upload"></i> Berkas Persyaratan
                </a>
                <div class="nav-label">Laporan</div>
                <a href="{{ route('administrator.laporan.index') }}"
                   class="nav-item {{ request()->routeIs('administrator.laporan.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Laporan Bulanan
                </a>
                <div class="nav-label">Kelola User</div>
                <a href="{{ route('administrator.users.index') }}"
                class="nav-item {{ request()->routeIs('administrator.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Manajemen User
                </a>
                <div class="nav-label">Akun</div>
                <a href="{{ route('profile.edit') }}"
                   class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> Edit Profil
                </a>
            @endif

            @if(auth()->user()->role === 'verifikator')
                <div class="nav-label">Menu Utama</div>
                <a href="{{ route('verifikator.dashboard') }}"
                   class="nav-item {{ request()->routeIs('verifikator.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <div class="nav-label">Verifikasi</div>
                <a href="{{ route('verifikator.permohonan.index') }}"
                   class="nav-item {{ request()->routeIs('verifikator.permohonan.*') ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i> Verifikasi Permohonan
                </a>
                <div class="nav-label">Akun</div>
                <a href="{{ route('profile.edit') }}"
                   class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> Edit Profil
                </a>
            @endif

        </nav>

        <!-- User Info -->
        <div class="sidebar-user">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="overflow:hidden;">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-email">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <div class="flex items-center">
                <!-- Hamburger Button -->
                <button class="hamburger" id="hamburgerBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="topbar-title">
                    @isset($header) {{ $header }} @endisset
                </div>
            </div>

            <div class="flex items-center">
                <!-- Notifikasi Bell (khusus administrator) -->
                @if(auth()->user()->role === 'administrator')
                @php
                    $kartuHampirKadaluarsa = App\Models\KartuPas::where('status', 'aktif')
                        ->whereBetween('tanggal_berlaku', [now(), now()->addDays(30)])
                        ->orderBy('tanggal_berlaku')
                        ->get();
                @endphp
                <div class="notif-bell" id="notifBell">
                    <i class="fas fa-bell" onclick="toggleNotif()"></i>
                    @if($kartuHampirKadaluarsa->count() > 0)
                        <div class="notif-badge">{{ $kartuHampirKadaluarsa->count() }}</div>
                    @endif
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">🔔 Notifikasi Kartu PAS</div>
                        @forelse($kartuHampirKadaluarsa as $kartu)
                        @php $sisaHari = now()->diffInDays($kartu->tanggal_berlaku); @endphp
                        <div class="notif-item {{ $sisaHari <= 7 ? 'danger' : 'warning' }}">
                            <div class="notif-name">{{ $kartu->nama_pemegang }}</div>
                            <div class="notif-detail">
                                {{ $kartu->nomor_kartu }} · {{ $kartu->perusahaan }}<br>
                                Berakhir dalam <strong>{{ $sisaHari }} hari</strong>
                                ({{ $kartu->tanggal_berlaku->format('d M Y') }})
                            </div>
                        </div>
                        @empty
                        <div class="notif-empty">Tidak ada notifikasi</div>
                        @endforelse
                    </div>
                </div>
                @endif

                <!-- Waktu Realtime -->
                <div class="topbar-date flex items-center gap-3">
                    <span><i class="fas fa-calendar-alt mr-1"></i><span id="tanggal"></span></span>
                    <span><i class="fas fa-clock mr-1"></i><span id="jam" style="font-weight:600; color:#1e3a5f;"></span></span>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            {{ $slot }}
        </div>

    </div>

</div>
@endauth

<script>
    // Waktu Realtime
    function updateWaktu() {
        const now       = new Date();
        const hari      = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const bulan     = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const namaHari  = hari[now.getDay()];
        const tgl       = now.getDate();
        const namaBulan = bulan[now.getMonth()];
        const tahun     = now.getFullYear();
        const jam       = String(now.getHours()).padStart(2, '0');
        const menit     = String(now.getMinutes()).padStart(2, '0');
        const detik     = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('tanggal').textContent = `${namaHari}, ${tgl} ${namaBulan} ${tahun}`;
        document.getElementById('jam').textContent     = `${jam}:${menit}:${detik}`;
    }
    updateWaktu();
    setInterval(updateWaktu, 1000);

    // Notifikasi Bell
    function toggleNotif() {
        document.getElementById('notifDropdown').classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        const bell = document.getElementById('notifBell');
        if (bell && !bell.contains(e.target)) {
            document.getElementById('notifDropdown').classList.remove('show');
        }
    });

    // Sidebar Mobile
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('overlay').classList.add('show');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('overlay').classList.remove('show');
    }
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', openSidebar);
    }
</script>

</body>
</html>