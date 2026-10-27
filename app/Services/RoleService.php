<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Support\AclCache;

class RoleService
{
    public function create(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'label' => $data['label'],
            'description' => $data['description'] ?? null,
            'is_locked' => false,
            'is_super' => false,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);
        AclCache::flush();

        return $role;
    }

    public function update(Role $role, array $data): void
    {
        $oldName = $role->name;

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
    }

    public function delete(Role $role): void
    {
        $role->delete();
        AclCache::flush();
    }
}
