<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'is_locked' => (bool) $this->is_locked,
            'is_super' => (bool) $this->is_super,
            'permissions' => $this->whenLoaded('permissions', fn () => $this->permissions->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'label' => $p->label, 'group' => $p->group])->values()),
            'users_count' => $this->whenCounted('users'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}