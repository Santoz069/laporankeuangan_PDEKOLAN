<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// 🔹 Ambil daftar akun
$akun_list = mysqli_query($koneksi, "
    SELECT DISTINCT a.id_akun, a.nama_akun 
    FROM buku_besar b
    JOIN akun a ON b.id_akun = a.id_akun
    ORDER BY a.nama_akun ASC
");

// 🔹 Filter akun & bulan
$filter_akun = isset($_GET['id_akun']) ? $_GET['id_akun'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';

// 🔹 Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// 🔹 Query utama
$sql = "
    SELECT b.*, a.nama_akun 
    FROM buku_besar b
    JOIN akun a ON b.id_akun = a.id_akun
    WHERE 1=1
";
if ($filter_akun != '') {
    $sql .= " AND b.id_akun = '$filter_akun'";
}
if ($filter_bulan != '') {
    $sql .= " AND MONTH(b.tanggal) = '$filter_bulan'";
}
$sql .= " ORDER BY b.tanggal ASC, b.id ASC LIMIT $start, $limit";
$data = mysqli_query($koneksi, $sql);

// 🔹 Hitung total data untuk pagination
$count_sql = "
    SELECT COUNT(*) AS total 
    FROM buku_besar b
    JOIN akun a ON b.id_akun = a.id_akun
    WHERE 1=1
";
if ($filter_akun != '') {
    $count_sql .= " AND b.id_akun = '$filter_akun'";
}
if ($filter_bulan != '') {
    $count_sql .= " AND MONTH(b.tanggal) = '$filter_bulan'";
}
$total_result = mysqli_query($koneksi, $count_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Buku Besar - PD. EKOLAN</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background-color: #f4f7fb;
    font-family: 'Segoe UI', sans-serif;
}
.content {
    margin-left: 260px;
    padding: 30px;
}
.card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
    padding: 25px;
}
h4 {
    color: #0d6efd;
    font-weight: 600;
}
.table {
    border-radius: 10px;
    overflow: hidden;
}
.table thead {
    background-color: #e3f2fd;
}
.table td, .table th {
    vertical-align: middle;
    font-size: 14px;
    padding: 10px;
}
.table tbody tr:hover {
    background-color: #f8fbff;
    transition: background 0.2s;
}
.text-success { color: #16a34a !important; }
.text-danger { color: #dc2626 !important; }
.date-header {
    background: #eef6ff;
    color: #0d6efd;
    font-weight: 600;
    padding: 8px 12px;
    border-left: 4px solid #0d6efd;
    margin-top: 20px;
    margin-bottom: 10px;
    border-radius: 6px;
}
.pagination {
    justify-content: center;
    margin-top: 20px;
}
@media (max-width: 768px) {
    .content { margin-left: 0; padding: 15px; }
}
</style>
</head>

<body>
<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card">
        <h4 class="mb-4"><i class="bi bi-book"></i> Buku Besar</h4>

        <?php if (isset($_GET['posted'])): ?>
            <div class="alert alert-success shadow-sm">
                <i class="bi bi-check-circle-fill"></i> Data berhasil diposting dari <b>Jurnal Umum</b> ke Buku Besar!
            </div>
        <?php endif; ?>

        <!-- 🔹 Filter Akun & Bulan -->
        <form method="GET" class="mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <select name="id_akun" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="">-- Tampilkan Semua Akun --</option>
                        <?php mysqli_data_seek($akun_list, 0); ?>
                        <?php while ($a = mysqli_fetch_assoc($akun_list)): ?>
                            <option value="<?= $a['id_akun']; ?>" <?= ($filter_akun == $a['id_akun']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($a['nama_akun']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3 ms-auto">
                    <select name="bulan" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Bulan --</option>
                        <?php
                        $bulan_nama = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        foreach ($bulan_nama as $num => $nama) {
                            $selected = ($filter_bulan == $num) ? 'selected' : '';
                            echo "<option value='$num' $selected>$nama</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </form>

        <!-- 🔹 Tabel Buku Besar -->
        <div class="table-responsive mt-3">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th style="width:200px;">Akun</th>
                        <th>Keterangan</th>
                        <th style="width:160px;">Debit (Rp)</th>
                        <th style="width:160px;">Kredit (Rp)</th>
                        <th style="width:160px;">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (mysqli_num_rows($data) > 0) {
                    $total_debit = 0;
                    $total_kredit = 0;
                    $current_date = '';

                    while ($row = mysqli_fetch_assoc($data)) {
                        $tanggal = date("d-m-Y", strtotime($row['tanggal']));
                        $debit = floatval($row['debit']);
                        $kredit = floatval($row['kredit']);
                        $saldo = floatval($row['saldo']);
                        $total_debit += $debit;
                        $total_kredit += $kredit;

                        if ($tanggal != $current_date) {
                            echo "<tr><td colspan='5' class='date-header'>📅 $tanggal</td></tr>";
                            $current_date = $tanggal;
                        }

                        $keterangan_bersih = preg_replace(['/Trx#\d+/','/#\d+/','/\(.*?\)/'], '', $row['keterangan']);
                        $keterangan_bersih = trim(preg_replace('/\s+/', ' ', $keterangan_bersih));

                        echo "<tr>
                                <td>{$row['nama_akun']}</td>
                                <td class='text-start'>{$keterangan_bersih}</td>
                                <td class='text-end text-success'>".($debit ? 'Rp '.number_format($debit,0,',','.') : '-')."</td>
                                <td class='text-end text-danger'>".($kredit ? 'Rp '.number_format($kredit,0,',','.') : '-')."</td>
                                <td class='text-end fw-semibold ".($saldo >= 0 ? 'text-success' : 'text-danger')."'>"
                                .($saldo >= 0 ? 'Rp '.number_format($saldo,0,',','.') : '(Rp '.number_format(abs($saldo),0,',','.').')')."
                                </td>
                              </tr>";
                    }

                    echo "<tr class='fw-bold table-info text-end'>
                            <td colspan='2' class='text-center'>Total</td>
                            <td class='text-success'>Rp ".number_format($total_debit,0,',','.')."</td>
                            <td class='text-danger'>Rp ".number_format($total_kredit,0,',','.')."</td>
                            <td>-</td>
                          </tr>";
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted py-4'>Belum ada data Buku Besar yang tersedia.</td></tr>";
                }
                ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php
                                $active = ($i == $page) ? 'active' : '';
                                $query_str = http_build_query(array_merge($_GET, ['page' => $i]));
                            ?>
                            <li class="page-item <?= $active; ?>">
                                <a class="page-link" href="?<?= $query_str; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
