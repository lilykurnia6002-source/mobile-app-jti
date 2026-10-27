<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_pelanggan', 'plat_nomor', 'merk', 'tipe', 'tahun'])]
class Mobil extends Model
{
    protected $table = 'tb_mobil';

    protected $primaryKey = 'id_mobil';

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_mobil', 'id_mobil');
    }
}
