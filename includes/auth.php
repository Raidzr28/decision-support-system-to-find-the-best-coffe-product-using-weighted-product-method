<?php
// =====================================================================
// Pengaman Halaman (cek login). Sertakan di setiap halaman privat.
// =====================================================================
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
