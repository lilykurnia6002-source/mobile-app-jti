<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Profil Admin PMM AutoCare">
    {{-- Token CSRF untuk permintaan fetch dari resources/js/app.js --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya</title>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex flex-col selection:bg-[#E5A919]/30 selection:text-[#1F3864]">

    {{-- Header Bar --}}
    @include('dashboard.header')

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 flex flex-col lg:flex-row gap-6">

        {{-- Navigasi Samping (blueprint komponen) + pintasan ke profil --}}
        <x-sidebar :jumlah-menunggu="$jumlahMenunggu ?? 0">
            <hr class="my-4 border-slate-100">
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">
                    Akun
                </div>
                <a href="{{ route('admin.profil.show') }}" aria-current="page"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm bg-[#1F3864] text-white shadow-md shadow-[#1F3864]/20">
                    <svg class="w-5 h-5 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil Saya</span>
                </a>
            </div>
        </x-sidebar>

        <main id="main-content" class="flex-1 space-y-6 min-w-0">

            <header class="page-header bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1F3864] tracking-tight">Profil Saya</h1>
                <p class="text-slate-500 text-sm mt-1">
                    Kelola informasi akun Anda yang dipakai untuk mengakses panel admin.
                </p>
            </header>

            {{-- Pesan sukses --}}
            @if (session('sukses'))
                <div class="flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                    role="status">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-emerald-800 font-medium">{{ session('sukses') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kartu identitas --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-4">
                    <div class="text-center space-y-3">
                        <div
                            class="w-20 h-20 mx-auto rounded-2xl bg-[#1F3864] text-[#E5A919] flex items-center justify-center font-extrabold text-2xl tracking-tight">
                            {{ Str::of($user->name)->substr(0, 2)->upper() }}
                        </div>
                        <div>
                            <h2 class="font-bold text-lg text-[#1F3864]">{{ $user->name }}</h2>
                            <p class="text-xs text-slate-500 break-all mt-0.5">{{ $user->email }}</p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#E5A919] text-[#1F3864]">
                            {{ ucfirst($user->role) }} Bengkel
                        </span>
                    </div>

                    <dl class="pt-4 border-t border-slate-100 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Nomor HP</dt>
                            <dd class="text-slate-800 font-medium text-right">{{ $user->no_hp ?? '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Terdaftar</dt>
                            <dd class="text-slate-800 font-medium text-right">
                                {{ $user->created_at?->translatedFormat('d M Y') }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</dt>
                            <dd class="text-emerald-700 font-semibold text-right">Aktif</dd>
                        </div>
                    </dl>

                    <form action="{{ route('admin.logout') }}" method="post" class="pt-2">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-rose-200 text-rose-700 text-xs sm:text-sm font-semibold hover:bg-rose-50 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Keluar dari Panel
                        </button>
                    </form>
                </div>

                {{-- Form edit profil --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-5">
                    <header class="border-b border-slate-100 pb-4">
                        <h2 class="text-lg font-bold text-[#1F3864]">Ubah Data Profil</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Biarkan kolom password kosong bila tidak ingin menggantinya.
                        </p>
                    </header>

                    {{-- Ringkasan error validasi --}}
                    @if ($errors->any())
                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4" role="alert">
                            <p class="text-sm font-bold text-rose-900">Periksa kembali data Anda:</p>
                            <ul class="mt-2 space-y-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li class="text-xs text-rose-800">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.profil.update') }}" method="post" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="name"
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" required
                                    value="{{ old('name', $user->name) }}"
                                    class="w-full text-sm bg-slate-50 border @error('name') border-rose-300 @else border-slate-300 @enderror rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white transition-all">
                                @error('name')
                                    <p class="text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="no_hp"
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Nomor HP
                                </label>
                                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                    placeholder="0812xxxxxxx"
                                    class="w-full text-sm bg-slate-50 border @error('no_hp') border-rose-300 @else border-slate-300 @enderror rounded-xl p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white transition-all">
                                @error('no_hp')
                                    <p class="text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Email <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                value="{{ old('email', $user->email) }}"
                                class="w-full text-sm bg-slate-50 border @error('email') border-rose-300 @else border-slate-300 @enderror rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white transition-all">
                            <p class="text-[11px] text-slate-500">
                                Email ini dipakai untuk masuk ke panel admin.
                            </p>
                            @error('email')
                                <p class="text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 border-t border-slate-100 space-y-1.5">
                            <label for="password"
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Password Baru
                            </label>
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                placeholder="Kosongkan bila tidak diganti"
                                class="w-full text-sm bg-slate-50 border @error('password') border-rose-300 @else border-slate-300 @enderror rounded-xl p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white transition-all">
                            <p class="text-[11px] text-slate-500">Minimal 8 karakter.</p>
                            @error('password')
                                <p class="text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1F3864] text-white font-bold text-xs sm:text-sm rounded-xl hover:bg-[#152747] transition-all shadow-md hover:shadow-lg cursor-pointer">
                                <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </main>
    </div>

    {{-- Popup / Alert Global --}}
    @include('popup.alert')

    {{-- Footer --}}
    @include('dashboard.footer')

</body>

</html>
