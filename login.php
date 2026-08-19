<?php
require_once __DIR__ . '/config/database.php';
session_start();
if (isset($_SESSION['user_id'])) { header('Location: beranda.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $u = $res->fetch_assoc();
        // verifikasi: hash bcrypt, atau fallback plaintext 'admin123' utk kemudahan demo
        $ok = password_verify($password, $u['password']) ||
              ($username === 'admin' && $password === 'admin123');
        if ($ok) {
            $_SESSION['user_id']      = $u['id'];
            $_SESSION['username']     = $u['username'];
            $_SESSION['nama_lengkap'] = $u['nama_lengkap'];
            header('Location: beranda.php');
            exit;
        }
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — Marako Space</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-bg">
  <div class="auth-card">
    <div class="brand"><span class="b1">Marako</span><span class="b2">Space</span></div>
    <div class="ttl">Selamat Datang</div>
    <div class="desc">SPK Rekomendasi Menu Kopi — Metode Weighted Product</div>

    <?php if ($error): ?><div class="alert alert-err"><?= $error ?></div><?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required autofocus>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn btn-primary">Masuk ke Sistem</button>
    </form>

    <div class="cred-hint">Akun demo &mdash; Username: <b>admin</b> &nbsp;·&nbsp; Password: <b>admin123</b></div>
  </div>
</body>
</html>
