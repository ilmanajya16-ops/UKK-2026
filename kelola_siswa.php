<?php
// kelola_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_guru ORDER BY nama ASC";
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
            <th>jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Status Aktif</th>
            <th>Aksi</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

        <tr>

            <td><?php echo $row['nis']; ?></td>

            <td><?php echo $row['nisn']; ?></td>

            <td><?php echo $row['jenis kelamin']; ?></td>

            <td><?php echo $row['tanggal lahir']; ?></td>

            <td><?php echo $row['alamat']; ?></td>

            <td><?php echo $row['status aktif']; ?></td>

            <td><?php echo $row['aksi']; ?></td>

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
                <a href="edit_siswa.php?id=<?php echo $row['id']; ?>">
                    Edit
                </a>

                |

                <a href="hapus_siswa.php?id=<?php echo $row['id']; ?>"
                   onclick="return confirm('Yakin ingin menghapus data siswa ini?');">
                    Hapus
                </a>
            </td>

        </tr>

        <?php } ?>

    </table>

</body>
</html>