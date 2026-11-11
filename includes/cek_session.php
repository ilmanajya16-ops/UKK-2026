
<?php
// includes/cek_session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['login']) ||
    $_SESSION['login'] !== true ||
    !isset($_SESSION['id'])
) {
    $_SESSION['pesan_error'] = "Sesi login tidak ditemukan. Silakan login kembali!";
    header("Location: login.php");
    exit;
}
?>
