
<?php
session_start();

$pesan_error = '';

if (isset($_SESSION['pesan_error'])) {
    $pesan_error = $_SESSION['pesan_error'];
    unset($_SESSION['pesan_error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 380px;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .login-card h3 {
            text-align: center;
            color: #173b59;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            font-size: 13px;
            margin-bottom: 30px;
        }

        .form-control {
            height: 45px;
            border-radius: 7px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #173b59;
            box-shadow: none;
        }

        .btn-login {
            width: 100%;
            height: 45px;
            background-color: #173b59;
            color: white;
            border-radius: 7px;
            font-weight: bold;
            border: none;
        }

        .btn-login:hover {
            background-color: #285c80;
            color: white;
        }

        .error-message {
            background: #fce8e8;
            color: #b42318;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 25px;
        }


.login-icon {
    text-align: center;
    margin-bottom: 15px;
}

.login-icon i {
    font-size: 55px;
    color: #173b59;
}

    </style>
</head>

<body>

    <div class="login-card">

        <div class="login-icon">
    <i class="bi bi-person-circle"></i>
</div>

        <p class="subtitle">
            Sistem Informasi Pelanggaran Siswa
        </p>

        <?php if ($pesan_error !== ''): ?>
            <div class="error-message">
                <?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">

            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Masukkan email"
                    autocomplete="username"
                    required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required>
            </div>

            <button type="submit" class="btn btn-login">
                LOGIN
            </button>

        </form>

        <div class="footer">
            &copy; 2026 Sistem Informasi Pelanggaran Siswa
        </div>

    </div>

</body>
</html>
