<?php

namespace App\Http\Requests\Admin;

use App\Models\SlotJadwal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', Rule::in([
                SlotJadwal::STATUS_DIBUKA,
                SlotJadwal::STATUS_DITUTUP,
            ])],
        ];
    }

    /**
     * Cegah jadwal bentrok dengan slot lain pada tanggal yang sama.
     *
     * Tabel memakai unique index (tanggal, jam_mulai), jadi tanpa
     * pemeriksaan ini pelanggaran database muncul sebagai error 500.
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

                $slot = $this->route('slot');

                if ($slot === null) {
                    return;
                }

                // Kecualikan slot yang sedang diedit, agar menyimpan tanpa
                // mengubah jam tidak dianggap bentrok dengan dirinya sendiri.
                $bentrok = SlotJadwal::whereDate('tanggal', $slot->tanggal)
                    ->where('id_slot', '!=', $slot->id_slot)
                    ->get(['jam_mulai'])
                    ->contains(fn (SlotJadwal $lain) => substr($lain->jam_mulai, 0, 5) === $this->input('jam_mulai'));

                if ($bentrok) {
                    $validator->errors()->add(
                        'jam_mulai',
                        'Sudah ada slot lain pada tanggal dan jam tersebut. Silakan pilih jam lain.',
                    );
                }
            },
        ];
    }
}
