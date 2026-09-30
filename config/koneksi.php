<!DOCTYPE html>
 <html>
    <head>
        <title>login - Pelanggaran siswa</title>
</head>
<body>
    <h1>login aplikasi Pelanggaran siswa</h1>
    
    <?php
    session_start();
    if (isset($_SESSION['pesan_error'])) {
        echo '<p>' . $_SESSION['pesan_error'] . '</p>';
    }
    ?>

    <form action="proses_login.php" method="POST">
        <table>
            <tr>
                <td>email</td>
                <td>:</td>
                <td><input type="text" name="email" required></td>
            </tr>
            <tr>
                <td>password</td>
                <td>:</td>
                <td><input type="password" name="password" required></td>
             </tr>
             <tr>
                 <td colspan="3">
                    <input type="submit" value="login">
                 </td>
             </tr> 
         </table>
    </form>
</body>
</html