<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfilAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'string', 'email', 'max:150',
                Rule::unique('tb_user', 'email')->ignore($this->user()->id_user, 'id_user'),
            ],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', Password::min(8)],
        ];
    }
}
