
<?php
// dashboard.php

// ============================================
// 1. KONEKSI DAN SESSION
// ============================================
include 'includes/cek_session.php';
include 'config/koneksi.php';

$role = $_SESSION['role'] ?? '';

// ============================================
// 2. MENGHITUNG JUMLAH SISWA
// ============================================
$query_siswa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_siswa");

if (!$query_siswa) {
    die("Gagal mengambil data siswa: " . mysqli_error($koneksi));
}

$total_siswa = mysqli_fetch_assoc($query_siswa)['total'];

// ============================================
// 3. MENGHITUNG JUMLAH GURU
// ============================================
$query_guru = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM t_guru g
     INNER JOIN t_users u ON g.user_id = u.id
     WHERE u.role = 'guru'"
);

if (!$query_guru) {
    die("Gagal mengambil data guru: " . mysqli_error($koneksi));
}

$total_guru = mysqli_fetch_assoc($query_guru)['total'];

// ============================================
// 4. MENGHITUNG JUMLAH KELAS
// ============================================
$query_kelas = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_kelas");

if (!$query_kelas) {
    die("Gagal mengambil data kelas: " . mysqli_error($koneksi));
}

$total_kelas = mysqli_fetch_assoc($query_kelas)['total'];

// ============================================
// 5. MENGHITUNG JUMLAH TAHUN AJARAN
// ============================================
$query_tahun = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM t_tahun_ajaran"
);

if (!$query_tahun) {
    die("Gagal mengambil tahun ajaran: " . mysqli_error($koneksi));
}

$total_tahun = mysqli_fetch_assoc($query_tahun)['total'];

// ============================================
// 6. MENGHITUNG CATATAN PELANGGARAN
// ============================================
$query_pelanggaran = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total FROM t_pelanggaran_siswa"
);

if (!$query_pelanggaran) {
    die("Gagal mengambil data pelanggaran: " . mysqli_error($koneksi));
}

$total_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)['total'];

// ============================================
// 7. MENGHITUNG TOTAL POIN PELANGGARAN
// ============================================
$query_poin = mysqli_query(
    $koneksi,
    "SELECT COALESCE(SUM(poin), 0) AS total
     FROM t_pelanggaran_siswa"
);

if (!$query_poin) {
    die("Gagal menghitung poin pelanggaran: " . mysqli_error($koneksi));
}

$total_poin = mysqli_fetch_assoc($query_poin)['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard</title>

    <!-- Bootstrap 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
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
            transition: 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card-body {
            padding: 25px;
        }

        h2 {
            color: #212529;
            font-weight: 600;
        }

        .angka {
            font-size: 2rem;
            font-weight: bold;
        }

        .card-title {
            font-weight: 600;
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

    <!-- KONTEN DASHBOARD -->
    <main class="content">

        <div class="mb-4">
            <h2>Dashboard</h2>

<p class="text-muted">
    Selamat datang,
    <strong>
        <?= htmlspecialchars($_SESSION['name'] ?? 'Pengguna'); ?>
    </strong>
    <span>
        (<?= htmlspecialchars(
            $role == 'admin' ? 'Admin' :
            ($role == 'guru' ? 'Guru' :
            ($role == 'wali_kelas' ? 'Wali Kelas' : 'Pengguna'))
        ); ?>)
    </span>
    di halaman dashboard.
</p>

        <?php if ($role == 'admin') : ?>

            <!-- ================================= -->
            <!-- DASHBOARD KHUSUS ADMIN -->
            <!-- ================================= -->

            <div class="row g-3">

                <!-- DATA SISWA -->
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-start border-primary border-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Data Siswa</h5>
                            <h2 class="angka text-primary">
                                <?= number_format((int) $total_siswa); ?>
                            </h2>
                            <a href="kelola_siswa.php" class="btn btn-primary btn-sm">
                                Lihat Data Siswa
                            </a>
                        </div>
                    </div>
                </div>

                <!-- DATA GURU -->
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-start border-success border-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Data Guru</h5>
                            <h2 class="angka text-success">
                                <?= number_format((int) $total_guru); ?>
                            </h2>
                            <a href="kelola_guru.php" class="btn btn-success btn-sm">
                                Lihat Data Guru
                            </a>
                        </div>
                    </div>
                </div>

                <!-- DATA KELAS -->
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-start border-warning border-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Data Kelas</h5>
                            <h2 class="angka text-warning">
                                <?= number_format((int) $total_kelas); ?>
                            </h2>
                            <a href="kelola_kelas.php" class="btn btn-warning btn-sm">
                                Lihat Data Kelas
                            </a>
                        </div>
                    </div>
                </div>

               

        <?php elseif ($role == 'guru') : ?>

            <!-- ================================= -->
            <!-- DASHBOARD KHUSUS GURU -->
            <!-- ================================= -->

            <div class="row g-3">

                <!-- CATAT PELANGGARAN -->
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-start border-primary border-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Catat Pelanggaran</h5>
                            <p class="text-muted">
                                Melakukan pencatatan pelanggaran siswa.
                            </p>
                            <a href="catat_pelanggaran.php" class="btn btn-primary btn-sm">
                                Catat Pelanggaran
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TINDAKAN -->
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-start border-warning border-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Tindakan</h5>
                            <p class="text-muted">
                                Melihat dan mengelola tindakan pelanggaran siswa.
                            </p>
                            <a href="tindakan.php" class="btn btn-warning btn-sm">
                                Lihat Tindakan
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIWAYAT -->
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-start border-info border-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Riwayat Pelanggaran</h5>
                            <p class="text-muted">
                                Melihat seluruh riwayat pelanggaran siswa.
                            </p>
                            <a href="riwayat.php" class="btn btn-info btn-sm">
                                Lihat Riwayat
                            </a>
                        </div>
                    </div>
                </div>

                <!-- REKAP POIN -->
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-start border-secondary border-4 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Poin Pelanggaran</h5>
                            <h2 class="angka text-secondary">
                                <?= number_format((int) $total_poin); ?>
                            </h2>
                            <a href="rekap_poin.php" class="btn btn-secondary btn-sm">
                                Lihat Rekap Poin
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        <?php else : ?>

            <div class="alert alert-danger">
                Role pengguna tidak dikenali.
            </div>

        <?php endif; ?>

    </main>

</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
