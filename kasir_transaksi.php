<?php
session_start();
include 'koneksi.php';

// 🔒 Cek login & role kasir
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'kasir') {
    header("Location: login.php");
    exit;
}

// 🧾 Inisialisasi draft
if (!isset($_SESSION['draft'])) $_SESSION['draft'] = [];

// ➕ Tambah ke draft (dengan pengecekan barang duplikat)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $id = (int)$_POST['id_produk'];
    $pcs_baru = max(1, (int)$_POST['pcs']);

    $produk_q = $koneksi->query("SELECT id_produk, nama_produk, stok, harga_jual, satuan FROM produk WHERE id_produk='$id'");
    if ($produk_q && $produk_q->num_rows > 0) {
        $produk = $produk_q->fetch_assoc();

        // 🔍 Cek apakah produk sudah ada di draft
        $found = false;
        foreach ($_SESSION['draft'] as $index => $item) {
            if ($item['id'] == $produk['id_produk']) {
                $pcs_total = $item['pcs'] + $pcs_baru;
                if ($pcs_total > $produk['stok']) {
                    echo "<script>alert('Stok {$produk['nama_produk']} tidak mencukupi! Stok tersedia: {$produk['stok']}');window.location='kasir_transaksi.php';</script>";
                    exit;
                }

                // ✅ Tambahkan jumlah pcs
                $_SESSION['draft'][$index]['pcs'] = $pcs_total;
                $found = true;
                break;
            }
        }

        // 🔹 Jika belum ada di draft → tambahkan item baru
        if (!$found) {
            if ($produk['stok'] >= $pcs_baru) {
                $_SESSION['draft'][] = [
                    'id' => $produk['id_produk'],
                    'nama' => $produk['nama_produk'],
                    'satuan' => $produk['satuan'] ?: 'pcs',
                    'pcs' => $pcs_baru,
                    'harga' => (float)$produk['harga_jual']
                ];
            } else {
                echo "<script>alert('Stok untuk {$produk['nama_produk']} tidak mencukupi! Stok tersedia: {$produk['stok']}');window.location='kasir_transaksi.php';</script>";
                exit;
            }
        }
    }
    header("Location: kasir_transaksi.php");
    exit;
}

// ❌ Hapus item draft
if (isset($_GET['hapus'])) {
    $i = (int)$_GET['hapus'];
    if (isset($_SESSION['draft'][$i])) {
        unset($_SESSION['draft'][$i]);
        $_SESSION['draft'] = array_values($_SESSION['draft']);
    }
    header("Location: kasir_transaksi.php");
    exit;
}

// 💰 Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bayar' && !empty($_SESSION['draft'])) {
    $total = 0;
    $total_pcs = 0;
    $daftar_barang = [];

    foreach ($_SESSION['draft'] as $item) {
        $subtotal = $item['pcs'] * $item['harga'];
        $total += $subtotal;
        $total_pcs += $item['pcs'];
        $daftar_barang[] = "{$item['nama']} x{$item['pcs']} {$item['satuan']}";
    }

    $tanggal = date("Y-m-d");
    $keterangan = implode(", ", $daftar_barang);
    $jenis_transaksi = 'debet';
    $created_by = $_SESSION['level']; // ✅ ENUM('admin','kasir')
    $id_user = $_SESSION['id_user'] ?? null;

    if (!$id_user) {
        echo "<script>alert('Session Anda habis, silakan login ulang.');window.location='login.php';</script>";
        exit;
    }

    // 🧩 Insert transaksi utama
    $stmt = $koneksi->prepare("
        INSERT INTO transaksi (tanggal, keterangan, pcs, jumlah, jenis_transaksi, created_by, id_user)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssdssi", $tanggal, $keterangan, $total_pcs, $total, $jenis_transaksi, $created_by, $id_user);
    $sukses = $stmt->execute();

    if (!$sukses) {
        echo "<script>alert('Gagal menyimpan transaksi: " . addslashes($stmt->error) . "');window.location='kasir_transaksi.php';</script>";
        exit;
    }

    $id_transaksi = $stmt->insert_id;
    $stmt->close();

    // 💾 Simpan detail transaksi & update stok
    foreach ($_SESSION['draft'] as $item) {
        $idp = (int)$item['id'];
        $pcs_item = (int)$item['pcs'];
        $harga = (float)$item['harga'];
        $subtotal = $pcs_item * $harga;

        $koneksi->query("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah_produk, subtotal)
                         VALUES ('$id_transaksi', '$idp', '$pcs_item', '$subtotal')");
        $koneksi->query("UPDATE produk SET stok = stok - $pcs_item WHERE id_produk='$idp'");
    }

    // 📘 Jurnal Umum
    $id_akun_kas = 1;
    $id_akun_pendapatan = 7;

    $koneksi->query("INSERT INTO jurnal_umum (id_transaksi, tanggal, id_akun, debet, kredit, keterangan)
                     VALUES ('$id_transaksi', '$tanggal', '$id_akun_kas', '$total', 0, 'Penjualan #$id_transaksi - $keterangan')");
    $koneksi->query("INSERT INTO jurnal_umum (id_transaksi, tanggal, id_akun, debet, kredit, keterangan)
                     VALUES ('$id_transaksi', '$tanggal', '$id_akun_pendapatan', 0, '$total', 'Pendapatan Penjualan #$id_transaksi - $keterangan')");

    $_SESSION['nota'] = $id_transaksi;
    $_SESSION['draft'] = [];

    header("Location: nota.php?from=transaksi&id_transaksi=$id_transaksi");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Transaksi - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .content {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            border-radius: 14px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            border: none;
        }
        h3 {
            font-weight: 600;
            color: #212529;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead {
            background-color: #e9ecef;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?php include 'kasir_sidebar.php'; ?>

    <div class="content">
        <h3 class="mb-4"><i class="bi bi-cash-stack me-2"></i>Transaksi Kasir</h3>

        <!-- 🔹 Form Input Barang -->
        <div class="card p-4 mb-4">
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Barang</label>
                        <input type="hidden" name="id_produk" id="idProduk">
                        <input type="text" id="inputProduk" class="form-control" placeholder="Ketik nama barang..." required autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <input type="number" name="pcs" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-1"></i> Tambah
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- 🔹 Draft Keranjang -->
        <div class="card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-cart me-2"></i>Keranjang</h5>

            <?php if (!empty($_SESSION['draft'])): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="bayar">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            foreach ($_SESSION['draft'] as $i => $item):
                                $sub = $item['pcs'] * $item['harga'];
                                $total += $sub;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nama']) ?></td>
                                <td><?= $item['pcs'] . ' ' . htmlspecialchars($item['satuan']) ?></td>
                                <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($sub, 0, ',', '.') ?></td>
                                <td>
                                    <a href="?hapus=<?= $i ?>" class="btn btn-sm btn-danger">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="3">TOTAL</th>
                                <th colspan="2">Rp <?= number_format($total, 0, ',', '.') ?></th>
                            </tr>
                        </tfoot>
                    </table>

                    <button type="submit" class="btn btn-success w-100 mt-2">
                        <i class="bi bi-check-circle me-1"></i> Bayar & Cetak Nota
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-info mb-0">Belum ada barang dalam keranjang.</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // 🔍 Autocomplete produk
        $("#inputProduk").autocomplete({
            source: "search_barang.php",
            minLength: 1,
            select: function(event, ui) {
                $("#inputProduk").val(ui.item.label);
                $("#idProduk").val(ui.item.id);
                return false;
            }
        });
    </script>
</body>
</html>
