
<?php
// catat_pelanggaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// 1. MENGAMBIL TAHUN AJARAN AKTIF
// ==========================================
$sql_tahun = "SELECT id, nama
              FROM t_tahun_ajaran
              WHERE status_aktif = 1
              ORDER BY id DESC
              LIMIT 1";

$query_tahun = mysqli_query($koneksi, $sql_tahun);

if (!$query_tahun) {
    die("Gagal mengambil data tahun ajaran: " . mysqli_error($koneksi));
}

$data_tahun = mysqli_fetch_assoc($query_tahun);

$tahun_ajaran_id = $data_tahun['id'] ?? '';
$nama_tahun_ajaran = $data_tahun['nama'] ?? '';


// ==========================================
// 2. MENGAMBIL DATA GURU YANG LOGIN
// ==========================================
$id_user = (int) ($_SESSION['id'] ?? 0);

$stmt_guru = mysqli_prepare(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE user_id = ?
       AND status_aktif = 1
     LIMIT 1"
);

if (!$stmt_guru) {
    die("Gagal menyiapkan data guru: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
mysqli_stmt_execute($stmt_guru);

$result_guru = mysqli_stmt_get_result($stmt_guru);
$data_guru = mysqli_fetch_assoc($result_guru);

mysqli_stmt_close($stmt_guru);

$guru_id = $data_guru['id'] ?? '';
$nama_guru = $data_guru['nama'] ?? '';


// ==========================================
// 3. MENGAMBIL DATA SISWA DAN KELAS
// ==========================================
$siswa = false;

if ($tahun_ajaran_id != '') {

    $stmt_siswa = mysqli_prepare(
        $koneksi,
        "SELECT
            s.id,
            s.nis,
            s.nama,
            ks.kelas_id,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan
         FROM t_siswa s
         LEFT JOIN t_kelas_siswa ks
            ON ks.siswa_id = s.id
            AND ks.tahun_ajaran_id = ?
            AND ks.status_aktif = 1
         LEFT JOIN t_kelas k
            ON k.id = ks.kelas_id
         WHERE s.status_aktif = 1
         ORDER BY s.nama ASC"
    );

    if (!$stmt_siswa) {
        die("Gagal menyiapkan data siswa: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt_siswa, "i", $tahun_ajaran_id);
    mysqli_stmt_execute($stmt_siswa);

    $siswa = mysqli_stmt_get_result($stmt_siswa);
    mysqli_stmt_close($stmt_siswa);
}


// ==========================================
// 4. MENGAMBIL DATA PELANGGARAN
// ==========================================
$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

if (!$pelanggaran) {
    die("Gagal mengambil data pelanggaran: " . mysqli_error($koneksi));
}


// ==========================================
// 5. MENGAMBIL DATA CATATAN PELANGGARAN
// ==========================================
$sql = "SELECT
            ps.*,
            ta.nama AS tahun_ajaran,
            s.nis,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas,
            p.nama AS nama_pelanggaran,
            g.nama AS nama_guru
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_tahun_ajaran ta
            ON ps.tahun_ajaran_id = ta.id
        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id
        LEFT JOIN t_pelanggaran p
            ON ps.pelanggaran_id = p.id
        LEFT JOIN t_guru g
            ON ps.guru_id = g.id
        ORDER BY ps.id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Gagal mengambil data catatan: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Catat Pelanggaran Siswa</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- DATATABLES -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <!-- SELECT2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
          rel="stylesheet">

    <!-- SELECT2 BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
          rel="stylesheet">

    <style>

        body {
            background-color: #f5f6fa;
        }

        /* KONTEN */
        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding: 30px;
            box-sizing: border-box;
        }

        /* CARD */
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

        /* HEADER TABEL */
        #tabelPelanggaran thead th {
            background-color: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd !important;
            vertical-align: middle;
            white-space: nowrap;
        }

        /* ISI TABEL */
        #tabelPelanggaran tbody td {
            vertical-align: middle;
            white-space: nowrap;
        }

        /* INPUT READONLY */
        .form-control[readonly] {
            background-color: #e9ecef;
        }

        /* SELECT2 */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

        /* STATUS */
        .badge-status {
            padding: 7px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-selesai {
            background-color: #198754 !important;
            color: #ffffff !important;
        }

        .status-proses {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .status-ditindaklanjuti {
            background-color: #0d6efd !important;
            color: #ffffff !important;
        }

        .status-lainnya {
            background-color: #6c757d !important;
            color: #ffffff !important;
        }

        /* TOMBOL AKSI */
        .aksi-wrapper {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        /* RESPONSIVE */
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

    <!-- KONTEN UTAMA -->
    <main class="content">

        <h2 class="mb-2">Catat Pelanggaran Siswa</h2>

        <p class="text-muted">
            Tambahkan dan kelola catatan pelanggaran siswa.
        </p>


        <!-- PESAN SUKSES -->
        <?php if (isset($_SESSION['pesan_sukses'])) { ?>

            <div class="alert alert-success alert-dismissible fade show">

                <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

            <?php unset($_SESSION['pesan_sukses']); ?>

        <?php } ?>


        <!-- PESAN ERROR -->
        <?php if (isset($_SESSION['pesan_error'])) { ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

            <?php unset($_SESSION['pesan_error']); ?>

        <?php } ?>


        <!-- FORM CATAT PELANGGARAN -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-4">Form Catat Pelanggaran</h5>

                <!-- PERINGATAN TAHUN AJARAN -->
                <?php if ($tahun_ajaran_id == '') { ?>

                    <div class="alert alert-warning">
                        Belum ada tahun ajaran yang aktif.
                        Silakan aktifkan tahun ajaran terlebih dahulu.
                    </div>

                <?php } ?>

                <!-- PERINGATAN GURU -->
                <?php if ($guru_id == '') { ?>

                    <div class="alert alert-danger">
                        Data guru untuk akun yang sedang login tidak ditemukan
                        atau belum aktif.
                    </div>

                <?php } ?>


                <form action="proses_simpan_pelanggaran.php"
                      method="POST">

                    <div class="row">

                        <!-- SISWA -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Siswa</label>

                            <select name="siswa_id"
                                    id="siswa"
                                    class="form-select"
                                    data-placeholder="Cari nama atau NIS siswa..."
                                    required>

                                <option value=""></option>

                                <?php if ($siswa) { ?>

                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>

                                        <option
                                            value="<?= (int) $s['id']; ?>"
                                            data-kelas-id="<?= htmlspecialchars((string) ($s['kelas_id'] ?? '')); ?>"
                                            data-kelas="<?= htmlspecialchars(
                                                ($s['nama_kelas'] ?? '') .
                                                (!empty($s['tingkat']) ? ' - ' . $s['tingkat'] : '') .
                                                (!empty($s['jurusan']) ? ' - ' . $s['jurusan'] : '')
                                            ); ?>">

                                            <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>

                                        </option>

                                    <?php } ?>

                                <?php } ?>

                            </select>

                            <small class="text-muted">
                                Ketik NIS atau nama siswa untuk mencari.
                            </small>

                        </div>


                        <!-- KELAS OTOMATIS -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Kelas Siswa</label>

                            <input type="text"
                                   id="nama_kelas"
                                   class="form-control"
                                   placeholder="Kelas otomatis muncul"
                                   readonly>

                            <input type="hidden"
                                   name="kelas_id"
                                   id="kelas_id">

                            <small class="text-muted">
                                Kelas mengikuti penempatan siswa pada tahun ajaran aktif.
                            </small>

                        </div>


                        <!-- TAHUN AJARAN -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Tahun Ajaran Aktif</label>

                            <input type="text"
                                   class="form-control"
                                   value="<?= htmlspecialchars($nama_tahun_ajaran); ?>"
                                   placeholder="Belum ada tahun ajaran aktif"
                                   readonly>

                            <input type="hidden"
                                   name="tahun_ajaran_id"
                                   value="<?= htmlspecialchars((string) $tahun_ajaran_id); ?>">

                        </div>


                        <!-- PELANGGARAN -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Nama Pelanggaran</label>

                            <select name="pelanggaran_id"
                                    id="pelanggaran"
                                    class="form-select"
                                    required>

                                <option value="">-- Pilih Pelanggaran --</option>

                                <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>

                                    <option
                                        value="<?= (int) $p['id']; ?>"
                                        data-poin="<?= (int) $p['poin']; ?>"
                                        data-kategori="<?= (int) $p['pelanggaran_kategori_id']; ?>">

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


                        <!-- GURU PELAPOR -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Guru Pelapor</label>

                            <input type="text"
                                   class="form-control"
                                   value="<?= htmlspecialchars($nama_guru); ?>"
                                   placeholder="Guru yang sedang login"
                                   readonly>

                            <input type="hidden"
                                   name="guru_id"
                                   value="<?= htmlspecialchars((string) $guru_id); ?>">

                        </div>


                        <!-- TANGGAL -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Tanggal</label>

                            <input type="date"
                                   name="tanggal"
                                   class="form-control"
                                   value="<?= date('Y-m-d'); ?>"
                                   required>

                        </div>


                        <!-- STATUS -->
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


                        <!-- KETERANGAN -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">Keterangan</label>

                            <textarea name="keterangan"
                                      class="form-control"
                                      rows="3"></textarea>

                        </div>

                    </div>


                    <!-- TOMBOL SIMPAN -->
                    <button type="submit"
                            class="btn btn-primary"
                            <?= ($tahun_ajaran_id == '' || $guru_id == '') ? 'disabled' : ''; ?>>

                        <i class="bi bi-save"></i>
                        Simpan Pelanggaran

                    </button>

                    <a href="dashboard.php"
                       class="btn btn-secondary">

                        <i class="bi bi-arrow-left"></i>
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
                           class="table table-striped table-bordered table-hover align-middle w-100">

                        <thead>

                            <tr>
                                <th>No.</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Pelanggaran</th>
                                <th>Guru</th>
                                <th>Tanggal</th>
                                <th>Poin</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php $no = 1; ?>

                            <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>

                                <?php
                                $status = trim($row['status'] ?? '');
                                $status_lower = strtolower($status);
                                ?>

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
                                        <?= (int) ($row['poin'] ?? 0); ?>
                                    </td>


                                    <!-- STATUS -->
                                    <td>

                                        <?php if ($status_lower === 'selesai') { ?>

                                            <span class="badge badge-status status-selesai">
                                                Selesai
                                            </span>

                                        <?php } elseif ($status_lower === 'proses') { ?>

                                            <span class="badge badge-status status-proses">
                                                Proses
                                            </span>

                                        <?php } elseif ($status_lower === 'ditindaklanjuti') { ?>

                                            <span class="badge badge-status status-ditindaklanjuti">
                                                Ditindaklanjuti
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge badge-status status-lainnya">
                                                <?= htmlspecialchars($status ?: '-'); ?>
                                            </span>

                                        <?php } ?>

                                    </td>


                                    <!-- AKSI -->
                                    <td>

                                        <?php if ($status_lower !== 'selesai') { ?>

                                            <div class="aksi-wrapper">

                                                <a href="edit_pelanggaran.php?id=<?= (int) $row['id']; ?>"
                                                   class="btn btn-warning btn-sm">

                                                    <i class="bi bi-pencil-square"></i>
                                                    Edit

                                                </a>


                                                <form action="hapus_catat_pelanggaran.php"
                                                      method="POST"
                                                      onsubmit="return confirm('Yakin ingin menghapus catatan pelanggaran ini?');">

                                                    <input type="hidden"
                                                           name="id"
                                                           value="<?= (int) $row['id']; ?>">

                                                    <button type="submit"
                                                            class="btn btn-danger btn-sm">

                                                        <i class="bi bi-trash"></i>
                                                        Hapus

                                                    </button>

                                                </form>

                                            </div>

                                        <?php } else { ?>

                                            <span class="text-muted">
                                                Tidak ada aksi
                                            </span>

                                        <?php } ?>

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


<!-- JAVASCRIPT -->

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- SELECT2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DATATABLES -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>


<script>

$(document).ready(function () {

    // ==========================================
    // 1. PENCARIAN SISWA
    // ==========================================
    $('#siswa').select2({

        theme: 'bootstrap-5',

        width: '100%',

        placeholder: 'Cari nama atau NIS siswa...',

        allowClear: true,

        language: {
            noResults: function () {
                return 'Siswa tidak ditemukan';
            },
            searching: function () {
                return 'Sedang mencari...';
            }
        }

    });


    // ==========================================
    // 2. KELAS OTOMATIS BERDASARKAN SISWA
    // ==========================================
    $('#siswa').on('change', function () {

        const opsi = this.options[this.selectedIndex];

        const namaKelas = opsi ? opsi.dataset.kelas : '';
        const kelasId = opsi ? opsi.dataset.kelasId : '';

        $('#nama_kelas').val(namaKelas || '');
        $('#kelas_id').val(kelasId || '');

    });


    // ==========================================
    // 3. POIN DAN KATEGORI OTOMATIS
    // ==========================================
    $('#pelanggaran').on('change', function () {

        const opsi = this.options[this.selectedIndex];

        const kategori = opsi ? opsi.dataset.kategori : '';

        $('#kategori').val(kategori || '');

    });


    // ==========================================
    // 4. DATATABLES
    // ==========================================
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

</script>

</body>
</html>
