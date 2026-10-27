<!-- ================== TABEL MASTER DATA SPAREPART ================== -->
<section id="tabel-sparepart" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden"
    aria-labelledby="judul-tabel-sparepart">
    <header class="p-5 sm:p-6 border-b border-slate-100">
        <h2 id="judul-tabel-sparepart" class="text-lg font-bold text-[#1F3864]">Daftar Sparepart</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Harga dan persediaan yang dipakai saat mencatat pemakaian pada pesanan
        </p>
    </header>

    @if ($sparepart->isEmpty())
        <div class="py-14 px-6 text-center">
            <div
                class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
            </div>
            <p class="text-sm font-bold text-slate-700">Belum ada data sparepart</p>
            <p class="text-xs text-slate-500 mt-1">
                Tambahkan sparepart agar dapat dicatat pemakaiannya pada pesanan servis.
            </p>
            <button type="button"
                class="btn-open-tambah-sparepart inline-flex items-center gap-2 mt-4 px-4 py-2.5 bg-[#E5A919] text-[#1F3864] text-xs sm:text-sm font-bold rounded-xl hover:bg-[#c48e0f] transition-all shadow-md cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Sparepart Pertama</span>
            </button>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="tabel-sparepart w-full text-left border-collapse text-sm">
                <caption class="sr-only">Daftar sparepart beserta harga, stok, dan status ketersediaannya</caption>
                <thead>
                    <tr
                        class="bg-slate-50/75 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                        <th scope="col" class="py-3.5 px-6">Nama Sparepart</th>
                        <th scope="col" class="py-3.5 px-6">Satuan</th>
                        <th scope="col" class="py-3.5 px-6 text-right">Harga</th>
                        <th scope="col" class="py-3.5 px-6 text-center">Stok</th>
                        <th scope="col" class="py-3.5 px-6 text-center">Status</th>
                        <th scope="col" class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($sparepart as $item)
                        @php
                            $habis = $item->stok === 0;
                            $menipis = ! $habis && $item->stok <= $item->stok_minimal;
                            $dipakai = $item->total_pemakaian > 0;

                            $badge = match (true) {
                                $habis => ['bg-rose-50 text-rose-700 border-rose-200', 'bg-rose-500', 'Habis'],
                                $menipis => ['bg-amber-50 text-amber-700 border-amber-200', 'bg-amber-500', 'Menipis'],
                                default => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500', 'Tersedia'],
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-bold text-[#1F3864]">{{ $item->nama }}</span>
                                    @if ($item->stok_minimal > 0)
                                        <span class="text-[11px] text-slate-400">
                                            Batas minimal: {{ $item->stok_minimal }} {{ $item->satuan }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-600">{{ $item->satuan }}</td>
                            <td class="py-4 px-6 text-right font-semibold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format((float) $item->harga, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span
                                    class="text-lg font-extrabold {{ $habis ? 'text-rose-600' : ($menipis ? 'text-amber-600' : 'text-[#1F3864]') }}">
                                    {{ $item->stok }}
                                </span>
                                <span class="text-xs text-slate-500 block">{{ $item->satuan }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span
                                    class="badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge[0] }}"
                                    data-status-sparepart="{{ strtolower($badge[2]) }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge[1] }}"></span>
                                    {{ $badge[2] }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Edit --}}
                                    <button type="button"
                                        class="btn-edit-sparepart inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 text-slate-700 hover:bg-[#1F3864] hover:text-white transition-all cursor-pointer"
                                        title="Edit data sparepart"
                                        data-nama="{{ $item->nama }}"
                                        data-satuan="{{ $item->satuan }}"
                                        data-harga="{{ (float) $item->harga }}"
                                        data-stok="{{ $item->stok }}"
                                        data-stok-minimal="{{ $item->stok_minimal }}"
                                        data-url="{{ route('admin.sparepart.update', $item) }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                        <span>Edit</span>
                                    </button>

                                    {{-- Hapus: dinonaktifkan bila sudah pernah dipakai agar
                                         riwayat nota pelanggan tetap utuh. --}}
                                    @if ($dipakai)
                                        <button type="button" disabled
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed"
                                            title="Tidak dapat dihapus: sudah dipakai pada {{ $item->total_pemakaian }} pesanan. Riwayat nota pelanggan harus tetap utuh.">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    @else
                                        <button type="button"
                                            class="btn-hapus-sparepart inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white transition-all cursor-pointer"
                                            title="Hapus sparepart dari master data"
                                            data-nama="{{ $item->nama }}"
                                            data-url="{{ route('admin.sparepart.destroy', $item) }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
