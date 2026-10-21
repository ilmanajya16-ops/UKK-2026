
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Me</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">
            <h2>About Me</h2>

        <a href="dashboard.php" class="btn btn-primary btn-sm mb-3">
            &larr; Kembali ke Dashboard
        </a>


            <p class="text-secondary">
                Halaman profil administrator.
            </p>

            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <h4 class="text-primary">Halo, Admin!</h4>
                    <p>Selamat datang di halaman About Me.</p>
                    <hr>

                    <p><strong>Nama:</strong> Ilma Najya</p>
                    <p><strong>Kelas:</strong> XII RPL</p>
                    <p><strong>Sekolah:</strong> SMK Muhammadiyah Tasikmalaya</p>
                    <p><strong>Jurusan:</strong> Rekayasa Perangkat Lunak</p>
                    <p><strong>Hobi:</strong> Menyanyi dan Memasak</p>
                </div>
            </div>
        </main>

    </div>
</div>

</body>
</html>
