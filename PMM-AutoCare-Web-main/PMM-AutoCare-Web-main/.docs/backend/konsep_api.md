# Konsep API — Sistem Informasi Pelayanan dan Penjadwalan Servis Mobil
### Bengkel PMM AUTO — REST API untuk Mobile App (Role: Pelanggan)

> Dokumen ini adalah **spesifikasi teknis lengkap** dari seluruh endpoint REST API yang dipakai aplikasi **Mobile Android** (role **Pelanggan**). Ini pecahan dari `konsep_pemrograman.md` — di sana hanya ada ringkasan tabel endpoint; di sini tiap endpoint dirinci dengan parameter, contoh request, contoh response, dan kode error.
>
> **Aturan utama:** pelanggan tidak punya akses Website. Satu-satunya jembatan antara app mobile dan sistem adalah API di bawah ini. **Slot jadwal disediakan oleh Admin via Website** — API hanya menyediakan pembacaan slot; pembelian slot tidak pernah dilakukan lewat endpoint di bawah ini.
>
> **Kontrak data** (nama kolom, tipe, relasi, aturan bisnis) diatur di `konsep_database.md`. Label `[pelanggan]` pada baris "Akses" berarti berhubungan dengan data milik pelanggan yang sedang login (scoping): pelanggan tidak bisa mengakses baris milik pelanggan lain.

---

## 1. Notasi Umum

### 1.1 Base URL & Versi

```
https://api.pmm-auto.example/api/v1
```

Prefix `/api/v1` disarankan (lihat `konsep_pemrograman.md` § 8) agar perubahan struktur di masa depan tidak memutus app lama. Contoh endpoint di dokumen ini ditulis memakai prefix `v1`.

### 1.2 Format Request/Response

- Body request: **JSON** (`Content-Type: application/json`).
- Response: **JSON** dengan pembungkus seragam:

```json
{
  "success": true,
  "message": "Pesan dari server",
  "data": { }
}
```

- `success`: `true` untuk 2xx, `false` untuk 4xx/5xx.
- `message`: kalimat singkat untuk ditampilkan app (bisa kosong).
- `data`: payload — **objek** untuk satu entitas, **array** untuk daftar.
- Halaman data (list) memakai `meta` tambahan:

```json
{
  "success": true,
  "message": "Daftar pesanan",
  "data": [ ... ],
  "meta": {
    "total": 12,
    "per_page": 10,
    "current_page": 2,
    "last_page": 2
  }
}
```

### 1.3 Autentikasi

| Skema | Header |
|---|---|
| Bearer token (Sanctum) | `Authorization: Bearer <token>` |

- Token diperoleh dari `POST /api/v1/auth/login` atau `POST /api/v1/auth/register`.
- Semua endpoint kecuali `auth/login`, `auth/register`, `layanan`, dan `slot` (mode baca) **wajib** menyertakan token.
- Response untuk token hilang/kedaluwarsa/salah:

```json
// HTTP 401
{
  "success": false,
  "message": "Unauthenticated.",
  "data": null
}
```

### 1.4 Aturan Ketersediaan Slot (Gate Booking)

Sebuah slot dianggap **tersedia** bila **seluruh** kondisi berikut terpenuhi:

1. `slot_jadwal.status = 'dibuka'` — Admin belum menutup slot.
2. `slot_jadwal.tanggal >= hari ini` — tanggal yang sudah lewat tidak bisa dipesan.
3. `slot_jadwal.terisi < slot_jadwal.kapasitas` — kuota belum penuh.

Kondisi ini berlaku untuk tampilan (`GET /slot`, `GET /slot/tanggal-tersedia`, `GET /slot/{id}`) **dan** validasi saat booking (`POST /pesanan`). Endpoint untuk daftar slot hanya menampilkan slot yang lolos gate; endpoint booking akan **menolak** bila gate gagal.

Format waktu di seluruh contoh dokumen ini: **`YYYY-MM-DD`** untuk tanggal, **`HH:MM`** untuk jam (24 jam), **datetime** gabungan `YYYY-MM-DD HH:MM:SS`.

### 1.5 Tabel Kode Error

