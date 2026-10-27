<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'key' => $this->key,
            'context' => $this->context,
            'section' => $this->section,
            'label' => $this->label,
            'route_name' => $this->route_name,
            'url' => $this->url,
            'icon' => $this->icon,
            'permission_name' => $this->permission_name,
            'sort_order' => $this->sort_order,
            'is_active' => (bool) $this->is_active,
            'is_landing' => (bool) $this->is_landing,
            'show_sidebar' => (bool) $this->show_sidebar,
            'show_bottom_nav' => (bool) $this->show_bottom_nav,
            'active_pattern' => $this->active_pattern,
            'children' => $this->whenLoaded('children', fn () => MenuResource::collection($this->children)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}