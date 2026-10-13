# LAPORAN PENGEMBANGAN SISTEM PERANGKAT LUNAK

## SISTEM RESERVASI MEJA DAN PEMESANAN MAKANAN (RESTORESERVE)
### TUGAS PRAKTIK DEMONSTRASI UJI KOMPETENSI KEAHLIAN PROGRAMMER
**Skema Sertifikasi:** Pemrogram (Software Developer)  

---

### IDENTITAS ASESI / PESERTA UJI

| Komponen Identitas | Keterangan |
|---|---|
| **Nama Peserta** | `[Isi Nama Lengkap Peserta]` |
| **Nomor Peserta / NIK** | `[Isi Nomor Peserta Uji / NIK]` |
| **Tempat Asesmen (TUK)** | `[Isi Tempat Uji Kompetensi]` |
| **Tanggal Asesmen** | `[Isi Tanggal Pelaksanaan Uji]` |
| **Nama Asesor** | `[Isi Nama Asesor Penguji]` |

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang
Operasional restoran modern skala menengah dan kecil sering menghadapi kendala pengelolaan pemesanan meja pada jam sibuk (*peak hours*). Penggunaan buku catatan manual atau sistem pencatatan konvensional kerap menimbulkan masalah *double booking* (dua tamu memesan meja yang sama di waktu yang bertabrakan) serta kekeliruan perhitungan nominal pesanan. 

Selain itu, ketika harga menu makanan mengalami pembaruan, sistem informasi yang tidak dirancang dengan baik kerap kali mengubah nilai transaksi masa lalu secara keliru karena tidak menerapkan mekanisme *snapshot* harga satuan.

Oleh karena itu, dikembangkan sistem **RestoReserve — Sistem Reservasi Meja dan Pemesanan Makanan**. Aplikasi ini dibangun sebagai bukti pemenuhan kompetensi standar SKKNI bagi pemrogram perangkat lunak dalam merancang logika terstruktur, menerapkan pemrograman berorientasi objek (OOP), mengelola basis data relasional dengan transaksi ACID, memanfaatkan pustaka pre-existing, serta melakukan pengujian unit otomatis dan penelusuran kesalahan (debugging).

### 1.2 Rumusan Masalah
1. Bagaimana merancang algoritma deteksi bentrokan waktu reservasi meja secara deterministik?
2. Bagaimana menjamin keakuratan perhitungan pesanan makanan di sisi server dan menjaga integritas audit historis saat terjadi perubahan harga menu master?
3. Bagaimana mencegah kondisi balapan (*race condition*) ketika beberapa reservasi diajukan pada waktu yang hampir bersamaan untuk meja yang sama?

### 1.3 Tujuan Pengembangan
1. Membangun sistem administrasi reservasi meja restoran dengan durasi tetap 2 jam (120 menit) yang menolak jadwal tumpang tindih secara otomatis.
2. Menerapkan penghitungan nilai pesanan berbasis server dengan mekanisme *snapshot* harga ke entitas rincian pesanan.
3. Mengimplementasikan transaksi basis data MySQL dengan *row-level locking* untuk mencegah inkonsistensi jadwal.
4. Menyediakan modul laporan operasional dengan ekspor berkas CSV yang kebal terhadap serangan *spreadsheet formula injection*.
5. Memenuhi 8 unit kompetensi dalam skema sertifikasi pemrogram.

### 1.4 Batasan Sistem
1. Sistem dikhususkan untuk **satu pengguna (Admin)** tanpa pendaftaran akun publik.
2. Operasional dirancang untuk **satu cabang restoran**.
3. Durasi setiap reservasi ditetapkan tepat **2 jam (120 menit)**.
4. Satu reservasi maksimal memiliki **satu pesanan makanan**.
5. Sistem **tidak menyediakan modul pembayaran/kasir** (*payment gateway*); nilai uang yang dilaporkan merupakan agregasi nilai pesanan terkonfirmasi, bukan bukti penerimaan uang kas.

---

## BAB II: ANALISIS DAN PERANCANGAN SISTEM

