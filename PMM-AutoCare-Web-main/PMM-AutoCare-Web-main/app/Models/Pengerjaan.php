<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pesanan', 'id_mekanik', 'dicatat_oleh', 'waktu_mulai', 'waktu_selesai', 'catatan'])]
class Pengerjaan extends Model
{
    /**
     * Mewakili penugasan mekanik + progres servis. Seluruh baris di sini
     * dicatat admin (dicatat_oleh), karena mekanik tidak punya akun.
     */
    protected $table = 'tb_pengerjaan';

    protected $primaryKey = 'id_pengerjaan';

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    /**
     * Mekanik yang ditugaskan.
     *
     * PENTING — withTrashed() bersifat WAJIB, bukan pilihan:
     * mekanik memakai soft delete, sehingga tanpa withTrashed() relasi ini
     * mengembalikan null untuk mekanik yang sudah resign. Akibatnya nama
     * mekanik hilang dari riwayat pengerjaan admin, riwayat status di
     * aplikasi mobile, dan nota pelanggan lama.
     */
    public function mekanik(): BelongsTo
    {
        return $this->belongsTo(Mekanik::class, 'id_mekanik', 'id_mekanik')->withTrashed();
    }

    /**
     * Penugasan yang pesanannya masih berjalan (belum selesai/ditolak).
     */
    public function scopeTugasBerjalan(Builder $query): Builder
    {
        return $query->whereHas('pesanan', function ($q) {
            $q->whereIn('status', [Pesanan::STATUS_DITERIMA, Pesanan::STATUS_DIKERJAKAN]);
        });
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh', 'id_user');
    }
}
