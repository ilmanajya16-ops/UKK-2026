
<?php
session_start();

// Jika sudah login, bisa diarahkan ke dashboard
// if (isset($_SESSION['user_id'])) {
//     header("Location: dashboard.php");
//     exit;
// }

$pesan_error = '';

if (isset($_SESSION['pesan_error'])) {
    $pesan_error = $_SESSION['pesan_error'];
    unset($_SESSION['pesan_error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #dceaf5, #9bbbd3);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 430px;
        }

        .login-card {
            background: #ffffff;
            padding: 38px 32px;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(25, 55, 80, 0.16);
            text-align: center;
        }

        .logo {
            width: 76px;
            height: 76px;
            margin: 0 auto 20px;
            border-radius: 22px;
            background: #173b59;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 35px;
            font-weight: bold;
            box-shadow: 0 7px 15px rgba(23, 59, 89, 0.2);
        }

        h2 {
            color: #173b59;
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #8795a3;
            font-size: 13px;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-control {
            height: 55px;
            border: 2px solid #edf1f5;
            background: #f0f2f5;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14px;
            box-shadow: none;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: #356b91;
            box-shadow: 0 0 0 3px rgba(53, 107, 145, 0.1);
        }

        .btn-login {
            width: 100%;
            height: 55px;
            margin-top: 8px;
            background: #173b59;
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #285c80;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .error-message {
            background: #fff0f0;
            color: #b42318;
            border: 1px solid #ffd6d6;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 18px;
            font-size: 13px;
            text-align: left;
        }

        .footer {
            color: #ffffff;
            text-align: right;
            margin-top: 24px;
            padding-right: 5px;
        }

        .footer h3 {
            font-family: Georgia, serif;
            font-size: 25px;
            margin-bottom: 4px;
        }

        .footer p {
            font-size: 12px;
            margin: 0;
            opacity: 0.9;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 22px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="login-card">

           

            <h2>Selamat Datang</h2>
            <p class="subtitle">
                Login ke Sistem Informasi Pelanggaran Siswa
            </p>

            <?php if ($pesan_error !== ''): ?>
                <div class="error-message">
                    <?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST" autocomplete="on">

                <div class="form-group">
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        aria-label="Email"
                        autocomplete="username"
                        required>
                </div>

                <div class="form-group">
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        aria-label="Password"
                        autocomplete="current-password"
                        required>
                </div>

                <button type="submit" class="btn btn-login">
                    LOGIN
                </button>

            </form>

        </div>

        <div class="footer">
            
            <p>Sistem Informasi Pelanggaran Siswa</p>
        </div>

    </div>

</body>
</html>
