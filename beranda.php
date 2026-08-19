<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$total_alt   = $conn->query('SELECT COUNT(*) c FROM alternatif')->fetch_assoc()['c'];
$total_krit  = $conn->query('SELECT COUNT(*) c FROM kriteria')->fetch_assoc()['c'];
$total_riw   = $conn->query('SELECT COUNT(*) c FROM riwayat')->fetch_assoc()['c'];
$total_menu  = $conn->query('SELECT COUNT(DISTINCT nama_menu) c FROM alternatif')->fetch_assoc()['c'];

$last = $conn->query('SELECT * FROM riwayat ORDER BY id DESC LIMIT 1')->fetch_assoc();
$recent = $conn->query('SELECT * FROM alternatif ORDER BY id DESC LIMIT 6');

$page_title = 'Beranda';
$active = 'beranda';
include __DIR__ . '/includes/header.php';
?>

<div class="hero-panel">
  <h2>Where every cup tells a story</h2>
  <p>Sistem Pendukung Keputusan rekomendasi menu kopi favorit di Cafe Marako Space menggunakan metode <b>Weighted Product</b> dengan 8 kriteria penilaian.</p>
  <div class="hero-actions">
    <a href="hitung.php" class="btn btn-ghost"><i class="fa-solid fa-mug-hot"></i> Mulai Hitung Rekomendasi</a>
    <a href="alternatif.php" class="btn btn-ghost"><i class="fa-solid fa-plus"></i> Input Data Pembelian</a>
  </div>
</div>

<div class="stat-grid">
  <div class="stat accent">
    <div class="ic"><i class="fa-solid fa-clipboard-list"></i></div>
    <div class="num"><?= $total_alt ?></div>
    <div class="lbl">Data Alternatif (Pembelian)</div>
  </div>
  <div class="stat">
    <div class="ic"><i class="fa-solid fa-scale-balanced"></i></div>
    <div class="num"><?= $total_krit ?></div>
    <div class="lbl">Kriteria Penilaian</div>
  </div>
  <div class="stat">
    <div class="ic"><i class="fa-solid fa-mug-saucer"></i></div>
    <div class="num"><?= $total_menu ?></div>
    <div class="lbl">Variasi Menu Kopi</div>
  </div>
  <div class="stat">
    <div class="ic"><i class="fa-solid fa-clock-rotate-left"></i></div>
    <div class="num"><?= $total_riw ?></div>
    <div class="lbl">Riwayat Perhitungan</div>
  </div>
</div>

<?php if ($last): ?>
<div class="card">
  <h2>Rekomendasi Terakhir</h2>
  <div class="sub">Hasil perhitungan WP yang terakhir disimpan</div>
  <div class="best-banner" style="margin-bottom:0">
    <div class="crown"><i class="fa-solid fa-crown"></i></div>
    <div>
      <div class="ttl">Menu Kopi Favorit</div>
      <div class="menu"><?= htmlspecialchars($last['menu_terbaik']) ?></div>
    </div>
    <div class="val">
      <div class="v"><?= number_format($last['nilai_tertinggi'], 5) ?></div>
      <div class="vl">Nilai Vektor V tertinggi · <?= date('d M Y H:i', strtotime($last['created_at'])) ?></div>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <h2>Pembelian Terbaru</h2>
  <div class="sub">6 data alternatif terakhir yang diinput</div>
  <div class="table-scroll">
    <table class="data">
      <thead><tr><th>Kode</th><th>Menu Kopi</th><th>Nama Pembeli</th><th>Waktu Pembelian</th></tr></thead>
      <tbody>
      <?php while($r = $recent->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($r['kode']) ?></td>
          <td><b><?= htmlspecialchars($r['nama_menu']) ?></b></td>
          <td><?= htmlspecialchars($r['nama_pembeli']) ?></td>
          <td><?= date('d M Y · H:i', strtotime($r['waktu_pembelian'])) ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
