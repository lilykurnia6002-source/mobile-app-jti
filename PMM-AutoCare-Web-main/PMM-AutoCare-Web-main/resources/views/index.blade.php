<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Dashboard Admin PMM AutoCare">
    {{-- Token CSRF untuk permintaan fetch dari resources/js/app.js --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin</title>

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

    <!-- Kontener Utama (Sidebar + Content) -->
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 flex flex-col lg:flex-row gap-6">

        {{-- Navigasi Samping (blueprint komponen) --}}
        <x-sidebar :jumlah-menunggu="$jumlahMenunggu ?? 0">
            <x-slot:info>
                <x-sidebar-info judul="Status Bengkel Aktif">
                    PMM AutoCare beroperasi normal. {{ $jumlahBertugas ?? 0 }} dari
                    {{ $jumlahMekanik ?? 0 }} mekanik sedang bertugas.
                </x-sidebar-info>
            </x-slot:info>
        </x-sidebar>

        <!-- Konten Utama Dashboard Admin -->
        <main id="main-content" class="flex-1 space-y-8 min-w-0">

            {{-- Header Halaman --}}
            @include('dashboard.page-header')

            {{-- A. Ringkasan Statistik --}}
            @include('dashboard.ringkasan')

            {{-- B. Jadwal Booking Hari Ini --}}
            @include('dashboard.jadwal-booking')

            {{-- C. Pesanan Masuk --}}
            @include('dashboard.pesanan-masuk')

            {{-- D. Penugasan / Status Mekanik --}}
            @include('dashboard.penugasan-mekanik')

        </main>

    </div>

    {{-- Modal Popup Terima & Tolak Pesanan --}}
    @include('dashboard.modal-proses')

    {{-- Popup / Alert Global (notifikasi & konfirmasi) --}}
    @include('popup.alert')

    {{-- Footer --}}
    @include('dashboard.footer')

</body>

</html>
