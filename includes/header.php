<?php
// Header bersama: topbar + navigasi. Set $active sebelum include.
if (session_status() === PHP_SESSION_NONE) session_start();
$active = $active ?? '';
$nama = $_SESSION['nama_lengkap'] ?? 'Admin';
$inisial = strtoupper(substr($nama, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'SPK Menu Kopi' ?> — Marako Space</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body>
<header class="topbar no-print">
  <div class="topbar-inner">
    <div class="brand"><span class="b1">Marako</span><span class="b2">Space</span></div>
    <nav class="nav">
      <a href="beranda.php"          class="<?= $active==='beranda'?'active':'' ?>">Beranda</a>
      <a href="kriteria.php"         class="<?= $active==='kriteria'?'active':'' ?>">Data Kriteria</a>
      <a href="alternatif.php"       class="<?= $active==='alternatif'?'active':'' ?>">Data Alternatif</a>
      <a href="hitung.php"           class="<?= $active==='hitung'?'active':'' ?>">Hitung WP</a>
      <a href="riwayat.php"          class="<?= $active==='riwayat'?'active':'' ?>">Hasil dan Ranking</a>
    </nav>
    <div class="topbar-right">
      <div class="user-chip"><span class="av"><?= $inisial ?></span><?= htmlspecialchars($nama) ?></div>
      <a href="logout.php" class="btn-logout">Keluar</a>
    </div>
  </div>
</header>
<main class="wrap">
