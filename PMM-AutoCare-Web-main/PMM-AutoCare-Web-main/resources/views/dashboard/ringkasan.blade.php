<!-- ================== A. RINGKASAN ANGKA ================== -->
<section id="ringkasan" aria-labelledby="judul-ringkasan" class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 id="judul-ringkasan" class="text-lg font-bold text-[#1F3864] flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#E5A919]"></span>
            Ringkasan Hari Ini
        </h2>
    </div>

    <ul class="kartu-statistik grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 list-none p-0 m-0">

        <!-- Total Booking -->
        <li
            class="kartu border-blue-500 bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80  hover:shadow-md hover:border-[#1F3864]/30 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Booking Hari
                    Ini</h3>
                <div
                    class="w-10 h-10 rounded-xl bg-[#1F3864]/10 text-[#1F3864] flex items-center justify-center group-hover:bg-[#1F3864] group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                        </path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-3xl font-extrabold text-[#1F3864] tracking-tight">{{ $ringkasan['total_booking_hari_ini'] }}</p>
                <span class="text-xs text-slate-500 mt-1 block">Pesanan terdaftar</span>
            </div>
        </li>

        <!-- Menunggu Antrean -->
        <li
            class="kartu bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-[#E5A919]/50 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Antrean
                    Booking</h3>
                <div
                    class="w-10 h-10 rounded-xl bg-[#E5A919]/15 text-[#543100] flex items-center justify-center group-hover:bg-[#E5A919] group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-3xl font-extrabold text-[#E5A919] tracking-tight">{{ $ringkasan['menunggu_antrean'] }}</p>
                <span class="text-xs text-amber-700 mt-1 font-medium block">Perlu konfirmasi &amp; penugasan</span>
            </div>
        </li>

        <!-- Mekanik Bertugas -->
        <li
            class="kartu bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-[#1F3864]/30 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mekanik Bertugas
                </h3>
                <div
                    class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-3xl font-extrabold text-blue-700 tracking-tight">{{ $ringkasan['sedang_dikerjakan'] }}</p>
                <span class="text-xs text-slate-500 mt-1 block">Sedang mengerjakan servis</span>
            </div>
        </li>

        <!-- Mekanik Tersedia -->
        <li
            class="kartu bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 hover:shadow-md hover:border-emerald-500/30 transition-all duration-200 flex flex-col justify-between group">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mekanik Tersedia
                </h3>
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <div>
                <p class="angka text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $ringkasan['selesai_hari_ini'] }}</p>
                <span class="text-xs text-emerald-700 font-medium mt-1 block">Siap ditugaskan</span>
            </div>
        </li>

    </ul>
</section>
