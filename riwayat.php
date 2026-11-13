<?php
// riwayat.php

// Memeriksa session login
include 'includes/cek_session.php';

// Menghubungkan ke database
include 'config/koneksi.php';

$guru_id = 0;

if (($_SESSION['role'] ?? '') == 'guru') {

    $id_user = (int) ($_SESSION['id'] ?? 0);

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_guru
         WHERE user_id = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    if ($data_guru) {
        $guru_id = (int) $data_guru['id'];
    }

    mysqli_stmt_close($stmt_guru);
}


// Mengambil kata pencarian
$cari = isset($_GET['cari'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['cari']))
    : '';

// Mengambil filter status
$status = isset($_GET['status'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['status']))
    : '';
$sql = "SELECT
            ps.id,
            ps.tanggal,
            ps.poin,
            ps.keterangan,
            ps.tindakan,
            ps.status,
            COALESCE(NULLIF(ps.nama_siswa, ''), s.nama, '-')       AS nama_siswa,
            COALESCE(NULLIF(ps.nama_kelas, ''), k.nama, '-')       AS nama_kelas,
            COALESCE(NULLIF(ps.nama_pelanggaran, ''), p.nama, '-') AS nama_pelanggaran,
            COALESCE(NULLIF(ps.nama_guru, ''), g.nama, '-')        AS nama_guru
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s       ON s.id = ps.siswa_id
        LEFT JOIN t_kelas k       ON k.id = ps.kelas_id
        LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
        LEFT JOIN t_guru g        ON g.id = ps.guru_id
        WHERE 1=1
          AND LOWER(TRIM(COALESCE(ps.status, ''))) <> 'selesai'";

if (($_SESSION['role'] ?? '') == 'guru') {

    if ($guru_id > 0) {
        $sql .= " AND ps.guru_id = " . (int) $guru_id;
    } else {
        $sql .= " AND 1=0";
    }

}

// Pencarian berdasarkan nama siswa, pelanggaran, kelas, atau guru
if ($cari != '') {
    $sql .= " AND (
        COALESCE(NULLIF(ps.nama_siswa, ''), s.nama) LIKE '%$cari%'
        OR COALESCE(NULLIF(ps.nama_pelanggaran, ''), p.nama) LIKE '%$cari%'
        OR COALESCE(NULLIF(ps.nama_kelas, ''), k.nama) LIKE '%$cari%'
        OR COALESCE(NULLIF(ps.nama_guru, ''), g.nama) LIKE '%$cari%'
    )";
}

// Filter berdasarkan status (nilai sama dengan yang disimpan di database)
if ($status != '') {
    $sql .= " AND ps.status = '$status'";
}

// Urutkan berdasarkan tanggal terbaru
$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// Menjalankan query
$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Gagal mengambil data: ' . mysqli_error($koneksi));
}

// Menghitung jumlah data sesuai filter
$jumlah = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat Pelanggaran Siswa</title>

    <!-- Bootstrap 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 4 -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding: 30px;
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

    <!-- KONTEN -->
    <main class="content">

        <!-- Judul halaman -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

            <div>
                <h2>Riwayat Pelanggaran Siswa</h2>

                <p class="text-muted mb-0">
                    Daftar catatan pelanggaran siswa yang telah dicatat.
                </p>
            </div>

            <a href="dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>

        </div>

        <!-- Ringkasan jumlah data -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h6 class="text-muted">
                    Jumlah Riwayat Ditampilkan
                </h6>

                <h3 class="fw-bold text-primary">
                    <?= $jumlah; ?> data
                </h3>

            </div>

        </div>

        <!-- Pencarian dan filter -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-4">Pencarian dan Filter Riwayat</h5>

                <form method="GET" action="riwayat.php">

                    <div class="row g-3">

                        <!-- Pencarian -->
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
                                value="<?= htmlspecialchars($cari); ?>">

                        </div>

                        <!-- Filter status -->
                        <div class="col-md-3">

                            <label for="status" class="form-label">
                                Filter Status
                            </label>

                            <select name="status"
                                    id="status"
                                    class="form-select">

                                <option value="">Semua Status</option>

                                <option value="Ditindaklanjuti"
                                    <?= $status == 'Ditindaklanjuti' ? 'selected' : ''; ?>>
                                    Ditindaklanjuti
                                </option>

                                <option value="Proses"
                                    <?= $status == 'Proses' ? 'selected' : ''; ?>>
                                    Proses
                                </option>

                            </select>

                        </div>

                        <!-- Tombol -->
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

        <!-- Tabel riwayat -->
        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-4">Data Riwayat Pelanggaran</h5>

                <div class="table-responsive">

                    <table id="tabelRiwayat"
                           class="table table-striped table-bordered table-hover">

                        <thead class="thead-dark">

                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Poin</th>
                                <th>Keterangan</th>
                                <th>Tindakan</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                                <tr>

                                    <!-- Nomor otomatis DataTables -->
                                    <td></td>

                                    <td>
                                        <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark">
                                            <?= htmlspecialchars((string) ($data['poin'] ?? 0)); ?>
                                        </span>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(trim((string) ($data['tindakan'] ?? '')) !== '' ? $data['tindakan'] : '-'); ?>
                                    </td>

                                    <td>

                                        <?php
                                        $status_data = strtolower(
                                            trim($data['status'] ?? '')
                                        );

                                        if ($status_data == 'proses') {

                                            echo '<span class="badge bg-warning text-dark">Proses</span>';

                                        } elseif ($status_data == 'ditindaklanjuti') {

                                            echo '<span class="badge bg-info text-dark">Ditindaklanjuti</span>';

                                        } else {

                                            echo '<span class="badge bg-secondary">'
                                                . htmlspecialchars($data['status'] ?? '-')
                                                . '</span>';

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

    </main>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS 4.1.3 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables 1.13.8 -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- Inisialisasi DataTables -->
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

        // Urutkan berdasarkan tanggal terbaru
        order: [[1, 'desc']],

        // Kolom nomor tidak diurutkan
        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 0
            }
        ],

        // Nomor otomatis menyesuaikan halaman
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

        // Bahasa Indonesia
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