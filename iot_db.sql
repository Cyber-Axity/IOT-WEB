-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2025 at 06:20 AM
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
-- Database: `iot_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `acc`
--

CREATE TABLE `acc` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `otp` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acc`
--

INSERT INTO `acc` (`id`, `user_name`, `email`, `password`, `otp`) VALUES
(2, 'admin', 'specialization10@gmail.com', '$2y$10$DG0tzyRCD3YKsEB.9759U.nuZcXGnphEi8W1zgXKZuVBWL5xrvkuG', 227611),
(3, 'kent', 'johnkent.demonteverde25@gmail.com', '$2y$10$txJFhyDUiYui5OaiLQ/1n.P5IgPBklmGTxr7yH4ElDWqOPSt7UHSG', 499228);

-- --------------------------------------------------------

--
-- Table structure for table `point_transactions`
--

CREATE TABLE `point_transactions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `points_added` decimal(10,2) NOT NULL DEFAULT 0.00,
  `source` varchar(50) DEFAULT 'RFID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `balance_after` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `point_transactions`
--

INSERT INTO `point_transactions` (`id`, `student_id`, `points_added`, `source`, `created_at`, `balance_after`) VALUES
(1, 13, 0.50, 'RFID', '2025-09-16 06:17:22', NULL),
(3, 10, 0.50, 'RFID', '2025-09-16 07:16:31', NULL),
(4, 11, 0.50, 'RFID', '2025-09-16 07:16:51', NULL),
(5, 11, 0.50, 'RFID', '2025-09-16 07:17:05', NULL),
(7, 10, 0.50, 'RFID', '2025-09-16 07:37:40', NULL),
(8, 11, 0.50, 'RFID', '2025-09-16 07:39:14', NULL),
(10, 11, 0.50, 'RFID', '2025-09-16 07:39:48', NULL),
(11, 10, 0.50, 'RFID', '2025-09-16 07:40:07', NULL),
(12, 10, 0.50, 'RFID', '2025-09-16 07:40:17', NULL),
(13, 10, 0.50, 'RFID', '2025-09-16 07:40:31', NULL),
(14, 10, 0.50, 'RFID', '2025-09-16 07:40:57', NULL),
(15, 10, 0.50, 'RFID', '2025-09-16 07:46:29', NULL),
(16, 10, 0.25, 'RFID', '2025-09-16 07:56:15', NULL),
(17, 10, 0.50, 'RFID', '2025-09-16 07:57:24', NULL),
(18, 10, 0.50, 'RFID', '2025-09-16 07:57:36', NULL),
(19, 10, 0.50, 'RFID', '2025-09-16 07:57:46', NULL),
(20, 11, 0.25, 'RFID', '2025-09-16 07:58:02', NULL),
(21, 10, 0.50, 'RFID', '2025-09-16 07:59:22', NULL),
(22, 10, -5.75, 'REDEEM', '2025-09-16 08:03:33', 14.00),
(23, 10, -2.00, 'REDEEM', '2025-09-16 08:15:59', 12.00),
(24, 10, -1.00, 'REDEEM', '2025-09-16 08:22:37', 11.00),
(25, 10, 0.75, 'RFID', '2025-09-16 09:39:45', 11.75),
(26, 10, -3.75, 'REDEEM', '2025-09-16 09:40:11', 8.00),
(27, 10, 0.75, 'RFID', '2025-09-16 09:40:27', 8.75),
(28, 10, 0.75, 'RFID', '2025-09-16 09:45:43', 9.50),
(29, 10, 0.75, 'RFID', '2025-09-16 10:01:18', 10.25),
(30, 10, 0.75, 'RFID', '2025-09-16 10:01:47', 11.00),
(31, 10, 0.75, 'RFID', '2025-09-16 10:04:22', 11.75),
(32, 10, 0.75, 'RFID', '2025-09-16 10:05:20', 12.50),
(33, 10, 0.75, 'RFID', '2025-09-16 10:05:49', 13.25),
(34, 10, 0.75, 'RFID', '2025-09-16 10:06:58', 14.00),
(35, 10, 0.75, 'RFID', '2025-09-16 10:07:39', 14.75),
(36, 10, 0.75, 'RFID', '2025-09-16 10:08:29', 15.50),
(37, 10, 0.75, 'RFID', '2025-09-16 10:09:20', 16.25),
(38, 10, 0.75, 'RFID', '2025-09-16 10:09:28', 17.00),
(39, 10, 0.75, 'RFID', '2025-09-16 10:09:39', 17.75),
(40, 10, 0.75, 'RFID', '2025-09-16 10:10:17', 18.50),
(41, 10, 0.75, 'RFID', '2025-09-16 10:10:27', 19.25),
(42, 10, -10.00, 'REDEEM', '2025-09-16 10:10:37', 9.25),
(43, 10, 0.75, 'RFID', '2025-09-16 10:10:59', 10.00),
(44, 10, 0.75, 'RFID', '2025-09-16 10:11:46', 10.75),
(45, 10, 0.75, 'RFID', '2025-09-17 15:10:03', 11.50),
(46, 10, 0.75, 'RFID', '2025-09-17 15:11:32', 12.25),
(47, 10, 0.75, 'RFID', '2025-09-17 15:12:18', 13.00),
(48, 10, 0.75, 'RFID', '2025-09-17 15:19:43', 13.75),
(49, 10, 0.75, 'RFID', '2025-09-17 15:35:21', 14.50),
(50, 10, -4.50, 'REDEEM', '2025-09-17 18:13:38', 10.00),
(51, 11, -0.25, 'REDEEM', '2025-09-18 00:20:10', 8.00);

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `student_id` varchar(50) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `cellphone` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `email`, `user_name`, `photo`, `student_id`, `fullname`, `cellphone`, `department`, `position`) VALUES
(1, 'johnkent.demonteverde25@gmail.com', 'kent', '1757235730_John Kent Demonteverde (8.5 x 11 in) (2).png', '1-2200053', 'Demonteverde, John Kent', '09305115924', 'BSIT', 'Vice President'),
(2, 'specialization10@gmail.com', 'admin', NULL, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_tbl`
--

