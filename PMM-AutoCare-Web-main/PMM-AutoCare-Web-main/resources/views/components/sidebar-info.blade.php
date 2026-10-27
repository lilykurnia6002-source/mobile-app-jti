{{--
============================================================================
 KOTAK INFORMASI SIDEBAR
============================================================================
 Kotak bergradasi di bagian bawah sidebar untuk memberi konteks singkat
 tentang halaman yang sedang dibuka.

 Pemakaian:
     <x-sidebar-info judul="Status Bengkel Aktif">
         PMM AutoCare beroperasi normal.
     </x-sidebar-info>

 Slot default berisi teks penjelasan (boleh memuat variabel Blade).
============================================================================
--}}

@props(['judul' => 'Informasi'])

<div class="bg-gradient-to-br from-[#1F3864]/5 to-[#E5A919]/10 rounded-xl p-3.5 border border-[#1F3864]/10">
    <div class="flex items-center gap-2 mb-1.5">
        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></div>
        <span class="text-xs font-semibold text-[#1F3864]">{{ $judul }}</span>
    </div>
    <p class="text-[11px] text-slate-600 leading-relaxed">{{ $slot }}</p>
</div>
