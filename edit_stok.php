<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Ambil data produk berdasarkan id
if (!isset($_GET['id'])) {
    header("Location: stok_barang.php");
    exit;
}

$id = (int) $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = $id");
if (mysqli_num_rows($query) == 0) {
    header("Location: stok_barang.php");
    exit;
}
$data = mysqli_fetch_assoc($query);

// Proses update
if (isset($_POST['update'])) {
    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori   = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok       = (int) $_POST['stok'];
    $satuan     = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga_beli = (float) $_POST['harga_beli'];
    $harga_jual = (float) $_POST['harga_jual'];

    $sql = "UPDATE produk SET 
                nama_produk='$nama',
                kategori='$kategori',
                stok=$stok,
                satuan='$satuan',
                harga_beli=$harga_beli,
                harga_jual=$harga_jual,
                tanggal_update=NOW()
            WHERE id_produk=$id";

    if (mysqli_query($koneksi, $sql)) {
        header("Location: stok_barang.php?success=1");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Stok Barang - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f1f6f9; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 700px; margin-top: 50px; }
        .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="card p-4">
        <h4 class="mb-4 text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Stok Barang</h4>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($data['nama_produk']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($data['kategori']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" value="<?= $data['stok']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Satuan</label>
                <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($data['satuan']); ?>" placeholder="Unit/Pcs/Set">
            </div>
            <div class="mb-3">
                <label class="form-label">Harga Beli</label>
                <input type="number" name="harga_beli" class="form-control" value="<?= $data['harga_beli']; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" value="<?= $data['harga_jual']; ?>" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="stok_barang.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
