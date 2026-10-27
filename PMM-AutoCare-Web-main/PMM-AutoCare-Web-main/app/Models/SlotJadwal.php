<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tanggal', 'jam_mulai', 'jam_selesai', 'kapasitas', 'terisi', 'status'])]
class SlotJadwal extends Model
{
    public const STATUS_DIBUKA = 'dibuka';

    public const STATUS_DITUTUP = 'ditutup';

    /**
     * Alasan gagal gate, dipakai pada respons SLOT_TIDAK_TERSEDIA.
     */
    public const ALASAN_DITUTUP = 'slot_ditutup';

    public const ALASAN_LEWAT = 'tanggal_lewat';

    public const ALASAN_PENUH = 'kuota_penuh';

    protected $table = 'tb_slot_jadwal';

    protected $primaryKey = 'id_slot';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'kapasitas' => 'integer',
            'terisi' => 'integer',
        ];
    }

    /**
     * Gate ketersediaan slot (konsep_api.md § 1.4).
     *
     * Scope ini adalah SATU-SATUNYA sumber aturan gate, dipakai bersama oleh
     * endpoint baca (GET /slot) dan validasi booking (POST /pesanan) supaya
     * keduanya tidak pernah berbeda.
     */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DIBUKA)
            ->whereDate('tanggal', '>=', today())
            ->whereColumn('terisi', '<', 'kapasitas');
    }

    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('tanggal')->orderBy('jam_mulai');
    }

    /**
     * Tentukan alasan kegagalan gate, atau null bila slot tersedia.
     *
     * Urutan pengecekan mengikuti konsep_api.md § 1.4 (status, lalu tanggal,
     * lalu kuota) agar `alasan` yang dikembalikan konsisten.
     */
    public function alasanTidakTersedia(): ?string
    {
        if ($this->status !== self::STATUS_DIBUKA) {
            return self::ALASAN_DITUTUP;
        }

        if ($this->tanggal->lt(today())) {
            return self::ALASAN_LEWAT;
        }

        if ($this->terisi >= $this->kapasitas) {
            return self::ALASAN_PENUH;
        }

        return null;
    }

    /**
     * Apakah slot ini lolos gate ketersediaan.
     *
     * Sengaja tidak dinamai `tersedia()` agar tidak bentrok dengan
     * pemanggilan statis `SlotJadwal::tersedia()` (scopeTersedia).
     */
    public function lolosGate(): bool
    {
        return $this->alasanTidakTersedia() === null;
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kapasitas - $this->terisi);
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_slot', 'id_slot');
    }
}
