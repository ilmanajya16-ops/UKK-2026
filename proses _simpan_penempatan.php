<?php
// ==================================================
// PROSES SIMPAN PENEMPATAN SISWA
// File: proses_simpan_penempatan.php
// ==================================================

include 'includes/cek_session.php';
include 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function kembali($pesan, $tipe = 'pesan_error')
{
    $_SESSION[$tipe] = $pesan;
    header("Location: penempatan_siswa.php");
    exit;
}

// Hanya menerima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    kembali('Permintaan tidak valid.');
}

// Ambil data form
$siswa_id        = (int)($_POST['siswa_id'] ?? 0);
$tahun_ajaran_id = (int)($_POST['tahun_ajaran_id'] ?? 0);
$kelas_id        = (int)($_POST['kelas_id'] ?? 0);
$tanggal_mulai   = trim($_POST['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
$status_aktif    = isset($_POST['status_aktif']) ? (int)$_POST['status_aktif'] : -1;

// Validasi input
if (
    $siswa_id <= 0 ||
    $tahun_ajaran_id <= 0 ||
    $kelas_id <= 0 ||
    !in_array($status_aktif, [0, 1], true)
) {
    kembali('Data penempatan belum lengkap atau tidak valid.');
}

if (
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai)
) {
    kembali('Format tanggal tidak valid.');
}

if (
    !checkdate((int)substr($tanggal_mulai, 5, 2), (int)substr($tanggal_mulai, 8, 2), (int)substr($tanggal_mulai, 0, 4)) ||
    !checkdate((int)substr($tanggal_selesai, 5, 2), (int)substr($tanggal_selesai, 8, 2), (int)substr($tanggal_selesai, 0, 4))
) {
    kembali('Tanggal yang dimasukkan tidak valid.');
}

if ($tanggal_selesai < $tanggal_mulai) {
    kembali('Tanggal selesai tidak boleh sebelum tanggal mulai.');
}

// Helper: cek apakah data aktif dengan id tertentu ada di tabel
function cek_ada($koneksi, $tabel, $id, $pesan_gagal)
{
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM $tabel WHERE id = ? AND status_aktif = 1");

    if (!$stmt) {
        throw new Exception(mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $ada = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if (!$ada) {
        throw new Exception($pesan_gagal);
    }
}

mysqli_begin_transaction($koneksi);

try {

    cek_ada($koneksi, 't_siswa', $siswa_id, 'Siswa tidak ditemukan atau tidak aktif.');
    cek_ada($koneksi, 't_tahun_ajaran', $tahun_ajaran_id, 'Tahun ajaran tidak ditemukan atau tidak aktif.');
    cek_ada($koneksi, 't_kelas', $kelas_id, 'Kelas tidak ditemukan atau tidak aktif.');

    // Cek penempatan siswa pada tahun ajaran tersebut
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_kelas_siswa
         WHERE siswa_id = ? AND tahun_ajaran_id = ?
         LIMIT 1"
    );

    if (!$stmt) {
        throw new Exception(mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, "ii", $siswa_id, $tahun_ajaran_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_lama);

    $sudah_ada = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($sudah_ada) {
        // UPDATE
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_kelas_siswa
             SET kelas_id = ?,
                 tanggal_mulai = ?,
                 tanggal_selesai = ?,
                 status_aktif = ?,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = ?"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt, "issii", $kelas_id, $tanggal_mulai, $tanggal_selesai, $status_aktif, $id_lama);
        $pesan = 'Penempatan siswa berhasil diperbarui!';

    } else {
        // INSERT
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO t_kelas_siswa
                (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception(mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt, "iiissi", $siswa_id, $tahun_ajaran_id, $kelas_id, $tanggal_mulai, $tanggal_selesai, $status_aktif);
        $pesan = 'Penempatan siswa berhasil disimpan!';
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
    mysqli_commit($koneksi);

    kembali($pesan, 'pesan_sukses');

} catch (Throwable $e) {

    mysqli_rollback($koneksi);
    kembali('Gagal menyimpan penempatan: ' . $e->getMessage());
}