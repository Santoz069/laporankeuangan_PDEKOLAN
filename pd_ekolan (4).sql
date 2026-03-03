-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 11:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pd_ekolan`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `id_akun` int(11) NOT NULL,
  `kode_akun` varchar(10) NOT NULL,
  `nama_akun` varchar(100) NOT NULL,
  `jenis_akun` enum('Aktiva','Pasiva','Modal','Pendapatan','Beban') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`id_akun`, `kode_akun`, `nama_akun`, `jenis_akun`) VALUES
(1, '1-100', 'Kas', 'Aktiva'),
(2, '1-101', 'Bank', 'Aktiva'),
(3, '1-200', 'Piutang Dagang', 'Aktiva'),
(4, '1-300', 'Perlengkapan Toko', 'Aktiva'),
(5, '2-100', 'Utang Dagang', 'Pasiva'),
(6, '3-100', 'Modal Pemilik', 'Modal'),
(7, '4-100', 'Pendapatan Penjualan', 'Pendapatan'),
(8, '5-100', 'Beban Gaji', 'Beban'),
(9, '5-101', 'Beban Listrik', 'Beban'),
(10, '5-102', 'Beban Sewa', 'Beban');

-- --------------------------------------------------------

--
-- Table structure for table `buku_besar`
--

CREATE TABLE `buku_besar` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `id_akun` int(11) DEFAULT NULL,
  `akun` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `kredit` decimal(15,2) DEFAULT 0.00,
  `saldo` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku_besar`
--

