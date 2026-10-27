<?php

namespace App\Http\Requests\Admin;

use App\Models\SlotJadwal;
use Illuminate\Foundation\Http\FormRequest;

class StoreSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    /**
     * Satu jam hanya boleh punya satu slot (ada unique index pada
     * tanggal + jam_mulai).
     *
     * Kolom jam_mulai bertipe TIME sehingga tersimpan sebagai "08:00:00",
     * sedangkan form mengirim "08:00". Keduanya perlu disamakan lebih dulu,
     * kalau tidak pemeriksaan ini meleset dan pelanggaran unique index
     * muncul sebagai error 500.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function ($validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $bentrok = SlotJadwal::whereDate('tanggal', $this->input('tanggal'))
                    ->get(['jam_mulai'])
                    ->contains(fn (SlotJadwal $slot) => substr($slot->jam_mulai, 0, 5) === $this->input('jam_mulai'));

                if ($bentrok) {
                    $validator->errors()->add(
                        'jam_mulai',
                        'Slot pada tanggal dan jam tersebut sudah ada. Silakan pilih jam lain.',
                    );
                }
            },
        ];
    }
}
