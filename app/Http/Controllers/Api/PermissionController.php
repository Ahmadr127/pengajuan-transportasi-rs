<?php

namespace App\Http\Controllers\Api;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends BaseApiController
{
    public function index(Request $request)
    {
        $permissions = Permission::query()->orderBy('group')->orderBy('label')->get()->groupBy('group');
        $result = [];
        foreach ($permissions as $group => $items) {
            $result[] = [
                'group' => $group,
                'permissions' => \App\Http\Resources\PermissionResource::collection($items)->resolve(),
            ];
        }
        return $this->success($result);
    }
}