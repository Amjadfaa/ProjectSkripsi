<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Login & Dokumen Persyaratan - MONPASKU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #eef2f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Background Blur Elements */
        body::before {
            content: "";
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30, 58, 95, 0.08) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            pointer-events: none;
            z-index: 0;
        }
        body::after {
            content: "";
            position: fixed;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(240, 180, 41, 0.08) 0%, transparent 70%);
            bottom: -80px;
            right: -80px;
            pointer-events: none;
            z-index: 0;
        }

        /* KONTENER CARD UTAMA (UNIFIED SPLIT-CARD SEPERTI REFERENSI) */
        .unified-card {
            width: 100%;
            max-width: 1040px;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12), 0 0 1px 1px rgba(226, 232, 240, 0.8);
            display: grid;
            grid-template-columns: 1fr 1.08fr;
            overflow: hidden;
            position: relative;
            z-index: 1;
            min-height: 620px;
        }

        @media (max-width: 920px) {
            .unified-card {
                grid-template-columns: 1fr;
                border-radius: 22px;
                min-height: auto;
            }
        }

        /* ======================================================== */
        /* PANEL KIRI: FORM LOGIN (BERSIH, ELEGAN, DAN MINIMALIS)   */
        /* ======================================================== */
        .panel-form {
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            min-height: 100%;
        }

        @media (max-width: 920px) {
            .panel-form {
                padding: 36px 28px;
            }
        }

        @media (max-width: 480px) {
            .panel-form {
                padding: 28px 20px;
            }
        }

        .panel-form-inner {
            width: 100%;
            max-width: 400px;
            margin: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 28px;
        }
        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }
        .brand-title {
            font-size: 21px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: 0.02em;
        }
        .brand-sub {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        /* Form Greeting */
        .form-heading {
            margin-bottom: 24px;
        }
        .form-title {
            font-size: 23px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.02em;
        }
        .form-subtitle {
            font-size: 12.5px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.5;
        }

        /* Modern Inputs */
        .form-group-modern {
            margin-bottom: 20px;
            position: relative;
        }
        .form-label-modern {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 7px;
        }
        .input-wrap {
            position: relative;
        }
        .input-modern {
            width: 100%;
            padding: 12px 14px 12px 40px;
            font-size: 13px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-modern:focus {
            background: #ffffff;
            border-color: #1e3a5f;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }
        .eye-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
        }
        .eye-toggle-btn:hover {
            color: #475569;
        }

        /* Tombol Submit */
        .btn-submit-login {
            width: 100%;
            padding: 13px 20px;
            background: #1e3a5f;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(30, 58, 95, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit-login:hover {
            background: #152943;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(30, 58, 95, 0.35);
        }

        .login-footer-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
        }
        .login-footer-info i {
            color: #f0b429;
            font-size: 12px;
        }

        /* ================================================================ */
        /* PANEL KANAN: DOKUMEN PERSYARATAN DENGAN STYLE VISUAL KARTU        */
        /* ================================================================ */
        .panel-showcase {
            background: linear-gradient(150deg, #1e3a5f 0%, #15273d 100%);
            padding: 34px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            color: #ffffff;
        }

        @media (max-width: 480px) {
            .panel-showcase {
                padding: 26px 18px;
            }
        }

        /* Ambient Glow & Watermark Pesawat */
        .panel-showcase::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(240, 180, 41, 0.15) 0%, transparent 70%);
            top: -60px;
            right: -60px;
            pointer-events: none;
        }
        .showcase-watermark {
            position: absolute;
            right: 15px;
            bottom: -20px;
            font-size: 160px;
            color: rgba(255, 255, 255, 0.03);
            pointer-events: none;
            transform: rotate(-15deg);
        }

        /* Header Panel Showcase */
        .showcase-header {
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
        }
        .showcase-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: rgba(240, 180, 41, 0.18);
            color: #f0b429;
            border: 1px solid rgba(240, 180, 41, 0.3);
            margin-bottom: 8px;
        }
        .showcase-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.01em;
            line-height: 1.25;
            color: #ffffff;
        }
        .showcase-desc {
            font-size: 11.5px;
            color: #cbd5e1;
            margin-top: 4px;
            line-height: 1.5;
        }

        /* KARTU FROSTED GLASS KONTEN PERSYARATAN */
        .glass-floating-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 20px;
            padding: 20px;
            position: relative;
            z-index: 2;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* Dokumen Box Item */
        .doc-entry-card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 14px 16px;
            transition: all 0.2s ease;
        }
        .doc-entry-card:hover {
            background: rgba(15, 23, 42, 0.55);
            border-color: rgba(240, 180, 41, 0.35);
        }

        .entry-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
        }
        .entry-pill-baru {
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            background: rgba(59, 130, 246, 0.25);
            color: #93c5fd;
            border: 1px solid rgba(147, 197, 253, 0.3);
        }
        .entry-pill-perpanjangan {
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            background: rgba(240, 180, 41, 0.25);
            color: #fde68a;
            border: 1px solid rgba(253, 230, 138, 0.3);
        }

        .entry-badge-ready {
            font-size: 10.5px;
            font-weight: 700;
            color: #6ee7b7;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .entry-badge-empty {
            font-size: 10.5px;
            font-weight: 600;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .entry-title {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
        }

        .entry-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
            flex-wrap: wrap;
        }

        .entry-notes {
            font-size: 11px;
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 10px;
            border-radius: 8px;
            margin-top: 8px;
            line-height: 1.4;
            border-left: 2px solid #f0b429;
        }

        .entry-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn-entry-download {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0b429;
            color: #1e3a5f;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-entry-download:hover {
            background: #e0a31b;
            transform: translateY(-1px);
        }

        .btn-entry-preview {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-entry-preview:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        /* Footer Showcase */
        .showcase-footer {
            position: relative;
            z-index: 2;
            margin-top: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

@php
    $dokumenBaru = $dokumenBaru ?? \App\Models\DokumenPersyaratan::where('kategori', 'baru')->first();
    $dokumenPerpanjangan = $dokumenPerpanjangan ?? \App\Models\DokumenPersyaratan::where('kategori', 'perpanjangan')->first();
@endphp

<div class="unified-card">

    {{-- ============================================================= --}}
    {{-- PANEL KIRI: FORM LOGIN ADMIN & SCANNER                       --}}
    {{-- ============================================================= --}}
    <div class="panel-form">

        <div class="panel-form-inner">
            {{-- Brand Logo & Title --}}
            <div class="brand-header">
                <div class="brand-logo-wrap">
                    <img src="{{ asset('images/pesawat.png') }}" alt="Logo MONPASKU" class="brand-logo-img">
                    <div>
                        <div class="brand-title">
                            <span style="color:#1e3a5f;">MONPAS</span><span style="color:#f0b429;">KU</span>
                        </div>
                        <p class="brand-sub">Sistem Monitoring Kartu PAS</p>
                    </div>
                </div>
            </div>

            {{-- Flash Notification Status --}}
            @if (session('status'))
                <div class="mb-4 text-xs bg-emerald-50 text-emerald-800 p-3 rounded-xl border border-emerald-200 font-medium">
                    ✅ {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 text-xs bg-rose-50 text-rose-800 p-3 rounded-xl border border-rose-200 font-medium">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            {{-- FORM LOGIN --}}
            <div id="formLoginContainer">
                <div class="form-heading">
                    <h2 class="form-title">Selamat Datang!</h2>
                    <p class="form-subtitle">Silakan masukkan akun Anda untuk masuk ke sistem kontrol.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="form-group-modern">
                        <label for="email" class="form-label-modern">Username / Email</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" id="email" class="input-modern" 
                                   value="{{ old('email') }}" placeholder="Masukkan email terdaftar" required autofocus>
                        </div>
                        @error('email')
                            <p style="color:#ef4444; font-size:11.5px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="form-group-modern">
                        <label for="password" class="form-label-modern">Kata Sandi (Password)</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="password" class="input-modern" 
                                   style="padding-right: 36px;" placeholder="••••••••" required>
                            <button type="button" class="eye-toggle-btn" data-target="password" data-eye="eye1" title="Lihat password">
                                <i id="eye1" class="fa fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <p style="color:#ef4444; font-size:11.5px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Forgot Password Link -->
                    <div style="text-align: right; margin-bottom: 22px; margin-top: -6px;">
                        @if (Route::has('password.request'))
                            <a href="{{ route('reset.manual') }}" style="font-size: 11.5px; color: #64748b; text-decoration: none; font-weight: 500;">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn-submit-login">
                            <span>MASUK KE SISTEM</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bottom Security Footer Note -->
            <div class="login-footer-info">
                <i class="fas fa-shield-halved"></i>
                <span>Sistem Monitoring PAS Bandara &bull; Jalur Akses Terenkripsi</span>
            </div>
        </div>
    </div>


    {{-- ============================================================= --}}
    {{-- PANEL KANAN: DOKUMEN PERSYARATAN (HERO VISUAL SHOWCASE)       --}}
    {{-- ============================================================= --}}
    <div class="panel-showcase">
        <i class="fas fa-plane-departure showcase-watermark"></i>

        {{-- Top Showcase Header --}}
        <div class="showcase-header">
            <div class="showcase-tag">
                <i class="fas fa-file-shield"></i> Panduan Resmi Pemohon
            </div>
            <h3 class="showcase-title">
                Dokumen Persyaratan PAS Bandara
            </h3>
            <p class="showcase-desc">
                Unduh berkas persyaratan resmi di bawah ini sebelum mengajukan pembuatan baru atau perpanjangan PAS Bandara.
            </p>
        </div>

        {{-- Floating Glass Card Box --}}
        <div class="glass-floating-box">

            {{-- 1. DOKUMEN PAS BARU --}}
            <div class="doc-entry-card">
                <div class="entry-top-row">
                    <span class="entry-pill-baru">
                        <i class="fas fa-id-card mr-1"></i> 1. PAS Baru
                    </span>

                    @if($dokumenBaru && $dokumenBaru->hasFile())
                        <span class="entry-badge-ready">
                            <i class="fas fa-circle-check"></i> Siap Diunduh
                        </span>
                    @else
                        <span class="entry-badge-empty">
                            <i class="fas fa-clock"></i> Belum Tersedia
                        </span>
                    @endif
                </div>

                <h4 class="entry-title">
                    Persyaratan Pembuatan PAS (Baru)
                </h4>

                @if($dokumenBaru && $dokumenBaru->hasFile())
                    <div class="entry-meta">
                        <span><i class="fas fa-paperclip mr-1 text-slate-400"></i>{{ $dokumenBaru->file_name }}</span>
                        <span>•</span>
                        <span>{{ $dokumenBaru->formatted_size }}</span>
                    </div>

                    @if($dokumenBaru->deskripsi)
                        <div class="entry-notes">
                            {{ $dokumenBaru->deskripsi }}
                        </div>
                    @endif

                    <div class="entry-actions">
                        <a href="{{ route('dokumen-persyaratan.public-download', $dokumenBaru->id) }}" 
                           class="btn-entry-download">
                            <i class="fas fa-download"></i> Unduh Berkas
                        </a>
                        <a href="{{ route('dokumen-persyaratan.public-preview', $dokumenBaru->id) }}" 
                           target="_blank" 
                           class="btn-entry-preview">
                            <i class="fas fa-eye"></i> Pratinjau
                        </a>
                    </div>
                @else
                    <p style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                        Berkas belum diunggah oleh administrator.
                    </p>
                @endif
            </div>

            {{-- 2. DOKUMEN PERPANJANGAN PAS --}}
            <div class="doc-entry-card">
                <div class="entry-top-row">
                    <span class="entry-pill-perpanjangan">
                        <i class="fas fa-arrows-rotate mr-1"></i> 2. Perpanjangan PAS
                    </span>

                    @if($dokumenPerpanjangan && $dokumenPerpanjangan->hasFile())
                        <span class="entry-badge-ready">
                            <i class="fas fa-circle-check"></i> Siap Diunduh
                        </span>
                    @else
                        <span class="entry-badge-empty">
                            <i class="fas fa-clock"></i> Belum Tersedia
                        </span>
                    @endif
                </div>

                <h4 class="entry-title">
                    Persyaratan Perpanjangan PAS Bandara
                </h4>

                @if($dokumenPerpanjangan && $dokumenPerpanjangan->hasFile())
                    <div class="entry-meta">
                        <span><i class="fas fa-paperclip mr-1 text-slate-400"></i>{{ $dokumenPerpanjangan->file_name }}</span>
                        <span>•</span>
                        <span>{{ $dokumenPerpanjangan->formatted_size }}</span>
                    </div>

                    @if($dokumenPerpanjangan->deskripsi)
                        <div class="entry-notes">
                            {{ $dokumenPerpanjangan->deskripsi }}
                        </div>
                    @endif

                    <div class="entry-actions">
                        <a href="{{ route('dokumen-persyaratan.public-download', $dokumenPerpanjangan->id) }}" 
                           class="btn-entry-download">
                            <i class="fas fa-download"></i> Unduh Berkas
                        </a>
                        <a href="{{ route('dokumen-persyaratan.public-preview', $dokumenPerpanjangan->id) }}" 
                           target="_blank" 
                           class="btn-entry-preview">
                            <i class="fas fa-eye"></i> Pratinjau
                        </a>
                    </div>
                @else
                    <p style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                        Berkas belum diunggah oleh administrator.
                    </p>
                @endif
            </div>

        </div>

        {{-- Showcase Bottom Info --}}
        <div class="showcase-footer">
            <span><i class="fas fa-shield-alt mr-1 text-[#f0b429]"></i> Aviation Security Compliance</span>
            <span>Bandara Udara</span>
        </div>

    </div>

</div>

<script>
    // Toggle Eye Password
    document.querySelectorAll('.eye-toggle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const eyeId    = this.getAttribute('data-eye');
            const field    = document.getElementById(targetId);
            const eye      = document.getElementById(eyeId);

            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        });
    });
</script>

</body>
</html>