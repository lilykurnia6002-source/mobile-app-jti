<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->foreignId('id_user')
                ->unique()
                ->constrained('tb_user', 'id_user')
                ->cascadeOnDelete();
            $table->string('nama');
            $table->string('no_hp', 20);
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pelanggan');
    }
};
