
<?php
// penempatan_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// MENGAMBIL DATA SISWA YANG BELUM DITEMPATKAN
// ==========================================

$siswa = mysqli_query(
    $koneksi,
    "SELECT s.id, s.nis, s.nama
     FROM t_siswa s
     WHERE s.status_aktif = 1
     AND EXISTS (
         SELECT 1
         FROM t_tahun_ajaran ta
         WHERE ta.status_aktif = 1
     )
     AND NOT EXISTS (
         SELECT 1
         FROM t_kelas_siswa ks
         INNER JOIN t_tahun_ajaran ta
             ON ks.tahun_ajaran_id = ta.id
         WHERE ks.siswa_id = s.id
         AND ta.status_aktif = 1
     )
     ORDER BY s.nama ASC"
);

if (!$siswa) {
    die("Gagal mengambil data siswa: " . mysqli_error($koneksi));
}

// ==========================================
// MENGAMBIL TAHUN AJARAN AKTIF
// ==========================================

$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

if (!$tahun) {
    die("Gagal mengambil tahun ajaran: " . mysqli_error($koneksi));
}

// ==========================================
// MENGAMBIL KELAS AKTIF
// ==========================================

$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

if (!$kelas) {
    die("Gagal mengambil data kelas: " . mysqli_error($koneksi));
}

// ==========================================
// MENGAMBIL DATA PENEMPATAN SISWA
// ==========================================

$sql = "SELECT
            ks.id,
            s.nis,
            s.nama AS nama_siswa,
            ta.nama AS tahun_ajaran,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            ks.tanggal_mulai,
            ks.tanggal_selesai,
            ks.status_aktif
        FROM t_kelas_siswa ks
        LEFT JOIN t_siswa s ON ks.siswa_id = s.id
        LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        ORDER BY ks.id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data penempatan: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penempatan Siswa</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DATATABLES CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <!-- STYLE -->
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

            <!-- CARD UTAMA -->
            <div class="card shadow-sm">

                <div class="card-body">

                    <h2 class="mb-3">Penempatan Siswa</h2>

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

                    </div>

                    <!-- FORM PENEMPATAN SISWA -->
                    <div class="card shadow-sm mb-4">

                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Form Penempatan Siswa</h5>
                        </div>

                        <div class="card-body">

                            <form action="proses_simpan_penempatan.php"
                                  method="POST">

                                <div class="row">

                                    <!-- PILIH SISWA -->
                                    <div class="col-md-6 mb-3">

                                        <label for="siswa_id" class="form-label">
                                            Siswa
                                        </label>

                                        <select name="siswa_id"
                                                id="siswa_id"
                                                class="form-select"
                                                required>

                                            <option value="">
                                                -- Pilih Siswa --
                                            </option>

                                            <?php if (mysqli_num_rows($siswa) > 0) { ?>

                                                <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                                    <option value="<?= htmlspecialchars($s['id']); ?>">
                                                        <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                                    </option>

                                                <?php } ?>

                                            <?php } else { ?>

                                                <option value="" disabled>
                                                    Tidak ada siswa yang belum ditempatkan
                                                </option>

                                            <?php } ?>

                                        </select>

                                    </div>

                                    <!-- PILIH TAHUN AJARAN -->
                                    <div class="col-md-6 mb-3">

                                        <label for="tahun_ajaran_id" class="form-label">
                                            Tahun Ajaran
                                        </label>

                                        <select name="tahun_ajaran_id"
                                                id="tahun_ajaran_id"
                                                class="form-select"
                                                required>

                                            <option value="">
                                                -- Pilih Tahun Ajaran --
                                            </option>

                                            <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>

                                                <option value="<?= htmlspecialchars($ta['id']); ?>">
                                                    <?= htmlspecialchars($ta['nama']); ?>
                                                </option>

                                            <?php } ?>

                                        </select>

                                    </div>

                                    <!-- PILIH KELAS -->
                                    <div class="col-md-6 mb-3">

                                        <label for="kelas_id" class="form-label">
                                            Kelas
                                        </label>

                                        <select name="kelas_id"
                                                id="kelas_id"
                                                class="form-select"
                                                required>

                                            <option value="">
                                                -- Pilih Kelas --
                                            </option>

                                            <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>

                                                <option value="<?= htmlspecialchars($k['id']); ?>">
                                                    <?= htmlspecialchars(
                                                        $k['nama'] . ' - Tingkat ' .
                                                        $k['tingkat'] . ' - ' .
                                                        $k['jurusan']
                                                    ); ?>
                                                </option>

                                            <?php } ?>

                                        </select>

                                    </div>

                                    <!-- TANGGAL MULAI -->
                                    <div class="col-md-6 mb-3">

                                        <label for="tanggal_mulai" class="form-label">
                                            Tanggal Mulai
                                        </label>

                                        <input type="date"
                                               name="tanggal_mulai"
                                               id="tanggal_mulai"
                                               class="form-control"
                                               required>

                                    </div>

                                    <!-- TANGGAL SELESAI -->
                                    <div class="col-md-6 mb-3">

                                        <label for="tanggal_selesai" class="form-label">
                                            Tanggal Selesai
                                        </label>

                                        <input type="date"
                                               name="tanggal_selesai"
                                               id="tanggal_selesai"
                                               class="form-control"
                                               required>

                                    </div>

                                    <!-- STATUS -->
                                    <div class="col-md-6 mb-3">

                                        <label for="status_aktif" class="form-label">
                                            Status
                                        </label>

                                        <select name="status_aktif"
                                                id="status_aktif"
                                                class="form-select"
                                                required>

                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>

                                        </select>

                                    </div>

                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Simpan Penempatan
                                </button>

                            </form>

                        </div>

                    </div>

                    <!-- TABEL DATA PENEMPATAN -->
                    <h5 class="mb-3">Daftar Penempatan Siswa</h5>

                    <div class="table-responsive">

                        <table id="tabelPenempatan"
                               class="table table-striped table-bordered"
                               style="width:100%">

                            <thead class="thead-dark">

                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status Aktif</th>
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
                                            <?= htmlspecialchars($row['nis'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tingkat'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['jurusan'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tanggal_mulai'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($row['tanggal_selesai'] ?? '-'); ?>
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

<!-- BOOTSTRAP JS SAMA SEPERTI KELOLA SISWA -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DATATABLES JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- DATATABLES -->
<script>

$(document).ready(function () {

    $('#tabelPenempatan').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        ordering: true,

        searching: true,

        paging: true,

        info: true,

        autoWidth: false,

        language: {

            search: "Cari penempatan:",

            lengthMenu: "Tampilkan _MENU_ data",

            zeroRecords: "Data penempatan tidak ditemukan",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data penempatan",

            infoFiltered: "(difilter dari _MAX_ total data)",

            emptyTable: "Belum ada data penempatan",

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
