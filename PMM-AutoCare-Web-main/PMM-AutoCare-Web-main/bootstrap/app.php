<?php

use App\Exceptions\ApiException;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Bentuk respons error API dipusatkan di sini agar seluruh endpoint
 * mengembalikan envelope {success, message, data} dengan kode stabil
 * (konsep_api.md § 1.5).
 */
$renderApiError = function (string $message, string $code, int $status, array|string|null $data = null) {
    return ApiResponse::error($message, $status, is_array($data)
        ? ['kode' => $code] + $data
        : ['kode' => $code]);
};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        // Tamu yang membuka halaman admin diarahkan ke form login milik kita.
        // Tanpa ini, Laravel mencari route bernama `login` dan gagal dengan
        // "Route [login] not defined".
        $middleware->redirectGuestsTo(fn () => route('auth.login'));

        // Pengguna yang sudah login tidak perlu melihat form login lagi.
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions) use ($renderApiError): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Envelope {success, message, data} adalah KONTRAK API MOBILE
        // (konsep_api.md § 1.2), jadi hanya diterapkan pada rute api/*.
        // Rute admin memakai bentuk bawaan Laravel {message, errors} agar
        // JavaScript panel admin dapat membacanya langsung.
        $hanyaApi = fn (Request $request) => ! $request->is('api/*');

        // 422 — validasi gagal. `data.errors` berupa OBJEK {field: [pesan]},
        // bukan array, sesuai catatan pada konsep_api.md § 1.5.
        $exceptions->render(function (ValidationException $e, Request $request) use ($hanyaApi) {
            if ($hanyaApi($request)) {
                return null;
            }

            return ApiResponse::error('Validasi gagal', 422, [
                'kode' => 'UNPROCESSABLE_CONTENT',
                'errors' => $e->errors(),
            ]);
        });

        // 401 — token hilang/tidak valid/kedaluwarsa.
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Unauthenticated.', 401, ['kode' => 'UNAUTHENTICATED']);
        });

        // 403 — mengakses data milik pelanggan lain.
        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error(
                $e->getMessage() !== '' ? $e->getMessage() : 'Anda tidak berhak mengakses data ini.',
                403,
                ['kode' => 'FORBIDDEN'],
            );
        });

        // 404 — sumber daya tidak ditemukan (termasuk route tidak dikenal).
        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error('Data tidak ditemukan.', 404, ['kode' => 'NOT_FOUND']);
        });

        // 429 — melebihi batas request.
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            return ApiResponse::error(
                'Terlalu banyak permintaan. Coba lagi nanti.',
                429,
                ['kode' => 'RATE_LIMIT'],
            );
        });

        // 500 — jaring terakhir. Saat APP_DEBUG aktif, biarkan Laravel
        // menampilkan detail asli agar mudah didiagnosis.
        $exceptions->render(function (Throwable $e, Request $request) use ($renderApiError) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null;
            }

            if (config('app.debug') || $e instanceof HttpExceptionInterface || $e instanceof ApiException) {
                return null;
            }

            report($e);

            return $renderApiError('Terjadi kesalahan pada server.', 'SERVER_ERROR', 500);
        });
    })->create();
