<!-- ================== PENUGASAN MEKANIK & PENGERJAAN ================== -->
<section class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4"
    aria-labelledby="judul-pengerjaan">
    <header class="border-b border-slate-100 pb-4">
        <h2 id="judul-pengerjaan" class="text-lg font-bold text-[#1F3864]">Penugasan Mekanik</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Mekanik tanpa akun login — seluruh penugasan dicatat atas nama admin
        </p>
    </header>

    @if ($pesanan->pengerjaan->isEmpty())
        <div class="text-center py-8">
            <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <p class="text-sm font-semibold text-slate-600">Belum ada mekanik yang ditugaskan</p>
            <p class="text-xs text-slate-500 mt-1">
                @if ($pesanan->status === 'menunggu')
                    Gunakan tombol <strong>Proses Pesanan</strong> di atas untuk menerima pesanan dan menugaskan mekanik.
                @else
                    Pesanan ini tidak memiliki penugasan mekanik (mis. ditolak atau dibatalkan).
                @endif
            </p>
        </div>
    @else
        <ul class="space-y-3">
            @foreach ($pesanan->pengerjaan as $tugas)
                @php
                    $berjalan = $tugas->waktu_selesai === null;
                @endphp
                <li class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#1F3864] text-[#E5A919] flex items-center justify-center font-bold text-sm shrink-0">
                                {{ Str::of($tugas->mekanik?->nama ?? '?')->substr(0, 2)->upper() }}
                            </div>
                            <div>
                                <p class="font-bold text-[#1F3864]">{{ $tugas->mekanik?->nama ?? '—' }}</p>
                                <p class="text-xs text-slate-500">
                                    Spesialis
                                    <span class="font-medium text-slate-700">
                                        {{ ucfirst($tugas->mekanik?->spesialis ?? '—') }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $berjalan ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $berjalan ? 'bg-blue-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                            {{ $berjalan ? 'Sedang Dikerjakan' : 'Pengerjaan Selesai' }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3 pt-3 border-t border-slate-200/70 text-xs">
                        <div>
                            <dt class="font-semibold text-slate-400 uppercase tracking-wide">Mulai</dt>
                            <dd class="text-slate-700 mt-0.5">
                                {{ $tugas->waktu_mulai?->translatedFormat('d M Y, H:i') ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-400 uppercase tracking-wide">Selesai</dt>
                            <dd class="text-slate-700 mt-0.5">
                                {{ $tugas->waktu_selesai?->translatedFormat('d M Y, H:i') ?? 'Belum selesai' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-slate-400 uppercase tracking-wide">Dicatat Oleh</dt>
                            <dd class="text-slate-700 mt-0.5">{{ $tugas->dicatatOleh?->name ?? '—' }}</dd>
                        </div>
                    </dl>

                    @if ($tugas->catatan)
                        <p class="text-xs text-slate-600 mt-3 pt-3 border-t border-slate-200/70 leading-relaxed">
                            <span class="font-semibold text-slate-500">Catatan:</span> {{ $tugas->catatan }}
                        </p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</section>
