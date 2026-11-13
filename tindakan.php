<?php
// tindakan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$pesan_error = '';

// ============================================
// PROSES MENYIMPAN TINDAKAN
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    $tindakan = trim($_POST['tindakan'] ?? '');

    if ($id <= 0 || $tindakan === '') {

        $_SESSION['pesan_error'] = "Data tindakan tidak boleh kosong.";

        header('Location: tindakan.php');
        exit;

    }

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE t_pelanggaran_siswa
         SET tindakan = ?
         WHERE id = ?"
    );

    if (!$stmt) {
        $_SESSION['pesan_error'] = mysqli_error($koneksi);
        header('Location: tindakan.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, "si", $tindakan, $id);

    if (mysqli_stmt_execute($stmt)) {

        $_SESSION['pesan_sukses'] = "Tindakan berhasil disimpan.";

    } else {

        $_SESSION['pesan_error'] = mysqli_stmt_error($stmt);

    }

    mysqli_stmt_close($stmt);

    header('Location: tindakan.php');
    exit;
}

// ============================================
// MENGAMBIL PESAN
// ============================================

if (isset($_SESSION['pesan_sukses'])) {
    $pesan_sukses = $_SESSION['pesan_sukses'];
    unset($_SESSION['pesan_sukses']);
} else {
    $pesan_sukses = '';
}

if (isset($_SESSION['pesan_error'])) {
    $pesan_error = $_SESSION['pesan_error'];
    unset($_SESSION['pesan_error']);
}

// ============================================
// MENGAMBIL DATA PELANGGARAN
// HANYA YANG BELUM MEMILIKI TINDAKAN
// ============================================

$sql = "SELECT
            ps.id,
            COALESCE(NULLIF(ps.nama_siswa, ''), s.nama, '-')           AS nama_siswa,
            COALESCE(NULLIF(ps.nama_kelas, ''), k.nama, '-')           AS nama_kelas,
            COALESCE(NULLIF(ps.nama_pelanggaran, ''), p.nama, '-')     AS nama_pelanggaran,
            ps.tanggal,
            ps.keterangan,
            ps.tindakan
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s       ON s.id = ps.siswa_id
        LEFT JOIN t_kelas k       ON k.id = ps.kelas_id
        LEFT JOIN t_pelanggaran p ON p.id = ps.pelanggaran_id
        WHERE ps.tindakan IS NULL
           OR TRIM(ps.tindakan) = ''
        ORDER BY ps.tanggal DESC, ps.id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die('Gagal mengambil data: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tindakan Pelanggaran Siswa</title>

    <!-- Bootstrap 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"
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

        textarea {
            min-width: 180px;
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

        <h2 class="mb-2">
            Tindakan Pelanggaran Siswa
        </h2>

        <p class="text-muted">
            Kelola tindakan untuk setiap pelanggaran siswa.
        </p>

        <!-- TOMBOL KEMBALI -->
        <div class="tombol-aksi">

            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Dashboard
            </a>

        </div>

        <!-- PESAN BERHASIL -->
        <?php if (!empty($pesan_sukses)) { ?>

            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle-fill"></i>

                <?= htmlspecialchars($pesan_sukses); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php } ?>

        <!-- PESAN ERROR -->
        <?php if (!empty($pesan_error)) { ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <i class="bi bi-exclamation-triangle-fill"></i>

                <?= htmlspecialchars($pesan_error); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php } ?>

        <!-- CARD DATA -->
        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-4">
                    <i class="bi bi-clipboard-check"></i>
                    Data Pelanggaran Siswa
                </h5>

                <div class="table-responsive">

                    <table id="tabelTindakan"
                           class="table table-striped table-bordered table-hover">

                        <thead class="table-dark">

                            <tr>
                                <th>No.</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Tindakan</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                <tr>

                                    <td></td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(trim((string) ($row['keterangan'] ?? '')) !== '' ? $row['keterangan'] : '-'); ?>
                                    </td>

                                    <!-- FORM TINDAKAN -->
                                    <td>

                                        <form
                                            id="formTindakan<?= (int) $row['id']; ?>"
                                            method="POST"
                                            action="tindakan.php">

                                            <input type="hidden"
                                                   name="id"
                                                   value="<?= (int) $row['id']; ?>">

                                            <textarea
                                                name="tindakan"
                                                class="form-control"
                                                rows="2"
                                                placeholder="Masukkan tindakan..."
                                                required></textarea>

                                        </form>

                                    </td>

                                    <!-- TOMBOL SIMPAN -->
                                    <td>

                                        <button
                                            type="submit"
                                            form="formTindakan<?= (int) $row['id']; ?>"
                                            class="btn btn-success btn-sm">

                                            <i class="bi bi-save"></i>
                                            Simpan

                                        </button>

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

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- INISIALISASI DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelTindakan').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,

        ordering: true,

        paging: true,

        info: true,

        autoWidth: false,

        scrollX: true,

        order: [[4, 'desc']],

        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: [0, 7]
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

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data pelanggaran",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data pelanggaran tidak ditemukan",

            emptyTable: "Belum ada pelanggaran yang perlu diberi tindakan",

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