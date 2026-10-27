<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'simutu_id' => $this->simutu_id,
            'kode_unit' => $this->kode_unit,
            'nama_unit' => $this->nama_unit,
            'deskripsi_unit' => $this->deskripsi_unit,
            'status_unit' => $this->status_unit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}