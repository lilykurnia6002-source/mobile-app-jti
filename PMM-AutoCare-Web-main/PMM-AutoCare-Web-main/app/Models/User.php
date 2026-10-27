<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'no_hp'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_PELANGGAN = 'pelanggan';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PEMILIK = 'pemilik';

    protected $table = 'tb_user';

    protected $primaryKey = 'id_user';

    /**
     * Get the attributes that should be cast.
     *
     * CATATAN PEMBELAJARAN: password sengaja TIDAK di-hash (tanpa cast
     * 'hashed') karena ini proyek demo perkuliahan — tujuannya agar isi
     * kolom password mudah dibaca langsung di basis data saat praktikum.
     * Untuk aplikasi sungguhan, kembalikan cast 'password' => 'hashed'
     * dan pastikan seluruh proses login memakai Hash::check()/Auth::attempt().
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class, 'id_user', 'id_user');
    }

    public function isPelanggan(): bool
    {
        return $this->role === self::ROLE_PELANGGAN;
    }
}
