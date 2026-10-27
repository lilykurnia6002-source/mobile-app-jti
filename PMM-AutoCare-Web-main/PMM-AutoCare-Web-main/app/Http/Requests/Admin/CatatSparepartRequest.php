<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CatatSparepartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_sparepart' => ['required', 'integer', 'exists:tb_sparepart,id_sparepart'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_sparepart.required' => 'Harap pilih sparepart yang dipakai.',
            'id_sparepart.exists' => 'Sparepart yang dipilih tidak ditemukan.',
            'jumlah.required' => 'Harap isi jumlah pemakaian.',
            'jumlah.min' => 'Jumlah pemakaian minimal :min.',
        ];
    }
}
