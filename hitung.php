<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/wp_function.php';

$kriteria   = $conn->query('SELECT * FROM kriteria ORDER BY urutan ASC')->fetch_all(MYSQLI_ASSOC);
$alternatif = $conn->query('SELECT * FROM alternatif ORDER BY id ASC')->fetch_all(MYSQLI_ASSOC);

$proses = false; $R = null; $saved = false;
if (isset($_GET['proses']) && count($alternatif) > 0) {
    $R = hitung_weighted_product($kriteria, $alternatif);
    $proses = true;

    // Simpan ke riwayat
    $best = $R['hasil'][0];
    $kode_proses = 'WP-' . date('YmdHis');
    $detail = json_encode([
        'bobot_normal'=>$R['bobot_normal'],
        'total_s'=>$R['total_s'],
        'ranking'=>array_map(function($h){
            return ['ranking'=>$h['ranking'],'kode'=>$h['kode'],'menu'=>$h['nama_menu'],
                    'pembeli'=>$h['nama_pembeli'],'s'=>$h['s'],'v'=>$h['v']];
        }, $R['hasil']),
    ]);
    $tot = count($alternatif);
    $stmt = $conn->prepare('INSERT INTO riwayat (kode_proses,total_alternatif,menu_terbaik,nilai_tertinggi,detail_json) VALUES (?,?,?,?,?)');
    $stmt->bind_param('sisds',$kode_proses,$tot,$best['nama_menu'],$best['v'],$detail);
    $stmt->execute();
    $saved = true;
}

$page_title = 'Hitung WP';
$active = 'hitung';
include __DIR__ . '/includes/header.php';
?>

<?php if (!$proses): ?>
  <div class="card">
    <div class="formula">
      <b>Tahapan:</b>
      ① Normalisasi bobot <code>Wj = wj / Σwj</code> &nbsp;
      ② Vektor S <code>Si = Π (Xij ^ Wj)</code> (cost = pangkat negatif) &nbsp;
      ③ Vektor V <code>Vi = Si / ΣSi</code> &nbsp;
      ④ Perangkingan (terbesar → terkecil)
    </div>
    <p class="muted" style="margin-bottom:18px">Total data alternatif siap dihitung: <b><?= count($alternatif) ?></b> · Jumlah kriteria: <b><?= count($kriteria) ?></b></p>
    <?php if (count($alternatif) === 0): ?>
      <div class="alert alert-err">Belum ada data alternatif. Silakan input data terlebih dahulu di menu Data Alternatif.</div>
    <?php else: ?>
      <a href="hitung.php?proses=1" class="btn btn-primary"><i class="fa-solid fa-gear"></i> Proses Perhitungan WP</a>
    <?php endif; ?>
  </div>
