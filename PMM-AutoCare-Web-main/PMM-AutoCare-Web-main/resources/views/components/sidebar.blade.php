@props(['jumlahMenunggu' => 0])

@php
    // Menu utama panel admin. `aktif` adalah pola nama route untuk
    // mendeteksi halaman yang sedang dibuka.
    $menu = [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'aktif' => 'admin.dashboard',
            'params' => [],
            'badge' => 0,
            'ikon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        [
            // Manajemen slot jadwal: prasyarat booking dari aplikasi mobile.
            'label' => 'Kelola Jadwal Booking',
            'route' => 'admin.slot.index',
            'aktif' => 'admin.slot.*',
            'params' => [],
            'badge' => 0,
            'ikon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        ],
        [
            'label' => 'Pesanan',
            'route' => 'admin.pesanan.index',
            'aktif' => 'admin.pesanan.*',
            'params' => ['status' => 'menunggu'],
            'badge' => $jumlahMenunggu,
            'ikon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        ],
        [
            'label' => 'Mekanik',
            'route' => 'admin.mekanik.index',
            'aktif' => 'admin.mekanik.*',
            'params' => [],
            'badge' => 0,
            'ikon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        ],
        [
            // Master data sparepart: harga & persediaan bengkel.
            'label' => 'Sparepart',
            'route' => 'admin.sparepart.index',
            'aktif' => 'admin.sparepart.*',
            'params' => [],
            'badge' => 0,
            'ikon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        ],
    ];
@endphp

<aside id="sidebar" aria-label="Navigasi panel admin" class="w-full lg:w-64 shrink-0">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 sticky top-24">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">
            Menu Utama
        </div>

        <nav>
            <ul class="space-y-1">
                @foreach ($menu as $item)
                    @php $aktif = request()->routeIs($item['aktif']); @endphp
                    <li>
                        <a href="{{ route($item['route'], $item['params']) }}"
                            @if ($aktif) aria-current="page" @endif
                            class="flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl text-sm transition-all duration-150 {{ $aktif ? 'bg-[#1F3864] text-white font-semibold shadow-md shadow-[#1F3864]/20' : 'font-medium text-slate-600 hover:text-[#1F3864] hover:bg-slate-100/80' }}">
                            <span class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 shrink-0 {{ $aktif ? 'text-[#E5A919]' : 'text-slate-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $item['ikon'] }}"></path>
                                </svg>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </span>

                            @if ($item['badge'] > 0)
                                <span
                                    class="bg-[#E5A919] text-[#1F3864] font-bold text-xs px-2 py-0.5 rounded-full shadow-sm shrink-0">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Seksi tambahan khusus halaman tertentu (mis. filter status). --}}
        {{ $slot }}

        {{-- Kotak informasi di bagian bawah sidebar. --}}
        @isset($info)
            <hr class="my-4 border-slate-100">
            {{ $info }}
        @endisset
    </div>
</aside>
