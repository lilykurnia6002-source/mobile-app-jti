<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_detail_sparepart', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_pesanan')
                ->constrained('tb_pesanan', 'id_pesanan')
                ->cascadeOnDelete();
            $table->foreignId('id_sparepart')
                ->constrained('tb_sparepart', 'id_sparepart')
                ->restrictOnDelete();
            $table->unsignedSmallInteger('jumlah')->default(1);
            // Snapshot harga saat pemakaian sparepart dicatat oleh admin.
            $table->decimal('harga', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_detail_sparepart');
    }
};
