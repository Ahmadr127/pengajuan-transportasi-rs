<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'posisi_pekerjaan' => ['nullable', 'string', 'max:255'],
            'profesi' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'priority_level' => ['nullable', 'integer', 'in:0,1'],
        ];
    }
}
