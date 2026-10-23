<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AclCache;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'alpha_dash', 'max:50', 'unique:roles,name'],
            'label' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'label' => $data['label'],
            'description' => $data['description'] ?? null,
            'is_locked' => false,
            'is_super' => false,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);
        AclCache::flush();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        $permissions = $this->groupedPermissions();
        $rolePermissions = $role->permissions()->pluck('permissions.id')->all();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_super) {
            return back()->with('error', 'Role super admin tidak dapat diubah.');
        }

        $data = $request->validate([
            'name' => ['required', 'alpha_dash', 'max:50', Rule::unique('roles', 'name')->ignore($role->id)],
            'label' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $oldName = $role->name;

        if ($role->is_locked && $oldName !== $data['name']) {
            return back()->with('error', 'Nama role inti tidak dapat diubah.');
        }

        $role->update([
            'name' => $data['name'],
            'label' => $data['label'],
            'description' => $data['description'] ?? null,
        ]);

        if ($oldName !== $data['name']) {
            User::whereHas('roles', fn ($q) => $q->where('roles.id', $role->id))
                ->update(['role' => $data['name']]);
        }

        $role->permissions()->sync($data['permissions'] ?? []);
        AclCache::flush();

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

        $role->delete();
        AclCache::flush();

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
