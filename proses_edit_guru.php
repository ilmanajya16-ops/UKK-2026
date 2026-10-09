<?php
// proses_edit_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nip = $_POST['nip'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$status_aktif = $_POST['status_aktif'];
$user_id = $_POST['user_id'];

$sql = "UPDATE tbl_guru SET
        nip = '$nip',
        nama = '$nama',
        email = '$email',
        status_aktif = '$status_aktif',
        user_id = '$user_id'
        WHERE id = '$id'";

$hasil = mysqli_query($koneksi, $sql);

if ($hasil) {
    header("Location: kelola_guru.php");
    exit;
} else {
    echo "Gagal mengubah data guru: " . mysqli_error($koneksi);
}
?>