Kode error dipakai sebagai penanda stabil di sisi app; `message` boleh berubah sesuai bahasa.

| Kode | HTTP | Kapan terjadi |
|---|---|---|
| `UNPROCESSABLE_CONTENT` | 422 | Validasi request gagal; `data.errors` berisi daftar kesalahan per field |
| `UNAUTHENTICATED` | 401 | Token tidak ada/tidak valid/kedaluwarsa |
| `FORBIDDEN` | 403 | Akses ke data milik pelanggan lain |
| `NOT_FOUND` | 404 | Sumber daya tidak ditemukan |
| `SLOT_TIDAK_TERSEDIA` | 422 | Gate booking gagal: slot ditutup, tanggal lewat, atau kuota penuh. Field `alasan` menjelaskan yang mana (lihat 6.1) |
| `MOBIL_TIDAK_TERSEDIA` | 422 | `id_mobil` yang dikirim bukan milik pelanggan login |
| `EMAIL_TERPAKAI` | 422 | Email sudah terdaftar |
| `PEMBAYARAN_LUNAS` | 409 | Konfirmasi pembayaran pada pesanan yang sudah lunas |
| `RATE_LIMIT` | 429 | Terlalu banyak request dalam waktu singkat |
| `SERVER_ERROR` | 500 | Error internal server |

Contoh respons 422 dengan `data.errors`:

```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": {
    "errors": {
      "tanggal": ["Kolom tanggal wajib diisi."],
      "jam_mulai": ["Kolom jam mulai wajib diisi."]
    }
  }
}
```

> Catatan: `data.errors` di atas berisi objek `{ field: [daftar pesan] }`, bukan array.

---

## 2. Modul Autentikasi & Profil

### 2.1 `POST /api/v1/auth/register`

Buat akun + profil pelanggan sekaligus.

| Akses | Publik |
|---|---|
| Body | lihat contoh |

Contoh request:

```json
{
  "nama": "Budi Santoso",
  "email": "budi@example.com",
  "password": "rahasia123",
  "no_hp": "081234567890",
  "alamat": "Jl. Melati No. 12, Malang"
}
```

Contoh response (sukses — langsung mengembalikan token):

```json
// HTTP 201
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "token": "1|abcdef1234567890",
    "pelanggan": {
      "id": 4,
      "nama": "Budi Santoso",
      "email": "budi@example.com",
      "no_hp": "081234567890",
      "alamat": "Jl. Melati No. 12, Malang"
    }
  }
}
```

Kode error: `EMAIL_TERPAKAI` (422), `UNPROCESSABLE_CONTENT` (422).

> **Aturan database:** register → insert satu `users` (`role = 'pelanggan'`) + satu `pelanggan` (1—1). Keduanya dalam satu transaksi.

### 2.2 `POST /api/v1/auth/login`

| Akses | Publik |
|---|---|
| Body | `email`, `password` |

Contoh request:

```json
{
  "email": "budi@example.com",
  "password": "rahasia123"
}
```

Contoh response:

```json
// HTTP 200
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "2|qwerty9876543210",
    "pelanggan": {
      "id": 4,
      "nama": "Budi Santoso",
      "email": "budi@example.com",
      "no_hp": "081234567890",
      "alamat": "Jl. Melati No. 12, Malang"
    }
  }
}
```

Error (kredensial salah):

```json
// HTTP 401
{
  "success": false,
  "message": "Email atau password salah",
  "data": null
}
```

> **Aturan database:** login hanya untuk akun **role `pelanggan`**. Admin/mekanik/owner tidak boleh login lewat API ini (mereka memakai session di Website). Kalau email terdaftar sebagai role lain, tolak dengan pesan "Akun ini tidak terdaftar sebagai pelanggan".

### 2.3 `POST /api/v1/auth/logout`

| Akses | Pelanggan (token) |
|---|---|
| Body | — |

Revoke token yang dipakai.

```json
// HTTP 200
{
  "success": true,
  "message": "Logout berhasil",
  "data": null
}
```

### 2.4 `GET /api/v1/profil`

| Akses | Pelanggan (token) — data milik sendiri |
|---|---|
| Query | — |

