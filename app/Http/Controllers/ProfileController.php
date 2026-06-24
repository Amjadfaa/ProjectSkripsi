<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diupdate.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success_password', 'Password berhasil diubah.');
    }
}