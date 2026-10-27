<!-- ============================================================
     NAVIGASI SAMPING (SIDEBAR) — MANAJEMEN SLOT
============================================================ -->
<aside id="sidebar" aria-label="Navigasi panel admin" class="w-full lg:w-64 shrink-0">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 sticky top-24">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">
            Menu Utama
        </div>
        <nav>
            <ul class="space-y-1">
                <li>
                    <a href="/admin/dashboard"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-600 hover:text-[#1F3864] hover:bg-slate-100/80 transition-all duration-150">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/slot" aria-current="page"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-150 bg-[#1F3864] text-white shadow-md shadow-[#1F3864]/20">
                        <svg class="w-5 h-5 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>Jadwal Slot</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/pesanan?status=menunggu"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-600 hover:text-[#1F3864] hover:bg-slate-100/80 transition-all duration-150">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            <span>Pesanan Masuk</span>
                        </div>
                        <span class="bg-[#E5A919] text-[#1F3864] font-bold text-xs px-2 py-0.5 rounded-full shadow-sm">5</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/mekanik"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-sm text-slate-600 hover:text-[#1F3864] hover:bg-slate-100/80 transition-all duration-150">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span>Mekanik</span>
                    </a>
                </li>
            </ul>
        </nav>

        <hr class="my-4 border-slate-100">

        <div class="bg-gradient-to-br from-[#1F3864]/5 to-[#E5A919]/10 rounded-xl p-3.5 border border-[#1F3864]/10">
            <div class="flex items-center gap-2 mb-1.5">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-xs font-semibold text-[#1F3864]">Status Slot System</span>
            </div>
            <p class="text-[11px] text-slate-600 leading-relaxed">
                Slot jadwal aktif menjadi syarat pemesanan servis dari Aplikasi Mobile Pelanggan.
            </p>
        </div>
    </div>
</aside>
