{{--
    Saran bidang keahlian untuk <datalist>.
    Hanya saran — input tetap berupa teks bebas, karena bidang keahlian
    mekanik tidak terbatas pada daftar tertentu.
--}}
<datalist id="saran-spesialis">
    @foreach (($saranSpesialis ?? collect()) as $saran)
        <option value="{{ $saran }}"></option>
    @endforeach
</datalist>
