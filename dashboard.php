<?php
include 'includes/cek_session.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Dashbord - Sistem Pelanggaran Siswa</title>
    </head>
    <body>
        <h1>Selamat datang, <?php echo $_SESSION['nama']; ?></h1>
        <p>Anda login sebagai: <?php echo $_SESSION['role']; ?></p>

        <ul>
            <?php if ($_SESSION['role'] == 'admin') { ?>
            <li><a href="kelola_guru.php">Kelola Guru</a></li>
            <li><a href="kelola_siswa.php">Kelola Siswa</a></li>
            <li><a href="menu3.php">Menu 3</a></li>
            <li><a href="menu4.php">Menu 4</a></li>
        <?php } ?>

        <?php if($_SESSION['role'] == 'guru') { ?>
        <li><a href="menu3.php">Menu 3</a></li>
        <li><a href="menu4.php">Menu 4</a></li>
        <?php } ?>
        </ul>
        
        <a href="logout.php">Logout</a>
    </body>
</html>