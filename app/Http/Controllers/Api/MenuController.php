<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Models\Menu;
use App\Support\AclCache;
use Illuminate\Http\Request;

class MenuController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Menu::query()->with('parent', 'children')->orderBy('sort_order');
        if ($request->filled('context')) {
            $query->where('context', $request->context);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $perPage = min((int) $request->get('per_page', 50), 200);
        $menus = $query->paginate($perPage);
        return $this->paginated($menus, \App\Http\Resources\MenuResource::collection($menus));
    }

    public function show(Menu $menu)
    {
        return $this->success(new \App\Http\Resources\MenuResource($menu->load('parent', 'children')));
    }

    public function store(StoreMenuRequest $request)
    {
        $menu = Menu::create($request->validated());
        AclCache::flush();
        return $this->created(new \App\Http\Resources\MenuResource($menu), 'Menu berhasil ditambahkan.');
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update($request->validated());
        AclCache::flush();
        return $this->success(new \App\Http\Resources\MenuResource($menu->fresh()), 'Menu berhasil diupdate.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        AclCache::flush();
        return $this->success(null, 'Menu berhasil dihapus.');
    }
}