<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecurringTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'jenis' => $this->jenis,
            'keperluan' => $this->keperluan,
            'prioritas' => $this->prioritas,
            'pemohon_nama' => $this->pemohon_nama,
            'pemohon_unit' => $this->pemohon_unit,
            'jumlah_penumpang' => $this->jumlah_penumpang,
            'jam' => $this->jam,
            'jam_sampai' => $this->jam_sampai,
            'hari' => $this->hari,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'alamat_asal' => $this->alamat_asal,
            'alamat_tujuan' => $this->alamat_tujuan,
            'keterangan' => $this->keterangan,
            'pasien_nama' => $this->pasien_nama,
            'pasien_no_rm' => $this->pasien_no_rm,
            'is_active' => (bool) $this->is_active,
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}