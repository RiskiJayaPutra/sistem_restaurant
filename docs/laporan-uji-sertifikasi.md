# SISTEM MANAJEMEN RESTORAN BERBASIS WEBSITE
### (Uji Sertifikasi Programmer)

**Oleh:**  
**ALYA NAYRA SYAFIQA**  
**NPM 2315061001**  

**FAKULTAS TEKNIK**  
**UNIVERSITAS LAMPUNG**  
**BANDAR LAMPUNG**  
**2026**

---

## 1. Deskripsi Sistem

Sistem Manajemen Restoran merupakan aplikasi berbasis web yang digunakan untuk membantu pengelolaan data pelanggan, meja, menu makanan dan minuman, reservasi, serta pesanan restoran. Sistem ini dikembangkan menggunakan **PHP Native** sebagai bahasa pemrograman dan **MySQL** sebagai basis data untuk menyimpan serta mengelola informasi. 

Sistem menyediakan fitur manajemen pelanggan untuk menambah, melihat, mencari, mengubah, dan menghapus data pelanggan. Fitur manajemen meja digunakan untuk mengelola nomor meja, kapasitas, dan status ketersediaan meja. Sementara itu, fitur manajemen menu memungkinkan admin mengelola informasi makanan dan minuman, termasuk nama menu, harga, dan ketersediaannya. 

Selain pengelolaan data, sistem menyediakan fitur reservasi meja untuk mencatat pemesanan meja berdasarkan pelanggan, tanggal, waktu, dan jumlah tamu. Sistem memeriksa kapasitas meja serta kemungkinan bentrokan jadwal sebelum menyimpan reservasi. Fitur pemesanan makanan dan minuman digunakan untuk mencatat menu yang dipesan, jumlah setiap menu, dan total biaya pesanan. Status pesanan juga dapat diperbarui sesuai proses pelayanan restoran. 

Sistem turut menyediakan fitur laporan dan dashboard untuk menampilkan ringkasan informasi terkait pelanggan, meja, menu, reservasi, dan pesanan. Dengan fitur-fitur tersebut, pengelolaan operasional restoran diharapkan menjadi lebih mudah, tertata, dan mengurangi kesalahan pencatatan secara manual.

---

## 2. Metode Pengembangan

Metode pengembangan yang digunakan untuk Sistem Pemesanan Restoran ini yaitu **metode Waterfall**. Metode ini memiliki tahapan yang berurutan sehingga mudah diterapkan pada pengembangan sistem dengan kebutuhan yang telah ditentukan. Tahapan metode Waterfall pada Sistem Pemesanan Restoran meliputi:

### 2.1 Analisis Kebutuhan

#### 2.1.1 Kebutuhan Fungsional
Adapun Kebutuhan Fungsional dari sistem ini adalah sebagai berikut:

**Tabel 1. Kebutuhan Fungsional**

| Kode | Kebutuhan Fungsional |
|:---:|:---|
| **KF-01** | Sistem menyediakan halaman dashboard yang menampilkan ringkasan informasi aktivitas operasional restoran. |
| **KF-02** | Sistem menyediakan fitur untuk menambah, melihat, mengubah, dan menghapus data pelanggan. |
| **KF-03** | Sistem menyediakan fitur untuk menambah, melihat, mengubah, dan menghapus data meja restoran. |
| **KF-04** | Sistem menyediakan fitur untuk menambah, melihat, mengubah, dan menghapus data menu makanan dan minuman. |
| **KF-05** | Sistem menyediakan fitur untuk membuat, melihat, mengubah, dan menghapus data reservasi pelanggan. |
| **KF-06** | Sistem menyediakan fitur untuk membuat, melihat, mengubah, dan menghapus data pesanan pelanggan. |
| **KF-07** | Sistem menyediakan fitur untuk melihat dan mencetak laporan aktivitas operasional restoran. |

#### 2.1.2 Kebutuhan Non-Fungsional

