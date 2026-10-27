<!-- ================== MODAL EDIT / PENGATURAN SLOT ================== -->
<div id="modal-edit-slot" class="fixed inset-0 m-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200 hidden opacity-0" aria-labelledby="modal-edit-title" role="dialog" aria-modal="true">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-200 scale-95" id="modal-edit-card">

        <!-- Header -->
        <div class="bg-[#1F3864] text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#E5A919] text-[#1F3864] flex items-center justify-center font-bold shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-edit-title" class="font-bold text-base text-white tracking-tight">Edit Kuota &amp; Status Slot</h3>
                    <p class="text-xs text-slate-300 font-normal">
                        <span id="modal-edit-info-jam" class="font-semibold text-[#E5A919]">08:00 - 10:00</span> — <span id="modal-edit-info-tanggal">16 Sep 2026</span>
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close-edit-slot text-slate-300 hover:text-white p-1.5 rounded-lg transition-colors focus:outline-none" aria-label="Tutup modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body Form -->
        <form id="form-edit-slot" action="#" method="post" data-method="put" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tambah-jam-mulai" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Jam Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="jam_mulai" name="jam_mulai" required value="08:00"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                </div>
                <div>
                    <label for="tambah-jam-selesai" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Jam Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="jam_selesai" name="jam_selesai" required value="10:00"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                </div>
            </div>

            <div>
                <label for="edit-kuota" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Kapasitas Kuota Mobil <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="edit-kuota" name="kuota" min="1" max="20" required value="1"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                <p class="text-[11px] text-slate-500 mt-1">
                    Terisi saat ini: <strong id="edit-terisi-info" class="text-[#1F3864]">0 Mobil</strong>.
                </p>
            </div>

            <div>
                <label for="edit-status" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Status Slot Operasional <span class="text-rose-500">*</span>
                </label>
                <select id="edit-status" name="status"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm cursor-pointer">
                    <option value="dibuka">🟢 Dibuka (Tersedia untuk Booking Mobile)</option>
                    <option value="ditutup">🔴 Ditutup / Non-Aktifkan Sementara</option>
                </select>
                <p class="text-[11px] text-amber-700 font-medium mt-1.5 bg-amber-50 p-2 rounded-lg border border-amber-200">
                    * Catatan: Menutup slot tidak menghapus data pesanan yang telah terdaftar sebelumnya.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" class="btn-close-edit-slot px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1F3864] hover:bg-[#152747] text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>
