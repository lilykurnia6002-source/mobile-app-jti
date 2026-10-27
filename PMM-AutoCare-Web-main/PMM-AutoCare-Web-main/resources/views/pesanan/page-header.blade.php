<!-- Header Halaman Daftar Pesanan -->
<header class="page-header bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1F3864] tracking-tight">
                Manajemen Pesanan Servis
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Verifikasi pesanan dari aplikasi mobile, pantau pengerjaan, dan selesaikan servis.
            </p>
        </div>

        {{-- Pencarian: kode pesanan / nama pelanggan / plat nomor --}}
        <form action="{{ route('admin.pesanan.index') }}" method="get"
            class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200 text-xs font-medium w-full lg:w-auto">
            @if ($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <input type="search" name="q" value="{{ $pencarian }}"
                placeholder="Cari kode / pelanggan / plat nomor…"
                class="flex-1 lg:w-64 px-3 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864]">
            <button type="submit"
                class="px-3 py-2 bg-[#1F3864] text-white rounded-lg font-semibold hover:bg-[#152747] transition-colors cursor-pointer whitespace-nowrap">
                Cari
            </button>
            @if ($pencarian)
                <a href="{{ route('admin.pesanan.index', array_filter(['status' => $status])) }}"
                    class="px-3 py-2 bg-white text-slate-600 border border-slate-300 rounded-lg font-semibold hover:bg-slate-100 transition-colors whitespace-nowrap">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tab filter status --}}
    <nav aria-label="Filter status pesanan" class="border-t border-slate-100 pt-4">
        <ul class="flex flex-wrap items-center gap-1.5 text-xs font-medium">
            <li>
                <a href="{{ route('admin.pesanan.index', array_filter(['q' => $pencarian])) }}"
                    class="px-3 py-1.5 rounded-lg transition-colors {{ empty($status) ? 'bg-[#1F3864] text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-[#1F3864] hover:bg-slate-100' }}">
                    Semua
                    <span class="ml-1 font-bold">{{ array_sum($jumlahPerStatus) }}</span>
                </a>
            </li>
            @foreach ([
        'menunggu' => 'Menunggu',
        'diterima' => 'Diterima',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ] as $kunci => $label)
                <li>
                    <a href="{{ route('admin.pesanan.index', array_filter(['status' => $kunci, 'q' => $pencarian])) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-colors {{ $status === $kunci ? 'bg-[#1F3864] text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-[#1F3864] hover:bg-slate-100' }}">
                        {{ $label }}
                        <span
                            class="{{ $status === $kunci ? 'bg-[#E5A919] text-[#1F3864]' : 'bg-slate-200 text-slate-600' }} font-bold px-1.5 rounded-full text-[10px]">
                            {{ $jumlahPerStatus[$kunci] ?? 0 }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</header>
