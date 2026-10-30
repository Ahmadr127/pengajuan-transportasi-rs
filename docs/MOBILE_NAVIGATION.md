# Navigasi Mobile (Menu Dinamis)

Menu di aplikasi Flutter **tidak lagi ditentukan oleh nama role**. Semua tab,
label, ikon, dan visibilitas tombol berasal dari tabel `menus` + tabel
`permissions` di database, difilter oleh `MenuService`.

## Endpoint

```
GET /api/me/navigation      (auth:sanctum)
```

```json
{
  "success": true,
  "data": {
    "context": "admin",
    "landing": "admin.dashboard",
    "permissions": ["transport.request.approve", "report.view"],
    "is_super_admin": false,
    "roles": [{ "id": 2, "name": "admin", "label": "Admin" }],
    "sidebar": [ { "key": "admin.dashboard", "label": "Dashboard", "children": [] } ],
    "bottom_nav": [
      { "key": "admin.dashboard", "label": "Dashboard", "icon": "dashboard", "route_name": "admin.dashboard", "children": [] },
      {
        "key": "admin.master", "label": "Master", "icon": "storage", "route_name": null,
        "children": [
          { "key": "admin.vehicles", "label": "Kendaraan", "route_name": "admin.vehicles.index", "children": [] }
        ]
      }
    ]
  }
}
```

Aturan yang dipakai server:

1. `context` dipilih otomatis (`MenuService::contextFor`) dari context yang paling
   banyak menunya untuk user tersebut.
2. Menu hanya dikirim jika `is_active` dan permission-nya dimiliki user
   (super-admin selalu lolos).
3. Menu induk yang semua anaknya tidak boleh diakses ikut dibuang, supaya tidak
   muncul tab kosong.
4. Menu dengan anak dipakai sebagai *hub* di mobile; anak tetap dikirim walau
   `show_bottom_nav = false`.
5. Menu tanpa `route_name`/`url` dan tanpa anak tidak dikirim.

## Menambah menu baru di mobile

1. Buat menu lewat **Admin → Menu** (atau seeder): isi `key`, `label`, `icon`,
   `permission_name`, dan centang **Tampil di bottom nav**.
2. Isi `parent_id` bila menu tersebut sub-menu dari menu induk (mis. Master).
3. Petakan `key` ke screen Flutter di `lib/screens/menu_router.dart`
   (`screenForMenu`) — tambahkan `case '<key>': return const YourScreen();`.
4. Tanpa pemetaan, menu tetap tampil dengan halaman placeholder
   "Belum tersedia di mobile" (tidak crash).

Ikon memakai nama di kolom `menus.icon`; pemetaannya ada di
`lib/core/menu_icons.dart` (`dashboard`, `document`, `chart`, `users`, `car`,
`driver`, `repeat`, `building`, `badge`, `shield`, `gear`, `home`, `history`,
`user`, `storage`, `assignment`).

## Gating tombol

Flutter membaca permission dari payload yang sama lewat
`AuthProvider.can(String permission)`:

- Master Data: tab difilter `*.manage`; FAB `*.create`; edit `*.edit`; hapus `*.delete`.
- Detail pengajuan admin: Setujui `transport.request.approve`, Tolak
  `transport.request.reject`, Tugaskan `transport.request.assign`.
- Detail pengajuan user: tombol tanda tangan hanya jika `signature.sign`.

Server tetap memvalidasi permission yang sama lewat middleware `permission`,
jadi menyembunyikan tombol hanya lapisan UX.

## Perubahan data

Migration `2026_10_30_000001_add_mobile_navigation_menus`:

- Menambah menu induk `admin.master` (bottom nav, tidak tampil di sidebar web)
  dan memindahkan menu master data menjadi anaknya.
- Menambah `admin.profile`, `user.dashboard`, `driver.dashboard`.
- Mengaktifkan `show_bottom_nav` untuk `admin.dashboard`, `admin.transport`,
  `admin.report`.
- Memberi permission `profile.view` ke role `admin` bila belum ada.

`AclSeeder` sudah memuat menu yang sama sehingga instalasi baru konsisten.
