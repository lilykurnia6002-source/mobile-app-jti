<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Autentikasi panel website (admin & pemilik).
 *
 * Pelanggan tidak boleh masuk lewat sini — mereka memakai aplikasi mobile
 * melalui POST /api/v1/auth/login (konsep_pemrograman.md § 1).
 */
class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $kredensial = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => 'email',
            'password' => 'password',
        ]);

        $user = User::where('email', $kredensial['email'])->first();

        // Tolak lebih awal bila akun tidak ada atau bukan admin/pemilik.
        if ($user === null || ! in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PEMILIK], true)) {
            // Pelanggan hanya dilayani lewat aplikasi mobile.
            $pesan = $user !== null && $user->role === User::ROLE_PELANGGAN
                ? 'Akun pelanggan hanya dapat mengakses aplikasi mobile.'
                : 'Email atau password salah.';

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => $pesan]);
        }

        // Password disimpan apa adanya (tanpa hash) untuk keperluan demo,
        // jadi dibandingkan langsung lalu sesi dibuat manual dengan
        // Auth::login(). Pada aplikasi produksi gunakan Auth::attempt()
        // yang otomatis memverifikasi lewat hasher.
        if ($user->password !== $kredensial['password']) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('sukses', 'Anda telah keluar dari panel admin.');
    }
}