**Tabel 2. Kebutuhan Non-Fungsional**

| Kode | Kebutuhan Non-Fungsional |
|:---:|:---|
| **KNF-01** | Sistem mampu mencegah serangan manipulasi data melalui SQL Injection (*Prepared Statements PDO*). |
| **KNF-02** | Sistem mampu mencegah serangan skrip berbahaya melalui Cross-Site Scripting (XSS) (*Output Escaping* `htmlspecialchars`). |
| **KNF-03** | Sistem mampu memvalidasi kelayakan seluruh input data formulir di sisi server (*Server-side Validation*). |
| **KNF-04** | Sistem memiliki antarmuka yang responsif dan dapat menyesuaikan berbagai ukuran layar perangkat pengguna (*Responsive Layout*). |
| **KNF-05** | Sistem mampu memberikan pesan notifikasi atau umpan balik langsung atas keberhasilan atau kegagalan setiap aksi pengguna (*Toast / Flash Notification*). |
| **KNF-06** | Sistem memiliki navigasi antarmuka yang intuitif, konsisten, dan mudah dipahami oleh pengguna (*Clean User Interface*). |
| **KNF-07** | Sistem dapat beroperasi secara normal di berbagai peramban web modern (*Cross-browser Compatibility*: Google Chrome, Mozilla Firefox, Microsoft Edge, Safari). |

---

### 2.2 Perancangan Sistem

#### 2.2.1 Use Case Diagram
Adapun Use Case Diagram dari sistem ini memodelkan interaksi antara Staff/Admin dengan fungsionalitas sistem:

*(Gambar 1. Use Case Diagram)*

**Tabel 3. Deskripsi Use Case Diagram**

| No. | Aktor | Use Case | Deskripsi |
|:---:|:---:|:---|:---|
| 1 | Staff | Melihat Dashboard | Staff melihat informasi ringkasan aktivitas operasional restoran yang ditampilkan pada halaman dashboard. |
| 2 | Staff | Mengelola Data Pelanggan | Staff menambah, melihat, mengubah, dan menghapus data pelanggan yang tersimpan dalam sistem. |
| 3 | Staff | Mengelola Data Meja | Staff menambah, melihat, mengubah, dan menghapus data meja restoran untuk mendukung pengelolaan ketersediaan meja. |
| 4 | Staff | Mengelola Data Menu | Staff menambah, melihat, mengubah, dan menghapus data menu makanan dan minuman yang tersedia di restoran. |
| 5 | Staff | Membuat dan Mengelola Reservasi | Staff membuat reservasi pelanggan serta melihat, mengubah, dan mengelola data reservasi yang telah tercatat dalam sistem. |
| 6 | Staff | Membuat dan Mengelola Pesanan | Staff mencatat pesanan pelanggan serta melihat, mengubah, dan mengelola data pesanan selama proses pelayanan restoran. |
| 7 | Staff | Melihat dan Mencetak Laporan | Staff melihat dan mencetak laporan yang dihasilkan sistem untuk memantau aktivitas operasional restoran. |

---

#### 2.2.2 Activity Diagram

##### 1) Menambahkan Data Menu
*(Gambar 2. Menambahkan Data Menu)*  
Activity diagram menambah data menu menggambarkan alur aktivitas Staff dalam menambahkan data menu makanan dan minuman ke dalam sistem restoran. Proses dimulai ketika Staff membuka menu makanan dan minuman, kemudian sistem menampilkan daftar menu yang telah tersimpan. Staff memilih aksi tambah dan mengisi data berupa nama menu, harga, serta ketersediaan menu. Selanjutnya, sistem memvalidasi data yang dimasukkan. Jika data tidak valid, sistem menampilkan pesan kesalahan dan Staff perlu memperbaiki data tersebut. Jika data valid, sistem menyimpan data menu dan menampilkan notifikasi bahwa data berhasil disimpan.

