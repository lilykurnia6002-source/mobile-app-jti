<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMekanikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'spesialis' => ['required', 'string', 'max:50'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'aktif' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama' => is_string($this->nama) ? trim($this->nama) : $this->nama,
            'spesialis' => is_string($this->spesialis) ? trim($this->spesialis) : $this->spesialis,
            // Checkbox tidak terkirim saat tidak dicentang, jadi default-kan 0.
            'aktif' => $this->boolean('aktif'),
        ]);
    }
}
