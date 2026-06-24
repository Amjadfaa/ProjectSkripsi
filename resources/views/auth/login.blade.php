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
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #e8e8e8;
            border-radius: 24px;
            padding: 40px 32px 36px 32px;
            width: 100%;
            max-width: 320px;
        }
        .input-line {
            border: none;
            border-bottom: 2px solid #aaa;
            border-radius: 0;
            outline: none;
            width: 100%;
            padding: 6px 0;
            font-size: 15px;
            color: #222;
            background: transparent;
        }
        .input-line:focus {
            border-bottom-color: #764ba2;
        }
        .input-line::placeholder {
            color: #888;
        }
       .login-btn {
            background: #f0b429;
            border: none;
            border-radius: 30px;
            padding: 10px 60px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #222;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .login-btn:hover {
            opacity: 0.88;
        }
        .eye-btn {
            position: absolute;
            right: 0;
            top: 60%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            z-index: 10;
            padding: 0;
        }
    </style>
</head>
<body>

<div class="card shadow-2xl">

    <!-- Logo & Nama -->
    <div class="flex items-center justify-between mb-10">
        <img src="{{ asset('images/pesawat.png') }}" alt="Logo" class="w-16 h-16 object-contain">
        <h1 class="text-3xl font-black tracking-wide" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
        <span class="text-blue-600">MONPAS</span><span class="text-gray-900">KU</span>
    </h1>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 text-sm text-green-600 text-center">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-7">
            <label class="block text-sm text-gray-600 mb-1">Username</label>
            <input type="email"
                   name="email"
                   class="input-line"
                   value="{{ old('email') }}"
                   required autofocus>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4" style="position: relative;">
            <label class="block text-sm text-gray-600 mb-1">Password</label>
            <input type="password" name="password" id="password" class="input-line" required>
            <button type="button" class="eye-btn" data-target="password" data-eye="eye1">
                <i id="eye1" class="fa fa-eye" style="color:#888;"></i>
            </button>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Forgot Password -->
        <div class="text-center mb-6">
            @if (Route::has('password.request'))
                <a href="{{ route('reset.manual') }}"
                class="text-sm text-gray-500 hover:text-purple-600">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="text-center mb-5">
            <button type="submit" class="login-btn">
                LOGIN
            </button>
        </div>

        <!-- Register -->
        <div class="text-center">
            <p class="text-sm text-gray-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-purple-600 font-semibold hover:underline">Register</a>
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