##### 2) Mengedit Data Meja
*(Gambar 3. Mengedit Data Meja)*  
Activity diagram mengedit data meja menggambarkan alur aktivitas Staff dalam memperbarui informasi meja restoran. Proses dimulai ketika Staff membuka menu pengelolaan meja, kemudian sistem menampilkan daftar meja yang tersedia dalam sistem. Staff memilih aksi ubah, menentukan meja yang akan diperbarui, dan mengubah informasi meja sesuai kebutuhan. Setelah itu, sistem memproses perubahan dan memperbarui data meja. Proses diakhiri dengan sistem menampilkan notifikasi bahwa data meja berhasil diperbarui.

##### 3) Menambahkan Data Pelanggan
*(Gambar 4. Menambahkan Data Pelanggan)*  
Activity diagram menambah data pelanggan menggambarkan alur aktivitas Staff dalam menambahkan informasi pelanggan baru ke dalam sistem restoran. Proses dimulai ketika Staff membuka menu pelanggan dan sistem menampilkan daftar pelanggan yang telah tersimpan. Staff kemudian memilih aksi tambah pelanggan serta mengisi data berupa nama, nomor telepon, dan alamat email pelanggan. Sistem selanjutnya memvalidasi data tersebut. Jika data tidak valid, sistem menampilkan pesan kesalahan sehingga Staff dapat memperbaiki data yang dimasukkan. Jika data valid, sistem menyimpan data pelanggan dan menampilkan notifikasi bahwa data berhasil disimpan.

##### 4) Membuat Pesanan
*(Gambar 5. Membuat Pesanan)*  
Activity diagram membuat pesanan menggambarkan alur aktivitas Staff dalam mencatat pesanan pelanggan. Proses dimulai ketika Staff membuka formulir pesanan dan sistem menampilkan formulir yang perlu diisi. Staff memilih pelanggan dan data reservasi apabila tersedia, kemudian menentukan menu yang dipesan beserta kuantitasnya. Setelah data dimasukkan, sistem menyimpan data pesanan dan menghitung total harga berdasarkan menu serta kuantitas yang dipilih. Selanjutnya, sistem menampilkan detail pesanan yang mencakup menu, kuantitas, dan total harga pesanan.

##### 5) Membuat Reservasi
*(Gambar 6. Membuat Reservasi)*  
Activity diagram membuat reservasi menggambarkan alur aktivitas Staff dalam mencatat reservasi pelanggan dengan mempertimbangkan kapasitas meja dan ketersediaan jadwal. Proses dimulai ketika Staff membuka formulir reservasi dan sistem menampilkan formulir tersebut. Staff kemudian memilih pelanggan, meja, tanggal, jam, serta jumlah tamu. Sistem memvalidasi kapasitas meja dan memeriksa kemungkinan bentrokan jadwal reservasi. Jika terjadi bentrokan, sistem menampilkan pesan kegagalan dan Staff kembali ke formulir untuk memperbaiki data reservasi. Jika tidak terdapat bentrokan jadwal, sistem menyimpan data reservasi dengan status Menunggu dan menampilkan notifikasi bahwa reservasi berhasil dibuat.

##### 6) Mencetak Laporan
*(Gambar 7. Mencetak Laporan)*  
Activity diagram mencetak laporan menggambarkan alur aktivitas Staff dalam menampilkan serta mencetak laporan berdasarkan jenis laporan dan rentang tanggal yang ditentukan. Proses dimulai ketika Staff membuka menu Laporan, kemudian sistem menampilkan halaman laporan yang menyediakan pilihan jenis laporan, tanggal mulai, dan tanggal akhir. Staff memilih jenis laporan dan menentukan periode laporan, lalu menekan tombol Tampilkan. Sistem memvalidasi rentang tanggal dan memproses data sesuai kriteria yang dipilih. Selanjutnya, sistem menampilkan ringkasan dan tabel laporan apabila data tersedia. Jika data tidak ditemukan atau rentang tanggal tidak valid, sistem menampilkan informasi yang sesuai. Staff dapat memilih tombol Cetak untuk mencetak laporan yang ditampilkan. Proses berakhir setelah laporan berhasil ditampilkan atau proses pencetakan selesai dilakukan.

