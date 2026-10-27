<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserAccountService;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles', 'driver')->orderBy('first_name');

        if ($request->filled('search')) {
            $s = '%'.strtolower($request->search).'%';
            $query->whereRaw('LOWER(first_name) LIKE ? OR LOWER(last_name) LIKE ? OR LOWER(username) LIKE ? OR LOWER(nip) LIKE ?', [$s, $s, $s, $s]);
        }
        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }
        if ($request->filled('unit')) {
            $query->where('unit_kerja', $request->unit);
        }
        if ($request->filled('simutu_status')) {
            $query->where('simutu_status', $request->simutu_status);
        }

        $perPage = min((int) $request->get('per_page', 15), 100);
        $users = $query->paginate($perPage);

        return $this->paginated($users, \App\Http\Resources\UserResource::collection($users));
    }

    public function show(User $user)
    {
        return $this->success(new \App\Http\Resources\UserResource($user->load('roles', 'driver')));
    }

    public function store(StoreUserRequest $request)
    {
        $role = Role::findOrFail($request->validated('role_id'));
        $user = app(UserAccountService::class)->create($request->validated(), $role);
        return $this->created(new \App\Http\Resources\UserResource($user->load('roles', 'driver')), 'User berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // guard last super admin
        if ($user->isSuperAdmin()) {
            $newRoleId = $request->validated('role_id');
            if ($newRoleId) {
                $newRole = Role::find($newRoleId);
                if (! $newRole || ! $newRole->is_super) {
                    // check if last super admin
                    $superCount = User::whereHas('roles', fn ($q) => $q->where('is_super', true))->count();
                    if ($superCount <= 1) {
                        return $this->error('Tidak dapat mengubah role super admin terakhir.', 422);
                    }
                }
            }
        }

        $roleId = $request->validated('role_id') ?? $user->primaryRole()?->id;
        $role = Role::findOrFail($roleId);
        app(UserAccountService::class)->update($user, $request->validated(), $role);
        return $this->success(new \App\Http\Resources\UserResource($user->fresh()->load('roles', 'driver')), 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            $superCount = User::whereHas('roles', fn ($q) => $q->where('is_super', true))->count();
            if ($superCount <= 1) {
                return $this->error('Tidak dapat menghapus super admin terakhir.', 422);
            }
        }
        $user->delete();
        return $this->success(null, 'User berhasil dihapus.');
    }

    public function syncFromSimutu()
    {
        app(\App\Services\SimutuUserSyncService::class)->syncAll();
        return $this->success(null, 'Sync Simutu selesai.');
    }
}