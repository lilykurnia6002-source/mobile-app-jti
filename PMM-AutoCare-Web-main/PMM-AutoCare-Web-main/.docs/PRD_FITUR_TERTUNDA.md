# PRD & Spesifikasi Teknis Fitur Tertunda

**Produk:** PMM AutoCare — sistem informasi pelayanan dan penjadwalan servis mobil  
**Dokumen:** handoff product/technical requirements untuk backend web + database  
**Status dokumen:** PROPOSED — spesifikasi belum berarti fitur sudah dibangun  
**Tanggal:** 2026-09-23  
**Pemilik keputusan:** Product Owner / pemilik bengkel  
**Implementer:** penanggung jawab backend web/database

> Dokumen ini sengaja memisahkan fakta yang terlihat di repository dari proposal fitur
> berikutnya. Semua endpoint, kolom, status, job, dan aturan yang diberi label
> **PROPOSED** belum boleh dianggap tersedia sebelum implementasi dan verifikasi selesai.

---

## 1. Ringkasan keputusan dan tujuan

### 1.1 Masalah

Aplikasi mobile sudah dapat menampilkan pesanan dan riwayat status, tetapi kontrak
backend saat ini belum mempunyai sumber data terstruktur untuk:

- waktu mulai pengerjaan yang dijanjikan/diestimasi selesai;
- countdown yang konsisten terhadap jam perangkat pelanggan;
- ambang merah saat waktu hampir habis;
- deteksi servis melewati deadline;
- reschedule deadline yang dapat diaudit dan diberitahukan kepada pelanggan.

Mockup mobile pernah menampilkan tracker enam langkah dan `estimasi selesai`, tetapi
repository mobile sendiri mencatat bahwa keduanya belum didukung data backend. PRD ini
mengganti asumsi UI tersebut dengan kontrak data yang eksplisit; PRD **tidak**
menghidupkan kembali status UI yang tidak ada di database.

### 1.2 Tujuan

1. Menetapkan satu definisi deadline servis dan countdown yang berasal dari server.
2. Menjaga status pesanan existing tetap kompatibel.
3. Membuat setiap penetapan/reschedule deadline atomik, idempoten, dan dapat diaudit.
4. Memberi admin cara menangani overrun tanpa menghapus jadwal lama.
5. Memberi mobile payload yang cukup untuk render tanpa mengandalkan jam perangkat.
6. Menjaga modul owner report yang sudah berjalan tetap terpisah dari ekspor file dan
   dari pembayaran.

### 1.3 Non-goals

- Tidak mengubah enam status pesanan existing menjadi pipeline enam langkah.
- Tidak mengimplementasikan payment gateway.
- Tidak mengklaim push notification tersedia; saat ini notifikasi pelanggan dibaca
  dengan polling.
- Tidak menambah akun/login mekanik; mekanik tetap data master tanpa akun.
- Tidak mengubah kuota slot secara diam-diam ketika deadline di-reschedule.
- Tidak membuat endpoint produksi sebelum keputusan product owner pada bagian 16
  disetujui.

---

## 2. Bukti repository dan kondisi saat ini

### 2.1 Arsitektur yang sudah ada

| Komponen | Fakta repository |
|---|---|
| Backend | Laravel 13.31.0, PHP 8.4.x, Laravel Sanctum; `composer.json`, `API.md` |
| Database | Model/tabel domain memakai prefix `tb_`, primary key kamus data seperti `id_pesanan`; `database/migrations/` |
| Klien mobile | Flutter feature-first, Riverpod, `http`, secure storage; `pmmauto_mobile/HANDOFF.md` dan `lib/features/` |
| Website | Blade panel `/admin`, session auth, middleware `admin`; `routes/web.php` |
| API | Prefix `/api/v1`, envelope `{success, message, data}`; `routes/api.php`, `ApiResponse` |
| Waktu aplikasi | Laravel default timezone UTC; business gate memakai `BUSINESS_TIMEZONE`, default `Asia/Jakarta`; `config/app.php`, `SlotJadwal` |
| Auth pelanggan/pemilik API | Bearer token Sanctum; admin/pemilik website memakai session. Repository saat ini juga menerima role `pemilik` pada login API; `AuthController` |
| Auth mekanik | Tidak ada; semua input pengerjaan dicatat admin melalui `dicatat_oleh`; `Pengerjaan`, konsep database |

Arsitektur target tetap satu backend dan satu database sebagai source of truth untuk
mobile serta website. Mobile dan website tidak boleh membuat deadline lokal yang tidak
tercatat server.

### 2.2 Model data existing yang relevan

Tabel/titik kode berikut benar-benar ada sekarang:

- `tb_slot_jadwal`: `tanggal`, `jam_mulai`, `jam_selesai`, `kapasitas`, `terisi`,
  `status` (`dibuka`/`ditutup`), unique `(tanggal, jam_mulai)`.
- `tb_pesanan`: `kode_pesanan`, pelanggan, mobil, `id_slot` nullable untuk walk-in,
  `status` (`menunggu`, `diterima`, `ditolak`, `dikerjakan`, `selesai`,
  `dibatalkan`), `total_biaya`, `waktu_pesan`, `alasan_tolak`.
- `tb_pengerjaan`: satu atau lebih assignment/proses dengan mekanik, `waktu_mulai`,
  `waktu_selesai`, `catatan`, dan admin pencatat.
- `tb_riwayat_status`: riwayat status + keterangan + actor + `waktu`.
- `tb_notifikasi`: notifikasi terikat ke pesanan; kepemilikan pelanggan diturunkan
  melalui pesanan; `dibaca` masih boolean.
- `tb_pembayaran` dan `tb_metode_bayar`: tabel sudah ada, endpoint pembayaran belum
  menjadi kontrak aktif.

### 2.3 Baseline yang sudah diimplementasikan

#### Booking, slot, dan pelanggan

- `GET /api/v1/slot/tanggal-tersedia`, `GET /api/v1/slot`, dan
  `GET /api/v1/slot/{id}` memakai gate availability.
- Gate existing: status `dibuka`, gabungan `tanggal + jam_mulai` harus lebih besar
  secara ketat dari waktu bisnis `Asia/Jakarta`, dan `terisi < kapasitas`.
- `POST /api/v1/pesanan` memakai `BookingService::buatPesanan()` dalam transaksi;
  slot dikunci `lockForUpdate()`, mobil di-scope ke pelanggan, harga layanan di-snapshot,
  `terisi` bertambah, dan riwayat status pertama dicatat.
