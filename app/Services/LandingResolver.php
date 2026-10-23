<?php

namespace App\Services;

use App\Models\User;

class LandingResolver
{
    public function __construct(private readonly MenuService $menus)
    {
    }

    public function routeNameFor(User $user): string
    {
        return $this->menus->landingRoute($user) ?? 'no.access';
    }

    public function redirectFor(User $user)
    {
        return redirect()->route($this->routeNameFor($user));
    }
}
