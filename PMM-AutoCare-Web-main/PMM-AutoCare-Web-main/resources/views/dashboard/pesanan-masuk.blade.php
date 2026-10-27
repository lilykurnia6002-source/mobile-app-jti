<!-- ================== C. PESANAN MASUK ================== -->
<section id="pesanan-masuk" aria-labelledby="judul-pesanan" class="space-y-4">
    <header class="section-header flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h2 id="judul-pesanan" class="text-lg font-bold text-[#1F3864] flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#1F3864]"></span>
            Pesanan Masuk
            @if ($pesananMasuk->total() > 0)
                <span class="text-xs font-semibold text-slate-500">
                    ({{ $pesananMasuk->total() }} menunggu)
                </span>
            @endif
        </h2>

        <!-- Pindah tab filter status (GET /admin/pesanan?status=...) -->
        <nav class="filter-status" aria-label="Filter status pesanan">
            <ul
                class="flex flex-wrap items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200/80 shadow-sm text-xs font-medium">
                <li>
                    <a href="{{ route('admin.pesanan.index', ['status' => 'menunggu']) }}" aria-current="true"
                        class="px-3 py-1.5 rounded-lg bg-[#1F3864] text-white font-semibold flex items-center gap-1.5 shadow-sm">
                        Menunggu
                        @if ($pesananMasuk->total() > 0)
                            <span
                                class="bg-[#E5A919] text-[#1F3864] font-bold px-1.5 py-0.2 rounded-full text-[10px]">{{ $pesananMasuk->total() }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pesanan.index', ['status' => 'diterima']) }}"
                        class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-[#1F3864] hover:bg-slate-100 transition-colors">
                        Diterima
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pesanan.index', ['status' => 'ditolak']) }}"
                        class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-[#1F3864] hover:bg-slate-100 transition-colors">
                        Ditolak
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pesanan.index', ['status' => 'dikerjakan']) }}"
                        class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-[#1F3864] hover:bg-slate-100 transition-colors">
                        Dikerjakan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pesanan.index', ['status' => 'selesai']) }}"
                        class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-[#1F3864] hover:bg-slate-100 transition-colors">
                        Selesai
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    {{-- Info urutan antrean (FIFO): yang paling lama menunggu tampil lebih dulu --}}
    @if ($pesananMasuk->total() > 0)
        <p class="text-xs text-slate-500 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
            </svg>
            Urutan antrean servis (terlama menunggu lebih dulu).
            Menampilkan <strong>{{ $pesananMasuk->firstItem() }}–{{ $pesananMasuk->lastItem() }}</strong>
            dari <strong>{{ $pesananMasuk->total() }}</strong> pesanan.
        </p>
    @endif

    <ol class="daftar-pesanan space-y-4 list-none p-0 m-0">
        @forelse ($pesananMasuk as $i => $pesanan)
            @php
                // Nomor antrean berurutan lintas halaman (FIFO).
                $noAntrean = $pesananMasuk->firstItem() + $i;
                $mobil = $pesanan->mobil;
                $slot = $pesanan->slot;
                $namaLayanan = $pesanan->detailLayanan->map(fn ($d) => $d->layanan?->nama_layanan)->filter()->implode(', ');
                $waktu = $pesanan->waktu_pesan;
            @endphp
            <li class="item-pesanan">
                <article
                    class="bg-white rounded-2xl shadow-sm border border-slate-200/80 hover:border-slate-300 transition-all p-5 sm:p-6 space-y-5">

                    <header
                        class="pesanan-head flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-100">
                        <div class="flex flex-wrap items-center gap-3">
                            {{-- Nomor antrean FIFO --}}
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#1F3864] text-white text-xs font-bold shrink-0"
                                title="Nomor antrean servis">
                                {{ $noAntrean }}
                            </span>
                            <h3 class="text-base font-bold text-[#1F3864]">
                                <a href="{{ route('admin.pesanan.show', $pesanan) }}"
                                    class="hover:text-[#E5A919] transition-colors inline-flex items-center gap-1.5">
                                    <span>{{ $pesanan->kode_pesanan }}</span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                </a>
                            </h3>
                            <span
                                class="badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200"
                                data-status-pesanan="menunggu">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#E5A919]"></span>
                                Menunggu
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 font-medium">
                            Diajukan
                            <time datetime="{{ $waktu?->toIso8601String() }}" class="text-slate-600 font-semibold">
                                {{ $waktu?->translatedFormat('d M Y, H:i') }}
                            </time>
                            <span class="text-slate-400">({{ $waktu?->diffForHumans() }})</span>
                        </p>
                    </header>

                    <!-- Detail Rincian Pesanan -->
                    <dl
                        class="detail-pesanan grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm bg-slate-50/75 p-4 rounded-xl border border-slate-100">
                        <div>
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan
                            </dt>
                            <dd class="text-slate-800 font-bold mt-0.5">
                                {{ $pesanan->pelanggan?->nama ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Mobil</dt>
                            <dd class="text-slate-800 font-medium mt-0.5">
                                @if ($mobil)
                                    {{ $mobil->merk }} {{ $mobil->tipe }} —
                                    <span class="font-bold text-[#1F3864]">{{ $mobil->plat_nomor }}</span>
                                    ({{ $mobil->tahun }})
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Slot Servis
                            </dt>
                            <dd class="text-slate-800 font-medium mt-0.5">
                                @if ($slot)
                                    <time datetime="{{ $slot->tanggal->format('Y-m-d') }}T{{ substr($slot->jam_mulai, 0, 5) }}"
                                        class="inline-flex items-center gap-1 text-[#1F3864] font-semibold">
                                        {{ $slot->tanggal->translatedFormat('d M Y') }},
                                        {{ substr($slot->jam_mulai, 0, 5) }}–{{ substr($slot->jam_selesai, 0, 5) }}
                                    </time>
                                @else
                                    <span class="text-slate-500 italic">Walk-in (tanpa slot)</span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2 lg:col-span-2">
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Keluhan
                                Pelanggan</dt>
                            <dd class="text-slate-700 mt-0.5 leading-relaxed">{{ $pesanan->keluhan }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Layanan
                                Dipilih</dt>
                            <dd class="text-slate-800 font-medium mt-0.5">
                                {{ $namaLayanan !== '' ? $namaLayanan : '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Estimasi
                                Biaya</dt>
                            <dd class="text-[#1F3864] font-extrabold text-base mt-0.5">
                                Rp {{ number_format((float) $pesanan->total_biaya, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>

                    <div class="w-full pt-2">
                        <button type="button"
                            class="btn-open-modal bg-[#1F3864] hover:bg-[#152747] text-white font-semibold text-xs sm:text-sm rounded-xl transition-all duration-200 shadow-sm hover:shadow inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 cursor-pointer"
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
                    </div>

                </article>
            </li>
        @empty
            <li class="item-pesanan">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-10 text-center">
                    <div
                        class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Tidak ada pesanan yang menunggu</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Semua pesanan sudah diproses. Antrean baru akan muncul di sini secara otomatis.
                    </p>
                    <a href="{{ route('admin.pesanan.index') }}"
                        class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-[#1F3864] hover:text-[#E5A919] transition-colors">
                        Lihat seluruh riwayat pesanan
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </li>
        @endforelse
    </ol>

    {{-- Navigasi antrean (FIFO) — memakai parameter `antrean` agar tidak
         bentrok dengan filter status/q pada halaman daftar pesanan. --}}
    @if ($pesananMasuk->hasPages())
        <nav class="pagination-antrean flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2"
            aria-label="Navigasi antrean pesanan">
            <p class="text-xs text-slate-500">
                Halaman <strong>{{ $pesananMasuk->currentPage() }}</strong>
                dari <strong>{{ $pesananMasuk->lastPage() }}</strong>
                &middot; antrean #{{ $pesananMasuk->firstItem() }}–#{{ $pesananMasuk->lastItem() }}
                (FIFO: terlama menunggu lebih dulu)
            </p>

            <div class="flex items-center gap-2">
                {{-- Halaman sebelumnya --}}
                @if ($pesananMasuk->onFirstPage())
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed"
                        aria-disabled="true">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $pesananMasuk->previousPageUrl() }}" rel="prev"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-white text-[#1F3864] border border-slate-200 hover:bg-[#1F3864] hover:text-white transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Sebelumnya
                    </a>
                @endif

                {{-- Nomor halaman --}}
                @foreach ($pesananMasuk->getUrlRange(max(1, $pesananMasuk->currentPage() - 2), min($pesananMasuk->lastPage(), $pesananMasuk->currentPage() + 2)) as $halaman => $url)
                    @if ($halaman === $pesananMasuk->currentPage())
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

                {{-- Halaman berikutnya --}}
                @if ($pesananMasuk->hasMorePages())
                    <a href="{{ $pesananMasuk->nextPageUrl() }}" rel="next"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-white text-[#1F3864] border border-slate-200 hover:bg-[#1F3864] hover:text-white transition-all">
                        Berikutnya
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed"
                        aria-disabled="true">
                        Berikutnya
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </span>
                @endif
            </div>
        </nav>
    @endif
</section>