- **Kuota slot berstatus PARTIAL, bukan otomatis penuh:** booking, pembatalan, dan
  penolakan admin otomatis menambah/mengurangi `terisi`; pembuatan slot serta
  provisioning `kapasitas` tetap manual oleh admin melalui website. Tidak ada automatic
  slot generation atau perhitungan kapasitas dari jumlah mekanik.
- Pembatalan pelanggan hanya saat `menunggu`, idempoten, mengembalikan kuota dalam
  transaksi. Penolakan admin juga mengembalikan kuota dengan lock.
- `kode_pesanan` server-generated dengan format `PMM-YYYYMMDD-NNN`.
- Alur booking confirmation di mobile sudah ada: review + CTA melakukan `POST
  /api/v1/pesanan`, menampilkan acknowledgement sukses, invalidasi data, lalu menuju
  riwayat. Ini adalah konfirmasi pengajuan, bukan endpoint terpisah atau persetujuan
  admin; order tetap berstatus `menunggu`.

#### Status, admin, dan notifikasi

- Status pesanan existing hanya enam nilai flat di `Pesanan`; jangan mengirim status
  enam langkah baru tanpa migration dan keputusan produk.
- Website admin sudah memiliki daftar/detail pesanan, terima + assign mekanik, tolak,
  dan tandai selesai pada `routes/web.php` + `Admin\\PesananController`.
- Perubahan admin yang sudah ada menulis `tb_riwayat_status` dan membuat baris
  `tb_notifikasi` untuk pelanggan.
- API mobile sudah memiliki detail pesanan, riwayat status, nota, daftar notifikasi,
  serta tandai notifikasi dibaca.

#### Owner report

- Backend sudah memiliki dan diuji:
  - `GET /api/v1/reports/transactions?start=&end=`;
  - `GET /api/v1/reports/productivity?start=&end=`;
  - `GET /api/v1/reports/non-cash-ratio?start=&end=`.
- Akses route report dilindungi Sanctum + middleware `admin` yang mengizinkan role
  admin dan pemilik. Filter tanggal inklusif, timezone bisnis `Asia/Jakarta`.
- Mobile sudah memanggil ketiga endpoint melalui
  `lib/features/reports/data/repositories/owner_report_repository.dart` dan
  menampilkan layar owner report.
- Statusnya **PARTIAL** pada level produk: API + layar mobile + ekspor lokal mobile
  ada, tetapi halaman owner di website dan export backend belum ada.
- Ekspor Excel/PDF yang terlihat pada mobile adalah **ekspor lokal di perangkat**
  (`excel`, `pdf`, `path_provider`, `share_plus`). Itu bukan endpoint ekspor backend,
  bukan file yang digenerate server, dan bukan bukti bahwa backend menyediakan CSV,
  Excel, atau PDF.

### 2.4 Gaps yang benar-benar masih ada

- `tb_pesanan` belum mempunyai deadline/ETA/red threshold.
- Tidak ada kontrak API countdown, `server_now`, state warning, atau overrun.
- Tidak ada riwayat revisi deadline.
- Tidak ada job/scheduler untuk menandai atau memberi notifikasi overrun.
- Tidak ada endpoint reschedule deadline atau workflow slot swap/reschedule.
- Endpoint pembayaran pelanggan/admin belum dibangun; resource pembayaran masih dapat
  mengembalikan `null`.
- Password produksi belum aman: `User::casts()` tidak memakai cast `hashed` dan
  `AuthController::login()` membandingkan password secara plain text untuk demo.
- Timezone berstatus **PARTIAL**: gate slot dan report memakai business timezone,
  tetapi serialisasi API/mobile display belum konsisten dan mobile memiliki clock
  hard-coded UTC+7.
- Test coverage berstatus **PARTIAL**: backend menguji baseline booking/slot/report,
  tetapi belum ada test countdown, reschedule, payment flow, production password,
  export backend/website, dan cross-platform timezone.
- Provisioning pemilik belum mempunyai alur production-grade yang terdokumentasi.
- Push FCM/WebSocket belum tersedia; polling adalah mekanisme yang ada.
- Status implementasi lama di `.docs/status_implementasi.md` menyebut beberapa modul
  admin sebagai fase berikutnya; repository terkini sudah memiliki subset admin (slot,
  order accept/reject/complete, mekanik). Handoff harus mengikuti route/kode terkini,
  bukan menyalin klaim lama tanpa verifikasi.

### 2.5 Status matrix permintaan dan gap audit

| Permintaan/area | Status saat ini | Bukti ringkas | Batasan yang wajib disebut |
|---|---|---|---|
| Automatic slot quota/provisioning | **PARTIAL** | Booking, cancel, reject mengubah `terisi` atomik dengan lock; admin membuat slot + menetapkan `kapasitas` | Tidak ada automatic slot generation atau kalkulasi kapasitas dari mekanik. |
| Strict past-slot cutoff | **IMPLEMENTED** | `SlotJadwal::scopeTersedia()` memakai `jam_mulai >` waktu bisnis + kuota; test boundary ada | Server authority; mobile hanya mirror/filter. |
| Countdown/deadline/red threshold | **NOT IMPLEMENTED** | Tidak ada field, route, calculator, atau UI deadline; `tb_pengerjaan` hanya punya start/end aktual | `estimasi_menit` layanan bukan deadline order; jangan infer ETA dari mockup. |
| Reschedule | **NOT IMPLEMENTED** | Tidak ada endpoint/transaction/history reschedule; mobile menyatakan ubah jadwal belum tersedia | Deadline reschedule dan slot swap harus dipisahkan; keduanya perlu policy + audit. |
| Owner report API + mobile | **IMPLEMENTED** | Tiga API report diuji; mobile fetch/render | Status produk keseluruhan tetap PARTIAL karena website owner page absent. |
| Owner report website/backend export | **NOT IMPLEMENTED** | Tidak ada owner report route website atau generator/API file backend | XLSX/PDF yang ada hanya dibuat dan dibagikan lokal di mobile. |
| Booking confirmation | **IMPLEMENTED** | Mobile CTA POST `pesanan`, success acknowledgement, invalidate, open history | Ini acknowledgement pengajuan; bukan customer-confirm endpoint dan bukan approval admin. |
| Payment | **NOT IMPLEMENTED** | Tabel/model ada, route/controller/mobile flow belum ada | Conflict verifikasi admin vs self-confirm harus diputuskan dulu. |
| Security/password | **PARTIAL, critical risk** | Sanctum, throttle, secure token storage ada; password masih plaintext dan token expiration null | Production wajib hash/check, migration/reset, expiry/revoke, no demo credentials. |
| Timezone | **PARTIAL** | Backend gate/report explicit `Asia/Jakarta`; mobile clock/display dan API datetime offset belum konsisten | Final contract wajib canonical UTC/offset + business timezone. |
| Tests/docs | **PARTIAL** | Backend baseline tests ada; mobile feature tests dan deferred-feature tests belum ada; docs drift | Tambah regression matrix untuk countdown, reschedule, payment, security, timezone, export. |

