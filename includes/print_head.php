<?php
/**
 * Kop surat untuk tampilan cetak (dipakai bersama beberapa halaman).
 * Set $print_title & $print_subtitle sebelum include file ini.
 * Sesuaikan data usaha di bawah ini bila diperlukan.
 */
$kop_nama    = 'MARAKO SPACE';
$kop_tagline = 'Coffee &amp; Space';
$kop_alamat  = 'Jl. Caduad Asrama No. 16, RT.01/RW.2, Kab. Bogor, Kec. Cibinong, Kota Bogor, Jawa Barat 16911';
$kop_kontak  = 'Telp/WA: 0812-0000-0000 &nbsp;·&nbsp; Email: info@marakospace.com';
?>
<div class="print-head">
  <div class="kop-surat">
    <div class="kop-logo"><i class="fa-solid fa-mug-saucer"></i></div>
    <div class="kop-text">
      <div class="kop-name"><?= $kop_nama ?></div>
      <div class="kop-desc"><?= $kop_tagline ?> — Sistem Pendukung Keputusan Rekomendasi Menu Kopi</div>
      <div class="kop-addr"><?= $kop_alamat ?> &nbsp;·&nbsp; <?= $kop_kontak ?></div>
    </div>
  </div>
  <div class="kop-line"></div>
  <h3><?= htmlspecialchars($print_title ?? '') ?></h3>
  <p><?= $print_subtitle ?? '' ?></p>
</div>
