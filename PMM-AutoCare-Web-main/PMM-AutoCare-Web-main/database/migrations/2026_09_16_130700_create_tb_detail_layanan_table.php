<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_detail_layanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_pesanan')
                ->constrained('tb_pesanan', 'id_pesanan')
                ->cascadeOnDelete();
            $table->foreignId('id_layanan')
                ->constrained('tb_jenis_layanan', 'id_layanan')
                ->restrictOnDelete();
            $table->unsignedSmallInteger('jumlah')->default(1);
            // Snapshot tarif saat pesanan dibuat, agar perubahan tarif master
            // tidak mengubah nilai pesanan yang sudah berjalan.
            $table->decimal('harga_jasa', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_detail_layanan');
    }
};
