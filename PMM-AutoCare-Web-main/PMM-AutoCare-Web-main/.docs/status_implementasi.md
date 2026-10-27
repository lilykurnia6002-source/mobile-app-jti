# Status Implementasi — REST API Mobile PMM AutoCare

**Tanggal:** 2026-09-16
**Fase:** API Mobile v1 (role **Pelanggan**) — **SELESAI & TERVERIFIKASI**
**Kontrak acuan:** `backend/konsep_api.md` · **Skema acuan:** `../konsep_rancanganBasisData.md`

---

## 1. Ringkasan

Seluruh endpoint API mobile yang dirinci pada `backend/konsep_api.md` telah
diimplementasikan dan diuji. Backend kini dapat dipakai aplikasi mobile
PMM AutoCare untuk registrasi, login, mengelola mobil, membaca slot jadwal,
mengajukan booking servis, melacak status, dan membaca notifikasi serta nota.

| Metrik | Nilai |
|---|---|
| Endpoint terdaftar | **21** |
| Tabel basis data | **16** (`tb_*`) |
| Test otomatis | **72 lulus** / 347 assertion |
| Formatter | Pint bersih |
| Versi | Laravel 13.31.0 · PHP 8.4.25 · Sanctum v4.3.3 |

Verifikasi dilakukan dua lapis: **test otomatis** (`php artisan test`) dan
**uji HTTP langsung** terhadap `php artisan serve` (login → booking → kuota →
pembatalan → nota → scoping 403 → seluruh kode error).

---

## 2. Yang Sudah Selesai

### 2.1 Paket & Bootstrap

| Berkas | Perubahan |
|---|---|
| `composer.json` / `.lock` | `laravel/sanctum` v4.3.3 ditambahkan |
| `bootstrap/app.php` | `api:` didaftarkan; seluruh render error API dipusatkan di sini |
| `routes/api.php` | Grup `prefix('v1')` + middleware throttle |
| `config/sanctum.php` | Terbit otomatis dari `install:api` |
| `app/Providers/AppServiceProvider.php` | Rate limiter `api` (60/menit) dan `auth` (10/menit) |
| `.env` | `APP_LOCALE=id` |

### 2.2 Basis Data — 16 Tabel

Semua memakai awalan `tb_` **dan** primary key bergaya kamus data
(`id_pesanan`, `id_slot`, `id_pelanggan`, dst), sesuai keputusan
"patuh dokumen penuh".

| # | Tabel | Primary Key | Catatan |
|---|---|---|---|
| 1 | `tb_user` | `id_user` | Menggantikan `users` bawaan; + kolom `role` (`pelanggan`/`admin`/`pemilik`) |
| 2 | `tb_pelanggan` | `id_pelanggan` | 1—1 dengan `tb_user` |
| 3 | `tb_mekanik` | `id_mekanik` | Tanpa akun login |
| 4 | `tb_mobil` | `id_mobil` | `plat_nomor` unik |
| 5 | `tb_slot_jadwal` | `id_slot` | `sisa_kuota` **dihitung**, tidak disimpan |
| 6 | `tb_pesanan` | `id_pesanan` | Entitas pusat; `id_slot` nullable (walk-in) |
| 7 | `tb_detail_layanan` | `id_detail` | Snapshot `harga_jasa` |
| 8 | `tb_detail_sparepart` | `id_detail` | Snapshot `harga` |
| 9 | `tb_pengerjaan` | `id_pengerjaan` | `dicatat_oleh` → `tb_user` |
| 10 | `tb_keluhan_tambahan` | `id_keluhan` | `ditambahkan_oleh` → `tb_user` |
| 11 | `tb_riwayat_status` | `id_riwayat` | `diubah_oleh` → `tb_user` |
| 12 | `tb_jenis_layanan` | `id_layanan` | |
| 13 | `tb_sparepart` | `id_sparepart` | |
| 14 | `tb_metode_bayar` | `id_metode` | Tabel saja (modul ditunda) |
| 15 | `tb_pembayaran` | `id_pembayaran` | Tabel saja (modul ditunda) |
| 16 | `tb_notifikasi` | `id_notifikasi` | Kepemilikan via relasi pesanan |

