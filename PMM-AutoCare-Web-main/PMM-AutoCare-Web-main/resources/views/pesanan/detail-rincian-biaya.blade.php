@php
    $subtotalJasa = (float) $pesanan->detailLayanan->sum('subtotal');
    $subtotalSparepart = (float) $pesanan->detailSparepart->sum('subtotal');
@endphp

<!-- ================== RINCIAN BIAYA (JASA & SPAREPART) ================== -->
<section class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden"
    aria-labelledby="judul-rincian-biaya">
    <header class="section-header p-5 sm:p-6 border-b border-slate-100">
        <h2 id="judul-rincian-biaya" class="text-lg font-bold text-[#1F3864]">Rincian Biaya Servis</h2>
        <p class="text-xs text-slate-500 mt-0.5">Layanan yang dipilih pelanggan dan sparepart yang dipakai</p>
    </header>

    {{-- Layanan / Jasa --}}
    <div class="p-5 sm:p-6 space-y-3">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Layanan (Jasa)</h3>

        @if ($pesanan->detailLayanan->isEmpty())
            <p class="text-sm text-slate-500 italic">Belum ada layanan yang tercatat.</p>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-sm border-collapse">
                    <caption class="sr-only">Rincian layanan jasa pada pesanan ini</caption>
                    <thead>
                        <tr class="bg-slate-50/75 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th scope="col" class="py-3 px-4">Nama Layanan</th>
                            <th scope="col" class="py-3 px-4 text-center">Jumlah</th>
                            <th scope="col" class="py-3 px-4 text-right">Harga Jasa</th>
                            <th scope="col" class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pesanan->detailLayanan as $detail)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4 font-semibold text-[#1F3864]">
                                    {{ $detail->layanan?->nama_layanan ?? '—' }}
                                </td>
                                <td class="py-3 px-4 text-center text-slate-700">{{ $detail->jumlah }}×</td>
                                <td class="py-3 px-4 text-right text-slate-700">
                                    Rp {{ number_format((float) $detail->harga_jasa, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-800">
                                    Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50/75 border-t border-slate-200">
                            <td colspan="3" class="py-3 px-4 text-right text-xs font-bold text-slate-500 uppercase">
                                Subtotal Jasa
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-[#1F3864]">
                                Rp {{ number_format($subtotalJasa, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Sparepart --}}
    <div class="p-5 sm:p-6 pt-0 space-y-3">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sparepart</h3>

        @if ($pesanan->detailSparepart->isEmpty())
            <p class="text-sm text-slate-500 italic">
                Tidak ada sparepart yang dicatat pada pesanan ini.
            </p>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-sm border-collapse">
                    <caption class="sr-only">Rincian sparepart yang dipakai</caption>
                    <thead>
                        <tr class="bg-slate-50/75 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th scope="col" class="py-3 px-4">Nama Sparepart</th>
                            <th scope="col" class="py-3 px-4">Satuan</th>
                            <th scope="col" class="py-3 px-4 text-center">Jumlah</th>
                            <th scope="col" class="py-3 px-4 text-right">Harga</th>
                            <th scope="col" class="py-3 px-4 text-right">Subtotal</th>
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
                                <td class="py-3 px-4 text-right text-slate-700">
                                    Rp {{ number_format((float) $detail->harga, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-slate-800">
                                    Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50/75 border-t border-slate-200">
                            <td colspan="4" class="py-3 px-4 text-right text-xs font-bold text-slate-500 uppercase">
                                Subtotal Sparepart
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-[#1F3864]">
                                Rp {{ number_format($subtotalSparepart, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- Total akhir — dibaca dari kolom pesanan, tidak dihitung ulang --}}
    <div class="px-5 sm:px-6 py-5 bg-[#1F3864]/5 border-t border-[#1F3864]/10 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Biaya Servis</p>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Nilai final tersimpan pada pesanan (dijaga konsisten oleh server).
            </p>
        </div>
        <p class="text-2xl sm:text-3xl font-extrabold text-[#1F3864] tracking-tight">
            Rp {{ number_format((float) $pesanan->total_biaya, 0, ',', '.') }}
        </p>
    </div>

    {{-- Status pembayaran: modul pembayaran belum dibangun --}}
    <div class="px-5 sm:px-6 py-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Status Pembayaran</p>
        @if ($pesanan->pembayaran)
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border bg-slate-100 text-slate-600 border-slate-200">
                {{ ucfirst(str_replace('_', ' ', $pesanan->pembayaran->status_bayar)) }}
                · Rp {{ number_format((float) $pesanan->pembayaran->jumlah, 0, ',', '.') }}
            </span>
        @else
            <span class="text-xs text-slate-400 italic">
                Belum ada data pembayaran (modul pembayaran belum aktif)
            </span>
        @endif
    </div>
</section>
