<!-- ================== TABEL MANAJEMEN SLOT JADWAL ================== -->
<section id="tabel-kelola-slot" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden space-y-4">
    <header class="section-header p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-[#1F3864]">Daftar Slot Jadwal Servis</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar kuota dan ketersediaan slot per hari operasional bengkel</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium">Catatan:</span>
            <span class="text-xs text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 font-mono">
                Menutup slot ≠ menghapus data
            </span>
        </div>
    </header>

    <div class="overflow-x-auto">
        <table class="tabel-slot w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-slate-50/75 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <th scope="col" class="py-3.5 px-6">ID / Tanggal</th>
                    <th scope="col" class="py-3.5 px-6">Jam Operasional</th>
                    <th scope="col" class="py-3.5 px-6">Kuota</th>
                    <th scope="col" class="py-3.5 px-6">Booking</th>
                    <th scope="col" class="py-3.5 px-6">Kuota</th>
                    <th scope="col" class="py-3.5 px-6">Status</th>
                    <th scope="col" class="py-3.5 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse ($slot as $s)
                    @php
                        $sisa = $s->sisa_kuota;
                        $ditutup = $s->status === 'ditutup';
                        $penuh = $sisa === 0;
                        // Penuh = kondisi terisi, bukan status tersimpan.
                        $label = $ditutup ? 'Ditutup' : ($penuh ? 'Penuh' : 'Dibuka');
                        $badge = match (true) {
                            $ditutup => 'bg-slate-100 text-slate-600 border-slate-200',
                            $penuh => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        };
                        $dot = match (true) {
                            $ditutup => 'bg-slate-400',
                            $penuh => 'bg-amber-500',
                            default => 'bg-emerald-500 animate-pulse',
                        };
                        $sisaBadge = match (true) {
                            $sisa === 0 => 'bg-rose-50 text-rose-700 border-rose-200',
                            $sisa === 1 => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-blue-50 text-[#1F3864] border-blue-200',
                        };
                        // Slot dengan pesanan aktif tidak boleh dihapus — datanya
                        // dipakai riwayat pesanan. Tutup saja lewat modal edit.
                        $punyaPesananAktif = $s->pesanan()
                            ->whereIn('status', [
                                \App\Models\Pesanan::STATUS_MENUNGGU,
                                \App\Models\Pesanan::STATUS_DITERIMA,
                                \App\Models\Pesanan::STATUS_DIKERJAKAN,
                            ])
                            ->exists();
                    @endphp
                    <tr class="{{ $ditutup ? 'bg-slate-50/80 hover:bg-slate-100/70 opacity-75' : 'hover:bg-slate-50/60' }} transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-400 font-mono">SLOT-{{ $s->tanggal->format('Ymd') }}-{{ str_pad($s->id_slot, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="font-bold text-[#1F3864]">{{ $s->tanggal->translatedFormat('l, d M Y') }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-slate-700">
                            <div class="inline-flex items-center gap-1.5 font-semibold text-[#1F3864]">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <time datetime="{{ substr($s->jam_mulai, 0, 5) }}">{{ substr($s->jam_mulai, 0, 5) }}</time>
                                &ndash;
                                <time datetime="{{ substr($s->jam_selesai, 0, 5) }}">{{ substr($s->jam_selesai, 0, 5) }}</time>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">{{ $s->kapasitas }}Mobil</td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">{{ $s->terisi }}Mobil</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $sisaBadge }}">
                                {{ $sisa }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badge }}">
                                <span class="w-2 h-2 rounded-full {{ $dot }}"></span>
                                {{ $label }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <button type="button"
                                    class="btn-edit-slot inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-[#1F3864] hover:text-white transition-all shadow-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#1F3864]"
                                    title="Edit Kuota Slot" aria-label="Edit Kuota Slot"
                                    data-id="{{ $s->id_slot }}"
                                    data-tanggal="{{ $s->tanggal->format('Y-m-d') }}"
                                    data-jam-mulai="{{ substr($s->jam_mulai, 0, 5) }}"
                                    data-jam-selesai="{{ substr($s->jam_selesai, 0, 5) }}"
                                    data-kuota="{{ $s->kapasitas }}"
                                    data-terisi="{{ $s->terisi }}"
                                    data-status="{{ $ditutup ? 'ditutup' : 'dibuka' }}"
                                    data-url="{{ route('admin.slot.update', $s) }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </button>
                                {{-- Hapus jadwal. Untuk membuka kembali slot yang ditutup,
                                     gunakan tombol Edit (kolom status di modal). --}}
                                @if ($punyaPesananAktif)
                                    <button type="button" disabled
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed"
                                        title="Tidak dapat dihapus: slot ini masih memiliki pesanan aktif. Tutup saja slotnya agar tidak menerima booking baru.">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                @else
                                    <button type="button"
                                        class="btn-hapus-slot inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white transition-all shadow-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500"
                                        title="Hapus slot dari jadwal" aria-label="Hapus slot"
                                        data-tanggal="{{ $s->tanggal->format('d/m/Y') }}"
                                        data-jam="{{ substr($s->jam_mulai, 0, 5) }}–{{ substr($s->jam_selesai, 0, 5) }}"
                                        data-terisi="{{ $s->terisi }}"
                                        data-url="{{ route('admin.slot.destroy', $s) }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                @empty
                    <tr>
                        <td colspan="7" class="py-12 px-6 text-center">
                            <p class="text-sm font-semibold text-slate-600">Belum ada slot pada periode ini</p>
                            <p class="text-xs text-slate-500 mt-1">
                                Buat slot baru agar pelanggan dapat memesan servis dari aplikasi mobile.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
