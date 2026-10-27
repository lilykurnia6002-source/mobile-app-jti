<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->string('kode_pesanan', 30)->unique();
            $table->foreignId('id_pelanggan')
                ->constrained('tb_pelanggan', 'id_pelanggan')
                ->cascadeOnDelete();
            $table->foreignId('id_mobil')
                ->constrained('tb_mobil', 'id_mobil')
                ->cascadeOnDelete();
            // NULL = pelanggan datang langsung (walk-in), tidak memakai slot.
            $table->foreignId('id_slot')
                ->nullable()
                ->constrained('tb_slot_jadwal', 'id_slot')
                ->nullOnDelete();
            $table->text('keluhan');
            $table->enum('status', [
                'menunggu',
                'diterima',
                'ditolak',
                'dikerjakan',
                'selesai',
                'dibatalkan',
            ])->default('menunggu');
            $table->string('kanal_pesan', 20)->default('mobile');
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->dateTime('waktu_pesan');
            $table->text('alasan_tolak')->nullable();
            $table->timestamps();

            $table->index(['id_pelanggan', 'status']);
            $table->index('waktu_pesan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pesanan');
    }
};