CREATE TABLE `student_tbl` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `card_no` varchar(100) NOT NULL,
  `uid` varchar(64) DEFAULT NULL,
  `points` float DEFAULT 0,
  `last_activity` timestamp NULL DEFAULT NULL COMMENT 'Last time student earned points via RFID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'When student was first registered',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'When student record was last updated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_tbl`
--

INSERT INTO `student_tbl` (`id`, `student_id`, `first_name`, `middle_name`, `last_name`, `course`, `year_level`, `card_no`, `uid`, `points`, `last_activity`, `created_at`, `updated_at`) VALUES
(3, '1-220053', 'John Kent', 'Dizon', 'Demonteverde', 'Bachelor of Science in Computer Engineering', '4th Year', '5626615F', NULL, 3.5, '2025-09-16 06:05:07', '2025-09-16 04:32:01', '2025-09-16 07:35:47'),
(6, '1-220054', 'Monkey', 'Dragon', 'Luffy', 'Bachelor of Science in Accountancy', '2nd Year', '46A5585F', NULL, 0, NULL, '2025-09-16 04:32:01', '2025-09-16 04:32:02'),
(10, '1-220550', 'Alyssa', 'Alzaga', 'Reyes', 'Bachelor of Science in Information Technology', '4th Year', '9358D2D9', NULL, 10, '2025-09-17 15:35:21', '2025-09-16 04:32:01', '2025-09-17 18:13:38'),
(11, '1-220363', 'Julienne', 'Ansay', 'Delmo', 'Bachelor of Science in Accountancy', '3rd Year', 'A611094E', NULL, 8, '2025-09-16 07:58:02', '2025-09-16 04:32:01', '2025-09-18 00:20:10'),
(13, '1-220323', 'Kristine', 'Pagampang', 'Rey', 'Bachelor of Science in Criminology', '2nd Year', '5684955F', NULL, 0.5, '2025-09-16 06:17:22', '2025-09-16 06:16:49', '2025-09-16 06:17:22'),
(14, '1-220443', 'Andrew', 'Ragot', 'Mindoro', 'Bachelor of Science in Information Technology', '4th Year', '7657EF70', NULL, 0, NULL, '2025-09-18 00:34:45', '2025-09-18 00:34:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acc`
--
ALTER TABLE `acc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `point_transactions`
--
ALTER TABLE `point_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_profile_acc` (`email`);

--
-- Indexes for table `student_tbl`
--
ALTER TABLE `student_tbl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `card_no` (`card_no`),
  ADD KEY `idx_student_uid` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acc`
--
ALTER TABLE `acc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `point_transactions`
--
ALTER TABLE `point_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_tbl`
--
ALTER TABLE `student_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `fk_profile_acc` FOREIGN KEY (`email`) REFERENCES `acc` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
