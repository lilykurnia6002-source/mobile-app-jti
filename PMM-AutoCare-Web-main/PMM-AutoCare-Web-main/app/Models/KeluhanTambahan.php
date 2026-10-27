<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pesanan', 'ditambahkan_oleh', 'keluhan', 'perkiraan_biaya'])]
class KeluhanTambahan extends Model
{
    /**
     * Temuan baru yang muncul saat pengerjaan berlangsung. Entitas lemah:
     * tidak punya arti tanpa pesanan induknya.
     */
    protected $table = 'tb_keluhan_tambahan';

    protected $primaryKey = 'id_keluhan';

    protected function casts(): array
    {
        return [
            'perkiraan_biaya' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function ditambahkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditambahkan_oleh', 'id_user');
    }
}
