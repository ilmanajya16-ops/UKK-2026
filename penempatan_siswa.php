<?php
// ==================================================
// PENEMPATAN SISWA
// File: penempatan_siswa.php
// ==================================================

include 'includes/cek_session.php';
include 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($data)
{
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// Siswa yang belum ditempatkan pada tahun ajaran aktif
$siswa = mysqli_query($koneksi, "
    SELECT s.id, s.nis, s.nama
    FROM t_siswa s
    WHERE s.status_aktif = 1
    AND NOT EXISTS (
        SELECT 1
        FROM t_kelas_siswa ks
        INNER JOIN t_tahun_ajaran ta ON ta.id = ks.tahun_ajaran_id
        WHERE ks.siswa_id = s.id
          AND ta.status_aktif = 1
    )
    ORDER BY s.nama ASC
");
if (!$siswa) {
    die('Gagal mengambil data siswa: ' . mysqli_error($koneksi));
}

// Tahun ajaran aktif
$tahun = mysqli_query($koneksi, "
    SELECT id, nama FROM t_tahun_ajaran
    WHERE status_aktif = 1
    ORDER BY id DESC
");
if (!$tahun) {
    die('Gagal mengambil tahun ajaran: ' . mysqli_error($koneksi));
}

// Kelas aktif
$kelas = mysqli_query($koneksi, "
    SELECT id, nama, tingkat, jurusan FROM t_kelas
    WHERE status_aktif = 1
    ORDER BY nama ASC
");
if (!$kelas) {
    die('Gagal mengambil data kelas: ' . mysqli_error($koneksi));
}

// Riwayat penempatan
$hasil = mysqli_query($koneksi, "
    SELECT
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
    LEFT JOIN t_siswa s ON s.id = ks.siswa_id
    LEFT JOIN t_tahun_ajaran ta ON ta.id = ks.tahun_ajaran_id
    LEFT JOIN t_kelas k ON k.id = ks.kelas_id
    ORDER BY ks.id DESC
");
if (!$hasil) {
    die('Gagal mengambil data penempatan: ' . mysqli_error($koneksi));
}

$jumlah_siswa = mysqli_num_rows($siswa);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>
        body { background: #f5f6fa; }
        .content { padding: 30px; }
        .card { border: none; border-radius: 12px; }
        .card-body { padding: 25px; }
        .judul { font-weight: 600; color: #212529; }
        .table th, .table td { vertical-align: middle; white-space: nowrap; }
        .table thead th {
            background-color: #ffffff !important;
            color: #212529 !important;
            border-bottom: 2px solid #dee2e6;
        }
        .table-responsive { overflow-x: auto; }
        .form-label { font-weight: 500; }
        .card-header { border-radius: 12px 12px 0 0 !important; }
        @media (max-width: 768px) { .content { padding: 15px; } }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 content">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="judul mb-4">Penempatan Siswa</h2>

                    <?php if (!empty($_SESSION['pesan_sukses'])) { ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= e($_SESSION['pesan_sukses']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['pesan_sukses']); ?>
                    <?php } ?>

                    <?php if (!empty($_SESSION['pesan_error'])) { ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= e($_SESSION['pesan_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['pesan_error']); ?>
                    <?php } ?>

                    <div class="mb-3">
                        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                    </div>

                    <!-- FORM PENEMPATAN -->
                    <div class="card shadow-sm mb-4">

                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Form Penempatan Siswa</h5>
                        </div>

                        <div class="card-body">

                            <?php if (mysqli_num_rows($tahun) == 0) { ?>

                                <div class="alert alert-warning">
                                    Belum ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.
                                </div>

                            <?php } elseif (mysqli_num_rows($kelas) == 0) { ?>

                                <div class="alert alert-warning">
                                    Belum ada kelas aktif. Tambahkan kelas terlebih dahulu.
                                </div>

                            <?php } else { ?>

                                <form action="proses_simpan_penempatan.php" method="POST">

                                    <div class="row">

                                        <!-- SISWA -->
                                        <div class="col-md-6 mb-3">
                                            <label for="siswa_id" class="form-label">Siswa</label>

                                            <select name="siswa_id" id="siswa_id" class="form-select" required>
                                                <option value="">-- Pilih Siswa --</option>

                                                <?php if ($jumlah_siswa > 0) { ?>
                                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                                        <option value="<?= e($s['id']); ?>">
                                                            <?= e($s['nis'] . ' - ' . $s['nama']); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <option value="" disabled>Tidak ada siswa yang belum ditempatkan</option>
                                                <?php } ?>
                                            </select>

                                            <?php if ($jumlah_siswa == 0) { ?>
                                                <small class="text-muted">
                                                    Semua siswa aktif sudah memiliki penempatan pada tahun ajaran aktif.
                                                </small>
                                            <?php } ?>
                                        </div>

                                        <!-- TAHUN AJARAN -->
                                        <div class="col-md-6 mb-3">
                                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>

                                            <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select" required>
                                                <option value="">-- Pilih Tahun Ajaran --</option>

                                                <?php
                                                mysqli_data_seek($tahun, 0);
                                                while ($ta = mysqli_fetch_assoc($tahun)) {
                                                ?>
                                                    <option value="<?= e($ta['id']); ?>">
                                                        <?= e($ta['nama']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!-- KELAS -->
                                        <div class="col-md-6 mb-3">
                                            <label for="kelas_id" class="form-label">Kelas</label>

                                            <select name="kelas_id" id="kelas_id" class="form-select" required>
                                                <option value="">-- Pilih Kelas --</option>

                                                <?php
                                                mysqli_data_seek($kelas, 0);
                                                while ($k = mysqli_fetch_assoc($kelas)) {
                                                ?>
                                                    <option value="<?= e($k['id']); ?>">
                                                        <?= e($k['nama'] . ' - Tingkat ' . $k['tingkat'] . ' - ' . $k['jurusan']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!-- TANGGAL MULAI -->
                                        <div class="col-md-6 mb-3">
                                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                                        </div>

                                        <!-- TANGGAL SELESAI -->
                                        <div class="col-md-6 mb-3">
                                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                                        </div>

                                        <!-- STATUS -->
                                        <div class="col-md-6 mb-3">
                                            <label for="status_aktif" class="form-label">Status</label>

                                            <select name="status_aktif" id="status_aktif" class="form-select" required>
                                                <option value="1">Aktif</option>
                                                <option value="0">Tidak Aktif</option>
                                            </select>
                                        </div>

                                    </div>

                                    <button type="submit" class="btn btn-primary" <?= $jumlah_siswa == 0 ? 'disabled' : ''; ?>>
                                        Simpan Penempatan
                                    </button>

                                </form>

                            <?php } ?>

                        </div>
                    </div>

                    <!-- TABEL PENEMPATAN -->
                    <h5 class="mb-3">Daftar Penempatan Siswa</h5>

                    <div class="table-responsive">

                        <table id="tabelPenempatan" class="table table-striped table-bordered" style="width:100%">

                            <thead>
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
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($hasil)) { ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= e($row['nis'] ?? '-'); ?></td>
                                        <td><?= e($row['nama_siswa'] ?? '-'); ?></td>
                                        <td><?= e($row['tahun_ajaran'] ?? '-'); ?></td>
                                        <td><?= e($row['nama_kelas'] ?? '-'); ?></td>
                                        <td><?= e($row['tingkat'] ?? '-'); ?></td>
                                        <td><?= e($row['jurusan'] ?? '-'); ?></td>
                                        <td><?= e($row['tanggal_mulai'] ?? '-'); ?></td>
                                        <td><?= e($row['tanggal_selesai'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ((int)$row['status_aktif'] === 1) { ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

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

    // Tanggal selesai tidak boleh sebelum tanggal mulai
    $('#tanggal_mulai').on('change', function () {
        $('#tanggal_selesai').attr('min', $(this).val());
    });

});
</script>

</body>
</html>