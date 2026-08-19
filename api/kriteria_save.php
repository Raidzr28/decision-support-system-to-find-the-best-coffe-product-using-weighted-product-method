<?php
// API: update bobot & atribut kriteria
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$id      = (int)($_POST['id'] ?? 0);
$bobot   = (float)($_POST['bobot'] ?? 0);
$atribut = ($_POST['atribut'] ?? 'benefit') === 'cost' ? 'cost' : 'benefit';

if ($id <= 0 || $bobot < 0) {
    echo json_encode(['ok'=>false,'msg'=>'Data tidak valid.']);
    exit;
}

$stmt = $conn->prepare('UPDATE kriteria SET bobot=?, atribut=? WHERE id=?');
$stmt->bind_param('dsi', $bobot, $atribut, $id);
$ok = $stmt->execute();

// total bobot terbaru
$total = $conn->query('SELECT SUM(bobot) t FROM kriteria')->fetch_assoc()['t'];

echo json_encode([
    'ok'=>$ok,
    'msg'=>$ok?'Kriteria berhasil diperbarui.':'Gagal menyimpan.',
    'total_bobot'=> rtrim(rtrim(number_format($total,2,'.',''),'0'),'.'),
]);
