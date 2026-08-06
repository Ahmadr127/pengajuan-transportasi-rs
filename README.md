# SIPETRANS — Sistem Pengajuan Transportasi RS Azra

Aplikasi web untuk mengelola pengajuan dan penugasan kendaraan transportasi di lingkungan RS Azra. Dibangun dengan Laravel 12, Bootstrap 5, dan Vite.

---

## Fitur Utama

### 👤 Pengguna (Karyawan)
- Login menggunakan username dan password
- Pengajuan transportasi jenis **Mobil Umum** dan **Ambulance**
- Pengajuan prioritas **CITO** (segera) untuk kondisi darurat
- Cek ketersediaan kendaraan secara real-time sebelum mengajukan
- Riwayat pengajuan lengkap dengan status terkini
- Notifikasi pengajuan yang disetujui atau sedang digunakan
- Cetak surat jalan (PDF-ready)
- Tanda tangan digital pemohon via QR code

### 🚗 Driver
- Dashboard tugas aktif yang sedang berjalan
- Input KM tiba, jam kedatangan, dan biaya e-tol saat menyelesaikan perjalanan
- Riwayat perjalanan yang telah diselesaikan
- Cetak surat jalan dari aplikasi
- Profil driver

### 🛡️ Admin
- Dashboard ringkasan dan statistik pengajuan
- Manajemen pengajuan: setujui, tolak, assign driver & kendaraan
- Notifikasi pengajuan baru dan reminder perjalanan hari ini
- Laporan pengajuan dengan filter dan ekspor Excel
- Master data: Pengguna, Driver, Kendaraan
- Template pengajuan berulang (recurring) otomatis
- Auto-reject pengajuan kadaluarsa via scheduled command
- Verifikasi tanda tangan via QR code

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Bootstrap 5.3, Tailwind CSS 4, Alpine.js 3 |
| Build Tool | Vite 7 |
| Database | MySQL / MariaDB |
| Export | Maatwebsite Excel + PhpSpreadsheet |
| Auth | Laravel built-in session auth |

---

## Struktur Role

| Role | Akses |
|---|---|
| `user` | Pengajuan, riwayat, profil |
| `driver` | Dashboard tugas, riwayat perjalanan, profil |
| `admin` | Semua fitur termasuk master data dan laporan |

---

## Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL / MariaDB

### Langkah-langkah

**1. Clone repository**
```bash
git clone https://github.com/username/sipetrans.git
cd sipetrans
```

**2. Install dependensi**
```bash
composer install
npm install
```

**3. Konfigurasi environment**
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipetrans
DB_USERNAME=root
DB_PASSWORD=
```

**4. Migrasi database**
```bash
php artisan migrate
```

**5. Build assets**
```bash
npm run build
```

**6. Jalankan aplikasi**
```bash
php artisan serve
```

Aplikasi tersedia di `http://localhost:8000`

### Setup cepat (semua langkah sekaligus)
```bash
composer run setup
```

---

## Menjalankan Mode Development

```bash
composer run dev
```

Perintah ini menjalankan secara bersamaan:
- `php artisan serve` — server Laravel
- `php artisan queue:listen` — queue worker
- `npm run dev` — Vite dev server (hot reload)

---

## Scheduled Commands

Daftarkan cron job berikut di server untuk fitur otomatis:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Perintah terjadwal yang berjalan:
- `AutoRejectExpiredRequests` — otomatis menolak pengajuan yang melewati tanggal tanpa diproses
- `GenerateRecurringRequests` — membuat pengajuan harian dari template berulang

---

## Struktur Database

| Tabel | Keterangan |
|---|---|
| `users` | Data pengguna (karyawan, driver, admin) |
| `drivers` | Data driver (nama, telepon, nomor SIM) |
| `vehicles` | Master kendaraan (mobil umum & ambulance) |
| `transport_requests` | Data pengajuan transportasi |
| `recurring_transport_templates` | Template pengajuan berulang |

---

