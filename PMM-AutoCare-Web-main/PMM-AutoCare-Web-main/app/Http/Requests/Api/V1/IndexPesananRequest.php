<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Pesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexPesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in([
                Pesanan::STATUS_MENUNGGU,
                Pesanan::STATUS_DITERIMA,
                Pesanan::STATUS_DITOLAK,
                Pesanan::STATUS_DIKERJAKAN,
                Pesanan::STATUS_SELESAI,
                Pesanan::STATUS_DIBATALKAN,
            ])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
