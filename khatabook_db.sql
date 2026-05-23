-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 13, 2025 at 01:36 PM
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
-- Database: `khatabook_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cashbook`
--

CREATE TABLE `cashbook` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('in','out') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `entry_date` date NOT NULL,
  `entry_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashbook`
--

INSERT INTO `cashbook` (`id`, `user_id`, `type`, `amount`, `note`, `entry_date`, `entry_time`, `created_at`) VALUES
(2, 10, 'out', 4000.00, 'grocery', '2025-08-07', '16:20:11', '2025-08-08 14:20:11'),
(3, 10, 'in', 25000.00, 'sales amount', '2025-08-09', '09:32:28', '2025-08-09 07:32:28'),
(4, 10, 'out', 3400.00, 'staff allowence', '2025-08-09', '13:46:55', '2025-08-09 11:46:55');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `type` enum('customer','supplier') DEFAULT 'customer',
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `name`, `mobile`, `balance`, `type`, `status`, `created_at`, `updated_at`) VALUES
(15, 10, 'boomi', '8903000209', 5700.00, 'customer', 1, '2025-08-01 12:29:37', '2025-08-01 12:29:37'),
(16, 10, 'muthu', '7010425790', 20000.00, 'customer', 1, '2025-08-02 08:24:01', '2025-08-05 13:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `date`, `category`, `description`, `amount`, `created_at`) VALUES
(18, 10, '2025-07-19', 'Travel', 'supply', 4000.00, '2025-07-29 07:02:07');

-- --------------------------------------------------------

--
-- Table structure for table `manage_users`
--

CREATE TABLE `manage_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` enum('admin','subadmin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manage_users`
--

INSERT INTO `manage_users` (`id`, `username`, `email`, `role`, `created_at`) VALUES
(1, 'bharathi', 'muthu96bharathi@gmail.com', 'subadmin', '2025-07-26 07:42:38'),
(2, 'balaji', 'balaji@gmail.comm', 'subadmin', '2025-07-26 07:43:21'),
(4, 'mani', 'mani@gmail.com', 'admin', '2025-08-03 08:08:53');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `pending_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('paid','pending') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) DEFAULT NULL,
  `customer_type` enum('people','hotel') NOT NULL DEFAULT 'people'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `user_id`, `customer_name`, `amount`, `pending_amount`, `status`, `created_at`, `customer_id`, `customer_type`) VALUES
(10, 10, 'boomi', 90000.00, 0.00, 'paid', '2025-08-01 12:30:17', 15, 'people'),
(11, 10, 'muthu', 2000.00, 1000.00, 'pending', '2025-08-02 08:25:07', 16, 'people');

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `id` int(11) NOT NULL,
  `subadmin_id` int(11) DEFAULT NULL,
  `staff_name` varchar(100) NOT NULL,
  `staff_role` varchar(100) NOT NULL,
  `staff_mobile` varchar(20) NOT NULL,
  `staff_address` varchar(255) DEFAULT NULL,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`id`, `subadmin_id`, `staff_name`, `staff_role`, `staff_mobile`, `staff_address`, `joined_at`) VALUES
(3, 14, 'bharathi', 'cashier', '7856987640', 'khilsdalnfl', '2025-08-13 15:53:02');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `product_type` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `opening_balance` decimal(10,2) DEFAULT 0.00,
  `current_balance` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `mobile`, `product_type`, `address`, `opening_balance`, `current_balance`, `status`, `created_at`, `updated_at`) VALUES
(7, 'linga', '9030897539', 'Mutton', '35,madurai road, apk', 200000.00, 2000.00, 'active', '2025-08-06 13:49:48', '2025-08-06 14:23:21'),
(10, 'durai', '8903874765', 'Chicken', '24,anbunagar 10 th street, apk', 24000.00, 2000.00, 'active', '2025-08-06 14:10:04', '2025-08-06 14:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','subadmin') DEFAULT 'subadmin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(10, 'Suresh', 'suresh2000.kss@gmail.com', NULL, 'admin', '2025-07-26 10:17:04'),
(14, 'bharathi', 'muthu96bharathi@gmail.com', NULL, 'subadmin', '2025-08-13 05:21:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_actions`
--

CREATE TABLE `user_actions` (
  `id` int(11) NOT NULL,
  `admin_email` varchar(255) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `target_email` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_actions`
--

INSERT INTO `user_actions` (`id`, `admin_email`, `action`, `target_email`, `timestamp`) VALUES
(1, 'suresh2000.kss@gmail.com', 'edit', 'mutu70@gmail.com', '2025-08-03 07:56:43'),
(2, 'suresh2000.kss@gmail.com', 'edit', 'mani19@gmail.com', '2025-08-03 08:00:51'),
(3, 'suresh2000.kss@gmail.com', 'edit', 'mani10@gmail.com', '2025-08-03 08:05:52'),
(4, 'suresh2000.kss@gmail.com', 'delete', 'mani10@gmail.com', '2025-08-03 08:08:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cashbook`
--
ALTER TABLE `cashbook`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_expenses_user` (`user_id`);

--
-- Indexes for table `manage_users`
--
ALTER TABLE `manage_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer` (`customer_id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_staffs_subadmin` (`subadmin_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_actions`
--
ALTER TABLE `user_actions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cashbook`
--
ALTER TABLE `cashbook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `manage_users`
--
ALTER TABLE `manage_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_actions`
--
ALTER TABLE `user_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expenses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `staffs`
--
ALTER TABLE `staffs`
  ADD CONSTRAINT `fk_staffs_subadmin` FOREIGN KEY (`subadmin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
