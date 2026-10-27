<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Detail Pesanan Servis PMM AutoCare - Sistem Informasi Pelayanan dan Penjadwalan Servis Mobil">
    {{-- Token CSRF untuk permintaan fetch dari resources/js/app.js --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail {{ $pesanan->kode_pesanan }} — PMM AutoCare</title>

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

        {{-- Navigasi Samping (blueprint komponen) + pintasan filter status.
             Halaman detail tidak memuat hitungan per status, jadi komponen
             memakai nilai bawaan dan hanya menandai status pesanan ini. --}}
        <x-sidebar :jumlah-menunggu="$jumlahMenunggu ?? 0">
            <x-sidebar-filter-status :aktif="$pesanan->status" />

            <x-slot:info>
                <x-sidebar-info judul="Alur Pesanan">
                    Menunggu → Diterima → Dikerjakan → Selesai. Setiap perubahan dikirim ke aplikasi mobile pelanggan.
                </x-sidebar-info>
            </x-slot:info>
        </x-sidebar>

        <main id="main-content" class="flex-1 space-y-6 min-w-0">

            @include('pesanan.detail-header')
            @include('pesanan.detail-ringkasan')
            @include('pesanan.detail-rincian-biaya')
            @include('pesanan.detail-sparepart')
            @include('pesanan.detail-pengerjaan')
            @include('pesanan.detail-riwayat')

        </main>

    </div>

    {{-- Modal Popup Terima & Tolak Pesanan (hanya relevan saat status menunggu) --}}
    @include('dashboard.modal-proses')

    {{-- Popup / Alert Global --}}
    @include('popup.alert')

    {{-- Footer --}}
    @include('dashboard.footer')

</body>

</html>
