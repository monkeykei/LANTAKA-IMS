-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2024 at 05:30 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `I_ID` int(255) NOT NULL,
  `I_Product` text NOT NULL,
  `I_SN` int(11) NOT NULL,
  `I_Quantity` varchar(255) NOT NULL,
  `I_Unit` text NOT NULL,
  `I_Location` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`I_ID`, `I_Product`, `I_SN`, `I_Quantity`, `I_Unit`, `I_Location`) VALUES
(1, 'Pillow Case', 0, '274', 'pieces', 'Sunken'),
(2, 'Old Assorted Foam Cover', 0, '164', '', 'Sunken'),
(3, 'Round Table Cloth Big Red', 0, '6', '', 'Sunken'),
(4, 'Round Table Cloth Cream', 0, '3', '', 'Sunken'),
(5, 'Table Runner Yellow', 0, '19', '', 'Sunken'),
(6, 'Table Runner Brown', 0, '37', '', 'Sunken'),
(7, 'Table Runner Light Blue', 0, '12', '', 'Sunken'),
(8, 'Table Runner Silver', 0, '39', '', 'Sunken'),
(9, 'Table Runner Blue', 0, '8', '', 'Sunken'),
(10, 'Table Runner Violet', 0, '24', '', 'Sunken'),
(11, 'Old Woolen', 0, '85', '', 'Sunken'),
(12, 'Yellow Woolen', 0, '50', '', 'Sunken'),
(13, 'Bed Sheet', 0, '346', '', 'Sunken'),
(14, 'Towel', 0, '175', '', 'Sunken'),
(15, 'Bath Mat', 0, '20', '', 'Sunken'),
(16, 'Bath Rug', 0, '30', '', 'Sunken'),
(17, 'Pillow Case', 0, '164', '', 'Sunken'),
(18, 'Chair Cover REd', 0, '111', '', 'Sunken'),
(19, 'Chair Cover Brown', 0, '144', '', 'Sunken');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(27, 'keileb', '$2y$10$hXDprr95u2gVCUNqmiFQyus1j6wQ6IbiKL7fAtLK2HZJHLiQzFZTy', 'keileb@gmail.com'),
(28, 'asd', '$2y$10$yVIazLEOntInBmqZ8LBrQuGSuLPtKbxveu8FJDWv8DHnDfBjD6bra', 'asd@gmail.com'),
(29, 'qwerty', '$2y$10$SW/s7330Be7Y/1Uwo8Pv0.92WxEZtPnYxr2qQy6NkrqreW7KP2Gw6', 'qwerty@gmail.com'),
(30, 'keileb1', '$2y$10$aAMNlsX18AlKT.K6K56NDuJguBpLEYou3OXgrTC2Hnncr/IyABHMi', 'keileb1@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`I_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `I_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
