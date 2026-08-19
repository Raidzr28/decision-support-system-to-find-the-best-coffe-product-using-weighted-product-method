<?php
session_start();
header('Location: ' . (isset($_SESSION['user_id']) ? 'beranda.php' : 'login.php'));
exit;
