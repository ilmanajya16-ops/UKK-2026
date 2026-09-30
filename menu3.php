<?php
include "cek_akses.php";
cek_role(['admin', 'guru']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu 3</title>
</head>
<body>

    <h1>Menu 3</h1>
    <p>Halaman Menu 3 (bisa diakses: admin dan guru)</p>
    <p><a href="dashboard.php">Kembali ke Dashboard</a></p>

</body>
</html>
