
<?php
// penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil daftar siswa
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil daftar tahun ajaran
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Ambil daftar kelas
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil data penempatan siswa
$sql = "SELECT
            ks.id,
            s.nis,
            s.nama AS nama_siswa,
            ta.nama AS tahun_ajaran,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            ks.tanggal_mulai,
            ks.tanggal_selesai,
            ks.status_aktif
        FROM t_kelas_siswa ks
        LEFT JOIN t_siswa s ON ks.siswa_id = s.id
        LEFT JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        ORDER BY ks.id DESC";

$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penempatan Siswa</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <!-- CSS DataTables -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: white;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
        }

        .sidebar .nav-link.active {
            background-color: #007bff;
        }

        .content {
            padding: 25px;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        
       <?php
            include 'layout/sidebar.php';
        ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 content">

            <h1>Penempatan Siswa</h1>

            <p>
                <a href="dashboard.php" class="btn btn-secondary btn-sm">
                    Kembali ke Dashboard
                </a>
            </p>

            <!-- PESAN SUKSES -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_sukses']);
                    unset($_SESSION['pesan_sukses']);
                    ?>
                </div>
            <?php } ?>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>
                </div>
            <?php } ?>

            <!-- FORM PENEMPATAN SISWA -->
            <div class="form-container shadow-sm">
                <h4 class="mb-3">Form Penempatan Siswa</h4>

                <form action="proses_simpan_penempatan.php" method="POST">

                    <div class="form-group">
                        <label>Siswa</label>
                        <select name="siswa_id" class="form-control" required>
                            <option value="">-- Pilih Siswa --</option>

                            <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                <option value="<?php echo $s['id']; ?>">
                                    <?php
                                    echo htmlspecialchars(
                                        $s['nis'] . ' - ' . $s['nama']
                                    );
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id"
                                class="form-control" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>

                            <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                <option value="<?php echo $ta['id']; ?>">
                                    <?php echo htmlspecialchars($ta['nama']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>

                            <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                <option value="<?php echo $k['id']; ?>">
                                    <?php
                                    echo htmlspecialchars(
                                        $k['nama'] . ' - Tingkat ' .
                                        $k['tingkat'] . ' - ' .
                                        $k['jurusan']
                                    );
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Tanggal Mulai</label>
                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Tanggal Selesai</label>
                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status_aktif"
                                class="form-control" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Simpan Penempatan
                    </button>

                </form>
            </div>

            <!-- TABEL DATA PENEMPATAN SISWA -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Data Penempatan Siswa</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table id="tabelPenempatan"
                               class="table table-striped table-bordered table-hover"
                               style="width:100%">

                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;

                                if ($hasil) {
                                    while ($row = mysqli_fetch_assoc($hasil)) {
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>

                                        <td>
                                            <?php echo htmlspecialchars($row['nis'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['tingkat'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['jurusan'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['tanggal_mulai'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($row['tanggal_selesai'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php if ($row['status_aktif'] == 1) { ?>
                                                <span class="badge badge-success">
                                                    Aktif
                                                </span>
                                            <?php } else { ?>
                                                <span class="badge badge-secondary">
                                                    Tidak Aktif
                                                </span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- jQuery: gunakan versi biasa, bukan slim -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 4 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- Integrasi DataTables Bootstrap 4 -->
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {

    $('#tabelPenempatan').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        paging: true,
        info: true,
        scrollX: true,

        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data penempatan siswa tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Belum ada data penempatan siswa",
            infoFiltered: "(difilter dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });

});
</script>

</body>
</html>
