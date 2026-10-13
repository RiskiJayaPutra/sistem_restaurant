# RestoReserve — Sistem Reservasi Meja & Pemesanan Makanan

**RestoReserve** adalah aplikasi web administrasi restoran skala kecil untuk sistem reservasi meja dan manajemen pemesanan makanan.

Aplikasi ini dibangun menggunakan arsitektur **PHP Native OOP** dengan pola **MVC (Model-View-Controller)**, Service, Repository, basis data **MySQL/MariaDB (InnoDB)** dengan transaksi dan row-level locking, **Tailwind CSS v4** (dikompilasi lokal offline via CLI), dan **Vanilla JavaScript** untuk micro-interactions.

---

## 1. Fitur Utama

- **Autentikasi Admin Tunggal:** Login berbasis session aman (`HttpOnly`, `SameSite=Lax`, `session_regenerate_id`), enkripsi password dengan `password_hash()` (bcrypt), dan proteksi CSRF di seluruh form aksi.
- **Dashboard Operasional:** 4 kartu metrik real-time (Total Pelanggan, Meja Aktif, Reservasi Hari Ini, Nilai Pesanan Terkonfirmasi Bulan Ini) dan daftar 5 reservasi terdekat.
- **CRUD Pelanggan:** Pengelolaan tamu dengan validasi nama, telepon, email, serta proteksi penghapusan data berelasi.
- **CRUD Meja Restoran:** Pengelolaan nomor meja unik, kapasitas 1–20 orang, dan status operasional aktif/nonaktif.
- **CRUD Menu:** Pengelolaan sajian makanan & minuman, harga Rupiah bulat, dan ketersediaan menu.
- **Reservasi Meja (Core Business):**
  - Durasi setiap reservasi tepat **2 jam (120 menit)**; waktu selesai dihitung otomatis.
  - **Pencegahan Double Booking:** Transaksi database InnoDB dengan kunci baris `SELECT ... FOR UPDATE` pada meja restoran untuk mencegah race condition.
  - Validasi ketat batas kapasitas kursi meja dan waktu di masa depan.
  - Matriks transisi status: `pending` &rarr; `confirmed` &rarr; `completed` / `cancelled`.
- **Pesanan Makanan (Orders):**
  - Maksimal 1 pesanan untuk reservasi berstatus `confirmed`.
  - **Snapshot Harga:** Menyalin harga menu saat pemesanan ke `order_items.unit_price` sehingga perubahan harga di kemudian hari tidak merusak data transaksi historis.
  - Perhitungan subtotal dan total dilakukan **100% di server** (client hanya menampilkan kalkulasi preview).
- **Laporan & Ekspor CSV:** Filter rentang tanggal reservasi, agregasi nilai pesanan terkonfirmasi, dan ekspor CSV aman dengan pencegahan *Spreadsheet Formula Injection*.

---

## 2. Prasyarat Sistem

- **Sistem Operasi:** Windows 10 / 11 (Kompatibel dengan Laragon atau XAMPP).
- **PHP:** Versi `>= 8.2` (Rekomendasi PHP 8.2, 8.3, atau 8.4).
- **Ekstensi PHP Wajib:** `pdo`, `pdo_mysql`, `mbstring`, `dom`, `xml`, `session`, `json`.
- **Basis Data:** MySQL 8.0+ atau MariaDB 10.4+ (Engine InnoDB, Charset `utf8mb4`).
- **Composer:** Versi 2.x untuk autoload PSR-4 dan dependensi runtime (`vlucas/phpdotenv`) serta dev (`phpunit/phpunit`).
- **Node.js & npm (Hanya untuk Development):** Digunakan untuk kompilasi stylesheet Tailwind CSS v4 CLI ke `public/assets/css/app.css`. File CSS hasil kompilasi **sudah disertakan dalam paket aplikasi**, sehingga saat demonstrasi pengujian, Node.js tidak diperlukan sama sekali.

---

## 3. Panduan Instalasi Langkah-Demi-Langkah

### Langkah 1 — Clone atau Salin Direktori Proyek
Buka terminal PowerShell dan arahkan ke direktori proyek:
```powershell
cd c:\laragon\www\sistem_restoran
```

### Langkah 2 — Pemasangan Dependensi Composer
Jalankan perintah Composer untuk mengunduh library dan menghasilkan autoload PSR-4:
```powershell
composer install
```

### Langkah 3 — Konfigurasi Lingkungan (`.env`)
Salin file template `.env.example` menjadi `.env`:
```powershell
copy .env.example .env
```
Sesuaikan konfigurasi koneksi MySQL di file `.env` jika menggunakan port atau password berbeda:
```ini
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restoreserve_db
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 4 — Migrasi Basis Data & Seed Data Awal
1. Buka MySQL client atau HeidiSQL / phpMyAdmin, lalu buat database `restoreserve_db`:
   ```sql
   CREATE DATABASE IF NOT EXISTS restoreserve_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Impor struktur 7 tabel dari file `database/schema.sql`:
   ```powershell
   Get-Content database/schema.sql -Raw | mysql -u root restoreserve_db
   ```
