<!-- ================== MODAL TAMBAH SLOT JADWAL ================== -->
<div id="modal-tambah-slot" class="fixed inset-0 m-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200 hidden opacity-0" aria-labelledby="modal-tambah-title" role="dialog" aria-modal="true">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-200 scale-95" id="modal-tambah-card">
        
        <!-- Header -->
        <div class="bg-[#1F3864] text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#E5A919] text-[#1F3864] flex items-center justify-center font-bold shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-tambah-title" class="font-bold text-base text-white tracking-tight">Buat Slot Jadwal Baru</h3>
                    <p class="text-xs text-slate-300">Buka kuota jam operasional untuk booking servis</p>
                </div>
            </div>
            <button type="button" class="btn-close-tambah-slot text-slate-300 hover:text-white p-1.5 rounded-lg transition-colors focus:outline-none" aria-label="Tutup modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body Form -->
        <form id="form-tambah-slot" action="{{ route('admin.slot.store') }}" method="post" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label for="tambah-tanggal" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Tanggal Servis <span class="text-rose-500">*</span>
                </label>
                <input type="date" id="tambah-tanggal" name="tanggal" required value="{{ now()->format('Y-m-d') }}"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tambah-jam-mulai" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Jam Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="tambah-jam-mulai" name="jam_mulai" required value="08:00"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                </div>
                <div>
                    <label for="tambah-jam-selesai" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Jam Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="tambah-jam-selesai" name="jam_selesai" required value="10:00"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                </div>
            </div>

            <div>
                <label for="tambah-kuota" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Kapasitas Kuota Mobil <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="tambah-kuota" name="kuota" min="1" max="20" required value="3"
                    placeholder="Contoh: 3"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                <p class="text-[11px] text-slate-400 mt-1">Maximum kuota yang dapat ditampung bengkel pada jam tersebut.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" class="btn-close-tambah-slot px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1F3864] hover:bg-[#152747] text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan &amp; Buka Slot
                </button>
            </div>
        </form>

    </div>
</div>
