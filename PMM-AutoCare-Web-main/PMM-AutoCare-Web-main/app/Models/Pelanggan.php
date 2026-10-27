<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_user', 'nama', 'no_hp', 'alamat'])]
class Pelanggan extends Model
{
    protected $table = 'tb_pelanggan';

    protected $primaryKey = 'id_pelanggan';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function mobil(): HasMany
    {
        return $this->hasMany(Mobil::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
