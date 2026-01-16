-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 28, 2025 at 02:57 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erpta`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `id_karyawan` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_login` enum('1','2') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_buat` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`id_karyawan`, `username`, `password`, `status_login`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('K001', 'admin', '$2y$12$BYj8huy3ywM2wwoEyxxkE.oyQx/6v8JpxxVddKEZux/Um53g7QpB2', '1', NULL, 'K001', '2025-11-17 08:14:56', '2025-11-29 11:16:10'),
('K002', 'gembul', '$2y$12$W2VSmmTdsTnyg3npkoyyVOLuih2BvR8lWbrpVSf87JMpDQm1vQjcK', '1', 'K001', 'K001', '2025-11-29 09:58:14', '2025-11-30 05:41:27'),
('K003', 'met', '$2y$12$kipD5uMnAddlHapZjynQ5eotImhabhO4xg91sk6xW5TeGtehmazPS', '2', 'K001', NULL, '2025-11-30 02:46:05', '2025-11-30 02:46:05'),
('K004', 'adi', '$2y$12$82zKr6AMsh2K8Qk54FgM2e2oFpNuP/2zkOseY5NtqmhaAxq7Bn2Cy', '2', 'K001', NULL, '2025-12-07 23:13:52', '2025-12-07 23:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok_bahan_baku` int NOT NULL DEFAULT '0',
  `satuan` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_buat` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bahan_baku`
--

