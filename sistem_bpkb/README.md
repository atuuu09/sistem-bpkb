# 🗂️ Sistem Monitoring BPKB — MAF & MCF

Sistem berbasis web untuk monitoring penerimaan dan pengeluaran BPKB kendaraan bermotor
pada perusahaan **Mandiri Artha Finance (MAF)** dan **Mega Central Finance (MCF)**.

---

## ⚙️ Teknologi

| Layer      | Tools                        |
|------------|------------------------------|
| Backend    | PHP 8.x (Native)             |
| Database   | MySQL via XAMPP              |
| Frontend   | Bootstrap 5.3 + DataTables   |
| Font       | Plus Jakarta Sans, JetBrains Mono |
| Icon       | Font Awesome 6               |
| Editor     | VS Code                      |

---

## 🚀 Cara Setup

### 1. Install XAMPP
Download di https://www.apachefriends.org — jalankan Apache + MySQL.

### 2. Copy Project
```
Salin folder `sistem_bpkb` ke:
C:\xampp\htdocs\sistem_bpkb\
```

### 3. Import Database
- Buka browser → `http://localhost/phpmyadmin`
- Klik **New** → buat database `db_sistem_bpkb`
- Klik tab **Import** → pilih file `config/database.sql`
- Klik **Go**

### 4. Jalankan Aplikasi
Buka browser: **http://localhost/sistem_bpkb**

---

## 🔐 Akun Login Default

| Role  | Username | Password   |
|-------|----------|------------|
| Admin | `admin`  | `admin123` |
| Staf MAF | `maf1` | `maf123` |
| Staf MCF | `mcf1` | `mcf123` |

---

## 📁 Struktur Folder

```
sistem_bpkb/
├── .vscode/
│   ├── settings.json       ← Konfigurasi VS Code
│   └── extensions.json     ← Rekomendasi ekstensi
│
├── config/
│   ├── config.php          ← Konfigurasi app, session, helper functions
│   ├── database.php        ← Koneksi MySQL
│   └── database.sql        ← Script buat tabel + data dummy
│
├── includes/
│   ├── header.php          ← Template <head> HTML
│   ├── footer.php          ← Script JS + penutup body
│   └── sidebar.php         ← Sidebar navigasi (auto-detect halaman aktif)
│
├── pages/
│   ├── home.php            ← 🏠 Beranda & ringkasan statistik
│   ├── in_bpkb.php         ← 📥 Form input BPKB baru
│   ├── all_bpkb.php        ← 🗃️ Tabel semua BPKB + filter + searching
│   ├── out_bpkb.php        ← 📤 Nasabah lunas + fitur cetak
│   └── detail_bpkb.php     ← 🔍 Halaman detail per BPKB
│
├── assets/
│   ├── css/style.css       ← Custom CSS (dark theme)
│   ├── js/app.js           ← Custom JavaScript
│   └── img/                ← Folder logo / gambar
│
├── uploads/
│   └── bpkb/               ← Folder upload dokumen (opsional)
│
├── index.php               ← Entry point (redirect otomatis)
├── login.php               ← Halaman login
├── logout.php              ← Proses logout
└── README.md               ← Panduan ini
```

---

## 📋 Fitur Per Halaman

### 🏠 Home
- Kartu statistik: total, aktif, lunas, masuk bulan ini
- Distribusi MAF vs MCF (dengan bar progress)
- Statistik motor vs mobil
- Tabel 5 data BPKB terbaru

### 📥 IN BPKB
- Form input BPKB baru (2 seksi: data kendaraan & nasabah)
- Validasi field wajib
- Auto-deteksi perusahaan berdasarkan role login

### 🗃️ ALL BPKB
- Tabel lengkap semua BPKB dengan DataTables (searching, sorting, pagination)
- Filter perusahaan (MAF/MCF) dan status
- Tombol: Lihat Detail, Tandai Lunas, Hapus
- Konfirmasi sebelum hapus/ubah status

### 📤 OUT BPKB
- Tab 1: BPKB Lunas — belum diambil (tombol "Tandai Diambil")
- Tab 2: Riwayat BPKB yang sudah diambil
- Fitur **Cetak** (print-friendly, sidebar & tombol tersembunyi)

---

## 🎨 Ekstensi VS Code yang Direkomendasikan

| Ekstensi | Fungsi |
|----------|--------|
| PHP Intelephense | Autocomplete PHP |
| Prettier | Format kode |
| Live Server | Preview browser otomatis |
| Auto Close/Rename Tag | Produktivitas HTML |
| Material Icon Theme | Ikon folder yang keren |
| One Dark Pro | Tema gelap |
| GitLens | Manajemen Git |
