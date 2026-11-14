
<?php
// ==========================================
// RIWAYAT.PHP
// ==========================================

include 'includes/cek_session.php';
include 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// FUNGSI KEAMANAN
// ==========================================
function e($data)
{
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// ==========================================
// IDENTITAS LOGIN
// ==========================================
$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int)($_SESSION['id_user'] ?? $_SESSION['id'] ?? 0);

$is_guru = in_array($role, ['guru', 'wali_kelas', 'wali kelas'], true);

$guru_id = 0;
$pesan_error = '';

// ==========================================
// MENCARI ID GURU YANG LOGIN
// ==========================================
if ($is_guru) {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT id
         FROM t_guru
         WHERE user_id = ?
         LIMIT 1"
    );

    if (!$stmt_guru) {
        die('Gagal menyiapkan data guru: ' . e(mysqli_error($koneksi)));
    }

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    if ($data_guru) {
        $guru_id = (int)$data_guru['id'];
    }

    mysqli_stmt_close($stmt_guru);

    if ($guru_id <= 0) {
        $pesan_error = 'Data guru yang login tidak ditemukan.';
    }
}

// ==========================================
// PENCARIAN DAN FILTER
// ==========================================
$cari = trim($_GET['cari'] ?? '');
$status = trim($_GET['status'] ?? '');

// ==========================================
// QUERY RIWAYAT PELANGGARAN
// ==========================================
$sql = "
    SELECT
        ps.id,
        ps.tanggal,
        ps.poin,
        ps.keterangan,
        ps.tindakan,
        ps.status,

        COALESCE(NULLIF(ps.nama_siswa, ''), s.nama, '-') AS nama_siswa,
        COALESCE(NULLIF(ps.nama_kelas, ''), k.nama, '-') AS nama_kelas,
        COALESCE(NULLIF(ps.nama_pelanggaran, ''), p.nama, '-') AS nama_pelanggaran,
        COALESCE(NULLIF(ps.nama_guru, ''), g.nama, '-') AS nama_guru

    FROM t_pelanggaran_siswa ps

    LEFT JOIN t_siswa s
        ON s.id = ps.siswa_id

    LEFT JOIN t_kelas k
        ON k.id = ps.kelas_id

    LEFT JOIN t_pelanggaran p
        ON p.id = ps.pelanggaran_id

    LEFT JOIN t_guru g
        ON g.id = ps.guru_id

    WHERE 1 = 1
";

// ==========================================
// FILTER BERDASARKAN GURU YANG LOGIN
// ==========================================
// Guru dan wali kelas hanya melihat laporan
// yang dicatat oleh dirinya sendiri.
// ==========================================
if ($is_guru) {

    if ($guru_id > 0) {

        $sql .= " AND ps.guru_id = ? ";

    } else {

        $sql .= " AND 1 = 0 ";

    }
}

// ==========================================
// FILTER PENCARIAN
// ==========================================
if ($cari !== '') {

    $cari_sql = mysqli_real_escape_string($koneksi, $cari);

    $sql .= "
        AND (
            COALESCE(NULLIF(ps.nama_siswa, ''), s.nama, '') LIKE '%$cari_sql%'
            OR COALESCE(NULLIF(ps.nama_pelanggaran, ''), p.nama, '') LIKE '%$cari_sql%'
            OR COALESCE(NULLIF(ps.nama_kelas, ''), k.nama, '') LIKE '%$cari_sql%'
            OR COALESCE(NULLIF(ps.nama_guru, ''), g.nama, '') LIKE '%$cari_sql%'
        )
    ";
}

// ==========================================
// FILTER STATUS
// ==========================================
if ($status !== '') {

    $status_sql = mysqli_real_escape_string($koneksi, $status);

    $sql .= " AND ps.status = '$status_sql' ";
}

