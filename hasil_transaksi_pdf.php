<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$jenis = $_GET['jenis'] ?? '';
$bulan = intval($_GET['bulan'] ?? 0);
$tahun = intval($_GET['tahun'] ?? 0);

// Query sama seperti halaman utama
$sql = "SELECT * FROM transaksi WHERE 1=1";
if ($jenis == 'debet') {
    $sql .= " AND jenis_transaksi = 'debet'";
} elseif ($jenis == 'kredit') {
    $sql .= " AND jenis_transaksi = 'kredit'";
}
if ($bulan) $sql .= " AND MONTH(tanggal) = $bulan";
if ($tahun) $sql .= " AND YEAR(tanggal) = $tahun";
$sql .= " ORDER BY tanggal DESC";

$query = mysqli_query($koneksi, $sql);
if (!$query) die("Query Error: " . mysqli_error($koneksi));

// HTML untuk PDF
ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
body { font-family: 'Segoe UI', sans-serif; font-size: 12px; color: #000; }
h2 { text-align: center; color: #0d6efd; margin-bottom: 10px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
th { background-color: #e8f0fe; }
.badge { padding: 4px 8px; border-radius: 6px; color: #fff; font-size: 10px; }
.bg-success { background-color: #198754; }
.bg-danger { background-color: #dc3545; }
.keterangan-item, .pcs-item { display: block; font-size: 12px; }
</style>
</head>
<body>

<h2>Hasil Transaksi PD. EKOLAN</h2>

<table>
    <thead>
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
        while ($row = mysqli_fetch_assoc($query)):
            $detail_html = '';
            $pcs_html = '';

            // Jika pemasukan (debet)
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
            }

            // Jika pengeluaran (kredit)
            if ($row['jenis_transaksi'] == 'kredit') {
                $detail_html = htmlspecialchars($row['keterangan']);
                $pcs_html = ($row['pcs'] > 0) ? $row['pcs'] : '-';
            } else {
                if (trim($detail_html) == '') $detail_html = htmlspecialchars($row['keterangan']);
                if (trim($pcs_html) == '') $pcs_html = '-';
            }
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
            <td><?= $detail_html ?></td>
            <td><?= $pcs_html ?></td>
            <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td>
                <?php if ($row['jenis_transaksi'] == 'debet'): ?>
                    <span class="badge bg-success">Pemasukan</span>
                <?php else: ?>
                    <span class="badge bg-danger">Pengeluaran</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<p style="text-align:right; margin-top:20px;">
Dicetak pada: <?= date("d-m-Y H:i") ?>
</p>
</body>
</html>