```json
// HTTP 200
{
  "success": true,
  "message": "Profil berhasil dimuat",
  "data": {
    "id": 4,
    "nama": "Budi Santoso",
    "email": "budi@example.com",
    "no_hp": "081234567890",
    "alamat": "Jl. Melati No. 12, Malang",
    "jumlah_mobil": 2
  }
}
```

### 2.5 `PUT /api/v1/profil`

| Akses | Pelanggan (token) — data milik sendiri |
|---|---|
| Body | `nama`, `email`, `no_hp`, `alamat`; `password` opsional (isi bila ingin ganti) |

```json
{
  "nama": "Budi Santosa",
  "email": "budi.baru@example.com",
  "no_hp": "081298765432",
  "alamat": "Jl. Anggrek No. 3, Malang",
  "password": "rahasiaBaru123"
}
```

```json
// HTTP 200
{
  "success": true,
  "message": "Profil diperbarui",
  "data": {
    "id": 4,
    "nama": "Budi Santosa",
    "email": "budi.baru@example.com",
    "no_hp": "081298765432",
    "alamat": "Jl. Anggrek No. 3, Malang"
  }
}
```

---

## 3. Modul Data Mobil Pelanggan

> Semua endpoint di modul ini **menscope ke pelanggan login**: hanya mobil milik pelanggan tersebut yang bisa dilihat/diubah/dipakai.

### 3.1 `GET /api/v1/mobil`

| Akses | Pelanggan (token) |
|---|---|
| Query | — |

```json
// HTTP 200
{
  "success": true,
  "message": "Daftar mobil",
  "data": [
    {
      "id": 11,
      "plat_nomor": "N 1234 AB",
      "merk": "Toyota",
      "tipe": "Avanza",
      "tahun": 2018
    },
    {
      "id": 12,
      "plat_nomor": "N 5678 CD",
      "merk": "Honda",
      "tipe": "Brio",
      "tahun": 2020
    }
  ]
}
```

### 3.2 `POST /api/v1/mobil`

| Akses | Pelanggan (token) |
|---|---|
| Body | `plat_nomor`, `merk`, `tipe`, `tahun` |

```json
{
  "plat_nomor": "N 9999 EF",
  "merk": "Suzuki",
  "tipe": "Ertiga",
  "tahun": 2021
}
```

```json
// HTTP 201
{
  "success": true,
  "message": "Mobil berhasil ditambahkan",
  "data": {
    "id": 13,
    "plat_nomor": "N 9999 EF",
    "merk": "Suzuki",
    "tipe": "Ertiga",
    "tahun": 2021
  }
}
```

Error: `UNPROCESSABLE_CONTENT` (422, termasuk saat `plat_nomor` duplikat).

### 3.3 `PUT /api/v1/mobil/{id}`

| Akses | Pelanggan (token) — hanya mobil milik sendiri |
|---|---|
| Body | `plat_nomor`, `merk`, `tipe`, `tahun` |

```json
{
  "plat_nomor": "N 9999 EF",
  "merk": "Suzuki",
  "tipe": "Ertiga",
  "tahun": 2022
}
```

Error: `NOT_FOUND` (404, mobil tidak ada) atau `FORBIDDEN` (403, mobil milik pelanggan lain).

---

## 4. Modul Layanan (Bacaan Publik)

### 4.1 `GET /api/v1/layanan`

| Akses | Publik (tanpa token) |
|---|---|
| Query | `q` opsional — filter nama layanan; `kategori` opsional jika `jenis_layanan` nanti punya kolom kategori |

```json
// HTTP 200
{
  "success": true,
  "message": "Daftar layanan",
  "data": [
    {
      "id": 1,
      "nama_layanan": "Ganti Oli Mesin",
      "harga_jasa": 150000.00,
      "estimasi_menit": 45
    },
    {
      "id": 2,
      "nama_layanan": "Servis Rem",
      "harga_jasa": 120000.00,
      "estimasi_menit": 60
    }
  ]
}
```

### 4.2 `GET /api/v1/layanan/{id}`

| Akses | Publik (tanpa token) |
|---|---|

