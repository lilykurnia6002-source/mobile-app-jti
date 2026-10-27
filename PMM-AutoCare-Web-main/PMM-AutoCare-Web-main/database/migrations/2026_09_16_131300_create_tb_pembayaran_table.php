<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
    | Tabel dibuat lengkap sekarang agar skema utuh sesuai konsep_database,
    | namun endpoint pembayaran belum dibangun (modul ditunda).
    | `menunggu_verifikasi` menampung unggahan bukti transfer pelanggan
    | yang masih harus disahkan admin.
    */
    public function up(): void
    {
        Schema::create('tb_pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_pesanan')
                ->unique()
                ->constrained('tb_pesanan', 'id_pesanan')
                ->cascadeOnDelete();
            $table->foreignId('id_metode')
                ->nullable()
                ->constrained('tb_metode_bayar', 'id_metode')
                ->nullOnDelete();
            $table->decimal('jumlah', 12, 2);
            $table->enum('status_bayar', [
                'menunggu',
                'menunggu_verifikasi',
                'lunas',
                'ditolak',
            ])->default('menunggu');
            $table->string('bukti_transfer')->nullable();
            $table->string('order_id')->nullable();
            $table->dateTime('waktu_bayar')->nullable();
            $table->foreignId('diverifikasi_oleh')
                ->nullable()
                ->constrained('tb_user', 'id_user')
                ->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pembayaran');
    }
};
