<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idUser = $this->user()->id_user;

        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'string', 'email', 'max:150',
                Rule::unique('tb_user', 'email')->ignore($idUser, 'id_user'),
            ],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', Password::min(8)],
        ];
    }
}