```json
// HTTP 200
{
  "success": true,
  "message": "Detail layanan",
  "data": {
    "id": 1,
    "nama_layanan": "Ganti Oli Mesin",
    "harga_jasa": 150000.00,
    "estimasi_menit": 45
  }
}
```

Error: `NOT_FOUND` (404).

---

## 5. Modul Slot Jadwal (Disediakan Admin)

> **Inilah modul yang mengontrol pintu masuk booking.** Slot dibuat/diatur oleh Admin di Website (`POST /admin/slot`, dst. — lihat `konsep_pemrograman.md` § 5.1.1). API di bawah hanya **membaca**; tidak ada metode untuk membuat/mengubah slot dari sisi mobile. Bila tidak ada slot tersedia, pelanggan **tidak dapat mengajukan servis** — app harus menampilkan keadaan kosong dan menonaktifkan tombol booking.

### 5.1 `GET /api/v1/slot/tanggal-tersedia`

| Akses | Publik (tanpa token) |
|---|---|
| Query | `dari` opsional (default: hari ini), `sampai` opsional (default: +30 hari) |

Daftar tanggal yang masih punya **minimal satu slot lolos gate** (dibuka + belum penuh + belum lewat). Dipakai app untuk menandai tanggal aktif di kalender.

```json
// HTTP 200
{
  "success": true,
  "message": "Tanggal tersedia",
  "data": {
    "dari": "2026-09-16",
    "sampai": "2026-10-16",
    "tanggal_tersedia": [
      "2026-09-17",
      "2026-09-18",
      "2026-09-20",
      "2026-09-21"
    ]
  }
}
```

**Kosong:** bila admin belum membuka slot sama sekali pada rentang tersebut:

```json
// HTTP 200
{
  "success": true,
  "message": "Belum ada jadwal yang tersedia",
  "data": {
    "dari": "2026-09-16",
    "sampai": "2026-10-16",
    "tanggal_tersedia": []
  }
}
```

**Perilaku app saat kosong:** kalender & daftar slot tidak aktif; pesan info "Belum ada jadwal tersedia dari bengkel" ditampilkan; tombol booking dinonaktifkan.

### 5.2 `GET /api/v1/slot?tanggal=2026-09-20`

| Akses | Publik (tanpa token) |
|---|---|
| Query | `tanggal` **wajib** (`YYYY-MM-DD`) |

Daftar slot yang **lolos gate** pada tanggal tersebut. Urutan: `jam_mulai` naik.

```json
// HTTP 200
{
  "success": true,
  "message": "Slot tersedia pada 2026-09-20",
  "data": [
    {
      "id_slot": 21,
      "tanggal": "2026-09-20",
      "jam_mulai": "08:00",
      "jam_selesai": "10:00",
      "kapasitas": 3,
      "terisi": 1,
      "sisa_kuota": 2
    },
    {
      "id_slot": 22,
      "tanggal": "2026-09-20",
      "jam_mulai": "10:00",
      "jam_selesai": "12:00",
      "kapasitas": 3,
      "terisi": 3,
      "sisa_kuota": 0
    }
  ]
}
```

**Kosong:** tanggal tanpa slot tersedia (tidak dibuka, semua lewat, atau semua penuh) mengembalikan array kosong dengan `message` informatif:

```json
// HTTP 200
{
  "success": true,
  "message": "Tidak ada slot tersedia pada 2026-09-20",
  "data": []
}
```

**Perilaku app saat kosong:** dropdown/pemilihan slot kosong; pilihan layanan & mobil boleh tetap tampil namun tombol submit booking dinonaktifkan.

> **Catatan:** `sisa_kuota` (`kapasitas - terisi`) dihitung server. App boleh mengecilkan/menonaktifkan slot dengan `sisa_kuota = 0`, tapi validasi akhir tetap di server (lihat 6.1).

### 5.3 `GET /api/v1/slot/{id}`

| Akses | Publik (tanpa token) |
|---|---|

Detail satu slot. **Hanya** mengembalikan slot yang lolos gate; kalau slot ada tapi sudah penuh/ditutup, tetap ditolak (app seharusnya tidak pernah memanggil untuk slot yang sudah lewat gate).

