<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_mobil', function (Blueprint $table) {
            $table->id('id_mobil');
            $table->foreignId('id_pelanggan')
                ->constrained('tb_pelanggan', 'id_pelanggan')
                ->cascadeOnDelete();
            $table->string('plat_nomor', 20)->unique();
            $table->string('merk', 50);
            $table->string('tipe', 50);
            $table->unsignedSmallInteger('tahun');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_mobil');
    }
};