Status di tabel ini adalah status repository saat audit, bukan acceptance status untuk
fitur PROPOSED. Semua implementasi baru tetap harus mengikuti label **PROPOSED** di
bagian requirement/API/migration berikut.



---

## 3. Terminologi dan state machine PROPOSED

### 3.1 Definisi waktu

| Istilah | Definisi PROPOSED |
|---|---|
| Waktu mulai appointment | Awal slot booking (`tanggal + jam_mulai`); bukan otomatis waktu mekanik mulai bekerja |
| `waktu_mulai` aktual | Timestamp ketika admin mencatat pengerjaan dimulai pada `tb_pengerjaan` |
| Deadline servis | `target_selesai_at`: target penyelesaian yang disimpan server untuk pesanan aktif |
| Ambang merah | `target_selesai_at - red_threshold_minutes`; awal fase warning |
| Overrun | Waktu server sudah mencapai/melewati deadline sementara pesanan belum terminal |
| Reschedule | Perubahan target selesai aktif; deadline lama tetap berada di riwayat |
| Versi deadline | Nomor monoton per pesanan; dipakai untuk idempotensi/deduplikasi notifikasi |

Semua timestamp yang disimpan di database **PROPOSED** menggunakan UTC. API mengirim
ISO-8601 dengan offset atau UTC (`Z`) serta `timezone: Asia/Jakarta` agar mobile tidak
menebak zona.

### 3.2 Status pesanan existing tetap dipakai

Tidak ada status baru `warning` atau `overdue` pada kolom `tb_pesanan.status`.
Countdown state adalah nilai turunan dari status terminal/non-terminal, deadline aktif,
dan `server_now`:

| `countdown.state` | Kondisi PROPOSED |
|---|---|
| `not_applicable` | Tidak ada deadline atau pesanan ditolak/dibatalkan sebelum ada deadline |
| `pending` | Deadline ada, status `diterima`, tetapi `waktu_mulai` aktual belum tercatat |
| `normal` | Pengerjaan aktif dan sisa waktu > threshold |
| `warning` | Pengerjaan aktif, sisa waktu > 0 dan <= threshold |
| `overdue` | Pengerjaan aktif/non-terminal, sisa waktu <= 0 |
| `completed` | Status `selesai` |

`remaining_seconds` dihitung server sebagai `max(0, target_selesai_at - server_now)`.
Mobile boleh mengurangi angka tersebut secara lokal untuk animasi, tetapi refresh API
harus mengoreksi clock drift.

### 3.3 Transisi utama PROPOSED

1. Admin menerima pesanan dan menetapkan deadline pertama → `deadline_version = 1`.
2. Admin mencatat mulai pengerjaan → countdown berubah dari `pending` menjadi `normal`
   atau `warning` sesuai sisa waktu.
3. Scheduler/read model mendeteksi waktu melewati deadline → state `overdue`, tanpa
   menambah status order baru.
4. Admin mengirim reschedule dengan waktu baru + alasan → versi bertambah, deadline
   baru aktif, riwayat + notifikasi dibuat dalam transaksi.
5. Admin menandai selesai → countdown `completed`; tidak boleh ada reschedule baru.
6. Tolak/batal → countdown `not_applicable` dan deadline tidak lagi actionable.

---

## 4. Requirement countdown, deadline, red threshold, dan overrun

Semua butir pada bagian ini **PROPOSED** kecuali jika disebut baseline.

### 4.1 Penetapan deadline

**Wajib:**

- Deadline harus dapat ditetapkan ketika pesanan diterima/ditugaskan atau ketika
  pengerjaan mulai, sesuai keputusan product owner.
- Request wajib memakai datetime yang eksplisit; backend tidak boleh memakai jam
  perangkat mobile sebagai sumber kebenaran.
- `target_selesai_at` harus lebih besar dari waktu mulai aktual jika sudah ada; untuk
  pesanan belum dimulai, minimal lebih besar dari `server_now`.
- `red_threshold_minutes` harus integer non-negatif dan lebih kecil dari durasi yang
  disetujui, kecuali product owner mengizinkan threshold sama dengan durasi.
- Nilai uang, slot, dan deadline yang sudah tersimpan tidak dihitung ulang oleh mobile.
- Deadline pertama memiliki `deadline_version = 1` dan event audit `deadline_set`.

**Default yang diusulkan, menunggu keputusan:** threshold 30 menit. Default ini tidak
boleh dikodekan sebelum product owner menyetujui karena jenis layanan dapat mempunyai
durasi berbeda.

### 4.2 Countdown response

Detail pesanan mobile **PROPOSED** menambahkan bentuk berikut ke `data`:

```json
{
  "deadline": {
    "target_selesai_at": "2026-09-23T10:30:00Z",
    "red_threshold_at": "2026-09-23T10:00:00Z",
    "red_threshold_minutes": 30,
    "deadline_version": 1,
    "state": "warning",
    "remaining_seconds": 900,
    "server_now": "2026-09-23T10:15:00Z",
    "timezone": "Asia/Jakarta"
  }
}
```

Kontrak penting:

- `remaining_seconds` integer, tidak negatif.
- `server_now` ada pada response agar mobile mengukur drift.
- `deadline: null` untuk order lama/jenis layanan yang belum diberi deadline; UI harus
  menampilkan fallback informatif, bukan countdown palsu.
- Timestamp yang ditampilkan pengguna dikonversi ke `Asia/Jakarta`.
- Mobile tidak boleh membuat status `overdue` permanen hanya karena app lama dibuka;
  state setelah refresh harus mengikuti server.

### 4.3 Red threshold dan notifikasi

**PROPOSED:**

- Saat deadline memasuki red threshold, buat maksimal satu notifikasi per
  `(id_pesanan, deadline_version, event_type=deadline_warning)`.
- Saat overrun terdeteksi, buat maksimal satu notifikasi per versi deadline untuk
  `(id_pesanan, deadline_version, event_type=deadline_overdue)`.
- Jika deadline di-reschedule, versi baru boleh menghasilkan warning/overdue baru;
  notifikasi versi lama tidak boleh diulang.
- Polling `GET /api/v1/notifikasi` tetap menjadi delivery baseline. Push channel adalah
  fase terpisah.
- Pesan notifikasi harus menyebut kode pesanan, state, dan tindakan berikutnya; jangan
  menjanjikan kendaraan selesai bila belum ada konfirmasi.

