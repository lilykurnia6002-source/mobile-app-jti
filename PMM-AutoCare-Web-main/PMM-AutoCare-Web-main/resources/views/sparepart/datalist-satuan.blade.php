{{--
    Saran satuan untuk <datalist>.
    Hanya saran — input tetap teks bebas, karena satuan sparepart bisa
    beragam (liter, pcs, set, unit, botol, meter, dan sebagainya).
--}}
<datalist id="saran-satuan">
    @foreach (($saranSatuan ?? collect()) as $saran)
        <option value="{{ $saran }}"></option>
    @endforeach
</datalist>
