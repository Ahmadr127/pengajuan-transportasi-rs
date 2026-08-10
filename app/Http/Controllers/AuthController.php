<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SimutuUserSyncService;

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

        // Sinkronisasi ringan dari Simutu: segarkan profil + status user yang login.
        try {
            app(SimutuUserSyncService::class)->syncForUsername($request->username);
        } catch (\Throwable $e) {
            Log::warning('Simutu lazy sync gagal saat login: '.$e->getMessage());
        }

        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->onlyInput('username');
        }

        if (
            $user->simutu_status
            && in_array($user->simutu_status, config('simutu_sync.block_login_statuses', ['non-aktif']))
        ) {
            return back()
                ->withErrors(['username' => 'Akun dinonaktifkan di sistem Simutu.'])
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

