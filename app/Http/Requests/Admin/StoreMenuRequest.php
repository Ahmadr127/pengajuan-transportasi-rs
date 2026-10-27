<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_landing' => $this->boolean('is_landing'),
            'show_sidebar' => $this->boolean('show_sidebar'),
            'show_bottom_nav' => $this->boolean('show_bottom_nav'),
        ]);
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'alpha_dash', 'max:100', 'unique:menus,key'],
            'context' => ['required', Rule::in(['admin', 'user', 'driver'])],
            'section' => ['nullable', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:100'],
            'route_name' => ['nullable', 'string', 'max:150'],
            'url' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'permission_name' => ['nullable', 'string', 'exists:permissions,name'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'active_pattern' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menus,id'],
            'is_active' => ['boolean'],
            'is_landing' => ['boolean'],
            'show_sidebar' => ['boolean'],
            'show_bottom_nav' => ['boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $routeName = $this->input('route_name');
                if (! empty($routeName) && ! Route::has($routeName)) {
                    $validator->errors()->add('route_name', 'Nama route tidak ditemukan.');
                }
            },
        ];
    }
}
