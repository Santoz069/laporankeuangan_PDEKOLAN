<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

/* ===============================
   FILTER TANGGAL
================================ */
$tanggalFilter = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$whereTanggal = '';

if (!empty($tanggalFilter)) {
    $whereTanggal = " AND DATE(tanggal) = '$tanggalFilter'";
}

/* ===============================
   TOTAL PEMASUKAN
================================ */
$sqlPemasukan = "
    SELECT SUM(jumlah) AS total 
    FROM transaksi 
    WHERE jenis_transaksi = 'debet' $whereTanggal
";
$resultPemasukan = mysqli_query($koneksi, $sqlPemasukan);
$pemasukan = mysqli_fetch_assoc($resultPemasukan)['total'] ?? 0;

/* ===============================
   TOTAL PENGELUARAN
================================ */
$sqlPengeluaran = "
    SELECT SUM(jumlah) AS total 
    FROM transaksi 
    WHERE jenis_transaksi = 'kredit' $whereTanggal
";
$resultPengeluaran = mysqli_query($koneksi, $sqlPengeluaran);
$pengeluaran = mysqli_fetch_assoc($resultPengeluaran)['total'] ?? 0;

$totalTransaksi = $pemasukan + $pengeluaran;

/* ===============================
   DATA GRAFIK BULANAN
================================ */
$sqlBulanan = "
    SELECT 
        DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
        SUM(CASE WHEN jenis_transaksi = 'debet' THEN jumlah ELSE 0 END) AS total_pemasukan,
        SUM(CASE WHEN jenis_transaksi = 'kredit' THEN jumlah ELSE 0 END) AS total_pengeluaran
    FROM transaksi
    WHERE 1=1 $whereTanggal
    GROUP BY bulan
    ORDER BY bulan ASC
";
$resultBulanan = mysqli_query($koneksi, $sqlBulanan);

$labels = [];
$pemasukanData = [];
$pengeluaranData = [];

while ($row = mysqli_fetch_assoc($resultBulanan)) {
    $labels[] = $row['bulan'];
    $pemasukanData[] = $row['total_pemasukan'];
    $pengeluaranData[] = $row['total_pengeluaran'];
}
?>


    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard - PD. EKOLAN</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            body {
                background-color: #f1f6f9;
                font-family: 'Segoe UI', sans-serif;
            }
            .content {
                margin-left: 260px;
                padding: 30px;
            }
            .card {
                background-color: #ffffff;
                border: none;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                margin-bottom: 20px;
                transition: transform 0.2s ease;
            }
            .card:hover {
                transform: translateY(-5px);
            }
            .alert {
                animation: fadeIn 1s ease-in-out;
            }
            .time-card-container {
        display: flex;
        justify-content: flex-end;
            }

            .glass-time-card {
                background: rgb(34, 155, 255);
                backdrop-filter: blur(12px);
                border-radius: 20px;
                border: 1px solid rgb(255, 255, 255);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
                min-width: 300px;
                transition: all 0.3s ease;
            }

            .glass-time-card:hover {
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
                transform: scale(1.01);
            }

            .small-text {
                font-size: 0.9rem;
            }

            @keyframes slideIn {
                from { transform: translateX(-100%); }
                to { transform: translateX(0); }
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            #chartTransaksi {
                max-width: 500px;
                max-height: 500px;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>

    <?php include 'admin_sidebar.php'; ?>



    <!-- Main Content -->
    <div class="content">
    <div class="alert alert-success mb-4 fade-in">
            Login berhasil, Selamat datang di sistem PD. EKOLAN 👋
        </div>

        <div class="time-card-container mb-4">
        <div class="glass-time-card d-flex align-items-center justify-content-between px-4 py-3">
            <div>
                <div id="tanggal" class="fw-semibold text-white small-text"></div>
                <div id="jam" class="fw-bold text-white display-6 m-0"></div>
            </div>
            <i class="bi bi-clock-history text-white fs-1 ms-3"></i>
        </div>
    </div>

        <div class="row mb-4">
            <div class="col-md-7">
                <div class="card p-4 mb-4">
                    <h4 class="text-primary mb-2">Halo, <?= $_SESSION['nama']; ?>!</h4>
                    <p>Anda login sebagai <strong>Admin</strong>.</p>
                    <p class="text-muted">Silakan gunakan menu di sebelah kiri untuk mengelola transaksi dan laporan keuangan.</p>
                </div>
            </div>
        </div>
 <!-- FILTER TANGGAL -->
    <div class="card p-3 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       value="<?= $tanggalFilter ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Terapkan
                </button>
            </div>
        </form>
    </div>
        <!-- Statistik Transaksi -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white p-4">
                    <h6>Total Transaksi</h6>
                    <h4>Rp <?= number_format($totalTransaksi, 0, ',', '.'); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white p-4">
                    <h6>Pemasukan</h6>
                    <h4>Rp <?= number_format($pemasukan, 0, ',', '.'); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white p-4">
                    <h6>Pengeluaran</h6>
                    <h4>Rp <?= number_format($pengeluaran, 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>

        <!-- Grafik Transaksi -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card p-4">
                    <h5 class="mb-3 text-primary">Grafik Pemasukan dan Pengeluaran</h5>
                    <canvas id="chartTransaksi" width="400" height="400"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Tren Bulanan -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card p-4">
                    <h5 class="mb-3 text-primary">Tren Transaksi Bulanan</h5>
                    <canvas id="chartBulanan" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Realtime Tanggal dan Jam -->
    <script>
        function updateTanggalJam() {
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const sekarang = new Date();
            const namaHari = hari[sekarang.getDay()];
            const tanggal = sekarang.getDate();
            const namaBulan = bulan[sekarang.getMonth()];
            const tahun = sekarang.getFullYear();
            const jam = sekarang.getHours().toString().padStart(2, '0');
            const menit = sekarang.getMinutes().toString().padStart(2, '0');
            const detik = sekarang.getSeconds().toString().padStart(2, '0');

            document.getElementById("tanggal").textContent = `${namaHari}, ${tanggal} ${namaBulan} ${tahun}`;
            document.getElementById("jam").textContent = `${jam}:${menit}:${detik}`;
        }

        setInterval(updateTanggalJam, 1000);
        updateTanggalJam();
    </script>

    <!-- Grafik Donat dan Line -->
    <script>
        const ctxTransaksi = document.getElementById('chartTransaksi').getContext('2d');
        new Chart(ctxTransaksi, {
            type: 'doughnut',
            data: {
                labels: ['Pemasukan', 'Pengeluaran'],
                datasets: [{
                    data: [<?= $pemasukan ?>, <?= $pengeluaran ?>],
                    backgroundColor: ['#198754', '#dc3545'],
                    borderColor: ['#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed;
                                return `${context.label}: Rp ${value.toLocaleString('id-ID')}`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        const ctxBulanan = document.getElementById('chartBulanan').getContext('2d');
        new Chart(ctxBulanan, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels); ?>,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: <?= json_encode($pemasukanData); ?>,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Pengeluaran',
                        data: <?= json_encode($pengeluaranData); ?>,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
