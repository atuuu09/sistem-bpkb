-- ================================================
-- DATABASE: db_sistem_bpkb
-- Sistem Monitoring BPKB - MAF / MCF
-- ================================================

-- 1. Buat databasenya (jika belum ada)
CREATE DATABASE IF NOT EXISTS db_sistem_bpkb
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Gunakan database tersebut
USE db_sistem_bpkb;

-- 3. Bersihkan tabel lama (Hapus tabel 'bpkb' dulu, baru 'users' agar tidak kena error Foreign Key)
DROP TABLE IF EXISTS bpkb;
DROP TABLE IF EXISTS users;

-- -------------------------
-- Tabel Users
-- -------------------------
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('MAF','MCF','kacab') NOT NULL DEFAULT 'MCF',
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------
-- Tabel BPKB
-- -------------------------
CREATE TABLE IF NOT EXISTS bpkb (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    no_bpkb         VARCHAR(50)  NOT NULL UNIQUE   COMMENT 'Nomor seri BPKB',
    no_polisi       VARCHAR(20)  NOT NULL,
    no_rangka       VARCHAR(50)  NOT NULL,
    no_mesin        VARCHAR(50)  NOT NULL,
    nama_nasabah    VARCHAR(100) NOT NULL,
    no_kontrak      VARCHAR(50)  NOT NULL UNIQUE,
    no_hp           VARCHAR(20),
    alamat          TEXT,
    jenis_kendaraan ENUM('Motor','Mobil') NOT NULL DEFAULT 'Motor',
    merk_kendaraan  VARCHAR(50),
    tahun_kendaraan YEAR,
    warna           VARCHAR(30),
    perusahaan      ENUM('MAF','MCF') NOT NULL     COMMENT 'MAF = Mega Auto Finance, MCF = Mega Central Finance',
    tanggal_masuk   DATE         NOT NULL,
    keterangan      TEXT,
    status          ENUM('aktif','lunas','diambil') NOT NULL DEFAULT 'aktif',
    tanggal_lunas   DATE         NULL,
    tanggal_keluar  DATE         NULL,
    user_input      INT          NOT NULL           COMMENT 'FK ke users.id',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_input) REFERENCES users(id) ON DELETE RESTRICT
);

-- -------------------------
-- Data Awal Users
-- -------------------------
INSERT INTO users (nama, username, password, role) VALUES
('Admin MAF',        'admin_maf',  MD5('maf123'),   'MAF'),
('Admin MCF',        'admin_mcf',  MD5('mcf123'),   'MCF'),
('Ketua Cabang',     'kacab',      MD5('kacab123'), 'kacab');

-- -------------------------
-- Data Dummy BPKB (No BPKB & No Kontrak Format Indonesia)
-- -------------------------
-- MAF: no_kontrak 10 digit diawali 1000
-- MCF: no_kontrak 10 digit diawali 5000
-- No BPKB: format seperti BPKB Indonesia (huruf + angka)

INSERT INTO bpkb (no_bpkb, no_polisi, no_rangka, no_mesin, nama_nasabah, no_kontrak, no_hp, alamat, jenis_kendaraan, merk_kendaraan, tahun_kendaraan, warna, perusahaan, tanggal_masuk, status, user_input) VALUES
('H-12345678',  'D 1234 ABC', 'MH1JF1110NK100001', 'JF11E1000001', 'Budi Santoso',      '1000000001', '081234567890', 'Jl. Merdeka No. 1, Bandung',           'Motor', 'Honda Beat',      2022, 'Putih',   'MAF', '2024-01-10', 'aktif',  1),
('H-23456789',  'D 5678 BCD', 'MH4SC1200NK100002', 'SC12E0000002', 'Siti Rahayu',       '1000000002', '082345678901', 'Jl. Sudirman No. 5, Cimahi',           'Motor', 'Yamaha NMAX',     2021, 'Hitam',   'MAF', '2024-02-15', 'aktif',  1),
('H-34567890',  'B 9012 CDE', 'MH1PC1160NK100003', 'PC16E0000003', 'Ahmad Fauzi',       '1000000003', '083456789012', 'Jl. Gatot Subroto No. 10, Bandung',    'Motor', 'Honda Vario 125', 2023, 'Biru',    'MAF', '2024-03-01', 'aktif',  1),
('H-45678901',  'F 3456 DEF', 'MH4KE8200NK100004', 'KE8E0000004',  'Eko Prasetyo',      '1000000004', '084567890123', 'Jl. Braga No. 8, Bandung',             'Motor', 'Honda PCX 150',   2022, 'Putih',   'MAF', '2024-04-05', 'lunas',  1),
('H-56789012',  'D 7890 EFG', 'MH4PC1100NK100005', 'PC11E0000005', 'Dewi Lestari',      '1000000005', '085678901234', 'Jl. Asia Afrika No. 20, Bandung',      'Motor', 'Honda Scoopy',    2023, 'Merah',   'MAF', '2024-05-10', 'lunas',  1),
('H-12398765',  'D 2468 FGH', 'MHFB86900NK100006', 'K4FB0000006',  'Fitri Handayani',   '5000000001', '086789012345', 'Jl. Cihampelas No. 15, Bandung',       'Mobil', 'Toyota Avanza',   2021, 'Silver',  'MCF', '2024-01-20', 'aktif',  2),
('H-22398765',  'D 1357 GHI', 'MHFXX00G0N0000007', 'G15U0000007',  'Rudi Hartono',      '5000000002', '087890123456', 'Jl. Dago No. 30, Bandung',             'Mobil', 'Daihatsu Xenia',  2020, 'Abu-abu', 'MCF', '2024-02-28', 'aktif',  2),
('H-32398765',  'B 2468 HIJ', 'MHFGB30H0K0000008', 'K3GE0000008',  'Sari Wulandari',    '5000000003', '088901234567', 'Jl. Setiabudhi No. 45, Bandung',       'Mobil', 'Toyota Rush',     2022, 'Hitam',   'MCF', '2024-03-15', 'aktif',  2),
('H-42398765',  'F 3579 IJK', 'MHFGB38H0K0000009', 'K3VE0000009',  'Bambang Suprapto',  '5000000004', '089012345678', 'Jl. Pasteur No. 10, Bandung',          'Mobil', 'Suzuki Ertiga',   2021, 'Putih',   'MCF', '2024-04-12', 'lunas',  2),
('H-52398765',  'D 4680 JKL', 'MHFB86900NK000010', 'K4FB0000010',  'Hendra Wijaya',     '5000000005', '081122334455', 'Jl. Kopo No. 88, Bandung',             'Mobil', 'Honda Mobilio',   2020, 'Merah',   'MCF', '2024-05-20', 'lunas',  2);

-- -------------------------
-- Update tanggal lunas
-- -------------------------
UPDATE bpkb SET tanggal_lunas = '2024-11-01' WHERE no_kontrak = '1000000004';
UPDATE bpkb SET tanggal_lunas = '2024-10-15' WHERE no_kontrak = '1000000005';
UPDATE bpkb SET tanggal_lunas = '2024-12-01' WHERE no_kontrak = '5000000004';
UPDATE bpkb SET tanggal_lunas = '2024-11-20' WHERE no_kontrak = '5000000005';
