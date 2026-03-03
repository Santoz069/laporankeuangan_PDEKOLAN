<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || !in_array($_SESSION['level'], ['admin', 'kasir'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];
    $tipe = $_POST['tipe'];
    $modified_by = $_SESSION['username']; // Ganti ke username agar lebih spesifik

    if (!$id || empty($tanggal) || empty($keterangan) || empty($jumlah) || empty($tipe)) {
        echo "Semua kolom wajib diisi!";
        exit;
    }

    // Update data transaksi
    $stmt = $koneksi->prepare("UPDATE transaksi SET tanggal = ?, keterangan = ?, jumlah = ?, tipe = ?, modified_by = ? WHERE id_transaksi = ?");
    $stmt->bind_param("ssissi", $tanggal, $keterangan, $jumlah, $tipe, $modified_by, $id);

    if ($stmt->execute()) {
        // Redirect berdasarkan level user
        if ($_SESSION['level'] === 'admin') {
            header("Location: hasil_transaksi.php?status=updated");
        } else {
            header("Location: kasir_histori.php?status=updated");
        }
        exit;
    } else {
        echo "Gagal memperbarui transaksi: " . $stmt->error;
    }
} else {
    echo "Permintaan tidak valid.";
}
