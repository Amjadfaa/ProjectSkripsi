<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
  public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name'       => ['required', 'string', 'max:255'],
        'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'role'       => ['required', 'in:pemohon,administrator,verifikator'],
        'perusahaan' => ['nullable', 'string', 'max:255', 'required_if:role,pemohon'],
        'password'   => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name'       => $request->name,
        'email'      => $request->email,
        'password'   => Hash::make($request->password),
        'role'       => $request->role,
        'perusahaan' => $request->role === 'pemohon' ? $request->perusahaan : null,
    ]);

    event(new Registered($user));

    Auth::login($user);

    return match($user->role) {
        'administrator' => redirect()->route('administrator.dashboard'),
        'verifikator'   => redirect()->route('verifikator.dashboard'),
        default         => redirect()->route('pemohon.dashboard'),
    };
    }
}
