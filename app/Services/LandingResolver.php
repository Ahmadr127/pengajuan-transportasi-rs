<?php

namespace App\Services;

use App\Models\User;

class LandingResolver
{
    public function __construct(private readonly MenuService $menus)
    {
    }

    public function routeNameFor(User $user): ?string
    {
        return $this->menus->landingRoute($user);
    }

    public function redirectFor(User $user)
    {
        $route = $this->routeNameFor($user);

        if (! $route) {
            abort(403);
        }

        return redirect()->route($route);
    }
}