```json
// HTTP 200
{
  "success": true,
  "message": "Detail slot",
  "data": {
    "id_slot": 21,
    "tanggal": "2026-09-20",
    "jam_mulai": "08:00",
    "jam_selesai": "10:00",
    "kapasitas": 3,
    "terisi": 1,
    "sisa_kuota": 2
  }
}
```

Error: `NOT_FOUND` (404) atau `SLOT_TIDAK_TERSEDIA` (422) bila slot ditutup/lewat/penuh.

---

## 6. Modul Pesanan / Booking Servis

### 6.1 `POST /api/v1/pesanan`

| Akses | Pelanggan (token) |
|---|---|
| Body | `id_mobil`, `id_slot`, `keluhan`, `detail_layanan[]` |

Ini endpoint **pusat booking** — dan titik ketergantungan terkuat pada slot. Prasyarat di sisi app: pengguna sudah memilih mobil, layanan, dan slot tersedia. Server **memvalidasi ulang** seluruh gate slot walaupun app sudah memuat daftar slot (bisa berubah sejak dimuat).

Contoh request:

```json
{
  "id_mobil": 11,
  "id_slot": 21,
  "keluhan": "Mesin bergetar saat idling, oli sudah 6000 km",
  "detail_layanan": [
    { "id_layanan": 1, "jumlah": 1 },
    { "id_layanan": 2, "jumlah": 1 }
  ]
}
```

Contoh response:

```json
// HTTP 201
{
  "success": true,
  "message": "Pesanan berhasil diajukan",
  "data": {
    "id_pesanan": 54,
    "kode_pesanan": "PMM-20260920-001",
    "status": "menunggu",
    "keluhan": "Mesin bergetar saat idling, oli sudah 6000 km",
    "waktu_pesan": "2026-09-16 09:24:11",
    "slot": {
      "id_slot": 21,
      "tanggal": "2026-09-20",
      "jam_mulai": "08:00",
      "jam_selesai": "10:00"
    },
    "detail_layanan": [
      { "id_layanan": 1, "nama_layanan": "Ganti Oli Mesin", "jumlah": 1, "harga_jasa": 150000.00, "subtotal": 150000.00 },
      { "id_layanan": 2, "nama_layanan": "Servis Rem", "jumlah": 1, "harga_jasa": 120000.00, "subtotal": 120000.00 }
    ],
    "total_biaya": 270000.00
  }
}
```

**Error — slot gagal gate (utama):**

```json
// HTTP 422
{
  "success": false,
  "message": "Slot tidak tersedia. Silakan pilih slot lain.",
  "data": {
    "kode": "SLOT_TIDAK_TERSEDIA",
    "alasan": "slot_ditutup | tanggal_lewat | kuota_penuh"
  }
}
```

**Error — mobil bukan milik pelanggan:**

```json
// HTTP 422
{
  "success": false,
  "message": "Mobil tidak ditemukan pada akun Anda.",
  "data": {
    "kode": "MOBIL_TIDAK_TERSEDIA",
    "errors": {
      "id_mobil": ["Mobil tidak ditemukan pada akun Anda."]
    }
  }
}
```

> **Aturan bisnis database yang harus dilakukan server secara atomik (satu transaksi):**
> 1. Validasi gate slot dengan `lockForUpdate()` pada baris `slot_jadwal`.
> 2. Insert `pesanan` (`status = 'menunggu'`, `kanal_pesan = 'mobile'`), `waktu_pesan = now()`.
> 3. Insert `detail_layanan` per item + hitung `subtotal`; hitung `total_biaya` = Σ subtotal.
> 4. Set `slot_jadwal.terisi = terisi + 1`.
> 5. (Sesuai konsep) insert `riwayat_status` baris pertama (`status = 'menunggu'`, `diubah_oleh = NULL` atau `users` pelanggan).

### 6.2 `GET /api/v1/pesanan`

| Akses | Pelanggan (token) — hanya pesanan milik sendiri |
|---|---|
| Query | `status` (opsional: `menunggu`, `diterima`, `ditolak`, `dikerjakan`, `selesai`); `page`, `per_page` |

