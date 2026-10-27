# Penjelasan Autentikasi API Mobile — PMM AutoCare

**Pertanyaan:** Apakah REST API `/api/v1` untuk aplikasi mobile memerlukan *key*?
Dan bagaimana cara admin men-*generate* key tersebut?

**Jawaban singkat:** API ini **tidak memakai API Key**, melainkan
**Bearer Token (Laravel Sanctum)** yang dibuat **otomatis saat pelanggan login**.
Admin **tidak perlu** — dan memang **tidak bisa** — membuatkan token untuk pelanggan.

---

## 1. Bedakan Dua Istilah yang Sering Tertukar

| Istilah | Contoh | Pemilik | Sifat |
|---|---|---|---|
| **API Key** | `X-API-KEY: abc123` | Aplikasi / server | Satu kunci untuk seluruh aplikasi, jarang berubah |
| **Bearer Token** ← *dipakai proyek ini* | `Authorization: Bearer 1\|fxPqZ...` | Per **pengguna** | Satu token per pelanggan, punya masa hidup |

Proyek PMM AutoCare memakai **Bearer Token Sanctum**, bukan API Key.

---

## 2. Kenapa Bukan API Key?

Konsep sistem ini menetapkan bahwa **satu backend melayani banyak pelanggan**
dengan data yang terpisah (`konsep_pemrograman.md` § 2):

```
Pelanggan A  →  hanya boleh melihat pesanan milik A
Pelanggan B  →  hanya boleh melihat pesanan milik B
```

Kalau memakai **satu API Key global**, aplikasi mobile memegang satu kunci yang
bisa mengakses **seluruh** data pelanggan. Server tidak punya cara membedakan
siapa yang sedang mengirim permintaan.

Karena itu digunakan **token per pengguna**. Justru inilah yang membuat aturan
scoping yang sudah dibangun bisa bekerja:

> Pelanggan yang mencoba membuka pesanan milik pelanggan lain
> akan menerima **403 FORBIDDEN**, bukan 404.

Aturan itu hanya mungkin ditegakkan kalau setiap permintaan membawa identitas
pengguna — dan identitas itu datang dari token, bukan dari API Key.

---

## 3. Siapa yang Membuat Token?

**Pelanggan sendiri, secara otomatis.** Bukan admin.

Lokasi di kode — `app/Http/Controllers/Api/V1/AuthController.php`:

| Endpoint | Baris | Yang terjadi |
|---|---|---|
| `POST /api/v1/auth/register` | 49 | `$user->createToken('mobile')->plainTextToken` |
| `POST /api/v1/auth/login` | 90 | `$user->createToken('mobile')->plainTextToken` |

### Alurnya

```
1. Pelanggan membuka aplikasi mobile
2. Pelanggan daftar (register) atau login
        ↓
3. Server membuat token acak
   → yang disimpan di database hanyalah HASH-nya
     (tabel personal_access_tokens, kolom `token`)
        ↓
4. Token dikirim sekali ke aplikasi
   → disimpan di penyimpanan aman perangkat
        ↓
5. Setiap permintaan berikutnya menyertakan header:
        Authorization: Bearer <token>
```

**Poin penting:** server **tidak menyimpan token asli**, hanya hash-nya. Jadi
kalau database bocor, token tidak bisa dipakai ulang oleh penyerang.

---

## 4. Tugas Admin Apa Kalau Bukan Generate Key?

Admin tidak mengelola token, tetapi mengelola hal-hal di sekitarnya:

| Kebutuhan | Status di proyek ini | Keterangan |
|---|---|---|
| Membuat akun pelanggan | ❌ Tidak perlu | Pelanggan mendaftar sendiri dari aplikasi mobile |
| Melihat token aktif pelanggan | ⚠️ Belum ada UI | Datanya ada di tabel `personal_access_tokens` |
| Mencabut token pelanggan | ⚠️ Belum ada UI | Saat ini hanya pelanggan sendiri lewat `logout` |
| Menonaktifkan pelanggan agar tak bisa login | ⚠️ Belum ada | Belum ada kolom status aktif di `tb_user` |

Yang **sudah** ada: `POST /api/v1/auth/logout` — mencabut token milik
pelanggan yang sedang login (`currentAccessToken()->delete()`).

---

## 5. Endpoint Mana yang Perlu Token?

Dari 21 endpoint yang terdaftar, **14 perlu token** dan **7 publik**.

### Publik (tanpa token) — 7 endpoint

| Method | Endpoint | Alasan dibiarkan publik |
|---|---|---|
| `POST` | `/api/v1/auth/register` | Untuk membuat akun |
| `POST` | `/api/v1/auth/login` | Untuk memperoleh token |
| `GET` | `/api/v1/layanan` | Daftar tarif, dilihat sebelum mendaftar |
| `GET` | `/api/v1/layanan/{id}` | Detail tarif |
| `GET` | `/api/v1/slot` | Ketersediaan jadwal |
| `GET` | `/api/v1/slot/tanggal-tersedia` | Kalender tanggal tersedia |
| `GET` | `/api/v1/slot/{id}` | Detail satu slot |

Ini sesuai `konsep_api.md` § 1.3: *"Semua endpoint kecuali `auth/login`,
`auth/register`, `layanan`, dan `slot` (mode baca) **wajib** menyertakan token."*

### Perlu token — 14 endpoint

