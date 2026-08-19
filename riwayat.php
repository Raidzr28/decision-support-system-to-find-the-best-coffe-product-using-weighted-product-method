<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (isset($_GET['hapus'])) {
    $id=(int)$_GET['hapus'];
    $conn->query("DELETE FROM riwayat WHERE id=$id");
    header('Location: riwayat.php'); exit;
}

$rows = $conn->query('SELECT * FROM riwayat ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);

$page_title = 'Riwayat';
$active = 'riwayat';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
  <div class="toolbar">
    <h2 style="margin:0">Histori Proses</h2>
    <div class="spacer"></div>
    <button class="btn btn-primary no-print" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak</button>
  </div>

  <?php
    $print_title = 'Riwayat Perhitungan WP — SPK Menu Kopi';
    $print_subtitle = 'Dicetak ' . date('d M Y H:i');
    include __DIR__ . '/includes/print_head.php';
  ?>

  <?php if (count($rows)===0): ?>
    <div class="empty"><div class="ic"><i class="fa-solid fa-clock-rotate-left"></i></div>Belum ada riwayat perhitungan.<br>Lakukan perhitungan di menu <b>Hitung WP</b>.</div>
  <?php else: ?>
  <div class="table-scroll">
    <table class="data">
      <thead>
        <tr><th>Kode Proses</th><th>Tanggal</th><th class="c">Jml Alternatif</th>
        <th>Menu Terbaik</th><th class="r">Nilai V</th><th class="c no-print">Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><b><?= htmlspecialchars($r['kode_proses']) ?></b></td>
          <td><?= date('d M Y · H:i', strtotime($r['created_at'])) ?></td>
          <td class="c"><?= $r['total_alternatif'] ?></td>
          <td><i class="fa-solid fa-crown"></i> <?= htmlspecialchars($r['menu_terbaik']) ?></td>
          <td class="r"><b><?= number_format($r['nilai_tertinggi'],6) ?></b></td>
          <td class="c no-print">
            <a href="riwayat_detail.php?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm">Detail</a>
            <a href="riwayat.php?hapus=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus riwayat ini?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <?php include __DIR__ . '/includes/print_signature.php'; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
