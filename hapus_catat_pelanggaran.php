
<?php
// hapus_catat_pelanggaran.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// 1. MENGECEK METHOD POST
// ==========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['pesan_error'] = 'Metode penghapusan tidak valid.';
    header('Location: catat_pelanggaran.php');
    exit;
}

// ==========================================
// 2. MENGAMBIL ID
// ==========================================
$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['pesan_error'] = 'ID catatan pelanggaran tidak valid.';
    header('Location: catat_pelanggaran.php');
    exit;
}

// ==========================================
// 3. MEMERIKSA DATA
// ==========================================
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_pelanggaran_siswa WHERE id = ? LIMIT 1"
);

if (!$stmt) {
    die('Gagal memeriksa data: ' . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['pesan_error'] = 'Data catatan pelanggaran tidak ditemukan.';
    header('Location: catat_pelanggaran.php');
    exit;
}

// ==========================================
// 4. PROSES HAPUS
// ==========================================
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_siswa WHERE id = ?"
);

if (!$stmt) {
    die('Gagal menyiapkan penghapusan: ' . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['pesan_sukses'] = 'Catatan pelanggaran berhasil dihapus.';
    } else {
        $_SESSION['pesan_error'] = 'Data gagal dihapus atau tidak ditemukan.';
    }

} else {

    die('Gagal menghapus data: ' . mysqli_stmt_error($stmt));

}

mysqli_stmt_close($stmt);

// ==========================================
// 5. KEMBALI KE HALAMAN CATAT PELANGGARAN
// ==========================================
header('Location: catat_pelanggaran.php');
exit;
?>
