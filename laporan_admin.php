<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

$tanggal_awal  = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

$filter = "";
$laporanJudul = "Laporan Keuangan";

if ($tanggal_awal && $tanggal_akhir) {
    $filter = "WHERE DATE(tanggal) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
    $laporanJudul = "Laporan Keuangan Tanggal " .
        date('d-m-Y', strtotime($tanggal_awal)) . " s/d " .
        date('d-m-Y', strtotime($tanggal_akhir));
}

$query = mysqli_query($koneksi, "SELECT * FROM transaksi $filter ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f1f6f9; font-family: 'Segoe UI', sans-serif; }
        .content { margin-left: 260px; padding: 30px; min-height: 100vh; }
        .card { background-color: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .table th { background-color: #e8f0fe; }
        .keterangan-item { display: block; font-size: 14px; }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <h4 class="mb-4 text-primary">
        <i class="bi bi-file-earmark-bar-graph me-2"></i>
        <?= $laporanJudul ?>
    </h4>

    <!-- FILTER TANGGAL -->
    <form method="get" class="row g-3 mb-4 w-75">
        <div class="col-md-4">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="tanggal_awal" class="form-control"
                   value="<?= htmlspecialchars($tanggal_awal) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="tanggal_akhir" class="form-control"
                   value="<?= htmlspecialchars($tanggal_akhir) ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-filter"></i> Tampilkan
            </button>
        </div>
    </form>

    <!-- TABEL TRANSAKSI -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Tipe</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            $totalMasuk = 0;
            $totalKeluar = 0;

            if (mysqli_num_rows($query) > 0):
                while ($row = mysqli_fetch_assoc($query)):
                    $jumlah = $row['jumlah'];
                    $tipe = $row['jenis_transaksi'];

                    if ($tipe == 'debet') $totalMasuk += $jumlah;
                    if ($tipe == 'kredit') $totalKeluar += $jumlah;

                    $detail = $koneksi->query("
                        SELECT d.*, p.nama_produk
                        FROM detail_transaksi d
                        JOIN produk p ON d.id_produk = p.id_produk
                        WHERE d.id_transaksi = '{$row['id_transaksi']}'
                    ");

                    ob_start();
                    while ($d = $detail->fetch_assoc()) {
                        echo "<span class='keterangan-item'>{$d['nama_produk']}</span>";
                    }
                    $keterangan_html = ob_get_clean();
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= $keterangan_html ?: htmlspecialchars($row['keterangan']) ?></td>
                    <td>Rp <?= number_format($jumlah, 0, ',', '.') ?></td>
                    <td>
                        <span class="badge <?= $tipe=='debet'?'bg-success':'bg-danger' ?>">
                            <?= $tipe=='debet'?'Pemasukan':'Pengeluaran' ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Silakan pilih rentang tanggal terlebih dahulu.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- RINGKASAN -->
    <div class="mb-4">
        <h5>Pemasukan (Debet): <strong class="text-success">Rp <?= number_format($totalMasuk,0,',','.') ?></strong></h5>
        <h5>Pengeluaran (Kredit): <strong class="text-danger">Rp <?= number_format($totalKeluar,0,',','.') ?></strong></h5>
        <h5>Saldo Akhir: <strong class="text-primary">Rp <?= number_format($totalMasuk - $totalKeluar,0,',','.') ?></strong></h5>
    </div>

    <!-- GRAFIK -->
    <canvas id="chart" height="100"></canvas>
</div>

<script>
const ctx = document.getElementById('chart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Pemasukan', 'Pengeluaran'],
        datasets: [{
            data: [<?= $totalMasuk ?>, <?= $totalKeluar ?>],
            backgroundColor: ['#198754', '#dc3545']
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        responsive: true
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
