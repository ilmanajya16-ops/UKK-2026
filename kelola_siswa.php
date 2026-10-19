
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_siswa ORDER BY nama ASC";
$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Siswa</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- CSS DataTables -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .sidebar {
            background-color: #212529;
            min-height: 100vh;
        }

        .sidebar h4 {
            color: white;
            margin-bottom: 25px;
        }

        .sidebar .nav-link {
            color: white;
            margin-bottom: 8px;
        }

        .sidebar .nav-link.active {
            background-color: #007bff;
        }

        .content {
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

<?php
            include 'layout/sidebar.php';
        ?>



        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 content">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="mb-3">Kelola Siswa</h2>

                    <p>
                        <a href="dashboard.php"
                           class="btn btn-secondary mb-3">
                            Kembali
                        </a>

                        <a href="tambah_siswa.php"
                           class="btn btn-primary mb-3">
                            + Tambah Siswa
                        </a>
                    </p>

                    <div class="table-responsive">

                        <table id="tabelSiswa"
                               class="table table-striped table-bordered"
                               style="width:100%">

                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Alamat</th>
                                    <th>Status Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;

                                while ($siswa = mysqli_fetch_assoc($hasil)) {
                                    $nis = $siswa['nis'] ?? '';
                                ?>

                                <tr>
                                    <td><?= $no++ ?></td>

                                    <td>
                                        <?= htmlspecialchars($siswa['nis'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($siswa['nisn'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($siswa['nama'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($siswa['jenis_kelamin'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($siswa['tanggal_lahir'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($siswa['alamat'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($siswa['status_aktif'] ?? 'Aktif') ?>
                                    </td>

                                    <td>
                                        <a href="edit_siswa.php?nis=<?= urlencode($nis) ?>"
                                           class="btn btn-warning btn-sm mb-1">
                                            Edit
                                        </a>

                                        <a href="hapus_siswa.php?nis=<?= urlencode($nis) ?>"
                                           class="btn btn-danger btn-sm mb-1"
                                           onclick="return confirm('Yakin ingin menghapus data siswa ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>

                                <?php } ?>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {
    $('#tabelSiswa').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "ordering": true,
        "searching": true,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "language": {
            "search": "Cari siswa:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Data siswa tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data siswa",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "emptyTable": "Belum ada data siswa",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>

</body>
</html>


