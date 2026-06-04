-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2026 at 12:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kpad`
--

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id_lokasi` int(11) NOT NULL,
  `id_UMKM` int(11) NOT NULL,
  `kota` varchar(25) NOT NULL,
  `kacamatan` varchar(25) NOT NULL,
  `jalan` varchar(50) NOT NULL,
  `link_gmap` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `id_UMKM` int(11) NOT NULL,
  `nama_menu` varchar(50) NOT NULL,
  `harga_menu` int(11) NOT NULL,
  `rasa` set('manis','asin','pedas','masam','pahit') DEFAULT NULL,
  `kategori` enum('makanan_berat','cemilan','minuman') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode` int(11) NOT NULL,
  `id_UMKM` int(11) NOT NULL,
  `nama_metode` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mitra_online`
--

CREATE TABLE `mitra_online` (
  `id_mitra` int(11) NOT NULL,
  `id_UMKM` int(11) NOT NULL,
  `nama_mitra` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `umkm`
--

CREATE TABLE `umkm` (
  `id_UMKM` int(11) NOT NULL,
  `nama_UMKM` varchar(50) NOT NULL,
  `status_halal` enum('halal','bersertifikat','non-halal') NOT NULL DEFAULT 'halal',
  `nomor_kontak` varchar(20) NOT NULL,
  `layanan_makan` set('dine_in','takeaway') NOT NULL DEFAULT 'takeaway'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `umkm`
--

INSERT INTO `umkm` (`id_UMKM`, `nama_UMKM`, `status_halal`, `nomor_kontak`, `layanan_makan`) VALUES
(1, 'Yamin baik', 'bersertifikat', '085863173970', 'dine_in,takeaway'),
(2, 'Tanpa Nama', 'halal', '08986687693', 'takeaway'),
(3, 'Warung Tieka', 'bersertifikat', '089529841554', 'takeaway'),
(4, 'Batagor', 'halal', '-', 'takeaway'),
(5, 'Kopi noname', 'halal', '085624374262', 'takeaway'),
(6, 'Seblak Al_Dzikri', 'bersertifikat', '085222888679', 'takeaway'),
(7, 'Crepes sibolang', 'halal', '-', 'takeaway'),
(8, 'Warung Pangyami Elmi', 'bersertifikat', '081546517472', 'dine_in,takeaway'),
(9, 'Gado-Gado Teh Kulsum', 'bersertifikat', '081225725880', 'takeaway'),
(10, 'Warung Makan Bunda Fida', 'halal', '-', 'dine_in,takeaway'),
(11, 'Gorengan', 'halal', '-', 'takeaway'),
(12, 'Bubur ayam uduk spesial', 'bersertifikat', '-', 'dine_in,takeaway'),
(13, 'Dapoer Teh Marina', 'halal', '082214600168', 'dine_in,takeaway'),
(14, 'Makaroni K Basah', 'bersertifikat', '0895326458251', 'takeaway'),
(15, 'Lumpia Basah Echo', 'bersertifikat', '-', 'takeaway'),
(16, 'Daifuku Mochi Gemoy KPAD', 'bersertifikat', '085314028481', 'takeaway'),
(17, 'Martabak Telor Titirah', 'halal', '0857162041141', 'takeaway'),
(18, 'Pop Ice Sticky Milk', 'halal', '0881022988626', 'takeaway'),
(19, 'Hanayoshi Takoyaki', 'bersertifikat', '081991606326', 'takeaway'),
(20, 'Cilung Jadul', 'bersertifikat', '081322169444', 'takeaway'),
(21, 'Baso Tahu Yoga', 'bersertifikat', '085722471485', 'takeaway');

-- --------------------------------------------------------

--
-- Table structure for table `waktu_operasional`
--

CREATE TABLE `waktu_operasional` (
  `id_UMKM` int(11) NOT NULL,
  `hari_buka` set('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Setiap_hari') NOT NULL,
  `jam_buka` time NOT NULL,
  `jam_tutup` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waktu_operasional`
--

INSERT INTO `waktu_operasional` (`id_UMKM`, `hari_buka`, `jam_buka`, `jam_tutup`) VALUES
(1, 'Setiap_hari', '09:00:00', '15:00:00'),
(2, 'Setiap_hari', '06:00:00', '13:00:00'),
(3, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu', '10:00:00', '17:30:00'),
(4, 'Setiap_hari', '09:30:00', '17:00:00'),
(5, 'Setiap_hari', '10:00:00', '16:30:00'),
(6, 'Setiap_hari', '06:00:00', '22:00:00'),
(7, 'Setiap_hari', '10:00:00', '17:00:00'),
(8, 'Setiap_hari', '12:00:00', '20:00:00'),
(9, 'Setiap_hari', '08:00:00', '12:00:00'),
(10, 'Setiap_hari', '12:00:00', '20:00:00'),
(11, 'Setiap_hari', '08:00:00', '12:00:00'),
(12, 'Setiap_hari', '06:00:00', '12:00:00'),
(13, 'Setiap_hari', '08:30:00', '18:00:00'),
(14, 'Senin,Selasa,Rabu,Kamis,Jumat,Sabtu', '07:00:00', '17:00:00'),
(15, 'Setiap_hari', '08:30:00', '17:00:00'),
(16, 'Setiap_hari', '08:00:00', '18:00:00'),
(17, 'Setiap_hari', '08:00:00', '18:00:00'),
(18, 'Setiap_hari', '09:00:00', '16:00:00'),
(19, 'Setiap_hari', '09:00:00', '16:30:00'),
(20, 'Setiap_hari', '16:00:00', '22:00:00'),
(21, 'Setiap_hari', '07:00:00', '14:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id_lokasi`),
  ADD KEY `id_UMKM` (`id_UMKM`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_UMKM` (`id_UMKM`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode`),
  ADD KEY `id_UMKM` (`id_UMKM`);

--
-- Indexes for table `mitra_online`
--
ALTER TABLE `mitra_online`
  ADD PRIMARY KEY (`id_mitra`),
  ADD KEY `id_UMKM` (`id_UMKM`);

--
-- Indexes for table `umkm`
--
ALTER TABLE `umkm`
  ADD PRIMARY KEY (`id_UMKM`);

--
-- Indexes for table `waktu_operasional`
--
ALTER TABLE `waktu_operasional`
  ADD PRIMARY KEY (`id_UMKM`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id_lokasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mitra_online`
--
ALTER TABLE `mitra_online`
  MODIFY `id_mitra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `umkm`
--
ALTER TABLE `umkm`
  MODIFY `id_UMKM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD CONSTRAINT `lokasi_ibfk_1` FOREIGN KEY (`id_UMKM`) REFERENCES `umkm` (`id_UMKM`);

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`id_UMKM`) REFERENCES `umkm` (`id_UMKM`);

--
-- Constraints for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD CONSTRAINT `metode_pembayaran_ibfk_1` FOREIGN KEY (`id_UMKM`) REFERENCES `umkm` (`id_UMKM`);

--
-- Constraints for table `mitra_online`
--
ALTER TABLE `mitra_online`
  ADD CONSTRAINT `mitra_online_ibfk_1` FOREIGN KEY (`id_UMKM`) REFERENCES `umkm` (`id_UMKM`);

--
-- Constraints for table `waktu_operasional`
--
ALTER TABLE `waktu_operasional`
  ADD CONSTRAINT `waktu_operasional_ibfk_1` FOREIGN KEY (`id_UMKM`) REFERENCES `umkm` (`id_UMKM`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
