<?php
require_once '../config/config.php';
require_once '../config/database.php';
cekLogin();

$pageTitle = 'ALL BPKB — ' . APP_NAME;
$alert = '';

// ── Proses aksi (ubah status, hapus) ─────────────────────────────────────────
if (isset($_GET['aksi'], $_GET['id'])) {
    $id   = (int)$_GET['id'];
    $aksi = $_GET['aksi'];

    if ($aksi === 'lunas') {
        $conn->query("UPDATE bpkb SET status='lunas', tanggal_lunas=CURDATE() WHERE id=$id");
        $alert = '<div class="alert alert-success" data-autohide><i class="fa-solid fa-circle-check me-2"></i>Status BPKB berhasil diubah ke <strong>Lunas</strong>.</div>';
    } elseif ($aksi === 'aktif') {
        $conn->query("UPDATE bpkb SET status='aktif', tanggal_lunas=NULL WHERE id=$id");
        $alert = '<div class="alert alert-warning" data-autohide><i class="fa-solid fa-rotate-left me-2"></i>Status BPKB dikembalikan ke <strong>Aktif</strong>.</div>';
    } elseif ($aksi === 'hapus') {
        $conn->query("DELETE FROM bpkb WHERE id=$id");
        $alert = '<div class="alert alert-danger" data-autohide><i class="fa-solid fa-trash me-2"></i>Data BPKB berhasil dihapus.</div>';
    }
}

// ── Filter perusahaan (opsional) ──────────────────────────────────────────────
$filter = '';
$roleFilter = userRole();

// MAF hanya bisa lihat data MAF, MCF hanya bisa lihat data MCF
if ($roleFilter === 'MAF') {
    $fPerusahaan = 'MAF';
    $filter .= " AND b.perusahaan = 'MAF'";
} elseif ($roleFilter === 'MCF') {
    $fPerusahaan = 'MCF';
    $filter .= " AND b.perusahaan = 'MCF'";
} else {
    // Kacab bisa filter manual
    $fPerusahaan = $_GET['perusahaan'] ?? '';
    if ($fPerusahaan) $filter .= " AND b.perusahaan = '" . $conn->real_escape_string($fPerusahaan) . "'";
}

$fStatus = $_GET['status'] ?? '';
if ($fStatus) $filter .= " AND b.status = '" . $conn->real_escape_string($fStatus) . "'";