### 2.1 Kebutuhan Fungsional (Functional Requirements)
1. **Autentikasi:** Admin dapat melakukan masuk (login) dan keluar (logout) dengan verifikasi sesi aman.
2. **Kelola Pelanggan:** Admin dapat menambah, melihat, memperbarui, dan menghapus data pelanggan (hanya jika pelanggan belum memiliki riwayat reservasi).
3. **Kelola Meja:** Admin dapat mengelola nomor meja unik, kapasitas kursi (1–20 orang), dan status keaktifan meja.
4. **Kelola Menu:** Admin dapat mengelola nama menu, kategori (Makanan/Minuman), harga bilangan bulat Rupiah, dan status ketersediaan.
5. **Reservasi Meja:** Admin dapat membuat reservasi meja dengan validasi bentrokan waktu, batas kapasitas tamu, serta mengubah status reservasi (`pending`, `confirmed`, `completed`, `cancelled`).
6. **Pesanan Makanan:** Admin dapat membuat draf pesanan makanan untuk reservasi berstatus `confirmed`, menambah/mengurangi menu dengan kalkulasi preview, mengonfirmasi pesanan, atau membatalkannya.
7. **Laporan & Ekspor:** Admin dapat memfilter laporan berdasarkan rentang tanggal reservasi, melihat rekap nilai pesanan terkonfirmasi, dan mengunduh berkas CSV.

### 2.2 Kebutuhan Non-Fungsional (Non-Functional Requirements)
1. **Keamanan:** Bebas SQL Injection (100% PDO Prepared Statements), bebas XSS (escaping `e()`), bebas CSRF (token acak 32-byte), password terenkripsi bcrypt.
2. **Kinerja:** Halaman dapat dirender di bawah 100 ms tanpa memuat aset eksternal via CDN (offline-ready).
3. **Integritas Data:** Menggunakan basis data MySQL engine InnoDB dengan konstrain *Foreign Key RESTRICT*.
4. **Ketahanan:** Menerapkan transaksi ACID dan *row-level lock* (`SELECT ... FOR UPDATE`) pada meja saat reservasi diproses.

### 2.3 Perancangan Arsitektur Perangkat Lunak
Sistem mengadopsi pola **MVC Sederhana** dengan pemisahan lapisan logika bisnis (*Service*) dan akses data (*Repository*):

```text
HTTP Request -> public/index.php -> Router -> Middleware (Auth/CSRF) 
             -> Controller -> Service -> Repository -> PDO MySQL
             -> View Template (Tailwind CSS) -> HTTP Response
```

### 2.4 Perancangan Basis Data (ERD)
Sistem memiliki 7 tabel relasional yang saling terhubung:
1. `admins` (id, username, password_hash, created_at)
2. `customers` (id, name, phone, email, created_at, updated_at)
3. `restaurant_tables` (id, table_number, capacity, is_active)
4. `menu_items` (id, name, category, price, is_available)
5. `reservations` (id, customer_id, table_id, reserved_at, guest_count, notes, status, created_at, updated_at)
6. `orders` (id, reservation_id, status, total, confirmed_at, created_at, updated_at)
7. `order_items` (id, order_id, menu_item_id, quantity, unit_price, subtotal)

---

## BAB III: IMPLEMENTASI SISTEM

### 3.1 Lingkungan Pengembangan & Perkakas
- **Bahasa Pemrograman:** PHP 8.4 (Native OOP, PSR-12, Strict Types)
- **Basis Data:** MySQL 8.0.30 Community Server (InnoDB Engine)
- **Web Server:** PHP Built-in Server dengan router statis
- **Package Manager:** Composer 2.8.11 & npm 10.9.3 (Node.js v22)
- **Styling UI:** Tailwind CSS v4.3.3 (dikompilasi lokal offline via CLI)
- **Unit Testing:** PHPUnit 10.5.66

### 3.2 Implementasi Pemrograman Berorientasi Objek (OOP)
Penerapan konsep OOP terlihat jelas pada struktur pewarisan kelas:
- Kelas `BaseController` mewarisi fungsi-fungsi umum seperti `view()`, `validateCsrf()`, dan `requireAuth()`. Seluruh controller turunan (`CustomerController`, `ReservationController`, dll.) mewarisi kelas ini.
- Prinsip enkapsulasi diterapkan dengan membatasi properti instance menjadi `private` dan menyediakan method publik ber-type hinting kuat.

