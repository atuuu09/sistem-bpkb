<?php
// Deteksi halaman aktif
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$role = userRole();
$roleColor = ($role === 'MAF') ? 'role-maf' : (($role === 'MCF') ? 'role-mcf' : 'role-kacab');

// Menu sesuai role
$menus = [
    ['page' => 'home',     'href' => BASE_URL . 'pages/home.php',    'icon' => 'fa-house',          'label' => 'Home',     'sub' => 'Beranda & Ringkasan'],
    ['page' => 'all_bpkb', 'href' => BASE_URL . 'pages/all_bpkb.php','icon' => 'fa-boxes-stacked',   'label' => 'ALL BPKB', 'sub' => 'Semua Data + Cari'],
    ['page' => 'out_bpkb', 'href' => BASE_URL . 'pages/out_bpkb.php','icon' => 'fa-file-arrow-up',   'label' => 'OUT BPKB', 'sub' => 'Nasabah Lunas'],
];

// Hanya MAF dan MCF yang bisa akses IN BPKB
if (in_array($role, ['MAF', 'MCF'])) {
    array_splice($menus, 1, 0, [[
        'page'  => 'in_bpkb',
        'href'  => BASE_URL . 'pages/in_bpkb.php',
        'icon'  => 'fa-file-arrow-down',
        'label' => 'IN BPKB',
        'sub'   => 'Input BPKB Baru',
    ]]);
}
?>

<style>
    /* Sidebar warna sesuai role */
    .sidebar {
        background: linear-gradient(180deg, #FFCA28 0%, #F57C00 100%) !important;
        border-right: none !important;
        color: #FFFFFF !important;
        box-shadow: 2px 0 10px rgba(245, 124, 0, 0.2);
    }

    <?php if ($role === 'MCF'): ?>
    .sidebar {
        background: linear-gradient(180deg, #1E88E5 0%, #1565C0 100%) !important;
        box-shadow: 2px 0 10px rgba(21, 101, 192, 0.2);
    }
    <?php elseif ($role === 'kacab'): ?>
    .sidebar {
        background: linear-gradient(180deg, #43A047 0%, #2E7D32 100%) !important;
        box-shadow: 2px 0 10px rgba(46, 125, 50, 0.2);
    }
    <?php endif; ?>

    .sidebar-brand { border-bottom: 1px solid rgba(255,255,255,0.2) !important; }
    .brand-icon { color: #FFFFFF !important; font-size: 1.5rem; }
    .brand-title { color: #FFFFFF !important; font-weight: 800; }
    .brand-accent { color: #FFE082 !important; }
    .brand-sub { color: rgba(255,255,255,0.8) !important; }
    .user-avatar { background: #FFFFFF !important; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .user-name { color: #FFFFFF !important; font-weight: 600; }
    .user-role { background-color: rgba(255,255,255,0.2) !important; color: #FFFFFF !important; border: 1px solid rgba(255,255,255,0.4) !important; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; }
    .sidebar-divider { background-color: rgba(255,255,255,0.2) !important; }
    .nav-label { color: rgba(255,255,255,0.7) !important; font-weight: 600; letter-spacing: 1px; }
    .nav-item { color: rgba(255,255,255,0.9) !important; border-radius: 8px !important; transition: all 0.2s ease-in-out; }
    .nav-item:hover { background-color: rgba(255,255,255,0.15) !important; color: #FFFFFF !important; }
    .nav-icon { color: #FFE082 !important; }
    .nav-item.active { background-color: rgba(255,255,255,0.25) !important; color: #FFFFFF !important; font-weight: 700; box-shadow: inset 2px 0 0 #FFFFFF; }
    .nav-item.active .nav-icon { color: #FFFFFF !important; }
    .nav-pip { background-color: #FFFFFF !important; width: 4px !important; border-radius: 4px !important; }
    .nav-desc { color: rgba(255,255,255,0.7) !important; }
    .nav-item:hover .nav-desc, .nav-item.active .nav-desc { color: rgba(255,255,255,0.95) !important; }
    .sidebar-footer { border-top: 1px solid rgba(255,255,255,0.2) !important; background-color: transparent !important; }
    .logout-btn { color: #FFFFFF !important; transition: 0.2s; }
    .logout-btn:hover { background-color: rgba(220,53,69,0.8) !important; border-radius: 6px; }
    .app-version { color: rgba(255,255,255,0.6) !important; }

    <?php if ($role === 'MCF'): ?>
    .user-avatar { color: #1565C0 !important; }
    .nav-icon { color: #BBDEFB !important; }
    <?php elseif ($role === 'kacab'): ?>
    .user-avatar { color: #2E7D32 !important; }
    .nav-icon { color: #C8E6C9 !important; }
    <?php else: ?>
    .user-avatar { color: #F57C00 !important; }
    <?php endif; ?>
</style>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="<?= BASE_URL ?>assets/img/icon-m.png" alt="Logo MACF"
                 style="width:45px;height:45px;object-fit:contain;background:#FFF;padding:4px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
        </div>
        <div class="brand-text">
            <span class="brand-title">BPKB<span class="brand-accent">Sys</span></span>
            <span class="brand-sub">MONITORING SYSTEM</span>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <?= strtoupper(substr(userNama(), 0, 1)) ?>
        </div>
        <div class="user-info">
            <span class="user-name"><?= userNama() ?></span>
            <span class="user-role"><?= roleLabelFull($role) ?></span>
        </div>
    </div>

    <div class="sidebar-divider"></div>

    <nav class="sidebar-nav">
        <span class="nav-label">NAVIGASI</span>
        <ul class="nav-list">
        <?php foreach ($menus as $menu): ?>
            <?php $isActive = ($currentPage === $menu['page']) ? 'active' : ''; ?>
            <li>
                <a href="<?= $menu['href'] ?>" class="nav-item <?= $isActive ?>">
                    <span class="nav-icon"><i class="fa-solid <?= $menu['icon'] ?>"></i></span>
                    <span class="nav-text">
                        <span class="nav-main"><?= $menu['label'] ?></span>
                        <span class="nav-desc"><?= $menu['sub'] ?></span>
                    </span>
                    <?php if ($isActive): ?>
                        <span class="nav-pip"></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    </nav>

    <?php if ($role === 'kacab'): ?>
    <div style="margin: 0 15px 10px; padding: 10px 12px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 11px; color: rgba(255,255,255,0.85);">
        <i class="fa-solid fa-shield-check me-1"></i>
        <strong>Mode Kacab:</strong> Hanya approve OUT BPKB
    </div>
    <?php elseif ($role === 'MAF'): ?>
    <div style="margin: 0 15px 10px; padding: 10px 12px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 11px; color: rgba(255,255,255,0.85);">
        <i class="fa-solid fa-building-columns me-1"></i>
        <strong>Mode MAF:</strong> Input & kelola data MAF
    </div>
    <?php elseif ($role === 'MCF'): ?>
    <div style="margin: 0 15px 10px; padding: 10px 12px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 11px; color: rgba(255,255,255,0.85);">
        <i class="fa-solid fa-landmark me-1"></i>
        <strong>Mode MCF:</strong> Input & kelola data MCF
    </div>
    <?php endif; ?>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>logout.php" class="logout-btn p-2 text-decoration-none d-flex align-items-center gap-2">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Keluar</span>
        </a>
        <span class="app-version">v<?= APP_VERSION ?></span>
    </div>

</aside>
