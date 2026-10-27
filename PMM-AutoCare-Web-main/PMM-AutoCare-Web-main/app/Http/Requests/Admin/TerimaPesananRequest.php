<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TerimaPesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_mekanik' => [
                'required', 'integer',
                Rule::exists('tb_mekanik', 'id_mekanik')->where('aktif', true),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id_mekanik.required' => 'Harap pilih mekanik bertugas terlebih dahulu.',
            'id_mekanik.exists' => 'Mekanik yang dipilih tidak ditemukan atau sudah tidak aktif.',
        ];
    }
}
