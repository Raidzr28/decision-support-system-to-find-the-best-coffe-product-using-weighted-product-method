<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/skala.php';

$kriteria = $conn->query('SELECT * FROM kriteria ORDER BY urutan ASC')->fetch_all(MYSQLI_ASSOC);
$next = $conn->query('SELECT COUNT(*) c FROM alternatif')->fetch_assoc()['c'] + 1;

$page_title = 'Data Alternatif';
$active = 'alternatif';
include __DIR__ . '/includes/header.php';
?>

<div id="alert-box"></div>

<div class="card no-print">
  <h2 id="form-title">Tambah Data Pembelian</h2>
  <div class="sub">Pilih nilai kriteria sesuai skala penilaian. Hover/lihat keterangan pada tiap pilihan.</div>

  <form id="form-alt">
    <input type="hidden" name="id" id="alt-id" value="">
    <div class="form-grid">
      <div class="field">
        <label>Kode Alternatif</label>
        <input type="text" name="kode" id="alt-kode" value="A<?= str_pad($next,3,'0',STR_PAD_LEFT) ?>" required>
      </div>
      <div class="field">
        <label>Nama Menu Kopi</label>
        <input type="text" name="nama_menu" id="alt-menu" placeholder="cth: Cafe Latte" required>
      </div>
      <div class="field">
        <label>Nama Pembeli</label>
        <input type="text" name="nama_pembeli" id="alt-pembeli" placeholder="cth: Budi Santoso" required>
      </div>
      <div class="field">
        <label>Waktu Pembelian</label>
        <input type="datetime-local" name="waktu_pembelian" id="alt-waktu" required>
      </div>
    </div>

    <div class="form-grid" style="margin-top:16px">
      <?php foreach ($kriteria as $i=>$k): $kode=$k['kode']; $s=$SKALA_KRITERIA[$kode]; ?>
      <div class="field">
        <label><?= $kode ?> · <?= htmlspecialchars($k['nama']) ?>
          <span class="tag tag-<?= $k['atribut'] ?>" style="font-size:10.5px"><?= ucfirst($k['atribut']) ?></span>
        </label>
        <select name="c<?= $i+1 ?>" required>
          <?php for($v=1;$v<=5;$v++): ?>
            <option value="<?= $v ?>" <?= $v==3?'selected':'' ?>><?= $v ?> — <?= $s[$v] ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" id="btn-submit"><i class="fa-solid fa-plus"></i> Simpan Data</button>
      <button type="button" class="btn btn-ghost" id="btn-reset" onclick="resetForm()" style="display:none">Batal Edit</button>
    </div>
  </form>
</div>

<div class="card">
  <div class="toolbar">
    <h2 style="margin:0">Daftar Alternatif</h2>
    <div class="spacer"></div>
    <div class="search-box no-print">
      <input type="text" id="search-alt" placeholder="Cari menu / pembeli...">
    </div>
    <button class="btn btn-primary no-print" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak</button>
  </div>

  <?php
    $print_title = 'Data Alternatif — SPK Weighted Product';
    $print_subtitle = 'Dicetak ' . date('d M Y H:i');
    include __DIR__ . '/includes/print_head.php';
  ?>

  <div class="table-scroll">
    <table class="data" id="tbl-alt">
      <thead>
        <tr>
          <th>Kode</th><th>Menu</th><th>Pembeli</th><th>Waktu</th>
          <?php foreach($kriteria as $k): ?><th class="c"><?= $k['kode'] ?></th><?php endforeach; ?>
          <th class="c no-print">Aksi</th>
        </tr>
      </thead>
      <tbody id="alt-body"><!-- diisi via AJAX --></tbody>
    </table>
  </div>

  <?php include __DIR__ . '/includes/print_signature.php'; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
