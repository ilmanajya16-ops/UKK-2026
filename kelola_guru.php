
<?php
// kelola_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data guru
$sql = "SELECT * FROM t_guru ORDER BY nama ASC";
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

    <title>Kelola Guru</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .content {
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .card-body {
            padding: 25px;
        }

        h2 {
            color: #212529;
            font-weight: 600;
        }

        .tombol-aksi {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
        }

        .tombol-aksi .btn {
            padding: 10px 14px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .aksi {
            display: flex;
            gap: 5px;
            align-items: center;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 content">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="mb-3">Kelola Guru</h2>

                    <!-- TOMBOL -->
                    <div class="tombol-aksi">

                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali ke Dashboard
                        </a>

                        <a href="tambah_guru.php" class="btn btn-primary">
                            + Tambah Guru
                        </a>

                    </div>

                    <!-- TABEL GURU -->
                    <div class="table-responsive">

                        <table id="tabelGuru"
                               class="table table-striped table-bordered"
                               style="width:100%">

                           <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>NIP</th>
                                <th>Nama Guru</th>
                                <th>Email</th>
                                <th>Status Aktif</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                while ($guru = mysqli_fetch_assoc($hasil)) {
                                    $id = $guru['id'] ?? '';
                                ?>

                                <tr>

                                    <td><?= $no++ ?></td>

                                    <td>
                                        <?= htmlspecialchars((string)($guru['id'] ?? '')) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars((string)($guru['nip'] ?? '')) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars((string)($guru['nama'] ?? '')) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars((string)($guru['email'] ?? '')) ?>
                                    </td>

                                    <td>
                                        <?php if (($guru['status_aktif'] ?? 0) == 1) { ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <div class="aksi">

                                            <a href="edit_guru.php?id=<?= urlencode((string)$id) ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a href="hapus_guru.php?id=<?= urlencode((string)$id) ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus data guru ini?')">
                                                Hapus
                                            </a>

                                        </div>
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

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- DATATABLES -->
<script>
$(document).ready(function () {

    $('#tabelGuru').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        autoWidth: false,

        language: {
            search: "Cari guru:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data guru tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data guru",
            infoFiltered: "(difilter dari _MAX_ total data)",
            emptyTable: "Belum ada data guru",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }

    });

});
</script>

</body>
</html>