**Perubahan pada migrasi bawaan** — `0001_01_01_000000_create_users_table.php`
kini membuat `tb_user` (bukan `users`) dengan tambahan kolom `role` dan `no_hp`,
sambil tetap membuat `password_reset_tokens` dan `sessions`.

### 2.3 Model — 16 buah

`app/Models/` — `User`, `Pelanggan`, `Mekanik`, `Mobil`, `SlotJadwal`, `Pesanan`,
`DetailLayanan`, `DetailSparepart`, `Pengerjaan`, `KeluhanTambahan`,
`RiwayatStatus`, `JenisLayanan`, `Sparepart`, `MetodeBayar`, `Pembayaran`,
`Notifikasi`. Masing-masing menetapkan `$table` dan `$primaryKey` eksplisit,
memakai gaya atribut Laravel 13 (`#[Fillable]`) seperti `User` yang sudah ada,
dan mendefinisikan relasi lengkap.

**Scope kunci yang dipakai bersama:**

- `SlotJadwal::scopeTersedia()` — **satu-satunya** sumber aturan gate (§1.4),
  dipakai endpoint baca *dan* validasi booking agar tidak pernah berbeda.
- `SlotJadwal::alasanTidakTersedia()` — mengembalikan `slot_ditutup` /
  `tanggal_lewat` / `kuota_penuh`.
- `Pesanan::scopeUntukPelanggan()` — scoping data milik pelanggan login.
- `Notifikasi::scopeUntukPelanggan()` — scoping lewat relasi pesanan.

### 2.4 Endpoint — 21 buah

**Publik (tanpa token)** — 7 endpoint

| Method | Endpoint |
|---|---|
| POST | `/api/v1/auth/register` |
| POST | `/api/v1/auth/login` |
| GET | `/api/v1/layanan` |
| GET | `/api/v1/layanan/{id}` |
| GET | `/api/v1/slot/tanggal-tersedia` |
| GET | `/api/v1/slot?tanggal=YYYY-MM-DD` |
| GET | `/api/v1/slot/{id}` |

**Perlu token Sanctum** — 14 endpoint

| Method | Endpoint |
|---|---|
| POST | `/api/v1/auth/logout` |
| GET | `/api/v1/profil` |
| PUT | `/api/v1/profil` |
| GET | `/api/v1/mobil` |
| POST | `/api/v1/mobil` |
| PUT | `/api/v1/mobil/{id}` |
| POST | `/api/v1/pesanan` |
| GET | `/api/v1/pesanan` |
| GET | `/api/v1/pesanan/{id}` |
| GET | `/api/v1/pesanan/{id}/riwayat-status` |
| PATCH | `/api/v1/pesanan/{id}/batal` |
| GET | `/api/v1/pesanan/{id}/nota` |
| GET | `/api/v1/notifikasi` |
| PATCH | `/api/v1/notifikasi/{id}/baca` |

**Lapisan kode pendukungnya:**

- 8 controller di `app/Http/Controllers/Api/V1/`
- 2 service di `app/Services/` — `BookingService`, `PesananService`
- 10 Form Request di `app/Http/Requests/Api/V1/`
- 13 API Resource di `app/Http/Resources/Api/V1/`
- 1 helper `app/Http/Responses/ApiResponse.php`
- 1 exception domain `app/Exceptions/ApiException.php`

### 2.5 Kontrak Respons & Kode Error

Seluruh respons berbentuk `{success, message, data}` (+ `meta` untuk daftar
berhalaman). Penanganan dipusatkan di `bootstrap/app.php`:

| Kode | HTTP | Pemicu |
|---|---|---|
| `UNPROCESSABLE_CONTENT` | 422 | Validasi gagal — `data.errors` berupa **objek** `{field: [pesan]}` |
| `UNAUTHENTICATED` | 401 | Token hilang / tidak valid |
| `FORBIDDEN` | 403 | Akses data milik pelanggan lain |
| `NOT_FOUND` | 404 | Sumber daya tidak ada |
| `SLOT_TIDAK_TERSEDIA` | 422 | Gate gagal — + `data.alasan` |
| `MOBIL_TIDAK_TERSEDIA` | 422 | `id_mobil` bukan milik pelanggan |
| `PESANAN_TIDAK_DAPAT_DIBATALKAN` | 422 | Batal saat status ≠ `menunggu` |
| `PEMBAYARAN_LUNAS` | 409 | Disiapkan untuk modul pembayaran |
| `RATE_LIMIT` | 429 | Melebihi batas |
| `SERVER_ERROR` | 500 | Jaring terakhir |

Pesan validasi berbahasa Indonesia sesuai contoh dokumen
("Kolom tanggal wajib diisi.") lewat `lang/id/validation.php`.

### 2.6 Logika Bisnis Kritis

**Booking atomik** (`BookingService::buatPesanan()`) — satu transaksi:
kunci baris slot dengan `lockForUpdate()` → cek gate → pastikan mobil milik
pelanggan → generate kode → insert pesanan → insert rincian + hitung
`total_biaya` → tambah `terisi` → catat riwayat status pertama.

**`kode_pesanan`** berformat `PMM-YYYYMMDD-NNN`, urut per hari, dengan index
unik sebagai pengaman terakhir.

