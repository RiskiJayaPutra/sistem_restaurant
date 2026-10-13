# Laporan Pengujian Perangkat Lunak — RestoReserve

Dokumen ini memuat bukti pelaksanaan **Pengujian Unit Otomatis (PHPUnit)** dan **Pengujian Fungsional Manual End-to-End (Black Box Testing)** pada aplikasi **RestoReserve**.

---

## 1. Pengujian Unit Otomatis (PHPUnit)

Pengujian unit dijalankan menggunakan PHPUnit 10.5 pada lingkungan lokal Windows dengan PHP 8.4.12. Seluruh tes inti dijalankan secara murni tanpa ketergantungan basis data (*isolated domain logic*).

### Perintah Eksekusi
```powershell
php vendor/phpunit/phpunit/phpunit
```

### Log Hasil Eksekusi Aktual
```text
PHPUnit 10.5.66 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.12
Configuration: C:\laragon\www\sistem_restoran\phpunit.xml

...................                                               19 / 19 (100%)

Time: 00:02.042, Memory: 8.00 MB

OK (19 tests, 37 assertions)
```

### Matriks Kasus Uji Otomatis (UT-01 s/d UT-19)

| ID Uji | File Test | Skenario Kasus Uji | Data Masukan | Hasil yang Diharapkan | Hasil Aktual | Status |
|:--:|:---|:---|:---|:---|:---|:--:|
| **UT-01** | `BookingRulesTest` | Interval 18.00–20.00 vs 19.00–21.00 | `[18:00, 20:00]` & `[19:00, 21:00]` | Terdeteksi bentrok (`true`) | Mengembalikan `true` | **PASS** |
| **UT-02** | `BookingRulesTest` | Interval 18.00–20.00 vs 20.00–22.00 | `[18:00, 20:00]` & `[20:00, 22:00]` | Tidak bentrok (`false`) | Mengembalikan `false` | **PASS** |
| **UT-03** | `BookingRulesTest` | Interval 18.00–20.00 vs 16.00–18.00 | `[18:00, 20:00]` & `[16:00, 18:00]` | Tidak bentrok (`false`) | Mengembalikan `false` | **PASS** |
| **UT-04** | `BookingRulesTest` | Jumlah tamu 5 pada meja kapasitas 4 | `guests: 5, capacity: 4` | Ditolak (`false`) | Mengembalikan `false` | **PASS** |
| **UT-05** | `BookingRulesTest` | Jumlah tamu 4 pada meja kapasitas 4 | `guests: 4, capacity: 4` | Diterima (`true`) | Mengembalikan `true` | **PASS** |
| **UT-06** | `OrderCalculatorTest` | Subtotal 2 × Rp25.000 | `qty: 2, price: 25000` | Subtotal `50000` | Nilai `50000` | **PASS** |
| **UT-07** | `OrderCalculatorTest` | Dua item: `2 × 25.000 + 2 × 5.000` | 2 Nasi Goreng + 2 Es Teh | Grand Total `60000` | Nilai `60000` | **PASS** |
| **UT-08** | `OrderCalculatorTest` | Kuantitas 0 pada pesanan | `qty: 0, price: 25000` | Ditolak dengan Exception | Lempar `InvalidArgumentException` | **PASS** |
| **UT-09** | `BookingRulesTest` | Transisi status `pending` &rarr; `confirmed` | `from: pending, to: confirmed` | Diizinkan (`true`) | Mengembalikan `true` | **PASS** |
| **UT-10** | `BookingRulesTest` | Transisi status `cancelled` &rarr; `confirmed` | `from: cancelled, to: confirmed` | Ditolak (`false`) | Mengembalikan `false` | **PASS** |
| **UT-11** | `OrderCalculatorTest` | Menu/harga negatif atau tidak valid | `qty: 2, price: -5000` | Ditolak dengan Exception | Lempar `InvalidArgumentException` | **PASS** |
| **UT-12** | `OrderCalculatorTest` | Daftar item pesanan kosong saat kalkulasi | `items: []` | Ditolak dengan Exception | Lempar `InvalidArgumentException` | **PASS** |
| **UT-13** | `BookingRulesTest` | Kalkulasi durasi reservasi tepat 2 jam | `start: 14:30:00` | End time `16:30:00` | `16:30:00` | **PASS** |
| **UT-14** | `OrderCalculatorTest` | Konsolidasi menu duplikat | Menu ID 1 (qty 2 + 3) | 1 baris Menu ID 1 (qty 5) | Consolidated count 2, qty 5 | **PASS** |
| **UT-15** | `ReservationStatusTest` | Pending &rarr; Cancelled | `pending -> cancelled` | Diizinkan | Mengembalikan `true` | **PASS** |
| **UT-16** | `ReservationStatusTest` | Pending &rarr; Completed (loncat status) | `pending -> completed` | Ditolak (harus konfirmasi dulu) | Mengembalikan `false` | **PASS** |
| **UT-17** | `ReservationStatusTest` | Confirmed &rarr; Completed | `confirmed -> completed` | Diizinkan | Mengembalikan `true` | **PASS** |
| **UT-18** | `ReservationStatusTest` | Completed &rarr; Confirmed (status terminal) | `completed -> confirmed` | Ditolak | Mengembalikan `false` | **PASS** |
| **UT-19** | `ReservationStatusTest` | Status yang sama ke status yang sama | `pending -> pending` | Idempoten / Diizinkan | Mengembalikan `true` | **PASS** |

