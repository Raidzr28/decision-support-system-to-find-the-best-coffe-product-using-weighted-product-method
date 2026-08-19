<?php
// API: CRUD data alternatif (list, save, get, delete)
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    $rows = $conn->query('SELECT * FROM alternatif ORDER BY id ASC')->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['ok'=>true,'data'=>$rows]);
    exit;
}

if ($action === 'get') {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare('SELECT * FROM alternatif WHERE id=?');
    $stmt->bind_param('i',$id); $stmt->execute();
    echo json_encode(['ok'=>true,'data'=>$stmt->get_result()->fetch_assoc()]);
    exit;
}

if ($action === 'save') {
    $id      = (int)($_POST['id'] ?? 0);
    $kode    = trim($_POST['kode'] ?? '');
    $menu    = trim($_POST['nama_menu'] ?? '');
    $pembeli = trim($_POST['nama_pembeli'] ?? '');
    $waktu   = str_replace('T',' ',($_POST['waktu_pembelian'] ?? ''));
    if (strlen($waktu) === 16) $waktu .= ':00';

    $c = [];
    for ($i=1;$i<=8;$i++){ $c[$i] = max(1,min(5,(int)($_POST["c$i"] ?? 3))); }

    if ($kode==='' || $menu==='' || $pembeli==='' || $waktu==='') {
        echo json_encode(['ok'=>false,'msg'=>'Semua field wajib diisi.']); exit;
    }

    if ($id > 0) {
        $stmt = $conn->prepare('UPDATE alternatif SET kode=?,nama_menu=?,nama_pembeli=?,waktu_pembelian=?,c1=?,c2=?,c3=?,c4=?,c5=?,c6=?,c7=?,c8=? WHERE id=?');
        $stmt->bind_param('ssssiiiiiiiii',$kode,$menu,$pembeli,$waktu,$c[1],$c[2],$c[3],$c[4],$c[5],$c[6],$c[7],$c[8],$id);
        $msg = 'Data alternatif berhasil diperbarui.';
    } else {
        $stmt = $conn->prepare('INSERT INTO alternatif (kode,nama_menu,nama_pembeli,waktu_pembelian,c1,c2,c3,c4,c5,c6,c7,c8) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('ssssiiiiiiii',$kode,$menu,$pembeli,$waktu,$c[1],$c[2],$c[3],$c[4],$c[5],$c[6],$c[7],$c[8]);
        $msg = 'Data alternatif berhasil ditambahkan.';
    }
    $ok = $stmt->execute();
    echo json_encode(['ok'=>$ok,'msg'=>$ok?$msg:'Gagal menyimpan data.']);
    exit;
}

if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $conn->prepare('DELETE FROM alternatif WHERE id=?');
    $stmt->bind_param('i',$id);
    $ok = $stmt->execute();
    echo json_encode(['ok'=>$ok,'msg'=>$ok?'Data dihapus.':'Gagal menghapus.']);
    exit;
}

echo json_encode(['ok'=>false,'msg'=>'Aksi tidak dikenal.']);
