<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_keluhan_tambahan', function (Blueprint $table) {
            $table->id('id_keluhan');
            $table->foreignId('id_pesanan')
                ->constrained('tb_pesanan', 'id_pesanan')
                ->cascadeOnDelete();
            $table->foreignId('ditambahkan_oleh')
                ->nullable()
                ->constrained('tb_user', 'id_user')
                ->nullOnDelete();
            $table->text('keluhan');
            $table->decimal('perkiraan_biaya', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_keluhan_tambahan');
    }
};
