<?php

namespace Database\Seeders;

use App\Models\SlotJadwal;
use Illuminate\Database\Seeder;

class SlotJadwalSeeder extends Seeder
{
    /**
     * Slot jadwal disediakan Admin lewat website. Seeder ini menyiapkan
     * kombinasi kondisi agar setiap perilaku mobile bisa diuji:
     * slot normal, slot penuh (sisa_kuota 0), slot ditutup, dan slot lewat.
     */
    public function run(): void
    {
        $jamKerja = [
            ['08:00', '10:00'],
            ['10:00', '12:00'],
            ['13:00', '15:00'],
            ['15:00', '17:00'],
        ];

        // Slot normal untuk 7 hari ke depan.
        foreach (range(0, 6) as $offset) {
            $tanggal = today()->addDays($offset);

            foreach ($jamKerja as [$mulai, $selesai]) {
                SlotJadwal::create([
                    'tanggal' => $tanggal->toDateString(),
                    'jam_mulai' => $mulai,
                    'jam_selesai' => $selesai,
                    'kapasitas' => 3,
                    'terisi' => 0,
                    'status' => SlotJadwal::STATUS_DIBUKA,
                ]);
            }
        }

        // Contoh slot yang sudah penuh.
        SlotJadwal::whereDate('tanggal', today()->addDays(1))
            ->where('jam_mulai', '10:00')
            ->update(['terisi' => 3]);

        // Contoh slot yang ditutup admin.
        SlotJadwal::whereDate('tanggal', today()->addDays(2))
            ->where('jam_mulai', '13:00')
            ->update(['status' => SlotJadwal::STATUS_DITUTUP]);

        // Contoh slot di masa lalu (tidak akan muncul karena gate tanggal).
        SlotJadwal::create([
            'tanggal' => today()->subDay()->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kapasitas' => 3,
            'terisi' => 1,
            'status' => SlotJadwal::STATUS_DIBUKA,
        ]);
    }
}