Riwayat pesanan **terbaru dulu** (`waktu_pesan` desc).

```json
// HTTP 200
{
  "success": true,
  "message": "Riwayat pesanan",
  "data": [
    {
      "id_pesanan": 54,
      "kode_pesanan": "PMM-20260920-001",
      "tanggal_slot": "2026-09-20",
      "jam_mulai": "08:00",
      "jam_selesai": "10:00",
      "status": "menunggu",
      "total_biaya": 270000.00,
      "waktu_pesan": "2026-09-16 09:24:11"
    },
    {
      "id_pesanan": 51,
      "kode_pesanan": "PMM-20260918-001",
      "tanggal_slot": "2026-09-18",
      "jam_mulai": "10:00",
      "jam_selesai": "12:00",
      "status": "selesai",
      "total_biaya": 450000.00,
      "waktu_pesan": "2026-09-14 14:02:37"
    }
  ],
  "meta": {
    "total": 12,
    "per_page": 10,
    "current_page": 1,
    "last_page": 2
  }
}
```

### 6.3 `GET /api/v1/pesanan/{id}`

| Akses | Pelanggan (token) — hanya pesanan milik sendiri |
|---|---|

Detail pesanan + data terkait (mobil, slot, layanan, sparepart, pembayaran, penugasan).

```json
// HTTP 200
{
  "success": true,
  "message": "Detail pesanan",
  "data": {
    "id_pesanan": 54,
    "kode_pesanan": "PMM-20260920-001",
    "status": "menunggu",
    "status_terakhir_diubah": "2026-09-16 09:24:11",
    "keluhan": "Mesin bergetar saat idling, oli sudah 6000 km",
    "mobil": {
      "id": 11,
      "plat_nomor": "N 1234 AB",
      "merk": "Toyota",
      "tipe": "Avanza",
      "tahun": 2018
    },
    "slot": {
      "id_slot": 21,
      "tanggal": "2026-09-20",
      "jam_mulai": "08:00",
      "jam_selesai": "10:00"
    },
    "detail_layanan": [
      { "id_layanan": 1, "nama_layanan": "Ganti Oli Mesin", "jumlah": 1, "harga_jasa": 150000.00, "subtotal": 150000.00 }
    ],
    "detail_sparepart": [],
    "total_biaya": 150000.00,
    "pembayaran": {
      "id_pembayaran": 90,
      "status_bayar": "menunggu",
      "jumlah": 150000.00,
      "metode": null
    },
    "penugasan": [
      {
        "id_penugasan": 7,
        "nama_mekanik": "Andi Wijaya",
        "spesialis": "mesin",
        "waktu_mulai": null,
        "waktu_selesai": null,
        "catatan": null
      }
    ]
  }
}
```

Error: `NOT_FOUND` (404), `FORBIDDEN` (403) jika bukan pesanan pelanggan tersebut.

### 6.4 `GET /api/v1/pesanan/{id}/riwayat-status`

| Akses | Pelanggan (token) — hanya pesanan milik sendiri |
|---|---|

Histori perubahan status, urut menaik per waktu (fitur "pelacakan status").

```json
// HTTP 200
{
  "success": true,
  "message": "Riwayat status pesanan",
  "data": {
    "id_pesanan": 54,
    "kode_pesanan": "PMM-20260920-001",
    "riwayat": [
      { "status": "menunggu", "waktu": "2026-09-16 09:24:11" },
      { "status": "diterima", "waktu": "2026-09-16 10:05:40" },
      { "status": "dikerjakan", "waktu": "2026-09-20 08:02:15", "mekanik": "Andi Wijaya" }
    ]
  }
}
```

Error: `NOT_FOUND` (404), `FORBIDDEN` (403).

---

## 7. Modul Notifikasi

### 7.1 `GET /api/v1/notifikasi`

| Akses | Pelanggan (token) — notifikasi utk pesanan milik sendiri |
|---|---|
| Query | `belum_baca` (opsional, `1` = hanya belum dibaca); `page`, `per_page` |

