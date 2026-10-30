<?php

namespace App\Http\Controllers\Api;

use App\Services\MenuService;
use Illuminate\Http\Request;

class NavigationController extends BaseApiController
{
    public function __construct(private readonly MenuService $menus)
    {
    }

    /**
     * Menu + permission milik user yang sedang login.
     *
     * Klien mobile memakai endpoint ini untuk merender navigasi secara dinamis
     * (tanpa hardcode role) dan untuk menyembunyikan tombol tanpa permission.
     */
    public function show(Request $request)
    {
        $user = $request->user()->loadMissing('roles');
        $navigation = $this->menus->navigation($user);

        return $this->success([
            'context' => $navigation['context'],
            'landing' => $navigation['landing'],
            'sidebar' => $navigation['sidebar'],
            'bottom_nav' => $navigation['bottom_nav'],
            'permissions' => $user->permissionNames(),
            'is_super_admin' => $user->isSuperAdmin(),
            'roles' => $user->roles
                ->map(fn ($role) => ['id' => $role->id, 'name' => $role->name, 'label' => $role->label])
                ->values(),
        ]);
    }
}
