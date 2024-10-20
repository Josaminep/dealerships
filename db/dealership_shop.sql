-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2024 at 04:54 PM
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
-- Database: `dealership_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `activity_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log2`
--

CREATE TABLE `activity_log2` (
  `id2` int(11) NOT NULL,
  `activity_type2` varchar(255) NOT NULL,
  `details2` text NOT NULL,
  `activity_time2` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log3`
--

CREATE TABLE `activity_log3` (
  `id3` int(11) NOT NULL,
  `activity_type3` varchar(255) NOT NULL,
  `details3` text NOT NULL,
  `activity_time3` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log4`
--

CREATE TABLE `activity_log4` (
  `id4` int(11) NOT NULL,
  `activity_type4` varchar(255) NOT NULL,
  `details4` text NOT NULL,
  `activity_time4` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log5`
--

CREATE TABLE `activity_log5` (
  `id5` int(11) NOT NULL,
  `activity_type5` varchar(255) NOT NULL,
  `details5` text NOT NULL,
  `activity_time5` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_records`
--

CREATE TABLE `income_records` (
  `id` int(11) NOT NULL,
  `period` enum('daily','weekly','monthly') NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_records2`
--

CREATE TABLE `income_records2` (
  `id2` int(11) NOT NULL,
  `period2` enum('daily','weekly','monthly') NOT NULL,
  `amount2` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_records3`
--

CREATE TABLE `income_records3` (
  `id3` int(11) NOT NULL,
  `period3` enum('daily','weekly','monthly') NOT NULL,
  `amount3` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_records4`
--

CREATE TABLE `income_records4` (
  `id4` int(11) NOT NULL,
  `period4` enum('daily','weekly','monthly') NOT NULL,
  `amount4` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income_records5`
--

CREATE TABLE `income_records5` (
  `id5` int(11) NOT NULL,
  `period5` enum('daily','weekly','monthly') NOT NULL,
  `amount5` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `categories` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `price`, `quantity`, `brand`, `stock`, `categories`, `created_at`) VALUES
(16, 'kawasaki raider fi', 120000.00, 1, 'kawasaki', 100, 'motors', '2024-10-20 14:52:04'),
(17, 'evo helmet', 20000.00, 1, 'evo', 100, 'gear', '2024-10-20 14:52:04'),
(18, 'bag', 5000.00, 1, 'kawasaki bag', 100, 'accessories', '2024-10-20 14:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `products2`
--

CREATE TABLE `products2` (
  `id2` int(11) NOT NULL,
  `product_name2` varchar(255) NOT NULL,
  `price2` decimal(10,2) NOT NULL,
  `quantity2` int(11) NOT NULL,
  `brand2` varchar(255) DEFAULT NULL,
  `stock2` int(11) NOT NULL,
  `created_at2` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products3`
--

CREATE TABLE `products3` (
  `id3` int(11) NOT NULL,
  `product_name3` varchar(255) NOT NULL,
  `price3` decimal(10,2) NOT NULL,
  `quantity3` int(11) NOT NULL,
  `brand3` varchar(255) DEFAULT NULL,
  `stock3` int(11) NOT NULL,
  `created_at3` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products4`
--

CREATE TABLE `products4` (
  `id4` int(11) NOT NULL,
  `product_name4` varchar(255) NOT NULL,
  `price4` decimal(10,2) NOT NULL,
  `quantity4` int(11) NOT NULL,
  `brand4` varchar(255) DEFAULT NULL,
  `stock4` int(11) NOT NULL,
  `created_at4` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products5`
--

CREATE TABLE `products5` (
  `id5` int(11) NOT NULL,
  `product_name5` varchar(255) NOT NULL,
  `price5` decimal(10,2) NOT NULL,
  `quantity5` int(11) NOT NULL,
  `brand5` varchar(255) DEFAULT NULL,
  `stock5` int(11) NOT NULL,
  `created_at5` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `receipt_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts2`
--

CREATE TABLE `receipts2` (
  `id2` int(11) NOT NULL,
  `sale_id2` int(11) DEFAULT NULL,
  `receipt_details2` text DEFAULT NULL,
  `created_at2` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts3`
--

CREATE TABLE `receipts3` (
  `id3` int(11) NOT NULL,
  `sale_id3` int(11) DEFAULT NULL,
  `receipt_details3` text DEFAULT NULL,
  `created_at3` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts4`
--

CREATE TABLE `receipts4` (
  `id4` int(11) NOT NULL,
  `sale_id4` int(11) DEFAULT NULL,
  `receipt_details4` text DEFAULT NULL,
  `created_at4` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts5`
--

CREATE TABLE `receipts5` (
  `id5` int(11) NOT NULL,
  `sale_id5` int(11) DEFAULT NULL,
  `receipt_details5` text DEFAULT NULL,
  `created_at5` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity_sold` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales2`
--

CREATE TABLE `sales2` (
  `id2` int(11) NOT NULL,
  `product_id2` int(11) DEFAULT NULL,
  `quantity_sold2` int(11) DEFAULT NULL,
  `total_price2` decimal(10,2) DEFAULT NULL,
  `sale_date2` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales3`
--

CREATE TABLE `sales3` (
  `id3` int(11) NOT NULL,
  `product_id3` int(11) DEFAULT NULL,
  `quantity_sold3` int(11) DEFAULT NULL,
  `total_price3` decimal(10,2) DEFAULT NULL,
  `sale_date3` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales4`
--

CREATE TABLE `sales4` (
  `id4` int(11) NOT NULL,
  `product_id4` int(11) DEFAULT NULL,
  `quantity_sold4` int(11) DEFAULT NULL,
  `total_price4` decimal(10,2) DEFAULT NULL,
  `sale_date4` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales5`
--

CREATE TABLE `sales5` (
  `id5` int(11) NOT NULL,
  `product_id5` int(11) DEFAULT NULL,
  `quantity_sold5` int(11) DEFAULT NULL,
  `total_price5` decimal(10,2) DEFAULT NULL,
  `sale_date5` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `branch_id`, `created_at`) VALUES
(1, 'arvee@gmail.com', '$2y$10$aozjvdYkJ/YX/J1z8LXbo.WfF7D9lrKJcZJ7rJl/oQMgwT2Ln9Htm', 'admin', 0, '2024-10-20 05:08:22'),
(2, 'admin', '$2y$10$Ek1RWz4UNbAIe3uEQunhOOEZ/000oqm2USFhuos3BgnfK1NO4bFD2', 'admin', 0, '2024-10-20 06:15:33'),
(3, 'branch1', '$2y$10$CUWCX3bOZYNsTLllVyzMgOMKebsint35fxRl2MkDbOJKlD16KIYgK', 'staff', 1, '2024-10-20 06:20:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_log2`
--
ALTER TABLE `activity_log2`
  ADD PRIMARY KEY (`id2`);

--
-- Indexes for table `activity_log3`
--
ALTER TABLE `activity_log3`
  ADD PRIMARY KEY (`id3`);

--
-- Indexes for table `activity_log4`
--
ALTER TABLE `activity_log4`
  ADD PRIMARY KEY (`id4`);

--
-- Indexes for table `activity_log5`
--
ALTER TABLE `activity_log5`
  ADD PRIMARY KEY (`id5`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `income_records`
--
ALTER TABLE `income_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `income_records2`
--
ALTER TABLE `income_records2`
  ADD PRIMARY KEY (`id2`);

--
-- Indexes for table `income_records3`
--
ALTER TABLE `income_records3`
  ADD PRIMARY KEY (`id3`);

--
-- Indexes for table `income_records4`
--
ALTER TABLE `income_records4`
  ADD PRIMARY KEY (`id4`);

--
-- Indexes for table `income_records5`
--
ALTER TABLE `income_records5`
  ADD PRIMARY KEY (`id5`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products2`
--
ALTER TABLE `products2`
  ADD PRIMARY KEY (`id2`);

--
-- Indexes for table `products3`
--
ALTER TABLE `products3`
  ADD PRIMARY KEY (`id3`);

--
-- Indexes for table `products4`
--
ALTER TABLE `products4`
  ADD PRIMARY KEY (`id4`);

--
-- Indexes for table `products5`
--
ALTER TABLE `products5`
  ADD PRIMARY KEY (`id5`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `receipts2`
--
ALTER TABLE `receipts2`
  ADD PRIMARY KEY (`id2`),
  ADD KEY `sale_id2` (`sale_id2`);

--
-- Indexes for table `receipts3`
--
ALTER TABLE `receipts3`
  ADD PRIMARY KEY (`id3`),
  ADD KEY `sale_id3` (`sale_id3`);

--
-- Indexes for table `receipts4`
--
ALTER TABLE `receipts4`
  ADD PRIMARY KEY (`id4`),
  ADD KEY `sale_id4` (`sale_id4`);

--
-- Indexes for table `receipts5`
--
ALTER TABLE `receipts5`
  ADD PRIMARY KEY (`id5`),
  ADD KEY `sale_id5` (`sale_id5`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sales2`
--
ALTER TABLE `sales2`
  ADD PRIMARY KEY (`id2`),
  ADD KEY `product_id2` (`product_id2`);

--
-- Indexes for table `sales3`
--
ALTER TABLE `sales3`
  ADD PRIMARY KEY (`id3`),
  ADD KEY `product_id3` (`product_id3`);

--
-- Indexes for table `sales4`
--
ALTER TABLE `sales4`
  ADD PRIMARY KEY (`id4`),
  ADD KEY `product_id4` (`product_id4`);

--
-- Indexes for table `sales5`
--
ALTER TABLE `sales5`
  ADD PRIMARY KEY (`id5`),
  ADD KEY `product_id5` (`product_id5`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log2`
--
ALTER TABLE `activity_log2`
  MODIFY `id2` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log3`
--
ALTER TABLE `activity_log3`
  MODIFY `id3` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log4`
--
ALTER TABLE `activity_log4`
  MODIFY `id4` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log5`
--
ALTER TABLE `activity_log5`
  MODIFY `id5` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_records`
--
ALTER TABLE `income_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_records2`
--
ALTER TABLE `income_records2`
  MODIFY `id2` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_records3`
--
ALTER TABLE `income_records3`
  MODIFY `id3` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_records4`
--
ALTER TABLE `income_records4`
  MODIFY `id4` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_records5`
--
ALTER TABLE `income_records5`
  MODIFY `id5` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products2`
--
ALTER TABLE `products2`
  MODIFY `id2` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products3`
--
ALTER TABLE `products3`
  MODIFY `id3` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products4`
--
ALTER TABLE `products4`
  MODIFY `id4` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products5`
--
ALTER TABLE `products5`
  MODIFY `id5` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts2`
--
ALTER TABLE `receipts2`
  MODIFY `id2` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts3`
--
ALTER TABLE `receipts3`
  MODIFY `id3` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts4`
--
ALTER TABLE `receipts4`
  MODIFY `id4` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts5`
--
ALTER TABLE `receipts5`
  MODIFY `id5` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales2`
--
ALTER TABLE `sales2`
  MODIFY `id2` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales3`
--
ALTER TABLE `sales3`
  MODIFY `id3` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales4`
--
ALTER TABLE `sales4`
  MODIFY `id4` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales5`
--
ALTER TABLE `sales5`
  MODIFY `id5` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);

--
-- Constraints for table `receipts2`
--
ALTER TABLE `receipts2`
  ADD CONSTRAINT `receipts2_ibfk_1` FOREIGN KEY (`sale_id2`) REFERENCES `sales2` (`id2`);

--
-- Constraints for table `receipts3`
--
ALTER TABLE `receipts3`
  ADD CONSTRAINT `receipts3_ibfk_1` FOREIGN KEY (`sale_id3`) REFERENCES `sales3` (`id3`);

--
-- Constraints for table `receipts4`
--
ALTER TABLE `receipts4`
  ADD CONSTRAINT `receipts4_ibfk_1` FOREIGN KEY (`sale_id4`) REFERENCES `sales4` (`id4`);

--
-- Constraints for table `receipts5`
--
ALTER TABLE `receipts5`
  ADD CONSTRAINT `receipts5_ibfk_1` FOREIGN KEY (`sale_id5`) REFERENCES `sales5` (`id5`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `sales2`
--
ALTER TABLE `sales2`
  ADD CONSTRAINT `sales2_ibfk_1` FOREIGN KEY (`product_id2`) REFERENCES `products2` (`id2`);

--
-- Constraints for table `sales3`
--
ALTER TABLE `sales3`
  ADD CONSTRAINT `sales3_ibfk_1` FOREIGN KEY (`product_id3`) REFERENCES `products3` (`id3`);

--
-- Constraints for table `sales4`
--
ALTER TABLE `sales4`
  ADD CONSTRAINT `sales4_ibfk_1` FOREIGN KEY (`product_id4`) REFERENCES `products4` (`id4`);

--
-- Constraints for table `sales5`
--
ALTER TABLE `sales5`
  ADD CONSTRAINT `sales5_ibfk_1` FOREIGN KEY (`product_id5`) REFERENCES `products5` (`id5`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