```json
// HTTP 200
{
  "success": true,
  "message": "Daftar notifikasi",
  "data": [
    {
      "id_notifikasi": 31,
      "judul": "Pesanan Diterima",
      "pesan": "Pesanan PMM-20260920-001 Anda telah diterima dan dijadwalkan pada 20 September 2026 pukul 08:00.",
      "waktu_kirim": "2026-09-16 10:05:40",
      "dibaca": false,
      "id_pesanan": 54
    },
    {
      "id_notifikasi": 28,
      "judul": "Servis Selesai",
      "pesan": "Pesanan PMM-20260918-001 telah selesai dikerjakan. Silakan cek nota pembayaran.",
      "waktu_kirim": "2026-09-15 12:30:00",
      "dibaca": true,
      "id_pesanan": 51
    }
  ]
}
```

### 7.2 `PATCH /api/v1/notifikasi/{id}/baca`

| Akses | Pelanggan (token) — hanya milik sendiri |
|---|---|

Tandai sebagai dibaca. `idempotent`: memanggil lagi tidak error.

```json
// HTTP 200
{
  "success": true,
  "message": "Notifikasi ditandai dibaca",
  "data": {
    "id_notifikasi": 31,
    "dibaca": true
  }
}
```

Error: `NOT_FOUND` (404), `FORBIDDEN` (403).

---

## 8. Modul Nota & Pembayaran

### 8.1 `GET /api/v1/pesanan/{id}/nota`

| Akses | Pelanggan (token) — hanya pesanan milik sendiri |
|---|---|

Rincian nota digital (jasa + sparepart + total).

```json
// HTTP 200
{
  "success": true,
  "message": "Nota pesanan",
  "data": {
    "id_pesanan": 51,
    "kode_pesanan": "PMM-20260918-001",
    "status": "selesai",
    "tanggal_slot": "2026-09-18",
    "mobil": { "plat_nomor": "N 5678 CD", "merk": "Honda", "tipe": "Brio" },
    "detail_layanan": [
      { "nama_layanan": "Ganti Oli Mesin", "jumlah": 1, "harga_jasa": 150000.00, "subtotal": 150000.00 }
    ],
    "detail_sparepart": [
      { "nama": "Oli Mesin 5W-30", "satuan": "liter", "jumlah": 2, "harga": 85000.00, "subtotal": 170000.00 },
      { "nama": "Filter Oli", "satuan": "pcs", "jumlah": 1, "harga": 45000.00, "subtotal": 45000.00 }
    ],
    "subtotal_jasa": 150000.00,
    "subtotal_sparepart": 215000.00,
    "total_biaya": 365000.00
  }
}
```

Error: `NOT_FOUND` (404), `FORBIDDEN` (403).

> **Aturan database:** `total_biaya` tidak dihitung ulang di sini — dibaca dari `pesanan.total_biaya`. Nilai itu dijaga konsisten oleh server setiap kali detail layanan/sparepart berubah (lihat `konsep_database.md` § 5).

### 8.2 `GET /api/v1/pembayaran/{id}`

| Akses | Pelanggan (token) — hanya milik pesanan sendiri |
|---|---|
| Query | — |

```json
// HTTP 200
{
  "success": true,
  "message": "Detail pembayaran",
  "data": {
    "id_pembayaran": 90,
    "id_pesanan": 54,
    "kode_pesanan": "PMM-20260920-001",
    "jumlah": 150000.00,
    "status_bayar": "menunggu",
    "metode": null,
    "order_id": null,
    "waktu_bayar": null
  }
}
```

Error: `NOT_FOUND` (404), `FORBIDDEN` (403).

### 8.3 `POST /api/v1/pembayaran/{id}/konfirmasi`

| Akses | Pelanggan (token) — hanya data sendiri |
|---|---|
| Body | `id_metode` opsional (akibat pemakaian payment gateway internal); untuk tahap manual cukup tanpa body |

Mengubah `status_bayar` dari `menunggu` → `lunas` (atau memicu callback payment gateway).

```json
// kosong, atau:
{ "id_metode": 1 }
```

```json
// HTTP 200
{
  "success": true,
  "message": "Pembayaran dikonfirmasi",
  "data": {
    "id_pembayaran": 90,
    "jumlah": 150000.00,
    "status_bayar": "lunas",
    "waktu_bayar": "2026-09-16 11:12:03"
  }
}
```

