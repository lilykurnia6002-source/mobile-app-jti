<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pesanan', 'id_sparepart', 'jumlah', 'harga', 'subtotal'])]
class DetailSparepart extends Model
{
    protected $table = 'tb_detail_sparepart';

    protected $primaryKey = 'id_detail';

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class, 'id_sparepart', 'id_sparepart');
    }
}
