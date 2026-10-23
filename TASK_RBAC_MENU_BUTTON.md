# TASK — RBAC Dinamis untuk Menu & Button

**Proyek:** SIPETRANS (Pengajuan Transportasi RS)
**Status:** Draft task (belum dieksekusi)
**Tanggal:** 2026-10-23
**Scope:** Role & permission **dinamis** yang hanya mengatur **visibilitas/akses menu dan button**.
**Prinsip utama:** **Tidak boleh ada hardcode role** di layer UI (`isAdmin()`, `isDriver()`, `role === '...'`, daftar role literal, atau cabang menu berdasarkan role).

---

## 1. Tujuan

1. Role dibuat dan dikelola dari database/UI, bukan dari kode.
2. Permission dibuat dan dipetakan ke role dari UI.
3. Menu (sidebar, bottom nav, header) dirender dari database dan difilter berdasarkan permission user.
4. Button aksi (create/edit/delete/approve/print/sign/dll.) ditampilkan hanya jika user punya permission terkait.
5. Tidak ada lagi pengecekan role di Blade maupun controller untuk keperluan tampilan menu/button.
6. Role baru dapat ditambahkan tanpa mengubah kode dan tanpa deploy.

## 2. Non-Goals (di luar scope task ini)

- Row-level/data-level authorization (mis. “admin A hanya boleh melihat pengajuan unit B”).
- Perubahan aturan bisnis state machine pengajuan.
- Perubahan alur tanda tangan digital.
- Audit log menyeluruh (boleh disiapkan sebagai hook, tetapi bukan deliverable utama).
- Restyling UI; tampilan visual tetap sama.
- Multi-tenant.

> **Catatan penting (prasyarat fungsional):** menu/button yang tampil harus selaras dengan guard route. Jika role baru diberi permission menu `transport.request.view` tetapi route `/admin` masih dijaga alias `admin` (hardcode `role === 'admin'`), user akan melihat menu lalu kena 403. Karena itu **Phase 7 (penyelarasan guard route) wajib** agar RBAC dinamis benar-benar berfungsi. Phase ini hanya mengganti guard halaman, bukan menambah aturan data-level.

---

## 3. Keputusan Desain

| Topik | Keputusan |
|---|---|
| Library ACL | **Rekomendasi:** `spatie/laravel-permission` untuk role/permission/pivot + cache. Alternatif: implementasi custom. |
| Tabel menu | Custom `menus` (tidak disediakan Spatie). |
| Relasi menu | `menus.permission_name` → mengacu ke `permissions.name` (string code). Menu tampil jika user `can(permission_name)`. |
| Multi-role | Didukung (many-to-many). `users.role_id` tidak dipakai permanen; gunakan pivot `model_has_roles`. |
| Backward compatibility | Kolom `users.role` dipertahankan sementara untuk login/redirect & seeder, lalu di-drop setelah Phase 7 stabil. |
| Nama permission | Format `domain.entity.action`, contoh `transport.request.approve`. |
| Nama menu | Key unik `admin.dashboard`, `admin.transport`, `user.transport.create`, dst. |
| Cache | Cache menu tree + permission per role; invalidasi via version key saat role/permission/menu berubah. |
| Deny by default | Menu/button tanpa permission = tidak dirender. |
| Super admin | Role `super-admin` dengan `Gate::before` (semua permission) — tetap data-driven, bukan `if role ==`. |

---

## 4. Katalog Permission

### 4.1 Menu

| Permission | Menu | Route |
|---|---|---|
| `dashboard.admin.view` | Dashboard (admin) | `admin.dashboard` |
| `transport.request.view` | Daftar Pengajuan | `admin.transport.index` |
| `report.view` | Laporan | `admin.laporan` |
| `user.manage` | User / Akun | `admin.users.index` |
| `vehicle.manage` | Kendaraan | `admin.vehicles.index` |
| `driver.manage` | Driver | `admin.drivers.index` |
| `recurring.manage` | Pengajuan Berulang | `admin.recurring-templates.index` |
| `transport.request.create` | Pengajuan (user) | `home` / `pengajuan.choose` |
| `transport.request.history` | Riwayat (user) | `pengajuan.index` |
| `driver.task.view` | Tugas Saya | `driver.dashboard` |
| `driver.task.history` | Riwayat (driver) | `driver.history` |
| `profile.view` | Profil | `profil` / `driver.profil` |
| `notification.admin.view` | Bell notifikasi admin | — |
| `notification.user.view` | Bell notifikasi user | — |

