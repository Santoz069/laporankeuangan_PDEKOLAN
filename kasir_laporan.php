<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'kasir') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Ambil filter bulan & tahun
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : '';
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : '';

$sql = "SELECT * FROM transaksi WHERE jenis_transaksi = 'debet'";
if ($bulan) $sql .= " AND MONTH(tanggal) = $bulan";
if ($tahun) $sql .= " AND YEAR(tanggal) = $tahun";
$sql .= " ORDER BY tanggal DESC";

$query = mysqli_query($koneksi, $sql);
if (!$query) die("Query Error: " . mysqli_error($koneksi));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Kasir - PD. EKOLAN</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color: #f1f6f9; font-family: 'Segoe UI', sans-serif; }
.content { margin-left: 260px; padding: 30px; }
.card { background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px; }
.table th { background-color: #e8f0fe; }
.keterangan-item, .pcs-item { display: block; font-size: 14px; }
</style>
</head>
<body>

<?php include 'kasir_sidebar.php'; ?>

<div class="content">
<div class="card">
<h4 class="mb-4 text-primary"><i class="bi bi-journal-text"></i> Laporan Transaksi Kasir</h4>

<form method="GET" class="d-flex gap-2 flex-wrap mb-3">
    <select name="bulan" class="form-select w-auto">
        <option value="">-- Pilih Bulan --</option>
        <?php for ($i = 1; $i <= 12; $i++): ?>
            <option value="<?= $i ?>" <?= ($bulan == $i) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 10)) ?></option>
        <?php endfor; ?>
    </select>
    <select name="tahun" class="form-select w-auto">
        <option value="">-- Pilih Tahun --</option>
        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
            <option value="<?= $y ?>" <?= ($tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
        <?php endfor; ?>
    </select>
    <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-filter-circle"></i> Filter</button>
</form>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-success">
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Keterangan</th>
    <th>PCS</th>
    <th>Jumlah</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php 
$no = 1; 
if (mysqli_num_rows($query) > 0):
    while ($row = mysqli_fetch_assoc($query)): 
        $detail = $koneksi->query("
            SELECT d.*, p.nama_produk 
            FROM detail_transaksi d
            JOIN produk p ON d.id_produk = p.id_produk
            WHERE d.id_transaksi = '{$row['id_transaksi']}'
        ");
        $namaProduk = "";
        $pcsText = "";
        while ($d = $detail->fetch_assoc()) {
            $namaProduk .= "<span class='keterangan-item'>{$d['nama_produk']}</span>";
            $pcsText .= "<span class='pcs-item'>{$d['jumlah_produk']}</span>";
        }
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= date("d-m-Y", strtotime($row['tanggal'])) ?></td>
    <td><?= $namaProduk ?: htmlspecialchars($row['keterangan']) ?></td>
    <td><?= $pcsText ?></td>
    <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
    <td><span class="badge bg-success">Pemasukan</span></td>
    <td>
        <a href="nota.php?id_transaksi=<?= $row['id_transaksi'] ?>&from=laporan" class="btn btn-sm btn-primary" target="_blank">
            <i class="bi bi-printer"></i> Print Nota
        </a>
    </td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="7" class="text-center text-muted">Tidak ada data pemasukan.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</body>
</html>
