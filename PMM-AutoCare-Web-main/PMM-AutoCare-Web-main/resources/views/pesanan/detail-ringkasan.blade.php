@php
    $mobil = $pesanan->mobil;
    $slot = $pesanan->slot;
    $pelanggan = $pesanan->pelanggan;
@endphp

<!-- ================== RINGKASAN: PELANGGAN, MOBIL, SLOT ================== -->
<section class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4"
    aria-labelledby="judul-ringkasan-detail">
    <header class="border-b border-slate-100 pb-4">
        <h2 id="judul-ringkasan-detail" class="text-lg font-bold text-[#1F3864]">Data Pelanggan &amp; Kendaraan</h2>
        <p class="text-xs text-slate-500 mt-0.5">Informasi pemesan dan jadwal servis yang dipilih</p>
    </header>

    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-sm">
        {{-- Pelanggan --}}
        <div class="space-y-1">
            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Nama Pelanggan</dt>
            <dd class="text-slate-800 font-bold">{{ $pelanggan?->nama ?? '—' }}</dd>
            @if ($pelanggan?->no_hp)
                <dd class="text-xs text-slate-500">{{ $pelanggan->no_hp }}</dd>
            @endif
            @if ($pelanggan?->user?->email)
                <dd class="text-xs text-slate-500 break-all">{{ $pelanggan->user->email }}</dd>
            @endif
        </div>

        {{-- Alamat --}}
        <div class="space-y-1 sm:col-span-2">
            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Alamat</dt>
            <dd class="text-slate-700 leading-relaxed">{{ $pelanggan?->alamat ?? '—' }}</dd>
        </div>

        {{-- Mobil --}}
        <div class="space-y-1">
            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Kendaraan</dt>
            @if ($mobil)
                <dd class="text-slate-800 font-bold">{{ $mobil->merk }} {{ $mobil->tipe }}</dd>
                <dd class="text-sm">
                    <span class="font-mono font-bold text-[#1F3864]">{{ $mobil->plat_nomor }}</span>
                    <span class="text-slate-500">· {{ $mobil->tahun }}</span>
                </dd>
            @else
                <dd class="text-slate-400 italic">Data mobil tidak tersedia</dd>
            @endif
        </div>

        {{-- Slot --}}
        <div class="space-y-1">
            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Slot Servis</dt>
            @if ($slot)
                <dd class="text-slate-800 font-semibold">{{ $slot->tanggal->translatedFormat('l, d F Y') }}</dd>
                <dd class="text-sm font-mono text-[#1F3864]">
                    {{ substr($slot->jam_mulai, 0, 5) }}–{{ substr($slot->jam_selesai, 0, 5) }}
                </dd>
                <dd class="text-xs text-slate-500">
                    Kuota terpakai: {{ $slot->terisi }}/{{ $slot->kapasitas }}
                </dd>
            @else
                <dd class="text-slate-500 italic">Walk-in (datang langsung tanpa slot)</dd>
            @endif
        </div>

        {{-- Kanal & total --}}
        <div class="space-y-1">
            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Kanal Pemesanan</dt>
            <dd class="text-slate-700 font-medium">{{ ucfirst($pesanan->kanal_pesan) }}</dd>
            <dd class="text-xs text-slate-500">Total: Rp
                {{ number_format((float) $pesanan->total_biaya, 0, ',', '.') }}</dd>
        </div>

        {{-- Keluhan --}}
        <div class="space-y-1 sm:col-span-2 lg:col-span-3 pt-3 border-t border-slate-100">
            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Keluhan Pelanggan</dt>
            <dd class="text-slate-700 leading-relaxed">{{ $pesanan->keluhan }}</dd>
        </div>
    </dl>

    {{-- Keluhan tambahan dari mekanik (bila ada) --}}
    @if ($pesanan->keluhanTambahan->isNotEmpty())
        <div class="pt-4 border-t border-slate-100 space-y-2">
            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M5.062 19h13.876c1.54 0 2.502-1.667 1.732-3L13.732 5c-.77-1.333-2.694-1.333-3.464 0L3.33 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                Temuan Tambahan Saat Pengerjaan
            </p>
            <ul class="space-y-2">
                @foreach ($pesanan->keluhanTambahan as $temuan)
                    <li class="rounded-xl border border-amber-200 bg-amber-50/60 p-3.5">
                        <p class="text-sm text-slate-800 leading-relaxed">{{ $temuan->keluhan }}</p>
                        <p class="text-[11px] text-slate-500 mt-1.5">
                            @if ($temuan->perkiraan_biaya)
                                Perkiraan biaya: <strong class="text-amber-800">Rp
                                    {{ number_format((float) $temuan->perkiraan_biaya, 0, ',', '.') }}</strong>
                                ·
                            @endif
                            Dicatat oleh {{ $temuan->ditambahkanOleh?->name ?? 'admin' }}
                        </p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
