<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// ===== FILTER BULAN DAN TAHUN =====
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// ===== PAGINATION =====
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ===== AMBIL DATA JURNAL PER BULAN =====
$query = mysqli_query($koneksi, "
    SELECT j.*, a.nama_akun 
    FROM jurnal_umum j
    JOIN akun a ON j.id_akun = a.id_akun
    WHERE MONTH(j.tanggal) = '$bulan' AND YEAR(j.tanggal) = '$tahun'
    ORDER BY j.tanggal ASC
    LIMIT $limit OFFSET $offset
");

$total_query = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM jurnal_umum 
    WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'
");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jurnal Umum - PD. EKOLAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* === GENERAL === */
        body {
            background-color: #f1f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            background-color: #fff;
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 25px;
        }

        /* === TABLE === */
        .table thead {
            background-color: #e3f2fd;
        }
        .table tbody tr:hover {
            background-color: #f8fbff;
        }
        .produk-item {
            display: block;
            font-size: 13px;
            color: #555;
        }

        /* === BUTTONS & PAGINATION === */
        .btn {
            border-radius: 8px;
            transition: 0.2s ease;
        }
        .btn:hover {
            transform: scale(1.03);
        }
        .pagination .page-link {
            border-radius: 6px;
        }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card">
        <h4 class="mb-4 text-primary">
            <i class="bi bi-journal-text me-2"></i> Jurnal Umum
        </h4>

        <!-- ===== FILTER FORM ===== -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Bulan</label>
                <select name="bulan" class="form-select">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $bulan) ? 'selected' : '';
                        echo "<option value='$i' $selected>" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Tahun</label>
                <select name="tahun" class="form-select">
                    <?php
                    for ($t = date('Y') - 5; $t <= date('Y'); $t++) {
                        $selected = ($t == $tahun) ? 'selected' : '';
                        echo "<option value='$t' $selected>$t</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Tampilkan
                </button>
            </div>
        </form>

        <!-- ===== TABEL JURNAL ===== -->
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Tanggal</th>
                        <th>Akun</th>
                        <th>Keterangan (Produk)</th>
                        <th>Debit (Rp)</th>
                        <th>Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_debet = 0;
                    $total_kredit = 0;

                    if ($total_data > 0 && mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $tanggal = date("d-m-Y", strtotime($row['tanggal']));
                            $debet = $row['debet'];
                            $kredit = $row['kredit'];
                            $debet_fmt = $debet > 0 ? "Rp " . number_format($debet, 0, ',', '.') : "-";
                            $kredit_fmt = $kredit > 0 ? "Rp " . number_format($kredit, 0, ',', '.') : "-";

                            // Keterangan bersih (hilangkan "(Transaksi xx)")
                            $keterangan = preg_replace('/\(Transaksi\s*\d+\)/i', '', $row['keterangan']);
                            $keterangan = trim($keterangan);
                            $keterangan = preg_replace('/\s+/', ' ', $keterangan);

                            if (stripos($keterangan, 'penjualan') !== false) {
                            $keterangan = "Pemasukan - Penjualan produk";
                            }

                            // Ambil detail produk
                            $produk_html = "";
                            preg_match('/#(\d+)/', $row['keterangan'], $matches);
                            if (!empty($matches[1])) {
                                $id_transaksi = intval($matches[1]);
                                $produk_query = mysqli_query($koneksi, "
                                    SELECT p.nama_produk, p.satuan, d.jumlah_produk
                                    FROM detail_transaksi d
                                    JOIN produk p ON d.id_produk = p.id_produk
                                    WHERE d.id_transaksi = '$id_transaksi'
                                ");

                                while ($p = mysqli_fetch_assoc($produk_query)) {
                                    $satuan = strtolower(!empty($p['satuan']) ? $p['satuan'] : 'pcs');
                                    $produk_html .= "<span class='produk-item'>• {$p['nama_produk']} ({$p['jumlah_produk']} {$satuan})</span>";
                                }
                            }

                            echo "
                            <tr>
                                <td>{$tanggal}</td>
                                <td>{$row['nama_akun']}</td>
                                <td class='text-start'>{$keterangan}<br>{$produk_html}</td>
                                <td class='text-end text-success'>{$debet_fmt}</td>
                                <td class='text-end text-danger'>{$kredit_fmt}</td>
                            </tr>";

                            $total_debet += $debet;
                            $total_kredit += $kredit;
                        }

                        echo "
                        <tr class='fw-bold table-info text-end'>
                            <td colspan='3'>Total</td>
                            <td class='text-success'>Rp " . number_format($total_debet, 0, ',', '.') . "</td>
                            <td class='text-danger'>Rp " . number_format($total_kredit, 0, ',', '.') . "</td>
                        </tr>";
                    } else {
                        echo "
                        <tr>
                            <td colspan='5' class='text-center text-muted'>
                                Tidak ada data jurnal untuk bulan ini.
                                <br>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- ===== PAGINATION ===== -->
        <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center mt-4">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <!-- ===== POSTING KE BUKU BESAR ===== -->
        <?php if ($total_data > 0): ?>
        <div class="text-end mt-4">
            <a href="posting_bukubesar.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-primary">
                <i class="bi bi-arrow-right-circle"></i> 
                Posting ke Buku Besar Bulan <?= date("F", mktime(0, 0, 0, $bulan, 1)) ?> <?= $tahun ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
