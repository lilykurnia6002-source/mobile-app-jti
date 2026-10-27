<!-- ================== D. PENUGASAN / STATUS MEKANIK ================== -->
<section id="penugasan-mekanik" aria-labelledby="judul-mekanik"
    class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4">
    <header class="section-header flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <h2 id="judul-mekanik" class="text-lg font-bold text-[#1F3864]">Daftar Mekanik</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ketersediaan dan keahlian spesialisasi mekanik bengkel
            </p>
        </div>
        <a href="{{ route('admin.mekanik.index') }}"
            class="text-xs font-semibold text-[#1F3864] hover:text-[#E5A919] inline-flex items-center gap-1 transition-colors">
            Kelola Mekanik
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                </path>
            </svg>
        </a>
    </header>

    {{-- Status mekanik: tersedia / bertugas (tb_mekanik + relasi tb_pengerjaan).
         Hanya mekanik aktif yang ditampilkan; yang sudah dihapus (resign)
         tidak muncul di sini, tetapi namanya tetap tersimpan di riwayat. --}}
    <ul class="daftar-mekanik grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 list-none p-0 m-0">
        @forelse ($daftarMekanik as $mekanik)
            @php
                $bertugas = $mekanik->tugas_berjalan > 0;
            @endphp
            <li
                class="item-mekanik bg-slate-50/75 rounded-xl p-4 border border-slate-200/70 {{ $bertugas ? 'hover:border-[#1F3864]/30' : 'hover:border-emerald-500/30' }} transition-all flex flex-col justify-between gap-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-[#1F3864] truncate">{{ $mekanik->nama }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Spesialis:
                            <span class="font-medium text-slate-700">{{ ucfirst($mekanik->spesialis) }}</span>
                        </p>
                        @if ($mekanik->no_hp)
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $mekanik->no_hp }}</p>
                        @endif
                    </div>
                    <div
                        class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center font-bold text-xs {{ $bertugas ? 'bg-[#1F3864]/10 text-[#1F3864]' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ Str::of($mekanik->nama)->substr(0, 2)->upper() }}
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200/50 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500">Status Pengerjaan:</span>
                    @if ($bertugas)
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200"
                            data-status-mekanik="bertugas"
                            title="Sedang mengerjakan {{ $mekanik->tugas_berjalan }} pesanan">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                            Bertugas ({{ $mekanik->tugas_berjalan }})
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"
                            data-status-mekanik="tersedia" title="Siap ditugaskan pada pesanan berikutnya">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Tersedia
                        </span>
                    @endif
                </div>
            </li>
        @empty
            <li class="sm:col-span-2 lg:col-span-3">
                <div class="text-center py-10 px-4">
                    <div
                        class="w-12 h-12 mx-auto rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">Belum ada mekanik aktif</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Tambahkan identitas mekanik agar pesanan dari aplikasi mobile dapat ditugaskan.
                    </p>
                    <a href="{{ route('admin.mekanik.index') }}"
                        class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-[#1F3864] hover:text-[#E5A919] transition-colors">
                        Kelola Data Mekanik
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </li>
        @endforelse
    </ul>
</section>
