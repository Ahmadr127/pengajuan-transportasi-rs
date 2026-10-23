<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Permission;
use App\Support\AclCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

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

        return view('admin.menus.create', compact('permissions', 'contexts', 'routeNames'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Menu::create($data);
        AclCache::flush();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $permissions = Permission::orderBy('name')->pluck('name');
        $contexts = ['admin', 'user', 'driver'];
        $routeNames = $this->routeNames();

        return view('admin.menus.edit', compact('menu', 'permissions', 'contexts', 'routeNames'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validated($request, $menu);

        $menu->update($data);
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

    private function validated(Request $request, ?Menu $menu = null): array
    {
        $data = $request->validate([
            'key' => ['required', 'alpha_dash', 'max:100', Rule::unique('menus', 'key')->ignore($menu?->id)],
            'context' => ['required', Rule::in(['admin', 'user', 'driver'])],
            'section' => ['nullable', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:100'],
            'route_name' => ['nullable', 'string', 'max:150'],
            'url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'permission_name' => ['nullable', 'string', 'exists:permissions,name'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'active_pattern' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menus,id'],
        ]);

        if (! empty($data['route_name']) && ! Route::has($data['route_name'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'route_name' => 'Nama route tidak ditemukan.',
            ]);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['is_landing'] = $request->boolean('is_landing');
        $data['show_sidebar'] = $request->boolean('show_sidebar');
        $data['show_bottom_nav'] = $request->boolean('show_bottom_nav');

        return $data;
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
