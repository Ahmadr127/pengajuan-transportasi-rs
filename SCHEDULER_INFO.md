# Laravel Scheduler - Auto Reject Expired Requests

## Deskripsi
Sistem ini secara otomatis mengubah status pengajuan transportasi yang masih berstatus "diajukan" menjadi "tidak_disetujui" jika sudah melewati waktu transportasi yang diajukan.

## Command Manual
Untuk menjalankan command secara manual:
```bash
php artisan transport:auto-reject-expired
```

## Scheduler Otomatis
Command ini sudah dijadwalkan untuk berjalan otomatis setiap jam melalui Laravel Scheduler.

### Cara Mengaktifkan Scheduler (Production)

#### Windows
Tambahkan task di Windows Task Scheduler:
1. Buka Task Scheduler
2. Create Basic Task
3. Trigger: Daily, repeat every 1 hour
4. Action: Start a program
5. Program: `php`
6. Arguments: `E:\TugasKuliah\MagangRSAzra\laravel-transportasi-rsazra\artisan schedule:run`
7. Start in: `E:\TugasKuliah\MagangRSAzra\laravel-transportasi-rsazra`

Atau gunakan command ini di Command Prompt (Run as Administrator):
```cmd
schtasks /create /tn "Laravel Scheduler" /tr "php E:\TugasKuliah\MagangRSAzra\laravel-transportasi-rsazra\artisan schedule:run" /sc minute /mo 1
```

#### Linux/Mac
Tambahkan cron job:
```bash
crontab -e
```

Tambahkan baris ini:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Status yang Digunakan
- **diajukan**: Pengajuan baru yang menunggu persetujuan
- **diproses**: Pengajuan yang sudah disetujui
- **digunakan**: Kendaraan sedang digunakan
- **selesai**: Transportasi selesai
- **tidak_disetujui**: Pengajuan tidak disetujui oleh admin
- **tidak_disetujui**: Pengajuan yang tidak disetujui atau terlewat waktu (otomatis/manual)

## Cara Kerja
1. Command berjalan setiap jam (atau manual)
2. **Kasus 1 — Status `diajukan`**: jika sudah lebih dari 24 jam sejak `created_at` tanpa diproses admin, status diubah ke `tidak_disetujui` dengan alasan "tidak diproses dalam 24 jam".
3. **Kasus 2 — Status `diproses`**: jika tanggal + jam transportasi sudah lewat lebih dari 24 jam tanpa dieksekusi (tidak berubah ke `digunakan`/`selesai`), status diubah ke `tidak_disetujui` dengan alasan "tidak dieksekusi dalam 24 jam setelah jadwal".
4. `rejection_reason` diisi otomatis dengan keterangan yang sesuai.
5. Log ditampilkan untuk setiap pengajuan yang diubah.

## Testing
Untuk testing, jalankan command manual:
```bash
php artisan transport:auto-reject-expired
```

Output akan menampilkan:
- Daftar pengajuan yang diubah statusnya
- Total pengajuan yang berubah menjadi tidak disetujui
- Atau pesan "Tidak ada pengajuan yang melewati waktu" jika tidak ada
