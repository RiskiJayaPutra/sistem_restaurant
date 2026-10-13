# Dokumentasi Kode Program — RestoReserve

Dokumen ini memuat dokumentasi teknis struktur class, method, input-output, serta contoh nyata penerapan konsep pemrograman terstruktur, berorientasi objek (OOP), dan transaksi database pada aplikasi **RestoReserve**.

---

## 1. Daftar Class dan Tanggung Jawab

| Class | Namespace | Tanggung Jawab Utama |
|---|---|---|
| `Database` | `App\Core` | Singleton instance koneksi PDO MySQL dengan error exception handling. |
| `Router` | `App\Core` | Routing HTTP request method GET/POST dengan regex named group `{id}`. |
| `Auth` | `App\Core` | Autentikasi admin, verifikasi password hash, regenerasi session ID, proteksi route. |
| `Csrf` | `App\Core` | Proteksi Cross-Site Request Forgery berbasis random_bytes & hash_equals. |
| `View` | `App\Core` | Template view renderer dengan buffering dan layout nesting. |
| `BaseController` | `App\Core` | Induk Controller penyedia helper otentikasi, validasi CSRF, dan view rendering. |
| `BookingRules` | `App\Services` | Logika murni: perhitungan overlap waktu reservasi 2 jam dan validasi kapasitas meja. |
| `OrderCalculator` | `App\Services` | Logika murni: kalkulasi subtotal dan total dalam integer Rupiah, konsolidasi item. |
| `ReservationService` | `App\Services` | Validasi bisnis waktu reservasi dan penjagaan transisi status reservasi. |
| `OrderService` | `App\Services` | Validasi pembuatan order untuk reservasi confirmed dan siklus hidup pesanan. |
| `ReportService` | `App\Services` | Agregasi data laporan operasional dan ekspor CSV aman anti formula injection. |
| `CustomerRepository` | `App\Repositories` | Query database tabel `customers`. |
| `TableRepository` | `App\Repositories` | Query database tabel `restaurant_tables`. |
| `MenuRepository` | `App\Repositories` | Query database tabel `menu_items`. |
| `ReservationRepository` | `App\Repositories` | Transaksi database dengan row-locking `SELECT ... FOR UPDATE` pada meja. |
| `OrderRepository` | `App\Repositories` | Transaksi database pembuatan pesanan dengan snapshot harga menu ke `order_items`. |

---

## 2. Contoh Implementasi Konsep Pemrograman

### 2.1 Pemrograman Terstruktur: Percabangan `if-else` & Fungsi Utilitas
Diambil dari [`app/Services/BookingRules.php`](file:///c:/laragon/www/sistem_restoran/app/Services/BookingRules.php):
```php
public static function isOverlapping(string $startA, string $endA, string $startB, string $endB): bool
{
    $dtStartA = new DateTimeImmutable($startA);
    $dtEndA   = new DateTimeImmutable($endA);
    $dtStartB = new DateTimeImmutable($startB);
    $dtEndB   = new DateTimeImmutable($endB);

    // Percabangan logika overlap: startA < endB DAN endA > startB
    return ($dtStartA < $dtEndB) && ($dtEndA > $dtStartB);
}
```

### 2.2 Pemrograman Terstruktur: Perulangan `foreach` & Kalkulasi Aritmatika
Diambil dari [`app/Services/OrderCalculator.php`](file:///c:/laragon/www/sistem_restoran/app/Services/OrderCalculator.php):
```php
public static function calculateTotal(array $items): int
{
    if (empty($items)) {
        throw new InvalidArgumentException("Daftar item pesanan tidak boleh kosong.");
    }

    $total = 0;
    foreach ($items as $item) {
        $qty   = (int) ($item['quantity'] ?? 0);
        $price = (int) ($item['unit_price'] ?? 0);

        $subtotal = self::calculateSubtotal($qty, $price);
        $total += $subtotal;
    }

    return $total;
}
```

### 2.3 Pemrograman Berorientasi Objek (OOP): Pewarisan (Inheritance) & Constructor
Diambil dari [`app/Controllers/CustomerController.php`](file:///c:/laragon/www/sistem_restoran/app/Controllers/CustomerController.php):
```php
class CustomerController extends BaseController
{
    private CustomerRepository $customerRepo;

    public function __construct()
    {
        $this->customerRepo = new CustomerRepository();
    }

    public function index(): void
    {
        $this->requireAuth(); // Method dari BaseController
        $search = trim($_GET['search'] ?? '');
        $customers = $this->customerRepo->getAll($search);

        $this->view('customers/index', [ // Method dari BaseController
            'title'     => 'Daftar Pelanggan',
            'customers' => $customers,
            'search'    => $search,
        ]);
    }
}
```

### 2.4 Akses Basis Data: Transaksi ACID & Row-Level Locking (`FOR UPDATE`)
Diambil dari [`app/Repositories/ReservationRepository.php`](file:///c:/laragon/www/sistem_restoran/app/Repositories/ReservationRepository.php):
```php
$this->db->beginTransaction();

try {
    // Kunci baris meja spesifik untuk mencegah race condition
    $lockStmt = $this->db->prepare('SELECT id, capacity, is_active FROM restaurant_tables WHERE id = :table_id FOR UPDATE');
    $lockStmt->execute([':table_id' => $tableId]);
    $table = $lockStmt->fetch();

    // Periksa konflik dengan reservasi lain yang aktif
    $conflictStmt = $this->db->prepare(
        "SELECT id FROM reservations 
         WHERE table_id = :table_id 
           AND status IN ('pending', 'confirmed') 
           AND reserved_at < :new_end 
           AND DATE_ADD(reserved_at, INTERVAL 2 HOUR) > :new_start 
         LIMIT 1"
    );
    $conflictStmt->execute([
        ':table_id'  => $tableId,
        ':new_end'   => $newEnd,
        ':new_start' => $reservedAt,
    ]);

    if ($conflictStmt->fetch()) {
        $this->db->rollBack();
        return ['success' => false, 'reservation_id' => null, 'error' => 'Meja sudah dipesan pada rentang jam tersebut.'];
    }

    // Tulis data reservasi
    $insertStmt = $this->db->prepare("INSERT INTO reservations (...) VALUES (...)");
    $insertStmt->execute([...]);

    $newId = (int) $this->db->lastInsertId();
    $this->db->commit();
    return ['success' => true, 'reservation_id' => $newId, 'error' => null];
} catch (\Throwable $e) {
    if ($this->db->inTransaction()) {
        $this->db->rollBack();
    }
    throw $e;
}
```
