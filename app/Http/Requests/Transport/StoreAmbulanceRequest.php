<?php

namespace App\Http\Requests\Transport;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmbulanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isRecurring = $this->boolean('is_recurring');
        $sampaiSelesai = $this->boolean('sampai_selesai');

        $rules = [
            'purpose' => ['required', 'in:antar,jemput'],
            'pasien_nama' => ['required', 'string', 'max:255'],
            'pasien_no_rm' => ['nullable', 'string', 'max:50'],
            'alamat_pasien' => ['required', 'string', 'max:2000'],
            'tanggal' => [$isRecurring ? 'nullable' : 'required', 'date'],
            'jam' => ['required', 'date_format:H:i'],
            'tanggal_sampai' => ($sampaiSelesai || $isRecurring) ? ['nullable', 'date'] : ['required', 'date'],
            'jam_sampai' => $sampaiSelesai ? ['nullable'] : ['required', 'date_format:H:i'],
            'prioritas' => ['required', 'in:segera,biasa'],
            'alamat_tujuan' => ['nullable', 'string', 'max:2000'],
            'alamat_asal' => ['nullable', 'string', 'max:2000'],
        ];

        if ($isRecurring) {
            $rules['recurring_hari'] = ['required', 'array', 'min:1'];
            $rules['recurring_hari.*'] = ['integer', 'min:1', 'max:7'];
        }

        return $rules;
    }
}
