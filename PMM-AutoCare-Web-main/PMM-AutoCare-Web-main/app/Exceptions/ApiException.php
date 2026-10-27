<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Exception domain yang membawa kode error stabil sesuai tabel
 * konsep_api.md § 1.5. Kode ini dipakai app mobile sebagai penanda,
 * sedangkan `message` boleh berubah bahasa.
 */
class ApiException extends Exception
{
    public function __construct(
        string $message,
        protected string $errorCode,
        protected int $status = 422,
        protected array|string|null $data = null,
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Slot gagal gate booking. Field `alasan` menjelaskan penyebabnya.
     *
     * @param  string  $alasan  slot_ditutup | tanggal_lewat | kuota_penuh
     */
    public static function slotTidakTersedia(string $alasan): self
    {
        return new self(
            'Slot tidak tersedia. Silakan pilih slot lain.',
            'SLOT_TIDAK_TERSEDIA',
            422,
            ['kode' => 'SLOT_TIDAK_TERSEDIA', 'alasan' => $alasan],
        );
    }

    /**
     * `id_mobil` yang dikirim bukan milik pelanggan yang login.
     */
    public static function mobilTidakTersedia(string $pesan = 'Mobil tidak ditemukan pada akun Anda.'): self
    {
        return new self(
            $pesan,
            'MOBIL_TIDAK_TERSEDIA',
            422,
            [
                'kode' => 'MOBIL_TIDAK_TERSEDIA',
                'errors' => ['id_mobil' => [$pesan]],
            ],
        );
    }

    public static function emailTerpakai(): self
    {
        $pesan = 'Email sudah terdaftar.';

        return new self(
            $pesan,
            'EMAIL_TERPAKAI',
            422,
            [
                'kode' => 'EMAIL_TERPAKAI',
                'errors' => ['email' => [$pesan]],
            ],
        );
    }

    /**
     * Pelanggan mencoba membatalkan pesanan yang sudah diterima admin.
     */
    public static function pesananTidakDapatDibatalkan(string $status): self
    {
        return new self(
            'Pesanan sudah diproses bengkel dan tidak dapat dibatalkan. Silakan hubungi bengkel.',
            'PESANAN_TIDAK_DAPAT_DIBATALKAN',
            422,
            [
                'kode' => 'PESANAN_TIDAK_DAPAT_DIBATALKAN',
                'status_saat_ini' => $status,
            ],
        );
    }

    public static function pembayaranLunas(): self
    {
        return new self(
            'Pesanan ini sudah lunas.',
            'PEMBAYARAN_LUNAS',
            409,
            ['kode' => 'PEMBAYARAN_LUNAS'],
        );
    }

    public function render(Request $request): ?JsonResponse
    {
        if (! $request->is('api/*') && ! $request->expectsJson()) {
            return null;
        }

        return ApiResponse::error($this->getMessage(), $this->status, $this->data);
    }
}
