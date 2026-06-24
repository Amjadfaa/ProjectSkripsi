<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('administrator.users.index', compact('users'));
    }

    public function create()
    {
        return view('administrator.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users'],
            'role'       => ['required', 'in:pemohon,administrator,verifikator'],
            'perusahaan' => ['nullable', 'string'],
            'password'   => ['required', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'perusahaan' => $request->perusahaan,
            'password'   => Hash::make($request->password),
        ]);

        return redirect()->route('administrator.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        return view('administrator.users.edit', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email,' . $id],
            'role'       => ['required', 'in:pemohon,administrator,verifikator'],
            'perusahaan' => ['nullable', 'string'],
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'perusahaan' => $request->perusahaan,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['min:8', 'confirmed'],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('administrator.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('administrator.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}