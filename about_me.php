```php
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$nama = "Ilma Najya";
$kelas = "XII RPL";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Menyanyi dan Memasak";
$cita_cita = "PNS";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tentang Diri Saya</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            min-height: 100vh;
            padding: 30px;
            background-color: #ffffff;
        }

        .page-title {
            color: #164b80;
            font-weight: bold;
        }

        .about-card {
            max-width: 850px;
            margin: 25px auto;
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.07);
        }

        .header-about {
            height: 170px;
            background: linear-gradient(135deg, #0756a5, #4b9be8);
        }

        /* FOTO PERSEGI PANJANG */
        .profile-img {
            width: 160px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            border: 5px solid white;
            margin-top: -85px;
            background: white;
        }

        .judul {
            color: #164b80;
        }

        .subjudul {
            color: #6c849d;
        }

        .biodata {
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 12px;
            border: 1px solid #d9e8f5;
            border-left: 4px solid #2684d9;
            transition: 0.3s;
        }

        .biodata:hover {
            background: #f5faff;
            transform: translateX(5px);
        }

        .biodata i {
            color: #1976c9;
            font-size: 20px;
            margin-right: 10px;
        }

        .btn-dashboard {
            background: #1769aa;
            color: white;
            border-radius: 8px;
            padding: 10px 25px;
            transition: 0.3s;
        }

        .btn-dashboard:hover {
            background: #104d80;
            color: white;
        }

        .icon-title {
            color: #1976c9;
        }

        hr {
            color: #b8d5ed;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .about-card {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR DASHBOARD -->
        <?php include "layout/sidebar.php"; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 main-content">

            <!-- JUDUL HALAMAN -->
            <h3 class="page-title">
                <i class="bi bi-person-lines-fill"></i>
                Tentang Diri Saya
            </h3>

            <p class="text-muted">
                Halaman informasi dan profil pribadi.
            </p>

            <!-- KARTU PROFIL -->
            <div class="about-card">

                <!-- HEADER BIRU -->
                <div class="header-about"></div>

                <div class="text-center px-4 pb-4">

                    <!-- FOTO PROFIL -->
                    <img src="img/image.png"
                         class="profile-img"
                         alt="Foto Profil">

                    <h3 class="fw-bold mt-3 judul">
                        <?php echo $nama; ?>
                    </h3>

                    <p class="subjudul">
                        Siswi Rekayasa Perangkat Lunak
                    </p>

                    <hr class="my-4">

                    <!-- PROFIL DIRI -->
                    <h5 class="fw-bold text-start mb-3">
                        <i class="bi bi-person-circle icon-title"></i>
                        Profil Diri Saya
                    </h5>

                    <p class="text-muted text-start">
                        Halo! Perkenalkan, nama saya
                        <?php echo $nama; ?>.
                        Saya merupakan siswi kelas
                        <?php echo $kelas; ?> di
                        <?php echo $sekolah; ?> dengan jurusan
                        <?php echo $jurusan; ?>.

                        Saya memiliki hobi menyanyi dan memasak.
                        Saya juga memiliki cita-cita menjadi seorang PNS.
                        Melalui halaman ini, saya ingin memperkenalkan
                        diri serta berbagi sedikit informasi mengenai diri saya.
                    </p>

                    <!-- BIODATA -->
                    <h5 class="fw-bold text-start mt-4 mb-3">
                        <i class="bi bi-card-list icon-title"></i>
                        Biodata Saya
                    </h5>

                    <div class="biodata text-start">
                        <i class="bi bi-person"></i>
                        <strong>Nama Lengkap:</strong>
                        <?php echo $nama; ?>
                    </div>

                    <div class="biodata text-start">
                        <i class="bi bi-mortarboard"></i>
                        <strong>Kelas:</strong>
                        <?php echo $kelas; ?>
                    </div>

                    <div class="biodata text-start">
                        <i class="bi bi-building"></i>
                        <strong>Sekolah:</strong>
                        <?php echo $sekolah; ?>
                    </div>

                    <div class="biodata text-start">
                        <i class="bi bi-laptop"></i>
                        <strong>Jurusan:</strong>
                        <?php echo $jurusan; ?>
                    </div>

                    <div class="biodata text-start">
                        <i class="bi bi-heart"></i>
                        <strong>Hobi:</strong>
                        <?php echo $hobi; ?>
                    </div>

                    <div class="biodata text-start">
                        <i class="bi bi-star"></i>
                        <strong>Cita-cita:</strong>
                        <?php echo $cita_cita; ?>
                    </div>

                    <!-- TOMBOL KEMBALI -->
                    <div class="mt-4">
                        <a href="dashboard.php" class="btn btn-dashboard">
                            <i class="bi bi-arrow-left"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- BOOTSTRAP JAVASCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
```