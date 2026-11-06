
<?php
// tambah_siswa.php
include 'includes/cek_session.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Siswa</title>

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

            <h1 class="mb-4">Tambah Siswa</h1>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Tambah Siswa
                </div>

                <div class="card-body">

                    <form action="proses_tambah_siswa.php"
                          method="POST"
                          id="formTambahSiswa">

                       <!-- NIS -->
                    <div class="mb-3">
                        <label for="nis" class="form-label">NIS</label>
                        <input type="text"
                            name="nis"
                            id="nis"
                            class="form-control"
                            maxlength="15"
                            pattern="[0-9]{1,15}"
                            inputmode="numeric"
                            title="NIS harus berupa angka maksimal 15 digit"
                            required>
                    </div>

                    <!-- NISN -->
                <div class="mb-3">
                    <label for="nisn" class="form-label">NISN</label>
                    <input type="text"
                        name="nisn"
                        id="nisn"
                        class="form-control"
                        maxlength="18"
                        pattern="[0-9]{1,18}"
                        inputmode="numeric"
                        title="NISN harus berupa angka maksimal 18 digit"
                        required>
                </div>
                        <!-- NAMA -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Siswa</label>
                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
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
                                    required>

                                <label class="form-check-label" for="perempuan">
                                    Perempuan
                                </label>
                            </div>

                        </div>
                    </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   required>
                        </div>

                        <!-- ALAMAT -->
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea name="alamat"
                                      id="alamat"
                                      class="form-control"
                                      rows="3"
                                      required></textarea>
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
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <button type="submit" class="btn btn-primary">
                            Simpan Siswa
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
