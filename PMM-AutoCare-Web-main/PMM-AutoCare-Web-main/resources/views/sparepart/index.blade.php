<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Master Data Sparepart PMM AutoCare - Sistem Informasi Pelayanan dan Penjadwalan Servis Mobil">
    {{-- Token CSRF untuk permintaan fetch dari resources/js/app.js --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Master Data Sparepart — PMM AutoCare</title>

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

        {{-- Navigasi Samping (blueprint komponen) --}}
        <x-sidebar :jumlah-menunggu="$jumlahMenunggu ?? 0">
            <x-slot:info>
                <x-sidebar-info judul="Stok Berkurang Otomatis">
                    Stok berkurang setiap admin mencatat pemakaian sparepart pada pesanan.
                </x-sidebar-info>
            </x-slot:info>
        </x-sidebar>

        <main id="main-content" class="flex-1 space-y-6 min-w-0">

            {{-- Header Halaman + Tombol Tambah --}}
            <header
                class="page-header bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1F3864] tracking-tight">
                        Master Data Sparepart
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">
                        Kelola harga dan persediaan sparepart bengkel. Pemakaian pada pesanan dicatat dari
                        halaman detail pesanan.
                    </p>
                </div>

                <button type="button" id="btn-open-tambah-sparepart"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E5A919] text-[#1F3864] text-xs sm:text-sm font-bold rounded-xl hover:bg-[#c48e0f] transition-all shadow-md hover:shadow-lg cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    <span>Tambah Sparepart</span>
                </button>
            </header>

            {{-- Ringkasan --}}
            <section aria-labelledby="judul-ringkasan-sparepart">
                <h2 id="judul-ringkasan-sparepart" class="sr-only">Ringkasan persediaan sparepart</h2>
                <ul class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 list-none p-0 m-0">
                    <li
                        class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Item</h3>
                        <p class="text-2xl font-extrabold text-[#1F3864] tracking-tight mt-2">
                            {{ $ringkasan['total_item'] }}
                        </p>
                        <span class="text-xs text-slate-500 mt-1 block">Jenis sparepart terdaftar</span>
                    </li>
                    <li
                        class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nilai Persediaan
                        </h3>
                        <p class="text-2xl font-extrabold text-emerald-600 tracking-tight mt-2">
                            Rp {{ number_format($ringkasan['nilai_persediaan'], 0, ',', '.') }}
                        </p>
                        <span class="text-xs text-slate-500 mt-1 block">Harga × stok tersedia</span>
                    </li>
                    <li
                        class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Menipis</h3>
                        <p class="text-2xl font-extrabold text-[#E5A919] tracking-tight mt-2">
                            {{ $ringkasan['stok_menipis'] }}
                        </p>
                        <span class="text-xs text-slate-500 mt-1 block">Sudah mencapai stok minimal</span>
                    </li>
                    <li
                        class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Habis</h3>
                        <p class="text-2xl font-extrabold text-rose-600 tracking-tight mt-2">
                            {{ $ringkasan['stok_habis'] }}
                        </p>
                        <span class="text-xs text-slate-500 mt-1 block">Perlu segera dipesan ulang</span>
                    </li>
                </ul>
            </section>

            {{-- Tabel sparepart --}}
            @include('sparepart.tabel-sparepart')

        </main>
    </div>

    {{-- Modal Popups CRUD Sparepart --}}
    @include('sparepart.modal-tambah-sparepart')
    @include('sparepart.modal-edit-sparepart')

    {{-- Popup / Alert Global --}}
    @include('popup.alert')

    {{-- Footer --}}
    @include('dashboard.footer')

</body>

</html>