Error: `NOT_FOUND` (404), `FORBIDDEN` (403), `PEMBAYARAN_LUNAS` (409) bila sudah lunas.

---

## 9. Matriks Ringkasan Endpoint

| Method | Endpoint (dengan prefix) | Auth | Deskripsi |
|---|---|---|---|
| POST | `/api/v1/auth/register` | Publik | Registrasi → token |
| POST | `/api/v1/auth/login` | Publik | Login → token |
| POST | `/api/v1/auth/logout` | Token | Revoke token |
| GET | `/api/v1/profil` | Token | Ambil profil |
| PUT | `/api/v1/profil` | Token | Perbarui profil |
| GET | `/api/v1/mobil` | Token | Daftar mobil |
| POST | `/api/v1/mobil` | Token | Tambah mobil |
| PUT | `/api/v1/mobil/{id}` | Token | Ubah mobil |
| GET | `/api/v1/layanan` | Publik | Daftar layanan |
| GET | `/api/v1/layanan/{id}` | Publik | Detail layanan |
| GET | `/api/v1/slot/tanggal-tersedia` | Publik | Tanggal yang punya slot tersedia |
| GET | `/api/v1/slot?tanggal=` | Publik | Slot tersedia per tanggal |
| GET | `/api/v1/slot/{id}` | Publik | Detail slot |
| POST | `/api/v1/pesanan` | Token | Ajukan booking (validasi gate slot) |
| GET | `/api/v1/pesanan` | Token | Riwayat pesanan |
| GET | `/api/v1/pesanan/{id}` | Token | Detail pesanan |
| GET | `/api/v1/pesanan/{id}/riwayat-status` | Token | Histori status |
| GET | `/api/v1/notifikasi` | Token | Daftar notifikasi |
| PATCH | `/api/v1/notifikasi/{id}/baca` | Token | Tandai dibaca |
| GET | `/api/v1/pesanan/{id}/nota` | Token | Rincian nota |
| GET | `/api/v1/pembayaran/{id}` | Token | Status pembayaran |
| POST | `/api/v1/pembayaran/{id}/konfirmasi` | Token | Konfirmasi bayar |

---

## 10. Open Items / Pertanyaan Terbuka

Berikut hal yang belum terkunci dan perlu keputusan (mungkin dari dosen/tim). Jawaban yang dipilih akan meng-update dokumen ini:

1. **Nota sebelum bayar atau sesudah?** Nota (8.1) tersedia untuk status `selesai` di contoh. Apakah pelanggan boleh melihat nota saat `dikerjakan` (pra-penagihan)? — *perlu keputusan.*
2. **Konfirmasi bayar tanpa payment gateway** — tahap awal cukup flag `lunas` manual di app? Atau langsung skema `order_id` (gateway) dari awal?
3. **Pembatalan dari sisi pelanggan** — apakah sarankan endpoint `PATCH /api/v1/pesanan/{id}/batal` (batalkan sebelum diterima, mengembalikan `terisi` ke semula)? Konsep saat ini belum punya status `dibatalkan`.
4. **Keluhan vs layanan** — di 6.1 pelanggan memilih layanan dari master. Apakah diizinkan menambah "layanan bebas/tidak standar"? Kalau ya, perlu kolom teks bebas.
5. **Detail sparepart saat booking** — `detail_sparepart` hanya diisi oleh mekanik/admin setelah pengerjaan; saat booking tidak diisi. Perlu konfirmasi bahwa alur ini benar.

---

## 11. Riwayat Perubahan Dokumen

| Tanggal | Perubahan |
|---|---|
| 2026-09-16 | Versi awal. Pecahan dari `konsep_pemrograman.md`: seluruh endpoint mobile dirinci (request/response/error). Menambah aturan ketersediaan slot (gate booking, § 1.4), tabel kode error (§ 1.5), modul slot sebagai bacaan publik yang disediakan admin (§ 5), perilaku app dikala daftar slot kosong, langkah atomik `POST /pesanan` (§ 6.1), dan daftar pertanyaan terbuka (§ 10) |