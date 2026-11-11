<?php
require_once 'config/config.php';

// Pastikan session sudah berjalan (berjaga-jaga jika di config.php belum ada session_start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gunakan BASE_URL agar arah halamannya selalu akurat mutlak
if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
} else {
    header('Location: ' . BASE_URL . 'pages/home.php');
}
exit();