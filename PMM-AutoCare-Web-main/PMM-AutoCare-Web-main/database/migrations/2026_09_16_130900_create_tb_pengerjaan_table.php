<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pengerjaan', function (Blueprint $table) {
            $table->id('id_pengerjaan');
            $table->foreignId('id_pesanan')
                ->constrained('tb_pesanan', 'id_pesanan')
                ->cascadeOnDelete();
            $table->foreignId('id_mekanik')
                ->constrained('tb_mekanik', 'id_mekanik')
                ->restrictOnDelete();
            // Mekanik tidak punya akun login; semua input dicatat atas nama admin.
            $table->foreignId('dicatat_oleh')
                ->nullable()
                ->constrained('tb_user', 'id_user')
                ->nullOnDelete();
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pengerjaan');
    }
};
