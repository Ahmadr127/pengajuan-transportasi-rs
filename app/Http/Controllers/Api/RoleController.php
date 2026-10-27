<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends BaseApiController
{
    public function __construct(private readonly RoleService $roles) {}

    public function index(Request $request)
    {
        $roles = Role::query()->withCount(['permissions', 'users'])->orderBy('id')->get();
        return $this->success(\App\Http\Resources\RoleResource::collection($roles));
    }

    public function show(Role $role)
    {
        return $this->success(new \App\Http\Resources\RoleResource($role->load('permissions')));
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roles->create($request->validated());
        return $this->created(new \App\Http\Resources\RoleResource($role->load('permissions')), 'Role berhasil ditambahkan.');
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        if ($role->is_super) {
            return $this->error('Role super admin tidak dapat diubah.', 422);
        }
        $data = $request->validated();
        if ($role->is_locked && $role->name !== $data['name']) {
            return $this->error('Nama role inti tidak dapat diubah.', 422);
        }
        $this->roles->update($role, $data);
        return $this->success(new \App\Http\Resources\RoleResource($role->fresh()->load('permissions')), 'Role berhasil diupdate.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_locked || $role->is_super) {
            return $this->error('Role inti tidak dapat dihapus.', 422);
        }
        if ($role->users()->exists()) {
            return $this->error('Role masih dipakai user. Pindahkan user terlebih dahulu.', 422);
        }
        $this->roles->delete($role);
        return $this->success(null, 'Role berhasil dihapus.');
    }
}