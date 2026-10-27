<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pesanan', 'judul', 'pesan', 'waktu_kirim', 'dibaca'])]
class Notifikasi extends Model
{
    /**
     * Notifikasi tidak menyimpan id_user — kepemilikannya diturunkan dari
     * pesanan induk (lihat kamus data). Karena itu scoping pelanggan selalu
     * lewat relasi pesanan, bukan kolom langsung.
     */
    protected $table = 'tb_notifikasi';

    protected $primaryKey = 'id_notifikasi';

    protected function casts(): array
    {
        return [
            'waktu_kirim' => 'datetime',
            'dibaca' => 'boolean',
        ];
    }

    public function scopeUntukPelanggan(Builder $query, int $idPelanggan): Builder
    {
        return $query->whereHas(
            'pesanan',
            fn (Builder $q) => $q->where('id_pelanggan', $idPelanggan)
        );
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->where('dibaca', false);
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
