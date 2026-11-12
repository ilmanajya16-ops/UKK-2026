<?php
// proses_tambah_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$nip = $_POST['nip'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$users = $_POST['users'];
$status_aktif = $_POST['status_aktif'];


$sql = "INSERT INTO t_guru
        (nip, nama, email, user, status_aktif)
        VALUES
        ('$nip','$nama','$email','users','$status_aktif',)";

if (mysqli_query($koneksi, $sql)) {

    header("Location:kelola_guru.php");
    exit;

} else {

    echo "Gagal menambahkan data guru: " . mysqli_error($koneksi);

}
?>