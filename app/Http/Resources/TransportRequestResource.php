<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_pengajuan' => $this->nomor_pengajuan,
            'user_id' => $this->user_id,
            'jenis' => $this->jenis,
            'keperluan' => $this->keperluan,
            'prioritas' => $this->prioritas,
            'pemohon_nama' => $this->pemohon_nama,
            'pemohon_unit' => $this->pemohon_unit,
            'jumlah_penumpang' => $this->jumlah_penumpang,
            'unit_mobil' => $this->unit_mobil,
            'driver_id' => $this->driver_id,
            'plat_nomor' => $this->plat_nomor,
            'km_awal' => $this->km_awal,
            'km_akhir' => $this->km_akhir,
            'tanggal' => $this->tanggal?->toDateString(),
            'tanggal_sampai' => $this->tanggal_sampai?->toDateString(),
            'jam' => $this->jam,
            'jam_sampai' => $this->jam_sampai,
            'jam_kedatangan' => $this->jam_kedatangan,
            'biaya_tol' => $this->biaya_tol,
            'kontak' => $this->kontak,
            'alamat_asal' => $this->alamat_asal,
            'alamat_tujuan' => $this->alamat_tujuan,
            'keterangan' => $this->keterangan,
            'pasien_nama' => $this->pasien_nama,
            'pasien_no_rm' => $this->pasien_no_rm,
            'ruangan' => $this->ruangan,
            'kondisi_pasien' => $this->kondisi_pasien,
            'alamat_pasien' => $this->alamat_pasien,
            'pendamping_nama' => $this->pendamping_nama,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'signature_pemohon' => $this->signature_pemohon,
            'signature_pemohon_at' => $this->signature_pemohon_at,
            'signature_pengelola_1' => $this->signature_pengelola_1,
            'signature_pengelola_1_at' => $this->signature_pengelola_1_at,
            'signature_pengelola_1_name' => $this->signature_pengelola_1_name,
            'signature_driver' => $this->signature_driver,
            'signature_driver_at' => $this->signature_driver_at,
            'signature_pengelola_2' => $this->signature_pengelola_2,
            'signature_pengelola_2_at' => $this->signature_pengelola_2_at,
            'signature_pengelola_2_name' => $this->signature_pengelola_2_name,
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'driver' => $this->whenLoaded('driver', fn () => $this->driver ? new DriverResource($this->driver) : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}