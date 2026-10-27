<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mekanik;
use App\Models\Pesanan;
use App\Models\SlotJadwal;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Dashboard Admin — ringkasan aktivitas bengkel hari ini.
 *
 * Seluruh data dihitung di server dari basis data yang sama dengan yang
 * dipakai aplikasi mobile, sehingga apa yang dilihat admin adalah kondisi
 * nyata antrean servis.
 */
class DashboardController extends Controller
{
    /**
     * Jumlah antrean yang ditampilkan per halaman pada panel "Pesanan Masuk".
     */
    private const ANTREAN_PER_HALAMAN = 5;

    public function index(Request $request): View
    {
        return view('index', [
            'ringkasan' => $this->ringkasanHariIni(),
            'slotHariIni' => $this->slotHariIni(),
            'pesananMasuk' => $this->pesananMasuk($request),
            'daftarMekanik' => $this->daftarMekanik(),
            'jumlahMenunggu' => Pesanan::where('status', Pesanan::STATUS_MENUNGGU)->count(),
        ]);
    }

    /**
     * Empat angka kartu statistik.
     *
     * @return array<string, int>
     */
    private function ringkasanHariIni(): array
    {
        return [
            'total_booking_hari_ini' => Pesanan::whereDate('waktu_pesan', today())->count(),
            'menunggu_antrean' => Pesanan::where('status', Pesanan::STATUS_MENUNGGU)->count(),
            'sedang_dikerjakan' => Pesanan::where('status', Pesanan::STATUS_DIKERJAKAN)->count(),
            'selesai_hari_ini' => Pesanan::where('status', Pesanan::STATUS_SELESAI)
                ->whereDate('updated_at', today())
                ->count(),
        ];
    }

    /**
     * Slot jadwal hari ini beserta sisa kuotanya.
     *
     * @return \Illuminate\Support\Collection<int, SlotJadwal>
     */
    private function slotHariIni()
    {
        return SlotJadwal::whereDate('tanggal', today())
            ->orderBy('jam_mulai')
            ->get();
    }

    /**
     * Antrean pesanan yang menunggu tindakan admin.
     *
     * Diurutkan FIFO — yang paling lama menunggu (waktu_pesan paling awal)
     * tampil lebih dulu — karena ini antrean servis: semakin lama pelanggan
     * mengantre, semakin cepat ia harus dilayani.
     *
     * Diberi nomor antrean berurutan lintas halaman, sehingga urutan tetap
     * terbaca "Antrean #6, #7, ..." saat admin membuka halaman berikutnya.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function pesananMasuk(Request $request)
    {
        return Pesanan::with(['pelanggan', 'mobil', 'slot', 'detailLayanan.layanan'])
            ->where('status', Pesanan::STATUS_MENUNGGU)
            ->orderBy('waktu_pesan')
            ->orderBy('id_pesanan')
            ->paginate(self::ANTREAN_PER_HALAMAN, ['*'], 'antrean')
            ->appends($request->query());
    }

    /**
     * Daftar mekanik aktif; status ditentukan dari ada/tidaknya
     * pengerjaan yang belum selesai.
     *
     * @return \Illuminate\Support\Collection<int, Mekanik>
     */
    private function daftarMekanik()
    {
        return Mekanik::withCount(['pengerjaan as tugas_berjalan' => function ($query) {
            $query->whereHas('pesanan', function ($q) {
                $q->whereIn('status', [Pesanan::STATUS_DITERIMA, Pesanan::STATUS_DIKERJAKAN]);
            });
        }])
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();
    }
}
