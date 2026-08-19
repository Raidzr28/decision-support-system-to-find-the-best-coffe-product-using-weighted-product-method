<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$riw = $conn->query('SELECT * FROM riwayat ORDER BY id DESC LIMIT 1')->fetch_assoc();

$page_title = 'Hasil & Ranking';
$active = 'hasil';
include __DIR__ . '/includes/header.php';
?>

<?php if (!$riw): ?>
  <div class="card">
    <div class="empty">
      <div class="ic"><i class="fa-solid fa-ranking-star"></i></div>
      Belum ada hasil perhitungan.<br>Lakukan proses di menu <a href="hitung.php">Hitung WP</a> terlebih dahulu.
    </div>
  </div>
<?php else:
  $detail   = json_decode($riw['detail_json'], true);
  $ranking  = $detail['ranking'];
  $top3     = array_slice($ranking, 0, 3);
  $best     = $ranking[0];

  $print_title = 'Hasil Akhir & Ranking Top 3 — Weighted Product';
  $print_subtitle = htmlspecialchars($riw['kode_proses']) . ' · ' . $riw['total_alternatif'] . ' alternatif · Dicetak ' . date('d M Y H:i');
  include __DIR__ . '/includes/print_head.php';
?>

  <div class="best-banner">
    <div class="crown"><i class="fa-solid fa-crown"></i></div>
    <div>
      <div class="ttl">Rekomendasi Menu Kopi Favorit</div>
      <div class="menu"><?= htmlspecialchars($best['menu']) ?></div>
      <div style="opacity:.9;font-size:13px;margin-top:2px"><?= htmlspecialchars($best['kode']) ?> · Pembeli: <?= htmlspecialchars($best['pembeli']) ?></div>
    </div>
    <div class="val">
      <div class="v"><?= number_format($best['v'],6) ?></div>
      <div class="vl">Nilai Vektor V tertinggi</div>
    </div>
  </div>

  <div class="toolbar no-print">
    <span class="muted"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($riw['kode_proses']) ?> · <?= date('d M Y · H:i', strtotime($riw['created_at'])) ?></span>
    <div class="spacer"></div>
    <a href="hitung.php" class="btn btn-ghost btn-sm"><i class="fa-solid fa-calculator"></i> Lihat Contoh Perhitungan</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak Hasil</button>
  </div>

  <!-- Podium Top 3 -->
  <div class="card">
    <h2>Top 3 Rekomendasi Menu Kopi</h2>
    <div class="sub">Peringkat berdasarkan nilai Vektor V tertinggi dari seluruh <?= $riw['total_alternatif'] ?> data alternatif.</div>
    <div class="podium">
      <?php foreach($top3 as $t): ?>
        <div class="podium-card rank-<?= $t['ranking'] ?>">
          <div class="podium-badge"><?= $t['ranking'] ?></div>
          <div class="podium-menu"><?= htmlspecialchars($t['menu']) ?></div>
          <div class="podium-kode"><?= htmlspecialchars($t['kode']) ?> · <?= htmlspecialchars($t['pembeli']) ?></div>
          <div class="podium-v"><?= number_format($t['v'],6) ?></div>
          <div class="podium-lbl">Nilai V</div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Tabel ranking lengkap -->
  <div class="card">
    <h2>Hasil Akhir Perangkingan (Seluruh Data)</h2>
    <div class="sub">Vi = Si / ΣSi &nbsp;·&nbsp; Diurutkan dari nilai tertinggi sebagai rekomendasi terbaik.</div>
    <div class="table-scroll">
      <table class="data">
        <thead>
          <tr><th class="c">Rank</th><th>Kode</th><th>Menu Kopi</th><th>Pembeli</th>
          <th class="r">Nilai S</th><th class="r">Nilai V</th><th class="c">Status</th></tr>
        </thead>
        <tbody>
        <?php foreach($ranking as $h): ?>
          <tr>
            <td class="c"><span class="rank-badge rank-<?= $h['ranking']<=3?$h['ranking']:'' ?>"><?= $h['ranking'] ?></span></td>
            <td><b><?= htmlspecialchars($h['kode']) ?></b></td>
            <td><?= htmlspecialchars($h['menu']) ?></td>
            <td><?= htmlspecialchars($h['pembeli']) ?></td>
            <td class="r"><?= number_format($h['s'],6) ?></td>
            <td class="r"><b><?= number_format($h['v'],6) ?></b></td>
            <td class="c"><?= $h['ranking']===1 ? '<span class="tag tag-best">Terbaik</span>' : '' ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php include __DIR__ . '/includes/print_signature.php'; ?>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
