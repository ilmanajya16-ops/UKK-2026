<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_siswa ORDER BY nama ASC";
$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Siswa</title>
</head>
<body>

    <h1>Kelola Siswa</h1>

    <p>
        <a href="dashboard.php">Kembali ke Dashboard</a> |
        <a href="tambah_siswa.php">Tambah Siswa</a>
    </p>

    <table border="1" cellpadding="6" cellspacing="0">
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
                <a href="edit_siswa.php?nis=<?= $siswa['nis'] ?? '' ?>">Edit</a> |
                <a href="hapus_siswa.php?nis=<?= $siswa['nis'] ?? '' ?>">Hapus</a>
            </td>
        </tr>
        <?php } ?>

    </table>

</body>
</html>