<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batasi panel admin hanya untuk role admin & pemilik bengkel.
 *
 * Pelanggan tidak punya akses ke website — seluruh interaksinya
 * hanya lewat aplikasi mobile (konsep_pemrograman.md § 1).
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! in_array($user->role, ['admin', 'pemilik'], true)) {
            abort(403, 'Akun ini tidak berhak mengakses panel admin.');
        }

        return $next($request);
    }
}