### 3.3 Implementasi Pemrograman Terstruktur
Logika inti perhitungan bentrokan jadwal reservasi diimplementasikan pada kelas `BookingRules`:
```php
public static function isOverlapping(string $startA, string $endA, string $startB, string $endB): bool
{
    $dtStartA = new DateTimeImmutable($startA);
    $dtEndA   = new DateTimeImmutable($endA);
    $dtStartB = new DateTimeImmutable($startB);
    $dtEndB   = new DateTimeImmutable($endB);

    return ($dtStartA < $dtEndB) && ($dtEndA > $dtStartB);
}
```

### 3.4 Implementasi Akses Basis Data PDO & Transaksi
Proses penyimpanan reservasi menerapkan row-level locking:
```php
$this->db->beginTransaction();
$lockStmt = $this->db->prepare('SELECT id, capacity, is_active FROM restaurant_tables WHERE id = :id FOR UPDATE');
$lockStmt->execute([':id' => $tableId]);
...
$this->db->commit();
```

---

## BAB IV: PENGUJIAN DAN DEBUGGING

### 4.1 Hasil Pengujian Unit Otomatis (PHPUnit)
Pengujian unit dijalankan terhadap aturan bisnis inti tanpa ketergantungan basis data:
- **Total Pengujian:** 19 Kasus Uji
- **Total Asersi:** 37 Asersi
- **Hasil Eksekusi:** **OK (100% Lulus / 0 Gagal / 0 Error)**
- **Waktu Eksekusi:** 0.035 detik

### 4.2 Hasil Pengujian Manual End-to-End
16 skenario pengujian fungsional manual (MT-01 sampai MT-16) diuji secara menyeluruh:
- Login valid & invalid: **PASS**
- Pencegahan akses tanpa sesi: **PASS**
- Operasi CRUD Pelanggan, Meja, dan Menu: **PASS**
- Penolakan reservasi bentrok jam pada meja yang sama: **PASS**
- Penolakan tamu melebihi kapasitas meja: **PASS**
- Snapshot harga dan total pesanan server-side: **PASS**
- Ekspor CSV dengan sanitasi formula: **PASS**
- Penolakan request POST tanpa token CSRF: **PASS**

### 4.3 Bukti Penelusuran Masalah (Debugging)
Selama fase pengembangan, tercatat 4 isu teknis nyata yang berhasil dianalisis dan diselesaikan:
1. *Isu Biner MySQL Laragon:* Pemanggilan biner MySQL dari terminal umum diselesaikan dengan penentuan path absolut Laragon.
2. *Isu Recursive Linting PowerShell:* Penyaringan direktori linting diperbaiki agar tidak memindai puluhan ribu file pada direktori vendor parser.
3. *Isu Spreadsheet Formula Injection:* Diatasi dengan penambahan fungsi `sanitizeCsvValue()` yang memberikan prefix kutip tunggal `'` pada teks yang diawali simbol matematika.
4. *Isu Race Condition Booking Meja:* Diatasi melalui implementasi transaksi InnoDB dengan `SELECT ... FOR UPDATE` pada meja restoran.

---

## BAB V: PENUTUP

### 5.1 Kesimpulan
Aplikasi **RestoReserve** berhasil dibangun dan diuji dengan sukses sesuai seluruh kriteria dan spesifikasi kebutuhan fungsional, pedoman antarmuka pengguna, serta standar interaksi sistem. Seluruh unit kompetensi dalam skema sertifikasi pemrogram telah terpenuhi dan dibuktikan dengan kode program nyata, basis data relasional yang berfungsi penuh, pengujian unit 100% PASS, serta dokumentasi yang lengkap.

### 5.2 Saran Pengembangan
1. Menambahkan integrasi pengiriman pesan pengingat jadwal reservasi via WhatsApp Gateway lokal.
2. Mengembangkan visualisasi denah tata letak meja restoran secara interaktif (Floor Plan 2D) pada antarmuka admin.
