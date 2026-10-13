-- ============================================================
-- RestoReserve — Seed Data Awal (Fiktif untuk Demonstrasi)
-- Catatan: Kredensial admin dibuat terpisah via scripts/create_admin.php
-- ============================================================

-- ------------------------------------------------------------
-- Data Pelanggan Fiktif
-- ------------------------------------------------------------
INSERT INTO customers (name, phone, email, created_at, updated_at) VALUES
('Budi Santoso', '081234567890', 'budi.santoso@example.test', NOW(), NOW()),
('Siti Rahayu', '082198765432', 'siti.rahayu@example.test', NOW(), NOW()),
('Rina Wulandari', '085712345678', 'rina.wulan@example.test', NOW(), NOW()),
('Andi Pratama', '087811223344', 'andi.pratama@example.test', NOW(), NOW()),
('Dewi Lestari', '089944332211', 'dewi.lestari@example.test', NOW(), NOW());

-- ------------------------------------------------------------
-- Data Meja Restoran (Nomor Unik & Kapasitas Tamu)
-- ------------------------------------------------------------
INSERT INTO restaurant_tables (table_number, capacity, is_active) VALUES
('M01', 2, 1),
('M02', 2, 1),
('M03', 4, 1),
('M04', 4, 1),
('M05', 6, 1);

-- ------------------------------------------------------------
-- Data Menu Makanan dan Minuman
-- ------------------------------------------------------------
INSERT INTO menu_items (name, category, price, is_available) VALUES
('Nasi Goreng Spesial', 'Makanan', 25000, 1),
('Mie Goreng Jawa', 'Makanan', 22000, 1),
('Ayam Bakar Madu', 'Makanan', 30000, 1),
('Es Teh Manis', 'Minuman', 5000, 1),
('Jus Jeruk Segar', 'Minuman', 12000, 1),
('Air Mineral 600ml', 'Minuman', 6000, 1);
