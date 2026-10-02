<?php
//buat_user_awal.php
//jalankan file ini satu kali saja lewat browseruntuk user awal 
include 'config/koneksi.php';

$name ='administrator';
$email ='guru@gmail.com';
$password = password_hash('guru123', PASSWORD_DEFAULT);
$role = 'guru';

$sql = "INSERT INTO t_users(name, email, password, role)";
$sql .= "VALUES('$name','$email','$password','$role')";

if (mysqli_query($koneksi, $sql)) {
    echo 'user admin berhasil dibuat. silakan hapus file ini.';
}else{
    echo 'gagal membuat user: ' . mysqli_error($koneksi);
}
?>