**Pembatalan** (`PesananService::batalkan()`) — **fitur tambahan** di luar
konsep awal (open item #3): hanya saat status `menunggu`, mengembalikan kuota
slot, mencatat riwayat, dan **idempoten** terhadap klik ganda.

**Snapshot harga** — `harga_jasa` dan `harga` disalin ke tabel detail, sehingga
perubahan tarif master tidak mengubah nilai pesanan lama.

**`total_biaya`** disimpan di `tb_pesanan` dan dijaga konsisten server;
`GET /pesanan/{id}/nota` membacanya, tidak menghitung ulang.

### 2.7 Data Contoh (Seeder)

9 seeder: `UserSeeder`, `MekanikSeeder`, `MobilSeeder`, `JenisLayananSeeder`,
`SparepartSeeder`, `SlotJadwalSeeder`, `MetodeBayarSeeder`, `PesananSeeder`,
`DatabaseSeeder`. Data dirancang agar **setiap layar mobile punya isi**:

- 5 user (1 admin, 1 pemilik, 3 pelanggan), 4 mekanik, 5 mobil
- 29 slot — termasuk slot **penuh**, slot **ditutup**, dan slot **lewat**
- 6 pesanan lintas status (`menunggu`, `diterima`, `dikerjakan`, `selesai`,
  `ditolak`, `dibatalkan`) + 11 riwayat status + 11 notifikasi
- Nota contoh dengan jasa **dan** sparepart

`UserFactory` diperluas dengan state `admin()`, `pemilik()`, `pelanggan()`.

### 2.8 Test — 72 lulus / 347 assertion

| Berkas | Test | Fokus |
|---|---|---|
| `PesananBookingTest` | 15 | Booking, gate, kuota, mobil orang lain, kode pesanan |
| `SlotGateTest` | 11 | Ketiga `alasan`, `sisa_kuota`, tanggal tersedia |
| `PesananBatalTest` | 8 | Pembatalan, pengembalian kuota, idempotensi |
| `AuthTest` | 7 | Register, login, logout, role admin ditolak |
| `MobilTest` | 7 | CRUD + scoping 403 |
| `NotifikasiTest` | 7 | Daftar, filter belum baca, scoping 403 |
| `ResponseEnvelopeTest` | 7 | Bentuk envelope & kode error |
| `ProfilTest` | 5 | Profil, ganti password, email duplikat |
| `ApiExceptionRenderingTest` | 3 | Render exception domain |
| `ExampleTest` (Feature + Unit) | 2 | Bawaan Laravel |

### 2.9 Dokumentasi

- `API.md` (root proyek) — cara menjalankan, akun demo, daftar endpoint,
  contoh alur booking, aturan bisnis, dan hal yang ditunda.

---

## 3. Belum Dibangun (Sengaja Ditunda)

| Item | Alasan |
|---|---|
| **Modul pembayaran** — `GET /pembayaran/{id}`, `POST /pembayaran/{id}/konfirmasi` | Ditunda atas permintaan; tabelnya sudah dibuat dan `pembayaran` mengembalikan `null` |
| **Endpoint admin/pemilik** — approve/tolak, penugasan mekanik, input progres & keluhan, pencatatan sparepart, verifikasi bayar, laporan produktivitas & proporsi non-tunai | Fase website; akan memakai backend & versi yang sama |
| **Push notification (FCM/WebSocket)** | Fase ini hanya menyediakan data notifikasi lewat polling |

---

## 4. Hal yang Perlu Diputuskan

1. **Konflik dokumen soal verifikasi pembayaran.**

   `../konsep_pemrograman.md` § 4.6 menyatakan pelanggan mengunggah bukti
   transfer lalu **admin** yang memverifikasi. Namun `backend/konsep_api.md`
   § 8.3 menyatakan pelanggan mengonfirmasi sendiri sehingga status langsung
   `lunas` tanpa verifikasi admin.

   Rekomendasi: ikuti § 4.6 (unggah bukti → admin verifikasi), karena
   `konsep_rancanganBasisData.md` menyediakan `bukti_transfer` **dan**
   `diverifikasi_oleh` — dua kolom yang tidak berguna bila pelanggan
   mengonfirmasi sendiri.

2. **`migrate:fresh` wajib dijalankan.** Perubahan `users` → `tb_user` menghapus
   data lama. Database dev kini berisi data seeder. Aman bila tidak ada data
   nyata yang ingin disimpan.

3. **MySQL vs SQLite untuk demo.** `lockForUpdate()` tidak berefek di SQLite.
   Untuk sidang/demo, aktifkan MySQL Laragon (blok `DB_CONNECTION=mysql` di
   `.env`) agar penguncian kuota slot benar-benar terbukti. Test tetap memakai
   SQLite in-memory sesuai `phpunit.xml`.

---

## 5. Cara Menjalankan

```sh
php artisan migrate:fresh --seed   # bangun ulang skema + data contoh
php artisan serve                  # http://127.0.0.1:8000
php artisan test                   # 72 test
```

**Akun demo** (password semuanya `password`):

| Peran | Email | Akses |
|---|---|---|
| Pelanggan | `budi@example.com` | API mobile |
| Pelanggan | `siti@example.com` | API mobile |
| Pelanggan | `rian@example.com` | API mobile |
| Admin | `admin@pmm-auto.test` | Website (ditolak di API mobile) |
| Pemilik | `pemilik@pmm-auto.test` | Website (ditolak di API mobile) |

---

## 6. Berkas yang Berubah / Ditambahkan

**Diubah (14):** `.gitignore`, `AGENTS.md`, `CLAUDE.md`, `app/Models/User.php`,
`app/Providers/AppServiceProvider.php`, `bootstrap/app.php`, `composer.json`,
`composer.lock`, `database/factories/UserFactory.php`,
`database/seeders/DatabaseSeeder.php`, migrasi `create_users_table`,
`resources/css/app.css`, `resources/js/app.js`, `routes/web.php`

**Ditambahkan:** `routes/api.php`, `API.md`, 16 migration domain,
16 model, 8 controller, 10 form request, 13 resource, 2 service,
1 exception, 1 response helper, `lang/id/validation.php`, 9 seeder,
9 berkas test API.

> Belum ada yang di-*commit*. Seluruh perubahan masih di working tree.