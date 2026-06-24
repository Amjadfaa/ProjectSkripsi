<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MONPASKU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { margin: 0; padding: 0; }
        .hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 24px;
            font-size: 14px;
            font-weight: 600;
            opacity: 0.9;
            transition: opacity 0.2s;
        }
        .nav-links a:hover { opacity: 1; }
        .nav-links .btn-login {
            background: rgba(255,255,255,0.15);
            padding: 8px 20px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .nav-links .btn-register {
            background: #f0b429;
            padding: 8px 20px;
            border-radius: 20px;
            color: #222 !important;
        }
        .hero-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
            color: white;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 16px;
            line-height: 1.2;
        }
        .hero-content p {
            font-size: 1.1rem;
            opacity: 0.85;
            max-width: 500px;
            margin-bottom: 36px;
            line-height: 1.7;
        }
        .hero-buttons {
            display: flex;
            gap: 16px;
        }
        .btn-primary {
            background: #f0b429;
            color: #222;
            padding: 14px 36px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn-primary:hover { opacity: 0.88; }
        .btn-secondary {
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 14px 36px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.3);
            transition: background 0.2s;
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.25); }

        /* Features */
        .features {
            background: #f8f9fa;
            padding: 80px 60px;
        }
        .features h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            color: #1e3a5f;
            margin-bottom: 10px;
        }
        .features p.subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 50px;
            font-size: 15px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 32px 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 10px;
        }
        .feature-card p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* Stats */
        .stats {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);
            padding: 60px;
            display: flex;
            justify-content: center;
            gap: 80px;
            color: white;
            text-align: center;
        }
        .stat-item h3 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 6px;
        }
        .stat-item p {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Footer */
        .footer {
            background: #111827;
            color: white;
            text-align: center;
            padding: 24px;
            font-size: 13px;
            opacity: 0.8;
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<div class="hero">

    <!-- Navbar -->
    <nav class="navbar" style="border-bottom: 1px solid rgba(255,255,255,0.15); background: rgba(0,0,0,0.2); backdrop-filter: blur(8px);">
        <div class="nav-brand">
            <img src="{{ asset('images/pesawat.png') }}" alt="Logo" style="width:48px; height:48px; object-fit:contain;">
            <span style="color:white; font-weight:800; font-size:1.3rem;">
                <span style="color:#f0b429;">MONPAS</span>KU
            </span>
        </div>
        <div class="nav-links">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="btn-login">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-register">Register</a>
                @endif
            @endif
        </div>
    </nav>

    <!-- Hero Content -->
    <div class="hero-content">
        <h1>Sistem Monitoring<br><span style="color:#f0b429;">PAS Bandara</span></h1>
        <p>Platform digital untuk pengelolaan dan monitoring Pas Bandara secara efisien, transparan, dan terintegrasi.</p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn-primary">Mulai Sekarang</a>
        </div>
    </div>

</div>

<!-- Features Section -->
<div class="features">
    <h2>Fitur Unggulan</h2>
    <p class="subtitle">Sistem lengkap untuk semua kebutuhan pengelolaan PAS Bandara</p>
    <div class="features-grid">

        <div class="feature-card">
            <div class="feature-icon" style="background:#eff6ff;">📄</div>
            <h3>Download Formulir</h3>
            <p>Pemohon dapat mengunduh formulir permohonan PAS bandara dengan mudah kapan saja.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon" style="background:#f0fdf4;">📁</div>
            <h3>Upload Berkas</h3>
            <p>Upload berkas persyaratan secara digital tanpa perlu datang langsung ke kantor.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon" style="background:#fefce8;">✅</div>
            <h3>Verifikasi Online</h3>
            <p>Verifikator dapat memeriksa dan memverifikasi berkas pemohon secara online.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon" style="background:#fdf4ff;">🪪</div>
            <h3>Kelola Kartu PAS</h3>
            <p>Administrator dapat mengelola masa berlaku kartu PAS dan mendapat notifikasi kadaluarsa.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon" style="background:#fff7ed;">📊</div>
            <h3>Laporan Bulanan</h3>
            <p>Dashboard lengkap dengan laporan bulanan dan statistik permohonan secara real-time.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon" style="background:#fef2f2;">🔒</div>
            <h3>Keamanan Data</h3>
            <p>Sistem keamanan berlapis dengan autentikasi role-based untuk setiap pengguna.</p>
        </div>

    </div>
</div>

<!-- Stats Section -->
<div class="stats">
    <div class="stat-item">
        <h3>3</h3>
        <p>Role Pengguna</p>
    </div>
    <div class="stat-item">
        <h3>100%</h3>
        <p>Digital Process</p>
    </div>
    <div class="stat-item">
        <h3>24/7</h3>
        <p>Akses Online</p>
    </div>
    <div class="stat-item">
        <h3>Fast</h3>
        <p>Proses Verifikasi</p>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>© {{ date('Y') }} MONPASKU - Sistem Monitoring PAS Bandara. All rights reserved.</p>
</div>

</body>
</html>