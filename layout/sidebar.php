
<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>

<style>
    /* ================= SIDEBAR ================= */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        overflow-y: auto;
        background-color: #212529;
        padding: 20px;
        box-sizing: border-box;
        z-index: 1000;
    }

    /* SCROLLBAR SIDEBAR */
    .sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background-color: #6c757d;
        border-radius: 10px;
    }

    /* JUDUL SIDEBAR */
    .sidebar h4 {
        color: white;
        font-weight: 600;
    }

    /* MENU */
    .sidebar .nav-link {
        color: white;
        padding: 10px 12px;
        border-radius: 6px;
    }

    .sidebar .nav-link:hover {
        background-color: #495057;
        color: white;
    }

    .sidebar .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }

    /* LOGOUT */
    .sidebar .nav-link.text-danger {
        color: #dc3545 !important;
    }

    /* ================= KONTEN ================= */
    .content {
        margin-left: 250px !important;
        width: calc(100% - 250px) !important;
        min-height: 100vh;
        padding: 30px;
        box-sizing: border-box;
    }

    /* MENGHILANGKAN JARAK ROW BOOTSTRAP */
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 767.98px) {
        .sidebar {
            position: relative;
            width: 100%;
            height: auto;
            max-height: none;
        }

        .content {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 20px;
        }
    }
</style>

<?php if ($role === "admin") { ?>

<!-- ================= SIDEBAR ADMIN ================= -->
<div class="sidebar">

    <h4 class="mb-4">Pelanggaran Siswa</h4>

    <ul class="nav nav-pills flex-column">

        <li class="nav-item mb-2">
            <a href="dashboard.php"
               class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kelola_siswa.php"
               class="nav-link <?= in_array($current_page, ['kelola_siswa.php','edit_siswa.php','tambah_siswa.php','hapus_siswa.php']) ? 'active' : '' ?>">
                Data Siswa
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kelola_guru.php"
               class="nav-link <?= in_array($current_page, ['kelola_guru.php','edit_guru.php','tambah_guru.php','hapus_guru.php']) ? 'active' : '' ?>">
                Data Guru
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kelola_kelas.php"
               class="nav-link <?= in_array($current_page, ['kelola_kelas.php','edit_kelas.php','tambah_kelas.php','hapus_kelas.php']) ? 'active' : '' ?>">
                Data Kelas
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kelola_tahun_ajaran.php"
               class="nav-link <?= in_array($current_page, ['kelola_tahun_ajaran.php','edit_tahun_ajaran.php','tambah_tahun_ajaran.php','hapus_tahun_ajaran.php']) ? 'active' : '' ?>">
                Data Tahun Ajaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kelola_wali_kelas.php"
               class="nav-link <?= in_array($current_page, ['kelola_wali_kelas.php','edit_wali_kelas.php','tambah_wali_kelas.php','hapus_wali_kelas.php']) ? 'active' : '' ?>">
                Data Wali Kelas
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="penempatan_siswa.php"
               class="nav-link <?= in_array($current_page, ['penempatan_siswa.php','edit_penempatan_siswa.php','tambah_penempatan_siswa.php','hapus_penempatan_siswa.php']) ? 'active' : '' ?>">
                Penempatan Siswa
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kelola_pelanggaran_kategori.php"
               class="nav-link <?= in_array($current_page, ['kelola_pelanggaran_kategori.php','edit_pelanggaran_kategori.php','tambah_pelanggaran_kategori.php','hapus_pelanggaran_kategori.php']) ? 'active' : '' ?>">
                Kategori Pelanggaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="kelola_jenis_pelanggaran.php"
               class="nav-link <?= in_array($current_page, ['kelola_jenis_pelanggaran.php','edit_jenis_pelanggaran.php','tambah_jenis_pelanggaran.php','hapus_jenis_pelanggaran.php']) ? 'active' : '' ?>">
                Jenis Pelanggaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="laporan.php"
               class="nav-link <?= in_array($current_page, ['laporan.php','editlaporan.php','tambah_laporan.php','hapus_laporan.php']) ? 'active' : '' ?>">
                Laporan
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="about_me.php"
               class="nav-link <?= $current_page == 'about_me.php' ? 'active' : '' ?>">
                About Me
            </a>
        </li>

        <hr class="text-secondary">

        <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
                Logout
            </a>
        </li>

    </ul>
</div>

<?php } elseif ($role === "guru") { ?>

<!-- ================= SIDEBAR GURU ================= -->
<div class="sidebar">

    <h4 class="mb-4">Pelanggaran Siswa</h4>

    <ul class="nav nav-pills flex-column">

        <li class="nav-item mb-2">
            <a href="dashboard.php"
               class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="catat_pelanggaran.php"
               class="nav-link <?= $current_page == 'catat_pelanggaran.php' ? 'active' : '' ?>">
                Catat Pelanggaran
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="tindakan.php"
               class="nav-link <?= $current_page == 'tindakan.php' ? 'active' : '' ?>">
                Tindakan
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="riwayat.php"
               class="nav-link <?= $current_page == 'riwayat.php' ? 'active' : '' ?>">
                Riwayat
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="rekap_poin.php"
               class="nav-link <?= $current_page == 'rekap_poin.php' ? 'active' : '' ?>">
                Rekap Poin
            </a>
        </li>

        <hr class="text-secondary">

        <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
                Logout
            </a>
        </li>

    </ul>
</div>

<?php } ?>
