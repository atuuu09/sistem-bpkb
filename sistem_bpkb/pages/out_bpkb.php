<?php
require_once '../config/config.php';
require_once '../config/database.php';
cekLogin();

$pageTitle = 'OUT BPKB — ' . APP_NAME;
$alert = '';
$role  = userRole();

// Hanya Kacab yang bisa APPROVE/acc (tandai diambil)
// Admin MAF/MCF hanya bisa MELIHAT (tidak ada tombol acc)
if (isset($_GET['aksi'], $_GET['id']) && $_GET['aksi'] === 'ambil') {
    if (!bisaAcc()) {
        $alert = '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left:4px solid #dc3545;">
                    <i class="fa-solid fa-ban me-2"></i> Akses ditolak! Hanya <strong>Ketua Cabang</strong> yang dapat meng-approve pengeluaran BPKB.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
    } else {
        $id = (int)$_GET['id'];
        $conn->query("UPDATE bpkb SET status='diambil', tanggal_keluar=CURDATE() WHERE id=$id AND status='lunas'");
        $alert = '<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-left:4px solid #198754;">
                    <i class="fa-solid fa-check-circle me-2"></i> BPKB berhasil di-<strong>approve</strong> dan ditandai sebagai <strong>Diambil / Keluar</strong>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
    }
}

// Filter perusahaan — otomatis sesuai role jika MAF/MCF
$fPerusahaan = '';
if ($role === 'MAF') {
    $fPerusahaan = 'MAF';
} elseif ($role === 'MCF') {
    $fPerusahaan = 'MCF';
} else {
    // Kacab bisa filter manual
    $fPerusahaan = $_GET['perusahaan'] ?? '';
}

$filterSQL = $fPerusahaan ? " AND b.perusahaan='" . $conn->real_escape_string($fPerusahaan) . "'" : '';

$lunas   = $conn->query("SELECT b.*, u.nama AS input_oleh FROM bpkb b JOIN users u ON b.user_input=u.id WHERE b.status='lunas' $filterSQL ORDER BY b.tanggal_lunas DESC");
$diambil = $conn->query("SELECT b.*, u.nama AS input_oleh FROM bpkb b JOIN users u ON b.user_input=u.id WHERE b.status='diambil' $filterSQL ORDER BY b.tanggal_keluar DESC");

include '../includes/header.php';
echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">';
echo '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>';
?>

