<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// 🔹 Kosongkan tabel buku besar untuk menghindari duplikasi
mysqli_query($koneksi, "TRUNCATE TABLE buku_besar");

// 🔹 Ambil semua data dari jurnal umum
$jurnal = mysqli_query($koneksi, "
    SELECT j.tanggal, j.id_akun, a.nama_akun, j.debet AS debit, j.kredit, j.keterangan
    FROM jurnal_umum j
    JOIN akun a ON j.id_akun = a.id_akun
    ORDER BY j.id_akun ASC, j.tanggal ASC, j.id_jurnal ASC
");

$current_akun = null;
$saldo = 0;

while ($row = mysqli_fetch_assoc($jurnal)) {
    $id_akun    = $row['id_akun'];
    $tanggal    = $row['tanggal'];
    $akun       = mysqli_real_escape_string($koneksi, $row['nama_akun']);
    $debit      = floatval($row['debit']);
    $kredit     = floatval($row['kredit']);
    $keterangan = mysqli_real_escape_string($koneksi, $row['keterangan']);

    // 🔹 Reset saldo ketika akun berubah
    if ($current_akun !== $id_akun) {
        $saldo = 0;
        $current_akun = $id_akun;
    }

    // 🔹 Hitung saldo berjalan
    $saldo = $saldo + $debit - $kredit;

    // 🔹 Simpan ke tabel buku_besar
    mysqli_query($koneksi, "
        INSERT INTO buku_besar (tanggal, id_akun, akun, keterangan, debit, kredit, saldo)
        VALUES ('$tanggal', '$id_akun', '$akun', '$keterangan', '$debit', '$kredit', '$saldo')
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Memposting ke Buku Besar...</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style>
body {
    background: linear-gradient(135deg, #f8fbff, #eef3f9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    font-family: 'Segoe UI', sans-serif;
    text-align: center;
}
h4 {
    color: #0d6efd;
    margin-top: 20px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
p {
    color: #6c757d;
}
.spinner-border {
    margin-top: 10px;
}
</style>
</head>
<body>

<!-- ✅ Animasi Lottie Baru (dijamin tampil) -->
<lottie-player 
    src="https://assets9.lottiefiles.com/packages/lf20_qp1q7mct.json" 
    background="transparent" 
    speed="1" 
    style="width:280px;height:280px;" 
    loop 
    autoplay>
</lottie-player>

<h4>Memposting data ke Buku Besar...</h4>
<p class="text-muted">Sistem sedang memproses data jurnal umum Anda</p>
<div class="spinner-border text-primary" role="status"></div>

<script>
setTimeout(() => {
    window.location.href = "buku_besar.php?posted=1";
}, 3000);
</script>

</body>
</html>

