<?php
session_start();
include 'koneksi.php';

// 🔹 Tentukan asal halaman (transaksi atau laporan)
$asal = $_GET['from'] ?? 'transaksi'; // default dari kasir_transaksi

// 🔹 Cek id_transaksi dari GET atau SESSION
if (isset($_GET['id_transaksi'])) {
    $id_transaksi = intval($_GET['id_transaksi']);
} elseif (isset($_SESSION['nota'])) {
    $id_transaksi = intval($_SESSION['nota']);
} else {
    // Jika tidak ada ID, ambil transaksi terakhir
    $cek_terakhir = $koneksi->query("SELECT id_transaksi FROM transaksi ORDER BY id_transaksi DESC LIMIT 1");
    if ($cek_terakhir && $cek_terakhir->num_rows > 0) {
        $id_transaksi = $cek_terakhir->fetch_assoc()['id_transaksi'];
    } else {
        echo "<script>alert('Tidak ada data transaksi ditemukan.');window.location='kasir_transaksi.php';</script>";
        exit;
    }
}

// 🔹 Ambil data transaksi
$q_transaksi = $koneksi->query("SELECT * FROM transaksi WHERE id_transaksi='$id_transaksi'");
if ($q_transaksi && $q_transaksi->num_rows > 0) {
    $transaksi = $q_transaksi->fetch_assoc();
} else {
    echo "<script>alert('Data transaksi tidak ditemukan!');window.location='kasir_transaksi.php';</script>";
    exit;
}

// 🔹 Ambil detail transaksi
$detail = $koneksi->query("
    SELECT d.*, p.nama_produk 
    FROM detail_transaksi d
    JOIN produk p ON d.id_produk = p.id_produk
    WHERE d.id_transaksi='$id_transaksi'
");

// 🔹 Gunakan waktu dari kolom created_at untuk menampilkan jam transaksi real
$tanggalTransaksi = date("d-m-Y", strtotime($transaksi['created_at']));
$jamTransaksi = date("H:i:s", strtotime($transaksi['created_at']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota Transaksi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f8f9fa; padding: 20px; }
.nota {
    width: 320px; margin: auto; background: #fff;
    padding: 20px; border: 1px dashed #333; font-size: 13px;
}
.nota h5 { text-align: center; margin-bottom: 10px; font-weight: bold; }
.nota p { margin: 0; font-size: 12px; }
.nota table { width: 100%; font-size: 12px; margin-top: 10px; }
.nota table th, .nota table td { padding: 4px; }
.nota-footer {
    text-align: center; margin-top: 15px; font-size: 11px;
    border-top: 1px dashed #333; padding-top: 10px;
}
@media print {
    body { background: #fff; }
    .btn-print { display: none; }
    .nota { border: none; }
}
</style>
</head>
<body>

<div class="nota">
    <h5>PD. EKOLAN</h5>
    <p class="text-center">Jl. Depok No.52A, Kembangsari, Kec. Semarang Tengah, Kota Semarang</p>
    <hr>
    <p>No. Nota: <?= htmlspecialchars($transaksi['id_transaksi']) ?></p>
    <p>Tanggal: <?= $tanggalTransaksi ?>, <?= $jamTransaksi ?></p>
    <p>Kasir: <?= htmlspecialchars($_SESSION['username'] ?? '-') ?></p>
    <hr>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Sub</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            if ($detail && $detail->num_rows > 0):
                while ($row = $detail->fetch_assoc()):
                    $harga_satuan = ($row['jumlah_produk'] > 0) ? $row['subtotal'] / $row['jumlah_produk'] : 0;
                    $sub = $row['jumlah_produk'] * $harga_satuan;
                    $total += $sub;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                <td><?= $row['jumlah_produk'] ?></td>
                <td><?= number_format($harga_satuan, 0, ',', '.') ?></td>
                <td><?= number_format($sub, 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="4" class="text-center text-muted">Tidak ada item transaksi.</td></tr>
            <?php endif; ?>
            <tr>
                <td colspan="3"><b>Total</b></td>
                <td><b>Rp <?= number_format($total, 0, ',', '.') ?></b></td>
            </tr>
        </tbody>
    </table>

    <div class="nota-footer">
        <p>Terima kasih sudah berbelanja</p>
        <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
    </div>
</div>

<div class="text-center mt-3">
    <button class="btn btn-primary btn-print" onclick="window.print()">🖨 Cetak Nota</button>

    <?php if ($asal === 'laporan'): ?>
        <a href="kasir_laporan.php" class="btn btn-secondary btn-print">⬅ Kembali ke Laporan</a>
    <?php else: ?>
        <a href="kasir_transaksi.php" class="btn btn-secondary btn-print">⬅ Kembali ke Transaksi</a>
    <?php endif; ?>
</div>

</body>
</html>
