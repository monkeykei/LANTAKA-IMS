-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2024 at 07:18 PM
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
  `I_SN` int(11) DEFAULT NULL,
  `I_Quantity` varchar(255) NOT NULL,
  `I_Unit` text NOT NULL,
  `I_Location` varchar(50) NOT NULL,
  `E_Storage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`I_ID`, `I_Product`, `I_SN`, `I_Quantity`, `I_Unit`, `I_Location`, `E_Storage`) VALUES
(1, 'Pillow Case', 0, '274', 'pieces', 'Sunken', 0),
(2, 'Old Assorted Foam Cover', 0, '164', 'pieces', 'Sunken', 0),
(3, 'Round Table Cloth Big Red', 0, '6', '', 'Sunken', 0),
(4, 'Round Table Cloth Cream', 0, '3', '', 'Sunken', 0),
(5, 'Table Runner Yellow', 0, '19', '', 'Sunken', 0),
(6, 'Table Runner Brown', 0, '37', '', 'Sunken', 0),
(7, 'Table Runner Light Blue', 0, '12', '', 'Sunken', 0),
(8, 'Table Runner Silver', 0, '39', '', 'Sunken', 0),
(9, 'Table Runner Blue', 0, '8', '', 'Sunken', 0),
(10, 'Table Runner Violet', 0, '24', '', 'Sunken', 0),
(11, 'Old Woolen', 0, '85', '', 'Sunken', 0),
(12, 'Yellow Woolen', 0, '50', '', 'Sunken', 0),
(13, 'Bed Sheet', 0, '346', '', 'Sunken', 0),
(14, 'Towel', 0, '175', '', 'Sunken', 0),
(15, 'Bath Mat', 0, '20', '', 'Sunken', 0),
(16, 'Bath Rug', 0, '30', '', 'Sunken', 0),
(17, 'Pillow Case', 0, '164', '', 'Sunken', 0),
(18, 'Chair Cover REd', 0, '111', '', 'Sunken', 0),
(19, 'Chair Cover Brown', 0, '144', '', 'Sunken', 0),
(21, 'pillows', 0, '200', 'pieces', 'Sunken', 0);

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `UN_ID` int(11) NOT NULL,
  `UN_Name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`UN_ID`, `UN_Name`) VALUES
(1, 'Pieces'),
(2, 'Kg'),
(3, 'Pieces'),
(4, 'Kg'),
(5, 'Litre'),
(6, 'Meter'),
(7, 'Feet'),
(8, 'Inches');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `U_ID` int(11) NOT NULL,
  `U_Username` varchar(50) NOT NULL,
  `U_Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`U_ID`, `U_Username`, `U_Password`) VALUES
(27, 'keileb', '$2y$10$hXDprr95u2gVCUNqmiFQyus1j6wQ6IbiKL7fAtLK2HZJHLiQzFZTy'),
(28, 'asd', '$2y$10$yVIazLEOntInBmqZ8LBrQuGSuLPtKbxveu8FJDWv8DHnDfBjD6bra'),
(29, 'qwerty', '$2y$10$SW/s7330Be7Y/1Uwo8Pv0.92WxEZtPnYxr2qQy6NkrqreW7KP2Gw6'),
(30, 'keileb1', '$2y$10$aAMNlsX18AlKT.K6K56NDuJguBpLEYou3OXgrTC2Hnncr/IyABHMi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`I_ID`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`UN_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`U_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `I_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `UN_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `U_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