### 4.4 Workshop overrun dan reschedule

**PROPOSED:**

- Overrun adalah kondisi turunan, bukan status order baru.
- Admin dashboard menampilkan filter `overdue=1` berdasarkan `server_now`, deadline
  aktif, dan status non-terminal.
- Admin wajib memilih salah satu tindakan:
  1. menyelesaikan servis lalu menandai selesai; atau
  2. mengirim reschedule dengan target baru dan alasan; atau
  3. memakai keputusan bisnis lain yang disetujui pada bagian open decisions.
- Reschedule harus menyimpan deadline lama, deadline baru, threshold baru, actor,
  alasan, waktu perubahan, dan versi.
- Deadline baru harus lebih besar dari `server_now` dan, untuk reschedule akibat
  overrun, lebih besar dari deadline lama.
- Reschedule tidak otomatis memindahkan `id_slot`, tidak mengubah `terisi`, dan tidak
  membuka kuota baru. Perubahan appointment slot adalah workflow terpisah.
- Order terminal (`selesai`, `ditolak`, `dibatalkan`) menolak reschedule dengan 409.
- Klik ganda atau retry request dengan idempotency key yang sama mengembalikan hasil
  yang sama dan tidak menggandakan history/notifikasi.

---

## 5. Skema database dan migration plan PROPOSED

### 5.1 Perubahan additive pada `tb_pesanan`

Migration baru, tidak mengedit migration lama yang sudah mungkin dijalankan:

| Kolom | Tipe usulan | Aturan |
|---|---|---|
| `target_selesai_at` | `datetime` nullable | UTC; deadline aktif saat ini |
| `red_threshold_minutes` | `unsignedInteger` nullable/default sesuai keputusan | threshold untuk versi aktif |
| `deadline_version` | `unsignedInteger` default 0 | 0 = belum pernah ditetapkan |
| `overdue_detected_at` | `datetime` nullable | optional materialized marker; boleh dihapus jika selalu derived |

Rekomendasi: jangan menyimpan `remaining_seconds` atau `red_threshold_at` karena keduanya
mudah stale dan dapat dihitung dari target + threshold. Jika `overdue_detected_at`
disimpan, nilainya harus ditulis idempoten dan tidak menjadi sumber truth state.

Index usulan: `(status, target_selesai_at)` untuk filter dashboard/scheduler. Semua
constraint yang tidak portable di SQLite juga wajib ditegakkan di service validation
serta diuji di MySQL.

### 5.2 Tabel riwayat deadline/reschedule

