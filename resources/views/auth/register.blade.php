<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Register - MONPASKU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            background-color: #e8e8e8;
            border-radius: 24px;
            padding: 36px 32px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .input-box {
            width: 100%;
            padding: 12px 16px;
            border: none;
            border-radius: 10px;
            background: #ffffff;
            font-size: 14px;
            color: #374151;
            outline: none;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .input-box::placeholder {
            color: #9ca3af;
            font-size: 13px;
        }
        .input-box:focus {
            box-shadow: 0 0 0 2px #f0b42955;
        }
        select.input-box {
            appearance: auto;
            color: #374151;
        }
        .register-btn {
            background: #f0b429;
            border: none;
            border-radius: 10px;
            width: 100%;
            padding: 13px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #222;
            cursor: pointer;
            transition: opacity 0.2s;
            font-family: 'Poppins', sans-serif;
        }
        .register-btn:hover {
            opacity: 0.88;
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            z-index: 10;
            font-size: 16px;
            padding: 0;
        }
    </style>
</head>
<body>

<div class="card">

    <!-- Logo & Nama -->
    <div class="flex items-center justify-between mb-6">
        <img src="{{ asset('images/pesawat.png') }}" alt="Logo" class="w-16 h-16 object-contain">
        <h1 class="text-3xl font-black tracking-wide" style="font-weight: 600;">
            <span class="text-blue-600">MONPAS</span><span class="text-gray-900">KU</span>
        </h1>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nama Lengkap -->
        <div class="mb-3">
            <input type="text" name="name" class="input-box" placeholder="Nama Lengkap"
                   value="{{ old('name') }}" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <input type="email" name="email" class="input-box" placeholder="Email"
                   value="{{ old('email') }}" required>
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Role hidden -->
        <input type="hidden" name="role" value="pemohon">

       <!-- Nama Perusahaan -->
        <div class="mb-3">
            <input type="text" name="perusahaan" class="input-box" placeholder="Nama Perusahaan"
                value="{{ old('perusahaan') }}" required>
            @error('perusahaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div class="mb-3" style="position: relative;">
            <input type="password" name="password" id="password" class="input-box" placeholder="Password" required>
            <button type="button" class="eye-btn" data-target="password" data-eye="eye1">
                <i id="eye1" class="fa fa-eye" style="color:#888;"></i>
            </button>
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-6" style="position: relative;">
            <input type="password" name="password_confirmation" id="password_confirmation" class="input-box" placeholder="Konfirmasi Password" required>
            <button type="button" class="eye-btn" data-target="password_confirmation" data-eye="eye2">
                <i id="eye2" class="fa fa-eye" style="color:#888;"></i>
            </button>
        </div>

        <!-- Register Button -->
        <button type="submit" class="register-btn mb-4">
            REGISTER
        </button>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-gray-700 font-semibold hover:underline">Login</a>
            </p>
        </div>

    </form>
</div>

    <script>
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
    </script>
</body>
</html>