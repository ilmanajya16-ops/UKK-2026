<?php
// laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ============================================
// 1. AMBIL DATA SESSION
// ============================================
$role = $_SESSION['role'] ?? '';
$id_user = (int) ($_SESSION['id'] ?? $_SESSION['id_user'] ?? 0);

// ============================================
// 2. CEK HAK AKSES
// ============================================
if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini hanya dapat diakses oleh Admin dan Guru Wali Kelas.');
}

// ============================================
// 3. CEK GURU WALI KELAS
// ============================================
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
        die("Query guru gagal: " . mysqli_error($koneksi));
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

// ============================================
// 4. FILTER TANGGAL
// ============================================
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Validasi format tanggal
function validasiTanggal($tanggal)
{
    if ($tanggal === '') {
        return true;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        return false;
    }

    $bagian = explode('-', $tanggal);

    return checkdate(
        (int) $bagian[1],
        (int) $bagian[2],
        (int) $bagian[0]
    );
}

if (
    !validasiTanggal($tanggal_mulai) ||
    !validasiTanggal($tanggal_selesai)
) {
    exit('Format tanggal tidak valid.');
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    exit('Tanggal mulai tidak boleh melebihi tanggal selesai.');
}

// ============================================
// 5. QUERY LAPORAN
// ============================================
$sql = "SELECT
            ps.id,
            ps.tahun_ajaran_id,
            ps.siswa_id,
            ps.nama_siswa,
            ps.kelas_id,
            ps.nama_kelas,
            ps.pelanggaran_id,
            ps.nama_pelanggaran,
            ps.pelanggaran_kategori_id,
            ps.guru_id,
            ps.nama_guru,
            ps.tanggal,
            ps.keterangan,
            ps.poin,
            ps.tindakan,
            ps.status

        FROM t_pelanggaran_siswa ps
        WHERE 1 = 1";

$types = "";
$params = [];

// Guru hanya melihat laporan kelas tanggung jawabnya
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

// Filter tanggal mulai
if ($tanggal_mulai !== '') {

    $sql .= " AND ps.tanggal >= ?";

    $types .= "s";
    $params[] = $tanggal_mulai;
}

// Filter tanggal selesai
if ($tanggal_selesai !== '') {

    $sql .= " AND ps.tanggal <= ?";

    $types .= "s";
    $params[] = $tanggal_selesai;
}

// Urutkan tanggal terbaru
$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// ============================================
// 6. EKSEKUSI QUERY
// ============================================
$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan laporan: " . htmlspecialchars(mysqli_error($koneksi)));
}

