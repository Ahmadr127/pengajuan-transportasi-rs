<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\LandingResolver;
use App\Services\SimutuUserSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseApiController
{
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $username = $request->validated('username');
        $password = $request->validated('password');

        try {
            app(SimutuUserSyncService::class)->syncForUsername($username);
        } catch (\Throwable $e) {
            Log::warning('Simutu lazy sync gagal saat login: '.$e->getMessage());
        }

        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return $this->error('Username atau password salah.', 401);
        }

        if ($user->simutu_status && in_array($user->simutu_status, config('simutu_sync.block_login_statuses', ['non-aktif']), true)) {
            return $this->error('Akun dinonaktifkan di sistem Simutu.', 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new \App\Http\Resources\UserResource($user->load('roles', 'driver')),
            'permissions' => $user->permissionNames(),
        ], 'Login berhasil');
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();
        return $this->success(null, 'Logout berhasil');
    }

    public function me(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user()->load('roles', 'driver');
        return $this->success([
            'user' => new \App\Http\Resources\UserResource($user),
            'permissions' => $user->permissionNames(),
            'roles' => $user->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'label' => $r->label])->values(),
        ]);
    }
}