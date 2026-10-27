<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSlotRequest;
use App\Http\Requests\Admin\UpdateSlotRequest;
use App\Models\Pesanan;
use App\Models\SlotJadwal;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Manajemen slot & jadwal servis.
 *
 * Slot yang dibuka di sini adalah prasyarat booking dari aplikasi mobile —
 * pelanggan hanya bisa memesan slot yang lolos gate (konsep_api.md § 1.4).
 */
class SlotController extends Controller
{
    public function index(Request $request): View
    {
        $dari = $request->filled('dari') ? Carbon::parse($request->date('dari')) : today();
        $sampai = $request->filled('sampai') ? Carbon::parse($request->date('sampai')) : today()->addDays(6);

        // Rentang terbalik ditukar agar tabel tidak kosong tanpa penjelasan.
        if ($sampai->lt($dari)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        $slot = SlotJadwal::whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $dibuka = $slot->where('status', SlotJadwal::STATUS_DIBUKA)->count();

        return view('schedule.index', [
            'slot' => $slot,
            'ringkasanSlot' => [
                'total_slot' => $slot->count(),
                'slot_dibuka' => $dibuka,
                'slot_ditutup' => $slot->count() - $dibuka,
                'total_kuota' => (int) $slot->sum('kapasitas'),
                'total_terisi' => (int) $slot->sum('terisi'),
                'total_sisa' => (int) $slot->sum(fn (SlotJadwal $s) => $s->sisa_kuota),
            ],
            'rentang' => [
                'dari' => $dari->format('Y-m-d'),
                'sampai' => $sampai->format('Y-m-d'),
                'label' => $dari->translatedFormat('d M Y').' – '.$sampai->translatedFormat('d M Y'),
            ],
        ]);
    }

    public function store(StoreSlotRequest $request): JsonResponse
    {
        $data = $request->validated();

        $slot = SlotJadwal::create([
            'tanggal' => $data['tanggal'],
            'jam_mulai' => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai'],
            'kapasitas' => $data['kuota'],
            'terisi' => 0,
            'status' => SlotJadwal::STATUS_DIBUKA,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Slot {$slot->jam_mulai}–{$slot->jam_selesai} pada {$slot->tanggal->format('d/m/Y')} berhasil dibuat.",
        ]);
    }

    public function update(UpdateSlotRequest $request, SlotJadwal $slot): JsonResponse
    {
        $data = $request->validated();

        // Kuota tidak boleh dikecilkan di bawah jumlah yang sudah terpakai.
        if ($data['kuota'] < $slot->terisi) {
            return response()->json([
                'status' => 'error',
                'message' => "Kuota tidak boleh kurang dari {$slot->terisi} slot yang sudah terisi.",
            ], 422);
        }

        $slot->update([
            'jam_mulai' => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai'],
            'kapasitas' => $data['kuota'],
            'status' => $data['status'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Slot {$slot->jam_mulai}–{$slot->jam_selesai} pada "
                ."{$slot->tanggal->format('d/m/Y')} berhasil diperbarui.",
        ]);
    }

    /**
     * Buka/tutup slot tanpa menghapusnya — data booking yang sudah
     * terdaftar tetap aman.
     */
    public function toggle(SlotJadwal $slot): JsonResponse
    {
        $slot->update([
            'status' => $slot->status === SlotJadwal::STATUS_DIBUKA
                ? SlotJadwal::STATUS_DITUTUP
                : SlotJadwal::STATUS_DIBUKA,
        ]);

        $label = $slot->status === SlotJadwal::STATUS_DIBUKA ? 'dibuka' : 'ditutup';

        return response()->json([
            'status' => 'success',
            'message' => 'Status slot berhasil diperbarui menjadi '.strtoupper($label).'.',
        ]);
    }

    /**
     * Hapus jadwal slot.
     *
     * Ditolak bila slot sudah punya booking berjalan, karena FK
     * tb_pesanan.id_slot memakai nullOnDelete — artinya pesanan lama akan
     * kehilangan informasi jadwal servisnya. Untuk slot seperti itu, gunakan
     * status "ditutup" agar kuotanya berhenti menerima booking baru tanpa
     * merusak riwayat.
     *
     * Membuka kembali slot dilakukan lewat modal edit (kolom status).
     */
    public function destroy(SlotJadwal $slot): JsonResponse
    {
        $jumlahPesananAktif = $slot->pesanan()
            ->whereIn('status', [
                Pesanan::STATUS_MENUNGGU,
                Pesanan::STATUS_DITERIMA,
                Pesanan::STATUS_DIKERJAKAN,
            ])
            ->count();

        if ($jumlahPesananAktif > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Slot {$slot->jam_mulai}–{$slot->jam_selesai} pada "
                    ."{$slot->tanggal->format('d/m/Y')} masih memiliki {$jumlahPesananAktif} pesanan aktif "
                    .'dan tidak dapat dihapus. Tutup saja slot ini agar tidak menerima booking baru.',
            ], 422);
        }

        $keterangan = "Slot {$slot->jam_mulai}–{$slot->jam_selesai} pada {$slot->tanggal->format('d/m/Y')}";

        $slot->delete();

        return response()->json([
            'status' => 'success',
            'message' => "{$keterangan} berhasil dihapus dari jadwal.",
        ]);
    }
}
