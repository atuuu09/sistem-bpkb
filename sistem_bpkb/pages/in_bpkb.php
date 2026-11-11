<?php
require_once '../config/config.php';
require_once '../config/database.php';
cekLogin();

// Hanya MAF dan MCF yang boleh akses halaman ini
if (!bisaInput()) {
    header('Location: home.php?akses=ditolak');
    exit();
}

$pageTitle = 'IN BPKB — ' . APP_NAME;
$alert = '';
$role = userRole(); // 'MAF' atau 'MCF'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_bpkb         = sanitize($_POST['no_bpkb']);
    $no_polisi       = sanitize($_POST['no_polisi']);
    $no_rangka       = sanitize($_POST['no_rangka']);
    $no_mesin        = sanitize($_POST['no_mesin']);
    $nama_nasabah    = sanitize($_POST['nama_nasabah']);
    $no_kontrak      = sanitize($_POST['no_kontrak']);
    $no_hp           = sanitize($_POST['no_hp']);
    $alamat          = sanitize($_POST['alamat']);
    $jenis_kendaraan = sanitize($_POST['jenis_kendaraan']);
    $merk_kendaraan  = sanitize($_POST['merk_kendaraan']);
    $tahun_kendaraan = (int)$_POST['tahun_kendaraan'];
    $warna           = sanitize($_POST['warna']);
    $perusahaan      = $role; // Force perusahaan = role user (MAF atau MCF)
    $tanggal_masuk   = sanitize($_POST['tanggal_masuk']);
    $keterangan      = sanitize($_POST['keterangan']);
    $user_input      = userId();

    // Validasi: no_kontrak MAF harus diawali 1000 (10 digit)
    // no_kontrak MCF harus diawali 5000 (10 digit)
    $valid = true;
    $kontrakError = '';
    if ($role === 'MAF') {
        if (!preg_match('/^1000\d{6}$/', $no_kontrak)) {
            $valid = false;
            $kontrakError = 'No. Kontrak MAF harus 10 digit diawali dengan 1000 (contoh: 1000000001)';
        }
    } elseif ($role === 'MCF') {
        if (!preg_match('/^5000\d{6}$/', $no_kontrak)) {
            $valid = false;
            $kontrakError = 'No. Kontrak MCF harus 10 digit diawali dengan 5000 (contoh: 5000000001)';
        }
    }

    if (!$valid) {
        $alert = '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #dc3545;">
            <i class="fa-solid fa-circle-exclamation me-2"></i>' . $kontrakError . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO bpkb
            (no_bpkb, no_polisi, no_rangka, no_mesin, nama_nasabah, no_kontrak, no_hp, alamat,
             jenis_kendaraan, merk_kendaraan, tahun_kendaraan, warna, perusahaan, tanggal_masuk, keterangan, user_input)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssssssssisssi',
            $no_bpkb, $no_polisi, $no_rangka, $no_mesin, $nama_nasabah, $no_kontrak, $no_hp, $alamat,
            $jenis_kendaraan, $merk_kendaraan, $tahun_kendaraan, $warna, $perusahaan, $tanggal_masuk, $keterangan, $user_input
        );

        if ($stmt->execute()) {
            $alert = '<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #198754;">
                <i class="fa-solid fa-circle-check me-2"></i>
                BPKB <strong>' . $no_bpkb . '</strong> atas nama <strong>' . $nama_nasabah . '</strong> berhasil disimpan sebagai data <strong>' . $perusahaan . '</strong>!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            $alert = '<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-left: 4px solid #dc3545;">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                Gagal menyimpan. ' . $conn->error . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
}

// Warna tema sesuai role
$themeColor  = $role === 'MAF' ? '#F57C00' : '#1565C0';
$themeBg     = $role === 'MAF' ? '#FFF3E0' : '#E3F2FD';
$themeBorder = $role === 'MAF' ? '#F57C00' : '#1565C0';

// Prefix contoh no kontrak
$kontrakPrefix  = $role === 'MAF' ? '1000' : '5000';
$kontrakContoh  = $role === 'MAF' ? '1000000001' : '5000000001';

include '../includes/header.php';
?>

