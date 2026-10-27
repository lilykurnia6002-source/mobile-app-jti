<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_metode', 'jenis', 'no_rekening', 'atas_nama', 'aktif'])]
class MetodeBayar extends Model
{
    protected $table = 'tb_metode_bayar';

    protected $primaryKey = 'id_metode';

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_metode', 'id_metode');
    }
}
