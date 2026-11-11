<?php
require_once '../config/config.php';
require_once '../config/database.php';
cekLogin();

$pageTitle = 'Home — ' . APP_NAME;
$role = userRole();

// Pesan akses ditolak jika ada
$aksesAlert = '';
if (isset($_GET['akses']) && $_GET['akses'] === 'ditolak') {
    $aksesAlert = '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left:4px solid #dc3545;">
        <i class="fa-solid fa-ban me-2"></i> Akses ditolak! Anda tidak memiliki izin untuk halaman tersebut.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

// ── Statistik (filter sesuai role) ───────────────────────────────────────────
// MAF hanya lihat data MAF, MCF hanya lihat data MCF, Kacab lihat semua
$whereRole = '';
if ($role === 'MAF') $whereRole = " WHERE perusahaan='MAF'";
elseif ($role === 'MCF') $whereRole = " WHERE perusahaan='MCF'";

$s = [];
$s['total']    = $conn->query("SELECT COUNT(*) c FROM bpkb" . ($whereRole ?: ''))->fetch_assoc()['c'];
$s['aktif']    = $conn->query("SELECT COUNT(*) c FROM bpkb" . ($whereRole ? $whereRole . " AND status='aktif'" : " WHERE status='aktif'"))->fetch_assoc()['c'];
$s['lunas']    = $conn->query("SELECT COUNT(*) c FROM bpkb" . ($whereRole ? $whereRole . " AND status='lunas'" : " WHERE status='lunas'"))->fetch_assoc()['c'];
$s['diambil']  = $conn->query("SELECT COUNT(*) c FROM bpkb" . ($whereRole ? $whereRole . " AND status='diambil'" : " WHERE status='diambil'"))->fetch_assoc()['c'];
$s['maf']      = $conn->query("SELECT COUNT(*) c FROM bpkb WHERE perusahaan='MAF'")->fetch_assoc()['c'];
$s['mcf']      = $conn->query("SELECT COUNT(*) c FROM bpkb WHERE perusahaan='MCF'")->fetch_assoc()['c'];
$s['motor']    = $conn->query("SELECT COUNT(*) c FROM bpkb WHERE jenis_kendaraan='Motor'")->fetch_assoc()['c'];
$s['mobil']    = $conn->query("SELECT COUNT(*) c FROM bpkb WHERE jenis_kendaraan='Mobil'")->fetch_assoc()['c'];

// BPKB masuk bulan ini
$whereRoleAnd = $whereRole ? str_replace(' WHERE ', ' AND ', $whereRole) : '';
$s['bulan_ini'] = $conn->query("SELECT COUNT(*) c FROM bpkb WHERE MONTH(tanggal_masuk)=MONTH(CURDATE()) AND YEAR(tanggal_masuk)=YEAR(CURDATE())" . $whereRoleAnd)->fetch_assoc()['c'];

// 5 data terbaru (filter sesuai role)
$recentWhere = $whereRole ? $whereRole : '';
$recent = $conn->query("SELECT b.*, u.nama AS input_oleh
    FROM bpkb b JOIN users u ON b.user_input = u.id
    $recentWhere
    ORDER BY b.created_at DESC LIMIT 5");

include '../includes/header.php';
?>

<style>
    :root {
        --brand-yellow: #F4C222;
        --brand-orange: #E65A28;
        --brand-grey: #5A5A5C;
        --text-dark: #1E2530;
    /* --- BONGKAR TOTAL WARNA HOVER GELAP --- */
    .table-hover > tbody > tr:hover > *,
    table > tbody > tr:hover > td,
    table > tbody > tr:hover > th {
        /* Menghancurkan variabel bawaan Bootstrap 5 */
        --bs-table-hover-bg: #FFF8E1 !important;
        --bs-table-hover-color: #495057 !important;
        --bs-table-accent-bg: transparent !important;
        
        /* Paksaan warna latar kuning pastel */
        background-color: #FFF8E1 !important; 
        box-shadow: inset 0 0 0 9999px #FFF8E1 !important; 
        color: #495057 !important; 
    }

    /* Memaksa teks nama nasabah, no kontrak, dan teks abu-abu lainnya tetap terbaca */
    .table-hover > tbody > tr:hover td div,
    .table-hover > tbody > tr:hover td span:not(.badge) {
        color: #495057 !important;
    }
    }

    /* Override Topbar */
    .topbar {
        background-color: var(--brand-yellow) !important;
        border-radius: 8px !important;
        padding: 15px 25px !important;
        margin-bottom: 25px !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .topbar .topbar-title h4,
    .topbar .topbar-title small {
        color: var(--text-dark) !important;
    }

    .date-badge {
        background-color: #ffffff;
        color: var(--text-dark);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Kustomisasi Card */
    .card {
        background-color: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 10px !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02) !important;
    }
    
    .card-header {
        background-color: #ffffff !important;
        color: var(--text-dark) !important;
        font-weight: 700 !important;
        border-bottom: 1px solid #f3f4f6 !important;
        padding: 16px 20px !important;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Styling 4 Stat Card Atas */
    .stat-card-custom {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .stat-card-custom:hover {
        transform: translateY(-3px);
    }
    .stat-icon-custom {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-info .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1;
        margin-bottom: 5px;
    }
    .stat-info .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
    }

    /* Warna Icon Stat */
    .icon-blue { background: #e0f2fe; color: #0284c7; }
    .icon-green { background: #dcfce7; color: #16a34a; }
    .icon-orange { background: rgba(230, 90, 40, 0.15); color: var(--brand-orange); }
    .icon-yellow { background: rgba(244, 194, 34, 0.2); color: #d97706; }

    /* Progress Bar Perusahaan */
    .progress-bar-custom {
        height: 8px;
        background: #f3f4f6;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .progress-fill-maf { background-color: var(--brand-yellow); }
    .progress-fill-mcf { background-color: var(--brand-orange); }

    /* Box Info Kendaraan & Status */
    .info-box {
        background: #f8f9fa;
        border: 1px solid #f3f4f6;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    /* Table Header */
    .table thead th {
        background-color: #f9fafb !important;
        color: #4b5563 !important;
        font-size: 0.8rem;
        text-transform: uppercase;
        border-bottom: 2px solid #e5e7eb !important;
    }
    .table tbody td {
        vertical-align: middle;
    }
</style>

<div class="d-flex">
<?php include '../includes/sidebar.php'; ?>

<div class="main-wrapper w-100 p-4" style="background-color: #f4f6f9 !important;">

    <?= $aksesAlert ?>

    <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <div class="topbar-title">
            <h4 class="mb-1 fw-bold">Dashboard</h4>
            <small style="font-weight: 500;">Selamat datang, <?= userNama() ?></small>
        </div>
        <div class="topbar-right d-flex gap-2 align-items-center">
            <span class="date-badge" id="live-date"></span>
            <span class="badge" style="background-color: var(--brand-orange); padding: 8px 12px; font-size: 0.85rem;">
                <i class="fa-solid fa-user-shield me-1"></i> <?= roleLabelFull(userRole()) ?>
            </span>
        </div>
    </div>

    <div class="main-content">

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card-custom">
                    <div class="stat-icon-custom icon-blue">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $s['total'] ?></div>
                        <div class="stat-label">Total BPKB</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-custom">
                    <div class="stat-icon-custom icon-green">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $s['aktif'] ?></div>
                        <div class="stat-label">BPKB Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-custom">
                    <div class="stat-icon-custom icon-yellow">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $s['lunas'] ?></div>
                        <div class="stat-label">Nasabah Lunas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card-custom">
                    <div class="stat-icon-custom icon-orange">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $s['bulan_ini'] ?></div>
                        <div class="stat-label">Masuk Bulan Ini</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fa-solid fa-building me-2 text-muted"></i> Distribusi Perusahaan
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background-color: var(--brand-yellow); color: #000; padding: 6px 14px; font-weight: 700;">MAF</span>
                            <span style="font-size:24px; font-weight:700; font-family:var(--font-mono); color:var(--text-dark)"><?= $s['maf'] ?></span>
                        </div>
                        <div class="progress-bar-custom">
                            <?php $pct_maf = $s['total'] ? round($s['maf']/$s['total']*100) : 0; ?>
                            <div class="progress-fill-maf" style="width:<?= $pct_maf ?>%; height:100%;"></div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-2 mt-4">
                            <span class="badge" style="background-color: var(--brand-orange); color: #fff; padding: 6px 14px; font-weight: 700;">MCF</span>
                            <span style="font-size:24px; font-weight:700; font-family:var(--font-mono); color:var(--text-dark)"><?= $s['mcf'] ?></span>
                        </div>
                        <div class="progress-bar-custom" style="margin-bottom: 0;">
                            <?php $pct_mcf = $s['total'] ? round($s['mcf']/$s['total']*100) : 0; ?>
                            <div class="progress-fill-mcf" style="width:<?= $pct_mcf ?>%; height:100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fa-solid fa-car-side me-2 text-muted"></i> Jenis Kendaraan
                    </div>
                    <div class="card-body p-4 d-flex flex-column gap-3 justify-content-center">
                        <div class="info-box">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:40px; height:40px; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--brand-grey); box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                    <i class="fa-solid fa-motorcycle fs-5"></i>
                                </div>
                                <div style="font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">Motor</div>
                            </div>
                            <div style="font-size:22px; font-weight:700; font-family:var(--font-mono); color:var(--text-dark);"><?= $s['motor'] ?></div>
                        </div>
                        
                        <div class="info-box" style="margin-bottom: 0;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:40px; height:40px; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--brand-grey); box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                    <i class="fa-solid fa-car fs-5"></i>
                                </div>
                                <div style="font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">Mobil</div>
                            </div>
                            <div style="font-size:22px; font-weight:700; font-family:var(--font-mono); color:var(--text-dark);"><?= $s['mobil'] ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fa-solid fa-clipboard-list me-2 text-muted"></i> Status BPKB
                    </div>
                    <div class="card-body p-4 d-flex flex-column gap-2 justify-content-center">
                        <?php
                        $statuses = [
                            ['label'=>'Aktif',   'val'=>$s['aktif'],   'color'=>'#0d6efd', 'icon'=>'fa-circle-dot'],
                            ['label'=>'Lunas',   'val'=>$s['lunas'],   'color'=>'#198754', 'icon'=>'fa-circle-check'],
                            ['label'=>'Diambil', 'val'=>$s['diambil'], 'color'=>'var(--brand-orange)', 'icon'=>'fa-arrow-right-from-bracket'],
                        ];
                        foreach ($statuses as $st):
                        ?>
                        <div class="info-box">
                            <div style="font-weight:600; color:#4b5563; font-size:14px;">
                                <i class="fa-solid <?= $st['icon'] ?> me-2" style="color: <?= $st['color'] ?>;"></i> <?= $st['label'] ?>
                            </div>
                            <span style="font-weight:700; font-family:var(--font-mono); font-size:20px; color:var(--text-dark);"><?= $st['val'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-regular fa-clock me-2 text-muted"></i> BPKB Terbaru Masuk</span>
                <a href="all_bpkb.php" class="btn btn-sm" style="background-color: var(--brand-orange); color: white; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                    Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No. BPKB</th>
                            <th>Nasabah</th>
                            <th>Kendaraan</th>
                            <th>No. Polisi</th>
                            <th>Perusahaan</th>
                            <th>Tgl Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($r = $recent->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-semibold font-monospace text-muted" style="font-size: 0.9rem;"><?= $r['no_bpkb'] ?></td>
                            <td>
                                <div style="font-weight:700; color:var(--text-dark);"><?= $r['nama_nasabah'] ?></div>
                                <div class="font-monospace text-muted" style="font-size: 0.85rem;"><?= $r['no_kontrak'] ?></div>
                            </td>
                            <td>
                                <div class="mb-1"><?= badgeKendaraan($r['jenis_kendaraan']) ?></div>
                                <div style="font-size:12px; font-weight: 500; color: #495057;"><?= $r['merk_kendaraan'] ?></div>
                            </td>
                            <td>
                                <span class="badge bg-dark text-white rounded-1" style="font-size: 0.8rem; letter-spacing: 1px;">
                                    <?= $r['no_polisi'] ?>
                                </span>
                            </td>
                            <td><?= badgePerusahaan($r['perusahaan']) ?></td>
                            <td>
                                <span style="font-size: 13px; font-weight: 500; color: #6b7280;">
                                    <?= formatTanggal($r['tanggal_masuk']) ?>
                                </span>
                            </td>
                            <td><?= badgeStatus($r['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if($recent->num_rows == 0): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data masuk.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div></div></div><?php include '../includes/footer.php'; ?>