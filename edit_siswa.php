
<?php
// edit_siswa.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_GET['nis']) || trim($_GET['nis']) === '') {
    die("NIS siswa tidak ditemukan!");
}

$nis = trim($_GET['nis']);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM t_siswa WHERE nis = ?");
mysqli_stmt_bind_param($stmt, "s", $nis);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data siswa tidak ditemukan!");
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

                        <!-- ID -->
                        <input type="hidden"
                               name="id"
                               value="<?= (int) $data['id']; ?>">

                        <!-- NIS LAMA -->
                        <input type="hidden"
                               name="nis_lama"
                               value="<?= htmlspecialchars($data['nis'], ENT_QUOTES, 'UTF-8'); ?>">

                        <!-- NIS -->
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>

                            <input type="text"
                                   name="nis"
                                   id="nis"
                                   maxlength="15"
                                   pattern="[0-9]{1,15}"
                                   inputmode="numeric"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nis'], ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <!-- NISN -->
                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>

                            <input type="text"
                                   name="nisn"
                                   id="nisn"
                                   maxlength="18"
                                   pattern="[0-9]{1,18}"
                                   inputmode="numeric"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nisn'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <!-- NAMA -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Siswa</label>

                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <!-- JENIS KELAMIN -->
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>

                            <div class="d-flex gap-4">

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="jenis_kelamin"
                                           id="laki"
                                           value="L"
                                           <?= $data['jenis_kelamin'] == 'L' ? 'checked' : ''; ?>
                                           required>

                                    <label class="form-check-label" for="laki">
                                        Laki-laki
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="jenis_kelamin"
                                           id="perempuan"
                                           value="P"
                                           <?= $data['jenis_kelamin'] == 'P' ? 'checked' : ''; ?>
                                           required>

                                    <label class="form-check-label" for="perempuan">
                                        Perempuan
                                    </label>
                                </div>

                            </div>
                        </div>

                        <!-- TANGGAL LAHIR -->
                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">
                                Tanggal Lahir
                            </label>

                            <input type="date"
                                   name="tanggal_lahir"
                                   id="tanggal_lahir"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_lahir'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <!-- ALAMAT -->
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>

                            <textarea name="alamat"
                                      id="alamat"
                                      class="form-control"
                                      rows="3"
                                      required><?= htmlspecialchars($data['alamat'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <!-- STATUS AKTIF -->
                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status Aktif
                            </label>

                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= (string) $data['status_aktif'] === '1' ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (string) $data['status_aktif'] === '0' ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Update Siswa
                        </button>

                        <a href="kelola_siswa.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
