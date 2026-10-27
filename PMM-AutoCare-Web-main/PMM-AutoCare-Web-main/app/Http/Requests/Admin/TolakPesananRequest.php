<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TolakPesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keterangan' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan.required' => 'Harap isi alasan penolakan pesanan.',
            'keterangan.min' => 'Alasan penolakan minimal :min karakter agar jelas bagi pelanggan.',
        ];
    }
}