<style>
    :root {
        --brand-yellow: #F4C222;
        --brand-orange: <?= $themeColor ?>;
        --text-dark: #1E2530;
    }
    .topbar {
        background-color: <?= $themeColor ?> !important;
        border-radius: 8px !important;
        padding: 15px 25px !important;
        margin-bottom: 25px !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .topbar .topbar-title h4,
    .topbar .topbar-title small { color: #fff !important; }
    .date-badge {
        background-color: rgba(255,255,255,0.25);
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .card {
        background-color: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 10px !important;
        overflow: hidden;
    }
    .card-header {
        background-color: <?= $themeColor ?> !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-bottom: none !important;
        padding: 16px 20px !important;
    }
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: <?= $themeColor ?>;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid <?= $themeBg ?>;
    }
    .form-control, .form-select {
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        border-radius: 6px;
        padding: 10px 15px;
        color: #333 !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: <?= $themeColor ?> !important;
        box-shadow: 0 0 0 3px <?= $role === 'MAF' ? 'rgba(245,124,0,0.15)' : 'rgba(21,101,192,0.15)' ?> !important;
    }
    .form-label { font-weight: 600; color: #4b5563; font-size: 0.9rem; }
    .btn-brand {
        background-color: <?= $themeColor ?>;
        color: #ffffff;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .btn-brand:hover { filter: brightness(0.9); color: #ffffff; transform: translateY(-1px); }

    .role-banner {
        background: <?= $themeBg ?>;
        border: 1px solid <?= $themeColor ?>55;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: <?= $themeColor ?>;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .no-kontrak-hint {
        font-size: 0.78rem;
        color: <?= $themeColor ?>;
        margin-top: 4px;
        font-weight: 600;
    }
</style>

<div class="d-flex">
<?php include '../includes/sidebar.php'; ?>

<div class="main-wrapper w-100 p-4" style="background-color: #f8f9fa !important;">
    <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <div class="topbar-title">
            <h4 class="mb-1 fw-bold">IN BPKB</h4>
            <small style="font-weight:500;">Form Input BPKB Baru — <?= $role === 'MAF' ? 'Mega Auto Finance' : 'Mega Central Finance' ?></small>
        </div>
        <div class="topbar-right">
            <span class="date-badge" id="live-date"></span>
        </div>
    </div>

    <div class="main-content">
        <?= $alert ?>

        <!-- Banner Perusahaan -->
        <div class="role-banner">
            <i class="fa-solid <?= $role === 'MAF' ? 'fa-building-columns' : 'fa-landmark' ?> fa-lg"></i>
            Anda login sebagai <strong>Admin <?= $role ?></strong> — Data yang diinput akan otomatis tersimpan sebagai data <strong><?= $role ?></strong>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fa-solid fa-file-signature me-2"></i> Form Penerimaan BPKB — <?= $role ?>
            </div>
            <div class="card-body p-4">
            <form method="POST" autocomplete="off">

                <div class="section-title">
                    <i class="fa-solid fa-car me-2"></i> Data BPKB &amp; Kendaraan
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">No. BPKB <span class="text-danger">*</span></label>
                        <input type="text" name="no_bpkb" class="form-control" placeholder="Cth: H-12345678" required>
                        <div class="no-kontrak-hint"><i class="fa-solid fa-circle-info me-1"></i>Format: huruf-8angka (cth: H-12345678)</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. Polisi <span class="text-danger">*</span></label>
                        <input type="text" name="no_polisi" class="form-control" placeholder="D 1234 ABC" required style="text-transform:uppercase">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" class="form-control" value="<?= $role ?> — <?= $role === 'MAF' ? 'Mega Auto Finance' : 'Mega Central Finance' ?>" disabled style="background:#f5f5f5!important;color:#666!important;">
                        <div class="no-kontrak-hint"><i class="fa-solid fa-lock me-1"></i>Otomatis sesuai login</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. Rangka <span class="text-danger">*</span></label>
                        <input type="text" name="no_rangka" class="form-control font-monospace" placeholder="MH1JF..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. Mesin <span class="text-danger">*</span></label>
                        <input type="text" name="no_mesin" class="form-control font-monospace" placeholder="JF11E-..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                        <select name="jenis_kendaraan" class="form-select" required>
                            <option value="Motor">Motor</option>
                            <option value="Mobil">Mobil</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Merk / Tipe</label>
                        <input type="text" name="merk_kendaraan" class="form-control" placeholder="Honda Beat">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun_kendaraan" class="form-control" placeholder="2023" min="1990" max="<?= date('Y') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Warna</label>
                        <input type="text" name="warna" class="form-control" placeholder="Hitam, Putih...">
                    </div>
                </div>

                <div class="section-title mt-2">
                    <i class="fa-solid fa-user me-2"></i> Data Nasabah
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Nasabah <span class="text-danger">*</span></label>
                        <input type="text" name="nama_nasabah" class="form-control" placeholder="Nama lengkap nasabah" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Kontrak <span class="text-danger">*</span></label>
                        <input type="text" name="no_kontrak" class="form-control font-monospace"
                               placeholder="<?= $kontrakContoh ?>" required maxlength="10"
                               pattern="<?= $kontrakPrefix ?>\d{6}"
                               oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <div class="no-kontrak-hint">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            <?= $role ?> — 10 digit, diawali <strong><?= $kontrakPrefix ?></strong> (cth: <?= $kontrakContoh ?>)
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat lengkap nasabah">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan jika ada..."></textarea>
                    </div>
                </div>

                <hr style="border-color:#e5e7eb; margin: 30px 0 20px;">

                <div class="d-flex gap-2 justify-content-end">
                    <a href="all_bpkb.php" class="btn btn-light border px-4">
                        <i class="fa-solid fa-arrow-left me-1"></i> Batal
                    </a>
                    <button type="reset" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Data <?= $role ?>
                    </button>
                </div>

            </form>
            </div>
        </div>
    </div>
</div>
</div>
<?php include '../includes/footer.php'; ?>
