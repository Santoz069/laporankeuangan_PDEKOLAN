<?php
include 'koneksi.php';

$term = mysqli_real_escape_string($koneksi, $_GET['term'] ?? '');

$query = mysqli_query($koneksi, "
    SELECT id_produk, nama_produk, harga_jual 
    FROM produk 
    WHERE nama_produk LIKE '%$term%' 
    LIMIT 10
");

$result = [];
while ($row = mysqli_fetch_assoc($query)) {
    $result[] = [
        "label" => $row['nama_produk'] . " (Rp " . number_format($row['harga_jual'], 0, ',', '.') . ")",
        "value" => $row['nama_produk'],
        "id" => $row['id_produk'],
        "harga" => $row['harga_jual'],
    ];
}

echo json_encode($result);
?>
