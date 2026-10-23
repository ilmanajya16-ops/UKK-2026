
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
    <title>About Me | Profil Saya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: Arial, sans-serif;
        }

        .judul {
            font-weight: bold;
            color: #263b60;
        }

        .profile-card {
            background: white;
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(135deg, #4776e6, #8e54e9);
            height: 160px;
        }

        .foto-profil {
            width: 155px;
            height: 155px;
            object-fit: cover;
            border: 5px solid white;
            margin-top: -80px;
            background: white;
        }

        .nama-profil {
            color: #263b60;
            font-weight: bold;
        }

        .badge-jurusan {
            background: #e8efff;
            color: #4169c1;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
        }

        .info-card {
            border: none;
            border-radius: 15px;
            background: white;
            height: 100%;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
        }

        .icon-box {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: #edf2ff;
            color: #4776e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            flex-shrink: 0;
        }

        .label-info {
            color: #8490a5;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .isi-info {
            color: #263b60;
            font-weight: 600;
            margin-bottom: 0;
        }

        .hobi-box {
            background: #f3f5ff;
            color: #4c63b6;
            padding: 12px 18px;
            border-radius: 12px;
            display: inline-block;
            margin: 5px;
        }

        .btn-kembali {
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <!-- Judul halaman -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h2 class="judul mb-1">About Me</h2>
                    <p class="text-secondary mb-0">
                        Kenali lebih dekat tentang diri saya.
                    </p>
                </div>

                <a href="dashboard.php" class="btn btn-primary btn-sm btn-kembali mt-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>

            <!-- Kartu profil utama -->
            <div class="card profile-card shadow-sm mb-4">

                <div class="profile-header"></div>

                <div class="card-body text-center pb-4">

                    <img src="img/image.png"
                         alt="Foto profil Ilma Najya"
                         class="rounded-circle foto-profil shadow-sm">

                    <h3 class="nama-profil mt-3 mb-2">
                        Ilma Najya
                    </h3>

                    <span class="badge-jurusan">
                        <i class="bi bi-laptop"></i>
                        Rekayasa Perangkat Lunak
                    </span>

                    <p class="text-secondary mt-3 mb-0">
                        Halo! Selamat datang di halaman profil saya.
                        Saya merupakan siswi yang sedang belajar
                        pemrograman dan pengembangan website.
                    </p>

                </div>
            </div>

            <!-- Biodata -->
            <h5 class="judul mb-3">
                <i class="bi bi-person-vcard"></i>
                Biodata Saya
            </h5>

            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <div class="card info-card p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-3">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <p class="label-info">Nama Lengkap</p>
                                <p class="isi-info">Ilma Najya</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card info-card p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-3">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <div>
                                <p class="label-info">Kelas</p>
                                <p class="isi-info">XII RPL</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card info-card p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-3">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <p class="label-info">Sekolah</p>
                                <p class="isi-info">
                                    SMK Muhammadiyah Tasikmalaya
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card info-card p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box me-3">
                                <i class="bi bi-code-slash"></i>
                            </div>
                            <div>
                                <p class="label-info">Jurusan</p>
                                <p class="isi-info">
                                    Rekayasa Perangkat Lunak
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Hobi -->
            <div class="card info-card p-4 mb-4">
                <h5 class="judul mb-3">
                    <i class="bi bi-heart-fill text-danger"></i>
                    Hobi Saya
                </h5>

                <div>
                    <span class="hobi-box">
                        <i class="bi bi-music-note-beamed"></i>
                        Menyanyi
                    </span>

                    <span class="hobi-box">
                        <i class="bi bi-egg-fried"></i>
                        Memasak
                    </span>
                </div>
            </div>

            <!-- Penutup -->
            <div class="text-center text-secondary py-3">
                <small>
                    <i class="bi bi-stars"></i>
                    Terus belajar, berkembang, dan berkarya.
                </small>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
