<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecurringTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
            'hari' => ['required', 'array', 'min:1'],
            'hari.*' => ['integer', 'min:1', 'max:7'],
            'jam' => ['required', 'date_format:H:i'],
            'jam_sampai' => ['nullable', 'date_format:H:i'],
        ];
    }
}
