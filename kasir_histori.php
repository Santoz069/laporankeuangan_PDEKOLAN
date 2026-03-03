<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'kasir') {
    header("Location: login.php");
    exit;
}

$transaksi = $koneksi->query("SELECT * FROM transaksi WHERE jenis_transaksi = 'debet' ORDER BY id_transaksi DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .content { margin-left: 260px; padding: 30px; }
        .card { border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); }
        .table th, .table td { vertical-align: top; }
        .keterangan-item, .pcs-item { display: block; font-size: 14px; }
    </style>
</head>
<body>
    <?php include 'kasir_sidebar.php'; ?>

    <div class="content">
        <h3 class="mb-4"><i class="bi bi-clock-history"></i> Riwayat Transaksi</h3>
        <div class="card p-4">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah (pcs)</th>
                        <th>Nominal (Rp)</th>
                        <th>Jenis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $transaksi->fetch_assoc()): ?>
                        <?php
                        $detail = $koneksi->query("
                            SELECT d.*, p.nama_produk 
                            FROM detail_transaksi d
                            JOIN produk p ON d.id_produk = p.id_produk
                            WHERE d.id_transaksi = '{$row['id_transaksi']}'
                        ");

                        ob_start();
                        $pcs_html = "";
                        while ($d = $detail->fetch_assoc()) {
                            echo "<span class='keterangan-item'>{$d['nama_produk']}</span>";
                            $pcs_html .= "<span class='pcs-item'>{$d['jumlah_produk']}</span>";
                        }
                        $keterangan_html = ob_get_clean();
                        ?>
                        <tr>
                            <td><?= $row['tanggal'] ?></td>
                            <td><?= $keterangan_html ?: $row['keterangan'] ?></td>
                            <td><?= $pcs_html ?></td>
                            <td>Rp <?= number_format($row['jumlah']) ?></td>
                            <td><span class="badge bg-success">Pemasukan</span></td>
                            <td>
                                <a href="hapus_transaksi.php?id=<?= $row['id_transaksi'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                   <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
