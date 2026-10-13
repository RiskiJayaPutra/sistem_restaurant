# Outline Presentasi Sertifikasi Kompetensi Programmer (9 Slide) — RestoReserve

Dokumen ini merupakan kerangka materi presentasi 9 slide untuk demonstrasi kompetensi pemrograman di hadapan Asesor Penguji.

---

### Slide 1: Judul Proyek & Identitas Peserta
- **Judul:** RestoReserve — Sistem Reservasi Meja dan Pemesanan Makanan
- **Subjudul:** Tugas Praktik Demonstrasi Sertifikasi Kompetensi Skema Pemrograman
- **Poin Utama:**
  - Nama Asesi: [Nama Peserta Asesmen]
  - Skema Sertifikasi: Pemrogram / Software Developer
  - Basis Teknologi: PHP Native OOP, MySQL InnoDB, Tailwind CSS v4, Vanilla JS
- *Catatan Narasi:* "Selamat pagi/siang Bapak/Ibu Asesor. Hari ini saya mempersembahkan RestoReserve, aplikasi administrasi restoran yang dirancang untuk membuktikan penguasaan 8 unit kompetensi pemrograman secara nyata, mulai dari penulisan kode terstruktur, OOP, akses basis data, hingga pengujian otomatis."

---

### Slide 2: Latar Belakang & Tujuan Pengembangan
- **Latar Belakang Permasalahan:**
  - Risiko double booking pada pemesanan meja restoran jam sibuk.
  - Inkonsistensi perhitungan pesanan bila mengandalkan client-side calculation.
  - Perubahan harga menu yang kerap merusak nilai historis transaksi lama.
- **Tujuan Pengembangan:**
  - Mengotomatiskan validasi jadwal reservasi 2 jam tanpa bentrokan.
  - Menerapkan snapshot harga untuk integritas audit keuangan.
  - Menyediakan bukti konkret penerapan standar industri perangkat lunak.
- *Catatan Narasi:* "RestoReserve menyelesaikan masalah klasik restoran, yaitu bentrokan jadwal meja dan inkonsistensi transaksi. Dengan arsitektur yang bersih, sistem ini menjamin validasi dilakukan secara deterministik di server."

---

### Slide 3: Analisis Kebutuhan & Lingkup Fitur
- **Fitur Utama Sistem:**
  - Autentikasi Admin Sesi Tunggal dengan enkripsi bcrypt dan CSRF guard.
  - Manajemen Master Data (Pelanggan, Meja Restoran, Menu Makanan/Minuman).
  - Modul Reservasi Meja dengan locking transaksi anti race-condition.
  - Modul Pesanan Makanan dengan snapshot harga dan verifikasi server.
  - Modul Laporan & Rekapitulasi Periode dengan ekspor CSV aman.
- **Batasan Sistem yang Ditetapkan:**
  - Khusus admin internal (satu cabang), tanpa payment gateway/kasir kas, durasi tetap 120 menit.
- *Catatan Narasi:* "Lingkup fitur sengaja difokuskan pada operasi inti reservasi dan pesanan tanpa payment gateway, agar seluruh logika bisnis dapat diverifikasi secara mendalam tanpa distraksi modul pihak ketiga."

---

### Slide 4: Arsitektur Perangkat Lunak & Teknologi
- **Stack Teknologi:**
  - Backend: PHP Native OOP 8.2+ (PSR-12, Strict Types).
  - Database: MySQL 8.0+ / MariaDB (InnoDB, Foreign Key Restrict, Transaksi ACID).
  - Frontend: HTML5 Semantik, Tailwind CSS v4 CLI (Offline Compilation), Vanilla JS.
  - Dependency: Composer PSR-4, vlucas/phpdotenv, PHPUnit 10.5.
- **Pola Desain:** MVC Sederhana + Service Layer (Domain Logic) + Repository Pattern (Data Access).
- *Catatan Narasi:* "Kami memilih PHP Native OOP dengan pola MVC dan Service-Repository agar setiap baris kode, percabangan, fungsi, dan class dapat diperiksa langsung tanpa tertutup oleh keajaiban framework besar."

---

### Slide 5: Perancangan Basis Data (ERD & Snapshot Harga)
- **Rancangan 7 Tabel Relasional:**
  - `admins`, `customers`, `restaurant_tables`, `menu_items`, `reservations`, `orders`, `order_items`.
- **Integritas Relasi:**
  - Foreign key dengan constraint `RESTRICT` mencegah data historis terhapus tidak sengaja.
  - Constraint `UNIQUE(reservation_id)` pada orders menjamin relasi 1-to-1 dengan reservasi.
