<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Password default akun hasil factory.
     *
     * Disimpan apa adanya (tanpa hashing) mengikuti konvensi proyek demo
     * perkuliahan ini — lihat catatan pada App\Models\User::casts().
     */
    public static string $passwordDefault = 'password';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => self::$passwordDefault,
            'role' => User::ROLE_PELANGGAN,
            'no_hp' => fake()->numerify('08##########'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Akun ber-role admin bengkel (memakai website, bukan API mobile).
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * Akun ber-role pemilik bengkel.
     */
    public function pemilik(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_PEMILIK,
        ]);
    }

    /**
     * Akun pelanggan lengkap dengan profil tb_pelanggan-nya.
     */
    public function pelanggan(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->pelanggan()->create([
                'nama' => $user->name,
                'no_hp' => $user->no_hp,
                'alamat' => fake()->address(),
            ]);
        });
    }
}
