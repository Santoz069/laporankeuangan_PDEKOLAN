<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login dan memiliki level yang diizinkan
if (!isset($_SESSION['username']) || !isset($_SESSION['level']) || !in_array($_SESSION['level'], ['admin', 'kasir'])) {
    header("Location: login.php");
    exit;
}

// Dapatkan nama file halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-bicycle me-1"></i> PD. EKOLAN</h4>
    </div>

    <div class="sidebar-profile">
        <i class="bi bi-person-circle"></i>
        <p><?= htmlspecialchars($_SESSION['nama']); ?></p>
        <small><?= ucfirst($_SESSION['level']); ?></small>
    </div>

    <ul class="nav-menu">
        <li><a href="admin_home.php" class="<?= $current_page == 'admin_home.php' ? 'active' : '' ?>"><i class="bi bi-house-door-fill"></i> Home</a></li>
        <li><a href="upload_transaksi.php" class="<?= $current_page == 'upload_transaksi.php' ? 'active' : '' ?>"><i class="bi bi-upload"></i> Input Transaksi</a></li>
        <li><a href="laporan_admin.php" class="<?= $current_page == 'laporan_admin.php' ? 'active' : '' ?>"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a></li>
        <li><a href="hasil_transaksi.php" class="<?= $current_page == 'hasil_transaksi.php' ? 'active' : '' ?>"><i class="bi bi-list-check"></i> Hasil Transaksi</a></li>
        <li><a href="jurnal_umum.php" class="<?= $current_page == 'jurnal_umum.php' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i> Jurnal Umum</a></li>
        <li><a href="buku_besar.php" class="<?= $current_page == 'buku_besar.php' ? 'active' : '' ?>"><i class="bi bi-book"></i> Buku Besar</a></li>
        <li><a href="neraca_saldo.php" class="<?= $current_page == 'neraca_saldo.php' ? 'active' : '' ?>"><i class="bi bi-clipboard-data"></i> Neraca Saldo</a></li>
        <li><a href="labarugi.php" class="<?= $current_page == 'labarugi.php' ? 'active' : '' ?>"><i class="bi bi-cash-coin"></i> Laporan Laba/Rugi</a></li>
        <li><a href="stok_barang.php" class="<?= $current_page == 'stok_barang.php' ? 'active' : '' ?>"><i class="bi bi-box-seam"></i> Stok Barang</a></li>
        <li><a href="register.php" class="<?= $current_page == 'register.php' ? 'active' : '' ?>"><i class="bi bi-person-plus"></i> Register User</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
    </ul>
</div>

<style>
    .sidebar {
        height: 100vh;
        width: 250px;
        background: #fff;
        border-right: 1px solid #e0e0e0;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        position: fixed;
        top: 0;
        left: 0;
        overflow-y: auto;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid #e9ecef;
    }

    .sidebar-header h4 {
        color: #0d6efd;
        font-weight: 700;
        margin: 0;
    }

    .sidebar-profile {
        text-align: center;
        padding: 20px 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .sidebar-profile i {
        font-size: 45px;
        color: #0d6efd;
    }

    .sidebar-profile p {
        margin: 8px 0 0;
        font-weight: 600;
    }

    .sidebar-profile small {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .nav-menu {
        list-style: none;
        padding: 0;
        margin-top: 10px;
    }

    .nav-menu a {
        display: flex;
        align-items: center;
        padding: 12px 18px;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .nav-menu a i {
        font-size: 18px;
        margin-right: 10px;
    }

    .nav-menu a:hover {
        background: #eaf2ff;
        color: #0d6efd;
        border-left: 4px solid #0d6efd;
    }

    .nav-menu a.active {
        background: #e3f0ff;
        color: #0d6efd;
        border-left: 4px solid #0d6efd;
        font-weight: 600;
    }

    @media (max-width: 992px) {
        .sidebar {
            position: relative;
            height: auto;
            width: 100%;
        }
    }
</style>
