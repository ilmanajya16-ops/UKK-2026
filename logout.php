<?php
session_start();

// Menghapus semua data session
$_SESSION = array();

// Menghapus session
session_destroy();

// Kembali ke halaman login
header("Location: login.php");
exit;
?>