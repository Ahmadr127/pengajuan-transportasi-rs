<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index()
    {
        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->orderBy('id')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->groupedPermissions();
        $rolePermissions = [];

        return view('admin.roles.create', compact('permissions', 'rolePermissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->roles->create($request->validated());

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        $permissions = $this->groupedPermissions();
        $rolePermissions = $role->permissions()->pluck('permissions.id')->all();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        if ($role->is_super) {
            return back()->with('error', 'Role super admin tidak dapat diubah.');
        }

        $data = $request->validated();

        if ($role->is_locked && $role->name !== $data['name']) {
            return back()->with('error', 'Nama role inti tidak dapat diubah.');
        }

        $this->roles->update($role, $data);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_locked || $role->is_super) {
            return back()->with('error', 'Role inti tidak dapat dihapus.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Role masih dipakai user. Pindahkan user terlebih dahulu.');
        }

        $this->roles->delete($role);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }

    private function groupedPermissions()
    {
        return Permission::query()
            ->orderBy('group')
            ->orderBy('label')
            ->get()
            ->groupBy('group');
    }
}
