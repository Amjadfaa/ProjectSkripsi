<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordManualController extends Controller
{
    public function create()
    {
        return view('auth.reset-password-manual');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'                 => ['required', 'email', 'exists:users,email'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'email.exists' => 'Email tidak ditemukan.',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah. Silakan login.');
    }
}