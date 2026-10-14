<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_siswa ORDER BY nama ASC";
$hasil = mysqli_query($koneksi, $sql);
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


    <h1>Kelola Siswa</h1>

    <p>
        <a href="dashboard.php">Kembali ke Dashboard</a> |
        <a href="tambah_siswa.php">Tambah Siswa</a>
    </p>


    <table class="table">
  <thead class="thead-dark">
        <tr>
            <th>NIS</th>
            <th>NISN</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Status Aktif</th>
            <th>Aksi</th>
        </tr>

        <?php
        while ($siswa = mysqli_fetch_assoc($hasil)) {
        ?>
        <tr>
            <td><?= $siswa['nis'] ?? '' ?></td>
            <td><?= $siswa['nisn'] ?? '' ?></td>
            <td><?= $siswa['nama'] ?? '' ?></td>
            <td><?= $siswa['jenis_kelamin'] ?? '' ?></td>
            <td><?= $siswa['tanggal_lahir'] ?? '' ?></td>
            <td><?= $siswa['alamat'] ?? '' ?></td>
            <td><?= $siswa['status_aktif'] ?? 'Aktif' ?></td>
            <td>
                <a href="edit_siswa.php?nis=<?= $siswa['nis'] ?? '' ?>"></a> 
            <button type="button" class="btn btn-warning">Edit</button>
                <a href="hapus_siswa.php?nis=<?= $siswa['nis'] ?? '' ?>"></a>
                  <button type="button" class="btn btn-danger">Hapus</button>
            </td>
        </tr>
        <?php } ?>

    </table>

</body>
</html>