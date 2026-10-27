{{--
============================================================================
 FILTER STATUS PESANAN (SIDEBAR)
============================================================================
 Daftar pintasan status pesanan beserta jumlahnya. Dipakai pada halaman
 pesanan memakai slot seksi tambahan sidebar:

     <x-sidebar>
         <x-sidebar-filter-status :jumlah-per-status="$jumlahPerStatus" :aktif="$status" />
         ...
     </x-sidebar>

 `$aktif` bernilai null saat menampilkan seluruh pesanan.
============================================================================
--}}

@props(['jumlahPerStatus' => [], 'aktif' => null])

@php
    // Urutan mengikuti alur bisnis: menunggu → diterima → dikerjakan → selesai.
    $status = [
        'menunggu' => ['label' => 'Menunggu Antrean', 'warna' => 'bg-amber-500'],
        'diterima' => ['label' => 'Diterima', 'warna' => 'bg-blue-500'],
        'dikerjakan' => ['label' => 'Dikerjakan', 'warna' => 'bg-indigo-500'],
        'selesai' => ['label' => 'Selesai', 'warna' => 'bg-emerald-500'],
        'ditolak' => ['label' => 'Ditolak', 'warna' => 'bg-rose-500'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'warna' => 'bg-slate-400'],
    ];
@endphp

<div>
    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2 mt-2">
        Filter Status
    </div>
    <ul class="space-y-0.5">
        @foreach ($status as $kunci => $meta)
            @php $terpilih = $aktif === $kunci; @endphp
            <li>
                <a href="{{ route('admin.pesanan.index', ['status' => $kunci]) }}"
                    @if ($terpilih) aria-current="page" @endif
                    class="flex items-center justify-between gap-2 px-3.5 py-2 rounded-xl text-xs transition-all duration-150 {{ $terpilih ? 'bg-slate-100 font-semibold text-[#1F3864]' : 'text-slate-600 hover:text-[#1F3864] hover:bg-slate-50' }}">
                    <span class="flex items-center gap-2 min-w-0">
                        <span class="w-1.5 h-1.5 rounded-full {{ $meta['warna'] }} shrink-0"></span>
                        <span class="truncate">{{ $meta['label'] }}</span>
                    </span>
                    <span class="font-bold text-slate-500 shrink-0">{{ $jumlahPerStatus[$kunci] ?? 0 }}</span>
                </a>
            </li>
        @endforeach

        <li>
            <a href="{{ route('admin.pesanan.index') }}"
                @if (empty($aktif)) aria-current="page" @endif
                class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition-all duration-150 {{ empty($aktif) ? 'bg-slate-100 font-semibold text-[#1F3864]' : 'text-slate-500 hover:text-[#1F3864] hover:bg-slate-50' }}">
                <span class="w-1.5 h-1.5 rounded-full bg-[#1F3864] shrink-0"></span>
                Semua Pesanan
            </a>
        </li>
    </ul>
</div>
