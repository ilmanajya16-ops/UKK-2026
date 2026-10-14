<?php

include "config/koneksi.php";

$data = mysqli_query($koneksi, "
    SELECT 
        ps.id,
        s.nis,
        s.nama,
        pk.nama AS pelanggaran
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON ps.siswa_id = s.id
    JOIN t_pelanggaran_kategori pk ON ps.pelanggaran_id = pk.id
    ORDER BY ps.id DESC
");

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Kelola Siswa</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php
            include 'layout/sidebar.php';
        ?>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
          
            <div class="row"></div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>



<h2>Laporan Pelanggaran Siswa</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<br><br>



<a href="cetak_laporan.php" target="_blank">
    Cetak Laporan
</a>

&nbsp;

<a href="export_laporan.php">
    Export Excel
</a>

<br><br>

 <table class="table">
  <thead class="thead-dark">

<tr>
    <th>No</th>
    <th>NIS</th>
    <th>Nama Siswa</th>
    <th>Jenis Pelanggaran</th>
</tr>

<?php

$no = 1;

while ($row = mysqli_fetch_assoc($data)) {

?>

<tr>

    <td><?php echo $no; ?></td>

    <td><?php echo $row['nis']; ?></td>

    <td><?php echo $row['nama']; ?></td>

    <td><?php echo $row['pelanggaran']; ?></td>

</tr>

<?php

$no++;

}

?>

</table>