<!-- ================== MODAL EDIT SPAREPART ================== -->
<div id="modal-edit-sparepart"
    class="fixed inset-0 m-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200 hidden opacity-0"
    aria-labelledby="modal-edit-sparepart-title" role="dialog" aria-modal="true">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-200 scale-95"
        id="modal-edit-sparepart-card">

        <!-- Header -->
        <div class="bg-[#1F3864] text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-[#E5A919] text-[#1F3864] flex items-center justify-center font-bold shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-edit-sparepart-title" class="font-bold text-base text-white tracking-tight">
                        Edit Data Sparepart
                    </h3>
                    <p class="text-xs text-slate-300">Perbarui harga dan persediaan</p>
                </div>
            </div>
            <button type="button"
                class="btn-close-edit-sparepart text-slate-300 hover:text-white p-1.5 rounded-lg transition-colors focus:outline-none"
                aria-label="Tutup modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Body Form -->
        {{-- action & method diisi dari JS (data-url pada tombol edit),
             karena setiap sparepart punya URL berbeda. --}}
        <form id="form-edit-sparepart" action="#" method="post" data-method="put" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="edit-nama-sparepart"
                    class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Nama Sparepart <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit-nama-sparepart" name="nama" required maxlength="100"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit-satuan"
                        class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Satuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="edit-satuan" name="satuan" required maxlength="20"
                        list="saran-satuan" autocomplete="off"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                    @include('sparepart.datalist-satuan')
                </div>

                <div>
                    <label for="edit-harga"
                        class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Harga Satuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="edit-harga" name="harga" required min="0" step="0.01"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit-stok"
                        class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Stok <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="edit-stok" name="stok" required min="0"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                </div>

                <div>
                    <label for="edit-stok-minimal"
                        class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Stok Minimal <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="edit-stok-minimal" name="stok_minimal" required min="0"
                        class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                </div>
            </div>

            <p class="text-[11px] text-slate-500 bg-slate-50 border border-slate-200 rounded-xl p-3 leading-relaxed">
                Kolom <strong class="text-slate-700">Stok</strong> di sini untuk koreksi manual (mis. stok opname).
                Pengurangan stok otomatis terjadi saat pemakaian dicatat pada pesanan.
            </p>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button"
                    class="btn-close-edit-sparepart px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors">
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