### 4.2 Button / Aksi

| Permission | Button | Lokasi |
|---|---|---|
| `transport.request.approve` | Setujui | `admin/transport/show` |
| `transport.request.reject` | Tolak | `admin/transport/show` |
| `transport.request.assign` | Assign unit + driver | `admin/transport/show` |
| `transport.request.complete` | Selesaikan | `admin/transport/show` |
| `transport.request.update` | Simpan perubahan | `admin/transport/show` |
| `transport.request.print` | Cetak surat | admin & user |
| `report.export` | Export Excel | `admin/laporan` |
| `report.print` | Cetak laporan | `admin/laporan` |
| `user.create` | Tambah User | `admin/users` |
| `user.edit` | Edit User | `admin/users` |
| `user.delete` | Hapus User | `admin/users` |
| `user.sync` | Sync Simutu | `admin/users` |
| `vehicle.create` / `.edit` / `.delete` | CRUD kendaraan | `admin/vehicles` |
| `driver.create` / `.edit` / `.delete` | CRUD driver | `admin/drivers` |
| `recurring.create` / `.edit` / `.delete` | CRUD template | `admin/recurring-templates` |
| `signature.sign` | Tombol tanda tangan | `signature/*` |
| `driver.task.complete` | Selesaikan tugas | `driver/dashboard` |
| `driver.task.cancel` | Batalkan tugas | `driver/dashboard` |
| `driver.task.print` | Cetak tugas | `driver/dashboard`, `driver/detail` |
| `role.manage` | Menu/CRUD Role | baru |
| `menu.manage` | Menu/CRUD Menu | baru |

### 4.3 Mapping role awal (seeder, bukan hardcode di view)

| Role | Permission |
|---|---|
| `super-admin` | semua |
| `admin` | semua permission admin (tanpa `role.manage`, `menu.manage` jika dipisah) |
| `user` | `transport.request.create`, `transport.request.history`, `transport.request.print`, `profile.view`, `signature.sign`, `notification.user.view` |
| `driver` | `driver.task.view`, `driver.task.history`, `driver.task.complete`, `driver.task.cancel`, `driver.task.print`, `profile.view`, `signature.sign` |

> Mapping ini hanya data awal di seeder. Perubahan setelahnya dilakukan dari UI, tanpa mengubah kode.

---

## 5. Katalog Menu

Tabel `menus` minimal berisi:

| key | parent | section | label | route_name | permission_name | context | sort | sidebar | bottom_nav | active_pattern |
|---|---|---|---|---|---|---|---|---|---|---|
| `admin.dashboard` | — | — | Dashboard | `admin.dashboard` | `dashboard.admin.view` | admin | 10 | ✅ | ❌ | `admin.dashboard` |
| `admin.transport` | — | — | Daftar Pengajuan | `admin.transport.index` | `transport.request.view` | admin | 20 | ✅ | ❌ | `admin.transport.*` |
| `admin.report` | — | — | Laporan | `admin.laporan` | `report.view` | admin | 30 | ✅ | ❌ | `admin.laporan*` |
| `admin.users` | — | Master Data | User / Akun | `admin.users.index` | `user.manage` | admin | 40 | ✅ | ❌ | `admin.users.*` |
| `admin.vehicles` | — | Master Data | Kendaraan | `admin.vehicles.index` | `vehicle.manage` | admin | 50 | ✅ | ❌ | `admin.vehicles.*` |
| `admin.drivers` | — | Master Data | Driver | `admin.drivers.index` | `driver.manage` | admin | 60 | ✅ | ❌ | `admin.drivers.*` |
| `admin.recurring` | — | Master Data | Pengajuan Berulang | `admin.recurring-templates.index` | `recurring.manage` | admin | 70 | ✅ | ❌ | `admin.recurring-templates.*` |
| `user.transport.create` | — | — | Pengajuan | `home` | `transport.request.create` | user | 10 | ✅ | ✅ | `home`, `pengajuan.choose`, `pengajuan.umum*`, `pengajuan.ambulance*` |
| `user.transport.history` | — | — | Riwayat | `pengajuan.index` | `transport.request.history` | user | 20 | ✅ | ✅ | `pengajuan.index`, `pengajuan.success`, `pengajuan.print` |
| `user.profile` | — | — | Profil | `profil` | `profile.view` | user | 30 | ❌ | ✅ | `profil` |
| `driver.task` | — | — | Tugas Saya | `driver.dashboard` | `driver.task.view` | driver | 10 | ✅ | ✅ | `driver.dashboard` |
| `driver.history` | — | — | Riwayat | `driver.history` | `driver.task.history` | driver | 20 | ✅ | ✅ | `driver.history` |
| `driver.profile` | — | — | Profil | `driver.profil` | `profile.view` | driver | 30 | ❌ | ✅ | `driver.profil` |
| `admin.roles` | — | Pengaturan | Role & Permission | `admin.roles.index` | `role.manage` | admin | 80 | ✅ | ❌ | `admin.roles.*` |
| `admin.menus` | — | Pengaturan | Menu | `admin.menus.index` | `menu.manage` | admin | 90 | ✅ | ❌ | `admin.menus.*` |

