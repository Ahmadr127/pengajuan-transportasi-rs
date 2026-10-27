<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\LandingResolver;
use App\Services\SimutuUserSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(private readonly LandingResolver $landing) {}

    public function create()
    {
        if (Auth::check()) {
            return $this->landing->redirectFor(Auth::user());
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $username = $request->validated('username');
        $password = $request->validated('password');
        $remember = $request->boolean('remember');

        // Sinkronisasi ringan dari Simutu: segarkan profil + status user yang login.
        try {
            app(SimutuUserSyncService::class)->syncForUsername($username);
        } catch (\Throwable $e) {
            Log::warning('Simutu lazy sync gagal saat login: '.$e->getMessage());
        }

        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
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

        return $this->landing->redirectFor(Auth::user());
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
