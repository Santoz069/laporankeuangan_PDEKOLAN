<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'kasir') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal     = $_POST['tanggal'];
    $keterangan  = $_POST['keterangan']; // ini nama produk
    $pcs         = intval($_POST['pcs']);
    $jenis_transaksi = "debet"; // selalu pemasukan
    $created_by  = "kasir";

    // Ambil harga_jual produk dari database
    $stmt = mysqli_prepare($koneksi, "SELECT harga_jual FROM produk WHERE nama_produk = ?");
    mysqli_stmt_bind_param($stmt, "s", $keterangan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $harga_jual);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!$harga_jual) {
        echo "<script>alert('Produk tidak ditemukan!');history.back();</script>";
        exit;
    }

    // Hitung jumlah transaksi (harga jual × pcs)
    $jumlah = $harga_jual * $pcs;

    // Simpan ke tabel transaksi
    $query = "INSERT INTO transaksi (tanggal, keterangan, pcs, jumlah, jenis_transaksi, created_by) 
              VALUES ('$tanggal', '$keterangan', '$pcs', '$jumlah', '$jenis_transaksi', '$created_by')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Transaksi berhasil disimpan!');window.location='kasir_transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan transaksi!');history.back();</script>";
    }
}
?>
