<?php
// config/database.php — Koneksi MySQL XAMPP

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');              // Default XAMPP: kosong
define('DB_NAME', 'db_sistem_bpkb');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;color:red;padding:20px">
        <h3>⚠️ Koneksi Database Gagal</h3>
        <p>' . $conn->connect_error . '</p>
        <p>Pastikan XAMPP sudah berjalan dan database sudah diimport.</p>
    </div>');
}

$conn->set_charset("utf8mb4");