INSERT INTO `buku_besar` (`id`, `tanggal`, `id_akun`, `akun`, `keterangan`, `debit`, `kredit`, `saldo`) VALUES
(1, '2025-11-10', 1, 'Kas', 'Kas masuk: Sepeda Ontel CV (Transaksi 56)', 15000000.00, 0.00, 15000000.00),
(2, '2025-12-10', 1, 'Kas', 'Kas masuk: Sepeda Lipat Family (Transaksi 53)', 2500000.00, 0.00, 17500000.00),
(3, '2025-12-14', 1, 'Kas', 'Kas masuk: Sepeda Balap Trex X (Transaksi 54)', 27000000.00, 0.00, 44500000.00),
(4, '2025-12-20', 1, 'Kas', 'Kas masuk: Sepeda Balap Trex X (Transaksi 55)', 27000000.00, 0.00, 71500000.00),
(5, '2026-01-08', 1, 'Kas', 'Kas masuk: Sepeda Ontel CV (Transaksi 52)', 15000000.00, 0.00, 86500000.00),
(6, '2026-01-08', 1, 'Kas', 'Kas keluar untuk Bayar Listrik Bulan Desember 2025 (Transaksi 58)', 0.00, 3500000.00, 83000000.00),
(7, '2026-01-10', 1, 'Kas', 'Kas keluar untuk Gaji Karyawan (Transaksi 50)', 0.00, 5000000.00, 78000000.00),
(8, '2026-01-14', 1, 'Kas', 'Pengeluaran untuk Sepeda Zeuz 2344 (Transaksi 51)', 45000000.00, 0.00, 123000000.00),
(9, '2026-01-14', 1, 'Kas', 'Kas keluar untuk Sepeda Zeuz 2344 (Transaksi 51)', 0.00, 45000000.00, 78000000.00),
(10, '2026-01-17', 1, 'Kas', 'Penjualan #49 - Helm Sepeda Polygon x1 Pcs, Sepeda Lipat Family x1 Unit, Sepeda Balap Trex X x1 Unit', 29700000.00, 0.00, 107700000.00),
(11, '2025-11-10', 7, 'Pendapatan Penjualan', 'Pendapatan penjualan: Sepeda Ontel CV (Transaksi 56)', 0.00, 15000000.00, -15000000.00),
(12, '2025-12-10', 7, 'Pendapatan Penjualan', 'Pendapatan penjualan: Sepeda Lipat Family (Transaksi 53)', 0.00, 2500000.00, -17500000.00),
(13, '2025-12-14', 7, 'Pendapatan Penjualan', 'Pendapatan penjualan: Sepeda Balap Trex X (Transaksi 54)', 0.00, 27000000.00, -44500000.00),
(14, '2025-12-20', 7, 'Pendapatan Penjualan', 'Pendapatan penjualan: Sepeda Balap Trex X (Transaksi 55)', 0.00, 27000000.00, -71500000.00),
(15, '2026-01-08', 7, 'Pendapatan Penjualan', 'Pendapatan penjualan: Sepeda Ontel CV (Transaksi 52)', 0.00, 15000000.00, -86500000.00),
(16, '2026-01-17', 7, 'Pendapatan Penjualan', 'Pendapatan Penjualan #49 - Helm Sepeda Polygon x1 Pcs, Sepeda Lipat Family x1 Unit, Sepeda Balap Trex X x1 Unit', 0.00, 29700000.00, -116200000.00),
(17, '2026-01-10', 8, 'Beban Gaji', 'Pengeluaran untuk Gaji Karyawan (Transaksi 50)', 5000000.00, 0.00, 5000000.00),
(18, '2026-01-08', 9, 'Beban Listrik', 'Pengeluaran untuk Bayar Listrik Bulan Desember 2025 (Transaksi 58)', 3500000.00, 0.00, 3500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah_produk` int(11) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah_produk`, `subtotal`) VALUES
(62, 48, 1, 1, 3500000.00),
(63, 48, 11, 1, 100000.00),
(64, 48, 3, 1, 200000.00),
(65, 49, 3, 1, 200000.00),
(66, 49, 2, 1, 2500000.00),
(67, 49, 5, 1, 27000000.00),
(68, 52, 10, 1, 15000000.00),
(71, 55, 5, 1, 27000000.00),
(72, 56, 10, 1, 15000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `histori_hapus`
--

CREATE TABLE `histori_hapus` (
  `id_histori` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `nama_kasir` varchar(100) DEFAULT NULL,
  `tanggal_hapus` datetime DEFAULT current_timestamp(),
  `total_nominal` decimal(15,2) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `histori_hapus`
--

INSERT INTO `histori_hapus` (`id_histori`, `id_transaksi`, `nama_kasir`, `tanggal_hapus`, `total_nominal`, `keterangan`) VALUES
(50, 57, 'fano', '2026-01-20 10:39:23', 3700000.00, 'Sepeda Gunung TREX X1 x1, Helm Sepeda Polygon x1'),
(51, 59, 'fano', '2026-01-29 12:29:18', 15000000.00, 'Sepeda Ontel CV x1'),
(52, 54, 'fano', '2026-01-29 12:29:25', 27000000.00, 'Sepeda Balap Trex X x1'),
(53, 53, 'fano', '2026-01-29 12:29:27', 2500000.00, 'Sepeda Lipat Family x1');

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_umum`
--

CREATE TABLE `jurnal_umum` (
  `id_jurnal` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `periode` varchar(7) DEFAULT NULL,
  `id_akun` int(11) NOT NULL,
  `debet` decimal(15,2) DEFAULT 0.00,
  `kredit` decimal(15,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurnal_umum`
--

INSERT INTO `jurnal_umum` (`id_jurnal`, `id_transaksi`, `tanggal`, `periode`, `id_akun`, `debet`, `kredit`, `keterangan`) VALUES
(69, 49, '2026-01-17', '2026-01', 1, 29700000.00, 0.00, 'Penjualan #49 - Helm Sepeda Polygon x1 Pcs, Sepeda Lipat Family x1 Unit, Sepeda Balap Trex X x1 Unit'),
(70, 49, '2026-01-17', '2026-01', 7, 0.00, 29700000.00, 'Pendapatan Penjualan #49 - Helm Sepeda Polygon x1 Pcs, Sepeda Lipat Family x1 Unit, Sepeda Balap Trex X x1 Unit'),
(71, NULL, '2026-01-10', '2026-01', 8, 5000000.00, 0.00, 'Pengeluaran untuk Gaji Karyawan (Transaksi 50)'),
(72, NULL, '2026-01-10', '2026-01', 1, 0.00, 5000000.00, 'Kas keluar untuk Gaji Karyawan (Transaksi 50)'),
(73, NULL, '2026-01-14', '2026-01', 1, 45000000.00, 0.00, 'Pengeluaran untuk Sepeda Zeuz 2344 (Transaksi 51)'),
(74, NULL, '2026-01-14', '2026-01', 1, 0.00, 45000000.00, 'Kas keluar untuk Sepeda Zeuz 2344 (Transaksi 51)'),
(75, NULL, '2026-01-08', '2026-01', 1, 15000000.00, 0.00, 'Kas masuk: Sepeda Ontel CV (Transaksi 52)'),
(76, NULL, '2026-01-08', '2026-01', 7, 0.00, 15000000.00, 'Pendapatan penjualan: Sepeda Ontel CV (Transaksi 52)'),
(77, NULL, '2025-12-10', '2025-12', 1, 2500000.00, 0.00, 'Kas masuk: Sepeda Lipat Family (Transaksi 53)'),
(78, NULL, '2025-12-10', '2025-12', 7, 0.00, 2500000.00, 'Pendapatan penjualan: Sepeda Lipat Family (Transaksi 53)'),
(79, NULL, '2025-12-14', '2025-12', 1, 27000000.00, 0.00, 'Kas masuk: Sepeda Balap Trex X (Transaksi 54)'),
(80, NULL, '2025-12-14', '2025-12', 7, 0.00, 27000000.00, 'Pendapatan penjualan: Sepeda Balap Trex X (Transaksi 54)'),
(81, NULL, '2025-12-20', '2025-12', 1, 27000000.00, 0.00, 'Kas masuk: Sepeda Balap Trex X (Transaksi 55)'),
(82, NULL, '2025-12-20', '2025-12', 7, 0.00, 27000000.00, 'Pendapatan penjualan: Sepeda Balap Trex X (Transaksi 55)'),
(83, NULL, '2025-11-10', '2025-11', 1, 15000000.00, 0.00, 'Kas masuk: Sepeda Ontel CV (Transaksi 56)'),
(84, NULL, '2025-11-10', '2025-11', 7, 0.00, 15000000.00, 'Pendapatan penjualan: Sepeda Ontel CV (Transaksi 56)'),
(87, NULL, '2026-01-08', '2026-01', 9, 3500000.00, 0.00, 'Pengeluaran untuk Bayar Listrik Bulan Desember 2025 (Transaksi 58)'),
(88, NULL, '2026-01-08', '2026-01', 1, 0.00, 3500000.00, 'Kas keluar untuk Bayar Listrik Bulan Desember 2025 (Transaksi 58)'),
(89, NULL, '2026-01-27', '2026-01', 7, 44700000.00, 0.00, 'Jurnal Penutup Pendapatan'),
(90, NULL, '2026-01-27', '2026-01', 9, 0.00, 44700000.00, 'Penutup Pendapatan ke Ikhtisar'),
(91, NULL, '2026-01-27', '2026-01', 9, 5000000.00, 0.00, 'Penutup Beban'),
(92, NULL, '2026-01-27', '2026-01', 8, 0.00, 5000000.00, 'Jurnal Penutup Beban'),
(93, NULL, '2026-01-27', '2026-01', 9, 39700000.00, 0.00, 'Penutup Laba'),
(94, NULL, '2026-01-27', '2026-01', 1, 0.00, 39700000.00, 'Laba Ditahan ke Modal');

-- --------------------------------------------------------

--
-- Table structure for table `laba_rugi`
--

CREATE TABLE `laba_rugi` (
  `id_laba_rugi` int(11) NOT NULL,
  `periode` varchar(20) DEFAULT NULL,
  `total_pendapatan` decimal(15,2) DEFAULT NULL,
  `total_beban` decimal(15,2) DEFAULT NULL,
  `laba_bersih` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laba_rugi`
--

INSERT INTO `laba_rugi` (`id_laba_rugi`, `periode`, `total_pendapatan`, `total_beban`, `laba_bersih`) VALUES
(3, 'januari 2026', 29700000.00, 5000000.00, 24700000.00),
(4, 'februari 2026', 116200000.00, 8500000.00, 107700000.00);

-- --------------------------------------------------------

--
-- Table structure for table `neraca_saldo`
--

CREATE TABLE `neraca_saldo` (
  `id_neraca` int(11) NOT NULL,
  `id_akun` int(11) NOT NULL,
  `debet` decimal(15,2) DEFAULT 0.00,
  `kredit` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `neraca_saldo`
--

INSERT INTO `neraca_saldo` (`id_neraca`, `id_akun`, `debet`, `kredit`) VALUES
(106, 1, 161200000.00, 53500000.00),
(107, 2, 0.00, 0.00),
(108, 3, 0.00, 0.00),
(109, 4, 0.00, 0.00),
(110, 5, 0.00, 0.00),
(111, 6, 0.00, 0.00),
(112, 7, 0.00, 116200000.00),
(113, 8, 5000000.00, 0.00),
(114, 9, 3500000.00, 0.00),
(115, 10, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_beli` decimal(15,2) DEFAULT NULL,
  `harga_jual` decimal(15,2) DEFAULT NULL,
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `kategori`, `stok`, `satuan`, `harga_beli`, `harga_jual`, `tanggal_update`) VALUES
(1, 'Sepeda Gunung TREX X1', 'Sepeda', 99997, 'Unit', 2500000.00, 3500000.00, '2026-01-20 03:38:58'),
(2, 'Sepeda Lipat Family', 'Sepeda', 99997, 'Unit', 1800000.00, 2500000.00, '2026-01-20 01:44:46'),
(3, 'Helm Sepeda Polygon', 'Aksesoris', 699995, 'Pcs', 120000.00, 200000.00, '2026-01-20 03:38:58'),
(4, 'Lampu Depan LED', 'Aksesoris', 50, 'Pcs', 45000.00, 75000.00, '2025-09-24 10:12:19'),
(5, 'Sepeda Balap Trex X', 'Sepeda', 988, 'Unit', 78000000.00, 27000000.00, '2026-01-20 01:45:44'),
(10, 'Sepeda Ontel CV', 'Sepeda', 105, 'Unit', 5500000.00, 15000000.00, '2026-01-29 05:29:06'),
(11, 'Service', 'Sepeda', 499999, 'Unit', 50000.00, 100000.00, '2026-01-17 17:13:24'),
(12, 'Sepeda Zeuz 2344', 'Sepeda', 150, 'Pcs', 1000000000.00, 1600000.00, '2026-01-20 03:41:25'),
(14, 'Sepeda Lipat 20 Inch', 'Sepeda', 15, 'Unit', 27000000.00, 3200000.00, '2026-01-26 14:40:43'),
(20, 'Sepeda Gunung MTB 26 x BrunoMars', 'Sepeda', 10, 'Unit', 55000000.00, 80000000.00, '2026-01-26 14:43:07'),
(21, 'Sepeda Lipat 20 Inch Capt', 'Sepeda', 7, 'Unit', 1500000.00, 3200000.00, '2026-01-26 14:42:26'),
(22, 'Helm Sepeda XN', 'Aksesoris', 20, 'Pcs', 142000.00, 250000.00, '2026-01-26 14:41:45'),
(23, 'Pompa Ban Sepeda', 'Aksesoris', 30, 'Pcs', 277000.00, 350000.00, '2026-01-26 14:41:30'),
(24, 'Lampu LED Sepeda', 'Aksesoris', 25, 'Pcs', 63500.00, 120000.00, '2026-01-26 14:41:02'),
(25, 'Sepeda Balap Road Bike', 'Sepeda', 4, 'Unit', 2760000.00, 4800000.00, '2026-01-26 14:40:25'),
(26, 'Sepeda Anak 16 Inch', 'Sepeda', 8, 'Unit', 1300000.00, 1800000.00, '2026-01-26 14:40:02'),
(27, 'Ban Sepeda MTB', 'Sparepart', 40, 'Pcs', 100000.00, 150000.00, '2026-01-26 14:39:23'),
(28, 'Rantai Sepeda', 'Aksesoris', 35, 'Pcs', 56500.00, 90000.00, '2026-01-26 14:39:40'),
(29, 'Rem Cakram Sepeda', 'Sparepart', 15, 'Pcs', 2000000.00, 350000.00, '2026-01-26 14:39:09'),
(30, 'Jok Sepeda Comfort', 'Sparepart', 20, 'Pcs', 167000.00, 200000.00, '2026-01-26 14:38:47'),
(31, 'Sarung Tangan Sepeda', 'Aksesoris', 25, 'Pcs', 50000.00, 85000.00, '2026-01-26 14:38:30'),
(32, 'Kunci Sepeda', 'Aksesoris', 18, 'Pcs', 100000.00, 175000.00, '2026-01-26 14:38:09'),
(33, 'Spion Sepeda', 'Sparepart', 30, 'Pcs', 15000.00, 65000.00, '2026-01-26 14:38:15');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `pcs` int(11) DEFAULT 0,
  `jumlah` decimal(15,2) NOT NULL,
  `jenis_transaksi` enum('debet','kredit') NOT NULL,
  `jenis_pengeluaran` varchar(100) DEFAULT NULL,
  `created_by` enum('admin','kasir') NOT NULL DEFAULT 'admin',
  `id_user` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal`, `keterangan`, `pcs`, `jumlah`, `jenis_transaksi`, `jenis_pengeluaran`, `created_by`, `id_user`, `created_at`) VALUES
(1, '2025-01-05', 'Penjualan Sepeda Gunung MTB 26', 0, 2500000.00, 'debet', NULL, 'admin', NULL, '2026-01-26 14:07:03'),
(2, '2025-01-06', 'Penjualan Helm Sepeda', 0, 250000.00, 'debet', NULL, 'admin', NULL, '2026-01-26 14:07:03'),
(3, '2025-01-07', 'Pembelian Stok Sepeda', 0, 1500000.00, 'kredit', NULL, 'admin', NULL, '2026-01-26 14:07:03'),
(4, '2025-01-08', 'Penjualan Sepeda Lipat 20 Inch', 0, 3200000.00, 'debet', NULL, 'admin', NULL, '2026-01-26 14:07:03'),
(5, '2025-01-09', 'Pembelian Aksesoris', 0, 500000.00, 'kredit', NULL, 'admin', NULL, '2026-01-26 14:07:03'),
(48, '2026-01-17', 'Sepeda Gunung TREX X1 x1 Unit, Service x1 Unit, Helm Sepeda Polygon x1 Pcs', 3, 3800000.00, 'debet', NULL, 'kasir', 7, '2026-01-17 17:13:24'),
(49, '2026-01-17', 'Helm Sepeda Polygon x1 Pcs, Sepeda Lipat Family x1 Unit, Sepeda Balap Trex X x1 Unit', 3, 29700000.00, 'debet', NULL, 'kasir', 7, '2026-01-17 17:16:11'),
(50, '2026-01-10', 'Gaji Karyawan', 1, 5000000.00, 'kredit', 'lainnya', 'admin', NULL, '2026-01-17 17:17:45'),
(51, '2026-01-14', 'Sepeda Zeuz 2344', 15, 45000000.00, 'kredit', 'pembelian', 'admin', NULL, '2026-01-17 17:19:07'),
(52, '2026-01-08', 'Sepeda Ontel CV', 1, 15000000.00, 'debet', NULL, 'admin', NULL, '2026-01-17 17:49:57'),
(55, '2025-12-20', 'Sepeda Balap Trex X', 1, 27000000.00, 'debet', NULL, 'admin', NULL, '2026-01-20 01:45:44'),
(56, '2025-11-10', 'Sepeda Ontel CV', 1, 15000000.00, 'debet', NULL, 'admin', NULL, '2026-01-20 01:46:40'),
(58, '2026-01-08', 'Bayar Listrik Bulan Desember 2025', 1, 3500000.00, 'kredit', 'lainnya', 'admin', NULL, '2026-01-26 14:45:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `level` enum('admin','kasir') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `level`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'Administrator', 'admin'),
(4, 'santoz', '814c1f252734dd419a5353c8eb600c77', 'jason santoso', 'admin'),
(6, 'fano', '195b167672e599a2f454eb3d6f9aa555', 'Fano sepuh', 'kasir'),
(7, 'bagus', '17b38fc02fd7e92f3edeb6318e3066d8', 'bagus', 'kasir'),
(9, 'mekel', '6cb3b4ca0c2580d660218e80eeea424f', 'mekel', 'kasir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id_akun`),
  ADD UNIQUE KEY `kode_akun` (`kode_akun`);

--
-- Indexes for table `buku_besar`
--
ALTER TABLE `buku_besar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_buku_besar_akun_baru` (`id_akun`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `histori_hapus`
--
ALTER TABLE `histori_hapus`
  ADD PRIMARY KEY (`id_histori`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `fk_jurnal_umum_akun` (`id_akun`),
  ADD KEY `fk_jurnal_umum_transaksi` (`id_transaksi`);

--
-- Indexes for table `laba_rugi`
--
ALTER TABLE `laba_rugi`
  ADD PRIMARY KEY (`id_laba_rugi`);

--
-- Indexes for table `neraca_saldo`
--
ALTER TABLE `neraca_saldo`
  ADD PRIMARY KEY (`id_neraca`),
  ADD KEY `fk_neraca_saldo_akun` (`id_akun`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun`
--
ALTER TABLE `akun`
  MODIFY `id_akun` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `buku_besar`
--
ALTER TABLE `buku_besar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `histori_hapus`
--
ALTER TABLE `histori_hapus`
  MODIFY `id_histori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `laba_rugi`
--
ALTER TABLE `laba_rugi`
  MODIFY `id_laba_rugi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `neraca_saldo`
--
ALTER TABLE `neraca_saldo`
  MODIFY `id_neraca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buku_besar`
--
ALTER TABLE `buku_besar`
  ADD CONSTRAINT `fk_buku_besar_akun_baru` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id_akun`);

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `jurnal_umum`
--
ALTER TABLE `jurnal_umum`
  ADD CONSTRAINT `fk_jurnal_umum_akun` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id_akun`),
  ADD CONSTRAINT `fk_jurnal_umum_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `neraca_saldo`
--
ALTER TABLE `neraca_saldo`
  ADD CONSTRAINT `fk_neraca_saldo_akun` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id_akun`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
