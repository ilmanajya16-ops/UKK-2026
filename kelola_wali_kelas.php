
<?php
// kelola_wali_kelas.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ============================================
// MENGAMBIL DATA WALI KELAS
// ============================================

$sql = "SELECT
            w.*,
            k.nama AS nama_kelas,
            g.nama AS nama_guru
        FROM t_wali_kelas w
        LEFT JOIN t_kelas k ON w.kelas_id = k.id
        LEFT JOIN t_guru g ON w.guru_id = g.id
        ORDER BY w.id DESC";

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

    <title>Kelola Wali Kelas</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- CSS DataTables Bootstrap 4 -->
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
            align-items: center;
            gap: 5px;
            flex-wrap: nowrap;
        }

        .aksi .btn {
            margin: 0;
        }
    </style>
</head>

<body>

<div class="container-fluid p-0">

    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN UTAMA -->
    <main class="content">

        <div class="card shadow-sm">

            <div class="card-body">

                <h2 class="mb-3">Kelola Wali Kelas</h2>

                <!-- TOMBOL -->
                <div class="mb-3">

                    <a href="dashboard.php"
                       class="btn btn-secondary">
                        Kembali ke Dashboard
                    </a>

                    <a href="tambah_wali_kelas.php"
                       class="btn btn-primary">
                        + Tambah Wali Kelas
                    </a>

                </div>

                <!-- TABEL DATA WALI KELAS -->
                <div class="table-responsive">

                    <table id="tabelWaliKelas"
                           class="table table-striped table-bordered"
                           style="width:100%">

                        <thead class="thead-dark">

                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Tahun Ajaran ID</th>
                                <th>Kelas</th>
                                <th>Guru</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php
                            $no = 1;

                            while ($row = mysqli_fetch_assoc($hasil)) {

                                $id = $row['id'] ?? '';
                            ?>

                            <tr>

                                <!-- NO -->
                                <td>
                                    <?= $no++ ?>
                                </td>

                                <!-- ID -->
                                <td>
                                    <?= htmlspecialchars((string)$id) ?>
                                </td>

                                <!-- TAHUN AJARAN -->
                                <td>
                                    <?= htmlspecialchars((string)($row['tahun_ajaran_id'] ?? '-')) ?>
                                </td>

                                <!-- KELAS -->
                                <td>
                                    <?= htmlspecialchars((string)($row['nama_kelas'] ?? '-')) ?>
                                </td>

                                <!-- GURU -->
                                <td>
                                    <?= htmlspecialchars((string)($row['nama_guru'] ?? '-')) ?>
                                </td>

                                <!-- TANGGAL MULAI -->
                                <td>
                                    <?= htmlspecialchars((string)($row['tanggal_mulai'] ?? '-')) ?>
                                </td>

                                <!-- TANGGAL SELESAI -->
                                <td>
                                    <?= htmlspecialchars((string)($row['tanggal_selesai'] ?? '-')) ?>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <?php
                                    if (($row['status_aktif'] ?? 0) == 1) {
                                        echo '<span class="badge badge-success">Aktif</span>';
                                    } else {
                                        echo '<span class="badge badge-secondary">Tidak Aktif</span>';
                                    }
                                    ?>
                                </td>

                                <!-- AKSI -->
                                <td>

                                    <div class="aksi">

                                        <!-- EDIT -->
                                        <a href="edit_wali_kelas.php?id=<?= urlencode((string)$id) ?>"
                                           class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <!-- HAPUS -->
                                        <a href="hapus_wali_kelas.php?id=<?= urlencode((string)$id) ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus data wali kelas ini?');">
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

<!-- ============================================ -->
<!-- JAVASCRIPT -->
<!-- ============================================ -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- PENGATURAN DATATABLES -->
<script>
$(document).ready(function () {

    $('#tabelWaliKelas').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        autoWidth: false,

        language: {

            search: "Cari wali kelas:",

            lengthMenu: "Tampilkan _MENU_ data",

            zeroRecords: "Data wali kelas tidak ditemukan",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data wali kelas",

            infoFiltered: "(difilter dari _MAX_ total data)",

            emptyTable: "Belum ada data wali kelas",

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