Catatan:
- `context` menentukan kelompok render (`admin` sidebar, `user` sidebar, `driver` sidebar).
- Menu dengan `sidebar = false` hanya muncul di bottom nav.
- Section header (`Master Data`, `Pengaturan`) jangan hardcode di Blade; ambil dari `section` + `sort_order`.
- `active_pattern` mendukung wildcard `*`.

---

## 6. Perubahan Skema Database

### 6.1 Tabel baru

```text
roles                 id, name, guard_name, label, description, is_locked, timestamps
permissions           id, name, guard_name, group, label, description, timestamps
role_has_permissions  permission_id, role_id
model_has_roles       role_id, model_type, model_id
menus                 id, parent_id, key(unique), section, label, route_name, url,
                      icon, permission_name(nullable), context, sort_order,
                      is_active, show_sidebar, show_bottom_nav, active_pattern, timestamps
```

### 6.2 Perubahan tabel `users`

1. Tambah relasi role via pivot (Spatie) — tidak perlu kolom baru.
2. Kolom `role` lama dipertahankan sementara (compatibility), ditandai **deprecated**.
3. Backfill: `admin → admin`, `driver → driver`, lainnya `user`.
4. Setelah Phase 7 lolos uji: hapus kolom `role` dan semua pemakaiannya.

---

## 7. Daftar Task

Legenda prioritas: **P0** blocker, **P1** wajib, **P2** nice-to-have.

### Phase 0 — Discovery & Guardrails

**T-00 (P0) Inventaris hardcode role**
- [ ] Inventaris semua `isAdmin()`, `isDriver()`, `->role ===`, `role ==`, `in_array(...role...)` di `resources/views`, `app/Http/Controllers`, `routes`.
- [ ] Simpan hasil di issue/komentar task.
- **Acceptance:** daftar lengkap dengan file + line number; tidak ada yang terlewat.

**T-01 (P0) Tetapkan naming convention**
- [ ] Finalisasi katalog permission (bagian 4) dan key menu (bagian 5).
- [ ] Simpan sebagai konstanta/enum di kode (mis. `app/Support/Permissions.php`).
- **Acceptance:** tidak ada string permission yang diketik ad-hoc di view.

### Phase 1 — Skema & Model

**T-02 (P0) Install & konfigurasi ACL**
- [ ] `composer require spatie/laravel-permission`.
- [ ] Publish migration & config; sesuaikan tabel `roles`, `permissions`.
- [ ] Jalankan migration di environment dev.
- **Acceptance:** tabel terbentuk; `php artisan migrate` bersih.

**T-03 (P0) Migration tabel `menus` + model `Menu`**
- [ ] Buat migration sesuai §6.1.
- [ ] Model `App\Models\Menu` dengan `parent()`, `children()`, scope `active()`, `forContext()`.
- **Acceptance:** relasi parent/child berfungsi; unit test sederhana lulus.

**T-04 (P0) Backfill role existing**
- [ ] Seeder/command memindahkan `users.role` ke pivot role.
- [ ] Pastikan user tanpa role mendapat `user`.
- **Acceptance:** jumlah user per role sama sebelum/sesudah; login tetap berjalan.

**T-05 (P1) Seeder permission, role, menu**
- [ ] Seed katalog permission §4.
- [ ] Seed 4 role awal + mapping §4.3.
- [ ] Seed menu §5.
- **Acceptance:** `db:seed` idempoten; data tidak duplikat saat dijalankan ulang.

### Phase 2 — Core ACL

**T-06 (P0) Registrasi Gate dari database**
- [ ] Daftarkan permission Spatie sebagai Gate (Spatie sudah menyediakan `@can`/`can()`).
- [ ] Tambah `Gate::before` untuk role `super-admin`.
- [ ] Tambah helper `hasPermission(string $permission): bool` via trait pada `User`.
- **Acceptance:** `auth()->user()->can('transport.request.approve')` mengembalikan nilai benar tanpa query berulang (cache aktif).

