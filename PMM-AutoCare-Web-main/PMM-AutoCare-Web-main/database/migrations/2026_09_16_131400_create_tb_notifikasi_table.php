<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_pesanan')
                ->constrained('tb_pesanan', 'id_pesanan')
                ->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->dateTime('waktu_kirim');
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            $table->index(['id_pesanan', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_notifikasi');
    }
};
