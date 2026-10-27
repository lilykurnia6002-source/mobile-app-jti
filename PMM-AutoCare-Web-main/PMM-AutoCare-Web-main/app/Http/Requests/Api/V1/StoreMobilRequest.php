<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreMobilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plat_nomor' => ['required', 'string', 'max:20', 'unique:tb_mobil,plat_nomor'],
            'merk' => ['required', 'string', 'max:50'],
            'tipe' => ['required', 'string', 'max:50'],
            'tahun' => ['required', 'integer', 'digits:4', 'min:1900', 'max:'.(date('Y') + 1)],
        ];
    }
}
