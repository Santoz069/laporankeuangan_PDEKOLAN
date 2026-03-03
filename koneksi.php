<?php
$host = "localhost";        // biasanya 'localhost'
$user = "root";             // username default XAMPP/MAMP/LAMP
$password = "";             // kosong kalau belum diatur passwordnya
$database = "pd_ekolan";    // ganti sesuai nama database kamu

// Membuat koneksi
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