---

### 2.3 Implementasi

Tahap ini dilakukan dengan menerjemahkan hasil perancangan ke dalam kode program yang terstruktur rapi.

#### 2.3.0 Teknologi dan Bahasa Pemrograman yang Digunakan
Sistem Manajemen Restoran ini dibangun menggunakan kombinasi bahasa pemrograman dan teknologi web standar industri sebagai berikut:

1. **PHP (Hypertext Preprocessor) Versi 8.2 (PHP Native - Back-End Core):**
   - Digunakan sebagai bahasa pemrograman utama sisi server (*server-side scripting*).
   - Mengimplementasikan pola arsitektur **MVC (Model-View-Controller)** sederhana dan modular tanpa dependensi framework luar (*Native/Pure PHP*).
   - Menggunakan ekstensi **PDO (PHP Data Objects)** dengan fitur *Prepared Statements* untuk mengeksekusi *query* secara aman dari kerentanan SQL Injection.
   - Mengelola sesi otentikasi login/logout pengguna melalui *native session management* PHP (`session_start()`).

2. **SQL (Structured Query Language) / MySQL (Database Management):**
   - Digunakan untuk merancang dan memanipulasi basis data relasional (*RDBMS*).
   - Terdiri dari 6 tabel relasional yang saling terhubung: `users`, `customers`, `tables`, `menu_items`, `reservations`, dan `orders`/`order_items` dengan integritas referensial *Foreign Key*.

3. **HTML5 (HyperText Markup Language):**
   - Digunakan untuk menyusun struktur kerangka halaman web secara semantik (*Semantic HTML5*), formulir input, tabel data rekapitulasi, dan navigasi antarmuka.

4. **CSS3 (Cascading Style Sheets):**
   - Digunakan untuk penataan tata letak (*layouting*), tipografi, estetika visual, dan skema warna antarmuka pengguna.
   - Mengombinasikan utilitas modern (*Flexbox*, *CSS Grid*) serta prinsip desain responsif (*Media Queries*) agar tampilan optimal di desktop, tablet, maupun ponsel.

5. **JavaScript (ECMAScript 6+ Native - Front-End Scripting):**
   - Digunakan untuk memberikan interaktivitas dinamis pada peramban web (*client-side*).
   - Menghitung subtotal dan grand total pemesanan menu makanan secara otomatis (*real-time calculation*).
   - Menangani dialog konfirmasi aksi (hapus data, konfirmasi logout), validasi interaktif formulir, dan penutupan otomatis notifikasi umpan balik (*flash toast alert*).

---

#### Hasil Tampilan Antarmuka Sistem
Berikut merupakan dokumentasi hasil implementasi antarmuka Sistem Manajemen Restoran:

- **2.3.1 Hasil Tampilan Dashboard**  
  *(Gambar 8. Hasil Tampilan Dashboard)*
- **2.3.2 Hasil Tampilan Menu Pelanggan**  
  *(Gambar 9. Hasil Tampilan Menu Pelanggan)*
- **2.3.3 Hasil Tampilan Menu Meja**  
  *(Gambar 10. Hasil Tampilan Menu Meja)*
- **2.3.4 Hasil Tampilan Menu**  
  *(Gambar 11. Hasil Tampilan Menu)*
- **2.3.5 Hasil Tampilan Menu Reservasi**  
  *(Gambar 12. Hasil Tampilan Menu Reservasi)*
- **2.3.6 Hasil Tampilan Menu Pesanan**  
  *(Gambar 13. Hasil Tampilan Menu Pesanan)*
- **2.3.7 Hasil Tampilan Menu Laporan**  
  *(Gambar 14. Hasil Tampilan Menu Laporan)*

