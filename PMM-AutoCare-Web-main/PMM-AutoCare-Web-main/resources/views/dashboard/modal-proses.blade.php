<!-- ================== POPUP / MODAL TERIMA & TOLAK PESANAN ================== -->
<div id="modal-proses-pesanan" class="fixed inset-0 m-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200 hidden opacity-0" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Outer Modal Card -->
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-200 scale-95" id="modal-card">
        
        <!-- Modal Header -->
        <div class="bg-[#1F3864] text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#E5A919] text-[#1F3864] flex items-center justify-center font-bold shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-title" class="font-bold text-base text-white tracking-tight">Proses Pesanan</h3>
                    <p class="text-xs text-slate-300 font-normal">
                        <span id="modal-pesanan-kode" class="font-semibold text-[#E5A919]">PMM-20260916-001</span> — <span id="modal-pesanan-pelanggan">Budi Santoso</span>
                    </p>
                </div>
            </div>
            <button type="button" id="btn-close-modal" class="text-slate-300 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors focus:outline-none" aria-label="Tutup popup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5">
            <!-- Select Keputusan / Aksi: Terima Atau Tolak -->
            <div>
                <label for="select-aksi-pesanan" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                    Pilih Keputusan Admin <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select id="select-aksi-pesanan" class="w-full text-sm font-semibold bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white transition-all appearance-none cursor-pointer">
                        <option value="terima" selected>✅ Terima Pesanan (Tugaskan Mekanik)</option>
                        <option value="tolak">❌ Tolak Pesanan (Beri Alasan)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Panel 1: Content untuk Terima Pesanan (Pemilihan Mekanik) -->
            {{-- action diisi dari JS (data-terima-url pada tombol) agar sesuai
                 pesanan yang dipilih; nilai default di bawah hanya jaga-jaga. --}}
            <form id="form-terima-modal" action="#" method="post" class="space-y-4">
                @csrf
                <input type="hidden" name="id_pesanan" id="input-terima-id-pesanan" value="">

                <div class="bg-emerald-50/70 p-4 rounded-xl border border-emerald-200/80 space-y-3">
                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Penugasan Mekanik</span>
                    </div>
                    <label for="select-mekanik-modal" class="block text-xs text-emerald-900 font-medium">
                        Pilih mekanik bertugas yang sedang tersedia di bengkel:
                    </label>
                    <select id="select-mekanik-modal" name="id_mekanik" required
                        class="w-full text-xs sm:text-sm bg-white border border-emerald-300 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold shadow-sm">
                        <option value="">— Pilih Mekanik Bertugas —</option>
                        @forelse ($daftarMekanik as $mekanik)
                            <option value="{{ $mekanik->id_mekanik }}">
                                {{ $mekanik->nama }} — (Spesialis {{ ucfirst($mekanik->spesialis) }})
                            </option>
                        @empty
                            <option value="" disabled>Belum ada mekanik aktif — tambahkan dulu di menu Mekanik</option>
                        @endforelse
                    </select>
                    <p class="text-[11px] text-emerald-700 leading-tight">
                        * Mekanik yang dipilih akan dicatat pada <code class="bg-emerald-100 px-1 py-0.5 rounded text-emerald-900 font-mono">tb_pengerjaan</code> dan status pesanan diubah menjadi <strong>Diterima</strong>.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="btn-cancel-modal px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Terima &amp; Tugaskan
                    </button>
                </div>
            </form>

            <!-- Panel 2: Content untuk Tolak Pesanan (Alasan Penolakan) -->
            {{-- action diisi dari JS (data-tolak-url pada tombol). --}}
            <form id="form-tolak-modal" action="#" method="post" class="space-y-4 hidden">
                @csrf
                <input type="hidden" name="id_pesanan" id="input-tolak-id-pesanan" value="">

                <div class="bg-rose-50/70 p-4 rounded-xl border border-rose-200/80 space-y-3">
                    <div class="flex items-center gap-2 text-rose-800 font-bold text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Alasan Penolakan Pesanan</span>
                    </div>
                    <label for="alasan-penolakan-modal" class="block text-xs text-rose-900 font-medium">
                        Berikan penjelasan mengapa pesanan ini ditolak (akan dikirimkan ke aplikasi mobile pelanggan):
                    </label>
                    <textarea id="alasan-penolakan-modal" name="keterangan" rows="3" required
                        placeholder="Contoh: Slot kuota jam ini sudah penuh / Bengkel sedang dalam pemeliharaan suku cadang..."
                        class="w-full text-xs sm:text-sm bg-white border border-rose-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-sm resize-none"></textarea>
                    <p class="text-[11px] text-rose-700 leading-tight">
                        * Status pesanan akan diperbarui menjadi <strong>Ditolak</strong> pada <code class="bg-rose-100 px-1 py-0.5 rounded text-rose-900 font-mono">tb_pesanan</code> dan dicatat di riwayat status.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="btn-cancel-modal px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tolak Pesanan
                    </button>
                </div>
            </form>

        </div>

    </div>
</div>
