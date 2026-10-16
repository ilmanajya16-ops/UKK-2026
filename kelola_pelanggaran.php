<?php

// kelola_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_kategori_pelanggaran ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kategori Pelanggaran</title>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
            <h4 class="text-white mb-4">
                My Website
            </h4>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link active">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_guru.php" class="nav-link text-white">
                        Data Guru
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="kelola_kelas.php" class="nav-link text-white">
                        Kelas
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="kelola_tahun_ajaran.php" class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                    <li class="nav-item mb-2">
                    <a href="kelola_wali_kelas.php" class="nav-link text-white">
                        Wali Kelas
                    </a>
                </li>
                    <li class="nav-item mb-2">
                    <a href="kelola_pelanggaran.php" class="nav-link active">
                        Kelola Pelanggaran
                    </a>
                </li>


                <li class="nav-item mb-2">
                    <a href="" class="nav-link text-white">
                        Laporan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <a href="#" class="nav-link text-danger">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">
            <h2>Dashboard</h2>
            <p>
                Selamat datang di halaman dashboard.
            </p>
            <div class="row"></div>
    <h1>Kelola Kategori Pelanggaran</h1>

    <p>
        <a href="dashboard.php">Kembali ke Dashboard</a> |
        <a href="tambah_pelanggaran_kategori.php">
            Tambah Kategori Pelanggaran
        </a>
    </p>

   
    <table class="table">
  <thead class="thead-dark"></thead>
        <tr>
            <th>ID</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th>Status Aktif</th>
            <th>Dibuat</th>
            <th>Diubah</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
        <tr>
            <td>
                <?php echo $row['id']; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['nama']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($row['deskripsi'] ?? ''); ?>
            </td>

            <td>
                <?php
                if ($row['status_aktif'] == 1) {
                    echo "Aktif";
                } else {
                    echo "Tidak Aktif";
                }
                ?>
            </td>

            <td>
                <?php echo $row['created_at']; ?>
            </td>

            <td>
                <?php echo $row['updated_at']; ?>
            </td>

            <td>
                <a href="edit_pelanggaran_kategori.php?id=<?php echo $row['id']; ?>">
                    Edit
                </a>
                |
                <a href="hapus_pelanggaran_kategori.php?id=<?php echo $row['id']; ?>"
                   onclick="return confirm('Yakin ingin menghapus kategori pelanggaran ini?');">
                    Hapus
                </a>
            </td>
        </tr>
        <?php } ?>

    </table>

</body>
</html>