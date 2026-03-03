<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'kasir') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Ambil tanggal dan waktu saat ini
$tanggal = date("l, d F Y");
$jam = date("H:i:s");

// Hitung jumlah transaksi hari ini oleh kasir
$tanggalHariIni = date('Y-m-d');
$sql = "SELECT COUNT(*) AS total FROM transaksi WHERE DATE(tanggal) = '$tanggalHariIni' AND created_by = 'kasir'";
$totalTransaksi = mysqli_fetch_assoc(mysqli_query($koneksi, $sql))['total'] ?? 0;

// Quote motivasi acak
$quotes = [
    "Pelayanan yang tulus adalah bentuk terbaik dari profesionalitas.",
    "Setiap senyuman pelanggan adalah hasil dari kerja kerasmu hari ini.",
    "Jadilah kasir yang bukan hanya menghitung uang, tapi juga menambah nilai.",
    "Ketelitian adalah kunci. Kejujuran adalah fondasi.",
    "Hari yang sibuk adalah tanda bahwa bisnismu dipercaya banyak orang.",
    "Kerja dengan hati, hasilkan pelayanan yang berarti.",
];
$quote = $quotes[array_rand($quotes)];
?>

<?php include 'kasir_sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda Kasir - PD. EKOLAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <style>
        body {
            background-color: #f1f6f9;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }
        .content {
            margin-left: 260px;
            padding: 40px;
        }
        .welcome-card {
            background: linear-gradient(135deg, #0d6efd, #3a7bd5);
            color: white;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .welcome-text h2 {
            font-weight: 600;
        }
        .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
        }
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .shortcut-card {
            cursor: pointer;
        }
        .shortcut-card:hover {
            background-color: #f8f9fa;
        }
        .quote-box {
            background: #fff;
            border-left: 6px solid #0d6efd;
            padding: 20px;
            border-radius: 10px;
            font-style: italic;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="content">
    <!-- Welcome Section -->
    <div class="welcome-card mb-4" data-aos="fade-down" data-aos-duration="800">
        <div class="welcome-text">
            <h2>Selamat Datang, <?= $_SESSION['nama']; ?> 👋</h2>
            <p class="mb-1"><?= $tanggal; ?> | <span id="jam"><?= $jam; ?></span></p>
            <small>Semoga hari ini penuh semangat dan pelayanan terbaik untuk pelanggan.</small>
        </div>
        <lottie-player 
            src="https://assets7.lottiefiles.com/packages/lf20_vnikrcia.json"
            background="transparent" speed="1"
            style="width: 200px; height: 200px;" loop autoplay>
        </lottie-player>
    </div>

    <!-- Status Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card text-center p-4">
                <i class="bi bi-calendar-check fs-1 text-primary mb-2"></i>
                <h6>Transaksi Hari Ini</h6>
                <h4 class="fw-bold"><?= $totalTransaksi; ?></h4>
                <small class="text-muted">Jumlah transaksi yang berhasil dicatat hari ini</small>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card text-center p-4">
                <i class="bi bi-clock-history fs-1 text-success mb-2"></i>
                <h6>Status Sistem</h6>
                <h5 class="fw-bold text-success">Aktif</h5>
                <small class="text-muted">Sistem kasir berjalan normal</small>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card text-center p-4">
                <i class="bi bi-emoji-smile fs-1 text-warning mb-2"></i>
                <h6>Pelayanan Hari Ini</h6>
                <h5 class="fw-bold text-warning">Responsif</h5>
                <small class="text-muted">Terima kasih atas dedikasi dan keramahanmu hari ini</small>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12" data-aos="zoom-in" data-aos-delay="400">
            <div onclick="window.location='kasir_stok.php'" class="card shortcut-card p-4 text-center">
                <i class="bi bi-box-seam fs-1 text-primary mb-2"></i>
                <h5>Lihat Stok Barang</h5>
                <small>Pantau ketersediaan produk di gudang toko</small>
            </div>
        </div>
    </div>

    <!-- Motivasi -->
    <div class="quote-box" data-aos="fade-in" data-aos-delay="500">
        <i class="bi bi-chat-quote text-primary"></i>
        <span class="ms-2"><?= $quote; ?></span>
    </div>
</div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true });

// Update jam realtime
function updateJam() {
    const now = new Date();
    const jam = now.toLocaleTimeString('id-ID', { hour12: false });
    document.getElementById('jam').textContent = jam;
}
setInterval(updateJam, 1000);
</script>
</body>
</html>
