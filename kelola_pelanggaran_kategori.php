
<?php
// kelola_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data kategori pelanggaran
$sql = "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC";
$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Kategori Pelanggaran</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- CSS DataTables Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: white;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }

        .sidebar .nav-link.active {
            background-color: #007bff;
        }

        .content {
            padding: 25px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .judul {
            margin-bottom: 20px;
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

            <h1 class="judul">Kelola Kategori Pelanggaran</h1>

            <p>
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_pelanggaran_kategori.php"
                   class="btn btn-primary">
                    + Tambah Kategori Pelanggaran
                </a>
            </p>

            <!-- PESAN ERROR JIKA QUERY GAGAL -->
            <?php if (!$hasil) { ?>

                <div class="alert alert-danger">
                    Gagal mengambil data:
                    <?php echo htmlspecialchars(mysqli_error($koneksi)); ?>
                </div>

            <?php } else { ?>

                <!-- TABEL DATA KATEGORI -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Data Kategori Pelanggaran</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">

                            <table id="tabelPelanggaran"
                                   class="table table-striped table-bordered table-hover"
                                   style="width:100%">

                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Status Aktif</th>
                                        <th>Dibuat</th>
                                        <th>Diubah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($row['id']); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($row['nama']); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($row['deskripsi'] ?? ''); ?>
                                            </td>

                                            <td>
                                                <?php if ($row['status_aktif'] == 1) { ?>

                                                    <span class="badge badge-success">
                                                        Aktif
                                                    </span>

                                                <?php } else { ?>

                                                    <span class="badge badge-secondary">
                                                        Tidak Aktif
                                                    </span>

                                                <?php } ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($row['created_at'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?php echo htmlspecialchars($row['updated_at'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <a href="edit_pelanggaran_kategori.php?id=<?php echo urlencode($row['id']); ?>"
                                                   class="btn btn-warning btn-sm">
                                                    Edit
                                                </a>

                                                <a href="hapus_pelanggaran_kategori.php?id=<?php echo urlencode($row['id']); ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus kategori pelanggaran ini?');">
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

            <?php } ?>

        </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 4 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- Integrasi DataTables dengan Bootstrap 4 -->
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {

    $('#tabelPelanggaran').DataTable({
        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        scrollX: true,

        order: [[0, 'desc']],

        columnDefs: [
            {
                targets: 6,
                orderable: false,
                searchable: false
            }
        ],

        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data kategori tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Belum ada data kategori pelanggaran",
            infoFiltered: "(difilter dari _MAX_ total data)",
            search: "Cari:",
            processing: "Memproses data...",
            loadingRecords: "Memuat data...",
            emptyTable: "Belum ada data kategori pelanggaran",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });

});
</script>

</body>
</html>
