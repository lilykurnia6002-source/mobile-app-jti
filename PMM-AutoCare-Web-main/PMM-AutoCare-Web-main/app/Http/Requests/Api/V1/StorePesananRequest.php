<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi permintaan booking (konsep_api.md § 6.1).
 *
 * Hanya memeriksa bentuk & keberadaan data. Seluruh aturan gate slot,
 * kepemilikan mobil, dan perhitungan biaya divalidasi ulang di service
 * di dalam satu transaksi, karena data bisa berubah sejak app memuatnya.
 */
class StorePesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_mobil' => ['required', 'integer'],
            'id_slot' => ['required', 'integer', 'exists:tb_slot_jadwal,id_slot'],
            'keluhan' => ['required', 'string', 'max:2000'],
            'detail_layanan' => ['required', 'array', 'min:1'],
            'detail_layanan.*.id_layanan' => ['required', 'integer', 'distinct', 'exists:tb_jenis_layanan,id_layanan'],
            'detail_layanan.*.jumlah' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}
