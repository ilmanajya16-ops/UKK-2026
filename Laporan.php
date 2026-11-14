
<?php
// laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// SESSION DAN HAK AKSES
// ========================================

$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id'] ?? $_SESSION['id_user'] ?? 0);

if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    exit('Akses ditolak.');
}

// ========================================
// CEK GURU WALI KELAS AKTIF
// ========================================

$id_guru = 0;

if ($role === 'guru') {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT g.id
         FROM t_guru g
         INNER JOIN t_wali_kelas wk ON wk.guru_id = g.id
         WHERE g.user_id = ?
         AND wk.status_aktif = 1
         LIMIT 1"
    );

    if (!$stmt_guru) {
        die("Gagal mengambil data guru: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    mysqli_stmt_close($stmt_guru);

    if (!$data_guru) {
        http_response_code(403);
        exit('Akses ditolak. Anda bukan Guru Wali Kelas yang aktif.');
    }

    $id_guru = (int) $data_guru['id'];
}

// ========================================
// FILTER TANGGAL
// ========================================

$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

foreach ([$tanggal_mulai, $tanggal_selesai] as $tanggal) {

    if ($tanggal !== '') {

        $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

        if (!$date || $date->format('Y-m-d') !== $tanggal) {
            exit('Tanggal tidak valid.');
        }
    }
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    exit('Tanggal mulai tidak boleh melebihi tanggal selesai.');
}

// ========================================
// QUERY LAPORAN
// ========================================

$sql = "SELECT
            ps.id,
            ps.nama_siswa,
            ps.nama_kelas,
            ps.nama_pelanggaran,
            ps.nama_guru,
            ps.tanggal,
            ps.poin,
            ps.tindakan,
            ps.status,
            ps.kelas_id,
            ps.tahun_ajaran_id

        FROM t_pelanggaran_siswa ps

        WHERE 1 = 1";

$types = "";
$params = [];

// ========================================
// FILTER HAK AKSES GURU
// ========================================
// Guru hanya melihat laporan dari kelas
// yang menjadi tanggung jawabnya.
//
// Tidak menggunakan filter ps.guru_id,
// sehingga guru pencatat boleh guru lain.
// ========================================

if ($role === 'guru') {

    $sql .= " AND EXISTS (
                SELECT 1
                FROM t_wali_kelas wk
                WHERE wk.guru_id = ?
                AND wk.kelas_id = ps.kelas_id
                AND wk.tahun_ajaran_id = ps.tahun_ajaran_id
                AND wk.status_aktif = 1
            )";

    $types .= "i";
    $params[] = $id_guru;
}

// ========================================
// FILTER TANGGAL MULAI
// ========================================

if ($tanggal_mulai !== '') {

    $sql .= " AND ps.tanggal >= ?";

    $types .= "s";
    $params[] = $tanggal_mulai;
}

// ========================================
// FILTER TANGGAL SELESAI
// ========================================

if ($tanggal_selesai !== '') {

    $sql .= " AND ps.tanggal <= ?";

    $types .= "s";
    $params[] = $tanggal_selesai;
}

// ========================================
// URUTKAN DATA
// ========================================

$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// ========================================
// EKSEKUSI QUERY
// ========================================

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan laporan: " . mysqli_error($koneksi));
}

if (!empty($params)) {

    $bind_params = [$stmt, $types];

    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    call_user_func_array(
        'mysqli_stmt_bind_param',
        $bind_params
    );
}

mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

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

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* ==================================
           TABEL LAPORAN
        ================================== */

        #tabelLaporan {
            width: 100% !important;
            border-collapse: collapse !important;
            background-color: #ffffff !important;
        }

        /* HEADER TABEL PUTIH */

        #tabelLaporan thead th {
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

        #tabelLaporan tbody td {
            vertical-align: middle !important;
            padding: 10px !important;
            white-space: nowrap;
            border: 1px solid #dee2e6;
        }

        /* BARIS SELANG-SELING */

        #tabelLaporan tbody tr:nth-child(even) {
            background-color: #f2f2f2 !important;
        }

        #tabelLaporan tbody tr:nth-child(odd) {
            background-color: #ffffff !important;
        }

        /* HOVER TABEL */

        #tabelLaporan tbody tr:hover {
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

        .form-label {
            font-weight: 500;
        }

        /* ==================================
           CETAK LAPORAN
        ================================== */

        @media print {

            .no-print,
            .sidebar,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_paginate,
            .dataTables_info {
                display: none !important;
            }

            main {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background: white;
                font-size: 11px;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            #tabelLaporan {
                width: 100% !important;
            }

            #tabelLaporan thead th {
                background-color: #ffffff !important;
                color: #212529 !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
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
                        Laporan Pelanggaran Siswa
                    </h2>

                    <p class="text-muted">
                        Halaman laporan data pelanggaran siswa.
                    </p>

                    <!-- INFORMASI AKSES -->
                    <div class="alert alert-info no-print">

                        <?php if ($role === 'admin') { ?>

                            Anda login sebagai <strong>Admin</strong>.
                            Anda dapat melihat seluruh laporan pelanggaran siswa.

                        <?php } else { ?>

                            Anda login sebagai <strong>Guru Wali Kelas</strong>.
                            Anda dapat melihat seluruh laporan pelanggaran dari kelas
                            yang menjadi tanggung jawab Anda, termasuk laporan
                            yang dicatat oleh guru lain.

                        <?php } ?>

                    </div>

                    <!-- FILTER LAPORAN -->
                    <div class="card shadow-sm mb-4 no-print">

                        <div class="card-header bg-primary text-white">
                            Filter Laporan
                        </div>

                        <div class="card-body">

                            <form method="GET" action="laporan.php">

                                <div class="row align-items-end">

                                    <!-- TANGGAL MULAI -->
                                    <div class="col-md-4 mb-3">

                                        <label for="tanggal_mulai" class="form-label">
                                            Tanggal Mulai
                                        </label>

                                        <input
                                            type="date"
                                            name="tanggal_mulai"
                                            id="tanggal_mulai"
                                            class="form-control"
                                            value="<?= htmlspecialchars($tanggal_mulai, ENT_QUOTES, 'UTF-8'); ?>"
                                        >

                                    </div>

                                    <!-- TANGGAL SELESAI -->
                                    <div class="col-md-4 mb-3">

                                        <label for="tanggal_selesai" class="form-label">
                                            Tanggal Selesai
                                        </label>

                                        <input
                                            type="date"
                                            name="tanggal_selesai"
                                            id="tanggal_selesai"
                                            class="form-control"
                                            value="<?= htmlspecialchars($tanggal_selesai, ENT_QUOTES, 'UTF-8'); ?>"
                                        >

                                    </div>

                                    <!-- TOMBOL FILTER -->
                                    <div class="col-md-4 mb-3">

                                        <button type="submit" class="btn btn-primary">
                                            Tampilkan
                                        </button>

                                        <a href="laporan.php" class="btn btn-secondary">
                                            Reset
                                        </a>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                    <!-- TABEL LAPORAN -->
                    <div class="card shadow-sm">

                        <!-- HEADER CARD -->
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                            <span>
                                Data Laporan Pelanggaran
                            </span>

                            <button
                                type="button"
                                onclick="window.print()"
                                class="btn btn-light btn-sm no-print"
                            >
                                Cetak Laporan
                            </button>

                        </div>

                        <!-- BODY TABEL -->
                        <div class="card-body">

                            <div class="table-responsive">

                                <table
                                    id="tabelLaporan"
                                    class="table table-striped table-bordered"
                                    style="width:100%"
                                >

                                    <thead>

                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Nama Pelanggaran</th>
                                            <th>Guru Pencatat</th>
                                            <th>Tanggal</th>
                                            <th>Poin</th>
                                            <th>Tindakan</th>
                                            <th>Status</th>
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
                                                    <?= htmlspecialchars($row['nama_siswa'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['nama_kelas'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['nama_guru'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['tanggal'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                                <td>
                                                    <?= (int)($row['poin'] ?? 0); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['tindakan'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($row['status'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- TOMBOL KEMBALI -->
                    <div class="mt-3 no-print">

                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali ke Dashboard
                        </a>

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DATATABLES JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- INISIALISASI DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelLaporan').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        autoWidth: false,

        order: [[5, 'desc']],

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        language: {

            search: "Cari laporan:",

            lengthMenu: "Tampilkan _MENU_ data",

            zeroRecords: "Data laporan tidak ditemukan",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data laporan",

            infoFiltered: "(difilter dari _MAX_ total data)",

            emptyTable: "Belum ada data pelanggaran",

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
mysqli_stmt_close($stmt);
?>
