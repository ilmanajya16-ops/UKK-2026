
<?php
// edit_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID siswa tidak valid.");
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_siswa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data siswa tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Edit Siswa</h1>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Edit Siswa
                </div>

                <div class="card-body">

                    <form action="proses_edit_siswa.php"
                          method="POST"
                          id="formEditSiswa">

                        <input type="hidden" name="id"
                               value="<?= htmlspecialchars($data['id']) ?>">

                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" name="nis"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nis']) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nisn']) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama']) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Jenis Kelamin</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="radio"
                                       name="jenis_kelamin"
                                       id="laki"
                                       value="L"
                                       <?= $data['jenis_kelamin'] == 'L' ? 'checked' : '' ?>
                                       required>
                                <label class="form-check-label" for="laki">
                                    Laki-laki
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="radio"
                                       name="jenis_kelamin"
                                       id="perempuan"
                                       value="P"
                                       <?= $data['jenis_kelamin'] == 'P' ? 'checked' : '' ?>
                                       required>
                                <label class="form-check-label" for="perempuan">
                                    Perempuan
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date"
                                   name="tanggal_lahir"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_lahir']) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat"
                                      class="form-control"
                                      rows="4"
                                      required><?= htmlspecialchars($data['alamat']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control"
                                   autocomplete="new-password">
                            <div class="form-text">
                                Kosongkan jika tidak ingin mengubah password.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password"
                                   name="konfirmasi_password"
                                   id="konfirmasi_password"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status Aktif</label>
                            <select name="status_aktif"
                                    class="form-select"
                                    required>
                                <option value="1"
                                    <?= $data['status_aktif'] == 1 ? 'selected' : '' ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $data['status_aktif'] == 0 ? 'selected' : '' ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Siswa
                        </button>

                        <a href="kelola_siswa.php"
                           class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

<script>
document.getElementById('formEditSiswa').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const konfirmasi = document.getElementById('konfirmasi_password').value;

    if (password !== konfirmasi) {
        e.preventDefault();
        alert('Password dan konfirmasi password tidak sama!');
        return;
    }

    if ((password === '') !== (konfirmasi === '')) {
        e.preventDefault();
        alert('Isi password dan konfirmasi password, atau kosongkan keduanya.');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
