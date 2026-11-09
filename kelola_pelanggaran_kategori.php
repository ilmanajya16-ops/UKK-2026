
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

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
            min-height: 100vh;
            box-sizing: border-box;
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
            flex-wrap: wrap;
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

        @media (max-width: 767.98px) {
            .content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid p-0">

    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="content">

        <h2 class="mb-3">Kelola Kategori Pelanggaran</h2>

        <!-- TOMBOL -->
        <div class="tombol-aksi">

            <a href="dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>

            <a href="tambah_pelanggaran_kategori.php"
               class="btn btn-primary">
                + Tambah Kategori Pelanggaran
            </a>

        </div>

        <!-- CEK QUERY -->
        <?php if (!$hasil) { ?>

            <div class="alert alert-danger">
                Gagal mengambil data:
                <?= htmlspecialchars(mysqli_error($koneksi)); ?>
            </div>

        <?php } else { ?>

            <!-- CARD DATA KATEGORI -->
            <div class="card shadow-sm">

                <div class="card-body">

                    <h5 class="mb-3">Data Kategori Pelanggaran</h5>

                    <div class="table-responsive">

                        <table id="tabelPelanggaran"
                               class="table table-striped table-bordered"
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
                                            <?= htmlspecialchars($row['id']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['deskripsi'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?php if ($row['status_aktif'] == 1) { ?>

                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-secondary">
                                                    Tidak Aktif
                                                </span>

                                            <?php } ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['created_at'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['updated_at'] ?? '-'); ?>
                                        </td>

                                        <td>

                                            <a href="edit_pelanggaran_kategori.php?id=<?= urlencode($row['id']); ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a href="hapus_pelanggaran_kategori.php?id=<?= urlencode($row['id']); ?>"
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- Integrasi DataTables Bootstrap 4 -->
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- DATATABLES -->
<script>
$(document).ready(function () {

    $('#tabelPelanggaran').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        autoWidth: false,

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
