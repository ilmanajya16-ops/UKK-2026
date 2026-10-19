
<?php
session_start();
include "config/koneksi.php";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Saya</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f3fa;
            color: #333;
        }

        .sidebar {
            width: 230px;
            height: 100vh;
            background-color: #302b43;
            color: white;
            position: fixed;
            padding: 25px 15px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #7565a8;
        }

        .content {
            margin-left: 230px;
            padding: 30px;
        }

        .header {
            background-color: white;
            padding: 20px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px #ddd;
        }

        .header h1 {
            margin: 0 0 8px;
            color: #514477;
            font-size: 26px;
        }

        .header p {
            margin: 0;
            color: #777;
        }

        .profile {
            background: linear-gradient(135deg, #7867ad, #a99bd0);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: white;
            color: #7867ad;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 38px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .profile h2 {
            margin: 0 0 10px;
        }

        .profile p {
            margin: 5px 0;
        }

        .card {
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px #e5e1ee;
        }

        .card h3 {
            color: #514477;
            margin-top: 0;
            border-bottom: 2px solid #eee9f8;
            padding-bottom: 12px;
        }

        .info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .info-item {
            background-color: #f8f6fc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #8877bd;
            overflow-wrap: anywhere;
        }

        .info-item small {
            display: block;
            color: #777;
            margin-bottom: 8px;
        }

        .info-item strong {
            color: #39304f;
        }

        .description {
            line-height: 1.8;
            color: #555;
            text-align: justify;
        }

        .button {
            display: inline-block;
            background-color: #6f5b9e;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 7px;
            margin-top: 5px;
        }

        .button:hover {
            background-color: #514477;
        }

        .footer {
            text-align: center;
            color: #888;
            padding: 15px;
            font-size: 13px;
        }

        @media (max-width: 700px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .sidebar h2 {
                margin-bottom: 15px;
            }

            .content {
                margin-left: 0;
                padding: 15px;
            }

            .profile {
                padding: 20px;
                gap: 15px;
            }

            .avatar {
                width: 75px;
                height: 75px;
                font-size: 28px;
            }

            .info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>My Website</h2>

        <a href="dashboard.php">Dashboard</a>
        <a href="kelola_siswa.php">Data Siswa</a>
        <a href="kelola_guru.php">Data Guru</a>
        <a href="kelola_kelas.php">Kelas</a>
        <a href="kelola_tahun_ajaran.php">Tahun Ajaran</a>
        <a href="penempatan_siswa.php">Penempatan Siswa</a>
        <a href="kelola_wali_kelas.php">Wali Kelas</a>
        <a href="kelola_pelanggaran_kategori.php">Kategori Pelanggaran</a>
        <a href="Laporan.php">Laporan</a>
        <a href="about_me.php" class="active">About Me</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- KONTEN -->
    <div class="content">

        <div class="header">
            <h1>Tentang Saya</h1>
            <p>Halaman profil pribadi dan informasi diri saya.</p>
        </div>

                
        <!-- PROFIL -->
        <div class="profile">
            <img class="mr-3" src="ilma najya.jpg" alt="Generic placeholder image">

            <div>
                <h2><?php echo htmlspecialchars($nama); ?></h2>
                <p><?php echo htmlspecialchars($kelas); ?></p>
                <p><?php echo htmlspecialchars($jurusan); ?></p>
            </div>
        </div>

        <!-- INFORMASI DIRI -->
        <div class="card">
            <h3>Informasi Pribadi</h3>

            <div class="info">
                <div class="info-item">
                    <small>Nama Lengkap</small>
                    <strong><?php echo htmlspecialchars($nama); ?></strong>
                </div>

                <div class="info-item">
                    <small>Kelas</small>
                    <strong><?php echo htmlspecialchars($kelas); ?></strong>
                </div>

                <div class="info-item">
                    <small>Nama Sekolah</small>
                    <strong><?php echo htmlspecialchars($sekolah); ?></strong>
                </div>

                <div class="info-item">
                    <small>Jurusan</small>
                    <strong><?php echo htmlspecialchars($jurusan); ?></strong>
                </div>

                <div class="info-item">
                    <small>Hobi</small>
                    <strong><?php echo htmlspecialchars($hobi); ?></strong>
                </div>

                <div class="info-item">
                    <small>Cita-cita</small>
                    <strong><?php echo htmlspecialchars($cita_cita); ?></strong>
                </div>
            </div>
        </div>

        <!-- DESKRIPSI DIRI -->
        <div class="card">
            <h3>Deskripsi Diri</h3>

            <p class="description">
                Halo! Nama saya <?php echo htmlspecialchars($nama); ?>.
                Saya merupakan pelajar kelas <?php echo htmlspecialchars($kelas); ?>
                di <?php echo htmlspecialchars($sekolah); ?>,
                dengan jurusan <?php echo htmlspecialchars($jurusan); ?>.

                Saya memiliki hobi menyanyi dan memasak. Saya juga senang
                mempelajari hal-hal baru, khususnya dalam bidang teknologi,
                pemrograman, dan pembuatan website. Saya berharap dapat terus
                mengembangkan kemampuan serta meraih cita-cita saya menjadi PNS.
            </p>

            <a href="dashboard.php" class="button">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <div class="footer">
            &copy; 2026 <?php echo htmlspecialchars($nama); ?> |
            Halaman Profil Pribadi
        </div>

    </div>

</body>
</html>
