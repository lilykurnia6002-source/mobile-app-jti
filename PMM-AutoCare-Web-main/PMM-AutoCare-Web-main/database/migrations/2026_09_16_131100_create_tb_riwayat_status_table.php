<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_riwayat_status', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_pesanan')
                ->constrained('tb_pesanan', 'id_pesanan')
                ->cascadeOnDelete();
            $table->string('status', 20);
            $table->text('keterangan')->nullable();
            // NULL bila perubahan dipicu sistem/pelanggan sendiri (mis. saat booking).
            $table->foreignId('diubah_oleh')
                ->nullable()
                ->constrained('tb_user', 'id_user')
                ->nullOnDelete();
            $table->dateTime('waktu');
            $table->timestamps();

            $table->index(['id_pesanan', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_riwayat_status');
    }
};
