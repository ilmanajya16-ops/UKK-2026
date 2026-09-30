<?php
include "cek_akses.php";
cek_role(['admin']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu 2</title>
</head>
<body>

    <h1>Menu 2</h1>
    <p>Halaman Menu 2 (bisa diakses: admin)</p>
    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
