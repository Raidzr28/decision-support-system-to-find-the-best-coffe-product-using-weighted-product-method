<?php
// =====================================================================
// Konfigurasi Koneksi Database (PHP Native + MySQLi)
// =====================================================================
// Salin file ini ke config/database.php lalu sesuaikan dengan server Anda.
// config/database.php di-gitignore agar kredensial tidak ikut ter-push.

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Laragon default kosong; XAMPP default kosong
define('DB_NAME', 'spk_kopi_wp');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// Zona waktu Indonesia
date_default_timezone_set('Asia/Jakarta');
