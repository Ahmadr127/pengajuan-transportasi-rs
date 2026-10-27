<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_unit' => ['required', 'string', 'max:50', Rule::unique('units', 'kode_unit')->ignore($this->route('unit')->id)],
            'nama_unit' => ['required', 'string', 'max:255'],
            'deskripsi_unit' => ['nullable', 'string', 'max:1000'],
            'status_unit' => ['required', 'in:aktif,non-aktif'],
        ];
    }
}
