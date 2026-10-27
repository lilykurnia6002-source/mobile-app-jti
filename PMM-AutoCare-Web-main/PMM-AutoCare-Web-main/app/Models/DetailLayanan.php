<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pesanan', 'id_layanan', 'jumlah', 'harga_jasa', 'subtotal'])]
class DetailLayanan extends Model
{
    protected $table = 'tb_detail_layanan';

    protected $primaryKey = 'id_detail';

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_jasa' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(JenisLayanan::class, 'id_layanan', 'id_layanan');
    }
}
