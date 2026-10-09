<?php
// hapus_guru.php
session_start();

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_GET['id'];

// Ambil nama guru sebelum dihapus
$cek = mysqli_query(
    $koneksi,
    "SELECT nama FROM tbl_guru WHERE id = '$id'"
);

$data = mysqli_fetch_assoc($cek);

// Hapus data guru
$sql = "DELETE FROM tbl_guru WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {

    $id_user = $_SESSION['id_user'];
    $waktu = date('Y-m-d H:i:s');

    $aktivitas = "hapus guru: " . $data['nama'];

    // Simpan aktivitas ke tabel log
    $log = "INSERT INTO tbl_log 
            (id_user, aktivitas, waktu) 
            VALUES 
            ('$id_user', '$aktivitas', '$waktu')";

    mysqli_query($koneksi, $log);
}

// Kembali ke halaman kelola guru
header('Location: kelola_guru.php');
exit;
?>