`auth/logout` · `profil` (GET, PUT) · `mobil` (GET, POST, PUT) ·
`pesanan` (POST, GET, GET detail, riwayat-status, batal, nota) ·
`notifikasi` (GET, PATCH baca)

Semuanya dijaga oleh middleware `auth:sanctum` di `routes/api.php` baris 46.

---

## 6. Kalau Token Tidak Sah

Server membalas dengan bentuk seragam (`bootstrap/app.php` baris 79):

```json
// HTTP 401
{
  "success": false,
  "message": "Unauthenticated.",
  "data": { "kode": "UNAUTHENTICATED" }
}
```

| Kode | HTTP | Kapan |
|---|---|---|
| `UNAUTHENTICATED` | 401 | Token tidak ada / salah / kedaluwarsa |
| `FORBIDDEN` | 403 | Token sah, tetapi data milik pelanggan lain |
| `RATE_LIMIT` | 429 | Terlalu banyak permintaan |

**Perbedaan 401 vs 403 ini penting** dan sering ditanyakan: 401 berarti
"kamu belum terbukti siapa", 403 berarti "kamu sudah terbukti, tapi ini bukan
datamu".

---

## 7. Pengaturan Token di Proyek Ini

Berkas `config/sanctum.php`:

| Setting | Nilai sekarang | Arti |
|---|---|---|
| `expiration` | `null` | **Token tidak pernah kedaluwarsa** — berlaku sampai logout |
| `token_prefix` | `''` (kosong) | Token tampil seperti `1\|fxPqZyl3...` |
| `guard` | `['web']` | Guard yang dicek Sanctum saat autentikasi |

### Batas permintaan (`app/Providers/AppServiceProvider.php`)

| Nama | Batas | Dasar pembatasan |
|---|---|---|
| `api` | 60 permintaan/menit | Per pengguna (atau per IP bila belum login) |
| `auth` | 10 permintaan/menit | Per IP — lebih ketat, mencegah coba-coba password |

`expiration => null` **aman untuk demo perkuliahan**, tetapi untuk aplikasi
sungguhan biasanya diisi (misalnya 30 hari) agar token yang bocor punya masa
hidup terbatas. Ini pilihan sadar, bukan kelalaian.

---

## 8. Yang Belum Ada: Pengenalan Aplikasi

Ada satu kebutuhan nyata yang **belum** ditangani: saat ini **siapa pun** bisa
memanggil `POST /api/v1/auth/register` dan membuat akun.

Kalau nanti bengkel ingin membatasi agar **hanya aplikasi mobile resmi** yang
boleh mendaftar, solusinya barulah **API Key** — bukan untuk pelanggan, tetapi
untuk **mengenali aplikasinya**:

```
X-App-Key: <kunci milik aplikasi mobile>     ← mengenali APLIKASI
Authorization: Bearer <token pelanggan>       ← mengenali PENGGUNA
```

Keduanya bisa dipakai bersamaan dan fungsinya berbeda.

**Tetapi ini belum diperlukan sekarang**, dan tidak tercantum di
`backend/konsep_api.md`. Kalau nanti ditambahkan, barulah muncul kebutuhan
"admin men-*generate* key" yang sebenarnya.

---

## 9. Cara Menguji Cepat

```sh
# 1. Login — server membalas token
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"email":"budi@example.com","password":"password"}'

# Balasan:
# { "success": true, "message": "Login berhasil",
#   "data": { "token": "1|fxPqZyl3cOBjlIiW...", "pelanggan": { ... } } }

# 2. Pakai token untuk endpoint yang dilindungi
curl http://127.0.0.1:8000/api/v1/profil \
  -H "Authorization: Bearer 1|fxPqZyl3cOBjlIiW..." \
  -H "Accept: application/json"

# 3. Tanpa token → 401 UNAUTHENTICATED
curl http://127.0.0.1:8000/api/v1/profil -H "Accept: application/json"

# 4. Token milik pelanggan A, membuka data pelanggan B → 403 FORBIDDEN
```

---

## 10. Ringkasan

> **API mobile PMM AutoCare tidak memakai API Key.**
> Ia memakai **Bearer Token Sanctum** yang dibuat **otomatis saat pelanggan
> login**, disimpan di perangkat, dan dikirim pada setiap permintaan lewat
> header `Authorization: Bearer <token>`.
>
> **Admin tidak perlu men-generate token**, karena token itu mewakili **sesi
> login pelanggan**, bukan kunci aplikasi. Yang mengelola token adalah
> pelanggan itu sendiri (login untuk mendapat, logout untuk mencabut).
>
> API Key baru akan diperlukan **hanya jika** nanti ingin membatasi aplikasi
> mana yang boleh mengakses API — dan itu belum termasuk lingkup konsep saat ini.

---

## Rujukan Berkas

| Berkas | Isi terkait |
|---|---|
| `routes/api.php` | Daftar endpoint + middleware `auth:sanctum` |
| `app/Http/Controllers/Api/V1/AuthController.php` | Pembuatan token (baris 49, 90) |
| `app/Models/User.php` | Trait `HasApiTokens` |
| `config/sanctum.php` | Masa berlaku & prefix token |
| `app/Providers/AppServiceProvider.php` | Batas permintaan `api` & `auth` |
| `bootstrap/app.php` | Bentuk respons 401 / 403 / 429 |
| `database/migrations/*_create_personal_access_tokens_table.php` | Tabel penyimpan token |
| `.docs/backend/konsep_api.md` § 1.3 | Aturan endpoint mana yang perlu token |
