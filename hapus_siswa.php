<?php
// hapus_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// FUNGSI ESCAPE
// ==========================================
function e($data)
{
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// ==========================================
// PROSES HAPUS SISWA
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil ID dari form POST
    $id = $_POST['id'] ?? '';

    if (!is_scalar($id) || !ctype_digit((string)$id) || (int)$id <= 0) {
        die("ID siswa tidak valid.");
    }

    $id = (int)$id;

    // Periksa apakah siswa masih tersedia
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_siswa WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $cek = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$cek) {
        echo "<script>
            alert('Data siswa tidak ditemukan!');
            window.location.href = 'kelola_siswa.php';
        </script>";
        exit;
    }

    // Hapus data siswa
    $stmt_hapus = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_siswa WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt_hapus, "i", $id);

    if (mysqli_stmt_execute($stmt_hapus)) {

        $jumlah = mysqli_stmt_affected_rows($stmt_hapus);

        mysqli_stmt_close($stmt_hapus);

        if ($jumlah > 0) {
            echo "<script>
                alert('Data siswa berhasil dihapus!');
                window.location.href = 'kelola_siswa.php';
            </script>";
            exit;
        }

    } else {

        mysqli_stmt_close($stmt_hapus);

        echo "<script>
            alert('Data siswa gagal dihapus! Siswa mungkin masih memiliki riwayat pelanggaran atau data kelas.');
            window.location.href = 'kelola_siswa.php';
        </script>";
        exit;
    }

    header("Location: kelola_siswa.php");
    exit;
}

// ==========================================
// AMBIL ID DARI URL GET
// ==========================================
$id = $_GET['id'] ?? '';

if (!is_scalar($id) || !ctype_digit((string)$id) || (int)$id <= 0) {
    echo "<script>
        alert('ID siswa tidak valid!');
        window.location.href = 'kelola_siswa.php';
    </script>";
    exit;
}

$id = (int)$id;

// ==========================================
// AMBIL DATA SISWA
// ==========================================
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$siswa) {
    echo "<script>
        alert('Data siswa tidak ditemukan!');
        window.location.href = 'kelola_siswa.php';
    </script>";
    exit;
}

// ==========================================
// JENIS KELAMIN
// ==========================================
if ($siswa['jenis_kelamin'] === 'L') {
    $jenis_kelamin = 'Laki-laki';
} elseif ($siswa['jenis_kelamin'] === 'P') {
    $jenis_kelamin = 'Perempuan';
} else {
    $jenis_kelamin = $siswa['jenis_kelamin'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hapus Siswa</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }

        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .card-header {
            font-weight: 600;
        }

        .table th {
            background: #f8f9fa;
            width: 30%;
        }

        @media (max-width: 768px) {
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

        <!-- KONTEN -->
        <main class="content">

            <h3 class="mb-4">Hapus Siswa</h3>

            <div class="card shadow-sm">

                <div class="card-header bg-danger text-white">
                    Konfirmasi Hapus Siswa
                </div>

                <div class="card-body">

                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong>
                        Apakah Anda yakin ingin menghapus data siswa ini?
                    </div>

                    <table class="table table-bordered">

                        <tr>
                            <th>ID Siswa</th>
                            <td><?= e($siswa['id']) ?></td>
                        </tr>

                        <tr>
                            <th>NIS</th>
                            <td><?= e($siswa['nis']) ?></td>
                        </tr>

                        <tr>
                            <th>NISN</th>
                            <td><?= e($siswa['nisn']) ?></td>
                        </tr>

                        <tr>
                            <th>Nama Siswa</th>
                            <td><?= e($siswa['nama']) ?></td>
                        </tr>

                        <tr>
                            <th>Jenis Kelamin</th>
                            <td><?= e($jenis_kelamin) ?></td>
                        </tr>

                    </table>

                    <!-- FORM HAPUS -->
                    <form method="POST" action="hapus_siswa.php">

                        <input type="hidden"
                               name="id"
                               value="<?= (int)$siswa['id'] ?>">

                        <button type="submit"
                                class="btn btn-danger"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini secara permanen?')">

                            Hapus Siswa

                        </button>

                        <a href="kelola_siswa.php"
                           class="btn btn-secondary">

                            Batal

                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>