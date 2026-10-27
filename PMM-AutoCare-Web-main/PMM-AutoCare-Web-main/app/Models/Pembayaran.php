<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_pesanan',
    'id_metode',
    'jumlah',
    'status_bayar',
    'bukti_transfer',
    'order_id',
    'waktu_bayar',
    'diverifikasi_oleh',
    'catatan',
])]
class Pembayaran extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    public const STATUS_LUNAS = 'lunas';

    public const STATUS_DITOLAK = 'ditolak';

    /**
     * Pembayaran manual tanpa payment gateway. Perubahan ke `lunas` adalah
     * hak admin lewat verifikasi (lihat konsep_pemrograman.md § 4.6).
     */
    protected $table = 'tb_pembayaran';

    protected $primaryKey = 'id_pembayaran';

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'waktu_bayar' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function metode(): BelongsTo
    {
        return $this->belongsTo(MetodeBayar::class, 'id_metode', 'id_metode');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh', 'id_user');
    }

    public function sudahLunas(): bool
    {
        return $this->status_bayar === self::STATUS_LUNAS;
    }
}
