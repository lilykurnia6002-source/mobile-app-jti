<!-- ============================================================
     HEADER / BAR ATAS
     Brand + pencarian global + info & menu pengguna (Admin)
============================================================ -->
<header id="app-header" class="sticky top-0 z-30 bg-[#1F3864] text-white shadow-md border-b border-[#152747]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            <!-- Brand / Logo Bengkel -->
            <a class="brand flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-[#E5A919] rounded-lg p-1"
                href="/admin/dashboard" aria-label="Kembali ke Dashboard PMM AutoCare">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#E5A919] to-[#c48e0f] flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6 text-[#1F3864]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span
                        class="font-extrabold text-lg leading-tight tracking-tight text-white flex items-center gap-1.5">
                        PMM <span class="text-[#E5A919] font-medium">AutoCare</span>
                    </span>
                    <span class="text-[11px] text-slate-300 font-normal tracking-wide">Sistem Servis Bengkel</span>
                </div>
            </a>

            <!-- Pencarian Global: Pesanan / Pelanggan / Plat Nomor -->
            <form id="pencarian-global" role="search" action="/admin/pesanan" method="get"
                class="flex-1 max-w-md hidden md:block">
                <label class="sr-only" for="cari-pesanan">Cari pesanan, pelanggan, atau plat nomor</label>
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="search" id="cari-pesanan" name="q"
                        placeholder="Cari pesanan / pelanggan / plat nomor…"
                        class="w-full pl-10 pr-4 py-2 bg-white/10 text-white placeholder-slate-300 text-sm rounded-xl border border-white/15 focus:outline-none focus:ring-2 focus:ring-[#E5A919] focus:bg-white focus:text-slate-900 transition-all duration-200">
                </div>
            </form>

            <!-- Menu Pengguna / Profil Admin -->
            <div id="user-menu" class="flex items-center gap-4">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs text-slate-300 font-medium">Selamat Datang,</span>
                    <p class="profile-info text-sm font-semibold text-white">
                        {{ ucfirst(auth()->user()->role) }}
                        <strong class="text-[#E5A919] font-semibold">{{ auth()->user()->name }}</strong>
                    </p>
                </div>

                <div
                    class="w-9 h-9 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center text-sm font-bold text-white shadow-inner">
                    {{ Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
                </div>

                <nav aria-label="Menu akun admin" class="flex items-center gap-2 border-l border-white/20 pl-3">
                    <ul class="flex items-center gap-1.5 text-xs">
                        <li>
                            <a href="{{ route('admin.profil.show') }}"
                                class="px-2.5 py-1.5 rounded-lg text-slate-200 hover:text-white hover:bg-white/10 transition-colors">
                                Profil
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="post" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-2.5 py-1.5 rounded-lg bg-[#E5A919] text-[#1F3864] font-semibold hover:bg-[#c48e0f] transition-colors shadow-sm cursor-pointer">
                                    Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
    </div>
</header>
