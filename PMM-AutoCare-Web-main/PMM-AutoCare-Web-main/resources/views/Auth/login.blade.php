<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk Panel Admin PMM AutoCare - Sistem Informasi Pelayanan dan Penjadwalan Servis Mobil">
    <title>Panel Admin PMM Auto Care</title>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex flex-col justify-center selection:bg-[#E5A919]/30 selection:text-[#1F3864]">

    <main class="flex-1 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <!-- Container Utama Inline Layout 2 Kolom -->
        <div
            class="w-full max-w-5xl bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">

            <!-- ============================================================
                 SISI KIRI: CARD INFORMASI ADMIN PMM AUTOCARE
            ============================================================ -->
            <div
                class="lg:col-span-6 bg-gradient-to-br from-[#1F3864] via-[#152747] to-[#1F3864] p-8 sm:p-10 text-white flex flex-col justify-between relative overflow-hidden">
                <!-- Elemen Dekorasi Background Blur -->
                <div class="absolute -top-24 -right-24 w-60 h-60 rounded-full bg-[#E5A919]/10 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -left-20 w-60 h-60 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

                <!-- Card Carousel Features -->
                <div id="login-carousel" class="relative z-10 my-6 space-y-3">
                    <div class="relative h-[96px] overflow-hidden rounded-2xl">
                        <!-- Slide 1 -->
                        <div data-carousel-slide
                            class="carousel-slide absolute inset-0 transition-all duration-500 ease-in-out opacity-100 translate-x-0">
                            <div
                                class="flex items-center gap-3.5 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 hover:border-[#E5A919]/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-[#E5A919]/20 text-[#E5A919] flex items-center justify-center shrink-0 font-bold border border-[#E5A919]/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-xs text-white uppercase tracking-wider">Manajemen Slot &amp; Kuota Jadwal</h4>
                                    <p class="text-xs text-slate-300 leading-snug">Atur kuota jam operasional sebagai syarat booking pelanggan dari Aplikasi Mobile.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div data-carousel-slide
                            class="carousel-slide absolute inset-0 transition-all duration-500 ease-in-out opacity-0 translate-x-8 pointer-events-none">
                            <div
                                class="flex items-center gap-3.5 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 hover:border-emerald-400/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 font-bold border border-emerald-500/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-xs text-white uppercase tracking-wider">Verifikasi &amp; Penugasan Mekanik</h4>
                                    <p class="text-xs text-slate-300 leading-snug">Terima/tolak pesanan masuk, tugaskan mekanik bertugas, dan catat progres servis.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div data-carousel-slide
                            class="carousel-slide absolute inset-0 transition-all duration-500 ease-in-out opacity-0 translate-x-8 pointer-events-none">
                            <div
                                class="flex items-center gap-3.5 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 hover:border-blue-400/40 transition-all">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center shrink-0 font-bold border border-blue-500/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-xs text-white uppercase tracking-wider">Laporan Produktivitas &amp; Keuangan</h4>
                                    <p class="text-xs text-slate-300 leading-snug">Pantau kinerja mekanik dan proporsi transaksi pembayaran non-tunai secara transparan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Pagination Bar (Dots & Counter) -->
                    <div class="flex items-center justify-between px-1 pt-1">
                        <div class="flex items-center gap-1.5" id="carousel-dots">
                            <button type="button" data-carousel-dot="0" class="h-2 rounded-full transition-all duration-300 w-6 bg-[#E5A919] cursor-pointer" aria-label="Slide 1"></button>
                            <button type="button" data-carousel-dot="1" class="h-2 rounded-full transition-all duration-300 w-2 bg-white/30 hover:bg-white/60 cursor-pointer" aria-label="Slide 2"></button>
                            <button type="button" data-carousel-dot="2" class="h-2 rounded-full transition-all duration-300 w-2 bg-white/30 hover:bg-white/60 cursor-pointer" aria-label="Slide 3"></button>
                        </div>
                        <div class="flex items-center gap-1 text-slate-300 text-xs font-semibold tracking-wider">
                            <span id="carousel-current-index" class="text-[#E5A919]">01</span> / <span>03</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Informasi -->
                <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                    <span>&copy; 2026 PMM AutoCare</span>
                    <span class="text-slate-300 font-medium"></span>
                </div>
            </div>

            <!-- ============================================================
                 SISI KANAN: CARD FORM LOGIN ADMIN
            ============================================================ -->
            <div class="lg:col-span-6 p-8 sm:p-10 flex flex-col justify-between bg-white space-y-6">
                <div class="space-y-6">
                     <!-- Brand Header -->
                <div class="relative z-10 space-y-4">
                    <a href="/" class="inline-flex items-center gap-3 group focus:outline-none">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#E5A919] to-[#c48e0f] flex items-center justify-center shadow-lg shadow-[#E5A919]/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-7 h-7 text-[#1F3864]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="font-extrabold text-xl leading-tight tracking-tight text-[#1F3864] flex items-center gap-1.5">
                                PMM <span class="text-[#E5A919] font-semibold">AutoCare</span>
                            </span>
                            <span class="text-xs text-[#1F3864]/20 font-medium tracking-wide">Portal Administrator &amp; Pemilik</span>
                        </div>
                    </a>

                </div>

                    <div>
                        <h2 class="text-2xl font-extrabold text-[#1F3864] tracking-tight">Masuk ke Akun Anda</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Khusus Administrator &amp; Pemilik Bengkel. Pelanggan menggunakan aplikasi mobile.
                        </p>
                    </div>

                    {{-- Pesan sukses (mis. setelah logout) --}}
                    @if (session('sukses'))
                        <div class="flex items-start gap-2.5 rounded-md border border-emerald-200 bg-emerald-50 p-3.5"
                            role="status">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-emerald-800 font-medium">{{ session('sukses') }}</p>
                        </div>
                    @endif

                    {{-- Pesan kesalahan autentikasi --}}
                    @if ($errors->has('email'))
                        <div class="flex items-start gap-2.5 rounded-md border border-rose-200 bg-rose-50 p-3.5"
                            role="alert">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-rose-800 font-medium">{{ $errors->first('email') }}</p>
                        </div>
                    @endif

                    <!-- Form Login -->
                    <form action="{{ route('auth.login.panel') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                Email Admin
                            </label>
                            <input type="email" id="email" name="email" required autofocus
                                autocomplete="username" placeholder="admin@pmm-auto.test"
                                class="w-full text-sm font-semibold bg-slate-50 border @error('email') border-rose-300 @else border-slate-300 @enderror rounded-md p-3.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white transition-all shadow-sm">
                        </div>

                        <div class="space-y-1.5">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                                Password
                            </label>
                            <input type="password" id="password" name="password" required autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full text-sm font-semibold bg-slate-50 border border-slate-300 rounded-md p-3.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white transition-all shadow-sm">
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" name="remember" value="1"
                                    class="w-4 h-4 rounded border-slate-300 text-[#1F3864] focus:ring-[#1F3864] cursor-pointer">
                                <span class="text-xs text-slate-600 font-medium">Ingat akun di perangkat ini</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-[#1F3864] text-white font-bold text-sm rounded-md hover:bg-[#152747] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1F3864] transition-all shadow-md hover:shadow-lg cursor-pointer">
                            <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Masuk ke Panel Admin</span>
                        </button>
                    </form>
                </div>

                <!-- Box Kredensial Demo Pengujian -->
                {{-- <div class="bg-gradient-to-br from-[#1F3864]/5 to-[#E5A919]/10 border border-[#1F3864]/15 rounded-2xl p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#1F3864]">Akun Demo Pengujian</span>
                        <span class="text-[10px] bg-[#E5A919] text-[#1F3864] font-extrabold px-2 py-0.5 rounded-md shadow-sm">Simulasi</span>
                    </div>
                    <dl class="text-xs text-slate-600 space-y-1">
                        <div class="flex justify-between gap-3">
                            <dt class="font-semibold text-slate-700">Admin Bengkel:</dt>
                            <dd class="font-mono text-slate-900 font-bold">admin@pmm-auto.test / password</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="font-semibold text-slate-700">Pemilik Bengkel:</dt>
                            <dd class="font-mono text-slate-900 font-bold">pemilik@pmm-auto.test / password</dd>
                        </div>
                    </dl>
                    <p class="text-[11px] text-slate-500 pt-1.5 border-t border-[#1F3864]/10">
                        * Pelanggan memesan servis via aplikasi mobile (tidak dapat login di web ini).
                    </p>
                </div> --}}
            </div>

        </div>
    </main>

</body>

</html>