**T-07 (P0) Cache & invalidasi**
- [ ] Version key `acl.version` di cache.
- [ ] Observer pada `Role`, `Permission`, `Menu` → bump version + flush cache.
- **Acceptance:** perubahan permission langsung terlihat tanpa restart; tidak ada query N+1 di layout.

### Phase 3 — Menu Service

**T-08 (P0) `MenuService`**
- [ ] `MenuService::forUser(User $user, string $context)` mengembalikan tree menu yang lolos permission.
- [ ] Filter `is_active`, `context`, `show_sidebar`/`show_bottom_nav`.
- [ ] Cache per role + context + version.
- **Acceptance:** user tanpa permission tidak menerima node terkait; urutan sesuai `sort_order`.

**T-09 (P0) Resolver halaman awal**
- [ ] Ganti redirect berbasis role di `routes/web.php:9-21`, `AuthController:12-25,63-70` dengan `LandingResolver` (menu pertama yang diizinkan).
- [ ] Tidak ada `isAdmin()/isDriver()` di resolver.
- **Acceptance:** setiap role mendarat di menu pertama yang tersedia; role baru otomatis berfungsi tanpa kode.

### Phase 4 — Layout Dinamis

**T-10 (P0) Komponen menu generik**
- [ ] Buat `resources/views/components/sidebar.blade.php` (rekursif, dukung section & submenu).
- [ ] Buat `resources/views/components/bottom-nav.blade.php`.
- [ ] Buat `resources/views/components/header-actions.blade.php` (bell, riwayat, profil, logout).
- **Acceptance:** ketiga komponen hanya menerima data menu; nol pengecekan role.

**T-11 (P0) Refactor `components/app-layout.blade.php`**
- [ ] Hapus 4 blok navigasi hardcoded (`:537-651`, `:657-707`, `:725-779`).
- [ ] Ganti dengan `<x-sidebar :context="..."/>` dan `<x-bottom-nav :context="..."/>`.
- [ ] Ganti `logoHref`, `historyRoute`, notifikasi, label profil, dan `sp-main-with-sidebar` agar memakai menu/permission, bukan role.
- [ ] Pastikan tombol hamburger muncul untuk **semua** konteks yang punya sidebar (perbaiki bug sidebar user/driver tidak bisa dibuka).
- **Acceptance:** tampilan identik untuk role existing; tidak ada `isAdmin/isDriver` di file ini.

**T-12 (P1) Hapus layout mati**
- [ ] Hapus `resources/views/layouts/app.blade.php` (tidak dipakai dan sudah rusak: `name`/`email`).
- **Acceptance:** `rg "layouts.app"` = 0 hasil.

### Phase 5 — Button Permission

**T-13 (P0) Ganti seluruh kondisi role di Blade dengan `@can`**
- [ ] `resources/views/signature/{show,confirm,already_signed}.blade.php` — ganti ternary `isAdmin()`.
- [ ] `resources/views/admin/transport/{index,show}.blade.php` — bungkus tombol aksi.
- [ ] `resources/views/admin/users/*` — sync/create/edit/delete.
- [ ] `resources/views/admin/vehicles/*` — create/edit/delete.
- [ ] `resources/views/admin/drivers/*` — create/edit/delete.
- [ ] `resources/views/admin/recurring-templates/*` — create/edit/delete.
- [ ] `resources/views/admin/laporan*.blade.php` — export/print.
- [ ] `resources/views/driver/*` — complete/cancel/print.
- [ ] `resources/views/user/transport/*` — print/sign/riwayat.
- **Acceptance:** setiap button punya `@can('...')` sesuai §4.2; kondisi status tetap dipertahankan (mis. tombol hanya muncul saat status `diajukan`).

**T-14 (P0) Hapus hardcode role dari tampilan**
- [ ] Hapus semua `auth()->user()->isAdmin()` / `isDriver()` di `resources/views`.
- [ ] Hapus label role literal (`'Administrator'`, `'Driver'`) → ambil `auth()->user()->roles->first()->label`.
- [ ] Hapus cabang `Role` di `app/Http/Controllers` yang khusus untuk memilih view/tombol.
- **Acceptance:** perintah verifikasi §9 mengembalikan 0 hasil untuk folder `resources/views`.

### Phase 6 — UI Manajemen Role, Permission, Menu