INSERT INTO `bahan_baku` (`id`, `nama`, `stok_bahan_baku`, `satuan`, `deskripsi`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('BB-001', 'Kain', 70, 'roll', '-', NULL, NULL, '2025-12-01 14:26:16', '2025-12-01 14:26:16'),
('BB-002', 'Benang', 31, 'pcs', 'benang merah', NULL, NULL, '2025-12-21 05:29:36', '2025-12-21 05:29:36'),
('BB-003', 'lem', 61, 'pcs', 'perekat', NULL, NULL, '2025-12-21 05:30:40', '2025-12-21 05:30:40');

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku_keluar`
--

CREATE TABLE `bahan_baku_keluar` (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_karyawan_gudang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bahan_baku_keluar`
--

INSERT INTO `bahan_baku_keluar` (`id`, `id_karyawan_gudang`, `tanggal_transaksi`, `keterangan`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('BK-202512-0001', 'K001', '2025-12-28 21:34:00', NULL, 'K001', NULL, '2025-12-28 14:35:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku_masuk`
--

CREATE TABLE `bahan_baku_masuk` (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_karyawan_gudang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `id_toko` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_nota` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bahan_baku_masuk`
--

INSERT INTO `bahan_baku_masuk` (`id`, `id_karyawan_gudang`, `tanggal_transaksi`, `id_toko`, `no_nota`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('BM-202512-0001', 'K001', '2025-12-28 21:30:00', 'SPL-001', '207', 'K001', NULL, '2025-12-28 14:31:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_bahan_baku_keluar`
--

CREATE TABLE `detail_bahan_baku_keluar` (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_bahan_baku_keluar` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_bahan_baku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_bahan_baku_keluar`
--

INSERT INTO `detail_bahan_baku_keluar` (`id`, `id_bahan_baku_keluar`, `id_bahan_baku`, `jumlah`, `satuan`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('BBKD-6951402b8cd61', 'BK-202512-0001', 'BB-001', 80, 'roll', 'K001', NULL, '2025-12-28 14:35:23', NULL),
('BBKD-6951402b8ec98', 'BK-202512-0001', 'BB-002', 20, 'pcs', 'K001', NULL, '2025-12-28 14:35:23', NULL),
('BBKD-6951402b90249', 'BK-202512-0001', 'BB-003', 100, 'pcs', 'K001', NULL, '2025-12-28 14:35:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_bahan_baku_masuk`
--

CREATE TABLE `detail_bahan_baku_masuk` (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_bahan_baku_masuk` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_bahan_baku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `nominal` int NOT NULL DEFAULT '0',
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_bahan_baku_masuk`
--

INSERT INTO `detail_bahan_baku_masuk` (`id`, `id_bahan_baku_masuk`, `id_bahan_baku`, `jumlah`, `nominal`, `satuan`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('BBMD-69513fccc7e6d', 'BM-202512-0001', 'BB-001', 20, 8000, 'roll', NULL, NULL, NULL, NULL),
('BBMD-69513fccc8b23', 'BM-202512-0001', 'BB-002', 10, 2000, 'pcs', NULL, NULL, NULL, NULL),
('BBMD-69513fccc988f', 'BM-202512-0001', 'BB-003', 80, 2500, 'pcs', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_order`
--

CREATE TABLE `detail_order` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_transaksi` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `harga` int NOT NULL DEFAULT '0',
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_order`
--

INSERT INTO `detail_order` (`id`, `id_transaksi`, `id_produk`, `jumlah`, `harga`, `satuan`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('DBWTDC1RKDZ', 'ORD-12', 'PRD-003', 3, 10000, 'pcs', 'K001', NULL, '2025-12-26 04:05:26', NULL),
('DH8OXMHLXSH', 'ORD-11', 'PRD-002', 1, 9000, 'pcs', 'K001', NULL, '2025-12-26 03:32:14', NULL),
('DIEL6ZFKZ4U', 'ORD-04', 'PRD-002', 1, 9000, 'pcs', 'K001', NULL, '2025-12-07 22:35:51', NULL),
('DJD3IJVKYTC', 'ORD-11', 'PRD-003', 3, 10000, 'pcs', 'K001', NULL, '2025-12-26 03:32:14', NULL),
('DJDQHTJO0MP', 'ORD-07', 'PRD-003', 3, 10000, 'pcs', 'K001', NULL, '2025-12-09 01:01:30', NULL),
('DK7MCTQAYMC', 'ORD-01', 'PRD-003', 2, 10000, 'pcs', 'K001', NULL, '2025-12-07 17:44:15', NULL),
('DREVFNXOMUW', 'ORD-08', 'PRD-002', 1, 9000, 'pcs', 'K001', NULL, '2025-12-09 02:55:25', NULL),
('DVQTLDUSFN1', 'ORD-06', 'PRD-003', 1, 10000, 'pcs', 'K001', NULL, '2025-12-07 23:17:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_produk_masuk`
--

CREATE TABLE `detail_produk_masuk` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produk_masuk` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_produk_masuk`
--

INSERT INTO `detail_produk_masuk` (`id`, `id_produk`, `id_produk_masuk`, `jumlah`, `satuan`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('PMD-6951438d8b3ed', 'PRD-001', 'PM-202512-0001', 10, 'pcs', NULL, NULL, NULL, NULL),
('PMD-6951438d8c88f', 'PRD-002', 'PM-202512-0001', 10, 'pcs', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `nama`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('001', 'admin', NULL, 'K001', '2025-11-17 08:14:56', '2025-11-29 03:22:25'),
('002', 'gudang', 'K001', NULL, '2025-11-29 03:26:17', '2025-11-29 03:26:17'),
('003', 'sales', 'K001', NULL, '2025-11-29 03:26:25', '2025-11-29 03:26:25');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jabatan` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `jenis_kelamin` enum('Laki-laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tempat_lahir` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `id_buat` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `id_jabatan`, `nama`, `alamat`, `jenis_kelamin`, `no_hp`, `status`, `tempat_lahir`, `tanggal_lahir`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('K001', '001', 'Oiyyy', 'Jombang', 'Laki-laki', '081234567890', 'Y', 'Jombang', '2000-01-01', NULL, NULL, '2025-11-17 08:14:56', '2025-11-22 12:16:28'),
('K002', '002', 'Gembul', 'mbito', 'Laki-laki', '0828', 'Y', 'Jombang', '2021-02-21', 'K001', 'K001', '2025-11-29 09:57:13', '2025-11-30 05:54:17'),
('K003', '003', 'metS', 'mbitto', 'Laki-laki', '0981', 'Y', 'Jombang', '2011-11-11', 'K001', 'K001', '2025-11-29 09:57:47', '2025-12-07 23:16:11'),
('K004', '003', 'Adi', 'Kepuhdoko', 'Laki-laki', '08979', 'Y', 'Jombang', '2012-12-12', 'K001', 'K001', '2025-12-07 18:02:01', '2025-12-08 05:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(13, '2025_11_23_151845_pelanggan', 1),
(14, '2025_11_26_125615_create_bahan_baku_table', 2),
(15, '2025_11_26_140019_create_produk_table', 3),
(16, '2025_12_01_122354_t_bahan_baku_masuk', 4),
(17, '2025_11_26_130000_create_bahan_baku_table', 5),
(32, '2025_12_01_130001_t_bahan_baku_masuk', 6),
(33, '2025_12_01_130002_detail_t_bahan_baku_masuk', 6),
(34, '2025_12_01_130003_t_bahan_baku_keluar', 6),
(35, '2025_12_01_130004_detail_t_bahan_baku_keluar', 6),
(36, '2025_12_01_130005_t_produk_masuk', 6),
(37, '2025_12_01_130006_detail_t_produk_masuk', 6),
(38, '2025_12_01_130007_order', 6),
(39, '2025_12_01_130008_detail_order', 6),
(40, '2025_12_01_130009_pembayaran_cash', 6),
(41, '2025_12_01_130010_pembayaran_va', 6),
(42, '2025_12_01_125428_detail_order', 1),
(43, '2025_12_10_032554_add_status_to_pembayaran_va_table', 7),
(44, '2025_12_15_030725_add_status_to_pembayaran_va_table', 7),
(45, '2025_12_22_021712_supplier', 8),
(46, '2025_12_28_121624_pengaturan_sistem', 9);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_sales` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_toko` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `id_sales`, `id_toko`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('ORD-01', 'K001', 'TOKO-001', 'K001', 'K001', '2025-12-07 17:44:15', NULL),
('ORD-04', 'K003', 'TOKO-001', 'K001', 'K001', '2025-12-07 22:35:51', NULL),
('ORD-06', 'K004', 'TOKO-003', 'K001', 'K001', '2025-12-07 23:17:46', NULL),
('ORD-07', 'K003', 'TOKO-003', 'K001', 'K001', '2025-12-09 01:01:30', NULL),
('ORD-08', 'K004', 'TOKO-003', 'K001', 'K001', '2025-12-09 02:55:25', NULL),
('ORD-11', 'K004', 'TOKO-001', 'K001', 'K001', '2025-12-26 03:32:14', NULL),
('ORD-12', 'K004', 'TOKO-001', 'K001', 'K001', '2025-12-26 04:05:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `no_hp`, `alamat`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('CUST001', 'Adam', '09871', 'Diwek', 'K001', NULL, '2025-11-30 02:37:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_cash`
--

CREATE TABLE `pembayaran_cash` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` bigint UNSIGNED NOT NULL,
  `id_order` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `petugas_pembayaran` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran_cash`
--

INSERT INTO `pembayaran_cash` (`id`, `nominal`, `id_order`, `petugas_pembayaran`, `tanggal_pembayaran`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('PC6PQRIXBLP5', 9000, 'ORD-04', 'K001', '2025-12-07 22:38:46', 'K001', 'K001', '2025-12-07 22:38:46', NULL),
('PCE01NHD5K4K', 100, 'ORD-07', 'K001', '2025-12-09 02:54:06', 'K001', 'K001', '2025-12-09 02:54:06', NULL),
('PCJAL9PP2KS1', 5000, 'ORD-06', 'K001', '2025-12-07 23:19:58', 'K001', 'K001', '2025-12-07 23:19:58', NULL),
('PCTRF9997E3L', 5000, 'ORD-07', 'K001', '2025-12-09 02:42:37', 'K001', 'K001', '2025-12-09 02:42:37', NULL),
('PCYI9RMFQ2FQ', 5000, 'ORD-06', 'K001', '2025-12-07 23:18:26', 'K001', 'K001', '2025-12-07 23:18:26', NULL),
('PCZ5SYHYR16S', 100, 'ORD-07', 'K001', '2025-12-09 02:47:03', 'K001', 'K001', '2025-12-09 02:47:03', NULL),
('PCZWKUAVBDEZ', 100, 'ORD-07', 'K001', '2025-12-09 14:56:17', 'K001', 'K001', '2025-12-09 14:56:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_va`
--

CREATE TABLE `pembayaran_va` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` bigint UNSIGNED NOT NULL,
  `id_order` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_transaksi` datetime DEFAULT NULL,
  `payment_link` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_ref` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','paid','expired','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran_va`
--

INSERT INTO `pembayaran_va` (`id`, `nominal`, `id_order`, `channel`, `tanggal_transaksi`, `payment_link`, `nomor_ref`, `status`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('PV12DQ3IEXPV', 10000, 'ORD-12', 'bank_transfer', '2025-12-26 04:05:45', 'https://app.sandbox.midtrans.com/snap/v4/redirection/96c10f04-1d41-48ca-8306-c10774f944ec', 'VA-ORD-12-b3a0b0f8-feb2-45c8-a306-b9a04a0fdabf', 'paid', 'K001', NULL, '2025-12-26 04:05:45', '2025-12-26 04:06:16'),
('PV12VGCNKREO', 2500, 'ORD-12', 'qris', '2025-12-26 04:07:44', 'https://app.sandbox.midtrans.com/snap/v4/redirection/8fc1612f-7f36-4f0b-8602-75fb4b1a4c61', 'VA-ORD-12-c4dfc0d5-b3fd-4590-91a8-d81ce9864ddc', 'paid', 'K001', NULL, '2025-12-26 04:07:44', '2025-12-26 04:08:43'),
('PVABAHHKGMMP', 10000, 'ORD-11', 'QRIS', '2025-12-26 05:09:39', 'https://app.sandbox.midtrans.com/snap/v4/redirection/6c316446-3ed2-4bc4-9e0a-c82aa3c33aa9', 'VA-ORD-11-ec7864a9-864d-47d4-a6fe-a9123d32dc02', 'expired', 'K001', NULL, '2025-12-26 05:09:39', '2025-12-26 05:26:02'),
('PVAOF9HBIE0Q', 3000, 'ORD-12', 'dana', '2025-12-26 04:12:11', 'https://app.sandbox.midtrans.com/snap/v4/redirection/0d58115a-db01-4656-b817-3d154d8b8374', 'VA-ORD-12-fd60d3ec-efcb-4874-8480-c7c91a81f951', 'paid', 'K001', NULL, '2025-12-26 04:12:11', '2025-12-26 04:12:37'),
('PVB0CWR3AJ1P', 1000, 'ORD-01', 'PERMATA', '2025-12-26 05:55:30', 'https://app.sandbox.midtrans.com/snap/v4/redirection/bb84ef90-89f4-42fe-8c70-37ec7e69f158', 'VA-ORD-01-dd276584-5fc0-4d5e-986b-de48a6b5a5df', 'paid', 'K001', NULL, '2025-12-26 05:55:30', '2025-12-26 06:02:36'),
('PVBU83CKTTAL', 4500, 'ORD-12', 'BSI', '2025-12-26 05:12:22', 'https://app.sandbox.midtrans.com/snap/v4/redirection/f999117f-e9c8-49e1-83e3-6c0502f84f20', 'VA-ORD-12-6c467ce8-9e31-420d-a8a8-0f4185347f02', 'paid', 'K001', NULL, '2025-12-26 05:12:22', '2025-12-26 05:13:07'),
('PVJUCOQNMQ6X', 4700, 'ORD-07', 'BNI', '2025-12-26 04:59:50', 'https://app.sandbox.midtrans.com/snap/v4/redirection/91d40836-4a60-4deb-811f-148a3c1a0868', 'VA-ORD-07-e53c9606-f4fd-4b25-ba34-0162f4701719', 'paid', 'K001', NULL, '2025-12-26 04:59:50', '2025-12-26 05:00:39'),
('PVRCBAVPH5FR', 10000, 'ORD-11', 'AKULAKU', '2025-12-26 05:12:05', 'https://app.sandbox.midtrans.com/snap/v4/redirection/0c36ab42-df42-4c58-bb91-8be7bdb24a58', 'VA-ORD-11-982ddc41-c67b-4f76-8425-2184ee50b30a', 'paid', 'K001', NULL, '2025-12-26 05:12:05', '2025-12-26 05:13:49'),
('PVSIGG1JK0P8', 5000, 'ORD-07', 'QRIS', '2025-12-26 05:01:30', 'https://app.sandbox.midtrans.com/snap/v4/redirection/0a0ecdf6-73e4-4c2d-8df3-2306af863e95', 'VA-ORD-07-d0473b07-4bc6-42f3-814e-2e470e6837f1', 'paid', 'K001', NULL, '2025-12-26 05:01:30', '2025-12-26 05:04:17'),
('PVUVIFSWKN2V', 10000, 'ORD-11', 'ALFAMART', '2025-12-26 05:10:43', 'https://app.sandbox.midtrans.com/snap/v4/redirection/e8d602aa-12d5-4078-b268-000e072a830f', 'VA-ORD-11-36377946-a588-4869-9c62-55da1a17bd36', 'paid', 'K001', NULL, '2025-12-26 05:10:43', '2025-12-26 05:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_sistem`
--

CREATE TABLE `pengaturan_sistem` (
  `id` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ketentuan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `id_buat` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan_sistem`
--

INSERT INTO `pengaturan_sistem` (`id`, `ketentuan`, `keterangan`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('KFG-001', '5000000', 'untuk membatasi limit sales', 'K001', 'K001', '2025-12-28 20:30:28', '2025-12-28 14:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` int NOT NULL DEFAULT '0',
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stok` int NOT NULL DEFAULT '0',
  `id_buat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `foto`, `harga`, `satuan`, `deskripsi`, `stok`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('PRD-001', 'Tas Spunbound', 'produk/PRD-001/1.jpg', 5000, 'pcs', 'Tas Spunbound uk 4x5', 15, NULL, NULL, '2025-11-26 14:36:44', '2025-11-26 15:30:44'),
('PRD-002', 'Royz', 'produk/PRD-002/1.jpg', 9000, 'pcs', 'yyyrght', 16, NULL, NULL, '2025-11-26 15:13:15', '2025-11-27 03:44:22'),
('PRD-003', 'Tas tis', 'produk/PRD-003/1.jpg', 10000, 'pcs', 'ysrght', 30, NULL, NULL, '2025-12-03 01:32:39', '2025-12-03 01:32:39');

-- --------------------------------------------------------

--
-- Table structure for table `produk_masuk`
--

CREATE TABLE `produk_masuk` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_karyawan_gudang` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_karyawan_produksi` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk_masuk`
--

INSERT INTO `produk_masuk` (`id`, `id_karyawan_gudang`, `id_karyawan_produksi`, `tanggal_transaksi`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('PM-202512-0001', 'K001', 'K001', '2025-12-28 21:40:00', 'K001', NULL, '2025-12-28 14:49:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_toko` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_buat` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama_toko`, `alamat`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('SPL-001', 'Toko Kain', 'Jl. Wahidin Sudiro Husodo, No.25, Kab. Jombang', 'K001', 'K001', '2025-12-22 14:04:33', '2025-12-22 14:04:33');

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE `toko` (
  `id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_karyawan` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pelanggan` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_toko` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lintang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bujur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buat` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_update` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_buat` datetime NOT NULL,
  `tanggal_update` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `toko`
--

INSERT INTO `toko` (`id`, `id_karyawan`, `id_pelanggan`, `nama_toko`, `alamat`, `lintang`, `bujur`, `id_buat`, `id_update`, `tanggal_buat`, `tanggal_update`) VALUES
('TOKO-001', 'K004', 'CUST001', 'Berkah Jaya', 'SBY', 'SBY', 'SBY', 'K001', 'K001', '2025-11-30 02:46:44', '2025-12-07 22:41:46'),
('TOKO-002', 'K003', 'CUST001', 'Ysroy', 'X-Pr', '404', '500', 'K001', NULL, '2025-12-03 05:03:39', NULL),
('TOKO-003', 'K003', 'CUST001', 'dimdim', 'asd', 'asd', 'asd', 'K001', 'K001', '2025-12-07 23:17:27', '2025-12-08 05:56:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD KEY `akun_id_buat_foreign` (`id_buat`),
  ADD KEY `akun_id_update_foreign` (`id_update`);

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bahan_baku_id_buat_foreign` (`id_buat`),
  ADD KEY `bahan_baku_id_update_foreign` (`id_update`);

--
-- Indexes for table `bahan_baku_keluar`
--
ALTER TABLE `bahan_baku_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bahan_baku_keluar_id_karyawan_gudang_foreign` (`id_karyawan_gudang`),
  ADD KEY `bahan_baku_keluar_id_buat_foreign` (`id_buat`),
  ADD KEY `bahan_baku_keluar_id_update_foreign` (`id_update`);

--
-- Indexes for table `bahan_baku_masuk`
--
ALTER TABLE `bahan_baku_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bahan_baku_masuk_id_karyawan_gudang_foreign` (`id_karyawan_gudang`),
  ADD KEY `bahan_baku_masuk_id_buat_foreign` (`id_buat`),
  ADD KEY `bahan_baku_masuk_id_update_foreign` (`id_update`),
  ADD KEY `id_toko` (`id_toko`);

--
-- Indexes for table `detail_bahan_baku_keluar`
--
ALTER TABLE `detail_bahan_baku_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_bahan_baku_keluar_id_bahan_baku_keluar_foreign` (`id_bahan_baku_keluar`),
  ADD KEY `detail_bahan_baku_keluar_id_bahan_baku_foreign` (`id_bahan_baku`),
  ADD KEY `detail_bahan_baku_keluar_id_buat_foreign` (`id_buat`),
  ADD KEY `detail_bahan_baku_keluar_id_update_foreign` (`id_update`);

--
-- Indexes for table `detail_bahan_baku_masuk`
--
ALTER TABLE `detail_bahan_baku_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_bahan_baku_masuk_id_bahan_baku_masuk_foreign` (`id_bahan_baku_masuk`),
  ADD KEY `detail_bahan_baku_masuk_id_bahan_baku_foreign` (`id_bahan_baku`),
  ADD KEY `detail_bahan_baku_masuk_id_buat_foreign` (`id_buat`),
  ADD KEY `detail_bahan_baku_masuk_id_update_foreign` (`id_update`);

--
-- Indexes for table `detail_order`
--
ALTER TABLE `detail_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_order_id_buat_foreign` (`id_buat`),
  ADD KEY `detail_order_id_update_foreign` (`id_update`),
  ADD KEY `detail_order_id_transaksi_index` (`id_transaksi`),
  ADD KEY `detail_order_id_produk_index` (`id_produk`);

--
-- Indexes for table `detail_produk_masuk`
--
ALTER TABLE `detail_produk_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_produk_masuk_id_produk_masuk_foreign` (`id_produk_masuk`),
  ADD KEY `detail_produk_masuk_id_buat_foreign` (`id_buat`),
  ADD KEY `detail_produk_masuk_id_update_foreign` (`id_update`),
  ADD KEY `detail_produk_masuk_id_produk_foreign` (`id_produk`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id_jabatan_foreign` (`id_jabatan`),
  ADD KEY `karyawan_id_buat_foreign` (`id_buat`),
  ADD KEY `karyawan_id_update_foreign` (`id_update`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id_toko_foreign` (`id_toko`),
  ADD KEY `order_id_buat_foreign` (`id_buat`),
  ADD KEY `order_id_update_foreign` (`id_update`),
  ADD KEY `order_id_sales_foreign` (`id_sales`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran_cash`
--
ALTER TABLE `pembayaran_cash`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_cash_id_order_foreign` (`id_order`),
  ADD KEY `pembayaran_cash_petugas_pembayaran_foreign` (`petugas_pembayaran`),
  ADD KEY `pembayaran_cash_id_buat_foreign` (`id_buat`),
  ADD KEY `pembayaran_cash_id_update_foreign` (`id_update`);

--
-- Indexes for table `pembayaran_va`
--
ALTER TABLE `pembayaran_va`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_va_id_order_foreign` (`id_order`),
  ADD KEY `pembayaran_va_id_buat_foreign` (`id_buat`),
  ADD KEY `pembayaran_va_id_update_foreign` (`id_update`);

--
-- Indexes for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengaturan_sistem_id_buat_foreign` (`id_buat`),
  ADD KEY `pengaturan_sistem_id_update_foreign` (`id_update`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id_buat_foreign` (`id_buat`),
  ADD KEY `produk_id_update_foreign` (`id_update`);

--
-- Indexes for table `produk_masuk`
--
ALTER TABLE `produk_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_masuk_id_karyawan_gudang_foreign` (`id_karyawan_gudang`),
  ADD KEY `produk_masuk_id_karyawan_produksi_foreign` (`id_karyawan_produksi`),
  ADD KEY `produk_masuk_id_buat_foreign` (`id_buat`),
  ADD KEY `produk_masuk_id_update_foreign` (`id_update`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id_buat_foreign` (`id_buat`),
  ADD KEY `supplier_id_update_foreign` (`id_update`);

--
-- Indexes for table `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `akun`
--
ALTER TABLE `akun`
  ADD CONSTRAINT `akun_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD CONSTRAINT `bahan_baku_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `karyawan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bahan_baku_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `karyawan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bahan_baku_keluar`
--
ALTER TABLE `bahan_baku_keluar`
  ADD CONSTRAINT `bahan_baku_keluar_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `bahan_baku_keluar_id_karyawan_gudang_foreign` FOREIGN KEY (`id_karyawan_gudang`) REFERENCES `karyawan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `bahan_baku_keluar_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `bahan_baku_masuk`
--
ALTER TABLE `bahan_baku_masuk`
  ADD CONSTRAINT `bahan_baku_masuk_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `bahan_baku_masuk_id_karyawan_gudang_foreign` FOREIGN KEY (`id_karyawan_gudang`) REFERENCES `karyawan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `bahan_baku_masuk_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `id_toko` FOREIGN KEY (`id_toko`) REFERENCES `supplier` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `detail_bahan_baku_keluar`
--
ALTER TABLE `detail_bahan_baku_keluar`
  ADD CONSTRAINT `detail_bahan_baku_keluar_id_bahan_baku_foreign` FOREIGN KEY (`id_bahan_baku`) REFERENCES `bahan_baku` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_bahan_baku_keluar_id_bahan_baku_keluar_foreign` FOREIGN KEY (`id_bahan_baku_keluar`) REFERENCES `bahan_baku_keluar` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_bahan_baku_keluar_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_bahan_baku_keluar_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `detail_bahan_baku_masuk`
--
ALTER TABLE `detail_bahan_baku_masuk`
  ADD CONSTRAINT `detail_bahan_baku_masuk_id_bahan_baku_foreign` FOREIGN KEY (`id_bahan_baku`) REFERENCES `bahan_baku` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_bahan_baku_masuk_id_bahan_baku_masuk_foreign` FOREIGN KEY (`id_bahan_baku_masuk`) REFERENCES `bahan_baku_masuk` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_bahan_baku_masuk_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_bahan_baku_masuk_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `detail_order`
--
ALTER TABLE `detail_order`
  ADD CONSTRAINT `detail_order_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_order_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_order_id_transaksi_foreign` FOREIGN KEY (`id_transaksi`) REFERENCES `order` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_order_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `detail_produk_masuk`
--
ALTER TABLE `detail_produk_masuk`
  ADD CONSTRAINT `detail_produk_masuk_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`),
  ADD CONSTRAINT `detail_produk_masuk_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `detail_produk_masuk_id_produk_masuk_foreign` FOREIGN KEY (`id_produk_masuk`) REFERENCES `produk_masuk` (`id`),
  ADD CONSTRAINT `detail_produk_masuk_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`);

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_id_jabatan_foreign` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `order_id_sales_foreign` FOREIGN KEY (`id_sales`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `order_id_toko_foreign` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `order_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran_cash`
--
ALTER TABLE `pembayaran_cash`
  ADD CONSTRAINT `pembayaran_cash_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_cash_id_order_foreign` FOREIGN KEY (`id_order`) REFERENCES `order` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_cash_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_cash_petugas_pembayaran_foreign` FOREIGN KEY (`petugas_pembayaran`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran_va`
--
ALTER TABLE `pembayaran_va`
  ADD CONSTRAINT `pembayaran_va_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_va_id_order_foreign` FOREIGN KEY (`id_order`) REFERENCES `order` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_va_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  ADD CONSTRAINT `pengaturan_sistem_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pengaturan_sistem_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `karyawan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `produk_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `karyawan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `produk_masuk`
--
ALTER TABLE `produk_masuk`
  ADD CONSTRAINT `produk_masuk_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`),
  ADD CONSTRAINT `produk_masuk_id_karyawan_gudang_foreign` FOREIGN KEY (`id_karyawan_gudang`) REFERENCES `karyawan` (`id`),
  ADD CONSTRAINT `produk_masuk_id_karyawan_produksi_foreign` FOREIGN KEY (`id_karyawan_produksi`) REFERENCES `karyawan` (`id`),
  ADD CONSTRAINT `produk_masuk_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`);

--
-- Constraints for table `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_id_buat_foreign` FOREIGN KEY (`id_buat`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `supplier_id_update_foreign` FOREIGN KEY (`id_update`) REFERENCES `akun` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `toko`
--
ALTER TABLE `toko`
  ADD CONSTRAINT `toko_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `toko_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
