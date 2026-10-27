<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi transisi status pengajuan (admin).
 *
 * Aturan bercabang sesuai transisi (status saat ini → status baru), sama seperti
 * implementasi lama. Transisi yang tidak dikenal sengaja dibiarkan lolos di sini
 * dan ditolak oleh TransportWorkflowService dengan pesan "Transisi status tidak valid.".
 */
class UpdateTransportStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('transportRequest')?->status;
        $new = $this->input('status');

        if ($current === 'diajukan' && $new === 'diproses') {
            return [
                'status' => ['required', Rule::in(['diproses', 'tidak_disetujui'])],
            ];
        }

        if ($current === 'digunakan' && $new === 'digunakan' && $this->boolean('_edit_digunakan')) {
            return [
                'status' => ['required', Rule::in(['digunakan'])],
                'unit_mobil' => ['nullable', 'string', 'max:100'],
                'plat_nomor' => ['nullable', 'string', 'max:20'],
                'driver_id' => ['nullable', 'exists:drivers,id'],
                'km_awal' => ['nullable', 'integer', 'min:0'],
            ];
        }

        if ($current === 'diproses' && $new === 'digunakan') {
            return [
                'status' => ['required', Rule::in(['digunakan'])],
                'unit_mobil' => ['required', 'string', 'max:100'],
                'plat_nomor' => ['nullable', 'string', 'max:20'],
                'driver_id' => ['required', 'exists:drivers,id'],
                'km_awal' => ['required', 'integer', 'min:0'],
            ];
        }

        if ($current === 'digunakan' && $new === 'selesai') {
            return [
                'status' => ['required', Rule::in(['selesai'])],
                'km_akhir' => ['required', 'integer', 'min:0'],
                'jam_kedatangan' => ['required', 'string', 'max:10', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'],
            ];
        }

        if (($current === 'diajukan' && $new === 'tidak_disetujui')
            || ($current === 'digunakan' && $new === 'tidak_disetujui')) {
            return [
                'status' => ['required', Rule::in(['tidak_disetujui'])],
                'rejection_reason' => ['required', 'string', 'max:500'],
            ];
        }

        // Transisi tidak dikenal — biarkan lolos, service yang menolak.
        return ['status' => ['nullable']];
    }
}
