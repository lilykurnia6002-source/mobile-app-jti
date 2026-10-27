<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'satuan', 'harga', 'stok', 'stok_minimal'])]
class Sparepart extends Model
{
    protected $table = 'tb_sparepart';

    protected $primaryKey = 'id_sparepart';

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'stok' => 'integer',
            'stok_minimal' => 'integer',
        ];
    }

    public function detailSparepart(): HasMany
    {
        return $this->hasMany(DetailSparepart::class, 'id_sparepart', 'id_sparepart');
    }
}