// ==========================================
// URUTAN DATA
// ==========================================
$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// ==========================================
// EKSEKUSI QUERY
// ==========================================
if ($is_guru) {

    if ($guru_id > 0) {

        $query_stmt = mysqli_prepare($koneksi, $sql);

        if (!$query_stmt) {
            die('Gagal menyiapkan query: ' . e(mysqli_error($koneksi)));
        }

        mysqli_stmt_bind_param($query_stmt, "i", $guru_id);
        mysqli_stmt_execute($query_stmt);

        $query = mysqli_stmt_get_result($query_stmt);

    } else {

        $query = mysqli_query($koneksi, $sql);

    }

} else {

    // Admin dan role selain guru melihat seluruh laporan.
    $query = mysqli_query($koneksi, $sql);

}

if (!$query) {
    die('Gagal mengambil data: ' . e(mysqli_error($koneksi)));
}

$jumlah = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat Pelanggaran Siswa</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DATATABLES -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background-color: #f5f6fa;
            color: #212529;
        }

        /* =====================================
           KONTEN UTAMA
        ===================================== */
        .content {
            margin-left: 270px;
            width: calc(100% - 270px);
            min-height: 100vh;
            padding: 30px;
            box-sizing: border-box;
        }

        /* =====================================
           CARD
        ===================================== */
        .card {
            border: none;
            border-radius: 12px;
        }

        .card-body {
            padding: 25px;
        }

        h2 {
            color: #212529;
            font-weight: 600;
        }

        /* =====================================
           TABEL
        ===================================== */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        #tabelRiwayat {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        #tabelRiwayat th,
        #tabelRiwayat td {
            vertical-align: middle;
            white-space: nowrap;
            padding: 12px;
        }

        /* HEADER TABEL PUTIH */
        #tabelRiwayat thead th,
        #tabelRiwayat.table-striped thead th {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #212529 !important;
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: 600 !important;
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }

        /* ISI TABEL */
        #tabelRiwayat tbody td {
            color: #212529;
            border: 1px solid #dee2e6;
        }

        /* =====================================
           DATATABLES
        ===================================== */
        .dataTables_wrapper {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 6px 10px;
            margin-left: 5px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 5px 8px;
        }

        /* =====================================
           RESPONSIVE
        ===================================== */
        @media (max-width: 767.98px) {

            .content {
                margin-left: 0;
                width: 100%;
                padding: 18px;
            }

            .card-body {
                padding: 18px;
            }

            h2 {
                font-size: 22px;
            }

        }

        /* =====================================
           CETAK
        ===================================== */
        @media print {

            @page {
                size: landscape;
                margin: 10mm;
            }

            body {
                background: #ffffff !important;
            }

            aside,
            nav,
            .sidebar,
            #sidebar,
            .no-print,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_paginate,
            .dataTables_info {
                display: none !important;
            }

            .content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            #tabelRiwayat {
                width: 100% !important;
                font-size: 10px;
            }

            #tabelRiwayat thead th {
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            #tabelRiwayat th,
            #tabelRiwayat td {
                white-space: normal !important;
                padding: 6px !important;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid p-0">

    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN RIWAYAT -->
    <main class="content">

        <!-- JUDUL -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

            <div>

                <h2>Riwayat Pelanggaran Siswa</h2>

                <p class="text-muted mb-0">

                    <?php if ($is_guru) { ?>

                        Riwayat pelanggaran yang Anda laporkan.

                    <?php } else { ?>

                        Daftar seluruh catatan pelanggaran siswa.

                    <?php } ?>

                </p>

            </div>

            <a href="dashboard.php" class="btn btn-secondary no-print">
                Kembali ke Dashboard
            </a>

        </div>


        <!-- PESAN ERROR -->
        <?php if ($pesan_error !== ''): ?>

            <div class="alert alert-warning">
                <?= e($pesan_error) ?>
            </div>

        <?php endif; ?>


        <!-- RINGKASAN -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h6 class="text-muted">
                    Jumlah Riwayat Ditampilkan
                </h6>

                <h3 class="fw-bold text-primary">
                    <?= number_format($jumlah) ?> data
                </h3>

            </div>

        </div>


        <!-- PENCARIAN DAN FILTER -->
        <div class="card shadow-sm mb-4 no-print">

            <div class="card-body">

                <h5 class="mb-4">
                    Pencarian dan Filter Riwayat
                </h5>

                <form method="GET" action="riwayat.php">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label for="cari" class="form-label">
                                Cari Riwayat
                            </label>

                            <input
                                type="text"
                                name="cari"
                                id="cari"
                                class="form-control"
                                placeholder="Nama siswa, pelanggaran, kelas, guru..."
                                value="<?= e($cari) ?>">

                        </div>


                        <div class="col-md-3">

                            <label for="status" class="form-label">
                                Filter Status
                            </label>

                            <select name="status" id="status" class="form-select">

                                <option value="">Semua Status</option>

                                <option value="Proses"
                                    <?= $status === 'Proses' ? 'selected' : '' ?>>
                                    Proses
                                </option>

                                <option value="Ditindaklanjuti"
                                    <?= $status === 'Ditindaklanjuti' ? 'selected' : '' ?>>
                                    Ditindaklanjuti
                                </option>

                                <option value="Selesai"
                                    <?= $status === 'Selesai' ? 'selected' : '' ?>>
                                    Selesai
                                </option>

                            </select>

                        </div>


                        <div class="col-md-3 d-flex align-items-end gap-2">

                            <button type="submit" class="btn btn-primary">
                                Cari
                            </button>

                            <a href="riwayat.php" class="btn btn-secondary">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <!-- TABEL RIWAYAT -->
        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-4">
                    Data Riwayat Pelanggaran
                </h5>

                <div class="table-responsive">

                    <table id="tabelRiwayat"
                           class="table table-striped table-bordered table-hover">

                        <thead>

                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Poin</th>
                                <th>Keterangan</th>
                                <th>Guru Pencatat</th>
                                <th>Tindakan</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($query)):
                            ?>

                                <tr>

                                    <td class="text-center">
                                        <?= $no++ ?>
                                    </td>

                                    <td>
                                        <?= e($data['tanggal'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_siswa'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_kelas'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_pelanggaran'] ?? '-') ?>
                                    </td>

                                    <td class="text-center">

                                        <span class="badge bg-warning text-dark">
                                            <?= e($data['poin'] ?? 0) ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?= e($data['keterangan'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= e($data['nama_guru'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            trim((string)($data['tindakan'] ?? '')) !== ''
                                                ? $data['tindakan']
                                                : '-'
                                        ) ?>
                                    </td>

                                    <td class="text-center">

                                        <?php
                                        $status_data = strtolower(
                                            trim($data['status'] ?? '')
                                        );

                                        if ($status_data === 'selesai') {
                                            echo '<span class="badge bg-success">Selesai</span>';

                                        } elseif ($status_data === 'proses') {
                                            echo '<span class="badge bg-warning text-dark">Proses</span>';

                                        } elseif ($status_data === 'ditindaklanjuti') {
                                            echo '<span class="badge bg-info text-dark">Ditindaklanjuti</span>';

                                        } else {
                                            echo '<span class="badge bg-secondary">'
                                                . e($data['status'] ?? '-')
                                                . '</span>';
                                        }
                                        ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>

</div>


<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DATATABLES JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>


<script>

$(document).ready(function () {

    $('#tabelRiwayat').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,
        ordering: true,
        paging: true,
        info: true,

        autoWidth: false,
        scrollX: true,

        order: [[1, 'desc']],

        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 0
            }
        ],

        drawCallback: function () {

            var api = this.api();
            var nomorAwal = api.page.info().start;

            api.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {

                cell.innerHTML = nomorAwal + i + 1;

            });

        },

        language: {

            search: "Cari di tabel:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data riwayat",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Riwayat pelanggaran tidak ditemukan",

            emptyTable: "Belum ada riwayat pelanggaran",

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
