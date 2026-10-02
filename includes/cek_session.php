<?php
// includes/cek_session.php

session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    $_SESSION['pesan_error'] = "Silakan login terlebih dahulu!";
    header("Location: login.php");
    exit;
}
?>