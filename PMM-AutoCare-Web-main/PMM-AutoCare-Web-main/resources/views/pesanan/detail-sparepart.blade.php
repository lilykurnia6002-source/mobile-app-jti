@php
    $totalPemakaian = (float) $pesanan->detailSparepart->sum('subtotal');
@endphp

<!-- ================== PEMAKAIAN SPAREPART PADA PESANAN ================== -->
<section class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4"
    aria-labelledby="judul-sparepart-pesanan">
    <header class="border-b border-slate-100 pb-4">
        <h2 id="judul-sparepart-pesanan" class="text-lg font-bold text-[#1F3864]">Pemakaian Sparepart</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Sparepart yang dipakai mekanik. Mencatat pemakaian akan mengurangi stok persediaan.
        </p>
    </header>

    {{-- Daftar sparepart yang sudah dipakai --}}
    @if ($pesanan->detailSparepart->isEmpty())
        <p class="text-sm text-slate-500 italic py-4 text-center">
            Belum ada sparepart yang dicatat untuk pesanan ini.
        </p>
    @else
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-sm border-collapse">
                <caption class="sr-only">Sparepart yang dipakai pada pesanan ini</caption>
                <thead>
                    <tr class="bg-slate-50/75 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th scope="col" class="py-3 px-4">Nama Sparepart</th>
                        <th scope="col" class="py-3 px-4">Satuan</th>
                        <th scope="col" class="py-3 px-4 text-center">Jumlah</th>
                        <th scope="col" class="py-3 px-4 text-right">Harga</th>
                        <th scope="col" class="py-3 px-4 text-right">Subtotal</th>
                        @if ($bolehUbahSparepart)
                            <th scope="col" class="py-3 px-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($pesanan->detailSparepart as $detail)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 font-semibold text-[#1F3864]">
                                {{ $detail->sparepart?->nama ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-xs">{{ $detail->sparepart?->satuan }}</td>
                            <td class="py-3 px-4 text-center text-slate-700">{{ $detail->jumlah }}</td>
                            <td class="py-3 px-4 text-right text-slate-700 whitespace-nowrap">
                                Rp {{ number_format((float) $detail->harga, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                            </td>
                            @if ($bolehUbahSparepart)
                                <td class="py-3 px-4 text-right">
                                    <button type="button"
                                        class="btn-hapus-pemakaian inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white transition-all cursor-pointer"
                                        title="Batalkan pemakaian ini. Stok akan dikembalikan."
                                        data-nama="{{ $detail->sparepart?->nama }}"
                                        data-jumlah="{{ $detail->jumlah }}"
                                        data-satuan="{{ $detail->sparepart?->satuan }}"
                                        data-url="{{ route('admin.pesanan.sparepart.destroy', [$pesanan, $detail->id_detail]) }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        <span>Batal</span>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50/75 border-t border-slate-200">
                        <td colspan="{{ $bolehUbahSparepart ? 5 : 4 }}"
                            class="py-3 px-4 text-right text-xs font-bold text-slate-500 uppercase">
                            Total Sparepart
                        </td>
                        <td class="py-3 px-4 text-right font-extrabold text-[#1F3864] whitespace-nowrap">
                            Rp {{ number_format($totalPemakaian, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    {{-- Form catat pemakaian, atau keterangan bila status tidak mengizinkan --}}
    @if ($bolehUbahSparepart)
        @if ($daftarSparepart->isEmpty())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs text-amber-900 leading-relaxed">
                    <strong>Tidak ada sparepart dengan stok tersedia.</strong>
                    Tambahkan stok terlebih dahulu di menu
                    <a href="{{ route('admin.sparepart.index') }}"
                        class="font-semibold underline hover:text-amber-700">Master Data Sparepart</a>
                    agar pemakaian dapat dicatat.
                </p>
            </div>
        @else
            <form id="form-catat-sparepart" action="{{ route('admin.pesanan.sparepart.store', $pesanan) }}"
                method="post" class="bg-slate-50/75 border border-slate-200 rounded-xl p-4 space-y-3">
                @csrf
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1F3864]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Catat Pemakaian Sparepart</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label for="catat-id-sparepart"
                            class="block text-xs font-semibold text-slate-600 mb-1.5">Sparepart</label>
                        <select id="catat-id-sparepart" name="id_sparepart" required
                            class="w-full text-xs sm:text-sm bg-white border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] font-semibold shadow-sm">
                            <option value="">— Pilih Sparepart —</option>
                            @foreach ($daftarSparepart as $item)
                                <option value="{{ $item->id_sparepart }}">
                                    {{ $item->nama }} — Rp
                                    {{ number_format((float) $item->harga, 0, ',', '.') }}/{{ $item->satuan }}
                                    (stok {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="catat-jumlah"
                            class="block text-xs font-semibold text-slate-600 mb-1.5">Jumlah</label>
                        <input type="number" id="catat-jumlah" name="jumlah" required min="1" value="1"
                            class="w-full text-xs sm:text-sm bg-white border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] font-semibold shadow-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 pt-1">
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Stok berkurang otomatis dan total biaya pesanan diperbarui.
                    </p>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-[#1F3864] hover:bg-[#152747] text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg inline-flex items-center gap-2 cursor-pointer shrink-0">
                        <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Catat Pemakaian
                    </button>
                </div>
            </form>
        @endif
    @else
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs text-slate-600 leading-relaxed">
                <strong class="text-slate-700">Rincian sparepart tidak dapat diubah</strong> karena pesanan
                berstatus <span class="font-semibold">{{ $pesanan->status }}</span>.
                @if ($pesanan->status === 'menunggu')
                    Terima pesanan terlebih dahulu untuk mulai mencatat pemakaian sparepart.
                @else
                    Nota pesanan ini sudah final.
                @endif
            </p>
        </div>
    @endif
</section>
