
<?php
// kelola_jenis_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data jenis pelanggaran beserta nama kategori
$sql = "SELECT
            p.id,
            p.pelanggaran_kategori_id,
            pk.nama AS nama_kategori,
            p.kode,
            p.nama,
            p.poin,
            p.deskripsi,
            p.status_aktif,
            p.created_at,
            p.updated_at
        FROM t_pelanggaran p
        LEFT JOIN t_pelanggaran_kategori pk
            ON p.pelanggaran_kategori_id = pk.id
        ORDER BY p.nama ASC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data: " .
        htmlspecialchars(mysqli_error($koneksi)));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Jenis Pelanggaran</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DATATABLES CSS BOOTSTRAP 4 -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

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

                    <h2 class="mb-3">Kelola Jenis Pelanggaran</h2>

                    <!-- PESAN SUKSES -->
                    <?php if (isset($_SESSION['pesan_sukses'])) { ?>

                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>
                        </div>

                        <?php unset($_SESSION['pesan_sukses']); ?>

                    <?php } ?>

                    <!-- PESAN ERROR -->
                    <?php if (isset($_SESSION['pesan_error'])) { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                        </div>

                        <?php unset($_SESSION['pesan_error']); ?>

                    <?php } ?>

                    <!-- TOMBOL -->
                    <div class="tombol-aksi">

                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali ke Dashboard
                        </a>

                        <a href="tambah_jenis_pelanggaran.php"
                           class="btn btn-primary">
                            + Tambah Jenis Pelanggaran
                        </a>

                    </div>

                    <!-- TABEL JENIS PELANGGARAN -->
                    <div class="table-responsive">

                        <table id="tabelPelanggaran"
                               class="table table-striped table-bordered"
                               style="width:100%">

                            <thead class="thead-dark">

                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Kategori</th>
                                    <th>Kode</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Diubah</th>
                                    <th>Aksi</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                while ($row = mysqli_fetch_assoc($hasil)) {
                                ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <?= (int) $row['id']; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['kode'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?= (int) $row['poin']; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['deskripsi'] ?? '-'); ?>
                                        </td>

                                        <td>

                                            <?php if ((int) $row['status_aktif'] === 1) { ?>

                                                <span class="badge bg-success">
                                                    Aktif
                                                </span>

                                            <?php } else { ?>

                                                <span class="badge bg-danger">
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

                                            <a href="edit_jenis_pelanggaran.php?id=<?= (int) $row['id']; ?>"
                                               class="btn btn-warning btn-sm mb-1">
                                                Edit
                                            </a>

                                            <a href="hapus_jenis_pelanggaran.php?id=<?= (int) $row['id']; ?>"
                                               class="btn btn-danger btn-sm mb-1"
                                               onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?');">
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

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DATATABLES JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

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

        language: {

            search: "Cari pelanggaran:",

            lengthMenu: "Tampilkan _MENU_ data",

            zeroRecords: "Data pelanggaran tidak ditemukan",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data pelanggaran",

            infoFiltered: "(difilter dari _MAX_ total data)",

            emptyTable: "Belum ada jenis pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Selanjutnya",

                previous: "Sebelumnya"

            }

        },

        order: [[4, 'asc']],

        columnDefs: [

            {
                targets: 10,
                orderable: false,
                searchable: false
            }

        ]

    });

});

</script>

</body>
</html>
