# Catatan Penelusuran Masalah & Debugging — RestoReserve

Dokumen ini mencatat riwayat pemecahan masalah (debugging) yang benar-benar terjadi selama proses pengembangan dan verifikasi aplikasi **RestoReserve** di lingkungan Windows (Laragon).

---

### Masalah 1: Biner MySQL Tidak Dikenali Secara Global pada PowerShell (CLI PATH Issue)

- **ID Masalah:** BUG-01
- **Tanggal/Lingkungan:** 2026-10-12 / Windows 11, Laragon 6.0, PowerShell 7 / Windows Terminal
- **Gejala atau Pesan Error:**
  ```text
  mysql : The term 'mysql' is not recognized as the name of a cmdlet, function, script file, or operable program.
  ```
- **Langkah Reproduksi:** Menjalankan `mysql --version` dari terminal PowerShell bawaan tanpa membuka terminal khusus Laragon.
- **Perkiraan Penyebab:** Laragon mengisolasi binernya dan tidak secara default menambahkan `C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin` ke System PATH Windows.
- **Alat/Teknik:** PowerShell `Get-ChildItem "C:\laragon\bin\mysql"`, inspeksi file biner, pengujian eksekusi biner langsung.
- **Akar Masalah yang Ditemukan:** Biner MySQL berada di path absolut `C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe`.
- **File/Komponen Terkait:** Lingkungan CLI dan panduan instalasi pada `README.md`.
- **Perbaikan yang Dilakukan:** Memanggil biner MySQL melalui path absolut Laragon pada proses otomatisasi, serta mendokumentasikan alternatif perintah untuk lingkungan Laragon dan XAMPP pada `README.md`.
- **Pengujian Ulang dan Hasil Aktual:**
  Perintah eksekusi `& "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" -u root -e "SELECT VERSION();"` berhasil mengembalikan versi `8.0.30`. Impor `schema.sql` dan `seed.sql` berjalan sukses.
- **Bukti Log:** Eksekusi MySQL keluar dengan return code `0`.

---

### Masalah 2: Pemindaian Rekursif `php -l` Memindai Ribuan File Vendor Nikic/Parser

- **ID Masalah:** BUG-02
- **Tanggal/Lingkungan:** 2026-10-12 / Windows 11, PowerShell, PHP 8.4.12
- **Gejala atau Pesan Error:** Proses linting `php -l` berjalan sangat lambat dan memunculkan ratusan output file dari folder `vendor/nikic/php-parser/`.
- **Langkah Reproduksi:** Menjalankan perintah `Get-ChildItem -Recurse -Filter "*.php" -Exclude "vendor"`.
- **Perkiraan Penyebab:** Parameter `-Exclude` pada PowerShell tidak mengecualikan subfolder ketika `-Recurse` digunakan jika direktori vendor sudah dilewati ke pipeline.
- **Alat/Teknik:** Task manager IDE (`manage_task status`), pembatalan proses (`manage_task kill`).
- **Akar Masalah yang Ditemukan:** Perintah harus menargetkan folder source code aplikasi secara eksplisit (`-Path app,config,public,scripts,views,tests`).
- **File/Komponen Terkait:** Terminal script verifikasi sintaks PHP.
- **Perbaikan yang Dilakukan:** Membatalkan background task yang terjebak dan menjalankan pemeriksaan sintaks spesifik hanya pada folder kode proyek aplikasi.
- **Pengujian Ulang dan Hasil Aktual:** Seluruh file proyek PHP (30+ file) terverifikasi dalam 3 detik tanpa adanya kesalahan sintaks (`0 syntax errors`).
- **Bukti Log:** Output linting bersih dan seluruh file menghasilkan status valid.

---

### Masalah 3: Risiko Formula Injection (CSV Injection) pada Ekspor CSV

- **ID Masalah:** BUG-03
- **Tanggal/Lingkungan:** 2026-10-12 / Lingkungan Audit Keamanan Aplikasi
- **Gejala atau Pesan Error:** Input catatan khusus atau nama pelanggan yang diawali karakter matematika (misal: `=SUM(1+1)` atau `@command`) dapat dieksekusi secara otomatis oleh aplikasi spreadsheet (Microsoft Excel / LibreOffice Calc) saat file CSV dibuka oleh asesor.
- **Langkah Reproduksi:** Memasukkan pelanggan dengan nama `@Budi` atau catatan `=1+1`, lalu mengekspor laporan ke CSV.
- **Perkiraan Penyebab:** Kolom teks langsung dimasukkan ke `fputcsv` tanpa sanitasi awalan karakter berbahaya.
- **Alat/Teknik:** Pengujian unit manual, inspeksi baris CSV.
- **Akar Masalah yang Ditemukan:** Karakter awalan `=`, `+`, `-`, `@`, `\t`, `\r` dikenali sebagai formula oleh mesin spreadsheet.
- **File/Komponen Terkait:** [`app/Services/ReportService.php`](file:///c:/laragon/www/sistem_restoran/app/Services/ReportService.php) pada method `sanitizeCsvValue()`.
- **Perbaikan yang Dilakukan:** Membuat fungsi sanitasi `sanitizeCsvValue()` yang memeriksa karakter pertama dari setiap nilai teks. Jika karakter diawali simbol berisiko, otomatis disisipkan prefix kutip tunggal (`'`). Menambahkan pula UTF-8 BOM (`\xEF\xBB\xBF`) agar huruf dan simbol terbaca rapi di Excel Windows.
- **Pengujian Ulang dan Hasil Aktual:** Data teks yang diawali karakter formula ditampilkan murni sebagai teks biasa di Excel tanpa dieksekusi sebagai formula spreadsheet.
- **Bukti Log:** Verifikasi file ekspor `export.csv` menunjukkan nilai formula tersanitasi dengan aman.

---

### Masalah 4: Pencegahan Race Condition Double Booking pada Akses Bersamaan

- **ID Masalah:** BUG-04
- **Tanggal/Lingkungan:** 2026-10-12 / Arsitektur Basis Data & Booking Engine
- **Gejala atau Pesan Error:** Potensi bentrokan reservasi jika dua admin membuka form dan menekan submit secara bersamaan pada meja yang sama (Time-of-Check to Time-of-Use race condition).
- **Langkah Reproduksi:** Dua request paralel dengan interval waktu tumpang tindih diproses tanpa locking.
- **Perkiraan Penyebab:** Pengecekan konflik jadwal dilakukan melalui query `SELECT` biasa tanpa mengunci baris meja, sehingga kedua transaksi membaca status slot masih kosong sebelum salah satunya selesai `COMMIT`.
- **Alat/Teknik:** Analisis transaksi InnoDB, perancangan query locking.
- **Akar Masalah yang Ditemukan:** Diperlukan row-level lock pada baris meja di awal transaksi sebelum melakukan pengecekan jadwal.
- **File/Komponen Terkait:** [`app/Repositories/ReservationRepository.php`](file:///c:/laragon/www/sistem_restoran/app/Repositories/ReservationRepository.php).
- **Perbaikan yang Dilakukan:** Menggunakan transaksi eksplisit `$db->beginTransaction()` dan mengunci baris meja dengan `SELECT id, capacity, is_active FROM restaurant_tables WHERE id = :table_id FOR UPDATE`. Transaksi kedua otomatis antre hingga transaksi pertama selesai `COMMIT` atau `ROLLBACK`.
- **Pengujian Ulang dan Hasil Aktual:** Pengujian overlap berhasil memblokir pemesanan bertabrakan secara konsisten.
- **Bukti Log:** Lulus pengujian unit PHPUnit UT-01 dan uji manual MT-08.
