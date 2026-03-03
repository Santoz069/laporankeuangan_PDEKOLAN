<?php
session_start();
include 'koneksi.php';

// Pastikan kasir login
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'kasir') {
    header("Location: login.php");
    exit;
}

// Pastikan ada parameter ID
if (!isset($_GET['id'])) {
    header("Location: kasir_histori.php");
    exit;
}

$id_transaksi = intval($_GET['id']);
$nama_kasir = $_SESSION['username'];

// Ambil data transaksi utama
$q_transaksi = $koneksi->query("SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'");
if ($q_transaksi->num_rows === 0) {
    header("Location: kasir_histori.php");
    exit;
}
$transaksi = $q_transaksi->fetch_assoc();

// Ambil detail transaksi
$q_detail = $koneksi->query("
    SELECT d.*, p.nama_produk 
    FROM detail_transaksi d
    JOIN produk p ON d.id_produk = p.id_produk
    WHERE d.id_transaksi = '$id_transaksi'
");

$keterangan_items = [];
while ($d = $q_detail->fetch_assoc()) {
    $keterangan_items[] = "{$d['nama_produk']} x{$d['jumlah_produk']}";
}
$keterangan_text = implode(", ", $keterangan_items);

// Simpan ke histori hapus
$stmt = $koneksi->prepare("
    INSERT INTO histori_hapus (id_transaksi, nama_kasir, total_nominal, keterangan)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("isds", $id_transaksi, $nama_kasir, $transaksi['jumlah'], $keterangan_text);
$stmt->execute();

// Pastikan hapus detail dulu baru transaksi utama
$hapus_detail = $koneksi->query("DELETE FROM detail_transaksi WHERE id_transaksi = '$id_transaksi'");
$hapus_transaksi = $koneksi->query("DELETE FROM transaksi WHERE id_transaksi = '$id_transaksi'");

// Debug (optional: tampilkan error jika ada)
if (!$hapus_transaksi) {
    die("Gagal hapus transaksi utama: " . $koneksi->error);
}

// Redirect kembali ke halaman histori kasir
header("Location: kasir_histori.php?hapus=success");
exit;
?>
