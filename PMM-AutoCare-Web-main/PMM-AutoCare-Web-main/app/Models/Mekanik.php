<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama', 'spesialis', 'no_hp', 'aktif'])]
class Mekanik extends Model
{
    use SoftDeletes;

    /**
     * Mekanik tidak memiliki akun login — seluruh input atas namanya
     * dicatat oleh admin (lihat kolom dicatat_oleh pada tb_pengerjaan).
     *
     * Soft delete dipakai agar mekanik yang sudah resign dapat dihapus dari
     * daftar aktif tanpa menghilangkan namanya dari riwayat pengerjaan dan
     * nota pelanggan yang pernah dikerjakan.
     */
    protected $table = 'tb_mekanik';

    protected $primaryKey = 'id_mekanik';

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function pengerjaan(): HasMany
    {
        return $this->hasMany(Pengerjaan::class, 'id_mekanik', 'id_mekanik');
    }

    /**
     * Jumlah penugasan yang masih berjalan (pesanan sudah diterima tetapi
     * belum selesai/ditolak).
     *
     * Dipakai untuk menolak penghapusan mekanik yang sedang bertugas.
     */
    public function jumlahTugasBerjalan(): int
    {
        return $this->pengerjaan()->tugasBerjalan()->count();
    }
}
