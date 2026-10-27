<!-- ================== B. JADWAL BOOKING HARI INI ================== -->
<section id="jadwal-booking" aria-labelledby="judul-jadwal"
    class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
    <header class="section-header p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 id="judul-jadwal" class="text-lg font-bold text-[#1F3864]">Jadwal Booking Hari Ini</h2>
            <p class="text-xs text-slate-500 mt-0.5">Sisa kuota slot servis per jam operasional</p>
        </div>
        <!-- prasyarat: slot dibuka admin; tutup ≠ hapus -->
        <a href="/admin/slot?dari=2026-09-16&sampai=2026-09-22"
            class="text-xs font-semibold text-[#1F3864] hover:text-[#E5A919] inline-flex items-center gap-1 transition-colors">
            Kelola Jadwal
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                </path>
            </svg>
        </a>
    </header>

    <div class="overflow-x-auto">
        <!-- Slot = berasal dari tabel tb_slot_jadwal (dibuka oleh Admin) -->
        <table class="tabel-jadwal w-full text-left border-collapse text-sm">
            <caption class="sr-only">Sisa kuota tiap slot pada hari ini</caption>
            <thead>
                <tr
                    class="bg-slate-50/75 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <th scope="col" class="py-3.5 px-6">Jam Slot</th>
                    <th scope="col" class="py-3.5 px-6">Kapasitas</th>
                    <th scope="col" class="py-3.5 px-6">Terisi</th>
                    <th scope="col" class="py-3.5 px-6">Sisa Kuota</th>
                    <th scope="col" class="py-3.5 px-6 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($slotHariIni as $slot)
                    @php
                        $sisa = $slot->sisa_kuota;
                        $penuh = $sisa === 0;
                        $ditutup = $slot->status === 'ditutup';
                        // "Penuh" adalah kondisi terisi, bukan status tersimpan.
                        $label = $ditutup ? 'Ditutup' : ($penuh ? 'Penuh' : 'Dibuka');
                        $badge = match (true) {
                            $ditutup => 'bg-slate-100 text-slate-600 border-slate-200',
                            $penuh => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        };
                        $dot = match (true) {
                            $ditutup => 'bg-slate-400',
                            $penuh => 'bg-rose-500',
                            default => 'bg-emerald-500',
                        };
                        $sisaBadge = match (true) {
                            $sisa === 0 => 'bg-rose-50 text-rose-700',
                            $sisa === 1 => 'bg-amber-50 text-amber-700',
                            default => 'bg-emerald-50 text-emerald-700',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 font-semibold text-[#1F3864]">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <time
                                    datetime="{{ $slot->jam_mulai }}">{{ substr($slot->jam_mulai, 0, 5) }}</time>–<time
                                    datetime="{{ $slot->jam_selesai }}">{{ substr($slot->jam_selesai, 0, 5) }}</time>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-slate-700 font-medium">{{ $slot->kapasitas }} Mobil</td>
                        <td class="py-4 px-6 text-slate-700 font-medium">{{ $slot->terisi }} Mobil</td>
                        <td class="py-4 px-6">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $sisaBadge }}">
                                {{ $sisa }} Sisa
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span
                                class="badge inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge }}"
                                data-status-slot="{{ $label }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                {{ $label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 px-6 text-center">
                            <p class="text-sm font-semibold text-slate-600">Belum ada slot untuk hari ini</p>
                            <p class="text-xs text-slate-500 mt-1">
                                Buat slot terlebih dahulu agar pelanggan dapat memesan servis dari aplikasi mobile.
                            </p>
                            <a href="{{ route('admin.slot.index') }}"
                                class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-[#1F3864] hover:text-[#E5A919] transition-colors">
                                Kelola Slot Jadwal
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>        </table>
    </div>
</section>
