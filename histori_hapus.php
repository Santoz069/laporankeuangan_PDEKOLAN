<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil tanggal dari GET (TANPA default hari ini)
$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

$histori = null;

// Query hanya dijalankan jika tanggal dipilih
if ($tanggal_awal !== '' && $tanggal_akhir !== '') {
    $stmt = $koneksi->prepare("
        SELECT * FROM histori_hapus 
        WHERE DATE(tanggal_hapus) BETWEEN ? AND ?
        ORDER BY tanggal_hapus DESC
    ");
    $stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);
    $stmt->execute();
    $histori = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Hapus Transaksi - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f6fa;
            font-family: "Poppins", sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 1000px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #0d6efd, #00b4d8);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .filter-box {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="text-center mb-4">
        <h3><i class="bi bi-trash"></i> Riwayat Hapus Transaksi</h3>
        <p class="text-muted">Menampilkan data penghapusan transaksi oleh kasir</p>
    </div>

    <a href="hasil_transaksi.php" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    <!-- FILTER TANGGAL -->
    <div class="filter-box">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Dari Tanggal</label>
                <input type="date" name="tanggal_awal" class="form-control"
                       value="<?= htmlspecialchars($tanggal_awal) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" class="form-control"
                       value="<?= htmlspecialchars($tanggal_akhir) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL DATA -->
    <div class="card">
        <div class="card-header text-center">
            <h5 class="mb-0">Riwayat Hapus Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kasir</th>
                            <th>Total Nominal</th>
                            <th>Keterangan</th>
                            <th>Tanggal & Waktu Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($histori === null): ?>
                            <tr>
                                <td colspan="4" class="text-muted py-3">
                                    <i class="bi bi-info-circle"></i>
                                    Silakan pilih rentang tanggal terlebih dahulu.
                                </td>
                            </tr>
                        <?php elseif ($histori->num_rows > 0): ?>
                            <?php while ($row = $histori->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nama_kasir']) ?></td>
                                    <td>Rp <?= number_format($row['total_nominal'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td><?= date('d-m-Y H:i:s', strtotime($row['tanggal_hapus'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-muted py-3">
                                    <i class="bi bi-x-circle"></i>
                                    Tidak ada data pada tanggal yang dipilih.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
