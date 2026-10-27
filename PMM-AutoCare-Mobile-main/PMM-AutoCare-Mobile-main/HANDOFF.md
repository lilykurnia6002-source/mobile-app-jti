# PMM AutoCare mobile — handoff

Written 2026-09-21. Context dump for continuing this work in a different
tool/session. Read this before touching code.

## Repo layout

Two sibling repos, one folder up from each other:

```
pmmauto/
  pmmauto-web/       Laravel backend + admin web panel (git repo)
  pmmauto_mobile/     Flutter app (NOT a git repo, no version control yet)
```

## Backend (pmmauto-web)

Laravel, MySQL (`DB_DATABASE=PMM_AUTOCARE`), Sanctum token auth. Real schema,
real seeded demo data (bengkel/car workshop domain, Indonesian naming).

Run: `php artisan serve` from `pmmauto-web/`. Default port 8000 — **check
nothing else is on 8000 first**, this machine also runs an unrelated
freelance project (`bengkel-web-prod`) that competes for the same port.

Demo login (seeded): `budi@example.com` / `password`. Password hashing is
**intentionally disabled** right now (`User::casts()` has no `'password' =>
'hashed'`, `AuthController::login` does plain `===` compare) — a teammate's
change, commented as "demo perkuliahan", not something I reverted. If login
ever fails with `UNAUTHENTICATED` after a fresh clone/migrate, run
`php artisan db:seed` (or `migrate:fresh --seed` for full reset) — a stale
bcrypt hash from before that change won't match anymore.

API prefix: `/api/v1`, response envelope everywhere is
`{success, message, data}` (see `app/Http/Responses/ApiResponse.php`).

Endpoint built for the mobile dashboard:
- `GET /api/v1/dashboard` (auth:sanctum) → `DashboardController::index` +
  `DashboardResource`. Returns `nama`, `jumlah_mobil`, `servis_aktif`
  (nullable — latest pesanan with status in menunggu/diterima/dikerjakan,
  includes `mobil` + full `riwayat_status` array), `riwayat` (last 3
  `selesai` orders).

Real `Pesanan.status` enum is **only 6 flat values**:
`menunggu, diterima, ditolak, dikerjakan, selesai, dibatalkan`
(`app/Models/Pesanan.php` constants). No multi-step pipeline field exists
anywhere in the schema — see "known gotcha" below, this bit us once already.

Existing controllers relevant to next mobile work: `PesananController`
(index/show/riwayat-status/batal), `MobilController`, `ProfilController`,
`NotifikasiController` — all already built server-side, none wired to
mobile yet except dashboard.

## Mobile (pmmauto_mobile)

Flutter, feature-first structure under `lib/features/<name>/{data,views}`.
Stack: `go_router` (navigation) + `flutter_riverpod` (state, **no
codegen/Freezed** — deliberate call to keep it light, add codegen later
only if the app actually grows enough to need it) + plain `http` package +
`flutter_secure_storage` (token) + `google_fonts` (Plus Jakarta Sans) +
`intl` (Indonesian currency/date formatting).

Design source: a Google Stitch mockup project ("Partner Mobile App UI",
screen "Beranda — PMM AUTO"). Colors/fonts in
`lib/common/theme/app_theme.dart` are the exact Stitch design tokens (navy
`#1F3864`, gold `#FEBF33`, status colors, etc).

### What's real (wired to live backend)

- **Auth**: `features/auth/` — login screen, `AuthController`
  (`AsyncNotifier<bool>`), token stored via `flutter_secure_storage`.
  `app_router.dart` redirect logic enforces it: logged out → `/login`,
  logged in → `/beranda`.
- **Dashboard**: `features/dashboard/` — `dashboard_provider.dart` calls
  the real `GET /dashboard` endpoint. `ServisAktifCard` shows the real
  status badge + a real `riwayat_status` timeline
  (`riwayat_status_timeline.dart`, dynamic length, not a fixed step count).

