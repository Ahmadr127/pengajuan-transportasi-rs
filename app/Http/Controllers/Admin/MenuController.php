<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\Permission;
use App\Support\AclCache;
use Illuminate\Support\Facades\Route;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::query()
            ->orderBy('context')
            ->orderBy('sort_order')
            ->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->pluck('name');
        $contexts = ['admin', 'user', 'driver'];
        $routeNames = $this->routeNames();
        $parents = Menu::query()->orderBy('context')->orderBy('sort_order')->get();

        return view('admin.menus.create', compact('permissions', 'contexts', 'routeNames', 'parents'));
    }

    public function store(StoreMenuRequest $request)
    {
        Menu::create($request->validated());
        AclCache::flush();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $permissions = Permission::orderBy('name')->pluck('name');
        $contexts = ['admin', 'user', 'driver'];
        $routeNames = $this->routeNames();
        $parents = Menu::query()
            ->whereKeyNot($menu->getKey())
            ->orderBy('context')
            ->orderBy('sort_order')
            ->get();

        return view('admin.menus.edit', compact('menu', 'permissions', 'contexts', 'routeNames', 'parents'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update($request->validated());
        AclCache::flush();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        AclCache::flush();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    private function routeNames(): array
    {
        return collect(Route::getRoutes()->getRoutes())
            ->pluck('name')
            ->filter()
            ->sort()
            ->values()
            ->all();
    }
}
