<?php
// kelola_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_tahun_ajaran ORDER BY tanggal_mulai DESC";
$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Kelola Tahun Ajaran</title>
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

    <h1>Kelola Tahun Ajaran</h1>

    <p>
        <a href="dashboard.php">Kembali ke Dashboard</a> |
        <a href="tambah_tahun_ajaran.php">Tambah Tahun Ajaran</a>
    </p>

  <table class="table">
  <thead class="thead-dark">

        <tr>
            <th>ID</th>
            <th>Nama Tahun Ajaran</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Status Aktif</th>
            <th>Dibuat</th>
            <th>Diubah</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>

            <td><?php echo $row['nama']; ?></td>

            <td><?php echo $row['tanggal_mulai']; ?></td>

            <td><?php echo $row['tanggal_selesai']; ?></td>

            <td>
                <?php
                if ($row['status_aktif'] == 1) {
                    echo "Aktif";
                } else {
                    echo "Tidak Aktif";
                }
                ?>
            </td>

            <td><?php echo $row['created_at']; ?></td>

            <td><?php echo $row['updated_at']; ?></td>

            <td>
           <td>
                <a href="edit_kelas.php?id=<?php echo $row['id']; ?>">
                    <button type="button" class="btn btn-warning">Edit</button>
                </a>

                |

                <a href="hapus_kelas.php?id=<?php echo $row['id']; ?>"
                   onclick="return confirm('Yakin ingin menghapus data kelas ini?');">
                <button type="button" class="btn btn-danger">Hapus</button>
                </a>
            </td>

        </tr>
        <?php } ?>

    </table>

</body>
</html>