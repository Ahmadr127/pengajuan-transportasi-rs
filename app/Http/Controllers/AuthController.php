<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->isDriver()) {
                return redirect()->route('driver.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->onlyInput('username');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->isDriver()) {
            return redirect()->route('driver.dashboard');
        }
        
        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

