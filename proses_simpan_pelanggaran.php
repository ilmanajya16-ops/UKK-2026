
<?php
// proses_simpan_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// 1. MEMASTIKAN FORM DIKIRIM DENGAN POST
// ==========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: catat_pelanggaran.php");
    exit;
}


// ==========================================
// 2. MENGAMBIL DATA DARI FORM
// ==========================================
$siswa_id = (int) ($_POST['siswa_id'] ?? 0);
$tanggal = trim($_POST['tanggal'] ?? '');
$status = trim($_POST['status'] ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');


// ==========================================
// 3. VALIDASI INPUT
// ==========================================
if (
    $siswa_id <= 0 ||
    $tanggal === '' ||
    !in_array($status, ['Ditindaklanjuti', 'Proses', 'Selesai'], true)
) {
    $_SESSION['pesan_error'] = "Data pelanggaran belum lengkap atau tidak valid.";
    header("Location: catat_pelanggaran.php");
    exit;
}

// Validasi format tanggal
$tanggal_obj = DateTime::createFromFormat('Y-m-d', $tanggal);

if (!$tanggal_obj || $tanggal_obj->format('Y-m-d') !== $tanggal) {
    $_SESSION['pesan_error'] = "Format tanggal tidak valid.";
    header("Location: catat_pelanggaran.php");
    exit;
}


// ==========================================
// 4. MENGAMBIL TAHUN AJARAN AKTIF
// ==========================================
$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC
     LIMIT 1"
);

if (!$query_tahun) {
    die("Gagal mengambil tahun ajaran: " . mysqli_error($koneksi));
}

$data_tahun = mysqli_fetch_assoc($query_tahun);

if (!$data_tahun) {
    $_SESSION['pesan_error'] = "Belum ada tahun ajaran yang aktif.";
    header("Location: catat_pelanggaran.php");
    exit;
}

$tahun_ajaran_id = (int) $data_tahun['id'];


// ==========================================
// 5. MENGAMBIL DATA GURU YANG LOGIN
// ==========================================
$id_user = (int) ($_SESSION['id'] ?? 0);

$stmt_guru = mysqli_prepare(
    $koneksi,
    "SELECT id
     FROM t_guru
     WHERE user_id = ?
       AND status_aktif = 1
     LIMIT 1"
);

if (!$stmt_guru) {
    die("Gagal menyiapkan data guru: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
mysqli_stmt_execute($stmt_guru);

$result_guru = mysqli_stmt_get_result($stmt_guru);
$data_guru = mysqli_fetch_assoc($result_guru);

mysqli_stmt_close($stmt_guru);

if (!$data_guru) {
    $_SESSION['pesan_error'] = "Data guru yang login tidak ditemukan atau tidak aktif.";
    header("Location: catat_pelanggaran.php");
    exit;
}

$guru_id = (int) $data_guru['id'];


// ==========================================
// 6. MENGAMBIL KELAS SISWA
// ==========================================
$stmt_siswa = mysqli_prepare(
    $koneksi,
    "SELECT ks.kelas_id
     FROM t_siswa s
     INNER JOIN t_kelas_siswa ks
        ON ks.siswa_id = s.id
     WHERE s.id = ?
       AND s.status_aktif = 1
       AND ks.tahun_ajaran_id = ?
       AND ks.status_aktif = 1
     LIMIT 1"
);

if (!$stmt_siswa) {
    die("Gagal menyiapkan data siswa: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt_siswa,
    "ii",
    $siswa_id,
    $tahun_ajaran_id
);

mysqli_stmt_execute($stmt_siswa);

$result_siswa = mysqli_stmt_get_result($stmt_siswa);
$data_siswa = mysqli_fetch_assoc($result_siswa);

mysqli_stmt_close($stmt_siswa);

if (!$data_siswa) {
    $_SESSION['pesan_error'] = "Siswa tidak ditemukan, tidak aktif, atau belum memiliki kelas pada tahun ajaran aktif.";
    header("Location: catat_pelanggaran.php");
    exit;
}

$kelas_id = (int) $data_siswa['kelas_id'];


// ==========================================
// 7. MENGAMBIL DATA PELANGGARAN
// ==========================================
$pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);

$stmt_pelanggaran = mysqli_prepare(
    $koneksi,
    "SELECT id, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE id = ?
       AND status_aktif = 1
     LIMIT 1"
);

if (!$stmt_pelanggaran) {
    die("Gagal menyiapkan data pelanggaran: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt_pelanggaran,
    "i",
    $pelanggaran_id
);

mysqli_stmt_execute($stmt_pelanggaran);

$result_pelanggaran = mysqli_stmt_get_result($stmt_pelanggaran);
$data_pelanggaran = mysqli_fetch_assoc($result_pelanggaran);

mysqli_stmt_close($stmt_pelanggaran);

if (!$data_pelanggaran) {
    $_SESSION['pesan_error'] = "Jenis pelanggaran tidak ditemukan atau sudah tidak aktif.";
    header("Location: catat_pelanggaran.php");
    exit;
}

$poin = (int) $data_pelanggaran['poin'];
$kategori_id = (int) $data_pelanggaran['pelanggaran_kategori_id'];


// ==========================================
// 8. MENYIMPAN DATA PELANGGARAN
// ==========================================
$stmt_simpan = mysqli_prepare(
    $koneksi,
    "INSERT INTO t_pelanggaran_siswa
    (
        siswa_id,
        kelas_id,
        tahun_ajaran_id,
        pelanggaran_id,
        pelanggaran_kategori_id,
        guru_id,
        tanggal,
        poin,
        status,
        keterangan
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt_simpan) {
    die("Gagal menyiapkan penyimpanan: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param(
    $stmt_simpan,
    "iiiiiisiss",
    $siswa_id,
    $kelas_id,
    $tahun_ajaran_id,
    $pelanggaran_id,
    $kategori_id,
    $guru_id,
    $tanggal,
    $poin,
    $status,
    $keterangan
);


// ==========================================
// 9. HASIL PENYIMPANAN
// ==========================================
if (mysqli_stmt_execute($stmt_simpan)) {

    $_SESSION['pesan_sukses'] = "Data pelanggaran siswa berhasil disimpan.";

    mysqli_stmt_close($stmt_simpan);

    header("Location: catat_pelanggaran.php");
    exit;

} else {

    $_SESSION['pesan_error'] = "Gagal menyimpan data pelanggaran: " .
        mysqli_stmt_error($stmt_simpan);

    mysqli_stmt_close($stmt_simpan);

    header("Location: catat_pelanggaran.php");
    exit;
}
?>
