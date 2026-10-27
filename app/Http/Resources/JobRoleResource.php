<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobRoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'simutu_id' => $this->simutu_id,
            'nama_role' => $this->nama_role,
            'deskripsi_role' => $this->deskripsi_role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}