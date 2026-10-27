<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Password seragam untuk semua akun demo. Dipakai apa adanya tanpa
     * hashing agar mudah dibaca saat praktikum.
     */
    private const PASSWORD_DEMO = 'password';

    /**
     * Akun admin & pemilik (memakai website) dan beberapa pelanggan
     * (memakai aplikasi mobile).
     *
     * Password disimpan apa adanya (tanpa hash) untuk keperluan demo
     * perkuliahan, sehingga mudah dibaca di basis data.
     */
    public function run(): void
    {
        $superuser = [
            [
                'name' => 'Alif Admin',
                'email' => 'admin@pmm-auto.test',
                'role' => User::ROLE_ADMIN,
                'no_hp' => '081200000001',
            ],
            [
                'name' => 'Pemilik Bengkel',
                'email' => 'pemilik@pmm-auto.test',
                'role' => User::ROLE_PEMILIK,
                'no_hp' => '081200000002',
            ],
        ];

        foreach ($superuser as $data) {
            User::create([
                ...$data,
                'password' => self::PASSWORD_DEMO,
                'email_verified_at' => now(),
            ]);
        }

        // Pelanggan demo — kredensial dipakai untuk mencoba aplikasi mobile.
        $pelanggan = [
            ['Budi Santoso', 'budi@example.com', '081234567890', 'Jl. Melati No. 12, Malang'],
            ['Siti Aminah', 'siti@example.com', '081298765432', 'Jl. Anggrek No. 3, Malang'],
            ['Rian Pratama', 'rian@example.com', '081377788899', 'Jl. Soekarno Hatta No. 88, Malang'],
        ];

        foreach ($pelanggan as [$nama, $email, $noHp, $alamat]) {
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => self::PASSWORD_DEMO,
                'role' => User::ROLE_PELANGGAN,
                'no_hp' => $noHp,
                'email_verified_at' => now(),
            ]);

            $user->pelanggan()->create([
                'nama' => $nama,
                'no_hp' => $noHp,
                'alamat' => $alamat,
            ]);
        }
    }
}
