
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    die("ID pelanggaran tidak valid.");
}

// Ambil data pelanggaran
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran_siswa WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Data pelanggaran tidak ditemukan.");
}

$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $pelanggaran_id = (int) ($_POST['pelanggaran_id'] ?? 0);
    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $poin = (int) ($_POST['poin'] ?? 0);
    $status = $_POST['status'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (
        $siswa_id <= 0 ||
        $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 ||
        $pelanggaran_id <= 0 ||
        $kategori_id <= 0 ||
        $tanggal === ''
    ) {
        $pesan_error = "Lengkapi seluruh data yang wajib diisi.";
    } else {

        $stmt_update = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_siswa SET
                siswa_id = ?,
                kelas_id = ?,
                tahun_ajaran_id = ?,
                pelanggaran_id = ?,
                pelanggaran_kategori_id = ?,
                tanggal = ?,
                poin = ?,
                status = ?,
                keterangan = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt_update,
            "iiiiisissi",
            $siswa_id,
            $kelas_id,
            $tahun_ajaran_id,
            $pelanggaran_id,
            $kategori_id,
            $tanggal,
            $poin,
            $status,
            $keterangan,
            $id
        );

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            $_SESSION['pesan_sukses'] = "Data pelanggaran berhasil diperbarui.";
            header("Location: catat_pelanggaran.php");
            exit;
        } else {
            $pesan_error = "Gagal memperbarui data: " . mysqli_stmt_error($stmt_update);
            mysqli_stmt_close($stmt_update);
        }
    }

    // Pertahankan input jika validasi gagal
    $data = array_merge($data, $_POST);
}

// Ambil siswa sesuai tahun ajaran pada catatan
$tahun_id = (int) ($data['tahun_ajaran_id'] ?? 0);

$stmt_siswa = mysqli_prepare(
    $koneksi,
    "SELECT s.id, s.nis, s.nama, ks.kelas_id,
            k.nama AS nama_kelas, k.tingkat, k.jurusan
     FROM t_siswa s
     LEFT JOIN t_kelas_siswa ks
        ON ks.siswa_id = s.id
        AND ks.tahun_ajaran_id = ?
        AND ks.status_aktif = 1
     LEFT JOIN t_kelas k ON k.id = ks.kelas_id
     WHERE s.status_aktif = 1
     ORDER BY s.nama ASC"
);
mysqli_stmt_bind_param($stmt_siswa, "i", $tahun_id);
mysqli_stmt_execute($stmt_siswa);
$siswa = mysqli_stmt_get_result($stmt_siswa);

// Ambil pelanggaran aktif
$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil nama tahun ajaran
$stmt_tahun = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_tahun_ajaran WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt_tahun, "i", $tahun_id);
mysqli_stmt_execute($stmt_tahun);
$result_tahun = mysqli_stmt_get_result($stmt_tahun);
$tahun = mysqli_fetch_assoc($result_tahun);
mysqli_stmt_close($stmt_tahun);

// Ambil nama guru
$guru_id = (int) ($data['guru_id'] ?? 0);
$stmt_guru = mysqli_prepare(
    $koneksi,
    "SELECT nama FROM t_guru WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt_guru, "i", $guru_id);
mysqli_stmt_execute($stmt_guru);
$result_guru = mysqli_stmt_get_result($stmt_guru);
$guru = mysqli_fetch_assoc($result_guru);
mysqli_stmt_close($stmt_guru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }

        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
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

<?php include 'layout/sidebar.php'; ?>

<main class="content">
    <h2 class="mb-2">Edit Pelanggaran Siswa</h2>
    <p class="text-muted">Ubah informasi catatan pelanggaran siswa.</p>

    <div class="card shadow-sm">
        <div class="card-body">

            <?php if ($pesan_error !== '') { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan_error); ?>
                </div>
            <?php } ?>

            <form method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Siswa</label>
                        <select name="siswa_id" id="siswa" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = mysqli_fetch_assoc($siswa)) {
                                $kelas_nama = ($s['nama_kelas'] ?? '') .
                                    (!empty($s['tingkat']) ? ' - ' . $s['tingkat'] : '') .
                                    (!empty($s['jurusan']) ? ' - ' . $s['jurusan'] : '');
                            ?>
                                <option
                                    value="<?= (int) $s['id']; ?>"
                                    data-kelas-id="<?= (int) ($s['kelas_id'] ?? 0); ?>"
                                    data-kelas="<?= htmlspecialchars($kelas_nama); ?>"
                                    <?= (int) $data['siswa_id'] === (int) $s['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas</label>
                        <input type="text" id="nama_kelas" class="form-control"
                               value="" readonly>
                        <input type="hidden" name="kelas_id" id="kelas_id"
                               value="<?= (int) $data['kelas_id']; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control"
                               value="<?= htmlspecialchars($tahun['nama'] ?? '-'); ?>" readonly>
                        <input type="hidden" name="tahun_ajaran_id"
                               value="<?= $tahun_id; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pelanggaran</label>
                        <select name="pelanggaran_id" id="pelanggaran"
                                class="form-select" required>
                            <option value="">-- Pilih Pelanggaran --</option>
                            <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>
                                <option
                                    value="<?= (int) $p['id']; ?>"
                                    data-poin="<?= (int) $p['poin']; ?>"
                                    data-kategori="<?= (int) $p['pelanggaran_kategori_id']; ?>"
                                    <?= (int) $data['pelanggaran_id'] === (int) $p['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($p['nama'] . ' (' . $p['poin'] . ' poin)'); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <input type="hidden" name="pelanggaran_kategori_id"
                               id="kategori"
                               value="<?= (int) $data['pelanggaran_kategori_id']; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guru Pelapor</label>
                        <input type="text" class="form-control"
                               value="<?= htmlspecialchars($guru['nama'] ?? '-'); ?>" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="<?= htmlspecialchars($data['tanggal'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Poin</label>
                        <input type="number" name="poin" id="poin"
                               class="form-control" min="0"
                               value="<?= (int) $data['poin']; ?>" readonly required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <?php
                            $daftar_status = ['Ditindaklanjuti', 'Proses', 'Selesai'];
                            foreach ($daftar_status as $status) {
                            ?>
                                <option value="<?= $status; ?>"
                                    <?= ($data['status'] ?? '') === $status ? 'selected' : ''; ?>>
                                    <?= $status; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control"
                                  rows="3"><?= htmlspecialchars($data['keterangan'] ?? ''); ?></textarea>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>

                <a href="catat_pelanggaran.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

            </form>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {

    function isiKelas() {
        const opsi = $('#siswa option:selected');
        $('#nama_kelas').val(opsi.data('kelas') || '');
        $('#kelas_id').val(opsi.data('kelas-id') || '');
    }

    function isiPelanggaran() {
        const opsi = $('#pelanggaran option:selected');
        $('#poin').val(opsi.data('poin') ?? '');
        $('#kategori').val(opsi.data('kategori') ?? '');
    }

    $('#siswa').on('change', isiKelas);
    $('#pelanggaran').on('change', isiPelanggaran);

    isiKelas();
    isiPelanggaran();
});
</script>

</body>
</html>
