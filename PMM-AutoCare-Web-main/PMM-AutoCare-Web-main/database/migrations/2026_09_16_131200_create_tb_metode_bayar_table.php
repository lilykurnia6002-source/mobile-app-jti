<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_metode_bayar', function (Blueprint $table) {
            $table->id('id_metode');
            $table->string('nama_metode', 50);
            $table->enum('jenis', ['transfer', 'tunai'])->default('transfer');
            $table->string('no_rekening', 50)->nullable();
            $table->string('atas_nama')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_metode_bayar');
    }
};
