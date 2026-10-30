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
            $menus = Menu::query()
                ->where('is_active', true)
                ->where('context', $context)
                ->orderBy('sort_order')
                ->get();

            $allowed = $menus->filter(fn (Menu $menu) => $this->allowed($user, $menu));

            // Menu induk (tanpa permission sendiri) hanya tampil bila ada anak
            // yang boleh diakses — mencegah menu "hub" kosong muncul untuk role lain.
            $parentsWithAllowedChild = $allowed
                ->pluck('parent_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->all();

            return $allowed
                ->reject(function (Menu $menu) use ($menus, $parentsWithAllowedChild) {
                    $hasChildren = $menus->contains(
                        fn (Menu $child) => (int) $child->parent_id === (int) $menu->getKey()
                    );

                    return $hasChildren && ! in_array((int) $menu->getKey(), $parentsWithAllowedChild, true);
                })
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
     * Navigasi siap pakai untuk klien mobile (Flutter).
     *
     * Struktur: context, landing, sidebar (tree), bottom_nav (tree).
     * Menu induk tanpa route dipakai sebagai "hub" — children ikut dikirim
     * walau `show_bottom_nav` false supaya klien bisa merender sub-menuanya.
     *
     * @return array<string, mixed>
     */
    public function navigation(User $user): array
    {
        $context = $this->contextFor($user);
        $allowed = $this->forUser($user, $context);

        return [
            'context' => $context,
            'landing' => $this->landingRoute($user),
            'sidebar' => $this->tree($allowed->where('show_sidebar', true), $allowed),
            'bottom_nav' => $this->tree($allowed->where('show_bottom_nav', true), $allowed),
        ];
    }

    /**
     * @param  Collection<int, Menu>  $roots
     * @param  Collection<int, Menu>  $allowed
     * @return array<int, array<string, mixed>>
     */
    private function tree(Collection $roots, Collection $allowed): array
    {
        return $roots
            ->map(function (Menu $menu) use ($allowed) {
                $children = $this->tree($allowed->where('parent_id', $menu->getKey()), $allowed);

                return [
                    'id' => $menu->getKey(),
                    'key' => $menu->key,
                    'label' => $menu->label,
                    'icon' => $menu->icon,
                    'route_name' => $menu->route_name,
                    'url' => $menu->url,
                    'section' => $menu->section,
                    'permission_name' => $menu->permission_name,
                    'sort_order' => $menu->sort_order,
                    'is_landing' => (bool) $menu->is_landing,
                    'active_pattern' => $menu->active_pattern,
                    'children' => $children,
                ];
            })
            // Menu tanpa route/url dan tanpa anak tidak berguna bagi klien.
            ->filter(fn (array $item) => filled($item['route_name'])
                || filled($item['url'])
                || $item['children'] !== [])
            ->values()
            ->all();
    }

    /**
     * Halaman pertama yang boleh diakses user, tanpa hardcode role.
     */
    public function landingRoute(User $user): ?string
    {
        // Context utama user didahulukan supaya menu bersama (mis. "Profil")
        // di context lain tidak mencuri landing page.
        $contexts = array_values(array_unique(array_merge(
            [$this->contextFor($user)],
            ['admin', 'driver', 'user']
        )));

        foreach ($contexts as $context) {
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
     *
     * Memilih context dengan menu paling banyak (bukan context pertama yang
     * kebetulan punya satu menu bersama seperti "Profil"), supaya user biasa
     * tidak terjebak ke layout admin.
     */
    public function contextFor(User $user): string
    {
        $best = null;
        $bestCount = 0;

        foreach (['admin', 'driver', 'user'] as $context) {
            $count = $this->forUser($user, $context)->count();

            if ($count > $bestCount) {
                $best = $context;
                $bestCount = $count;
            }
        }

        return $best ?? 'user';
    }
}
