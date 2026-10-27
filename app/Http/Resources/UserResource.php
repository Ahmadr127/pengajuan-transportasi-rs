<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'nip' => $this->nip,
            'unit_kerja' => $this->unit_kerja,
            'posisi_pekerjaan' => $this->posisi_pekerjaan,
            'profesi' => $this->profesi,
            'jabatan' => $this->jabatan,
            'role' => $this->primaryRole()?->name,
            'priority_level' => $this->priority_level,
            'simutu_id' => $this->simutu_id,
            'simutu_status' => $this->simutu_status,
            'simutu_synced_at' => $this->simutu_synced_at,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'label' => $r->label])->values()),
            'driver' => $this->whenLoaded('driver', fn () => $this->driver ? new DriverResource($this->driver) : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}