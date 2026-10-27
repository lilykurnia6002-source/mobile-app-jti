<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Pembungkus respons seragam untuk seluruh endpoint API.
 *
 * Setiap respons berbentuk:
 *   { "success": bool, "message": string, "data": mixed }
 * dan daftar berhalaman menambahkan "meta".
 *
 * Lihat .docs/backend/konsep_api.md § 1.2.
 */
class ApiResponse
{
    public static function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(
        string $message,
        int $status = 400,
        array|string|null $data = null,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Respons daftar berhalaman dengan meta {total, per_page, current_page, last_page}.
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        mixed $data = null,
        string $message = '',
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data ?? $paginator->items(),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}
