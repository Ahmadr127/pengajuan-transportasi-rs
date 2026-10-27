<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_role' => ['required', 'string', 'max:255'],
            'deskripsi_role' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
