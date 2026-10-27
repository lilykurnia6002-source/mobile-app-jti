<!-- ================== RINGKASAN SLOT JADWAL ================== -->
<section id="ringkasan-slot" class="space-y-4">
    <ul class="kartu-statistik grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 list-none p-0 m-0">

        <!-- Total Slot Dibuka -->
        <li class="kartu bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-[#1F3864]/30 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Slot Dibuka</h3>
                <div class="w-10 h-10 rounded-xl bg-[#1F3864]/10 text-[#1F3864] flex items-center justify-center group-hover:bg-[#1F3864] group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-2xl font-extrabold text-[#1F3864] tracking-tight">{{ $ringkasanSlot['total_slot'] }} Slot</p>
                <span class="text-xs text-slate-500 mt-1 block">Periode {{ $rentang['label'] }}</span>
            </div>
        </li>

        <!-- Total Kapasitas Kuota -->
        <li class="kartu bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-emerald-500/30 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kuota Kapasitas</h3>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-2xl font-extrabold text-emerald-600 tracking-tight">{{ $ringkasanSlot['total_kuota'] }} Mobil</p>
                <span class="text-xs text-emerald-700 font-medium mt-1 block">{{ $ringkasanSlot['total_sisa'] }} sisa kuota tersedia</span>
            </div>
        </li>

        <!-- Slot Terisi -->
        <li class="kartu bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-[#E5A919]/50 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kapasitas Terisi</h3>
                <div class="w-10 h-10 rounded-xl bg-[#E5A919]/15 text-[#543100] flex items-center justify-center group-hover:bg-[#E5A919] group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-2xl font-extrabold text-[#E5A919] tracking-tight">{{ $ringkasanSlot['total_terisi'] }} Mobil</p>
                <span class="text-xs text-amber-700 font-medium mt-1 block">Telah dibooking pelanggan</span>
            </div>
        </li>

        <!-- Slot Non-Aktif / Ditutup -->
        <li class="kartu bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-slate-300 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Slot Ditutup</h3>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-slate-700 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-2xl font-extrabold text-slate-700 tracking-tight">{{ $ringkasanSlot['slot_ditutup'] }} Slot</p>
                <span class="text-xs text-slate-500 mt-1 block">Non-aktif sementara</span>
            </div>
        </li>

    </ul>
</section>