### What's still fake/local (NOT wired to backend)

- **Pesanan feature** (`features/pesanan/`): `booking_orders_provider.dart`
  is a `StateNotifier` seeded with hardcoded `_initialBookings` — no
  network calls at all. `booking_order_model.dart`'s `status` values
  (`'sedang dikerjakan'`, etc) and `currentStep` (1–6, mapped to
  Diterima/Cek Awal/Estimasi/Pengerjaan/Finishing/Selesai) **do not match
  the real backend enum** — this is invented UI-only state with no DB
  column behind it.
- `service_progress_tracker.dart` (the fixed 6-step visual) is still used
  by `booking_detail_screen.dart` for this fake data. It was **removed**
  from the dashboard's `ServisAktifCard` for the same reason (real data has
  no such field) — don't reintroduce it there without a real field to back
  it.
- `notifikasi_screen.dart`, `profil_screen.dart` (beyond its logout button)
  are stub screens ("Segera hadir").

### Known gotcha — don't repeat this

The Stitch mockup's dashboard has a decorative 6-step progress tracker and
an "estimasi selesai" time. **Neither is backed by real data.** Once
already, a UI pass (done via Gemini) quietly reintroduced this fake step
scheme plus hardcoded mock data into the dashboard provider itself,
disconnecting it from the real API without flagging it. If you regenerate
UI from the mockup again, check `dashboard_provider.dart` still calls
`dashboardRepositoryProvider` (not a hardcoded `DashboardModel`) and that
`app_router.dart`'s `redirect` callback isn't stubbed to `return null;`
(that was found disabled too, bypassing login entirely).

### Other fixed issues worth knowing

- **iOS build was broken** (unrelated to any of the above): deployment
  target stuck at 13.0/9.0 in `ios/Runner.xcodeproj` and Pods, below
  Xcode's 15.0 minimum. Fixed in `ios/Runner.xcodeproj/project.pbxproj`
  (3 values bumped to 15.0) and `ios/Podfile` (added a `post_install` loop
  forcing `IPHONEOS_DEPLOYMENT_TARGET = '15.0'` on every pod target,
  because the Podfile's `platform :ios, '15.0'` alone wasn't enough — some
  pod subtargets kept a lower value). Re-run `pod install` if this
  regresses.
- **Base URL**: `lib/common/network/api_client.dart` reads the backend origin
  from the compile-time `API_BASE_URL` dart define. Use
  `http://10.0.2.2:8000` for an Android emulator, `http://127.0.0.1:8000`
  for an iOS Simulator, or `http://<machine-lan-ip>:8000` for a physical
  device. The app appends `/api/v1`; no host-specific default is shipped.
  Physical-device testing also requires Laravel to bind to the host interface
  (for example, `php artisan serve --host=0.0.0.0`).

## What's left (2-day CRUD sprint, in progress as of this doc)

1. Wire `Pesanan` feature to real `PesananController` endpoints, fix its
   status/step mismatch the same way dashboard's was fixed (drop the fake
   6-step scheme, use real status + `riwayat_status` timeline pattern).
2. Notifikasi and Profil screens are still stubs — scope not yet decided
   with the user (ask before building, mockup has more screens than the
   2-day window probably allows — e.g. Mobil Saya, Nota Digital, Tambah
   Mobil, Registrasi).
3. No automated tests written for the mobile app yet.

## Verification status

Login → dashboard flow with real DB data was proven working end-to-end on
an Android emulator (screenshots, real seeded pelanggan "Budi Santoso").
After the mock-data regression was fixed and re-wired, the exact same code
path was re-verified logically (`flutter analyze` clean, backend endpoints
re-tested via curl with real data) but **not re-confirmed via a live tap-
through screenshot** on iOS Simulator — Simulator UI automation needs
Accessibility permission this session didn't have. Worth a real device tap
before trusting it blindly.
