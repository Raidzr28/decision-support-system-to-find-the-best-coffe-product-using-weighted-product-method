-- =====================================================================
-- DATABASE: SPK Weighted Product - Rekomendasi Menu Kopi Favorit
-- Cafe Marako Space
-- =====================================================================
-- Cara import: buka phpMyAdmin > Import > pilih file ini
-- atau via terminal: mysql -u root -p < database.sql
-- =====================================================================

CREATE DATABASE IF NOT EXISTS spk_kopi_wp
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spk_kopi_wp;

-- ---------------------------------------------------------------------
-- Tabel User (autentikasi)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  role ENUM('admin','user') DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- password default: admin123 (hash bcrypt)
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2b$10$nPtOlmc17BODmU3A7FYCCuIY.d/zvvUrIr92/l5KGV4L.f5TpdMX6', 'Administrator', 'admin');

-- ---------------------------------------------------------------------
-- Tabel Kriteria (8 kriteria sesuai BAB III)
-- atribut: benefit / cost ; bobot dapat diubah di halaman input kriteria
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS kriteria;
CREATE TABLE kriteria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(5) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  bobot DECIMAL(6,3) NOT NULL,
  atribut ENUM('benefit','cost') NOT NULL,
  urutan INT NOT NULL
) ENGINE=InnoDB;

INSERT INTO kriteria (kode, nama, bobot, atribut, urutan) VALUES
('C1', 'Cita Rasa',                 5, 'benefit', 1),
('C2', 'Aroma',                     4, 'benefit', 2),
('C3', 'Harga',                     5, 'cost',    3),
('C4', 'Popularitas',               4, 'benefit', 4),
('C5', 'Ketersediaan Bahan Baku',   3, 'benefit', 5),
('C6', 'Waktu Penyajian',           3, 'cost',    6),
('C7', 'Nilai Gizi',                3, 'benefit', 7),
('C8', 'Tingkat Kepuasan Pelanggan',4, 'benefit', 8);

