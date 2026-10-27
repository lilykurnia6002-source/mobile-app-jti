@php
    $mobil = $pesanan->mobil;
    $badge = match ($pesanan->status) {
        'menunggu' => ['bg-amber-50 text-amber-800 border-amber-200', 'bg-[#E5A919]'],
        'diterima' => ['bg-blue-50 text-blue-700 border-blue-200', 'bg-blue-500'],
        'dikerjakan' => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'bg-indigo-500'],
        'selesai' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500'],
        'ditolak' => ['bg-rose-50 text-rose-700 border-rose-200', 'bg-rose-500'],
        default => ['bg-slate-100 text-slate-600 border-slate-200', 'bg-slate-400'],
    };
@endphp

<!-- Header Detail Pesanan -->
<header class="page-header bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
        <div class="space-y-2">
            <a href="{{ route('admin.pesanan.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#1F3864] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke daftar pesanan
            </a>

            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1F3864] tracking-tight">
                    {{ $pesanan->kode_pesanan }}
                </h1>
                <span
                    class="badge inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border {{ $badge[0] }}"
                    data-status-pesanan="{{ $pesanan->status }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $badge[1] }}"></span>
                    {{ ucfirst($pesanan->status) }}
                </span>
            </div>

            <p class="text-sm text-slate-500">
                Diajukan
                <time datetime="{{ $pesanan->waktu_pesan?->toIso8601String() }}" class="font-semibold text-slate-700">
                    {{ $pesanan->waktu_pesan?->translatedFormat('l, d F Y — H:i') }}
                </time>
                <span class="text-slate-400">({{ $pesanan->waktu_pesan?->diffForHumans() }})</span>
            </p>
        </div>

        {{-- Aksi sesuai status --}}
        <div class="flex flex-wrap items-center gap-2">
            @if ($pesanan->status === 'menunggu')
                <button type="button"
                    class="btn-open-modal inline-flex items-center gap-2 px-4 py-2.5 bg-[#1F3864] hover:bg-[#152747] text-white font-semibold text-xs sm:text-sm rounded-xl transition-all shadow-sm cursor-pointer"
                    data-id="{{ $pesanan->id_pesanan }}"
                    data-kode="{{ $pesanan->kode_pesanan }}"
                    data-pelanggan="{{ $pesanan->pelanggan?->nama }}"
                    data-mobil="{{ $mobil ? trim($mobil->merk.' '.$mobil->tipe).' ('.$mobil->plat_nomor.')' : '' }}"
                    data-terima-url="{{ route('admin.pesanan.terima', $pesanan) }}"
                    data-tolak-url="{{ route('admin.pesanan.tolak', $pesanan) }}">
                    <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    <span>Proses Pesanan (Terima / Tolak)</span>
                </button>
            @endif

            {{-- Diterima → Mulai Kerjakan (mekanik mulai) --}}
            @if ($pesanan->status === 'diterima')
                <button type="button"
                    class="btn-kerjakan-pesanan inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs sm:text-sm rounded-xl transition-all shadow-sm cursor-pointer"
                    data-url="{{ route('admin.pesanan.kerjakan', $pesanan) }}"
                    data-kode="{{ $pesanan->kode_pesanan }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Mulai Kerjakan</span>
                </button>
            @endif

            {{-- Dikerjakan → Selesaikan --}}
            @if ($pesanan->status === 'dikerjakan')
                <button type="button"
                    class="btn-selesai-pesanan inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs sm:text-sm rounded-xl transition-all shadow-sm cursor-pointer"
                    data-url="{{ route('admin.pesanan.selesai', $pesanan) }}"
                    data-kode="{{ $pesanan->kode_pesanan }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Tandai Selesai</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Alasan penolakan, bila ada --}}
    @if ($pesanan->status === 'ditolak' && $pesanan->alasan_tolak)
        <div class="flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3.5" role="alert">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-bold text-rose-900">Alasan Penolakan</p>
                <p class="text-xs text-rose-800 mt-0.5 leading-relaxed">{{ $pesanan->alasan_tolak }}</p>
            </div>
        </div>
    @endif
</header>
