<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'kode_pesanan',
    'id_pelanggan',
    'id_mobil',
    'id_slot',
    'keluhan',
    'status',
    'kanal_pesan',
    'total_biaya',
    'waktu_pesan',
    'alasan_tolak',
])]
class Pesanan extends Model
{
    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DITERIMA = 'diterima';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_DIKERJAKAN = 'dikerjakan';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const KANAL_MOBILE = 'mobile';

    protected $table = 'tb_pesanan';

    protected $primaryKey = 'id_pesanan';

    protected function casts(): array
    {
        return [
            'waktu_pesan' => 'datetime',
            'total_biaya' => 'decimal:2',
        ];
    }

    /**
     * Batasi ke pesanan milik satu pelanggan. Semua endpoint mobile wajib
     * memakai scope ini agar pelanggan tidak pernah melihat data pelanggan lain.
     */
    public function scopeUntukPelanggan(Builder $query, int $idPelanggan): Builder
    {
        return $query->where('id_pelanggan', $idPelanggan);
    }

    public function scopeRiwayat(Builder $query): Builder
    {
        return $query->orderByDesc('waktu_pesan');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class, 'id_mobil', 'id_mobil');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(SlotJadwal::class, 'id_slot', 'id_slot');
    }

    public function detailLayanan(): HasMany
    {
        return $this->hasMany(DetailLayanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function detailSparepart(): HasMany
    {
        return $this->hasMany(DetailSparepart::class, 'id_pesanan', 'id_pesanan');
    }

    /** Di API disebut "penugasan" (mekanik yang ditetapkan admin). */
    public function pengerjaan(): HasMany
    {
        return $this->hasMany(Pengerjaan::class, 'id_pesanan', 'id_pesanan');
    }

    public function keluhanTambahan(): HasMany
    {
        return $this->hasMany(KeluhanTambahan::class, 'id_pesanan', 'id_pesanan');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class, 'id_pesanan', 'id_pesanan')
            ->orderBy('waktu');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_pesanan', 'id_pesanan');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    public function dapatDibatalkan(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }
}
