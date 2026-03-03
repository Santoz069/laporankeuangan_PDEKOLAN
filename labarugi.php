<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// ==== Simpan laporan laba rugi ke database ====
if (isset($_POST['simpan_laba_rugi'])) {
    $periode = mysqli_real_escape_string($koneksi, $_POST['periode']);
    $pendapatan = floatval($_POST['pendapatan']);
    $beban = floatval($_POST['beban']);
    $laba = floatval($_POST['laba']);

    $cek = mysqli_query($koneksi, "SELECT * FROM laba_rugi WHERE periode='$periode'");
    if (!$cek) {
        die("Error cek laporan: " . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($cek) > 0) {
        $pesan = "❗ Laporan untuk periode $periode sudah ada.";
    } else {
        $simpan = mysqli_query($koneksi, "
            INSERT INTO laba_rugi (periode, total_pendapatan, total_beban, laba_bersih)
            VALUES ('$periode', '$pendapatan', '$beban', '$laba')
        ");
        $pesan = $simpan
            ? "✅ Laporan Laba Rugi periode $periode berhasil disimpan."
            : "❌ Gagal menyimpan laporan: " . mysqli_error($koneksi);
    }
}

// ==== Ambil data dari neraca_saldo + akun ====
$akunData = mysqli_query($koneksi, "
    SELECT a.kode_akun, a.nama_akun, a.jenis_akun, 
           n.debet, n.kredit
    FROM akun a
    LEFT JOIN neraca_saldo n ON a.id_akun = n.id_akun
    ORDER BY a.kode_akun ASC
");
if (!$akunData) {
    die("Error ambil data akun: " . mysqli_error($koneksi));
}

$labaPendapatan = 0;
$labaBeban = 0;
$aktiva = [];
$pasiva = [];
$modal = [];

while ($row = mysqli_fetch_assoc($akunData)) {
    $debet = floatval($row['debet']);
    $kredit = floatval($row['kredit']);
    $saldo = $debet - $kredit;

    switch ($row['jenis_akun']) {
        case 'Pendapatan':
            $labaPendapatan += $kredit;
            break;
        case 'Beban':
            $labaBeban += $debet;
            break;
        case 'Aktiva':
            $aktiva[] = $row + ['saldo' => $saldo];
            break;
        case 'Pasiva':
            $pasiva[] = $row + ['saldo' => $saldo];
            break;
        case 'Modal':
            $modal[] = $row + ['saldo' => $saldo];
            break;
    }
}

$labaBersih = $labaPendapatan - $labaBeban;

// ==== Ambil riwayat laba rugi ====
$riwayat = mysqli_query($koneksi, "SELECT * FROM laba_rugi ORDER BY id_laba_rugi DESC");
if (!$riwayat) {
    die("Error ambil riwayat: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Keuangan - PD. EKOLAN</title>
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
    animation: fadeIn 0.6s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 25px;
}
h4 { font-weight: 600; }
.table thead { background-color: #e3f2fd; }
.table tfoot { background-color: #f8fbff; font-weight: bold; }
.section-title {
    margin-top: 30px;
    color: #0d6efd;
    border-left: 5px solid #0d6efd;
    padding-left: 10px;
}
</style>
</head>

<body>
<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary mb-0"><i class="bi bi-bar-chart"></i> Laporan Keuangan PD. EKOLAN</h4>
            <form method="POST" class="d-flex align-items-center" onsubmit="return confirm('Simpan laporan laba rugi periode ini?');">
                <input type="hidden" name="pendapatan" value="<?= $labaPendapatan ?>">
                <input type="hidden" name="beban" value="<?= $labaBeban ?>">
                <input type="hidden" name="laba" value="<?= $labaBersih ?>">
                <input type="text" name="periode" class="form-control form-control-sm me-2" placeholder="cth: Oktober 2025" required>
                <button type="submit" name="simpan_laba_rugi" class="btn btn-success btn-sm">
                    <i class="bi bi-save"></i> Simpan Laba Rugi
                </button>
            </form>
        </div>

        <?php if (!empty($pesan)): ?>
            <div class="alert alert-info py-2"><?= $pesan; ?></div>
        <?php endif; ?>

        <!-- Laporan Laba Rugi -->
        <h5 class="section-title"><i class="bi bi-cash-stack"></i> Laporan Laba Rugi</h5>
        <table class="table table-bordered mt-3">
            <thead>
                <tr><th>Keterangan</th><th class="text-end">Jumlah (Rp)</th></tr>
            </thead>
            <tbody>
                <tr><td>Pendapatan</td><td class="text-end text-success">Rp <?= number_format($labaPendapatan, 0, ',', '.') ?></td></tr>
                <tr><td>Beban</td><td class="text-end text-danger">Rp <?= number_format($labaBeban, 0, ',', '.') ?></td></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>Laba Bersih</td>
                    <td class="text-end fw-bold <?= $labaBersih >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $labaBersih >= 0 ? 'Rp ' . number_format($labaBersih, 0, ',', '.') : '(Rp ' . number_format(abs($labaBersih), 0, ',', '.') . ')' ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Laporan Neraca -->
        <h5 class="section-title"><i class="bi bi-columns-gap"></i> Laporan Neraca</h5>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="fw-bold text-primary">Aktiva</h6>
                <table class="table table-sm table-bordered">
                    <tbody>
                        <?php
                        $totalAktiva = 0;
                        foreach ($aktiva as $a) {
                            $totalAktiva += $a['saldo'];
                            echo "<tr><td>{$a['nama_akun']}</td><td class='text-end'>Rp " . number_format($a['saldo'], 0, ',', '.') . "</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr><td>Total Aktiva</td><td class="text-end fw-bold">Rp <?= number_format($totalAktiva, 0, ',', '.') ?></td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="col-md-6">
                <h6 class="fw-bold text-primary">Pasiva & Modal</h6>
                <table class="table table-sm table-bordered">
                    <tbody>
                        <?php
                        $totalPasivaModal = 0;
                        foreach ($pasiva as $p) {
                            $totalPasivaModal += $p['saldo'];
                            echo "<tr><td>{$p['nama_akun']}</td><td class='text-end'>Rp " . number_format($p['saldo'], 0, ',', '.') . "</td></tr>";
                        }
                        foreach ($modal as $m) {
                            $totalPasivaModal += $m['saldo'];
                            echo "<tr><td>{$m['nama_akun']}</td><td class='text-end'>Rp " . number_format($m['saldo'], 0, ',', '.') . "</td></tr>";
                        }
                        $totalPasivaModal += $labaBersih;
                        ?>
                        <tr class="table-info fw-semibold">
                            <td>Laba Bersih Tahun Berjalan</td>
                            <td class="text-end"><?= 'Rp ' . number_format($labaBersih, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr><td>Total Pasiva & Modal</td><td class="text-end fw-bold">Rp <?= number_format($totalPasivaModal, 0, ',', '.') ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Status Keseimbangan -->
        <div class="mt-3">
            <?php
            if (abs($totalAktiva - $totalPasivaModal) < 1) {
                echo "<div class='alert alert-success py-2'><i class='bi bi-check-circle'></i> Neraca Seimbang ✅</div>";
            } else {
                echo "<div class='alert alert-warning py-2'><i class='bi bi-exclamation-triangle'></i> Neraca Belum Seimbang ⚠️</div>";
            }
            ?>
        </div>

        <!-- Riwayat Laporan Laba Rugi -->
        <h5 class="section-title"><i class="bi bi-clock-history"></i> Riwayat Laporan Laba Rugi</h5>
        <table class="table table-bordered mt-2">
            <thead>
                <tr class="table-light text-center">
                    <th>No</th><th>Periode</th><th>Pendapatan</th><th>Beban</th><th>Laba Bersih</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($riwayat && mysqli_num_rows($riwayat) > 0) {
                    $no = 1;
                    while ($r = mysqli_fetch_assoc($riwayat)) {
                        echo "<tr class='text-end'>
                                <td class='text-center'>{$no}</td>
                                <td class='text-start'>{$r['periode']}</td>
                                <td>Rp " . number_format($r['total_pendapatan'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($r['total_beban'], 0, ',', '.') . "</td>
                                <td class='fw-semibold " . ($r['laba_bersih'] >= 0 ? 'text-success' : 'text-danger') . "'>
                                    " . ($r['laba_bersih'] >= 0 ? 'Rp ' . number_format($r['laba_bersih'], 0, ',', '.') : '(Rp ' . number_format(abs($r['laba_bersih']), 0, ',', '.') . ')') . "
                                </td>
                              </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted fst-italic py-3'>Belum ada laporan yang disimpan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
