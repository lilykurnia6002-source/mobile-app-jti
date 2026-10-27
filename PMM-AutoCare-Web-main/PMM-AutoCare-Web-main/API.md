# REST API Mobile — PMM AutoCare (`/api/v1`)

Dokumen ini merangkum cara menjalankan dan memakai REST API backend PMM AutoCare
yang dipakai aplikasi **mobile** (role **pelanggan**).

Kontrak lengkap tiap endpoint ada di `.docs/backend/konsep_api.md`.
Skema basis data ada di `.docs/konsep_rancanganBasisData.md`.

---

## Menjalankan

```sh
php artisan migrate:fresh --seed   # bangun ulang skema + data contoh
php artisan serve                  # http://127.0.0.1:8000
php artisan test                   # 72 test
```

Semua respons JSON memakai pembungkus seragam:

```json
{ "success": true, "message": "...", "data": { } }
```

Daftar berhalaman menambahkan `meta: {total, per_page, current_page, last_page}`.
Error menyertakan kode stabil pada `data.kode`:

| Kode | HTTP |
|---|---|
| `UNPROCESSABLE_CONTENT` | 422 (validasi; `data.errors` berupa objek `{field: [pesan]}`) |
| `UNAUTHENTICATED` | 401 |
| `FORBIDDEN` | 403 (data milik pelanggan lain) |
| `NOT_FOUND` | 404 |
| `SLOT_TIDAK_TERSEDIA` | 422 (+ `data.alasan`: `slot_ditutup` \| `tanggal_lewat` \| `kuota_penuh`) |
| `MOBIL_TIDAK_TERSEDIA` | 422 |
| `PESANAN_TIDAK_DAPAT_DIBATALKAN` | 422 |
| `PEMBAYARAN_LUNAS` | 409 |
| `RATE_LIMIT` | 429 |

Autentikasi memakai Bearer token Sanctum:
`Authorization: Bearer <token>`.

---

## Akun demo (hasil seeder)

| Peran | Email | Password | Akses |
|---|---|---|---|
| Pelanggan | `budi@example.com` | `password` | API mobile |
| Pelanggan | `siti@example.com` | `password` | API mobile |
| Pelanggan | `rian@example.com` | `password` | API mobile |
| Admin | `admin@pmm-auto.test` | `password` | Website (bukan API mobile) |
| Pemilik | `pemilik@pmm-auto.test` | `password` | Website (bukan API mobile) |

Hanya akun ber-role `pelanggan` yang boleh login lewat API mobile.

---

## Endpoint

### Publik (tanpa token)

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/api/v1/auth/register` | Daftar akun + profil pelanggan, langsung dapat token |
| POST | `/api/v1/auth/login` | Login pelanggan |
| GET | `/api/v1/layanan` | Daftar layanan & tarif |
| GET | `/api/v1/layanan/{id}` | Detail satu layanan |
| GET | `/api/v1/slot/tanggal-tersedia` | Tanggal yang masih punya slot (opsional `?dari=&sampai=`) |
| GET | `/api/v1/slot?tanggal=YYYY-MM-DD` | Slot tersedia pada tanggal tertentu |
| GET | `/api/v1/slot/{id}` | Detail slot (hanya bila lolos gate) |

### Perlu token

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/api/v1/auth/logout` | Cabut token aktif |
| GET | `/api/v1/profil` | Profil + `jumlah_mobil` |
| PUT | `/api/v1/profil` | Ubah profil (`password` opsional) |
| GET | `/api/v1/mobil` | Daftar mobil milik sendiri |
| POST | `/api/v1/mobil` | Tambah mobil |
| PUT | `/api/v1/mobil/{id}` | Ubah mobil |
| POST | `/api/v1/pesanan` | **Booking servis** (endpoint pusat) |
| GET | `/api/v1/pesanan` | Riwayat pesanan (`?status=&page=&per_page=`) |
| GET | `/api/v1/pesanan/{id}` | Detail pesanan |
| GET | `/api/v1/pesanan/{id}/riwayat-status` | Pelacakan status |
| PATCH | `/api/v1/pesanan/{id}/batal` | Batalkan (hanya saat `menunggu`, idempoten) |
| GET | `/api/v1/pesanan/{id}/nota` | Nota digital (jasa + sparepart) |
| GET | `/api/v1/notifikasi` | Notifikasi (`?belum_baca=1`) |
| PATCH | `/api/v1/notifikasi/{id}/baca` | Tandai dibaca (idempoten) |

