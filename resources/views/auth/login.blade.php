<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Login - MONPASKU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 16px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 36px 32px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .role-tab-btn {
            flex: 1;
            padding: 9px 12px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #64748b;
        }
        .role-tab-btn.active {
            background: #1e3a5f;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 58, 95, 0.25);
        }
        .input-line {
            border: none;
            border-bottom: 2px solid #cbd5e1;
            border-radius: 0;
            outline: none;
            width: 100%;
            padding: 8px 0;
            font-size: 14px;
            color: #1e293b;
            background: transparent;
            transition: border-color 0.2s;
        }
        .input-line:focus {
            border-bottom-color: #2563eb;
        }
        .input-line::placeholder {
            color: #94a3b8;
        }
        .login-btn {
            background: #f0b429;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #1e293b;
            cursor: pointer;
            width: 100%;
            transition: transform 0.15s, opacity 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(240, 180, 41, 0.4);
        }
        .login-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }
        .login-btn-camera {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }
        .login-btn-camera:hover {
            background: #1d4ed8;
        }
        .eye-btn {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            z-index: 10;
            padding: 4px;
        }
    </style>
</head>
<body>

<div class="login-card">

    <!-- Logo & Title -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/pesawat.png') }}" alt="Logo" class="w-12 h-12 object-contain">
            <div>
                <h1 class="text-2xl font-black tracking-wide leading-tight">
                    <span class="text-blue-600">MONPAS</span><span class="text-gray-900">KU</span>
                </h1>
                <p class="text-[11px] text-gray-500 font-medium">Sistem Monitoring Kartu PAS Bandara</p>
            </div>
        </div>
    </div>

    <!-- Role Switcher -->
    <div class="flex bg-gray-100 p-1.5 rounded-xl mb-6 border border-gray-200">
        <button type="button" id="tabAdminBtn" onclick="switchRole('admin')" class="role-tab-btn active">
            <i class="fas fa-user-shield mr-1"></i> Admin
        </button>
        <button type="button" id="tabCameraBtn" onclick="switchRole('camera')" class="role-tab-btn">
            <i class="fas fa-qrcode mr-1"></i> Scan QR
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session('status'))
        <div class="mb-4 text-xs bg-emerald-50 text-emerald-700 p-3 rounded-xl border border-emerald-200 font-medium text-center">
            ✅ {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 text-xs bg-rose-50 text-rose-700 p-3 rounded-xl border border-rose-200 font-medium text-center">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <!-- FORM LOGIN ADMIN -->
    <div id="formAdminContainer">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Username / Email</label>
                <input type="email" name="email" class="input-line" value="{{ old('email') }}" placeholder="masukkan email..." required autofocus>
                @error('email')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4 relative">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" class="input-line pr-8" placeholder="••••••••" required>
                    <button type="button" class="eye-btn" data-target="password" data-eye="eye1">
                        <i id="eye1" class="fa fa-eye text-gray-400 text-sm"></i>
                    </button>
                </div>
                @error('password')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Forgot Password -->
            <div class="text-right mb-6">
                @if (Route::has('password.request'))
                    <a href="{{ route('reset.manual') }}" class="text-xs text-gray-500 hover:text-blue-600 font-medium">
                        Lupa password?
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <div class="mb-4">
                <button type="submit" class="login-btn">
                    MASUK KONTROL ADMIN
                </button>
            </div>

            <!-- Register -->
            <div class="text-center pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Daftar Akun Baru</a>
                </p>
            </div>
        </form>
    </div>

    <!-- FORM LOGIN PERANGKAT KAMERA SCAN -->
    <div id="formCameraContainer" class="hidden">
        <form method="POST" action="{{ route('scan.login.submit') }}">
            @csrf

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-5 text-center">
                <i class="fas fa-video text-blue-600 text-2xl mb-1"></i>
                <p class="text-xs text-blue-800 font-bold">Modus Kamera Barcode Scan</p>
                <p class="text-[11px] text-blue-600">Masukkan Kode Akses khusus lokasi area perangkat kamera ini.</p>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Kode Akses Perangkat Kamera</label>
                <div class="relative">
                    <input type="text" name="kode_akses" id="kode_akses" class="input-line uppercase font-mono font-bold tracking-widest text-center"
                           placeholder="CONTOH: CAM-AREA-A" value="{{ old('kode_akses') }}" required>
                </div>
                @error('kode_akses')
                    <p class="text-rose-500 text-xs mt-1 text-center font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <button type="submit" class="login-btn login-btn-camera">
                    <i class="fas fa-camera mr-1.5"></i> MASUK MODE SCANNER
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function switchRole(role) {
        const tabAdmin  = document.getElementById('tabAdminBtn');
        const tabCamera = document.getElementById('tabCameraBtn');
        const formAdmin = document.getElementById('formAdminContainer');
        const formCam   = document.getElementById('formCameraContainer');

        if (role === 'camera') {
            tabAdmin.classList.remove('active');
            tabCamera.classList.add('active');
            formAdmin.classList.add('hidden');
            formCam.classList.remove('hidden');
            document.getElementById('kode_akses').focus();
        } else {
            tabCamera.classList.remove('active');
            tabAdmin.classList.add('active');
            formCam.classList.add('hidden');
            formAdmin.classList.remove('hidden');
        }
    }

    // Toggle Eye Password
    document.querySelectorAll('.eye-btn').forEach(function(btn) {
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

    @if(request()->get('role') === 'camera' || old('kode_akses') || (isset($loginMode) && $loginMode === 'camera'))
        switchRole('camera');
    @endif
</script>
</body>
</html>