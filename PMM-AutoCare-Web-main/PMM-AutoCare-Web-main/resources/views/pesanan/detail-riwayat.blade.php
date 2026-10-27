<!-- ================== RIWAYAT STATUS (JEJAK AUDIT) ================== -->
<section class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4"
    aria-labelledby="judul-riwayat">
    <header class="border-b border-slate-100 pb-4">
        <h2 id="judul-riwayat" class="text-lg font-bold text-[#1F3864]">Riwayat Status Pesanan</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Jejak audit setiap perubahan status — juga dikirim ke aplikasi mobile pelanggan
        </p>
    </header>

    @if ($pesanan->riwayatStatus->isEmpty())
        <p class="text-sm text-slate-500 italic text-center py-6">Belum ada riwayat status.</p>
    @else
        <ol class="relative space-y-5 pl-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            @foreach ($pesanan->riwayatStatus as $riwayat)
                @php
                    $warna = match ($riwayat->status) {
                        'menunggu' => 'bg-[#E5A919]',
                        'diterima' => 'bg-blue-500',
                        'dikerjakan' => 'bg-indigo-500',
                        'selesai' => 'bg-emerald-500',
                        'ditolak' => 'bg-rose-500',
                        'dibatalkan' => 'bg-slate-400',
                        default => 'bg-slate-300',
                    };
                    $terakhir = $loop->last;
                @endphp
                <li class="relative">
                    {{-- Titik penanda pada garis waktu --}}
                    <span
                        class="absolute -left-6 top-1.5 w-4 h-4 rounded-full border-2 border-white shadow-sm {{ $warna }} {{ $terakhir ? 'ring-2 ring-offset-1 ring-slate-300' : '' }}"></span>

                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <p class="font-bold text-sm text-[#1F3864]">{{ ucfirst($riwayat->status) }}</p>
                        <time datetime="{{ $riwayat->waktu?->toIso8601String() }}"
                            class="text-xs text-slate-500 font-medium">
                            {{ $riwayat->waktu?->translatedFormat('d M Y, H:i') }}
                        </time>
                    </div>

                    @if ($riwayat->keterangan)
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $riwayat->keterangan }}</p>
                    @endif

                    <p class="text-[11px] text-slate-400 mt-1">
                        @if ($riwayat->diubahOleh)
                            Diubah oleh <span class="font-medium text-slate-500">{{ $riwayat->diubahOleh->name }}</span>
                        @else
                            Dicatat otomatis oleh sistem
                        @endif
                    </p>
                </li>
            @endforeach
        </ol>
    @endif
</section>
