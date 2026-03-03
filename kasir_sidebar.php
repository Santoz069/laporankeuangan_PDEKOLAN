<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isActive($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
?>

<div class="sidebar">
    <h4 class="text-center text-primary fw-bold mb-4">PD. EKOLAN</h4>

    <div class="text-center mb-4">
        <i class="bi bi-person-circle fs-1 text-primary"></i>
        <p class="m-0 fw-semibold mt-2"><?= $_SESSION['nama']; ?></p>
        <small class="text-muted">Kasir</small>
    </div>

    <a href="kasir_home.php" class="<?= isActive('kasir_home.php'); ?>">
        <i class="bi bi-house-door-fill me-2"></i> Home
    </a>
    <a href="kasir_transaksi.php" class="<?= isActive('kasir_transaksi.php'); ?>">
        <i class="bi bi-upload me-2"></i> Input Transaksi
    </a>
    <a href="kasir_histori.php" class="<?= isActive('kasir_histori.php'); ?>">
        <i class="bi bi-clock-history me-2"></i> Histori Transaksi
    </a>
    <a href="kasir_stok.php" class="<?= isActive('kasir_stok.php'); ?>">
        <i class="bi bi-box-seam me-2"></i> Stok Barang
    </a>
    <a href="kasir_laporan.php" class="<?= isActive('kasir_laporan.php'); ?>">
        <i class="bi bi-bar-chart-fill me-2"></i> Laporan Penjualan
    </a>
    <a href="logout.php">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
</div>

<style>
/* ===== SIDEBAR STYLE ===== */
.sidebar {
    width: 260px;
    height: 100vh;
    background: #fff;
    border-right: 1px solid #ddd;
    position: fixed;
    padding: 20px 0;
    box-shadow: 2px 0 8px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.sidebar h4 {
    letter-spacing: 0.5px;
}

.sidebar a {
    display: block;
    padding: 14px 22px;
    margin: 2px 10px;
    color: #333;
    text-decoration: none;
    border-left: 4px solid transparent;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sidebar a i {
    font-size: 18px;
    vertical-align: middle;
}

.sidebar a:hover, .sidebar a.active {
    background: #e3f2fd;
    border-left: 4px solid #0d6efd;
    color: #0d6efd;
}

.sidebar .text-center i {
    transition: transform 0.3s ease;
}

.sidebar .text-center:hover i {
    transform: scale(1.1);
}

.content {
    margin-left: 260px;
    padding: 30px;
}
</style>