---

### 2.4 Pengujian

Pengujian Sistem Manajemen Restoran dilakukan menggunakan kombinasi metode **Pengujian Unit Otomatis (*Unit Testing* PHPUnit)** dan **Pengujian Fungsional Manual (*Black Box Testing*)** untuk memastikan setiap fitur dan logika bisnis berjalan sesuai dengan kebutuhan fungsional yang telah ditentukan.

#### 2.4.1 Pengujian Unit Otomatis (PHPUnit)
Pengujian unit dijalankan secara otomatis pada logika bisnis inti (*isolated domain logic*) tanpa dependensi basis data, mencakup validasi bentrokan waktu 2 jam, kalkulasi kuantitas dan harga pesanan, serta aturan transisi status reservasi.

Hasil eksekusi pada terminal menunjukkan seluruh pengujian berhasil lulus:
```text
PHPUnit 10.5.66 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.4.12
Configuration: C:\laragon\www\sistem_restoran\phpunit.xml

...................                                               19 / 19 (100%)

Time: 00:02.042, Memory: 8.00 MB
OK (19 tests, 37 assertions)
```

#### 2.4.2 Matriks Pengujian Fungsional (Black Box Testing - 26 Skenario)
Pengujian fungsional manual mencakup 26 skenario uji yang mengevaluasi skenario positif (*normal path*), skenario negatif (*error handling*), skenario batas aturan bisnis (*boundary rules*), serta keamanan sistem:

**Tabel 4. Matriks Pengujian Fungsional Black Box Testing**

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

Berdasarkan seluruh hasil pengujian yang dilakukan, seluruh 26 skenario fungsional dan 19 unit pengujian otomatis berhasil dijalankan dengan status **PASS (100% Berhasil)** tanpa ditemukan kesalahan fungsi (*bug*) yang menghambat operasional sistem.

---

### 2.5 Pemeliharaan

Tahap pemeliharaan dilakukan untuk menjaga agar Sistem Pemesanan Restoran tetap berjalan sesuai dengan kebutuhan pengguna. Kegiatan pemeliharaan meliputi perbaikan kesalahan fungsi yang ditemukan setelah sistem digunakan, pemeriksaan data dan proses transaksi, serta penyesuaian fitur apabila terdapat perubahan kebutuhan. Pemeliharaan juga dilakukan dengan memeriksa fungsi pengelolaan pelanggan, meja, menu, reservasi, pemesanan, dan laporan agar sistem tetap dapat digunakan dengan baik.

---

## 3. Pembahasan dan Diskusi

Berdasarkan hasil perancangan, implementasi, dan pengujian yang telah dilakukan, terdapat beberapa poin penting yang menjadi pembahasan dalam pengembangan Sistem Manajemen Restoran ini:

### 3.1 Keunggulan dan Efektivitas Sistem
1. **Otomatisasi Validasi Bentrokan Jadwal (*Conflict Detection*):**  
   Sistem berhasil mengatasi salah satu masalah utama pada pengelolaan restoran konvensional, yaitu risiko pemesanan ganda (*double booking*) pada meja yang sama. Melalui algoritma pengecekan di sisi *server*, sistem secara otomatis menolak reservasi baru apabila nomor meja dan rentang jam yang dipilih telah terisi oleh reservasi lain yang aktif.
2. **Arsitektur Ringan dan Mandiri (*Lightweight Native Architecture*):**  
   Penggunaan bahasa PHP Native dengan penerapan pola arsitektur MVC sederhana membuat aplikasi memiliki performa eksekusi yang cepat, penggunaan memori yang efisien, serta kemudahan proses pemasangan (*deployment*) di berbagai peladen web tanpa beban ketergantungan pada *framework* eksternal.
