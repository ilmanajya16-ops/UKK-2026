<?php
// proses_tambah_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$status_aktif = $_POST['status_aktif'];

$sql = "INSERT INTO t_tahun_ajaran
        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
        VALUES
        ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')";

if (mysqli_query($koneksi, $sql)) {

    header("Location: kelola_tahun_ajaran.php");
    exit;

} else {

    echo "Gagal menambahkan data tahun ajaran: "
         . mysqli_error($koneksi);

}
?>