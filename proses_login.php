
<?php
// proses_login.php

session_start();
include 'config/koneksi.php';

// Mengambil input
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi input
if ($email === '' || $password === '') {
    $_SESSION['pesan_error'] = "Email dan password wajib diisi!";
    header("Location: login.php");
    exit;
}

// Mencari akun berdasarkan email
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_users WHERE email = ? LIMIT 1"
);

if (!$stmt) {
    $_SESSION['pesan_error'] = "Terjadi kesalahan pada database.";
    header("Location: login.php");
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

// Memeriksa akun
if ($data) {

    if (password_verify($password, $data['password'])) {

        // Membuat session baru setelah login berhasil
        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['id'] = $data['id'];
        $_SESSION['name'] = $data['name'];
        $_SESSION['role'] = $data['role'];

        // Membersihkan pesan error lama
        unset($_SESSION['pesan_error']);

        header("Location: dashboard.php");
        exit;

    } else {

        $_SESSION['pesan_error'] = "Password salah!";
        header("Location: login.php");
        exit;

    }

} else {

    $_SESSION['pesan_error'] = "Email tidak ditemukan!";
    header("Location: login.php");
    exit;

}
?>
