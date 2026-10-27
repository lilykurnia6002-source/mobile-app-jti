<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pesanan', 'status', 'keterangan', 'diubah_oleh', 'waktu'])]
class RiwayatStatus extends Model
{
    /** Jejak audit setiap perubahan status pesanan. */
    protected $table = 'tb_riwayat_status';

    protected $primaryKey = 'id_riwayat';

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh', 'id_user');
    }
}
