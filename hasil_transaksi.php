<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

// Export PDF
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    ob_start();
    include 'hasil_transaksi_pdf.php';
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Hasil_Transaksi_PD_EKOLAN.pdf', ['Attachment' => true]);
    exit;
}

// Ambil filter
$jenis = $_GET['jenis'] ?? '';
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

// Query utama
$sql = "SELECT * FROM transaksi WHERE 1=1";

if ($jenis == 'debet') {
    $sql .= " AND jenis_transaksi = 'debet'";
} elseif ($jenis == 'kredit') {
    $sql .= " AND jenis_transaksi = 'kredit'";
}

if ($tanggal_awal && $tanggal_akhir) {
    $sql .= " AND DATE(tanggal) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

$sql .= " ORDER BY tanggal DESC";

$query = mysqli_query($koneksi, $sql);
if (!$query) die("Query Error: " . mysqli_error($koneksi));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Transaksi - PD. EKOLAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
        }
        .table th {
            background-color: #e8f0fe;
        }
        .keterangan-item, .pcs-item {
            display: block;
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary">
                <i class="bi bi-list-check"></i> Hasil Transaksi
            </h4>
            <div>
                <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>"
                   class="btn btn-outline-danger mb-2">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
                <br>
                <a href="histori_hapus.php" class="btn btn-outline-dark">
                    <i class="bi bi-clock-history"></i> Histori Hapus
                </a>
            </div>
        </div>

        <!-- Filter Jenis -->
        <div class="mb-3">
            <a href="?jenis=debet" class="btn btn-success <?= ($jenis=='debet')?'active':'' ?>">Pemasukan</a>
            <a href="?jenis=kredit" class="btn btn-danger <?= ($jenis=='kredit')?'active':'' ?>">Pengeluaran</a>
        </div>

        <!-- FILTER TANGGAL (PER HARI) -->
        <form method="GET" class="d-flex flex-wrap gap-2 mb-4">
            <input type="hidden" name="jenis" value="<?= htmlspecialchars($jenis) ?>">

            <input type="date" name="tanggal_awal" class="form-control w-auto"
                   value="<?= htmlspecialchars($tanggal_awal) ?>">

            <input type="date" name="tanggal_akhir" class="form-control w-auto"
                   value="<?= htmlspecialchars($tanggal_akhir) ?>">

            <button type="submit" class="btn btn-outline-secondary">
                <i class="bi bi-filter-circle"></i> Filter
            </button>
        </form>

        <!-- TABEL -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>PCS</th>
                    <th>Jumlah</th>
                    <th>Tipe</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($query) > 0):
                    while ($row = mysqli_fetch_assoc($query)):
                        $detail_html = '';
                        $pcs_html = '';

                        if ($row['jenis_transaksi'] == 'debet') {
                            $detail = $koneksi->query("
                                SELECT d.*, p.nama_produk
                                FROM detail_transaksi d
                                JOIN produk p ON d.id_produk = p.id_produk
                                WHERE d.id_transaksi = '{$row['id_transaksi']}'
                            ");
                            ob_start();
                            while ($d = $detail->fetch_assoc()) {
                                echo "<span class='keterangan-item'>{$d['nama_produk']}</span>";
                                $pcs_html .= "<span class='pcs-item'>{$d['jumlah_produk']}</span>";
                            }
                            $detail_html = ob_get_clean();
                        } else {
                            $detail_html = htmlspecialchars($row['keterangan']);
                            $pcs_html = $row['pcs'] ?: '-';
                        }
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= $detail_html ?></td>
                    <td><?= $pcs_html ?: '-' ?></td>
                    <td class="text-end">Rp <?= number_format($row['jumlah'],0,',','.') ?></td>
                    <td>
                        <span class="badge <?= $row['jenis_transaksi']=='debet'?'bg-success':'bg-danger' ?>">
                            <?= $row['jenis_transaksi']=='debet'?'Pemasukan':'Pengeluaran' ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Silakan pilih rentang tanggal terlebih dahulu.
                    </td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
