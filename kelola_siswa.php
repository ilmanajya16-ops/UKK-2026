
<?php
// kelola_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// MENGAMBIL DATA SISWA
// ========================================

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

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DATATABLES BOOTSTRAP 5 -->
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
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .tombol-aksi .btn {
            padding: 10px 14px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* ==================================
           TABEL SISWA
        ================================== */

        #tabelSiswa {
            width: 100% !important;
            border-collapse: collapse !important;
            background-color: #ffffff !important;
        }

        /* HEADER TABEL PUTIH */

        #tabelSiswa thead th {
            background-color: #ffffff !important;
            color: #212529 !important;
            text-align: center !important;
            vertical-align: middle !important;
            padding: 12px !important;
            white-space: nowrap;
            border: 1px solid #dee2e6 !important;
            font-weight: 600;
        }

        /* ISI TABEL */

        #tabelSiswa tbody td {
            vertical-align: middle !important;
            padding: 10px !important;
            white-space: nowrap;
            border: 1px solid #dee2e6 !important;
        }

        /* BARIS SELANG-SELING */

        #tabelSiswa tbody tr:nth-child(even) {
            background-color: #f2f2f2 !important;
        }

        #tabelSiswa tbody tr:nth-child(odd) {
            background-color: #ffffff !important;
        }

        /* HOVER */

        #tabelSiswa tbody tr:hover {
            background-color: #e9ecef !important;
        }

        /* DATATABLES */

        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_length {
            margin-bottom: 15px;
        }

        .dataTables_info {
            padding-top: 12px !important;
        }

        .dataTables_paginate {
            padding-top: 8px !important;
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

                    <!-- JUDUL -->
                    <h2 class="mb-3">
                        Kelola Siswa
                    </h2>

                    <!-- TOMBOL -->
                    <div class="tombol-aksi">

                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali ke Dashboard
                        </a>

                        <a href="tambah_siswa.php" class="btn btn-primary">
                            + Tambah Siswa
                        </a>

                    </div>

                    <!-- TABEL SISWA -->
                    <div class="table-responsive">

                        <table
                            id="tabelSiswa"
                            class="table table-striped table-bordered"
                            style="width:100%"
                        >

                            <thead>

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

                                    // ==================================
                                    // KONVERSI JENIS KELAMIN
                                    // ==================================

                                    $jk = strtoupper(trim($siswa['jenis_kelamin'] ?? ''));

                                    if ($jk === 'L') {
                                        $jenis_kelamin = 'Laki-laki';
                                    } elseif ($jk === 'P') {
                                        $jenis_kelamin = 'Perempuan';
                                    } else {
                                        $jenis_kelamin = $siswa['jenis_kelamin'] ?? '-';
                                    }
                                ?>

                                    <tr>

                                        <td>
                                            <?= $no++; ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($nis, ENT_QUOTES, 'UTF-8'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($siswa['nisn'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($siswa['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($jenis_kelamin, ENT_QUOTES, 'UTF-8'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($siswa['tanggal_lahir'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($siswa['alamat'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($siswa['status_aktif'] ?? 'Aktif', ENT_QUOTES, 'UTF-8'); ?>
                                        </td>

                                        <td>

                                            <a
                                                href="edit_siswa.php?nis=<?= urlencode($nis); ?>"
                                                class="btn btn-warning btn-sm mb-1"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="hapus_siswa.php?nis=<?= urlencode($nis); ?>"
                                                class="btn btn-danger btn-sm mb-1"
                                                onclick="return confirm('Yakin ingin menghapus data siswa ini?')"
                                            >
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

<!-- BOOTSTRAP 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DATATABLES JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- INISIALISASI DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelSiswa').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        autoWidth: false,

        language: {

            search: "Cari siswa:",

            lengthMenu: "Tampilkan _MENU_ data",

            zeroRecords: "Data siswa tidak ditemukan",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data siswa",

            infoFiltered: "(difilter dari _MAX_ total data)",

            emptyTable: "Belum ada data siswa",

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

<?php
mysqli_free_result($hasil);
?>