---

## 2. Pengujian Fungsional Manual End-to-End (Black Box Testing MT-01 s/d MT-26)

Pengujian fungsional manual dilakukan pada seluruh modul operasional sistem dengan mencakup skenario positif (*normal/happy path*), skenario negatif (*error handling*), skenario batas (*boundary logic*), dan skenario keamanan (*security validation*).

| ID Uji | Modul | Skenario Langkah Pengujian | Data Masukan (Input) | Hasil yang Diharapkan | Hasil Aktual | Status |
|:--:|:---|:---|:---|:---|:---|:--:|
| **MT-01** | Autentikasi | Login dengan username & password benar | `admin` / `password123` | Berhasil autentikasi, redirect ke `/`, muncul flash selamat datang | Berhasil login dan masuk Dashboard | **PASS** |
| **MT-02** | Autentikasi | Login dengan password salah | `admin` / `salah123` | Gagal login, tetap di `/login`, muncul pesan kredensial tidak valid | Muncul alert kredensial salah | **PASS** |
| **MT-03** | Autentikasi | Mengakses halaman internal tanpa login | Buka URL `/customers` | Dicegat oleh middleware auth, dialihkan ke `/login` | Teredirect ke login dengan pesan peringatan | **PASS** |
| **MT-04** | Autentikasi | Logout dari sistem | Klik tombol logout pada sidebar | Sesi dihapus, dialihkan kembali ke `/login` | Sesi berakhir dan kembali ke login | **PASS** |
| **MT-05** | Pelanggan | Tambah data pelanggan baru yang valid | Nama: `Budi Hartono`, HP: `081234567899` | Data tersimpan di MySQL dan muncul di tabel pelanggan | Data tersimpan & muncul di list | **PASS** |
| **MT-06** | Pelanggan | Tambah pelanggan dengan nama kosong | Nama: `[kosong]`, HP: `081234567899` | Validasi form menolak submit, nama wajib diisi | Ditolak dengan pesan validasi nama wajib | **PASS** |
| **MT-07** | Pelanggan | Perbarui data nomor telepon pelanggan | Ubah HP menjadi `081299998888` | Data terbarui di database dan tabel | Data berhasil diperbarui | **PASS** |
| **MT-08** | Pelanggan | Hapus pelanggan yang memiliki riwayat reservasi | Klik hapus pelanggan aktif | Ditolak demi integritas referensial data (*Foreign Key*) | Ditolak dengan pesan flash ramah | **PASS** |
| **MT-09** | Meja Restoran | Tambah meja baru dengan nomor & kapasitas valid | Nomor: `M10`, Kapasitas: `4` | Meja tersimpan di denah dan basis data | Meja baru muncul di denah & daftar | **PASS** |
| **MT-10** | Meja Restoran | Tambah meja dengan nomor duplikat | Nomor: `M01` (sudah ada) | Form menolak dengan pesan nomor meja sudah digunakan | Muncul pesan error validasi nomor unik | **PASS** |
| **MT-11** | Meja Restoran | Tambah meja dengan kapasitas 0 atau minus | Nomor: `M11`, Kapasitas: `0` | Ditolak, kapasitas meja minimal 1 orang | Ditolak dengan validasi kapasitas minimal | **PASS** |
| **MT-12** | Menu Makanan | Tambah menu baru dengan harga valid | Nama: `Ayam Bakar`, Kategori: `Makanan`, Harga: `35000` | Menu tersimpan di daftar sajian | Menu baru muncul di daftar menu | **PASS** |
| **MT-13** | Menu Makanan | Tambah menu dengan harga minus atau teks | Nama: `Es Jeruk`, Harga: `-5000` | Ditolak, harga harus berupa nominal positif | Ditolak dengan pesan harga tidak valid | **PASS** |
| **MT-14** | Menu Makanan | Ubah harga master menu setelah pesanan dibuat | Ubah harga Nasi Goreng di `/menus` | Nilai pesanan lama tetap utuh (*Snapshot harga aman*) | Nilai order historis tetap utuh | **PASS** |
| **MT-15** | Reservasi | Reservasi pada meja kosong dengan kapasitas pas | Meja `M03`, Besok 18:00 WIB, 4 Tamu | Reservasi tersimpan dengan status `Pending` | Tersimpan status Pending di DB | **PASS** |
| **MT-16** | Reservasi | Reservasi di meja sama pada rentang jam bertabrakan | Meja `M03`, Besok 19:00 WIB, 2 Tamu | Sistem menolak dengan pesan bentrokan jadwal 2 jam | Ditolak dengan alert bentrokan | **PASS** |
| **MT-17** | Reservasi | Reservasi tepat setelah slot 2 jam selesai | Meja `M03`, Besok 20:00 WIB, 3 Tamu | Bersebelahan tepat 2 jam diterima tanpa konflik | Berhasil tersimpan status Pending | **PASS** |
| **MT-18** | Reservasi | Tamu melebihi kapasitas maksimal kursi meja | Meja `M01` (kapasitas 2) dengan 5 tamu | Sistem menolak karena melebihi kapasitas meja | Ditolak dengan pesan kapasitas terlampaui | **PASS** |
| **MT-19** | Reservasi | Konfirmasi status reservasi aktif | Ubah status `Pending` &rarr; `Confirmed` | Status terkonfirmasi dan siap dipesankan makanan | Status diperbarui menjadi Confirmed | **PASS** |
| **MT-20** | Reservasi | Pembatalan reservasi oleh admin | Ubah status menjadi `Cancelled` | Status menjadi Batal dan slot meja kembali bebas | Meja kembali dapat dipesan orang lain | **PASS** |
| **MT-21** | Pesanan | Buat pesanan: 2 Nasi Goreng + 2 Es Teh | 2 × Rp25.000 + 2 × Rp5.000 | Total dihitung server tepat Rp60.000 | Total pesanan tepat Rp60.000 | **PASS** |
| **MT-22** | Pesanan | Simpan pesanan tanpa memilih menu (kosong) | Tidak mencentang menu apa pun | Ditolak, wajib memilih minimal 1 menu | Ditolak dengan peringatan pesanan kosong | **PASS** |
| **MT-23** | Pesanan | Mengisi porsi pesanan dengan angka 0 | Menu ID 1 dengan kuantitas `0` | Ditolak, kuantitas porsi harus minimal 1 | Ditolak dengan peringatan kuantitas | **PASS** |
| **MT-24** | Laporan | Filter omzet periode rentang tanggal valid | Filter tanggal awal s.d. tanggal akhir | Menampilkan akumulasi omzet pesanan terkonfirmasi | Total laporan sesuai agregat | **PASS** |
| **MT-25** | Laporan | Unduh ekspor laporan transaksi ke CSV | Klik tombol `Ekspor ke Format CSV` | File CSV terunduh dengan BOM UTF-8 dan data tersanitasi | File terunduh rapi di spreadsheet | **PASS** |
| **MT-26** | Keamanan | Pengiriman form POST tanpa token CSRF | Submit form tanpa `_csrf_token` | Permintaan ditolak dengan HTTP 403 Forbidden | Ditolak dengan halaman 403 CSRF Error | **PASS** |