---

## Contoh alur booking

```sh
# 1. Login
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"email":"budi@example.com","password":"password"}' | jq -r .data.token)

# 2. Lihat slot tersedia
curl -s "http://127.0.0.1:8000/api/v1/slot?tanggal=2026-09-20" -H "Accept: application/json"

# 3. Ajukan pesanan
curl -s -X POST http://127.0.0.1:8000/api/v1/pesanan \
  -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"id_mobil":1,"id_slot":21,"keluhan":"Mesin bergetar saat idling",
       "detail_layanan":[{"id_layanan":1,"jumlah":1},{"id_layanan":2,"jumlah":1}]}'
```

---

## Aturan bisnis penting

- **Gate slot** (`konsep_api.md` § 1.4) — slot tersedia hanya bila
  `status = 'dibuka'` **dan** `tanggal >= hari ini` **dan** `terisi < kapasitas`.
  Aturan ini ditulis sekali pada `SlotJadwal::scopeTersedia()` dan dipakai
  bersama oleh endpoint baca maupun validasi booking.
- **Kuota slot** — `terisi` ditambah saat booking dan dikembalikan saat
  pembatalan, keduanya di dalam transaksi dengan `lockForUpdate()`.
- **`kode_pesanan`** berformat `PMM-YYYYMMDD-NNN`, urut per hari.
- **`total_biaya`** disimpan di `tb_pesanan` dan dijaga konsisten server;
  nota membacanya, tidak menghitung ulang.
- **Tarif & harga disimpan sebagai snapshot** di `tb_detail_layanan` /
  `tb_detail_sparepart`, agar perubahan tarif master tidak mengubah pesanan lama.
- **Scoping pelanggan** — pelanggan tidak pernah bisa melihat atau mengubah data
  pelanggan lain; percobaan menghasilkan **403**, bukan 404.

---

## Hal yang belum dibangun (fase berikutnya)

- **Modul pembayaran** — `GET /pembayaran/{id}` dan `POST /pembayaran/{id}/konfirmasi`
  belum ada. Tabel `tb_pembayaran` dan `tb_metode_bayar` sudah dibuat, dan
  `PesananDetailResource` mengembalikan `pembayaran: null` sementara.

  > Ada konflik antar dokumen yang perlu diputuskan lebih dulu:
  > `konsep_pemrograman.md` § 4.6 menyatakan **admin** yang memverifikasi
  > pembayaran, sedangkan `konsep_api.md` § 8.3 menyatakan pelanggan
  > mengonfirmasi sendiri sehingga langsung `lunas`.

- **Endpoint admin/pemilik** — approve/tolak pesanan, penetapan mekanik, input
  progres & keluhan tambahan, pencatatan sparepart, verifikasi pembayaran, dan
  laporan produktivitas. Merupakan fase website dan akan menempati backend yang sama.
- **Push notification** — saat ini notifikasi hanya dibaca lewat polling
  `GET /notifikasi`.

---

## Catatan teknis

- **MySQL vs SQLite.** `lockForUpdate()` tidak berefek di SQLite. Untuk demo
  atau sidang, aktifkan MySQL Laragon (blok `DB_CONNECTION=mysql` di `.env`)
  agar penguncian kuota slot benar-benar bekerja. Test tetap memakai SQLite
  in-memory (`phpunit.xml`).
- **Rate limit.** 60 permintaan/menit per pengguna (`throttle:api`);
  endpoint auth dibatasi 10/menit per IP (`throttle:auth`).
- **Pesan validasi** berbahasa Indonesia (`lang/id/validation.php`, `APP_LOCALE=id`).