- **Konsep Snapshot Harga:**
  - Kolom `order_items.unit_price` menyalin harga menu saat transaksi dibuat agar kebal terhadap perubahan harga menu di masa depan.
- *Catatan Narasi:* "Tabel didesain dengan 7 entitas ternormalisasi. Konsep snapshot harga pada tabel order_items merupakan best practice industri untuk menjaga data audit keuangan tetap akurat sepanjang masa."

---

### Slide 6: Demonstrasi Fitur Utama (Alur Bisnis Inti)
- **Alur Pengujian Langsung:**
  1. Login Admin & Tinjauan Dashboard Metrik.
  2. Pendaftaran Tamu & Pemilihan Meja.
  3. Reservasi Meja Slot 18.00–20.00 WIB & Pembuktian Penolakan Double Booking pada 19.00 WIB.
  4. Pembuatan Pesanan Makanan (2 Nasi Goreng + 2 Es Teh = Rp60.000).
  5. Konfirmasi Order & Rekapitulasi Laporan Periode.
  6. Unduh CSV dengan sanitasi formula injection.
- *Catatan Narasi:* "Pada demonstrasi langsung ini, kita membuktikan bagaimana sistem menolak bentrokan jam secara real-time dan menghitung nominal pesanan secara akurat di sisi server."

---

### Slide 7: Penerapan 8 Unit Standar Kompetensi Pemrograman
- **Matriks Pembuktian Unit:**
  1. *J.620100.016.01* (Best Practices/PSR-12) &rarr; Strict types, CSRF, sanitasi XSS global.
  2. *J.620100.017.02* (Pemrograman Terstruktur) &rarr; Percabangan `isOverlapping()`, perulangan item.
  3. *J.620100.018.02* (OOP) &rarr; Class, encapsulation, pewarisan `BaseController`.
  4. *J.620100.019.02* (Komponen Pre-existing) &rarr; Composer, Dotenv, Tailwind CLI, PHPUnit.
  5. *J.620100.021.02* (Akses Basis Data) &rarr; PDO, Prepared Statements, transaksi FOR UPDATE.
  6. *J.620100.023.02* (Dokumen Kode) &rarr; PHPDoc lengkap pada semua method publik.
  7. *J.620100.025.02* (Debugging) &rarr; Penelusuran masalah Laragon PATH & CSV injection.
  8. *J.620100.033.02* (Pengujian Unit) &rarr; 19 PHPUnit test suites lulus 100%.
- *Catatan Narasi:* "Kedelapan unit kompetensi diimplementasikan secara utuh dan nyata di dalam source code proyek, lengkap dengan bukti baris kode dan method yang dapat diverifikasi."

---

### Slide 8: Pengujian Otomatis & Penelusuran Masalah (Testing & Debugging)
- **Hasil PHPUnit:**
  - 19 Unit Tests & 37 Assertions &rarr; Status **OK (100% PASS)** tanpa error.
  - Menguji kasus bentrokan interval, toleransi kapasitas, kalkulasi Rupiah, dan penolakan nilai negatif.
- **Pengujian Manual:**
  - 16 skenario uji manual end-to-end (MT-01 s/d MT-16) seluruhnya berstatus **PASS**.
- **Dokumentasi Debugging:**
  - 4 isu nyata (CLI MySQL Laragon, Recursive Linting, CSV Formula Injection, Race Condition Locking) tercatat rapi di `docs/debugging.md`.
- *Catatan Narasi:* "Pengujian otomatis membuktikan logika reservasi dan kalkulator uang bebas dari bug. Bukti debugging mencatat analisis akar masalah dan perbaikan nyata yang telah dilakukan."

---

### Slide 9: Kesimpulan & Rekomendasi
- **Kesimpulan:**
  - RestoReserve berhasil memenuhi seluruh kriteria unjuk kerja pada 8 unit kompetensi keahlian programmer.
  - Aplikasi siap didemonstrasikan secara lokal (offline-ready) tanpa ketergantungan koneksi internet.
  - Kode program rapi, terdokumentasi dengan DocBlock, aman, dan dapat dipertanggungjawabkan.
- **Rencana Pengembangan Selanjutnya:**
  - Notifikasi reminder reservasi via WhatsApp Gateway lokal.
  - Denah interaktif meja restoran (Floor Plan Layout 2D).
- *Catatan Narasi:* "Demikian presentasi sistem RestoReserve. Aplikasi ini telah teruji, stabil, dan memenuhi seluruh tolok ukur kompetensi yang dipersyaratkan. Saya siap menjawab pertanyaan dan mendemonstrasikan kode lebih lanjut. Terima kasih."
