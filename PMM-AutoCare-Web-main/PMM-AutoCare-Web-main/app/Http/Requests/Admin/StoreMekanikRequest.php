<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMekanikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            // Spesialis berupa teks bebas: bidang keahlian mekanik tidak
            // terbatas pada daftar tertentu di dokumen konsep.
            'spesialis' => ['required', 'string', 'max:50'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Rapikan spasi berlebih tanpa memaksa kapitalisasi tertentu,
        // agar admin tetap bebas menuliskan nama bidang keahlian.
        $this->merge([
            'nama' => is_string($this->nama) ? trim($this->nama) : $this->nama,
            'spesialis' => is_string($this->spesialis) ? trim($this->spesialis) : $this->spesialis,
        ]);
    }
}
