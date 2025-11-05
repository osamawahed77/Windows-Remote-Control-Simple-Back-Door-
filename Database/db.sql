-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql-hexaauth.alwaysdata.net
-- Generation Time: Nov 05, 2025 at 12:45 PM
-- Server version: 10.11.14-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hexaauth_spy`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_seen` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_id`, `created_at`, `last_seen`) VALUES
(1, 'PC-001', '2025-11-05 11:58:36', '2025-11-05 12:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `commands`
--

CREATE TABLE `commands` (
  `id` int(11) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `command_text` text NOT NULL,
  `status` enum('pending','in_progress','done') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commands`
--

INSERT INTO `commands` (`id`, `client_id`, `command_text`, `status`, `created_at`) VALUES
(1, 'PC-001', '#getinfo', 'done', '2025-11-05 11:59:16'),
(2, 'PC-001', 'start chrome', 'done', '2025-11-05 12:04:48'),
(3, 'PC-001', '#openchrome', 'done', '2025-11-05 12:07:48'),
(4, 'PC-001', '#ban', 'done', '2025-11-05 12:08:08'),
(5, 'PC-001', '#openchrome', 'done', '2025-11-05 12:15:59'),
(6, 'PC-001', '#openchrome', 'done', '2025-11-05 12:16:28');

-- --------------------------------------------------------

--
-- Table structure for table `command_results`
--

CREATE TABLE `command_results` (
  `id` int(11) NOT NULL,
  `command_id` int(11) NOT NULL,
  `stdout` text DEFAULT NULL,
  `stderr` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `command_results`
--

INSERT INTO `command_results` (`id`, `command_id`, `stdout`, `stderr`, `created_at`) VALUES
(1, 1, 'Machine: SecHex-ZIKMMQE\nUser: osama\nOS: Microsoft Windows NT 6.2.9200.0', '', '2025-11-05 11:59:19'),
(2, 2, '', '', '2025-11-05 12:04:49'),
(3, 3, 'Chrome opened.', '', '2025-11-05 12:07:53'),
(4, 4, '', '\'#ban\' is not recognized as an internal or external command,\r\noperable program or batch file.\r\n', '2025-11-05 12:08:15'),
(5, 5, 'Chrome opened.', '', '2025-11-05 12:16:06'),
(6, 6, 'Chrome opened.', '', '2025-11-05 12:16:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_id` (`client_id`);

--
-- Indexes for table `commands`
--
ALTER TABLE `commands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `command_results`
--
ALTER TABLE `command_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `command_id` (`command_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commands`
--
ALTER TABLE `commands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `command_results`
--
ALTER TABLE `command_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commands`
--
ALTER TABLE `commands`
  ADD CONSTRAINT `commands_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;

--
-- Constraints for table `command_results`
--
ALTER TABLE `command_results`
  ADD CONSTRAINT `command_results_ibfk_1` FOREIGN KEY (`command_id`) REFERENCES `commands` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