3. **Kalkulasi Pesanan yang Konsisten dan Aman:**  
   Sistem memadukan kemudahan antarmuka melalui kalkulasi otomatis di sisi peramban (*JavaScript*) untuk menampilkan total tagihan secara instan, sekaligus menerapkan validasi ulang nominal harga di sisi *server* (*PHP*) untuk menjamin data pembayaran tetap akurat dan tidak dapat dimanipulasi dari sisi klien.

### 3.2 Tantangan Teknis dan Solusi
1. **Pengecekan Ketersediaan Meja Secara Real-Time:**  
   - *Tantangan:* Memastikan status keterisian meja selalu sinkron dengan jadwal reservasi pelanggan tanpa menimbulkan penurunan performa kueri basis data.  
   - *Solusi:* Menerapkan pengindeksan (*indexing*) pada kolom tanggal dan jam reservasi serta merancang kueri SQL bersyarat yang mengevaluasi tumpang tindih waktu secara spesifik.
2. **Keamanan Transaksi Data Sensitif:**  
   - *Tantangan:* Mencegah potensi serangan siber umum seperti manipulasi parameter kueri SQL dan penyisipan skrip jahat pada data masukan formulir.  
   - *Solusi:* Seluruh interaksi basis data wajib menggunakan mekanisme *Prepared Statements PDO*, serta seluruh data yang ditampilkan ke peramban diproses menggunakan fungsi *output escaping* `htmlspecialchars()`.

### 3.3 Keterbatasan Sistem
1. **Metode Pembayaran:** Proses pembayaran saat ini difokuskan pada pencatatan kasir internal (tunai/manual) dan belum terintegrasi secara langsung dengan gerbang pembayaran daring (*payment gateway*) seperti Midtrans atau QRIS.
2. **Notifikasi Pelanggan:** Informasi keberhasilan reservasi saat ini baru ditampilkan di layar antarmuka sistem dan belum terhubung ke layanan pengiriman pesan otomatis (seperti WhatsApp Gateway atau surel pelanggan).
3. **Cakupan Akses Pengguna:** Sistem saat ini difokuskan untuk penggunaan staf/petugas operasional restoran di tempat (*internal back-office*) dan belum menyediakan portal reservasi daring mandiri untuk pelanggan umum.

### 3.4 Saran Pengembangan Selanjutnya (*Future Enhancements*)
1. **Integrasi Gerbang Pembayaran (*Payment Gateway*):** Menghubungkan sistem dengan API pembayaran digital untuk memfasilitasi transaksi nontunai dan penerbitan nota elektronik secara otomatis.
2. **Layanan Pengingat Otomatis (*Notification Reminder*):** Mengintegrasikan WhatsApp API atau SMTP Mailer untuk mengirimkan pengingat jadwal reservasi (H-1 jam) kepada pelanggan.
3. **Visualisasi Tata Letak Meja Interaktif:** Mengembangkan tampilan denah meja visual berbasis grafis interaktif (*floor plan visualizer*) untuk memudahkan staf dalam memantau dan memilih meja secara visual.

---

## 4. Kesimpulan

Berdasarkan seluruh tahapan pengembangan yang telah diselesaikan, dapat disimpulkan bahwa:
1. Sistem Manajemen Restoran Berbasis Website berhasil dikembangkan menggunakan metode Waterfall, mencakup tahapan analisis kebutuhan, perancangan diagram UML, implementasi kode program, hingga pengujian fungsionalitas.
2. Sistem telah berhasil mengimplementasikan seluruh kebutuhan fungsional (KF-01 hingga KF-07) dan kebutuhan non-fungsional (KNF-01 hingga KNF-07), meliputi manajemen data pelanggan, meja, menu, reservasi anti-bentrok, pencatatan pesanan, dan pencetakan laporan.
3. Berdasarkan pengujian menggunakan metode *Black Box Testing*, seluruh fitur berjalan dengan baik tanpa ditemukan kesalahan fungsi (*bug*), membuktikan bahwa sistem layak dan siap digunakan untuk mendukung efisiensi operasional restoran.

