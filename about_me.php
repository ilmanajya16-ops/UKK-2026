<?php
session_start();
include "config/koneksi.php";


$nama = "Ilma Najya";
$kelas = "XII RPL";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Masak,Menyanyi";
$cita_cita = "ASN";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Saya</title>
</head>
<body>

    <h2>Tentang Saya</h2>

    <p>Nama: <?php echo $nama; ?></p>
    <p>Kelas: <?php echo $kelas; ?></p>
    <p>Sekolah: <?php echo $sekolah; ?></p>
    <p>Jurusan: <?php echo $jurusan; ?></p>
    <p>Hobi: <?php echo $hobi; ?></p>
    <p>Cita-cita: <?php echo $cita_cita; ?></p>

    <h3>Deskripsi Diri</h3>
    <p>
        Saya adalah seorang pelajar yang sedang belajar
        Rekayasa Perangkat Lunak. Saya senang mempelajari
        teknologi dan membuat website. Saya ingin terus
        belajar agar bisa menjadi Web Developer.
    </p>

    <a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>