**PROPOSED migration:** `tb_jadwal_pesanan` (nama final dapat diputuskan, jangan
menggunakan tabel ini sebelum disetujui).

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_jadwal` | bigint PK | identifier |
| `id_pesanan` | FK `tb_pesanan` | cascade sesuai kebijakan order |
| `versi` | unsigned int | unik per pesanan, mulai 1 |
| `target_selesai_at` | datetime | target untuk versi ini |
| `red_threshold_minutes` | unsigned int | threshold versi ini |
| `berlaku_mulai_at` | datetime | kapan versi aktif |
| `berlaku_sampai_at` | datetime nullable | null untuk versi aktif |
| `jenis_perubahan` | string/enum | `initial`, `reschedule`, `manual_correction` |
| `alasan` | text nullable | wajib untuk reschedule/correction |
| `dibuat_oleh` | FK `tb_user` nullable | null hanya untuk system event yang disetujui |
| timestamps | — | created/updated |

Invariants:

- unique `(id_pesanan, versi)`;
- maksimum satu row aktif (`berlaku_sampai_at IS NULL`) per order, ditegakkan service
  transaction + unique strategy yang sesuai engine database;
- versi baru tidak boleh menimpa row lama;
- perubahan active row dan snapshot di `tb_pesanan` wajib satu transaksi;
- history tidak boleh dihapus dari UI biasa.

Alternatif lebih sederhana: simpan seluruh nilai historis hanya di `tb_riwayat_status`
metadata. Pilihan ini hanya boleh dipakai bila kebutuhan query/audit disetujui; tabel
khusus di atas lebih mudah untuk rekonsiliasi dan reporting.

### 5.3 Perluasan `tb_riwayat_status`

**PROPOSED:** pertahankan `status` existing untuk kompatibilitas, tambah:

- `jenis_peristiwa` string default `status_changed`;
- `metadata` JSON nullable untuk `deadline_version`, old/new target, threshold,
  reason, dan source (`admin`, `system`, `mobile`);
- index `(id_pesanan, waktu, jenis_peristiwa)`.

Contoh event: `deadline_set`, `deadline_warning`, `deadline_overdue`,
`deadline_rescheduled`. Event tersebut tidak boleh mengarang status order baru.

### 5.4 Perluasan `tb_notifikasi`

**PROPOSED:** tambah:

- `jenis` string, mis. `deadline_warning`, `deadline_overdue`, `deadline_rescheduled`;
- `dedupe_key` string unique untuk event yang harus idempoten;
- `data` JSON nullable (order code, deadline version, timestamps);
- `dibaca_at` nullable datetime; pertahankan `dibaca` untuk backward compatibility
  sementara migration/backfill berjalan.

Kepemilikan pelanggan tetap melalui `id_pesanan` agar scoping existing tidak rusak.
Jika owner/admin juga memerlukan notifikasi personal, itu adalah perluasan terpisah
(`id_user` nullable + policy), bukan perubahan diam-diam pada scope pelanggan.

### 5.5 Existing payment tables tetap terpisah

`tb_pembayaran` sudah memiliki status `menunggu`, `menunggu_verifikasi`, `lunas`,
`ditolak`, bukti transfer, actor verifikasi, dan nominal. PRD countdown tidak mengubah
status pembayaran. Migration pembayaran/API adalah dependency masa depan tersendiri.

---

## 6. Transaksi, invariants, dan concurrency PROPOSED

### 6.1 Set deadline awal

Dalam satu `DB::transaction()`:

1. `tb_pesanan` diambil `lockForUpdate()`.
2. Validasi actor, status, dan deadline terhadap server time.
3. Insert row versi deadline.
4. Update snapshot deadline di `tb_pesanan`.
5. Insert `tb_riwayat_status` event `deadline_set`.
6. Commit.
7. Notification delivery boleh melalui outbox/queue setelah commit; jangan kirim
   network call eksternal di dalam transaksi DB.

### 6.2 Reschedule

Dalam satu transaksi yang sama:

1. lock order;
2. baca active deadline + versi;
3. validasi idempotency key dan kondisi terminal/overdue;
4. tutup row jadwal aktif (`berlaku_sampai_at`);
5. insert versi baru;
6. update snapshot order;
7. insert event history + notifikasi dengan `dedupe_key`;
8. commit.

Concurrent reschedule harus menghasilkan satu versi yang menang; request lain menerima
409 `DEADLINE_CONFLICT` atau hasil idempoten bila key sama. Jangan mengandalkan hanya
validasi controller tanpa row lock.

### 6.3 Scheduler/derived overrun

**PROPOSED:** Laravel scheduler menjalankan command periodik (mis. tiap menit) untuk
mencari order non-terminal dengan deadline terlewati dan membuat event/notifikasi secara
idempoten. Query harus batch dan memakai index. Jika scheduler terlambat, API tetap
menghitung state `overdue` dari `server_now`; scheduler hanya untuk materialisasi
history/notifikasi, bukan sumber kebenaran deadline.

### 6.4 Invariant lintas modul

- `target_selesai_at` tidak boleh mengubah `kapasitas`/`terisi` slot.
- Cancel/reject dan reschedule tidak boleh mengurangi kuota dua kali.
- Order terminal tidak menerima deadline update.
- `deadline_version` monoton dan tidak boleh mundur.
- Actor harus role admin/pemilik sesuai policy; system event harus dapat dibedakan dari
  aksi manual.
- Semua status transition existing tetap menulis history; event deadline menambah
  audit, bukan menghapus status history.
- API mobile tidak boleh mengembalikan deadline milik order pelanggan lain; policy
  existing 403 untuk data milik pihak lain dipertahankan.

---

## 7. API contract PROPOSED

Semua response mengikuti envelope existing `{success, message, data}` dan kode error
stabil. Endpoint di bawah **belum ada** kecuali endpoint baseline yang disebut pada
bagian 2.3.

### 7.1 Mobile: detail pesanan diperluas

`GET /api/v1/pesanan/{id}`  
**Akses:** Sanctum pelanggan pemilik order; admin/pemilik hanya bila policy API
mengizinkan, bukan otomatis.  
**Response:** payload existing + `deadline` object pada bagian 4.2.  
**Error:** existing `UNAUTHENTICATED`, `FORBIDDEN`, `NOT_FOUND`.

Backward compatibility: field baru nullable; app versi lama harus tetap dapat decode
response tanpa field deadline.

### 7.2 Mobile: riwayat status/event diperluas

`GET /api/v1/pesanan/{id}/riwayat-status`  
**Akses:** pelanggan pemilik order.  
**Response PROPOSED:** setiap item tetap memiliki status existing dan boleh memiliki
`jenis_peristiwa`, `metadata`, `waktu`. Client lama mengabaikan field tambahan.

### 7.3 Admin: tetapkan deadline awal

`POST /api/v1/admin/pesanan/{id}/deadline` **PROPOSED**

Request:

```json
{
  "target_selesai_at": "2026-09-23T10:30:00+07:00",
  "red_threshold_minutes": 30,
  "idempotency_key": "client-generated-uuid"
}
```

Response `201`:

```json
{
  "success": true,
  "message": "Deadline servis ditetapkan",
  "data": {
    "id_pesanan": 42,
    "deadline_version": 1,
    "target_selesai_at": "2026-09-23T03:30:00Z",
    "red_threshold_at": "2026-09-23T03:00:00Z",
    "state": "pending"
  }
}
```

Authorization: admin; pemilik hanya jika product owner memang memberi hak operasional.
Pelanggan tidak boleh menetapkan deadline.

### 7.4 Admin: reschedule deadline

`POST /api/v1/admin/pesanan/{id}/reschedule` **PROPOSED**

Request:

```json
{
  "target_selesai_at": "2026-09-23T12:00:00+07:00",
  "red_threshold_minutes": 30,
  "alasan": "Menunggu sparepart yang kompatibel",
  "expected_deadline_version": 1,
  "idempotency_key": "client-generated-uuid"
}
```

Response `200` memakai envelope yang sama dan mengembalikan versi baru + deadline aktif.
`alasan` wajib, panjang minimum/maksimum mengikuti policy produk.

### 7.5 Admin: daftar order overdue

`GET /api/v1/admin/pesanan?state=overdue&start=&end=` **PROPOSED**

Response list harus memuat `id_pesanan`, `kode_pesanan`, status existing, customer,
mechanic aktif, target selesai, remaining seconds (0), dan deadline version.
Pagination wajib; jangan query seluruh order tanpa batas.

### 7.6 Endpoint yang tidak dibuat

Tidak diperlukan endpoint `POST /overdue` untuk pelanggan. Overdue diturunkan dari
waktu server. Jika admin perlu men-trigger scheduler manual, command/internal admin
operation harus dipisahkan dari kontrak mobile dan diaudit.

### 7.7 Error contract PROPOSED

| Kode | HTTP | Kondisi |
|---|---:|---|
| `UNAUTHENTICATED` | 401 | token hilang/tidak valid |
| `FORBIDDEN` | 403 | role/data bukan milik actor |
| `NOT_FOUND` | 404 | order tidak ada |
| `UNPROCESSABLE_CONTENT` | 422 | format/field request invalid |
| `DEADLINE_INVALID` | 422 | target <= server now, threshold invalid, atau tanggal tidak valid |
| `DEADLINE_NOT_ALLOWED` | 422 | status order belum memenuhi aturan penetapan |
| `PESANAN_TERMINAL` | 409 | order selesai/ditolak/dibatalkan |
| `DEADLINE_CONFLICT` | 409 | versi berubah oleh request lain |
| `IDEMPOTENCY_REPLAY` | 200 | key sama, response hasil lama dikembalikan |
| `RATE_LIMIT` | 429 | batas request terlampaui |
| `SERVER_ERROR` | 500 | error tak terduga, tanpa detail sensitif |

`message` dapat dilokalkan; `data.kode` stabil untuk mobile/website.

---

## 8. Authorization, password, dan provisioning owner

### 8.1 Role policy

- Pelanggan: hanya membaca countdown/riwayat/notifikasi order sendiri.
- Admin: mengelola slot, antrian, status, mekanik, deadline, reschedule, dan payment
  verification setelah modul payment disetujui.
- Pemilik: default-nya read-only report dan dashboard; hak mengubah deadline adalah
  **open decision**, bukan asumsi dari role report.
- Mekanik: tidak punya token/akun; admin adalah actor pencatat.

Gunakan policy/gate berbasis `role` + ownership, bukan ID dari request sebagai bukti
kepemilikan. `id_user` actor diambil dari auth context.

### 8.2 Password production wajib

Kondisi saat ini adalah demo only: password disimpan/dibandingkan plain text. Sebelum
production:

1. `User` memakai cast `'password' => 'hashed'` atau `Hash::make()` saat create/update.
2. Login memakai `Hash::check()`/`Auth::attempt()`, bukan `===`.
3. Backfill hash dilakukan terkontrol. Password plain text existing tidak boleh dicetak
   ke log; bila tidak dapat dibedakan aman, paksa reset password.
4. Seeder demo tidak boleh berjalan di production; semua credential demo harus diganti.
5. Validasi password minimum, rate limit auth, HTTPS, secure session cookie, CSRF untuk
   website, dan token expiration/revocation Sanctum ditetapkan via environment.
6. Token tidak pernah masuk log/analytics; secret dan bukti transfer disimpan private.

### 8.3 Provisioning owner

**PROPOSED:** tidak ada public registration untuk role pemilik. Provisioning melalui:

- command/operator bootstrap sekali jalan yang meminta email + password interaktif;
- atau invite token sekali pakai yang kedaluwarsa dan memaksa set password baru;
- audit actor, waktu, dan IP untuk perubahan role/password;
- least privilege: default pemilik report read-only; admin operasional terpisah;
- disable/revoke token saat owner keluar atau role diturunkan.

Akun pemilik demo `pemilik@pmm-auto.test` tidak boleh dipakai sebagai production
credential.

---

## 9. Slot, kuota, dan timezone

Aturan baseline wajib dipertahankan:

1. Slot publik hanya lolos bila `status= dibuka`, waktu mulai gabungan lebih besar
   ketat dari waktu bisnis, dan `terisi < kapasitas`.
2. `terisi` dikunci dalam transaksi pada booking/cancel/reject.
3. Kuota tidak boleh turun di bawah `terisi` saat admin edit.
4. Unique `(tanggal, jam_mulai)` tetap.
5. `jam_selesai` tidak memperpanjang jendela booking.
6. Slot walk-in memiliki `id_slot = null`.

Aturan PROPOSED untuk deadline:

- Slot appointment dan deadline pengerjaan adalah konsep berbeda. Deadline tidak boleh
  dihitung otomatis dari `jam_selesai` tanpa keputusan durasi layanan.
- Penyimpanan UTC; business policy dan tampilan `Asia/Jakarta`.
- Semua API menerima offset/ISO-8601; server menormalisasi lalu mengembalikan UTC +
  timezone bisnis.
- Scheduler memakai clock server yang sinkron (NTP); jangan memakai `today()` tanpa
  timezone bisnis pada query deadline.
- Jika reschedule juga memindahkan tanggal appointment, buat workflow terpisah yang
  mengunci slot lama + baru dalam urutan deterministic, memvalidasi capacity, dan
  mencatat perubahan kuota. PRD ini hanya mencakup reschedule deadline.

---

## 10. Owner report, exports, dan payment dependency

### 10.1 Owner report — sudah ada

Backend report bukan fitur tertunda utama. Status saat ini:

- tiga endpoint API report sudah tersedia dan diuji di `ReportTest`;
- admin + pemilik dapat mengakses report API;
- mobile owner report memanggil endpoint tersebut dengan filter `start/end`;
- timezone/report period dikembalikan oleh backend.

Perubahan deadline **PROPOSED** nantinya dapat menambah metrik seperti jumlah overdue,
rata-rata reschedule, dan completion-vs-deadline, tetapi jangan mengubah makna report
existing tanpa versioning atau acceptance baru.

### 10.2 Exports — berbeda dari report API

Saat ini mobile menggenerate Excel/PDF secara lokal lalu membagikannya dari perangkat.
Backend tidak menyediakan endpoint export atau MIME file. Jika backend export diperlukan,
itu adalah feature terpisah **PROPOSED**:

- endpoint async + signed download URL;
- permission report yang sama;
- batas periode, audit download, dan redaction PII;
- format file/version yang terdokumentasi;
- jangan mengembalikan JSON dengan `Content-Type` Excel/PDF palsu.

### 10.3 Payment — dependency masa depan terpisah

Tabel payment sudah ada, tetapi endpoint dan workflow belum aktif. Keputusan yang harus
dipertahankan:

- countdown/deadline tidak menunggu modul payment;
- status payment tidak menjadi status pesanan baru;
- rekomendasi existing adalah upload bukti oleh pelanggan lalu verifikasi admin,
  karena schema memiliki `bukti_transfer` dan `diverifikasi_oleh`;
- ada konflik dokumen lama yang menyebut pelanggan dapat langsung mengonfirmasi lunas;
  product owner harus memilih sebelum endpoint payment dibangun;
- payment acceptance mencakup idempotensi, nominal terhadap `total_biaya`, file upload
  private, actor verifikasi, dan audit.

---

## 11. Tanggung jawab mobile integration

### 11.1 Mobile wajib

- Decode field `deadline` nullable tanpa merusak app versi lama.
- Render countdown dari `remaining_seconds` + `server_now`; koreksi drift saat refresh.
- Tampilkan state `pending`, `normal`, `warning`, `overdue`, `completed`, dan fallback
  `not_applicable` sesuai kontrak final.
- Jangan hardcode tracker enam langkah, ETA, atau status yang tidak ada di response.
- Refresh detail/notification setelah app resume dan setelah timer mencapai nol.
- Tampilkan alasan reschedule dan timestamp baru dari server.
- Tangani 401 (logout/re-auth), 403 (ownership), 409 conflict, 422 validation, 429,
  dan offline retry tanpa menggandakan request.
- Polling notifikasi tetap dipakai sampai push channel resmi tersedia.
- Simpan token di secure storage; jangan menyimpan deadline sebagai source of truth.

### 11.2 Backend wajib menyediakan untuk mobile

- response schema nullable dan version-compatible;
- `server_now`, timezone, version deadline, state, dan remaining seconds;
- stable error codes;
- riwayat event yang dapat ditampilkan manusia;
- dedupe/idempotency response untuk retry.

### 11.3 Mobile bukan tanggung jawab backend

- animasi timer per detik;
- format Rupiah/tanggal lokal;
- pembuatan Excel/PDF lokal (sudah ada untuk report);
- akses langsung database atau menghitung ulang kuota.

---

## 12. Acceptance criteria

### 12.1 Deadline/countdown

- [ ] Admin berwenang dapat menetapkan deadline valid pada order yang diizinkan.
- [ ] Deadline tersimpan UTC dan API mengembalikan waktu + timezone secara eksplisit.
- [ ] Detail order tanpa deadline mengembalikan `deadline: null`, bukan angka palsu.
- [ ] `remaining_seconds` tidak negatif dan konsisten dengan `server_now`.
- [ ] State berubah normal → warning tepat pada threshold dan overdue tepat pada deadline.
- [ ] Order selesai menampilkan `completed` dan tidak berubah kembali menjadi overdue.

### 12.2 Reschedule/overrun

- [ ] Order overdue terlihat admin melalui query/filter yang terindeks.
- [ ] Reschedule memerlukan target baru, threshold, alasan, dan actor sah.
- [ ] Deadline lama tetap ada di history; versi baru monoton.
- [ ] Reschedule atomik: snapshot, history, dan notification tidak boleh parsial.
- [ ] Retry idempotency key tidak membuat versi/notifikasi ganda.
- [ ] Reschedule tidak memodifikasi slot quota.
- [ ] Order terminal menolak reschedule dengan kode 409 yang stabil.

### 12.3 Security/operations

- [ ] Production login memakai password hash; plaintext login path dihapus.
- [ ] Owner dibuat melalui provisioning aman, bukan public registration/default password.
- [ ] Pelanggan tidak dapat melihat deadline/order pelanggan lain.
- [ ] Semua deadline mutation tercatat actor/time/reason/IP bila audit policy mengharuskan.
- [ ] Scheduler terlambat tidak membuat state API salah.
- [ ] Tidak ada secret, token, password, atau bukti transfer di log.

### 12.4 Compatibility

- [ ] Endpoint existing booking/slot/cancel/report tetap lolos regression suite.
- [ ] Mobile lama mengabaikan field baru tanpa crash.
- [ ] Order historis tanpa data deadline tetap dapat dilihat, nota tetap konsisten.
- [ ] Report existing dan export lokal mobile tetap berjalan.

---

## 13. Test matrix

| Area | Skenario minimum | Expected |
|---|---|---|
| Validasi waktu | target di masa lalu, threshold negatif, threshold terlalu besar | 422 `DEADLINE_INVALID` |
| State | normal, tepat threshold, satu detik sebelum/tepat deadline, overdue | state dan seconds sesuai server clock |
| Terminal | selesai, ditolak, dibatalkan lalu set/reschedule | 409/422 sesuai contract; tidak ada mutation |
| Pending | deadline ada, status diterima, belum ada `waktu_mulai` | `pending`, bukan running palsu |
| Warning dedupe | scheduler dijalankan berkali-kali pada versi sama | satu history/notifikasi |
| Reschedule | valid, alasan kosong, target baru <= deadline lama | valid pertama; reject invalid |
| Concurrency | dua reschedule berbeda versi bersamaan | satu menang; satu 409; tidak ada split history |
| Idempotency | request sama + key sama diulang | response sama; satu versi |
| Ownership | pelanggan A membaca order B; pelanggan mengubah deadline | 403/unauthenticated |
| RBAC | pelanggan, admin, pemilik pada endpoint mutation | policy sesuai keputusan |
| Slot | booking/cancel/reject concurrent + reschedule | quota tepat sekali; deadline tidak mengubah quota |
| Timezone | UTC midnight vs Jakarta date, DST irrelevant for Jakarta | period/gate/countdown konsisten |
| API compatibility | response field tambahan pada client lama | decode existing tidak rusak |
| Reports | report existing setelah kolom deadline ditambah | aggregate lama tidak berubah |
| Security | password migration/login, token revoke, brute-force limit | hash/check, revoke, throttle |
| Scheduler | no overdue, batch overdue, scheduler delay | idempotent event; API derived state benar |
| Migration | existing order/history/payment data | migration reversible sesuai policy, no data loss |

Target implementer: unit test untuk calculator/state machine, feature test endpoint dan
policy, integration test MySQL untuk lock/unique constraints, serta satu smoke test
mobile API terhadap response nyata. Jangan hanya menguji status HTTP; assert invariant,
actor, history, dedupe, dan quota.

---

## 14. Rollout, migration, dan backfill

### 14.1 Fase rollout PROPOSED

1. **Decision freeze:** product owner menyetujui terminology, threshold, actor,
   reschedule, payment conflict, dan owner provisioning.
2. **Schema additive:** tambah kolom/index/tabel baru; deploy migration tanpa mengubah
   data existing.
3. **Read-compatible API:** kirim `deadline: null` untuk order lama; mobile lama tetap
   aman.
4. **Backfill policy:** jangan menebak deadline dari `jam_selesai` slot atau mockup.
   Existing order mendapat null kecuali admin/product owner memberi data sumber yang
   dapat diverifikasi.
5. **Write enablement:** aktifkan set deadline untuk order baru/aktif melalui feature
   flag; audit semua mutation.
6. **Scheduler shadow mode:** hitung kandidat overdue dan log metric tanpa notification;
   rekonsiliasi hasil.
7. **Notification enablement:** aktifkan warning/overdue setelah dedupe diuji.
8. **Mobile release:** rilis decoder/state UI; setelah adoption cukup, wajibkan field
   sesuai policy baru.
9. **Cleanup:** setelah retention/backward-compatibility disetujui, hapus compatibility
   bridge, bukan data audit.

### 14.2 Backfill

- `deadline_version = 0`, semua deadline nullable untuk historical orders.
- Jangan mengisi `red_threshold_minutes` default pada order lama bila itu dapat memberi
  kesan deadline pernah disepakati.
- Jika business menyediakan daftar ETA manual, import harus dry-run, report conflict,
  menyimpan actor `system/import`, dan menghasilkan event `deadline_set` yang diberi
  metadata `source=backfill`.
- Backfill tidak boleh menyentuh `terisi`, total biaya, status, atau payment.

### 14.3 Rollback

Migration harus additive dan rollback-safe secara operasional. Sebelum rollback schema,
nonaktifkan writes, drain scheduler/queue, export audit, dan pastikan mobile dapat
menangani `deadline: null`. Jangan drop history deadline untuk rollback aplikasi.

---

## 15. Observability, audit, dan operasi

### 15.1 Metrics PROPOSED

- `deadline_set_total{role,source}`
- `deadline_rescheduled_total{reason}`
- `deadline_overdue_total`
- `deadline_warning_notification_total`
- `deadline_notification_deduplicated_total`
- `deadline_api_conflict_total`
- `deadline_scheduler_lag_seconds`
- `deadline_completion_before_deadline_ratio`
- `deadline_null_active_order_total`

Dashboard harus memisahkan overdue karena data deadline null dari overdue nyata.

### 15.2 Structured logs

Log JSON minimal: `request_id`, `id_pesanan`, `kode_pesanan`, `deadline_version`,
`event_type`, `actor_user_id`, `role`, result, latency. Redact password, bearer token,
bukti transfer, alamat lengkap, dan PII yang tidak perlu. Log old/new deadline hanya
bila policy privacy mengizinkan; bila tidak, log version + hash/reference.

### 15.3 Audit trail

Audit minimal menyimpan actor, waktu UTC, event, old/new values, alasan, request ID,
dan source. `tb_riwayat_status` adalah baseline status history; deadline table + event
metadata menjadi detail domain. Audit tidak boleh dapat diedit melalui CRUD normal.

### 15.4 Alerting dan reconciliation

Alert bila:

- scheduler lag melewati SLA;
- jumlah active order tanpa deadline melewati threshold setelah rollout;
- notification insert gagal/retry berulang;
- active deadline ganda terdeteksi;
- snapshot `tb_pesanan` berbeda dari row deadline aktif;
- `terisi` negatif atau melebihi kapasitas.

Sediakan command rekonsiliasi read-only terlebih dahulu; command repair harus
idempoten, dry-run, dan membutuhkan authorization operator.

---

## 16. Open decisions untuk Product Owner

Keputusan berikut wajib ditulis sebelum implementasi dianggap siap:

1. Apakah deadline ditetapkan saat pesanan diterima, saat mekanik ditugaskan, atau saat
   pengerjaan mulai?
2. Apakah deadline adalah target informasi atau SLA yang memicu kompensasi?
3. Default `red_threshold_minutes`: 15, 30, persentase durasi, atau per jenis layanan?
4. Apakah pemilik boleh mengubah deadline, atau hanya admin operasional?
5. Apakah pelanggan harus menyetujui reschedule, atau notifikasi admin cukup?
6. Berapa kali reschedule diperbolehkan? Apakah target baru wajib lebih besar dari
   target lama dalam semua kasus?
7. Apakah overrun otomatis mengubah status bisnis, atau tetap state turunan seperti
   proposal ini?
8. Kapan countdown mulai: slot mulai, `waktu_mulai` aktual, atau saat admin menerima?
9. Apa yang terjadi jika admin lupa menetapkan deadline? `null`, default durasi, atau
   order tidak dapat masuk `dikerjakan`?
10. Apakah reschedule deadline boleh mengubah appointment slot/kuota? Proposal ini
    menjawab tidak.
11. Berapa SLA scheduler dan notification polling/push yang diinginkan?
12. Retention history/audit dan PII berapa lama?
13. Apakah owner report perlu metrik deadline/overdue pada fase pertama?
14. Apakah backend export resmi diperlukan, atau ekspor lokal mobile sudah cukup?
15. Payment: upload bukti lalu admin verifikasi, atau customer confirmation langsung?
16. Token Sanctum production expiration, reset password, dan offboarding owner?
17. Apakah deadline dapat diubah untuk walk-in tanpa slot? Proposal ini memungkinkan jika
    order memiliki konteks pengerjaan yang valid, tetapi aturan final harus eksplisit.
18. Apakah notifikasi warning/overdue hanya polling, atau FCM/email/WhatsApp masuk
    milestone berikutnya?

---

## 17. Handoff checklist untuk backend web/database

### Product contract

- [ ] Semua open decision bagian 16 memiliki jawaban tertulis.
- [ ] Status existing vs countdown state disetujui.
- [ ] Role mutation dan owner provisioning disetujui.
- [ ] Payment dan export dipisahkan dari milestone countdown.

### Database

- [ ] Migration additive untuk snapshot deadline, history version, event metadata, dan
      notification dedupe.
- [ ] Index, FK, unique, nullable/backfill policy, dan timezone storage direview.
- [ ] Invariant concurrency diuji di MySQL, bukan hanya SQLite.
- [ ] Data migration/backfill dry-run menghasilkan report.

### Backend/API

- [ ] Policy authorization dan ownership test ada.
- [ ] Calculator countdown memiliki clock injectable untuk test boundary.
- [ ] Set/reschedule transaction memakai row lock + idempotency.
- [ ] API envelope/error code/version documented.
- [ ] Scheduler/outbox/notification dedupe selesai dan punya retry policy.
- [ ] Existing slot, booking, cancel, admin, report, dan payment-null behavior tidak
      rusak.

### Security

- [ ] Plaintext password path dihapus; hash/backfill/reset selesai.
- [ ] Production env tidak memakai demo credentials/seeder.
- [ ] Sanctum expiration/revoke, HTTPS, CSRF/session, rate limit, secret redaction,
      dan upload-private policy ditetapkan.
- [ ] Owner invite/bootstrap dan offboarding direhearsal.

### Mobile coordination

- [ ] Mobile owner menerima schema final dan contoh response/error.
- [ ] Mobile tidak memakai mock six-step/ETA.
- [ ] Countdown memakai server time dan menangani null/overdue/reschedule.
- [ ] Polling/push decision dan release sequencing disepakati.

### Verification and launch

- [ ] Test matrix bagian 13 lulus, termasuk MySQL concurrency.
- [ ] Smoke: set deadline → warning → overdue → reschedule → complete.
- [ ] Metrics/log/alert/dashboard tersedia.
- [ ] Rollout feature flag dan rollback runbook disetujui.
- [ ] PRD, API contract, migration notes, dan keputusan final di-commit bersama
      implementation PR.

---

## 18. Referensi repository

- `API.md` — envelope, endpoint mobile, gate slot, report API, payment/export status.
- `.docs/backend/konsep_api.md` — kontrak API mobile existing.
- `.docs/konsep_rancanganBasisData.md` — ERD dan kamus data.
- `.docs/konsep_pemrograman.md` — role/platform dan usulan website.
- `.docs/status_implementasi.md` — snapshot implementasi lama; cocokkan dengan source
  terkini sebelum menggunakannya sebagai status final.
- `routes/api.php`, `routes/web.php` — route aktual.
- `app/Services/BookingService.php`, `app/Services/PesananService.php` — transaksi
  booking/cancel dan history.
- `app/Models/SlotJadwal.php`, `Pesanan.php`, `Pengerjaan.php`, `RiwayatStatus.php`,
  `Notifikasi.php`, `Pembayaran.php` — model dan relasi aktual.
- `app/Http/Controllers/Api/V1/ReportController.php` + `tests/Feature/Api/V1/ReportTest.php`
  — owner report API yang sudah berjalan.
- `app/Models/User.php` + `app/Http/Controllers/Api/V1/AuthController.php` — kondisi
  password demo yang wajib di-hardening sebelum production.
- `pmmauto_mobile/HANDOFF.md` — batasan mobile: dashboard live, pesanan/notifikasi
  wiring, fake six-step tracker, dan known gotcha.
- `pmmauto_mobile/lib/features/reports/` — owner report API client + export lokal.
