<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_mekanik', function (Blueprint $table) {
            $table->id('id_mekanik');
            $table->string('nama');
            $table->string('spesialis', 50);
            $table->string('no_hp', 20)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_mekanik');
    }
};
