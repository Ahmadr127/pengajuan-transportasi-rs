<?php

namespace App\Http\Requests\Transport;

use Illuminate\Foundation\Http\FormRequest;

class StoreUmumRequest extends FormRequest
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
            'tanggal' => [$isRecurring ? 'nullable' : 'required', 'date'],
            'jam' => ['required', 'date_format:H:i'],
            'tanggal_sampai' => ($sampaiSelesai || $isRecurring) ? ['nullable', 'date'] : ['required', 'date'],
            'jam_sampai' => $sampaiSelesai ? ['nullable'] : ['required', 'date_format:H:i'],
            'prioritas' => ['required', 'in:segera,biasa'],
            'alamat_tujuan' => ['required', 'string', 'max:2000'],
            'keperluan' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ];

        if ($isRecurring) {
            $rules['recurring_hari'] = ['required', 'array', 'min:1'];
            $rules['recurring_hari.*'] = ['integer', 'min:1', 'max:7'];
        }

        return $rules;
    }
}
