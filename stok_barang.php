<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// Tambah barang
if (isset($_POST['tambah'])) {
    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori   = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok       = (int) $_POST['stok'];
    $satuan     = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga_beli = (float) $_POST['harga_beli'];
    $harga_jual = (float) $_POST['harga_jual'];

    $sql = "INSERT INTO produk (nama_produk, kategori, stok, satuan, harga_beli, harga_jual, tanggal_update) 
            VALUES ('$nama', '$kategori', '$stok', '$satuan', '$harga_beli', '$harga_jual', NOW())";
    mysqli_query($koneksi, $sql);
    header("Location: stok_barang.php");
    exit;
}

// Hapus barang
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id'");
    header("Location: stok_barang.php");
    exit;
}

// Ambil data stok
$result = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id_produk DESC");
?>
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

        /* Konten utama di kanan sidebar */
        .content {
            margin-left: 260px; /* sesuaikan dengan lebar sidebar */
            padding: 40px 30px;
            min-height: 100vh;
            animation: fadeIn 0.8s ease-in;
        }

        /* Animasi */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Kartu utama */
        .card {
            background-color: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        /* Tombol */
        .btn-success {
            background: #198754;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-success:hover {
            background: #157347;
            transform: translateY(-2px);
        }

        /* Tabel */
        .table {
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
        }
        .table th {
            background: #0d6efd;
            color: white;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
            color: #333;
        }

        /* Modal */
        .modal-content {
            border-radius: 15px;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 992px) {
            .content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<!-- Konten -->
<div class="content">
    <h3 class="text-primary mb-4">
        <i class="bi bi-box-seam me-2"></i> Manajemen Stok Barang
    </h3>

    <div class="card p-4">
        <!-- Tombol Tambah -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Barang
        </button>

        <!-- Tabel Stok -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th width="80">Stok</th>
                        <th>Satuan</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Update Terakhir</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                        <td><?= htmlspecialchars($row['kategori']); ?></td>
                        <td><?= $row['stok']; ?></td>
                        <td><?= htmlspecialchars($row['satuan']); ?></td>
                        <td>Rp <?= number_format($row['harga_beli'],0,',','.'); ?></td>
                        <td>Rp <?= number_format($row['harga_jual'],0,',','.'); ?></td>
                        <td><?= $row['tanggal_update']; ?></td>
                        <td>
                            <a href="edit_stok.php?id=<?= $row['id_produk']; ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="stok_barang.php?hapus=<?= $row['id_produk']; ?>" 
                               onclick="return confirm('Yakin hapus barang ini?')" 
                               class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Barang -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Tambah Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="nama_produk" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Kategori</label>
            <input type="text" name="kategori" class="form-control">
        </div>
        <div class="mb-2">
            <label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="form-control" placeholder="Unit/Pcs/Set">
        </div>
        <div class="mb-2">
            <label class="form-label">Harga Beli</label>
            <input type="number" name="harga_beli" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Harga Jual</label>
            <input type="number" name="harga_jual" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
