<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'kasir') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Ambil data produk
$keyword = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
$sql = "SELECT * FROM produk";
if ($keyword != '') {
    $sql .= " WHERE nama_produk LIKE '%$keyword%' OR kategori LIKE '%$keyword%'";
}
$sql .= " ORDER BY kategori, nama_produk ASC";
$data = mysqli_query($koneksi, $sql);
?>

<?php include 'kasir_sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stok Barang - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .content {
            margin-left: 260px;
            padding: 40px;
            animation: fadeIn 0.8s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .search-box {
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .table thead {
            background-color: #e3f2fd;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .badge-stok {
            font-size: 0.85rem;
            padding: 6px 10px;
            border-radius: 8px;
        }
        .low-stock {
            background-color: #ffc107;
            color: #000;
        }
        .good-stock {
            background-color: #198754;
            color: #fff;
        }
        .no-stock {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary fw-bold"><i class="bi bi-box-seam"></i> Stok Barang</h4>
        <form method="GET" class="d-flex search-box">
            <input type="text" name="cari" class="form-control me-2" placeholder="Cari produk atau kategori..." value="<?= htmlspecialchars($keyword); ?>">
            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="card p-4">
        <?php if (mysqli_num_rows($data) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                            <th>Harga Jual (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($data)): 
                            $stok = (int)$row['stok'];
                            if ($stok == 0) {
                                $badge = "no-stock";
                                $label = "Habis";
                            } elseif ($stok <= 5) {
                                $badge = "low-stock";
                                $label = "Menipis";
                            } else {
                                $badge = "good-stock";
                                $label = "Aman";
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['kategori']); ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['nama_produk']); ?></td>
                            <td><span class="badge badge-stok <?= $badge; ?>"><?= $stok . " (" . $label . ")"; ?></span></td>
                            <td class="text-end">Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center p-5 text-muted">
                <i class="bi bi-box text-secondary" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">Tidak ada data stok yang tersedia.</p>
                <small>Silakan periksa kembali atau hubungi admin.</small>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