3. Impor data seed fiktif awal dari `database/seed.sql`:
   ```powershell
   Get-Content database/seed.sql -Raw | mysql -u root restoreserve_db
   ```

*Catatan untuk pengguna Laragon tanpa `mysql` di PATH sistem:*
Gunakan path biner MySQL Laragon:
```powershell
Get-Content database/schema.sql -Raw | & "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root restoreserve_db
Get-Content database/seed.sql -Raw | & "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root restoreserve_db
```

### Langkah 5 — Pembuatan Akun Admin
Buat kredensial login admin menggunakan script CLI interaktif:
```powershell
php scripts/create_admin.php admin password123
```
*Username default: `admin` | Password: `password123`*

### Langkah 6 (Opsional) — Menyiapkan Data Demo Masa Depan
Jalankan script untuk menginjeksi 3 data reservasi uji coba pada tanggal besok dan lusa:
```powershell
php scripts/seed_demo.php
```

---

## 4. Menjalankan Server Aplikasi

Jalankan PHP Built-in Server dengan document root diarahkan ke folder `public/`:
```powershell
php -S 127.0.0.1:8000 -t public public/router.php
```

Buka peramban web (browser) dan akses:
**`http://127.0.0.1:8000`**

### Kredensial Login Default:
- **URL:** `http://127.0.0.1:8000/login`
- **Username:** `admin`
- **Password:** `password123`

---

## 5. Menjalankan Pengujian Unit Otomatis (PHPUnit)

Aplikasi dilengkapi 19 unit test otomatis yang mencakup 100% aturan bisnis inti (kalkulasi overlap bentrokan jam, validasi kapasitas meja, perhitungan subtotal/total Rupiah, penolakan input invalid, dan matriks transisi status).

Jalankan pengujian melalui terminal:
```powershell
php vendor/phpunit/phpunit/phpunit
```

Hasil eksekusi:
```text
PHPUnit 10.5.66 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.4.12
Configuration: phpunit.xml

...................                                               19 / 19 (100%)

Time: 00:00.035, Memory: 8.00 MB
OK (19 tests, 37 assertions)
```

---

## 6. Skenario Demonstrasi Pengujian (5–7 Menit di Depan Asesor)

1. **Login Admin:** Masuk dengan username `admin` dan password `password123` &rarr; berhasil masuk ke Dashboard.
2. **Tambah Pelanggan Baru:** Masuk menu Pelanggan &rarr; Tambah pelanggan fiktif: `Budi Hartono` (HP: `081234567899`).
3. **Pemesanan Reservasi:** Masuk menu Reservasi &rarr; Buat reservasi untuk Budi pada Meja `M03` (Kapasitas 4 orang), waktu besok jam 18:00 WIB, 4 tamu &rarr; Tersimpan status `Pending`.
4. **Pengujian Validasi Konflik Jam (Double Booking):** Buat reservasi kedua pada Meja `M03` di tanggal yang sama jam 19:00 WIB &rarr; **Sistem menolak dengan pesan bentrokan jadwal**.
5. **Konfirmasi Reservasi:** Buka detail reservasi Budi &rarr; Klik tombol **Konfirmasi Reservasi** &rarr; Status berubah menjadi `Confirmed`.
6. **Pembuatan Pesanan Makanan:** Klik **+ Buat Pesanan Makanan** &rarr; Pilih `2 Nasi Goreng Spesial` (@ Rp25.000) dan `2 Es Teh Manis` (@ Rp5.000) &rarr; Tunjukkan kalkulasi preview Rp60.000 &rarr; Simpan &rarr; Konfirmasi pesanan.
7. **Laporan & Ekspor CSV:** Buka menu Laporan &rarr; Pilih periode besok &rarr; Tampilkan ringkasan Rp60.000 terkonfirmasi &rarr; Klik **Ekspor CSV** &rarr; Tunjukkan file spreadsheet hasil unduhan.
8. **Pengujian Unit & Kode:** Jalankan `php vendor/phpunit/phpunit/phpunit` di terminal (19 test PASS), lalu perlihatkan source code `BookingRules.php`, `OrderCalculator.php`, dan `ReservationRepository.php`.

---

## 7. Batasan Sistem yang Disengaja (Scope Limitation)

- **Akses Admin Tunggal:** Tidak menyediakan registrasi publik atau sistem multi-role bertingkat.
- **Satu Cabang Restoran:** Didesain khusus untuk operasional restoran satu cabang.
- **Durasi Tetap 2 Jam:** Setiap reservasi memiliki durasi tepat 120 menit untuk menjaga kepastian rotasi meja.
- **Tanpa Modul Kasir/Pembayaran:** Sistem mencatat pesanan makanan dan rekapitulasi nilai pesanan terkonfirmasi, bukan bukti penerimaan uang tunai atau integrasi payment gateway.
