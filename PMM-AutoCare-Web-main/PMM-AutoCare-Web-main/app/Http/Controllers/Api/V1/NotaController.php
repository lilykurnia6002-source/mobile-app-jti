<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DetailLayananResource;
use App\Http\Resources\Api\V1\DetailSparepartResource;
use App\Http\Responses\ApiResponse;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Nota digital pesanan (konsep_api.md § 8.1).
 *
 * Murni rincian tagihan: jasa + sparepart + total. Tidak menyentuh alur
 * pembayaran, sehingga aman dibangun walau modul pembayaran masih ditunda.
 */
class NotaController extends Controller
{
    /**
     * GET /api/v1/pesanan/{id}/nota
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $pesanan = Pesanan::with([
            'mobil', 'slot', 'detailLayanan.layanan', 'detailSparepart.sparepart',
        ])->find($id);

        if ($pesanan === null) {
            throw new ApiException('Pesanan tidak ditemukan.', 'NOT_FOUND', 404, ['kode' => 'NOT_FOUND']);
        }

        if ($pesanan->id_pelanggan !== $request->user()->pelanggan->id_pelanggan) {
            throw new ApiException(
                'Anda tidak berhak mengakses nota ini.',
                'FORBIDDEN',
                403,
                ['kode' => 'FORBIDDEN'],
            );
        }

        // total_biaya dibaca dari kolom pesanan (tidak dihitung ulang) —
        // nilainya dijaga konsisten server saat rincian berubah.
        $subtotalJasa = (float) $pesanan->detailLayanan->sum('subtotal');
        $subtotalSparepart = (float) $pesanan->detailSparepart->sum('subtotal');

        return ApiResponse::success([
            'id_pesanan' => $pesanan->id_pesanan,
            'kode_pesanan' => $pesanan->kode_pesanan,
            'status' => $pesanan->status,
            'tanggal_slot' => $pesanan->slot?->tanggal?->format('Y-m-d'),
            'mobil' => $pesanan->mobil ? [
                'plat_nomor' => $pesanan->mobil->plat_nomor,
                'merk' => $pesanan->mobil->merk,
                'tipe' => $pesanan->mobil->tipe,
            ] : null,
            'detail_layanan' => DetailLayananResource::collection($pesanan->detailLayanan),
            'detail_sparepart' => DetailSparepartResource::collection($pesanan->detailSparepart),
            'subtotal_jasa' => $subtotalJasa,
            'subtotal_sparepart' => $subtotalSparepart,
            'total_biaya' => (float) $pesanan->total_biaya,
        ], 'Nota pesanan');
    }
}
