<?php

session_start();

// Jika sudah login, langsung ke dashboard
// if (isset($_SESSION['user_id'])) {
//     header("Location: dashboard.php");
//     exit;
// }

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <meta charset="UTF-8">
    <title>Login Pelanggaran Siswa</title>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <h1>Login Aplikasi Pelanggaran Siswa</h1>

    <?php

    if (isset($_SESSION['pesan_error'])) {

        echo "<p>" . htmlspecialchars($_SESSION['pesan_error']) . "</p>";

        unset($_SESSION['pesan_error']);
    }

    ?>

<form action="proses_login.php" method="POST" autocomplete="off">
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
    
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
 
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
  

    <!-- <form action="proses_login.php" method="POST" autocomplete="off">

        <table>

            <tr>
                <td>Email</td>
                <td>:</td>
                <td>
                    <input
                        type="email"
                        name="email"
                        value=""
                        autocomplete="off"
                        required>
                </td>
            </tr>

            <tr>
                <td>Password</td>
                <td>:</td>
                <td>
                    <input
                        type="password"
                        name="password"
                        value=""
                        autocomplete="new-password"
                        required>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <input type="submit" value="Login">
                </td>
            </tr>

        </table>

    </form> -->

</body>

</html>