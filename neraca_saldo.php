<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Jika tombol simpan ditekan
if (isset($_POST['simpan_neraca'])) {
    // Hapus data lama
    mysqli_query($koneksi, "DELETE FROM neraca_saldo");

    // Masukkan hasil rekap baru dari buku besar
    $insert = mysqli_query($koneksi, "
        INSERT INTO neraca_saldo (id_akun, debet, kredit)
        SELECT 
            a.id_akun,
            COALESCE(SUM(b.debit), 0) AS debet,
            COALESCE(SUM(b.kredit), 0) AS kredit
        FROM akun a
        LEFT JOIN buku_besar b ON a.id_akun = b.id_akun
        GROUP BY a.id_akun
    ");

    if ($insert) {
        $pesan = "Neraca Saldo berhasil disimpan ke database.";
    } else {
        $pesan = "Gagal menyimpan Neraca Saldo: " . mysqli_error($koneksi);
    }
}

// Ambil saldo per akun
$query = mysqli_query($koneksi, "
    SELECT a.id_akun, a.kode_akun, a.nama_akun,
           COALESCE(SUM(b.debit), 0) AS total_debit,
           COALESCE(SUM(b.kredit), 0) AS total_kredit,
           (COALESCE(SUM(b.debit), 0) - COALESCE(SUM(b.kredit), 0)) AS saldo
    FROM akun a
    LEFT JOIN buku_besar b ON a.id_akun = b.id_akun
    GROUP BY a.id_akun, a.nama_akun, a.kode_akun
    ORDER BY a.kode_akun ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Neraca Saldo - PD. EKOLAN</title>
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
    padding: 20px;
}
.table thead {
    background-color: #e3f2fd;
}
.table tbody tr:hover {
    background-color: #f8fbff;
}
.text-debit { color: #198754; font-weight: 500; }
.text-kredit { color: #dc3545; font-weight: 500; }
.empty-state {
    text-align: center;
    color: #777;
    font-style: italic;
    padding: 40px 0;
    font-size: 15px;
}
</style>
</head>

<body>
<?php include 'admin_sidebar.php'; ?>

<!-- Content -->
<div class="content">
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-primary mb-0">
                <i class="bi bi-clipboard-data"></i> Neraca Saldo
            </h4>
            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan Neraca Saldo ini?');">
                <button type="submit" name="simpan_neraca" class="btn btn-success btn-sm">
                    <i class="bi bi-save"></i> Simpan ke Database
                </button>
            </form>
        </div>

        <?php if (!empty($pesan)): ?>
            <div class="alert alert-info py-2"><?= $pesan; ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Akun</th>
                        <th>Nama Akun</th>
                        <th>Debit (Rp)</th>
                        <th>Kredit (Rp)</th>
                        <th>Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $total_debit = 0;
                    $total_kredit = 0;
                    $total_saldo = 0;

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $debit = floatval($row['total_debit']);
                            $kredit = floatval($row['total_kredit']);
                            $saldo = floatval($row['saldo']);

                            $total_debit += $debit;
                            $total_kredit += $kredit;
                            $total_saldo += $saldo;

                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['kode_akun']}</td>
                                    <td class='text-start'>{$row['nama_akun']}</td>
                                    <td class='text-end text-debit'>Rp " . number_format($debit, 0, ',', '.') . "</td>
                                    <td class='text-end text-kredit'>Rp " . number_format($kredit, 0, ',', '.') . "</td>
                                    <td class='text-end fw-semibold " . ($saldo >= 0 ? 'text-success' : 'text-danger') . "'>" . 
                                        ($saldo >= 0 
                                            ? 'Rp ' . number_format($saldo, 0, ',', '.') 
                                            : '(Rp ' . number_format(abs($saldo), 0, ',', '.') . ')') .
                                    "</td>
                                  </tr>";
                            $no++;
                        }

                        echo "<tr class='fw-bold table-info text-end'>
                                <td colspan='3'>Total</td>
                                <td class='text-success'>Rp " . number_format($total_debit, 0, ',', '.') . "</td>
                                <td class='text-danger'>Rp " . number_format($total_kredit, 0, ',', '.') . "</td>
                                <td>Rp " . number_format($total_saldo, 0, ',', '.') . "</td>
                              </tr>";
                    } else {
                        echo "<tr><td colspan='6' class='empty-state'>Belum ada data Neraca Saldo.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
