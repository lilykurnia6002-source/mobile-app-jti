<!-- ================== TABEL DAFTAR PESANAN ================== -->
<section id="tabel-pesanan" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden"
    aria-labelledby="judul-tabel-pesanan">
    <header class="section-header p-5 sm:p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 id="judul-tabel-pesanan" class="text-lg font-bold text-[#1F3864]">Daftar Pesanan</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                @if ($pencarian)
                    Hasil pencarian "<strong>{{ $pencarian }}</strong>" —
                @endif
                {{ $pesanan->total() }} pesanan ditemukan, terbaru lebih dulu.
            </p>
        </div>
    </header>

    <div class="overflow-x-auto">
        <table class="tabel-pesanan w-full text-left border-collapse text-sm">
            <caption class="sr-only">Daftar pesanan servis beserta status dan aksi yang tersedia</caption>
            <thead>
                <tr
                    class="bg-slate-50/75 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <th scope="col" class="py-3.5 px-6">Kode &amp; Waktu</th>
                    <th scope="col" class="py-3.5 px-6">Pelanggan &amp; Mobil</th>
                    <th scope="col" class="py-3.5 px-6">Slot Servis</th>
                    <th scope="col" class="py-3.5 px-6">Total</th>
                    <th scope="col" class="py-3.5 px-6">Status</th>
                    <th scope="col" class="py-3.5 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pesanan as $p)
                    @php
                        $mobil = $p->mobil;
                        $slot = $p->slot;
                        // Warna badge per status; 'dibatalkan' di luar alur konsep awal.
                        $badge = match ($p->status) {
                            'menunggu' => ['bg-amber-50 text-amber-800 border-amber-200', 'bg-[#E5A919]'],
                            'diterima' => ['bg-blue-50 text-blue-700 border-blue-200', 'bg-blue-500'],
                            'dikerjakan' => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'bg-indigo-500'],
                            'selesai' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500'],
                            'ditolak' => ['bg-rose-50 text-rose-700 border-rose-200', 'bg-rose-500'],
                            default => ['bg-slate-100 text-slate-600 border-slate-200', 'bg-slate-400'],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        {{-- Kode & waktu --}}
                        <td class="py-4 px-6">
                            <div class="flex flex-col gap-0.5">
                                <a href="{{ route('admin.pesanan.show', $p) }}"
                                    class="font-bold text-[#1F3864] hover:text-[#E5A919] transition-colors">
                                    {{ $p->kode_pesanan }}
                                </a>
                                <span class="text-xs text-slate-500">
                                    {{ $p->waktu_pesan?->translatedFormat('d M Y, H:i') }}
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    {{ $p->waktu_pesan?->diffForHumans() }}
                                </span>
                            </div>
                        </td>

                        {{-- Pelanggan & mobil --}}
                        <td class="py-4 px-6">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-semibold text-slate-800">
                                    {{ $p->pelanggan?->nama ?? '—' }}
                                </span>
                                @if ($mobil)
                                    <span class="text-xs text-slate-500">
                                        {{ $mobil->merk }} {{ $mobil->tipe }} —
                                        <span class="font-semibold text-[#1F3864]">{{ $mobil->plat_nomor }}</span>
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Mobil tidak tersedia</span>
                                @endif
                            </div>
                        </td>

                        {{-- Slot servis --}}
                        <td class="py-4 px-6">
                            @if ($slot)
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-medium text-slate-800">
                                        {{ $slot->tanggal->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-mono">
                                        {{ substr($slot->jam_mulai, 0, 5) }}–{{ substr($slot->jam_selesai, 0, 5) }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-slate-500 italic">Walk-in</span>
                            @endif
                        </td>

                        {{-- Total biaya --}}
                        <td class="py-4 px-6 font-bold text-[#1F3864] whitespace-nowrap">
                            Rp {{ number_format((float) $p->total_biaya, 0, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td class="py-4 px-6">
                            <span
                                class="badge inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge[0] }}"
                                data-status-pesanan="{{ $p->status }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge[1] }}"></span>
                                {{ ucfirst($p->status) }}
                            </span>
                            @if ($p->status === 'ditolak' && $p->alasan_tolak)
                                <p class="text-[11px] text-slate-500 mt-1 max-w-48 leading-snug"
                                    title="{{ $p->alasan_tolak }}">
                                    {{ Str::limit($p->alasan_tolak, 60) }}
                                </p>
                            @endif
                        </td>

                        {{-- Aksi sesuai alur status --}}
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Detail selalu tersedia --}}
                                <a href="{{ route('admin.pesanan.show', $p) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 text-slate-700 hover:bg-[#1F3864] hover:text-white transition-all"
                                    title="Lihat detail pesanan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <span>Detail</span>
                                </a>

                                {{-- Menunggu → Terima / Tolak --}}
                                @if ($p->status === 'menunggu')
                                    <button type="button"
                                        class="btn-open-modal inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-[#1F3864] text-white hover:bg-[#152747] transition-all cursor-pointer"
                                        title="Proses pesanan (terima / tolak)"
                                        data-id="{{ $p->id_pesanan }}"
                                        data-kode="{{ $p->kode_pesanan }}"
                                        data-pelanggan="{{ $p->pelanggan?->nama }}"
                                        data-mobil="{{ $mobil ? trim($mobil->merk.' '.$mobil->tipe).' ('.$mobil->plat_nomor.')' : '' }}"
                                        data-terima-url="{{ route('admin.pesanan.terima', $p) }}"
                                        data-tolak-url="{{ route('admin.pesanan.tolak', $p) }}">
                                        <svg class="w-3.5 h-3.5 text-[#E5A919]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                            </path>
                                        </svg>
                                        <span>Proses</span>
                                    </button>
                                @endif

                                {{-- Diterima → Mulai Kerjakan (mekanik mulai) --}}
                                @if ($p->status === 'diterima')
                                    <button type="button"
                                        class="btn-kerjakan-pesanan inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all cursor-pointer"
                                        title="Tandai mekanik mulai mengerjakan kendaraan"
                                        data-url="{{ route('admin.pesanan.kerjakan', $p) }}"
                                        data-kode="{{ $p->kode_pesanan }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Kerjakan</span>
                                    </button>
                                @endif

                                {{-- Dikerjakan → Selesaikan (pengerjaan tuntas) --}}
                                @if ($p->status === 'dikerjakan')
                                    <button type="button"
                                        class="btn-selesai-pesanan inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all cursor-pointer"
                                        title="Tandai pengerjaan selesai dan kendaraan siap diambil"
                                        data-url="{{ route('admin.pesanan.selesai', $p) }}"
                                        data-kode="{{ $p->kode_pesanan }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Selesaikan</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-14 px-6 text-center">
                            <div
                                class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">
                                @if ($pencarian)
                                    Tidak ada pesanan yang cocok
                                @elseif ($status)
                                    Tidak ada pesanan berstatus "{{ ucfirst($status) }}"
                                @else
                                    Belum ada pesanan
                                @endif
                            </p>
                            <p class="text-xs text-slate-500 mt-1">
                                @if ($pencarian)
                                    Coba kata kunci lain, atau lihat seluruh pesanan.
                                @elseif ($status)
                                    Pesanan akan muncul di sini setelah pelanggan memesan dari aplikasi mobile.
                                @else
                                    Pesanan dari aplikasi mobile akan tampil di sini.
                                @endif
                            </p>
                            @if ($pencarian || $status)
                                <a href="{{ route('admin.pesanan.index') }}"
                                    class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-[#1F3864] hover:text-[#E5A919] transition-colors">
                                    Lihat semua pesanan
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (memakai parameter `page`, terpisah dari `antrean` dashboard) --}}
    @if ($pesanan->hasPages())
        <nav class="pagination-pesanan flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 border-t border-slate-100"
            aria-label="Navigasi halaman pesanan">
            <p class="text-xs text-slate-500">
                Menampilkan <strong>{{ $pesanan->firstItem() }}</strong>–<strong>{{ $pesanan->lastItem() }}</strong>
                dari <strong>{{ $pesanan->total() }}</strong> pesanan
            </p>

            <div class="flex items-center gap-2">
                @if ($pesanan->onFirstPage())
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed"
                        aria-disabled="true">Sebelumnya</span>
                @else
                    <a href="{{ $pesanan->previousPageUrl() }}" rel="prev"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-white text-[#1F3864] border border-slate-200 hover:bg-[#1F3864] hover:text-white transition-all">
                        Sebelumnya
                    </a>
                @endif

                @foreach ($pesanan->getUrlRange(max(1, $pesanan->currentPage() - 2), min($pesanan->lastPage(), $pesanan->currentPage() + 2)) as $halaman => $url)
                    @if ($halaman === $pesanan->currentPage())
                        <span aria-current="page"
                            class="inline-flex items-center justify-center min-w-9 px-2.5 py-2 text-xs font-bold rounded-xl bg-[#1F3864] text-white shadow-sm">
                            {{ $halaman }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                            class="inline-flex items-center justify-center min-w-9 px-2.5 py-2 text-xs font-semibold rounded-xl bg-white text-slate-600 border border-slate-200 hover:bg-slate-100 transition-colors">
                            {{ $halaman }}
                        </a>
                    @endif
                @endforeach

                @if ($pesanan->hasMorePages())
                    <a href="{{ $pesanan->nextPageUrl() }}" rel="next"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-white text-[#1F3864] border border-slate-200 hover:bg-[#1F3864] hover:text-white transition-all">
                        Berikutnya
                    </a>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed"
                        aria-disabled="true">Berikutnya</span>
                @endif
            </div>
        </nav>
    @endif
</section>