<?php else: ?>

  <?php
    $print_title = 'Contoh Perhitungan Manual Weighted Product — 3 Data Sampel';
    $print_subtitle = count($alternatif) . ' alternatif diproses · Dicetak ' . date('d M Y H:i');
    include __DIR__ . '/includes/print_head.php';

    // Ambil 3 data sampel pertama (urutan input) untuk didemonstrasikan manual
    $sample_ids = array_slice(array_column($alternatif, 'id'), 0, 3);
    $sample = array_values(array_filter($R['hasil'], function($h) use ($sample_ids) {
        return in_array($h['id'], $sample_ids);
    }));
    usort($sample, function($a,$b) use ($sample_ids) {
        return array_search($a['id'], $sample_ids) <=> array_search($b['id'], $sample_ids);
    });
  ?>

  <div class="alert alert-ok">
    <i class="fa-solid fa-check"></i> Perhitungan seluruh <?= count($alternatif) ?> data alternatif berhasil diproses &amp; tersimpan otomatis ke Riwayat.
    Halaman ini menampilkan <b>contoh perhitungan manual 3 data sampel</b> agar proses tiap tahap dapat ditelusuri.
    Lihat <a href="hasil.php">Hasil &amp; Ranking Top 3 lengkap →</a>
  </div>

  <div class="toolbar no-print">
    <span class="muted"><i class="fa-solid fa-flask"></i> Contoh perhitungan manual · 3 data sampel · 3 tahap</span>
    <div class="spacer"></div>
    <a href="hitung.php" class="btn btn-ghost btn-sm"><i class="fa-solid fa-rotate-left"></i> Hitung Ulang</a>
    <a href="hasil.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-ranking-star"></i> Lihat Hasil Top 3</a>
  </div>

  <div class="card">
    <h2>Data Sampel yang Digunakan</h2>
    <div class="sub">Diambil dari 3 data alternatif pertama, dari total <?= count($alternatif) ?> data yang diproses.</div>
    <div class="table-scroll">
      <table class="data">
        <thead><tr><th>Kode</th><th>Menu Kopi</th><th>Pembeli</th>
        <?php foreach($kriteria as $k): ?><th class="c"><?= $k['kode'] ?></th><?php endforeach; ?></tr></thead>
        <tbody>
        <?php foreach($sample as $s): ?>
          <tr>
            <td><b><?= $s['kode'] ?></b></td>
            <td><?= htmlspecialchars($s['nama_menu']) ?></td>
            <td><?= htmlspecialchars($s['nama_pembeli']) ?></td>
            <?php foreach($kriteria as $i=>$k): $f='c'.($i+1); ?>
              <td class="c"><?= $s['nilai'][$f] ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tahap 1: Normalisasi bobot -->
  <div class="card">
    <h2>Tahap 1 — Normalisasi Bobot Kriteria</h2>
    <div class="sub">Rumus: Wj = wj / Σwj &nbsp;·&nbsp; Total bobot awal Σwj = <?= rtrim(rtrim(number_format($R['total_bobot'],2,'.',''),'0'),'.') ?></div>
    <div class="table-scroll">
      <table class="data">
        <thead><tr><th>Kode</th><th>Kriteria</th><th>Atribut</th><th class="r">Bobot (wj)</th><th class="r">Bobot Normal (Wj)</th></tr></thead>
        <tbody>
        <?php foreach($kriteria as $k): ?>
          <tr>
            <td><b><?= $k['kode'] ?></b></td>
            <td><?= htmlspecialchars($k['nama']) ?></td>
            <td><span class="tag tag-<?= $k['atribut'] ?>"><?= ucfirst($k['atribut']) ?></span></td>
            <td class="r"><?= rtrim(rtrim(number_format($k['bobot'],2,'.',''),'0'),'.') ?></td>
            <td class="r"><b><?= number_format($R['bobot_normal'][$k['kode']],4) ?></b></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tahap 2: Vektor S untuk 3 sampel, dengan rincian per kriteria -->
  <div class="card">
    <h2>Tahap 2 — Perhitungan Vektor S (3 Data Sampel)</h2>
    <div class="sub">Si = Π (Xij ^ ±Wj). Kriteria Cost menggunakan pangkat negatif (−Wj), Benefit pangkat positif (+Wj).</div>
    <?php foreach($sample as $s): ?>
      <div class="table-scroll" style="margin-bottom:16px">
        <table class="data">
          <caption style="text-align:left;padding:6px 2px;font-weight:700"><?= $s['kode'] ?> — <?= htmlspecialchars($s['nama_menu']) ?> (<?= htmlspecialchars($s['nama_pembeli']) ?>)</caption>
          <thead>
            <tr><th>Kriteria</th><th class="r">Xij</th><th class="r">Wj</th><th class="r">Pangkat</th><th class="r">Xij ^ Pangkat</th></tr>
          </thead>
          <tbody>
          <?php foreach($kriteria as $k): $d = $s['detail'][$k['kode']]; ?>
            <tr>
              <td><?= $k['kode'] ?> · <?= htmlspecialchars($k['nama']) ?></td>
              <td class="r"><?= $d['x'] ?></td>
              <td class="r"><?= number_format($d['wj'],4) ?></td>
              <td class="r"><?= number_format($d['pangkat'],4) ?></td>
              <td class="r"><?= number_format($d['faktor'],6) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr><td colspan="4" class="r"><b>Si = Π faktor = </b></td><td class="r"><b><?= number_format($s['s'],6) ?></b></td></tr>
          </tfoot>
        </table>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Tahap 3: Vektor V untuk 3 sampel -->
  <div class="card">
    <h2>Tahap 3 — Perhitungan Vektor V (3 Data Sampel)</h2>
    <div class="sub">Vi = Si / ΣSi &nbsp;·&nbsp; ΣSi dihitung dari seluruh <?= count($alternatif) ?> data alternatif = <?= number_format($R['total_s'],6) ?></div>
    <div class="table-scroll">
      <table class="data">
        <thead><tr><th>Kode</th><th>Menu Kopi</th><th class="r">Si</th><th class="r">ΣSi</th><th class="r">Vi = Si / ΣSi</th></tr></thead>
        <tbody>
        <?php foreach($sample as $s): ?>
          <tr>
            <td><b><?= $s['kode'] ?></b></td>
            <td><?= htmlspecialchars($s['nama_menu']) ?></td>
            <td class="r"><?= number_format($s['s'],6) ?></td>
            <td class="r"><?= number_format($R['total_s'],6) ?></td>
            <td class="r"><b><?= number_format($s['v'],6) ?></b></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card" style="text-align:center">
    <p class="muted" style="margin-bottom:14px">Perangkingan akhir dihitung dari seluruh <?= count($alternatif) ?> data alternatif (bukan hanya 3 sampel di atas).</p>
    <a href="hasil.php" class="btn btn-primary"><i class="fa-solid fa-ranking-star"></i> Lihat Hasil &amp; Ranking Top 3</a>
  </div>

  <?php include __DIR__ . '/includes/print_signature.php'; ?>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
