
<?php
// catat_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Daftar siswa
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Daftar tahun ajaran
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Daftar kelas
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Daftar pelanggaran
$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Daftar guru
$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Data catatan pelanggaran
$sql = "SELECT
            ps.*,
            ta.nama AS tahun_ajaran,
            s.nis,
            k.tingkat,
            k.jurusan
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_tahun_ajaran ta
            ON ps.tahun_ajaran_id = ta.id
        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id
        ORDER BY ps.id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die('Gagal mengambil data: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catat Pelanggaran Siswa</title>

    <!-- Bootstrap 5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 4 -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding: 30px;
            box-sizing: border-box;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .card-body {
            padding: 25px;
        }

        h2 {
            color: #212529;
            font-weight: 600;
        }

        .tombol-aksi {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
        }

        .tombol-aksi .btn {
            padding: 10px 14px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 767.98px) {
            .content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid p-0">

    <!-- SIDEBAR -->
    <?php include 'layout/sidebar.php'; ?>

    <!-- KONTEN -->
    <main class="content">

        <h2 class="mb-2">Catat Pelanggaran Siswa</h2>

        <p class="text-muted">
            Tambahkan dan kelola catatan pelanggaran siswa.
        </p>

        <!-- Pesan sukses -->
        <?php if (isset($_SESSION['pesan_sukses'])) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_sukses']); ?>
        <?php } ?>

        <!-- Pesan error -->
        <?php if (isset($_SESSION['pesan_error'])) { ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['pesan_error']); ?>
        <?php } ?>

        <!-- FORM CATAT PELANGGARAN -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-4">Form Catat Pelanggaran</h5>

                <form action="proses_simpan_pelanggaran.php"
                      method="POST">

                    <div class="row">

                        <!-- Siswa -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Siswa</label>

                            <select name="siswa_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Siswa --</option>

                                <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                    <option value="<?= $s['id']; ?>">
                                        <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- Tahun Ajaran -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Ajaran</label>

                            <select name="tahun_ajaran_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                    <option value="<?= $ta['id']; ?>">
                                        <?= htmlspecialchars($ta['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- Kelas -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kelas</label>

                            <select name="kelas_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Kelas --</option>

                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $k['id']; ?>">
                                        <?= htmlspecialchars(
                                            $k['nama'] . ' - ' .
                                            $k['tingkat'] . ' - ' .
                                            $k['jurusan']
                                        ); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- Pelanggaran -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pelanggaran</label>

                            <select name="pelanggaran_id"
                                    id="pelanggaran"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Pelanggaran --</option>

                                <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>
                                    <option
                                        value="<?= $p['id']; ?>"
                                        data-poin="<?= $p['poin']; ?>"
                                        data-kategori="<?= $p['pelanggaran_kategori_id']; ?>">

                                        <?= htmlspecialchars(
                                            $p['nama'] . ' (' . $p['poin'] . ' poin)'
                                        ); ?>

                                    </option>
                                <?php } ?>

                            </select>

                            <input type="hidden"
                                   name="pelanggaran_kategori_id"
                                   id="kategori">
                        </div>

                        <!-- Guru Pelapor -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Guru Pelapor</label>

                            <select name="guru_id"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Guru --</option>

                                <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                    <option value="<?= $g['id']; ?>">
                                        <?= htmlspecialchars($g['nama']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <!-- Tanggal -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal</label>

                            <input type="date"
                                   name="tanggal"
                                   class="form-control"
                                   value="<?= date('Y-m-d'); ?>"
                                   required>
                        </div>

                        <!-- Poin -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Poin Pelanggaran</label>

                            <input type="number"
                                   name="poin"
                                   id="poin"
                                   class="form-control"
                                   min="0"
                                   required>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>

                            <select name="status"
                                    class="form-select"
                                    required>

                                <option value="Ditindaklanjuti">
                                    Ditindaklanjuti
                                </option>

                                <option value="Proses">
                                    Proses
                                </option>

                                <option value="Selesai">
                                    Selesai
                                </option>

                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Keterangan</label>

                            <textarea name="keterangan"
                                      class="form-control"
                                      rows="3"></textarea>
                        </div>

                        <!-- Tindakan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tindakan</label>

                            <textarea name="tindakan"
                                      class="form-control"
                                      rows="3"></textarea>
                        </div>

                    </div>

                    <!-- Tombol -->
                    <button type="submit" class="btn btn-primary">
                        Simpan Pelanggaran
                    </button>

                    <a href="dashboard.php" class="btn btn-secondary">
                        Kembali
                    </a>

                </form>

            </div>
        </div>

        <!-- TABEL DATA PELANGGARAN -->
        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-4">Data Catatan Pelanggaran</h5>

                <div class="table-responsive">

                    <table id="tabelPelanggaran"
                           class="table table-striped table-bordered table-hover">

                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Pelanggaran</th>
                                <th>Guru</th>
                                <th>Tanggal</th>
                                <th>Poin</th>
                                <th>Tindakan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $no = 1; ?>

                            <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= (int) $row['poin']; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['tindakan'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['status'] ?? '-'); ?>
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </main>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS 4.1.3 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables 1.13.8 -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- DataTables -->
<script>
$(document).ready(function () {

    $('#tabelPelanggaran').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,
        ordering: true,
        paging: true,
        info: true,

        autoWidth: false,
        scrollX: true,

        order: [[6, 'desc']],

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data pelanggaran",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data pelanggaran tidak ditemukan",

            emptyTable: "Belum ada data pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        }

    });

});

// Mengisi poin dan kategori otomatis
const pilihanPelanggaran = document.getElementById('pelanggaran');
const inputPoin = document.getElementById('poin');
const inputKategori = document.getElementById('kategori');

pilihanPelanggaran.addEventListener('change', function () {

    const opsi = this.options[this.selectedIndex];

    inputPoin.value = opsi.dataset.poin || '';
    inputKategori.value = opsi.dataset.kategori || '';

});
</script>

</body>
</html>
