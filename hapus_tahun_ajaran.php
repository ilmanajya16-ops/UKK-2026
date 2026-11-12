<?php
// hapus_tahun_ajaran.php
session_start();

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_GET['id'];

// Ambil nama kelas sebelum dihapus
$cek = mysqli_query(
    $koneksi,
    "SELECT nama FROM t_tahun_ajaran WHERE id = '$id'"
);

$data = mysqli_fetch_assoc($cek);

// Hapus data kelas
// Hapus data
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    $_SESSION['pesan_sukses'] =
        'hapus tahun ajaran "' . $data['nama'] .
        ' berhasil dihapus.';

    header('Location: kelola_tahun_ajaran.php');
    exit;
}

header('Location: kelola_tahun_ajaran.php');
exit;
?>