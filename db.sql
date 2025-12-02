-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 02, 2025 at 03:58 PM
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
-- Database: `hackathon_imphnen`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `ID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` enum('YES','NO') NOT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `session_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`ID`, `created_at`, `username`, `password`, `isAdmin`, `session_token`, `session_expiry`) VALUES
(1, '2025-12-01 05:16:46', 'hylmi', '$2a$12$aMkBUAVjtwfI46Us13Jx0.cNcJVa5ri0MaUFWt97U5fi4yupNBlS2', 'YES', '628986f24c53645b4e914e0bc5f7e48e6d0b7410f4c27f3a5bfbd23169c51f31', '2025-12-03 10:45:44'),
(2, '2025-12-01 14:49:20', 'test', '$2y$12$Sg50YvG6VnAeNp2dmgoujeP3iOAeqHfSyH9b2GzVsF.kALL81egK2', 'YES', NULL, NULL),
(3, '2025-12-02 01:17:20', 'akuruoaklah', '$2y$12$0/zkk9Vs1OZaQWoeLLFhIuP4aAULm1wyNHfKu7x3j/fmlYyXQSd.K', 'YES', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `ID` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(150) NOT NULL,
  `stock` int(50) NOT NULL,
  `price` double(20,2) NOT NULL,
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`ID`, `created_at`, `name`, `stock`, `price`, `created_by`) VALUES
(1, '2025-12-01 05:16:01', 'Nasi goreng', 5, 10000.00, 'hylmi'),
(6, '2025-12-02 00:57:22', 'Mie ayam', 10, 12000.00, 'hylmi'),
(7, '2025-12-02 01:14:54', 'Nasi goreng kampung', 10, 15000.00, 'hylmi'),
(10, '2025-12-02 02:58:06', 'Ayam Kentucky', 10, 7000.00, 'hylmi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
