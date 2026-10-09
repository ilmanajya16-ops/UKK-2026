```php
<?php
// proses_edit_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$status_aktif = $_POST['status_aktif'];

$sql = "UPDATE t_tahun_ajaran SET
        nama = '$nama',
        tanggal_mulai = '$tanggal_mulai',
        tanggal_selesai = '$tanggal_selesai',
        status_aktif = '$status_aktif',
        updated_at = CURRENT_TIMESTAMP
        WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {

    header("Location: kelola_tahun_ajaran.php");
    exit;

} else {

    echo "Gagal mengubah data tahun ajaran: "
         . mysqli_error($koneksi);

}
?>
```