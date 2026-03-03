<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['level'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $jumlah = floatval($_POST['jumlah'] ?? 0);
    $pcs = intval($_POST['pcs'] ?? 0);
    $jenis_transaksi = ($_POST['jenis_transaksi'] === 'debet') ? 'debet' : 'kredit';
    $jenis_pengeluaran = !empty($_POST['jenis_pengeluaran']) ? mysqli_real_escape_string($koneksi, $_POST['jenis_pengeluaran']) : NULL;
    $created_by = $_SESSION['level'];

    // === Jika pengeluaran dengan akun baru ===
    if ($jenis_transaksi === 'kredit') {
        if ($_POST['id_akun'] === 'new' && !empty($_POST['nama_akun_baru'])) {
            $namaBaru = mysqli_real_escape_string($koneksi, $_POST['nama_akun_baru']);
            $jenisBaru = $_POST['jenis_akun_baru'];
            $prefix = ['Aktiva'=>'1','Pasiva'=>'2','Modal'=>'3','Pendapatan'=>'4','Beban'=>'5'];
            $kodeAkun = ($prefix[$jenisBaru] ?? '9') . '-' . rand(100,999);
            mysqli_query($koneksi, "INSERT INTO akun (kode_akun, nama_akun, jenis_akun) VALUES ('$kodeAkun', '$namaBaru', '$jenisBaru')");
            $id_akun = mysqli_insert_id($koneksi);
        } else {
            $id_akun = $_POST['id_akun'];
        }
    }

    // === Simpan transaksi utama ===
    $sql = "INSERT INTO transaksi (tanggal, keterangan, pcs, jumlah, jenis_transaksi, jenis_pengeluaran, created_by)
            VALUES ('$tanggal', '$keterangan', $pcs, $jumlah, '$jenis_transaksi', " . 
            ($jenis_pengeluaran ? "'$jenis_pengeluaran'" : "NULL") . ", '$created_by')";
    mysqli_query($koneksi, $sql);
    $id_transaksi = mysqli_insert_id($koneksi);

    // === Kategori otomatis ===
    $kategori = (stripos($keterangan, 'sepeda') !== false) ? 'Sepeda' :
                ((stripos($keterangan, 'lampu') !== false || stripos($keterangan, 'helm') !== false) ? 'Aksesoris' : 'Lainnya');

    // === Update stok (pembelian/pemasukan stok baru)
    if ($jenis_transaksi === 'kredit' && $jenis_pengeluaran === 'pembelian') {
        $cek = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk = '$keterangan'");
        if (mysqli_num_rows($cek) > 0)
            mysqli_query($koneksi, "UPDATE produk SET stok = stok + $pcs, harga_beli = $jumlah WHERE nama_produk = '$keterangan'");
        else
            mysqli_query($koneksi, "INSERT INTO produk (nama_produk, kategori, stok, satuan, harga_beli, harga_jual)
                                    VALUES ('$keterangan', '$kategori', $pcs, 'Pcs', $jumlah, 0)");
    }

    // === Pemasukan (penjualan)
    if ($jenis_transaksi === 'debet' && $pcs > 0) {
        $produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk = '$keterangan'");
        if ($p = mysqli_fetch_assoc($produk)) {
            $id_produk = $p['id_produk'];
            mysqli_query($koneksi, "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah_produk, subtotal)
                                    VALUES ($id_transaksi, $id_produk, $pcs, $jumlah)");
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $pcs WHERE id_produk = $id_produk");
        }
    }

    // === Jurnal Umum Otomatis ===
    $id_akun_kas = 1; // akun Kas
    $id_akun_pendapatan = 7; // akun Pendapatan Penjualan
    if ($jenis_transaksi === 'debet') {
        mysqli_query($koneksi, "INSERT INTO jurnal_umum (tanggal, id_akun, debet, kredit, keterangan)
                                VALUES ('$tanggal', $id_akun_kas, $jumlah, 0, 'Kas masuk: $keterangan (Transaksi $id_transaksi)')");
        mysqli_query($koneksi, "INSERT INTO jurnal_umum (tanggal, id_akun, debet, kredit, keterangan)
                                VALUES ('$tanggal', $id_akun_pendapatan, 0, $jumlah, 'Pendapatan penjualan: $keterangan (Transaksi $id_transaksi)')");
    } else {
        mysqli_query($koneksi, "INSERT INTO jurnal_umum (tanggal, id_akun, debet, kredit, keterangan)
                                VALUES ('$tanggal', $id_akun, $jumlah, 0, 'Pengeluaran untuk $keterangan (Transaksi $id_transaksi)')");
        mysqli_query($koneksi, "INSERT INTO jurnal_umum (tanggal, id_akun, debet, kredit, keterangan)
                                VALUES ('$tanggal', $id_akun_kas, 0, $jumlah, 'Kas keluar untuk $keterangan (Transaksi $id_transaksi)')");
    }

    header("Location: hasil_transaksi.php?success=1");
    exit;
}
?>
