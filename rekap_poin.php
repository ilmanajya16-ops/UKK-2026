
<?php
// ============================================
// REKAP_POIN.PHP
// ============================================

// ============================================
// 1. KONEKSI DAN SESSION
// ============================================
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// FUNGSI KEAMANAN
// ============================================
function e($data)
{
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

// ============================================
// 2. MENGAMBIL TAHUN AJARAN AKTIF
// ============================================
$query_tahun = mysqli_query(
    $koneksi,
    "SELECT nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     LIMIT 1"
);

$tahun_ajaran = '-';

if ($query_tahun && mysqli_num_rows($query_tahun) > 0) {
    $data_tahun = mysqli_fetch_assoc($query_tahun);
    $tahun_ajaran = $data_tahun['nama'];
}

// ============================================
// 3. QUERY REKAP POIN SISWA
// ============================================
$sql = "SELECT
            s.id AS siswa_id,
            s.nis,
            s.nama AS nama_siswa,
            COALESCE(k.nama, '-') AS nama_kelas,
            COALESCE(p.total_poin, 0) AS total_poin,
            COALESCE(p.jumlah_pelanggaran, 0) AS jumlah_pelanggaran

        FROM t_siswa s

        LEFT JOIN (
            SELECT
                siswa_id,
                SUM(poin) AS total_poin,
                COUNT(*) AS jumlah_pelanggaran
            FROM t_pelanggaran_siswa
            GROUP BY siswa_id
        ) p ON p.siswa_id = s.id

        LEFT JOIN (
            SELECT
                siswa_id,
                MAX(kelas_id) AS kelas_id
            FROM t_kelas_siswa
            WHERE status_aktif = 1
            GROUP BY siswa_id
        ) ks ON ks.siswa_id = s.id

        LEFT JOIN t_kelas k ON k.id = ks.kelas_id

        ORDER BY total_poin DESC, s.nama ASC";

$query_rekap = mysqli_query($koneksi, $sql);

if (!$query_rekap) {
    die("Gagal mengambil rekap poin: " . e(mysqli_error($koneksi)));
}

// ============================================
// 4. MENGHITUNG RINGKASAN
// ============================================
$data_rekap = [];
$total_poin_semua = 0;
$total_catatan = 0;

while ($data = mysqli_fetch_assoc($query_rekap)) {

    $data_rekap[] = $data;

    $total_poin_semua += (int)$data['total_poin'];
    $total_catatan += (int)$data['jumlah_pelanggaran'];
}

$jumlah_siswa = count($data_rekap);

// ============================================
// 5. TANGGAL CETAK
// ============================================
$nama_kelas_cetak = 'Semua Kelas';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Poin Pelanggaran Siswa</title>

    <!-- BOOTSTRAP -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- DATATABLES -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f6fa;
            color: #212529;
        }

        /* ==========================================
           KONTEN UTAMA DAN JARAK SIDEBAR
        ========================================== */

        main.content {
            margin-left: 270px;
            width: calc(100% - 270px);
            min-height: 100vh;
            padding: 30px;
            box-sizing: border-box;
        }

        main.content > .container-fluid {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }

        /* ==========================================
           CARD
        ========================================== */

        .card {
            border: none;
            border-radius: 12px;
        }

        .card-body {
            padding: 25px;
        }

        /* ==========================================
           ANGKA RINGKASAN
        ========================================== */

        .angka {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }

        /* ==========================================
           HEADER TABEL PUTIH
        ========================================== */

        #tabelRekap {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        #tabelRekap th,
        #tabelRekap td {
            vertical-align: middle;
            white-space: nowrap;
            padding: 12px;
        }

        #tabelRekap thead th,
        #tabelRekap.table-striped thead th,
        #tabelRekap thead.table-dark th {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #212529 !important;
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: 600 !important;
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }

        #tabelRekap tbody td {
            color: #212529;
            border: 1px solid #dee2e6;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* ==========================================
           DATATABLES
        ========================================== */

        .dataTables_wrapper {
            width: 100%;
        }

        .dt-search input,
        .dataTables_filter input {
            border: 1px solid #ced4da !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
        }

        .dt-length select,
        .dataTables_length select {
            border: 1px solid #ced4da !important;
            border-radius: 6px !important;
            padding: 5px 8px !important;
        }

        /* ==========================================
           HEADER CETAK DAN TANDA TANGAN
        ========================================== */

        .header-cetak,
        .tanda-tangan {
            display: none;
        }

        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 767.98px) {

            main.content {
                margin-left: 0;
                width: 100%;
                padding: 18px;
            }

            .card-body {
                padding: 18px;
            }

            h2 {
                font-size: 22px;
            }

            .angka {
                font-size: 1.7rem;
            }

        }

        /* ==========================================
           PENGATURAN CETAK
        ========================================== */

        @page {
            size: A4 portrait;
            margin: 2cm;
        }

        @media print {

            /* SEMBUNYIKAN SELURUH ELEMEN */
            body * {
                visibility: hidden !important;
            }

            /* TAMPILKAN KONTEN UTAMA */
            main.content,
            main.content * {
                visibility: visible !important;
            }

            html,
            body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                font-family: "Times New Roman", serif !important;
                font-size: 12pt !important;
                color: #000000 !important;
            }

            /* POSISI KONTEN CETAK */
            main.content {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
                background: #ffffff !important;
            }

            main.content > .container-fluid {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* SIDEBAR DISEMBUNYIKAN */
            aside,
            nav,
            .sidebar,
            #sidebar,
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }

            /* KONTROL DATATABLES DISEMBUNYIKAN */
            .dt-search,
            .dt-length,
            .dt-paging,
            .dt-info,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_paginate,
            .dataTables_info {
                display: none !important;
                visibility: hidden !important;
            }

            /* HEADER LAPORAN */
            .header-cetak {
                display: block !important;
                visibility: visible !important;
                margin-bottom: 20px !important;
                color: #000000 !important;
            }

            .header-cetak * {
                visibility: visible !important;
                color: #000000 !important;
            }

            /* HILANGKAN HEADER CARD WEBSITE */
            .card-header {
                display: none !important;
            }

            /* HILANGKAN TAMPILAN CARD */
            .card,
            .card-body,
            .table-responsive {
                border: none !important;
                box-shadow: none !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }

            /* TABEL CETAK */
            #tabelRekap {
                width: 100% !important;
                border-collapse: collapse !important;
                font-family: "Times New Roman", serif !important;
                font-size: 10pt !important;
                color: #000000 !important;
            }

            #tabelRekap th,
            #tabelRekap td {
                border: 1px solid #000000 !important;
                padding: 7px !important;
                color: #000000 !important;
                background: #ffffff !important;
            }

            #tabelRekap thead th {
                text-align: center !important;
                font-weight: bold !important;
            }

            /* BADGE POIN */
            .badge {
                color: #000000 !important;
                background: #ffffff !important;
                font-weight: normal !important;
                border: none !important;
            }

            /* TANDA TANGAN */
            .tanda-tangan {
                display: block !important;
                visibility: visible !important;
                margin-top: 35px !important;
                page-break-inside: avoid !important;
            }

            .tanda-tangan * {
                visibility: visible !important;
                color: #000000 !important;
            }

            .tanda-tangan table,
            .tanda-tangan td {
                border: none !important;
            }

            h2,
            h3,
            h4,
            p {
                color: #000000 !important;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid p-0">

    <div class="row g-0">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="content">

            <div class="container-fluid">

                <!-- ================================= -->
                <!-- JUDUL HALAMAN -->
                <!-- ================================= -->

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 no-print">

                    <div>

                        <h2>Rekap Poin Pelanggaran Siswa</h2>

                        <p class="text-muted mb-0">
                            Ringkasan jumlah pelanggaran dan poin setiap siswa.
                        </p>

                    </div>

                    <div class="d-flex gap-2">

                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali ke Dashboard
                        </a>

                        <button
                            type="button"
                            onclick="window.print()"
                            class="btn btn-success">

                            Cetak Rekap

                        </button>

                    </div>

                </div>

                <!-- ================================= -->
                <!-- RINGKASAN -->
                <!-- ================================= -->

                <div class="row g-3 mb-4 no-print">

                    <div class="col-md-4">

                        <div class="card shadow-sm h-100">

                            <div class="card-body">

                                <p class="text-muted mb-1">
                                    Jumlah Siswa
                                </p>

                                <div class="angka">
                                    <?= number_format($jumlah_siswa); ?>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card shadow-sm h-100">

                            <div class="card-body">

                                <p class="text-muted mb-1">
                                    Total Poin
                                </p>

                                <div class="angka">
                                    <?= number_format($total_poin_semua); ?>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card shadow-sm h-100">

                            <div class="card-body">

                                <p class="text-muted mb-1">
                                    Jumlah Catatan Pelanggaran
                                </p>

                                <div class="angka">
                                    <?= number_format($total_catatan); ?>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- ================================= -->
                <!-- HEADER KHUSUS CETAK -->
                <!-- ================================= -->

                <div class="header-cetak mb-4">

                    <div class="text-center">

                        <h3 class="fw-bold mb-1">
                            SMK MUHAMMADIYAH TASIKMALAYA
                        </h3>

                        <p class="mb-1">
                            Alamat: ........................................................
                        </p>

                        <p class="mb-2">
                            Telepon: ........................ Email: ........................
                        </p>

                        <hr style="border: 2px solid black; opacity: 1;">

                        <h4 class="fw-bold text-uppercase mt-3">
                            REKAPITULASI POIN PELANGGARAN SISWA
                        </h4>

                        <p class="mb-3">
                            Tahun Ajaran:
                            <?= e($tahun_ajaran); ?>
                        </p>

                    </div>

                    <div class="mb-3">

                        <p class="mb-1">
                            Kelas:
                            <?= e($nama_kelas_cetak); ?>
                        </p>

                        <p class="mb-1">
                            Tanggal Cetak:
                            <?= date('d-m-Y'); ?>
                        </p>

                    </div>

                </div>

                <!-- ================================= -->
                <!-- TABEL REKAP POIN -->
                <!-- ================================= -->

                <div class="card shadow-sm">

                    <div class="card-header bg-white no-print">

                        <h5 class="mb-0 text-dark">
                            Data Rekap Poin Siswa
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table
                                id="tabelRekap"
                                class="table table-striped table-bordered table-hover align-middle w-100">

                                <thead>

                                    <tr>
                                        <th>No.</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jumlah Pelanggaran</th>
                                        <th>Total Poin</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($data_rekap as $data) { ?>

                                        <tr>

                                            <td class="text-center"></td>

                                            <td>
                                                <?= e($data['nis'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= e($data['nama_siswa'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= e($data['nama_kelas'] ?? '-'); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= (int)$data['jumlah_pelanggaran']; ?>
                                            </td>

                                            <td class="text-center">

                                                <span class="badge bg-warning text-dark">
                                                    <?= (int)$data['total_poin']; ?> poin
                                                </span>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                        <p class="text-muted mb-0 mt-3 no-print">
                            Total poin dihitung berdasarkan catatan pada
                            tabel t_pelanggaran_siswa.
                        </p>

                    </div>

                </div>

                <!-- ================================= -->
                <!-- TANDA TANGAN -->
                <!-- ================================= -->

                <div class="tanda-tangan">

                    <table style="width:100%; margin-top:30px;">

                        <tr>

                            <td style="width:50%; text-align:center;">

                                Mengetahui,<br>
                                Kepala Sekolah

                                <br><br><br><br>

                                <strong>(................................)</strong>

                                <br>

                                NIP. ................................

                            </td>

                            <td style="width:50%; text-align:center;">

                                Tasikmalaya, <?= date('d F Y'); ?><br>
                                Petugas/Pembuat Laporan

                                <br><br><br><br>

                                <strong>(................................)</strong>

                                <br>

                                NIP. ................................

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </main>

    </div>

</div>

<!-- ================================= -->
<!-- JAVASCRIPT -->
<!-- ================================= -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>

$(document).ready(function () {

    var tabel = $('#tabelRekap').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        searching: true,

        ordering: true,

        paging: true,

        info: true,

        autoWidth: false,

        order: [[5, 'desc'], [2, 'asc']],

        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: 0
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

            search: "Cari di tabel:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data rekap",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data siswa tidak ditemukan",

            emptyTable: "Belum ada data rekap poin siswa",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        }

    });

    // =====================================
    // CETAK SELURUH DATA
    // =====================================

    var panjangSebelumnya = 10;

    window.addEventListener('beforeprint', function () {

        panjangSebelumnya = tabel.page.len();

        tabel.page.len(-1).draw();

    });

    window.addEventListener('afterprint', function () {

        tabel.page.len(panjangSebelumnya).draw();

    });

});

</script>

</body>
</html>
