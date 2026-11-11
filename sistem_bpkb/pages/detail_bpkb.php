<?php
require_once '../config/config.php';
require_once '../config/database.php';
cekLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: all_bpkb.php'); exit(); }

$stmt = $conn->prepare("SELECT b.*, u.nama AS input_oleh FROM bpkb b JOIN users u ON b.user_input=u.id WHERE b.id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if (!$r) { header('Location: all_bpkb.php'); exit(); }

$pageTitle = 'Detail BPKB — ' . $r['no_bpkb'];
include '../includes/header.php';
?>

<style>
    :root {
        --brand-yellow: #F4C222;
        --brand-orange: #E65A28;
        --brand-grey: #5A5A5C;
    }
    .topbar { background-color: var(--brand-yellow) !important; border-radius: 8px; padding: 15px 25px; margin-bottom: 25px; }
    .topbar .topbar-title h4 { color: #1E2530 !important; }
    
    /* Perbaikan: Paksa Card & Card Body Berwarna Putih Bersih */
    .card { 
        border: 1px solid #e5e7eb !important; 
        border-radius: 10px !important; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.02) !important; 
        background-color: #ffffff !important; 
        color: #1E2530 !important;
    }
    .card-header { 
        background-color: var(--brand-yellow) !important; 
        color: #1E2530 !important; 
        font-weight: 700; 
        border-bottom: none; 
        padding: 15px 20px; 
    }
    .card-body { 
        background-color: #ffffff !important; 
        border-radius: 0 0 10px 10px !important; 
        padding: 20px !important;
    }
    
    /* Data Grid */
    .data-row { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #f3f4f6; }
    .data-label { font-size: 12px; color: #6b7280 !important; font-weight: 700; text-transform: uppercase; }
    .data-value { font-weight: 600; color: #1E2530 !important; text-align: right; }
    
    /* Proteksi Warna Teks Khusus Agar Tidak Mengikuti Dark Mode */
    .text-dark-forced { color: #1E2530 !important; }
    .text-muted-forced { color: #6b7280 !important; }
</style>

<div class="d-flex">
<?php include '../includes/sidebar.php'; ?>
<div class="main-wrapper w-100 p-4" style="background-color: #f8f9fa;">
    
    <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <div class="topbar-title">
            <h4 class="mb-1 fw-bold text-dark-forced">Detail BPKB</h4>
            <small class="text-dark-forced" style="font-weight:500;"><?= $r['no_bpkb'] ?></small>
        </div>
        <div class="topbar-right d-flex gap-2">
            <a href="all_bpkb.php" class="btn btn-sm btn-light border text-dark-forced">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
            <button class="btn btn-sm btn-light border text-dark-forced" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Cetak
            </button>
        </div>
    </div>

    <div class="main-content">
        <div class="card p-4 mb-4" style="background-color: #ffffff !important;">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="text-muted-forced text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Nomor BPKB</div>
                    <h2 class="fw-bold mb-3 font-monospace text-dark-forced"><?= $r['no_bpkb'] ?></h2>
                    <div class="d-flex gap-2">
                        <?= badgeStatus($r['status']) ?>
                        <?= badgePerusahaan($r['perusahaan']) ?>
                        <?= badgeKendaraan($r['jenis_kendaraan']) ?>
                    </div>
                </div>
                <div class="text-md-end">
                    <div class="text-muted-forced" style="font-size: 11px;">Diinput oleh</div>
                    <div class="fw-bold text-dark-forced"><?= $r['input_oleh'] ?></div>
                    <div class="text-muted-forced" style="font-size: 12px;"><?= formatTanggal(substr($r['created_at'], 0, 10)) ?></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="fa-solid fa-car me-2"></i> Data Kendaraan</div>
                    <div class="card-body">
                        <?php
                        $fields = [
                            'No. Polisi'    => $r['no_polisi'],
                            'Merk / Tipe'   => $r['merk_kendaraan'],
                            'Tahun'         => $r['tahun_kendaraan'],
                            'Warna'         => $r['warna'],
                            'No. Rangka'    => '<span class="font-monospace text-dark-forced">'.$r['no_rangka'].'</span>',
                            'No. Mesin'     => '<span class="font-monospace text-dark-forced">'.$r['no_mesin'].'</span>',
                        ];
                        foreach ($fields as $k => $v): if (!$v) continue; ?>
                        <div class="data-row">
                            <span class="data-label"><?= $k ?></span>
                            <span class="data-value"><?= $v ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="fa-solid fa-user me-2"></i> Data Nasabah</div>
                    <div class="card-body">
                        <?php
                        $fields2 = [
                            'Nama'          => $r['nama_nasabah'],
                            'No. Kontrak'   => '<span class="font-monospace text-dark-forced">'.$r['no_kontrak'].'</span>',
                            'No. HP'        => $r['no_hp'],
                            'Alamat'        => $r['alamat'],
                            'Perusahaan'    => badgePerusahaan($r['perusahaan']),
                            'Tgl Masuk'     => formatTanggal($r['tanggal_masuk']),
                            'Tgl Lunas'     => $r['tanggal_lunas'] ? formatTanggal($r['tanggal_lunas']) : '-',
                            'Tgl Keluar'    => $r['tanggal_keluar'] ? formatTanggal($r['tanggal_keluar']) : '-',
                            'Keterangan'    => $r['keterangan'] ?: '-',
                        ];
                        foreach ($fields2 as $k => $v): ?>
                        <div class="data-row">
                            <span class="data-label" style="white-space:nowrap;"><?= $k ?></span>
                            <span class="data-value"><?= $v ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php include '../includes/footer.php'; ?>