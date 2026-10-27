<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom deleted_at untuk soft delete mekanik.
     *
     * Dipakai saat mekanik resign: barisnya disembunyikan dari daftar aktif,
     * tetapi TETAP ADA sehingga riwayat pengerjaan & nota lama tidak
     * kehilangan nama mekaniknya (lihat Pengerjaan::mekanik()).
     */
    public function up(): void
    {
        Schema::table('tb_mekanik', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('tb_mekanik', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
