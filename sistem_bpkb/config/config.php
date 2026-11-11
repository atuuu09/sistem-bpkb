<?php
// config/config.php — Konfigurasi Umum Aplikasi

define('APP_NAME',    'Sistem Monitoring BPKB');
define('APP_VERSION', '1.0.0');
define('BASE_URL',    'http://localhost/sistem_bpkb/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/bpkb/');

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Auth helpers ─────────────────────────────────────────────────────────────

/** Redirect ke login jika belum login */
function cekLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

/** Cek akses khusus role */
function cekRole(array $roles): void {
    cekLogin();
    if (!in_array($_SESSION['role'] ?? '', $roles)) {
        header('Location: ' . BASE_URL . 'pages/home.php?akses=ditolak');
        exit();
    }
}

/** Cek apakah role bisa input BPKB */
function bisaInput(): bool {
    return in_array($_SESSION['role'] ?? '', ['MAF', 'MCF']);
}

/** Cek apakah role bisa acc / approve OUT BPKB */
function bisaAcc(): bool {
    return ($_SESSION['role'] ?? '') === 'kacab';
}

/** Ambil info user dari session */
function userNama(): string  { return $_SESSION['nama']  ?? '-'; }
function userRole(): string  { return $_SESSION['role']  ?? '-'; }
function userId(): int       { return (int)($_SESSION['user_id'] ?? 0); }

// ── Sanitasi & format ────────────────────────────────────────────────────────

function sanitize(string $str): string {
    return htmlspecialchars(strip_tags(trim($str)));
}

function formatTanggal(?string $tgl): string {
    if (!$tgl) return '-';
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    [$y, $m, $d] = explode('-', $tgl);
    return "$d {$bulan[(int)$m]} $y";
}

// ── Badge helpers ────────────────────────────────────────────────────────────

function badgeStatus(string $s): string {
    return match($s) {
        'aktif'   => '<span class="badge badge-aktif">Aktif</span>',
        'lunas'   => '<span class="badge badge-lunas">Lunas</span>',
        'diambil' => '<span class="badge badge-diambil">Diambil</span>',
        default   => '<span class="badge badge-secondary">' . $s . '</span>',
    };
}

function badgePerusahaan(string $p): string {
    return match($p) {
        'MAF' => '<span class="badge badge-maf">MAF</span>',
        'MCF' => '<span class="badge badge-mcf">MCF</span>',
        default => $p,
    };
}

function badgeKendaraan(string $k): string {
    $icon = $k === 'Motor' ? '🏍️' : '🚗';
    return "<span class=\"badge badge-kendaraan\">$icon $k</span>";
}

function roleLabelFull(string $role): string {
    return match($role) {
        'MAF'   => 'Admin MAF',
        'MCF'   => 'Admin MCF',
        'kacab' => 'Ketua Cabang',
        default => $role,
    };
}
