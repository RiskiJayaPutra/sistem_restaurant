# Panduan Skenario Demonstrasi Pengujian Sistem — RestoReserve

Panduan operasional ini dirancang khusus untuk peserta dalam mendemonstrasikan aplikasi **RestoReserve** di hadapan tim penguji dalam durasi **5 hingga 7 menit**.

---

## 1. Persiapan Sebelum Asesor Memulai Ujian (H-5 Menit)

1. Pastikan server web PHP berjalan di terminal PowerShell:
   ```powershell
   php -S 127.0.0.1:8000 -t public public/router.php
   ```
2. Buka browser pada alamat: `http://127.0.0.1:8000/login`
3. Siapkan terminal kedua untuk demonstrasi unit testing dan perintah PHPUnit:
   ```powershell
   php vendor/phpunit/phpunit/phpunit
   ```
4. Buka IDE (VS Code / PHPStorm) dengan tab file penting siap dibuka:
   - [`app/Services/BookingRules.php`](file:///c:/laragon/www/sistem_restoran/app/Services/BookingRules.php)
   - [`app/Services/OrderCalculator.php`](file:///c:/laragon/www/sistem_restoran/app/Services/OrderCalculator.php)
   - [`app/Repositories/ReservationRepository.php`](file:///c:/laragon/www/sistem_restoran/app/Repositories/ReservationRepository.php)

---

## 2. Alur Demonstrasi Langkah demi Langkah (5–7 Menit)

### Menit 1 — Login & Dashboard (Bukti Autentikasi & Antarmuka)
1. **Tunjukkan Halaman Login:** Jelaskan bahwa aplikasi memiliki otentikasi admin aman dengan perlindungan CSRF dan password hashing bcrypt.
2. **Login:** Masukkan username `admin` dan password `password123`.
3. **Jelaskan Dashboard:**
   - 4 kartu ringkasan: Total Pelanggan, Meja Aktif, Reservasi Hari Ini, dan Nilai Pesanan Terkonfirmasi Bulan Ini.
   - Tabel reservasi aktif terdekat. Jelaskan bahwa nilai uang adalah nilai pesanan terkonfirmasi (bukan klaim kas, karena sistem tidak memiliki modul pembayaran).

### Menit 2 — CRUD Pelanggan & Meja (Bukti OOP & Database)
1. Buka menu **Pelanggan** &rarr; klik **Tambah Pelanggan**.
2. Masukkan nama: `Budi Hartono`, HP: `081234567899`, Email: `budi@example.com` &rarr; klik **Simpan Pelanggan**.
3. Jelaskan bahwa input divalidasi ketat (nama 3-100 karakter, format telepon regex, email RFC) dan disimpan menggunakan PDO Prepared Statement.

### Menit 3 — Reservasi & Validasi Konflik Jam (Bukti Algoritma & Transaksi)
1. Buka menu **Reservasi** &rarr; klik **Buat Reservasi Baru**.
2. Pilih Tamu: `Budi Hartono`, Meja: `Meja M03 (Kapasitas 4 Orang)`.
3. Tentukan Jadwal: **Besok pukul 18:00 WIB**, Jumlah Tamu: **4 Orang** &rarr; klik **Simpan Reservasi**.
   - Tunjukkan bahwa sistem otomatis menetapkan waktu selesai pukul **20:00 WIB** (durasi pasti 2 jam).
4. **Demonstrasikan Validasi Bentrokan Jam (Paling Penting!):**
   - Klik **Buat Reservasi Baru** lagi.
   - Pilih Meja yang sama: `Meja M03`, Jadwal: **Besok pukul 19:00 WIB** (tumpang tindih).
   - Klik **Simpan Reservasi** &rarr; **Tunjukkan bahwa sistem menolak pemesanan** dengan pesan: *"Meja ini sudah dipesan pada rentang jam tersebut."*

### Menit 4 — Konfirmasi Reservasi & Pembuatan Pesanan Makanan (Bukti Snapshot Harga)
1. Buka kembali detail Reservasi Budi &rarr; klik tombol **Konfirmasi Reservasi** (status berubah jadi `Confirmed`).
2. Klik tombol **+ Buat Pesanan Makanan**.
3. Tambah menu:
   - Pilih `Nasi Goreng Spesial` &rarr; Qty `2` (@ Rp25.000 = Rp50.000).
   - Klik `+ Tambah Menu` &rarr; baris baru muncul dengan animasi halus.
   - Pilih `Es Teh Manis` &rarr; Qty `2` (@ Rp5.000 = Rp10.000).
   - Tunjukkan preview total real-time: **Rp60.000**.
4. Klik **Simpan Draft Pesanan** &rarr; jelaskan bahwa backend mengambil harga dari database (snapshot), bukan mempercayai harga browser.
5. Klik **Konfirmasi Pesanan**.

### Menit 5 — Laporan & Ekspor CSV (Bukti Agregasi & Keamanan Output)
1. Buka menu **Laporan**.
2. Filter rentang tanggal yang mencakup reservasi besok &rarr; klik **Terapkan Periode**.
3. Tunjukkan ringkasan kartu metrik dan tabel rekapitulasi nilai Rp60.000.
4. Klik tombol **Ekspor ke Format CSV** &rarr; buka file CSV yang terunduh dan perlihatkan bahwa datanya rapi serta aman dari CSV Formula Injection.

### Menit 6 — Pembuktian Kode & Unit Testing (Bukti Kualitas Kode Program)
1. Pindah ke terminal PowerShell kedua &rarr; jalankan:
   ```powershell
   php vendor/phpunit/phpunit/phpunit
   ```
   Tunjukkan hasil: **19 tests, 37 assertions, OK (100% PASS)**.
2. Buka file di IDE:
   - [`app/Services/BookingRules.php`](file:///c:/laragon/www/sistem_restoran/app/Services/BookingRules.php): Tunjukkan fungsi `isOverlapping()`, percabangan terstruktur, dan type hinting.
   - [`app/Repositories/ReservationRepository.php`](file:///c:/laragon/www/sistem_restoran/app/Repositories/ReservationRepository.php): Tunjukkan transaksi `$db->beginTransaction()` dan locking `SELECT ... FOR UPDATE` untuk mencegah race condition.

---

## 3. Jawaban Praktis atas Pertanyaan Umum Asesor

| Pertanyaan Asesor | Jawaban Singkat & Tepat |
|---|---|
| *"Mengapa durasi reservasi dipatok tepat 2 jam?"* | *"Untuk kejelasan aturan operasional, aturan bisnis yang terukur dan deterministik (120 menit) menjamin kalkulasi interval waktu dapat diuji secara otomatis dan menghindari kompleksitas durasi bebas yang rawan bias."* |
| *"Bagaimana Anda mencegah dua orang memesan meja yang sama di detik yang sama?"* | *"Menggunakan transaksi database InnoDB dengan row-level lock `SELECT ... FOR UPDATE` pada meja terkait sebelum mengecek konflik jadwal, sehingga transaksi kedua otomatis mengantre."* |
| *"Apa itu snapshot harga pada tabel order_items?"* | *"Menyalin harga menu master ke kolom `order_items.unit_price` saat pesanan dibuat. Tujuannya agar jika harga menu diubah di kemudian hari, nilai historis pesanan yang telah lalu tidak berubah."* |
| *"Mengapa Tailwind CSS menggunakan CLI dan bukan CDN?"* | *"Agar backend tetap PHP Native murni, dan CSS yang dihasilkan berupa file statis lokal (`app.css`) sehingga aplikasi dapat didemonstrasikan secara offline tanpa bergantung pada internet."* |
| *"Bagaimana aplikasi mencegah serangan XSS dan CSRF?"* | *"XSS dicegah melalui escaping global `e()` dengan `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. CSRF dicegah dengan token acak 32-byte per sesi yang divalidasi via `hash_equals()` pada setiap request POST."* |