$result = $conn->query("SELECT b.*, u.nama AS input_oleh
    FROM bpkb b JOIN users u ON b.user_input = u.id
    WHERE 1=1 $filter
    ORDER BY b.created_at DESC");

include '../includes/header.php';
// DataTables CSS
echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">';
// jQuery harus sebelum DT
echo '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>';
?>

<!-- KUSTOMISASI TEMA TERANG MACF (Kuning & Oranye) -->
<style>
    :root {
        --macf-yellow: #FFCA28;
        --macf-orange: #F57C00;
        --macf-bg-light: #F8F9FA;
        --macf-surface: #FFFFFF;
        --macf-text: #495057;
    }
    
    /* Latar Belakang & Teks Umum */
    body, .main-wrapper {
        background-color: var(--macf-bg-light) !important;
        color: var(--macf-text) !important;
    }

    /* Topbar Gradasi Kuning ke Oranye */
    .topbar {
        background: linear-gradient(135deg, var(--macf-yellow) 0%, var(--macf-orange) 100%) !important;
        color: #fff !important;
        border-bottom: none !important;
        box-shadow: 0 4px 12px rgba(245, 124, 0, 0.15);
    }
    .topbar .topbar-title {
        color: #fff !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    .topbar .topbar-title small {
        color: rgba(255,255,255,0.9) !important;
    }

    /* Tombol Primary Oranye */
    .btn-primary {
        background-color: var(--macf-orange) !important;
        border-color: var(--macf-orange) !important;
        color: #fff !important;
        box-shadow: 0 2px 4px rgba(245, 124, 0, 0.3);
    }
    .btn-primary:hover {
        background-color: #E65100 !important;
        border-color: #E65100 !important;
        transform: translateY(-1px);
    }

    /* Card Box yang Bersih & Terang */
    .card {
        background-color: var(--macf-surface) !important;
        border: 1px solid #E0E0E0 !important;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.03) !important;
    }
    .card-header {
        background-color: var(--macf-surface) !important;
        border-bottom: 2px solid var(--macf-yellow) !important;
        color: var(--macf-orange) !important;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Tabel yang Lebih Soft */
    .table {
        color: var(--macf-text) !important;
    }
    .table thead th {
        background-color: #FFF8E1 !important; /* Kuning sangat pudar */
        color: var(--macf-orange) !important;
        border-bottom: 2px solid var(--macf-yellow) !important;
        font-weight: 600;
    }
    .table-hover tbody tr:hover {
        background-color: #FFF3E0 !important; /* Oranye pudar saat kursor diarahkan */
    }
    
    /* Penyesuaian warna teks spesifik di dalam tabel */
    div[style*="color:var(--clr-text)"] {
        color: var(--macf-text) !important;
    }
    /* --- 1. MENGUBAH SEMUA KOTAK INPUT & PILIHAN (FILTER & DATATABLES) JADI TERANG --- */
    .form-control, 
    .form-select,
    .dataTables_wrapper select,
    .dataTables_wrapper input {
        background-color: #FFFFFF !important; /* Latar putih */
        color: #495057 !important; /* Teks gelap */
        border: 1px solid #CED4DA !important; /* Garis abu-abu terang */
    }

    /* Efek saat kotak pencarian/filter diklik */
    .form-control:focus, 
    .form-select:focus,
    .dataTables_wrapper select:focus,
    .dataTables_wrapper input:focus {
        background-color: #FFF8E1 !important; /* Kuning pastel */
        border-color: #F57C00 !important; /* Garis oranye */
        box-shadow: 0 0 0 0.25rem rgba(245, 124, 0, 0.25) !important;
        color: #495057 !important;
    }
    
    /* Warna teks panduan (placeholder) pencarian */
    .form-control::placeholder,
    .dataTables_wrapper input::placeholder {
        color: #B0BEC5 !important;
    }

    /* --- 2. JURUS PAMUNGKAS: MENGHILANGKAN BARIS TABEL HITAM --- */
    .table-hover > tbody > tr:hover > *,
    table > tbody > tr:hover > td,
    table > tbody > tr:hover > th,
    table.dataTable > tbody > tr.selected > *,
    table > tbody > tr.active > td {
        /* Menghancurkan variabel bawaan Bootstrap 5 */
        --bs-table-hover-bg: #FFF8E1 !important;
        --bs-table-hover-color: #495057 !important;
        --bs-table-accent-bg: transparent !important;
        
        /* Paksaan warna latar kuning pastel */
        background-color: #FFF8E1 !important; 
        box-shadow: inset 0 0 0 9999px #FFF8E1 !important; 
        color: #495057 !important; 
    }

    /* Memastikan teks nama nasabah dll tetap terbaca saat baris disorot */
    .table-hover > tbody > tr:hover td div,
    .table-hover > tbody > tr:hover td span:not(.badge) {
        color: #495057 !important;
    }
</style>

<div class="d-flex">
<?php include '../includes/sidebar.php'; ?>

<div class="main-wrapper">
    <div class="topbar">
        <div class="topbar-title">
             ALL BPKB
            <small>Keseluruhan Data &amp; Pencarian</small>
        </div>
        <div class="topbar-right">
            <a href="in_bpkb.php" class="btn btn-primary btn-sm no-print">
                <i class="fa-solid fa-plus"></i> Tambah BPKB
            </a>
        </div>
    </div>

    <div class="main-content">
        <?= $alert ?>

        <!-- Filter bar -->
        <div class="card mb-3 no-print">
            <div class="card-body" style="padding:14px 20px!important">
                <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
                    <div>
                        <label class="form-label mb-1 fw-bold text-secondary">Perusahaan</label>
                        <select name="perusahaan" class="form-select form-select-sm" style="min-width:140px">
                            <option value="">Semua</option>
                            <option value="MAF" <?= $fPerusahaan==='MAF'?'selected':'' ?>>MAF</option>
                            <option value="MCF" <?= $fPerusahaan==='MCF'?'selected':'' ?>>MCF</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1 fw-bold text-secondary">Status</label>
                        <select name="status" class="form-select form-select-sm" style="min-width:120px">
                            <option value="">Semua</option>
                            <option value="aktif"   <?= $fStatus==='aktif'?'selected':''   ?>>Aktif</option>
                            <option value="lunas"   <?= $fStatus==='lunas'?'selected':''   ?>>Lunas</option>
                            <option value="diambil" <?= $fStatus==='diambil'?'selected':'' ?>>Diambil</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter"></i> Filter</button>
                    <a href="all_bpkb.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-x"></i> Reset</a>
                </form>
            </div>
        </div>

        <!-- Tabel -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span> Daftar BPKB (<?= $result->num_rows ?> data)</span>
            </div>
            <div class="card-body">
            <div class="table-responsive">
            <table id="tblAll" class="table table-hover dt-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. BPKB</th>
                        <th>Nasabah</th>
                        <th>Kendaraan</th>
                        <th>No. Polisi</th>
                        <th>No. Rangka</th>
                        <th>Perusahaan</th>
                        <th>Tgl Masuk</th>
                        <th>Status</th>
                        <th class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; while ($r = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="mono fw-bold"><?= $r['no_bpkb'] ?></td>
                        <td>
                            <div style="font-weight:600;color:var(--macf-text)"><?= $r['nama_nasabah'] ?></div>
                            <div class="mono text-muted"><?= $r['no_kontrak'] ?></div>
                            <?php if ($r['no_hp']): ?>
                            <div style="font-size:11px;color:#6c757d">📞 <?= $r['no_hp'] ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= badgeKendaraan($r['jenis_kendaraan']) ?>
                            <div style="font-size:12px;margin-top:3px"><?= $r['merk_kendaraan'] ?> <?= $r['tahun_kendaraan'] ?></div>
                        </td>
                        <td class="mono"><?= $r['no_polisi'] ?></td>
                        <td class="mono" style="font-size:11px"><?= $r['no_rangka'] ?></td>
                        <td><?= badgePerusahaan($r['perusahaan']) ?></td>
                        <td style="white-space:nowrap"><?= formatTanggal($r['tanggal_masuk']) ?></td>
                        <td><?= badgeStatus($r['status']) ?></td>
                        <td class="no-print" style="white-space:nowrap">
                            <!-- Detail -->
                            <a href="detail_bpkb.php?id=<?= $r['id'] ?>" class="btn btn-outline-secondary btn-sm" title="Detail">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <!-- Tandai Lunas -->
                            <?php if ($r['status'] === 'aktif'): ?>
                            <a href="?aksi=lunas&id=<?= $r['id'] ?>" class="btn btn-success btn-sm"
                               data-confirm="Tandai BPKB ini sebagai LUNAS?" title="Lunas">
                                <i class="fa-solid fa-circle-check"></i>
                            </a>
                            <?php elseif ($r['status'] === 'lunas'): ?>
                            <a href="?aksi=aktif&id=<?= $r['id'] ?>" class="btn btn-warning btn-sm text-dark"
                               data-confirm="Kembalikan status ke Aktif?" title="Batalkan Lunas">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                            <?php endif; ?>
                            <!-- Hapus -->
                            <a href="?aksi=hapus&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
                               data-confirm="Yakin hapus data BPKB ini? Tindakan ini tidak bisa dibatalkan!" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
            </div>
        </div>

    </div>
</div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<?php include '../includes/footer.php'; ?>