// Bind parameter
if (!empty($params)) {

    $bind_params = [$stmt, $types];

    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    call_user_func_array('mysqli_stmt_bind_param', $bind_params);
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

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

    <style>

        /* KONTEN UTAMA */
        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 25px;
            min-height: 100vh;
        }

        /* HEADER TABEL */
        #tabelLaporan thead th {
            background-color: #212529 !important;
            color: white !important;
            border-color: #495057 !important;
            vertical-align: middle;
            white-space: nowrap;
        }

        /* ISI TABEL */
        #tabelLaporan tbody td {
            vertical-align: middle;
        }

        /* FILTER LAPORAN */
        .filter-card {
            border-radius: 7px;
            overflow: hidden;
        }

        .filter-card .card-header {
            font-weight: 500;
        }

        /* CARD LAPORAN */
        .card {
            border-radius: 7px;
        }

        /* TOMBOL CETAK */
        @media print {

            .no-print,
            .sidebar,
            #tabelLaporan_wrapper .dataTables_filter,
            #tabelLaporan_wrapper .dataTables_length,
            #tabelLaporan_wrapper .dataTables_paginate,
            #tabelLaporan_wrapper .dataTables_info {
                display: none !important;
            }

            .content {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                font-size: 11px;
            }

            table {
                width: 100% !important;
            }

        }

        /* RESPONSIVE */
        @media (max-width: 767.98px) {

            .content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
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
        <main class="content">

            <!-- JUDUL HALAMAN -->
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
                    Anda hanya dapat melihat laporan dari kelas yang menjadi tanggung jawab Anda.

                <?php } ?>

            </div>


            <!-- ================================= -->
            <!-- FILTER LAPORAN -->
            <!-- ================================= -->

            <div class="card shadow-sm mb-4 filter-card no-print">

                <!-- HEADER FILTER -->
                <div class="card-header bg-primary text-white">

                    <i class="bi bi-funnel"></i>
                    Filter Laporan

                </div>


                <!-- BODY FILTER -->
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
                                    value="<?= htmlspecialchars($tanggal_mulai); ?>"
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
                                    value="<?= htmlspecialchars($tanggal_selesai); ?>"
                                >

                            </div>


                            <!-- TOMBOL FILTER -->
                            <div class="col-md-4 mb-3">

                                <button type="submit" class="btn btn-primary">

                                    <i class="bi bi-search"></i>
                                    Tampilkan

                                </button>

                                <a href="laporan.php" class="btn btn-secondary">

                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Reset

                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- ================================= -->
            <!-- DATA LAPORAN -->
            <!-- ================================= -->

            <div class="card shadow-sm">

                <!-- HEADER CARD -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <span>
                        <i class="bi bi-file-earmark-text"></i>
                        Data Laporan Pelanggaran
                    </span>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-light btn-sm no-print">

                        <i class="bi bi-printer"></i>
                        Cetak Laporan

                    </button>

                </div>


                <!-- BODY CARD -->
                <div class="card-body">

                    <div class="table-responsive">

                        <table
                            id="tabelLaporan"
                            class="table table-striped table-bordered table-hover align-middle w-100">

                            <!-- HEADER TABEL -->
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


                            <!-- ISI TABEL -->
                            <tbody>

                                <?php
                                $nomor = 1;

                                while ($row = mysqli_fetch_assoc($hasil)) {
                                ?>

                                    <tr>

                                        <!-- NOMOR -->
                                        <td></td>


                                        <!-- NAMA SISWA -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                        </td>


                                        <!-- KELAS -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </td>


                                        <!-- NAMA PELANGGARAN -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                        </td>


                                        <!-- GURU PENCATAT -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                        </td>


                                        <!-- TANGGAL -->
                                        <td>
                                            <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                        </td>


                                        <!-- POIN -->
                                        <td>
                                            <?= (int) ($row['poin'] ?? 0); ?>
                                        </td>


                                        <!-- TINDAKAN -->
                                        <td>
                                            <?= htmlspecialchars($row['tindakan'] ?? '-'); ?>
                                        </td>


                                        <!-- STATUS -->
                                        <td>

                                            <?php

                                            $status = $row['status'] ?? '-';

                                            if ($status === 'Selesai') {

                                                echo '<span class="badge bg-success">Selesai</span>';

                                            } elseif ($status === 'Diproses') {

                                                echo '<span class="badge bg-warning text-dark">Diproses</span>';

                                            } else {

                                                echo '<span class="badge bg-secondary">' .
                                                    htmlspecialchars($status) .
                                                    '</span>';

                                            }

                                            ?>

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

                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Dashboard

                </a>

            </div>

        </main>

    </div>

</div>


<!-- ================================= -->
<!-- JAVASCRIPT -->
<!-- ================================= -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- DataTables Bootstrap -->
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>


<!-- INISIALISASI DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelLaporan').DataTable({

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data laporan",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data laporan tidak ditemukan",

            emptyTable: "Belum ada data pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        },

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[5, 'desc']],

        scrollX: true,

        autoWidth: false,

        columnDefs: [

            {
                targets: 0,
                orderable: false,
                searchable: false
            }

        ],

        // NOMOR URUT OTOMATIS
        drawCallback: function () {

            var api = this.api();

            var info = api.page.info();

            api.column(0, {
                page: 'current'
            }).nodes().each(function (cell, i) {

                cell.innerHTML = info.start + i + 1;

            });

        }

    });

});

</script>

</body>
</html>

<?php

// TUTUP STATEMENT
mysqli_stmt_close($stmt);

?>