**T-15 (P1) CRUD Role & assign permission**
- [ ] Route + controller + view `admin/roles`.
- [ ] Proteksi dengan `role.manage`.
- [ ] Role `is_locked` tidak boleh dihapus.
- **Acceptance:** admin dapat membuat role baru, centang permission, assign ke user, dan menu/button langsung berubah.

**T-16 (P1) Assign role ke user**
- [ ] Ganti dropdown `role` di `admin/users/create` & `edit` dengan dropdown role dari DB (multi-select opsional).
- [ ] Update validasi `in:user,admin,driver` → `exists:roles,id`.
- **Acceptance:** tidak ada daftar role literal di form.

**T-17 (P2) Menu manager**
- [ ] CRUD menu: label, route, icon, permission, urutan, context, tampil di sidebar/bottom nav.
- [ ] Proteksi `menu.manage`.
- **Acceptance:** perubahan urutan/visibilitas menu berlaku tanpa deploy.

**T-18 (P1) Permission matrix viewer**
- [ ] Halaman read-only matriks role × permission untuk audit.
- **Acceptance:** mudah memverifikasi role baru.

### Phase 7 — Penyelarasan Guard Route (prasyarat fungsional)

**T-19 (P0) Ganti middleware role-based dengan permission**
- [ ] `routes/web.php:81` → `middleware(['auth','permission:transport.request.view'])` (atau guard per grup).
- [ ] `routes/web.php:104` → `permission:driver.task.view`.
- [ ] Group user `/pengajuan` → `permission:transport.request.create`.
- [ ] Hapus alias `admin` & `driver` setelah semua route termigrasi.
- [ ] Hapus `AdminMiddleware`, `DriverMiddleware`, dan `User::isAdmin()/isDriver()`.
- **Acceptance:** role baru dengan permission yang sama bisa mengakses halaman; tidak ada 403 palsu; `rg "isAdmin|isDriver|role ==="` = 0 hasil di seluruh `app` dan `routes`.

> Phase ini **bukan** data-level authorization. Hanya memastikan guard halaman memakai permission yang sama dengan menu/button.

### Phase 8 — Testing & Rollout

**T-20 (P0) Feature test matriks akses**
- [ ] Test per role: menu yang tampil, button yang tampil, route yang bisa diakses.
- [ ] Test role baru (mis. `operator`) hanya dengan `transport.request.view` → menu Daftar Pengajuan tampil, tombol approve tidak tampil.
- [ ] Test user tanpa permission → 403 pada route terkait.
- **Acceptance:** semua test hijau; matrix §8 terpenuhi.

**T-21 (P1) Rollback plan**
- [ ] Backup DB sebelum migrasi.
- [ ] Feature flag `RBAC_DYNAMIC_ENABLED` untuk kembali ke layout lama selama 1 rilis.
- **Acceptance:** rollback teruji di staging.

**T-22 (P1) Dokumentasi**
- [ ] Update README: cara menambah role, permission, menu.
- [ ] Catat permission yang tersedia.
- **Acceptance:** operator non-developer bisa menambah role/menu lewat UI.

---

## 8. Matriks Verifikasi

| Menu / Button | super-admin | admin | user | driver | operator (contoh baru) |
|---|---|---|---|---|---|
| Dashboard admin | ✅ | ✅ | ❌ | ❌ | ❌ |
| Daftar Pengajuan | ✅ | ✅ | ❌ | ❌ | ✅ |
| Laporan | ✅ | ✅ | ❌ | ❌ | ❌ |
| Master User/Kendaraan/Driver/Recurring | ✅ | ✅ | ❌ | ❌ | ❌ |
| Pengajuan (buat) | ✅ | ❌ | ✅ | ❌ | ❌ |
| Riwayat user | ✅ | ❌ | ✅ | ❌ | ❌ |
| Tugas Saya | ✅ | ❌ | ❌ | ✅ | ❌ |
| Riwayat driver | ✅ | ❌ | ❌ | ✅ | ❌ |
| Profil | ✅ | ✅ | ✅ | ✅ | ✅ |
| Tombol Approve/Reject/Assign/Complete | ✅ | ✅ | ❌ | ❌ | ❌ |
| CRUD master data | ✅ | ✅ | ❌ | ❌ | ❌ |
| Export laporan | ✅ | ✅ | ❌ | ❌ | ❌ |
| Tanda tangan | ✅ | ✅ | ✅ (milik sendiri) | ✅ (ditugaskan) | ❌ |

