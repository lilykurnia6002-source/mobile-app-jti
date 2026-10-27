<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_sparepart', function (Blueprint $table) {
            $table->id('id_sparepart');
            $table->string('nama');
            $table->string('satuan', 20);
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->unsignedInteger('stok_minimal')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_sparepart');
    }
};
