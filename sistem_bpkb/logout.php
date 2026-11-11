<?php
require_once 'config/config.php';

// Pastikan session sudah berjalan sebelum dihancurkan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kosongkan semua variabel sesi (seperti user_id, nama, role)
$_SESSION = array();
session_unset();

// 2. Hapus cookie sesi dari browser (Langkah ekstra untuk keamanan)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan sesi sepenuhnya di server
session_destroy();

// 4. Arahkan kembali ke halaman login
// (Opsi: Anda bisa menambahkan ?logout=success di akhir URL jika ingin memunculkan notifikasi di halaman login)
header('Location: ' . BASE_URL . 'login.php');
exit();