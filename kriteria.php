<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/skala.php';

$kriteria = $conn->query('SELECT * FROM kriteria ORDER BY urutan ASC')->fetch_all(MYSQLI_ASSOC);
$total_bobot = array_sum(array_column($kriteria, 'bobot'));

$page_title = 'Input Kriteria';
$active = 'kriteria';
include __DIR__ . '/includes/header.php';
?>

<div id="alert-box"></div>

<div class="card">
  <div class="toolbar">
    <div>
      <h2 style="margin-bottom:2px">Daftar Kriteria</h2>
      <span class="muted" style="font-size:13.5px">Total bobot saat ini: <b id="total-bobot"><?= rtrim(rtrim(number_format($total_bobot,2,'.',''),'0'),'.') ?></b></span>
    </div>
    <div class="spacer"></div>
    <button class="btn btn-primary no-print" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak</button>
  </div>

  <?php
    $print_title = 'Daftar Kriteria — SPK Weighted Product';
    $print_subtitle = 'Dicetak ' . date('d M Y H:i');
    include __DIR__ . '/includes/print_head.php';
  ?>

  <div class="table-scroll">
    <table class="data" id="tbl-kriteria">
      <thead>
        <tr>
          <th>Kode</th><th>Nama Kriteria</th><th style="width:140px">Bobot (W)</th>
          <th style="width:170px">Atribut</th><th class="c no-print" style="width:120px">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($kriteria as $k): ?>
        <tr data-id="<?= $k['id'] ?>">
          <td><b><?= htmlspecialchars($k['kode']) ?></b></td>
          <td><?= htmlspecialchars($k['nama']) ?></td>
          <td>
            <input type="number" step="0.01" min="0" class="in-bobot" value="<?= rtrim(rtrim(number_format($k['bobot'],2,'.',''),'0'),'.') ?>" style="max-width:110px">
          </td>
          <td>
            <select class="in-atribut">
              <option value="benefit" <?= $k['atribut']==='benefit'?'selected':'' ?>>Benefit</option>
              <option value="cost"    <?= $k['atribut']==='cost'?'selected':'' ?>>Cost</option>
            </select>
          </td>
          <td class="c no-print">
            <button class="btn btn-primary btn-sm" onclick="simpanKriteria(this)">Simpan</button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php include __DIR__ . '/includes/print_signature.php'; ?>
</div>

<div class="card no-print">
  <h2>Referensi Skala Penilaian (1–5)</h2>
  <div class="sub">Panduan nilai tiap kriteria sesuai Tabel 3.2 – 3.9 BAB III</div>
  <div class="table-scroll">
    <table class="data">
      <thead><tr><th>Kode</th><th>Kriteria</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th></tr></thead>
      <tbody>
      <?php foreach ($SKALA_KRITERIA as $kode=>$s): ?>
        <tr>
          <td><b><?= $kode ?></b></td>
          <td><?= $s['nama'] ?> <span class="tag tag-<?= strtolower($s['tipe']) ?>"><?= $s['tipe'] ?></span></td>
          <td><?= $s[1] ?></td><td><?= $s[2] ?></td><td><?= $s[3] ?></td><td><?= $s[4] ?></td><td><?= $s[5] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
