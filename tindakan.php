
<?php
// tindakan.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$pesan_error = '';

// Proses menyimpan tindakan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    $tindakan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['tindakan'] ?? '')
    );

    $sql_update = "UPDATE t_pelanggaran_siswa
                   SET tindakan = '$tindakan'
                   WHERE id = $id";

    if (mysqli_query($koneksi, $sql_update)) {
        header('Location: tindakan.php?pesan=berhasil');
        exit;
    } else {
        $pesan_error = mysqli_error($koneksi);
    }
}

// Mengambil data pelanggaran siswa
$sql = "SELECT id, nama_siswa, nama_kelas,
               nama_pelanggaran, tanggal,
               keterangan, tindakan
        FROM t_pelanggaran_siswa
        ORDER BY tanggal DESC";

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

    <title>Tindakan Pelanggaran Siswa</title>

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

        textarea {
            min-width: 180px;
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

        <h2 class="mb-2">Tindakan Pelanggaran Siswa</h2>

        <p class="text-muted">
            Kelola tindakan untuk setiap pelanggaran siswa.
        </p>

        <!-- Tombol kembali -->
        <div class="tombol-aksi">
            <a href="dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>
        </div>

        <!-- Pesan berhasil -->
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'berhasil') { ?>
            <div class="alert alert-success alert-dismissible fade show">
                Tindakan berhasil disimpan.

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <!-- Pesan error -->
        <?php if (!empty($pesan_error)) { ?>
            <div class="alert alert-danger">
                Gagal menyimpan tindakan:
                <?= htmlspecialchars($pesan_error); ?>
            </div>
        <?php } ?>

        <!-- CARD DATA -->
        <div class="card shadow-sm">

            <div class="card-body">

                <h5 class="mb-4">Data Pelanggaran Siswa</h5>

                <div class="table-responsive">

                    <table id="tabelTindakan"
                           class="table table-striped table-bordered table-hover">

                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Tindakan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                <tr>

                                    <td></td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['keterangan'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <form
                                            id="formTindakan<?= (int) $row['id']; ?>"
                                            method="POST"
                                            action="tindakan.php">

                                            <input type="hidden"
                                                   name="id"
                                                   value="<?= (int) $row['id']; ?>">

                                            <textarea
                                                name="tindakan"
                                                class="form-control"
                                                rows="2"
                                                placeholder="Masukkan tindakan..."
                                                required><?= htmlspecialchars($row['tindakan'] ?? ''); ?></textarea>

                                        </form>
                                    </td>

                                    <td>
                                        <button
                                            type="submit"
                                            form="formTindakan<?= (int) $row['id']; ?>"
                                            class="btn btn-success btn-sm">
                                            Simpan
                                        </button>
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

<!-- Inisialisasi DataTables -->
<script>
$(document).ready(function () {

    $('#tabelTindakan').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,
        ordering: true,
        paging: true,
        info: true,

        autoWidth: false,
        scrollX: true,

        order: [[4, 'desc']],

        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: [0, 7]
            }
        ],

        drawCallback: function () {

            var api = this.api();
            var nomorAwal = api.page.info().start;

            api.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {

                cell.innerHTML = nomorAwal + i + 1;

            });

        },

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
</script>

</body>
</html>
