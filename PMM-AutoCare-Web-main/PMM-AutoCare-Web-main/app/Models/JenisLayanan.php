<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_layanan', 'kategori', 'harga_jasa', 'estimasi_menit', 'aktif'])]
class JenisLayanan extends Model
{
    protected $table = 'tb_jenis_layanan';

    protected $primaryKey = 'id_layanan';

    protected function casts(): array
    {
        return [
            'harga_jasa' => 'decimal:2',
            'estimasi_menit' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function detailLayanan(): HasMany
    {
        return $this->hasMany(DetailLayanan::class, 'id_layanan', 'id_layanan');
    }
}
