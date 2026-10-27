<!-- ================== MODAL EDIT MEKANIK ================== -->
<div id="modal-edit-mekanik"
    class="fixed inset-0 m-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200 hidden opacity-0"
    aria-labelledby="modal-edit-mekanik-title" role="dialog" aria-modal="true">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-200 scale-95"
        id="modal-edit-mekanik-card">

        <!-- Header -->
        <div class="bg-[#1F3864] text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#E5A919] text-[#1F3864] flex items-center justify-center font-bold shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-edit-mekanik-title" class="font-bold text-base text-white tracking-tight">
                        Edit Data Mekanik
                    </h3>
                    <p class="text-xs text-slate-300">Perbarui identitas dan status ketersediaan</p>
                </div>
            </div>
            <button type="button"
                class="btn-close-edit-mekanik text-slate-300 hover:text-white p-1.5 rounded-lg transition-colors focus:outline-none"
                aria-label="Tutup modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Body Form -->
        {{-- action & method diisi dari JS (data-url pada tombol edit),
             karena setiap mekanik punya URL berbeda. --}}
        <form id="form-edit-mekanik" action="#" method="post" data-method="put" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="edit-nama-mekanik"
                    class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit-nama-mekanik" name="nama" required maxlength="100"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
            </div>

            <div>
                <label for="edit-spesialis"
                    class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Bidang Keahlian / Profesi <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit-spesialis" name="spesialis" required maxlength="50"
                    list="saran-spesialis" autocomplete="off"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                @include('mekanik.datalist-spesialis')
            </div>

            <div>
                <label for="edit-no-hp-mekanik"
                    class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Nomor HP <span class="text-slate-400 font-normal normal-case">(opsional)</span>
                </label>
                <input type="number" id="edit-no-hp-mekanik" name="no_hp" maxlength="20" placeholder="0812xxxxxxx"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white shadow-sm">
            </div>

            {{-- Status ketersediaan --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" id="edit-aktif" name="aktif" value="1"
                        class="mt-0.5 w-4 h-4 rounded border-slate-300 text-[#1F3864] focus:ring-[#1F3864] cursor-pointer">
                    <span>
                        <span class="block text-xs font-bold text-slate-700">Mekanik Aktif</span>
                        <span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Mekanik nonaktif tidak muncul pada pilihan penugasan saat menerima pesanan,
                            tetapi seluruh riwayat pengerjaannya tetap tersimpan.
                        </span>
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button"
                    class="btn-close-edit-mekanik px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-[#1F3864] hover:bg-[#152747] text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>
