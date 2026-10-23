<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use App\Support\AclCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class MenuService
{
    /**
     * Menu aktif yang boleh diakses user untuk context tertentu.
     *
     * @return Collection<int, Menu>
     */
    public function forUser(User $user, string $context): Collection
    {
        $cacheKey = 'acl.menu.'.$user->getKey().'.'.$context.'.'.AclCache::version();

        return Cache::remember($cacheKey, 300, function () use ($user, $context) {
            return Menu::query()
                ->where('is_active', true)
                ->where('context', $context)
                ->orderBy('sort_order')
                ->get()
                ->filter(fn (Menu $menu) => $this->allowed($user, $menu))
                ->values();
        });
    }

    /**
     * Menu sidebar dikelompokkan per section.
     *
     * @return Collection<string, Collection<int, Menu>>
     */
    public function sidebar(User $user, string $context): Collection
    {
        return $this->forUser($user, $context)
            ->where('show_sidebar', true)
            ->groupBy(fn (Menu $menu) => $menu->section ?: '');
    }

    /**
     * Menu bottom navigation.
     *
     * @return Collection<int, Menu>
     */
    public function bottomNav(User $user, string $context): Collection
    {
        return $this->forUser($user, $context)
            ->where('show_bottom_nav', true)
            ->values();
    }

    /**
     * Halaman pertama yang boleh diakses user, tanpa hardcode role.
     */
    public function landingRoute(User $user): ?string
    {
        // Urutan context hanya urutan tampilan, bukan role hardcode.
        foreach (['admin', 'driver', 'user'] as $context) {
            $menus = Menu::query()
                ->where('is_active', true)
                ->where('context', $context)
                ->orderBy('sort_order')
                ->get();

            // Prioritas: menu yang ditandai landing page.
            foreach ($menus->where('is_landing', true) as $menu) {
                if ($menu->route_name
                    && Route::has($menu->route_name)
                    && $this->allowed($user, $menu)) {
                    return $menu->route_name;
                }
            }

            // Fallback: menu pertama yang boleh diakses.
            foreach ($menus as $menu) {
                if ($menu->route_name
                    && Route::has($menu->route_name)
                    && $this->allowed($user, $menu)) {
                    return $menu->route_name;
                }
            }
        }

        return null;
    }

    /**
     * Cari route dari key menu (mis. tombol Riwayat di header).
     */
    public function routeFor(User $user, string $key): ?string
    {
        $menu = Menu::query()->where('key', $key)->where('is_active', true)->first();

        if (! $menu || ! $menu->route_name || ! Route::has($menu->route_name)) {
            return null;
        }

        return $this->allowed($user, $menu) ? $menu->route_name : null;
    }

    public function allowed(User $user, Menu $menu): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (! $menu->permission_name) {
            return true;
        }

        return $user->hasPermission($menu->permission_name);
    }

    /**
     * Context tampilan ditentukan dari menu yang tersedia untuk user.
     */
    public function contextFor(User $user): string
    {
        foreach (['admin', 'driver', 'user'] as $context) {
            if ($this->sidebar($user, $context)->isNotEmpty()
                || $this->bottomNav($user, $context)->isNotEmpty()) {
                return $context;
            }
        }

        return 'user';
    }
}