> Kepemilikan data (mis. “tanda tangan hanya untuk pengajuan sendiri”) tetap memakai pengecekan yang sudah ada; bukan bagian RBAC menu/button.

---

## 9. Perintah Verifikasi “Bebas Hardcode Role”

Jalankan setelah Phase 7:

```bash
# UI tidak boleh punya cek role
rg -n "isAdmin\(|isDriver\(|->role\s*===|->role\s*==|role\s*===\s*'|role\s*==\s*'" resources/views

# Seluruh app/routes tidak boleh punya cek role
rg -n "isAdmin\(|isDriver\(" app routes

# Tidak boleh ada daftar role literal di validasi/form
rg -n "in:user,admin,driver|'admin'\s*,\s*'driver'" app resources

# Pastikan permission dipakai
rg -n "@can\(" resources/views | wc -l
```

Kriteria lulus: tiga perintah pertama menghasilkan **0 baris**.

---

## 10. Definition of Done

- [ ] Role & permission dapat dibuat/diubah dari UI tanpa deploy.
- [ ] Menu sidebar, bottom nav, dan header dirender dari tabel `menus`.
- [ ] Semua button aksi dibungkus permission; tidak ada `isAdmin()/isDriver()/role ===` di Blade.
- [ ] Tidak ada daftar role literal di form/validasi.
- [ ] Role baru langsung mendapat menu/button sesuai permission tanpa perubahan kode.
- [ ] Route guard memakai permission yang sama (Phase 7) sehingga tidak ada menu tampil tapi 403.
- [ ] Cache permission/menu terinvalidasi otomatis saat data berubah.
- [ ] Feature test matriks akses lulus.
- [ ] Dokumentasi cara menambah role/menu tersedia.

---

## 11. Estimasi & Urutan Eksekusi

| Phase | Estimasi | Dependency |
|---|---|---|
| 0 — Discovery | 0.5 hari | — |
| 1 — Skema & model | 1 hari | Phase 0 |
| 2 — Core ACL | 1 hari | Phase 1 |
| 3 — Menu service | 1 hari | Phase 2 |
| 4 — Layout dinamis | 1.5 hari | Phase 3 |
| 5 — Button permission | 2 hari | Phase 4 |
| 6 — UI manajemen | 2 hari | Phase 2 |
| 7 — Guard route | 1 hari | Phase 5 |
| 8 — Test & rollout | 1.5 hari | semua |

**Total ± 11–12 hari kerja.**

Urutan aman: **0 → 1 → 2 → 3 → 4 → 5 → 6 → 7 → 8**. Phase 6 boleh paralel dengan Phase 4–5, tetapi Phase 7 tidak boleh dimulai sebelum semua view selesai dimigrasi.

---

## 12. Risiko

| Risiko | Mitigasi |
|---|---|
| Menu tampil tetapi route 403 | Phase 7 wajib; permission menu = permission route |
| Button disembunyikan tetapi aksi masih bisa dipanggil via URL | Guard route Phase 7; untuk aksi sensitif tetap gunakan validasi status yang sudah ada |
| Role existing berubah perilaku | Feature flag + matriks verifikasi + test |
| Cache stale setelah ubah permission | Observer + version key |
| Developer kembali memakai `isAdmin()` | Larangan di code review + perintah verifikasi §9 di CI |
| Kehilangan akses admin saat migrasi | Seeder idempoten + akun super-admin cadangan + backup DB |
| Menu manager disalahgunakan | `menu.manage` hanya untuk super-admin; validasi `route_name` harus ada di route list |

---

## 13. Catatan Implementasi

1. **Jangan** menyimpan daftar role di Blade, config menu, atau array controller. Semua lewat query.
2. **Jangan** memakai `auth()->user()->role` di view. Gunakan `@can` atau data menu.
3. Label role di UI diambil dari `roles.label`, bukan string literal.
4. Untuk kebutuhan “tampilkan menu X hanya untuk role tertentu”, gunakan permission khusus (mis. `report.view`), bukan nama role.
5. Semua permission harus terdaftar di enum/katalog; typo permission = menu tidak pernah tampil (deny by default), sehingga perlu test.
6. Hapus `AdminMiddleware`/`DriverMiddleware` hanya setelah Phase 7 selesai; jangan menghapus lebih awal agar aplikasi tetap berjalan.
7. File `resources/views/layouts/app.blade.php` adalah dead code; hapus pada T-12.
8. Bug sidebar user/driver yang tidak bisa dibuka diperbaiki sekaligus di T-11.
