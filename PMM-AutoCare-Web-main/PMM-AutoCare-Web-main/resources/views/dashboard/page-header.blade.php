<!-- Header Halaman -->
<header
    class="page-header bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1F3864] tracking-tight">Dashboard Admin</h1>
        <p class="text-slate-500 text-sm mt-1 flex items-center gap-2">
            <span>Ringkasan aktivitas operasional bengkel</span>
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-slate-300"></span>
            <time datetime="2026-09-16"
                class="font-semibold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-md text-xs">
                Rabu, 16 September 2026
            </time>
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="/admin/slot?dari=2026-09-16&sampai=2026-09-22"
            class="inline-flex items-center gap-2 px-4 py-2 bg-[#1F3864] text-white text-xs sm:text-sm font-semibold rounded-xl hover:bg-[#152747] transition-all shadow-sm">
            <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                </path>
            </svg>
            <span>Atur Slot Hari Ini</span>
        </a>
    </div>
</header>