-- ---------------------------------------------------------------------
-- Tabel Alternatif (menu kopi + nama pembeli + waktu pembelian)
-- nilai c1..c8 = skala 1-5 sesuai tabel skala BAB III
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS alternatif;
CREATE TABLE alternatif (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(10) NOT NULL,
  nama_menu VARCHAR(120) NOT NULL,
  nama_pembeli VARCHAR(120) NOT NULL,
  waktu_pembelian DATETIME NOT NULL,
  c1 TINYINT NOT NULL,
  c2 TINYINT NOT NULL,
  c3 TINYINT NOT NULL,
  c4 TINYINT NOT NULL,
  c5 TINYINT NOT NULL,
  c6 TINYINT NOT NULL,
  c7 TINYINT NOT NULL,
  c8 TINYINT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabel Riwayat Perhitungan
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS riwayat;
CREATE TABLE riwayat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_proses VARCHAR(30) NOT NULL,
  total_alternatif INT NOT NULL,
  menu_terbaik VARCHAR(120) NOT NULL,
  nilai_tertinggi DECIMAL(12,8) NOT NULL,
  detail_json LONGTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 100 data sample alternatif
INSERT INTO alternatif (kode, nama_menu, nama_pembeli, waktu_pembelian, c1,c2,c3,c4,c5,c6,c7,c8) VALUES
('A001','Matcha Espresso Fusion','Dewi Pratama','2026-05-08 11:15:00',2,2,5,1,2,2,2,3),
('A002','Kopi Tubruk','Tono Pratama','2026-04-23 18:45:00',3,5,4,1,3,5,3,4),
('A003','Mocha','Gita Kusuma','2026-03-03 14:00:00',4,4,4,1,5,2,4,2),
('A004','Vietnam Drip','Joko Gunawan','2026-05-19 11:00:00',2,3,4,1,3,2,4,4),
('A005','Piccolo Latte','Umi Hartono','2026-04-12 13:15:00',4,2,3,5,3,3,4,5),
('A006','Caramel Latte','Doni Yulianti','2026-04-22 13:00:00',3,2,4,4,4,2,2,4),
('A007','Flat White','Umi Wibowo','2026-06-21 15:15:00',4,3,3,5,4,5,5,5),
('A008','Affogato','Hadi Nugroho','2026-06-03 20:00:00',2,3,3,4,2,5,4,5),
('A009','Kopi Tubruk','Indah Yulianti','2026-03-22 19:00:00',4,4,2,3,5,3,4,2),
('A010','Spanish Latte','Sinta Maulana','2026-04-17 09:30:00',3,3,4,2,2,4,4,2),
('A011','Cafe Latte','Doni Hartono','2026-05-08 08:15:00',2,2,5,1,3,3,4,3),
('A012','Caramel Latte','Qori Gunawan','2026-06-07 16:15:00',4,5,4,4,5,2,2,3),
('A013','Cappuccino','Kiki Pratama','2026-04-19 11:00:00',2,2,3,1,2,4,1,3),
('A014','Caramel Latte','Vina Wibowo','2026-04-18 10:45:00',3,5,5,2,2,2,4,4),
('A015','Es Kopi Susu','Nanda Sari','2026-03-22 18:00:00',2,5,4,1,3,3,2,5),
('A016','Mocha','Nanda Hidayat','2026-05-15 11:00:00',5,2,2,5,2,2,2,3),
('A017','Es Kopi Susu','Putri Wibowo','2026-04-28 14:00:00',3,5,2,4,4,5,3,5),
('A018','Kopi Pandan','Yuni Yulianti','2026-06-05 11:30:00',3,2,2,3,2,2,5,5),
('A019','Kopi Tubruk','Doni Halim','2026-04-02 16:00:00',3,2,2,2,5,2,5,3),
('A020','Irish Coffee','Tono Santoso','2026-03-14 18:30:00',4,3,4,2,4,5,2,4),
('A021','Piccolo Latte','Kiki Wijaya','2026-03-15 17:00:00',2,3,4,2,4,2,2,4),
('A022','Vanilla Latte','Fajar Sari','2026-05-20 20:00:00',4,2,3,3,2,2,5,3),
('A023','Caramel Latte','Joko Gunawan','2026-04-23 13:15:00',4,5,4,1,2,5,3,2),
('A024','Espresso','Kiki Nugroho','2026-05-06 19:45:00',5,2,2,1,3,2,3,3),
('A025','Es Kopi Susu','Eka Santoso','2026-05-12 20:00:00',4,3,3,1,4,5,5,3),
('A026','Kopi Susu Gula Aren','Reza Hidayat','2026-04-14 08:15:00',4,5,3,3,3,2,4,2),
('A027','Cortado','Hadi Anggraini','2026-06-12 12:15:00',3,2,3,4,4,4,1,4),
('A028','Affogato','Umi Halim','2026-06-22 21:30:00',2,2,4,2,4,2,1,5),
('A029','Affogato','Yuni Kusuma','2026-06-20 16:00:00',5,3,4,1,5,2,5,3),
('A030','Affogato','Nanda Wijaya','2026-05-20 13:00:00',4,4,5,3,5,4,5,3),
('A031','Flat White','Nanda Ramadhan','2026-04-20 17:30:00',5,2,4,3,3,5,5,4),
('A032','Piccolo Latte','Oki Sari','2026-04-17 15:15:00',2,4,4,1,3,4,2,3),
('A033','Mocha','Ahmad Santoso','2026-04-16 17:00:00',5,5,3,4,5,5,2,3),
('A034','Matcha Espresso Fusion','Wawan Pratama','2026-03-25 14:15:00',3,5,2,5,3,2,4,3),
('A035','Piccolo Latte','Vina Halim','2026-05-25 15:45:00',5,3,5,4,4,3,3,5),
('A036','Matcha Espresso Fusion','Hadi Maulana','2026-06-03 19:30:00',3,4,4,3,2,3,2,3),
('A037','Cold Brew','Wawan Nugroho','2026-04-03 14:45:00',4,5,5,1,3,5,4,2),
('A038','Dirty Coffee','Sari Ramadhan','2026-06-01 13:30:00',5,5,3,4,3,4,4,5),
('A039','Espresso','Maya Kusuma','2026-06-24 10:45:00',3,2,5,5,2,2,4,3),
('A040','Piccolo Latte','Fajar Santoso','2026-05-13 13:15:00',5,4,4,4,4,5,3,2),
('A041','Cortado','Ahmad Yulianti','2026-03-12 11:00:00',2,2,3,2,2,3,2,3),
('A042','Cortado','Vina Lestari','2026-04-15 19:30:00',4,3,2,2,4,2,5,2),
('A043','Vanilla Latte','Sari Ramadhan','2026-06-23 11:00:00',3,2,4,5,2,2,3,5),
('A044','Brown Sugar Coffee','Lina Wijaya','2026-05-01 21:45:00',5,2,5,3,5,3,4,3),
('A045','Spanish Latte','Qori Maulana','2026-06-15 14:30:00',4,3,2,3,5,3,4,5),
('A046','Hazelnut Latte','Ahmad Wibowo','2026-05-06 15:15:00',4,4,4,3,4,2,5,3),
('A047','Cappuccino','Hadi Putra','2026-06-18 20:15:00',5,5,5,1,2,4,2,5),
('A048','Kopi Pandan','Hadi Permata','2026-05-16 16:30:00',5,4,4,4,4,4,3,3),
('A049','Cafe Latte','Yuni Anggraini','2026-05-04 19:15:00',3,3,5,3,4,2,2,4),
('A050','Kopi Susu Gula Aren','Lina Hidayat','2026-05-01 19:15:00',4,2,2,5,4,3,4,2),
('A051','Espresso','Sari Permata','2026-06-16 15:30:00',3,2,4,4,2,2,4,5),
('A052','Cappuccino','Sari Santoso','2026-04-05 20:30:00',2,3,2,5,5,3,5,5),
('A053','Piccolo Latte','Doni Sari','2026-05-28 17:45:00',4,2,2,2,3,4,1,3),
('A054','Kopi Susu Gula Aren','Fajar Yulianti','2026-03-06 08:45:00',5,5,4,1,3,4,3,5),
('A055','Cappuccino','Vina Saputra','2026-05-26 20:15:00',5,2,3,2,4,3,1,2),
('A056','Macchiato','Bagas Permata','2026-05-15 09:45:00',4,5,4,5,5,5,1,2),
('A057','Es Kopi Susu','Yuni Kusuma','2026-05-01 09:15:00',2,4,2,2,5,5,3,3),
('A058','Irish Coffee','Nanda Wibowo','2026-03-16 13:45:00',4,4,2,2,4,5,4,4),
('A059','Brown Sugar Coffee','Maya Yulianti','2026-03-15 09:30:00',4,4,2,4,2,5,4,2),
('A060','Flat White','Qori Hartono','2026-06-21 15:00:00',3,4,3,3,5,5,1,2),
('A061','Matcha Espresso Fusion','Tono Saputra','2026-04-10 16:00:00',5,2,3,1,5,2,2,5),
('A062','Kopi Pandan','Joko Halim','2026-05-14 21:45:00',5,3,5,5,3,5,2,3),
('A063','Cappuccino','Indah Putra','2026-05-26 16:30:00',2,4,4,5,5,3,4,5),
('A064','Affogato','Kiki Yulianti','2026-06-15 13:15:00',3,5,3,4,2,4,4,5),
('A065','Cold Brew','Vina Nugroho','2026-06-02 10:30:00',2,5,2,5,5,2,2,5),
('A066','Matcha Espresso Fusion','Eka Wijaya','2026-06-26 12:30:00',5,2,4,5,5,4,4,2),
('A067','Coconut Latte','Citra Saputra','2026-05-08 19:00:00',5,2,2,4,3,4,1,2),
('A068','Hazelnut Latte','Bagas Santoso','2026-05-12 13:45:00',3,3,5,5,3,3,2,2),
('A069','Coconut Latte','Reza Ramadhan','2026-04-16 17:15:00',3,5,4,4,4,2,4,4),
('A070','Brown Sugar Coffee','Rian Hidayat','2026-03-15 13:30:00',5,4,5,3,3,5,4,2),
('A071','Kopi Susu Gula Aren','Maya Firmansyah','2026-05-19 12:30:00',2,5,4,1,2,5,3,3),
('A072','Coconut Latte','Bagas Hartono','2026-04-21 11:30:00',3,2,2,3,5,2,5,4),
('A073','Spanish Latte','Eka Wijaya','2026-05-11 19:45:00',3,3,3,5,4,4,2,4),
('A074','Cortado','Bagas Permata','2026-05-26 09:45:00',2,3,3,4,4,2,4,2),
('A075','Caramel Latte','Rian Lestari','2026-06-12 18:30:00',5,4,2,2,5,2,5,4),
('A076','Coconut Latte','Hadi Wijaya','2026-06-23 12:45:00',2,3,2,1,4,5,1,2),
('A077','Kopi Susu Gula Aren','Sinta Yulianti','2026-04-13 15:30:00',5,3,5,1,5,5,3,2),
('A078','Kopi Pandan','Lina Anggraini','2026-06-15 11:30:00',2,4,4,1,5,4,2,2),
('A079','Piccolo Latte','Citra Anggraini','2026-03-02 20:30:00',3,3,3,1,3,3,2,4),
('A080','Dirty Coffee','Eka Gunawan','2026-03-09 21:15:00',3,4,3,1,2,3,1,4),
('A081','Kopi Susu Gula Aren','Sari Kusuma','2026-03-06 12:00:00',3,5,2,1,5,5,3,2),
('A082','Piccolo Latte','Qori Saputra','2026-03-24 20:30:00',5,2,2,4,5,5,1,5),
('A083','Kopi Pandan','Doni Sari','2026-03-03 13:15:00',2,3,4,5,4,5,5,4),
('A084','Piccolo Latte','Qori Gunawan','2026-06-04 20:00:00',3,5,5,2,5,4,4,5),
('A085','Es Kopi Susu','Yuni Lestari','2026-05-14 13:30:00',4,3,5,1,2,2,1,5),
('A086','Cafe Latte','Yuni Hartono','2026-04-18 08:30:00',2,5,4,4,2,4,5,4),
('A087','Affogato','Dewi Firmansyah','2026-04-05 18:45:00',3,2,4,5,4,2,3,3),
('A088','Es Kopi Susu','Reza Yulianti','2026-03-20 18:30:00',2,3,4,3,4,4,1,3),
('A089','Mocha','Sari Ramadhan','2026-03-05 19:00:00',2,3,5,4,5,4,2,4),
('A090','Vanilla Latte','Yuni Kusuma','2026-03-02 10:15:00',2,2,4,4,5,5,5,5),
('A091','Es Kopi Susu','Indah Anggraini','2026-03-12 14:00:00',4,5,4,1,3,5,5,2),
('A092','Espresso','Gita Permata','2026-04-25 10:30:00',4,4,2,1,5,5,2,3),
('A093','Cold Brew','Rian Saputra','2026-05-03 14:00:00',5,2,5,1,4,5,5,5),
('A094','Kopi Pandan','Umi Putra','2026-05-04 14:00:00',4,3,5,3,2,5,1,3),
('A095','Es Kopi Susu','Sari Ramadhan','2026-03-13 21:30:00',4,3,4,2,2,2,5,3),
('A096','Dirty Coffee','Lina Hartono','2026-04-08 09:15:00',4,3,3,5,3,2,2,5),
('A097','Piccolo Latte','Zaki Firmansyah','2026-06-22 17:30:00',4,3,5,1,5,5,3,4),
('A098','Irish Coffee','Budi Hartono','2026-03-10 15:45:00',2,2,4,3,2,2,5,5),
('A099','Piccolo Latte','Sari Yulianti','2026-03-15 20:15:00',4,5,3,1,5,2,3,2),
('A100','Kopi Tubruk','Umi Hidayat','2026-03-08 19:45:00',5,3,4,3,4,5,4,4);
