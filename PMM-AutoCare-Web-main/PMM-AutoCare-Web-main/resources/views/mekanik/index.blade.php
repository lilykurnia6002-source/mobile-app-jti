<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Data Mekanik Bengkel PMM AutoCare">
    {{-- Token CSRF untuk permintaan fetch dari resources/js/app.js --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Mekanik</title>

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
                <x-sidebar-info judul="Tanpa Akun Login">
                    Mekanik tidak login ke sistem. Admin mencatat penugasan dan progres atas nama mereka.
                </x-sidebar-info>
            </x-slot:info>
        </x-sidebar>

        <main id="main-content" class="flex-1 space-y-6 min-w-0">

            {{-- Header Halaman --}}
            <header
                class="page-header bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1F3864] tracking-tight">Data Mekanik Bengkel
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">
                        Mekanik tidak memiliki akun login — seluruh penugasan dan progres dicatat oleh admin
                        berdasarkan laporan lisan di lokasi.
                    </p>
                </div>

                {{-- Tombol Tambah Mekanik --}}
                <button type="button" id="btn-open-tambah-mekanik"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#E5A919] text-[#1F3864] text-xs sm:text-sm font-bold rounded-xl hover:bg-[#c48e0f] transition-all shadow-md hover:shadow-lg cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    <span>Tambah Mekanik</span>
                </button>
            </header>

            {{-- Ringkasan --}}
            <section aria-labelledby="judul-ringkasan-mekanik">
                <h2 id="judul-ringkasan-mekanik" class="sr-only">Ringkasan mekanik</h2>
                <ul class="grid grid-cols-1 sm:grid-cols-3 gap-4 list-none p-0 m-0">
                    <li class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Mekanik</h3>
                        <p class="text-2xl font-extrabold text-[#1F3864] tracking-tight mt-2">
                            {{ $ringkasan['total_mekanik'] }}
                        </p>
                        <span class="text-xs text-slate-500 mt-1 block">Terdaftar di bengkel</span>
                    </li>
                    <li class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mekanik Aktif</h3>
                        <p class="text-2xl font-extrabold text-emerald-600 tracking-tight mt-2">
                            {{ $ringkasan['aktif'] }}
                        </p>
                        <span class="text-xs text-slate-500 mt-1 block">Siap ditugaskan</span>
                    </li>
                    <li class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Bertugas</h3>
                        <p class="text-2xl font-extrabold text-[#E5A919] tracking-tight mt-2">
                            {{ $ringkasan['bertugas'] }}
                        </p>
                        <span class="text-xs text-slate-500 mt-1 block">Mengerjakan pesanan</span>
                    </li>
                </ul>
            </section>

            {{-- Daftar mekanik --}}
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden"
                aria-labelledby="judul-daftar-mekanik">
                <header class="p-5 sm:p-6 border-b border-slate-100">
                    <h2 id="judul-daftar-mekanik" class="text-lg font-bold text-[#1F3864]">Daftar Mekanik</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Ketersediaan dan keahlian spesialisasi mekanik bengkel
                    </p>
                </header>

                @if ($mekanik->isEmpty())
                    <div class="py-14 px-6 text-center">
                        <div
                            class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Belum ada data mekanik</p>
                        <p class="text-xs text-slate-500 mt-1">
                            Tambahkan identitas mekanik agar pesanan dari aplikasi mobile dapat ditugaskan.
                        </p>
                        <button type="button"
                            class="btn-open-tambah-mekanik inline-flex items-center gap-2 mt-4 px-4 py-2.5 bg-[#E5A919] text-[#1F3864] text-xs sm:text-sm font-bold rounded-xl hover:bg-[#c48e0f] transition-all shadow-md cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Tambah Mekanik Pertama</span>
                        </button>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100 list-none p-0 m-0">
                        @foreach ($mekanik as $m)
                            @php $bertugas = $m->tugas_berjalan > 0; @endphp
                            <li class="p-5 sm:p-6 hover:bg-slate-50/60 transition-colors">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="flex items-start gap-4 min-w-0">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-[#1F3864] text-[#E5A919] flex items-center justify-center font-bold shrink-0">
                                            {{ Str::of($m->nama)->substr(0, 2)->upper() }}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-[#1F3864]">{{ $m->nama }}</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                Spesialis
                                                <span class="font-semibold text-slate-700">
                                                    {{ ucfirst($m->spesialis) }}
                                                </span>
                                            </p>
                                            @if ($m->no_hp)
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $m->no_hp }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        {{-- Status ketersediaan --}}
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $bertugas ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $bertugas ? 'bg-blue-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                            {{ $bertugas ? 'Bertugas' : 'Tersedia' }}
                                        </span>

                                        {{-- Status aktif/nonaktif --}}
                                        @if (! $m->aktif)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border bg-slate-100 text-slate-600 border-slate-200">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-slate-100 text-xs">
                                    <div>
                                        <dt class="font-semibold text-slate-400 uppercase tracking-wide">Tugas Berjalan
                                        </dt>
                                        <dd class="text-slate-800 font-bold mt-0.5">{{ $m->tugas_berjalan }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-slate-400 uppercase tracking-wide">Total
                                            Penugasan</dt>
                                        <dd class="text-slate-800 font-bold mt-0.5">{{ $m->total_penugasan }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-slate-400 uppercase tracking-wide">Ketersediaan
                                        </dt>
                                        <dd class="text-slate-800 font-bold mt-0.5">
                                            {{ $bertugas ? 'Sedang mengerjakan' : 'Dapat ditugaskan' }}
                                        </dd>
                                    </div>
                                </dl>

                                {{-- Aksi CRUD --}}
                                <div class="flex flex-wrap items-center justify-end gap-1.5 mt-4 pt-4 border-t border-slate-100">
                                    {{-- Edit identitas --}}
                                    <button type="button"
                                        class="btn-edit-mekanik inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 text-slate-700 hover:bg-[#1F3864] hover:text-white transition-all cursor-pointer"
                                        title="Edit identitas mekanik"
                                        data-nama="{{ $m->nama }}"
                                        data-spesialis="{{ $m->spesialis }}"
                                        data-no-hp="{{ $m->no_hp }}"
                                        data-aktif="{{ $m->aktif ? '1' : '0' }}"
                                        data-url="{{ route('admin.mekanik.update', $m) }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        <span>Edit</span>
                                    </button>

                                    {{-- Aktif / nonaktif --}}
                                    <button type="button"
                                        class="btn-toggle-mekanik inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl transition-all cursor-pointer {{ $m->aktif ? 'bg-slate-100 text-slate-700 hover:bg-slate-700 hover:text-white' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                                        title="{{ $m->aktif ? 'Nonaktifkan mekanik' : 'Aktifkan mekanik' }}"
                                        data-nama="{{ $m->nama }}"
                                        data-aktif="{{ $m->aktif ? '1' : '0' }}"
                                        data-url="{{ route('admin.mekanik.toggle', $m) }}">
                                        @if ($m->aktif)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                </path>
                                            </svg>
                                            <span>Nonaktifkan</span>
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Aktifkan</span>
                                        @endif
                                    </button>

                                    {{-- Hapus (soft delete). Dinonaktifkan selama masih bertugas
                                         agar aturan server juga terlihat di antarmuka. --}}
                                    @if ($bertugas)
                                        <button type="button" disabled
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed"
                                            title="Tidak dapat dihapus: mekanik masih mengerjakan {{ $m->tugas_berjalan }} pesanan. Selesaikan atau alihkan tugasnya terlebih dahulu.">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn-hapus-mekanik inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white transition-all cursor-pointer"
                                            title="Hapus mekanik. Riwayat pengerjaannya tetap tersimpan."
                                            data-nama="{{ $m->nama }}"
                                            data-total-penugasan="{{ $m->total_penugasan }}"
                                            data-url="{{ route('admin.mekanik.destroy', $m) }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

        </main>
    </div>

    {{-- Modal Popups CRUD Mekanik --}}
    @include('mekanik.modal-tambah-mekanik')
    @include('mekanik.modal-edit-mekanik')

    {{-- Popup / Alert Global --}}
    @include('popup.alert')

    {{-- Footer --}}
    @include('dashboard.footer')

</body>

</html>
