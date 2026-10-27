<!-- ================== MODAL TAMBAH MEKANIK ================== -->
<div id="modal-tambah-mekanik"
    class="fixed inset-0 m-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200 hidden opacity-0"
    aria-labelledby="modal-tambah-mekanik-title" role="dialog" aria-modal="true">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-200 scale-95"
        id="modal-tambah-mekanik-card">

        <!-- Header -->
        <div class="bg-[#1F3864] text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#E5A919] text-[#1F3864] flex items-center justify-center font-bold shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="modal-tambah-mekanik-title" class="font-bold text-base text-white tracking-tight">
                        Tambah Identitas Mekanik
                    </h3>
                    <p class="text-xs text-slate-300">Mekanik tidak memiliki akun login ke sistem</p>
                </div>
            </div>
            <button type="button"
                class="btn-close-tambah-mekanik text-slate-300 hover:text-white p-1.5 rounded-lg transition-colors focus:outline-none"
                aria-label="Tutup modal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Body Form -->
        <form id="form-tambah-mekanik" action="{{ route('admin.mekanik.store') }}" method="post" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="tambah-nama-mekanik"
                    class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="tambah-nama-mekanik" name="nama" required maxlength="100"
                    placeholder="Contoh: Andi Wijaya"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
            </div>

            <div>
                <label for="tambah-spesialis"
                    class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Bidang Keahlian / Profesi <span class="text-rose-500">*</span>
                </label>
                {{-- Teks bebas: bidang keahlian mekanik tidak terbatas daftar.
                     datalist hanya memberi saran dari nilai yang pernah dipakai. --}}
                <input type="text" id="tambah-spesialis" name="spesialis" required maxlength="50"
                    list="saran-spesialis" autocomplete="off" placeholder="Contoh: Mesin, Kelistrikan, Kaki-kaki"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white font-semibold shadow-sm">
                @include('mekanik.datalist-spesialis')
                <p class="text-[11px] text-slate-400 mt-1">
                    Bebas diisi sesuai keahlian. Saran muncul dari bidang yang sudah pernah diinput.
                </p>
            </div>

            <div>
                <label for="tambah-no-hp-mekanik"
                    class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Nomor HP <span class="text-slate-400 font-normal normal-case">(opsional)</span>
                </label>
                <input type="number" id="tambah-no-hp-mekanik" name="no_hp" maxlength="20" placeholder="0812xxxxxxx"
                    class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-xl p-3 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1F3864] focus:bg-white shadow-sm">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button"
                    class="btn-close-tambah-mekanik px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-[#1F3864] hover:bg-[#152747] text-white font-bold text-xs sm:text-sm transition-all shadow-md hover:shadow-lg inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E5A919]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Mekanik
                </button>
            </div>
        </form>

    </div>
</div>
