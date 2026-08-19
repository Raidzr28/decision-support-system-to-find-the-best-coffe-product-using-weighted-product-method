<?php
/**
 * Blok tanda tangan penanggung jawab/pemilik untuk tampilan cetak.
 * Set $print_tempat & $print_role sebelum include bila ingin override default.
 */
$sign_tempat = $print_tempat ?? 'Cibinong';
$sign_role   = $print_role ?? 'Penanggung Jawab / Pemilik';
?>
<div class="print-sign">
  <div class="sign-col">
    <div class="sign-date"><?= htmlspecialchars($sign_tempat) ?>, <?= date('d M Y') ?></div>
    <div class="sign-role"><?= htmlspecialchars($sign_role) ?></div>
    <div class="sign-space"></div>
    <div class="sign-name">( Farhan Albariq )</div>
  </div>
</div>
