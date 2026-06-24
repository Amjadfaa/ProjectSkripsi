<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - MONPASKU</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .input-box::placeholder { color: #9ca3af; font-size: 13px; }
        .input-box:focus { box-shadow: 0 0 0 2px #f0b42955; }
        .submit-btn {
            background: #f0b429;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #222;
            cursor: pointer;
            transition: opacity 0.2s;
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }
        .submit-btn:hover { opacity: 0.88; }
        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
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

<div class="card">

    <!-- Logo & Nama -->
    <div class="flex items-center justify-between mb-6">
        <img src="{{ asset('images/pesawat.png') }}" alt="Logo" class="w-16 h-16 object-contain">
        <h1 class="text-3xl font-black tracking-wide" style="font-weight: 900;">
            <span class="text-blue-600">MONPAS</span><span class="text-gray-900">KU</span>
        </h1>
    </div>

    <h2 class="text-lg font-bold text-gray-800 mb-1">Reset Password</h2>
    <p class="text-sm text-gray-500 mb-5">Masukkan email dan password baru kamu.</p>

    @if(session('status'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm">✅ {{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('reset.manual.store') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <input type="email" name="email" class="input-box" placeholder="Email"
                   value="{{ old('email') }}" required>
        </div>

        <!-- Password Baru -->
        <div class="mb-3" style="position: relative;">
            <input type="password" name="password" id="password" class="input-box"
                   placeholder="Password Baru" required>
            <button type="button" class="eye-btn" data-target="password" data-eye="eye1">
                <i id="eye1" class="fa fa-eye" style="color:#888;"></i>
            </button>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-6" style="position: relative;">
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="input-box" placeholder="Konfirmasi Password Baru" required>
            <button type="button" class="eye-btn" data-target="password_confirmation" data-eye="eye2">
                <i id="eye2" class="fa fa-eye" style="color:#888;"></i>
            </button>
        </div>

        <button type="submit" class="submit-btn mb-4">RESET PASSWORD</button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Kembali ke Login
            </a>
        </div>

    </form>
</div>

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

</body>
</html>