<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM riwayat WHERE id=?');
$stmt->bind_param('i',$id); $stmt->execute();
$riw = $stmt->get_result()->fetch_assoc();
if (!$riw) { header('Location: riwayat.php'); exit; }
$detail = json_decode($riw['detail_json'], true);

$page_title = 'Detail Riwayat';
$active = 'riwayat';
include __DIR__ . '/includes/header.php';
?>

<div class="toolbar no-print">
  <a href="riwayat.php" class="btn btn-ghost btn-sm"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  <div class="spacer"></div>
  <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak Detail</button>
</div>

<?php
  $print_title = 'Detail Hasil Perhitungan WP';
  $print_subtitle = htmlspecialchars($riw['kode_proses']) . ' · Dicetak ' . date('d M Y H:i');
  include __DIR__ . '/includes/print_head.php';
?>

<div class="best-banner">
  <div class="crown"><i class="fa-solid fa-crown"></i></div>
  <div>
    <div class="ttl">Menu Kopi Favorit</div>
    <div class="menu"><?= htmlspecialchars($riw['menu_terbaik']) ?></div>
    <div style="opacity:.9;font-size:13px;margin-top:2px"><?= htmlspecialchars($riw['kode_proses']) ?> · <?= date('d M Y H:i', strtotime($riw['created_at'])) ?></div>
  </div>
  <div class="val">
    <div class="v"><?= number_format($riw['nilai_tertinggi'],6) ?></div>
    <div class="vl">Nilai Vektor V tertinggi</div>
  </div>
</div>

<div class="card">
  <h2>Hasil Perangkingan</h2>
  <div class="sub"><?= $riw['total_alternatif'] ?> alternatif · Σ S = <?= number_format($detail['total_s'],6) ?></div>
  <div class="table-scroll">
    <table class="data">
      <thead><tr><th class="c">Rank</th><th>Kode</th><th>Menu Kopi</th><th>Pembeli</th><th class="r">Nilai S</th><th class="r">Nilai V</th></tr></thead>
      <tbody>
      <?php foreach($detail['ranking'] as $h): ?>
        <tr>
          <td class="c"><span class="rank-badge rank-<?= $h['ranking']<=3?$h['ranking']:'' ?>"><?= $h['ranking'] ?></span></td>
          <td><b><?= htmlspecialchars($h['kode']) ?></b></td>
          <td><?= htmlspecialchars($h['menu']) ?></td>
          <td><?= htmlspecialchars($h['pembeli']) ?></td>
          <td class="r"><?= number_format($h['s'],6) ?></td>
          <td class="r"><b><?= number_format($h['v'],6) ?></b></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/print_signature.php'; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