<style>
    :root {
        --brand-yellow: #F4C222;
        --brand-orange: #E65A28;
        --text-dark: #1E2530;
    }
    .topbar {
        background-color: var(--brand-yellow) !important;
        border-radius: 8px !important;
        padding: 15px 25px !important;
        margin-bottom: 25px !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .topbar .topbar-title h4,
    .topbar .topbar-title small { color: var(--text-dark) !important; }
    .card { background-color: #fff !important; border: 1px solid #e5e7eb !important; border-radius: 10px !important; overflow: hidden; }
    .card-header { background-color: var(--brand-yellow) !important; color: var(--text-dark) !important; font-weight: 700 !important; border-bottom: none !important; padding: 16px 20px !important; }
    .table thead th { background-color: #fff9e6 !important; color: var(--text-dark) !important; border-bottom: 2px solid var(--brand-yellow) !important; font-weight: 700 !important; }
    .table tbody td { background-color: #fff !important; color: var(--text-dark) !important; }
    table.table-hover > tbody > tr:hover > td { background-color: #FFF8E1 !important; box-shadow: inset 0 0 0 9999px #FFF8E1 !important; color: #495057 !important; }
    .nav-tabs { border-bottom: 2px solid #e5e7eb; }
    .nav-tabs .nav-link { color: #6b7280; font-weight: 600; border: none; background: transparent; border-bottom: 3px solid transparent; padding: 10px 20px; margin-bottom: -2px; transition: all 0.3s ease; }
    .nav-tabs .nav-link:hover { color: var(--brand-orange); }
    .nav-tabs .nav-link.active { color: var(--brand-orange) !important; background: transparent !important; border-bottom: 3px solid var(--brand-orange) !important; }
    .badge-tab { background-color: #e9ecef; color: #495057; border-radius: 12px; padding: 4px 8px; font-size: 0.75rem; margin-left: 5px; }
    .nav-link.active .badge-tab { background-color: rgba(230,90,40,0.1); color: var(--brand-orange); }
    .btn-print-custom { background-color: #fff; color: var(--text-dark); border: 1px solid var(--text-dark); font-weight: 600; transition: all 0.2s; }
    .btn-print-custom:hover { background-color: var(--text-dark); color: #fff; }

    /* Banner info role */
    .role-access-banner {
        border-radius: 8px;
        padding: 10px 16px;
        margin-bottom: 16px;
        font-size: 0.88rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .role-access-banner.kacab-mode {
        background: rgba(46,125,50,0.08);
        border: 1px solid rgba(46,125,50,0.25);
        color: #2E7D32;
    }
    .role-access-banner.view-only {
        background: rgba(21,101,192,0.07);
        border: 1px solid rgba(21,101,192,0.2);
        color: #1565C0;
    }
</style>

<div class="d-flex">
<?php include '../includes/sidebar.php'; ?>

<div class="main-wrapper w-100 p-4" style="background-color: #f8f9fa !important;">
    <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <div class="topbar-title">
            <h4 class="mb-1 fw-bold">OUT BPKB</h4>
            <small style="font-weight:500;">Nasabah Lunas — Siap Pengambilan</small>
        </div>
        <div class="topbar-right no-print d-flex align-items-center gap-3">
            <?php if ($role === 'kacab'): ?>
            <form method="GET" class="d-flex align-items-center mb-0">
                <div class="input-group input-group-sm shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-building text-muted"></i></span>
                    <select name="perusahaan" class="form-select border-start-0" style="width:140px; cursor:pointer;" onchange="this.form.submit()">
                        <option value="">Semua Perusahaan</option>
                        <option value="MAF" <?= $fPerusahaan==='MAF'?'selected':'' ?>>MAF</option>
                        <option value="MCF" <?= $fPerusahaan==='MCF'?'selected':'' ?>>MCF</option>
                    </select>
                </div>
            </form>
            <?php endif; ?>
            <button class="btn btn-sm btn-print-custom px-3 py-1 rounded shadow-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Cetak
            </button>
        </div>
    </div>

    <div class="main-content">
        <?= $alert ?>

        <?php if ($role === 'kacab'): ?>
        <div class="role-access-banner kacab-mode">
            <i class="fa-solid fa-shield-check fa-lg"></i>
            Mode <strong>Ketua Cabang</strong> — Anda dapat meng-<strong>approve</strong> pengeluaran BPKB dengan menekan tombol <strong>Acc &amp; Keluarkan</strong>
        </div>
        <?php else: ?>
        <div class="role-access-banner view-only">
            <i class="fa-solid fa-eye fa-lg"></i>
            Mode <strong><?= roleLabelFull($role) ?></strong> — Anda hanya dapat <strong>melihat</strong> data OUT BPKB. Approval dilakukan oleh Ketua Cabang.
        </div>
        <?php endif; ?>

        <div class="d-none d-print-block mb-4" style="text-align:center; border-bottom:2px solid #000; padding-bottom:12px;">
            <h2 style="font-size:20px; font-weight:700; color:#000;">DAFTAR BPKB LUNAS — SIAP DIAMBIL</h2>
            <p style="font-size:13px; color:#333;">Tanggal Cetak: <?= date('d/m/Y H:i') ?> | Dicetak oleh: <?= userNama() ?> (<?= roleLabelFull($role) ?>)</p>
        </div>

        <ul class="nav nav-tabs mb-4 no-print" id="outTab">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-lunas">
                    Siap Diambil <span class="badge-tab"><?= $lunas->num_rows ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-diambil">
                    Sudah Diambil <span class="badge-tab"><?= $diambil->num_rows ?></span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-lunas">
                <div class="card shadow-sm">
                    <div class="card-header">
                        BPKB Lunas — Menunggu Diambil (<?= $lunas->num_rows ?> data)
                        <?php if ($role === 'kacab'): ?>
                        <span class="ms-2 badge" style="background:rgba(46,125,50,0.15);color:#2E7D32;font-size:0.75rem;">
                            <i class="fa-solid fa-gavel me-1"></i>Butuh Approval Kacab
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive p-3">
                            <table id="tblLunas" class="table table-hover align-middle dt-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>No. BPKB</th>
                                        <th>Nasabah</th>
                                        <th>Kendaraan</th>
                                        <th>No. Polisi</th>
                                        <th>PT</th>
                                        <th>Tgl Lunas</th>
                                        <th class="no-print text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $no=1; while ($r = $lunas->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-muted"><?= $no++ ?></td>
                                        <td class="fw-semibold font-monospace" style="font-size:0.9rem;"><?= $r['no_bpkb'] ?></td>
                                        <td>
                                            <div style="font-weight:700;"><?= $r['nama_nasabah'] ?></div>
                                            <div class="font-monospace text-muted" style="font-size:0.85rem;"><?= $r['no_kontrak'] ?></div>
                                        </td>
                                        <td>
                                            <div class="mb-1"><?= badgeKendaraan($r['jenis_kendaraan']) ?></div>
                                            <div style="font-size:12px; font-weight:500; color:#495057;"><?= $r['merk_kendaraan'] ?> <?= $r['tahun_kendaraan'] ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark text-white rounded-1" style="font-size:0.8rem; letter-spacing:1px;">
                                                <?= $r['no_polisi'] ?>
                                            </span>
                                        </td>
                                        <td><?= badgePerusahaan($r['perusahaan']) ?></td>
                                        <td>
                                            <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                <i class="fa-solid fa-calendar-check me-1"></i> <?= formatTanggal($r['tanggal_lunas']) ?>
                                            </div>
                                        </td>
                                        <td class="no-print text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="detail_bpkb.php?id=<?= $r['id'] ?>" class="btn btn-light btn-sm border shadow-sm text-secondary" title="Detail">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <?php if (bisaAcc()): ?>
                                                <a href="?aksi=ambil&id=<?= $r['id'] ?><?= $fPerusahaan?'&perusahaan='.$fPerusahaan:'' ?>"
                                                   class="btn btn-sm shadow-sm"
                                                   style="background-color:#2E7D32; color:white; border:none;"
                                                   onclick="return confirm('Approve & tandai BPKB ini sudah DIAMBIL oleh nasabah?')" title="Acc & Keluarkan">
                                                    <i class="fa-solid fa-check-circle me-1"></i> Acc &amp; Keluarkan
                                                </a>
                                                <?php else: ?>
                                                <span class="btn btn-sm btn-light border text-muted" style="cursor:not-allowed;" title="Hanya Kacab yang bisa approve">
                                                    <i class="fa-solid fa-lock me-1"></i> Butuh Kacab
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-diambil">
                <div class="card shadow-sm">
                    <div class="card-header">BPKB Sudah Diambil (<?= $diambil->num_rows ?> data)</div>
                    <div class="card-body p-0">
                        <div class="table-responsive p-3">
                            <table id="tblDiambil" class="table table-hover align-middle dt-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>No. BPKB</th>
                                        <th>Nasabah</th>
                                        <th>Kendaraan</th>
                                        <th>No. Polisi</th>
                                        <th>PT</th>
                                        <th>Tgl Keluar</th>
                                        <th>Diproses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $no=1; while ($r = $diambil->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-muted"><?= $no++ ?></td>
                                        <td class="fw-semibold font-monospace" style="font-size:0.9rem;"><?= $r['no_bpkb'] ?></td>
                                        <td>
                                            <div style="font-weight:700;"><?= $r['nama_nasabah'] ?></div>
                                            <div class="font-monospace text-muted" style="font-size:0.85rem;"><?= $r['no_kontrak'] ?></div>
                                        </td>
                                        <td>
                                            <?= badgeKendaraan($r['jenis_kendaraan']) ?>
                                            <span style="font-size:13px; font-weight:500;" class="ms-1"><?= $r['merk_kendaraan'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary text-white rounded-1" style="font-size:0.8rem; letter-spacing:1px;">
                                                <?= $r['no_polisi'] ?>
                                            </span>
                                        </td>
                                        <td><?= badgePerusahaan($r['perusahaan']) ?></td>
                                        <td>
                                            <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                                <i class="fa-solid fa-people-carry-box me-1"></i> <?= formatTanggal($r['tanggal_keluar']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size:12px; color:#6b7280;">
                                                <i class="fa-solid fa-user-check me-1"></i><?= $r['input_oleh'] ?>
                                            </span>
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
</div>
</div>

<script>
$(document).ready(function() {
    $('.dt-table').DataTable({
        "destroy": true,
        "language": {
            "search": "Cari Data:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "emptyTable": "Belum ada data BPKB di kategori ini.",
            "zeroRecords": "Data BPKB tidak ditemukan.",
            "paginate": { "first": "Awal", "last": "Akhir", "next": "Lanjut", "previous": "Kembali" }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>