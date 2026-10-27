<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_slot_jadwal', function (Blueprint $table) {
            $table->id('id_slot');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedSmallInteger('kapasitas')->default(1);
            $table->unsignedSmallInteger('terisi')->default(0);
            $table->enum('status', ['dibuka', 'ditutup'])->default('dibuka');
            $table->timestamps();

            $table->unique(['tanggal', 'jam_mulai']);
            $table->index(['status', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_slot_jadwal');
    }
};
