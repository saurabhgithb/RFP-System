-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2024 at 06:22 AM
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
-- Database: `rfp_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `status`) VALUES
(1, 'Groceries', 'Active'),
(2, 'Hardware', 'Active'),
(3, 'Office Furniture', 'Active'),
(4, 'Software', 'Inactive'),
(5, 'Furniture', 'Active'),
(6, 'Bottles', 'Active'),
(7, 'Fans', 'Inactive'),
(8, 'Pen Holder', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE `errors` (
  `error_id` int(11) NOT NULL,
  `error_code` int(11) NOT NULL,
  `error_message` varchar(80) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `errors`
--

INSERT INTO `errors` (`error_id`, `error_code`, `error_message`) VALUES
(10, 101, 'First name is required.'),
(11, 201, 'Last name is required.'),
(12, 301, 'Email is required.'),
(13, 302, 'Invalid email format.'),
(14, 303, 'Email already exist.'),
(15, 401, 'Password is required.'),
(16, 402, 'Password must be at least 6 characters.'),
(17, 403, 'Confirm password is required.'),
(18, 404, 'Passwords do not match.'),
(19, 501, 'Revenue is required.'),
(20, 502, 'Revenue must be a number.'),
(21, 601, 'Number of employees is required.'),
(22, 602, 'Number of employees must be a number.'),
(23, 701, 'GST number is required.'),
(24, 702, 'Invalid GST number format.'),
(25, 801, 'PAN number is required.'),
(26, 802, 'Invalid PAN number format.'),
(27, 901, 'Phone number is required.'),
(28, 902, 'Invalid phone number format.'),
(29, 1001, 'Categories are required.'),
(30, 603, 'Number of employees must be a non-negative whole number.'),
(31, 1101, 'Category Name is required.'),
(32, 1102, 'Category status is required'),
(33, 1201, 'Item name is required.'),
(34, 1301, 'Item Description is required.'),
(35, 1401, 'Quantity is required.'),
(36, 1402, 'Quantity must be a positive integer.'),
(37, 1501, 'Last Date is required.'),
(38, 1502, 'Last Date must be in future.'),
(39, 1601, 'Maximum Price is required.'),
(40, 1602, 'Maximum Price must be a number.'),
(41, 1603, 'Maximum Price must be greater than zero.'),
(42, 1701, 'Vendors is required.'),
(43, 1801, 'Minimum Price is required.'),
(44, 1802, 'Minimum Price must be a number.'),
(45, 1803, 'Minimum Price must be greater than zero.'),
(46, 1901, 'Vendor price is required.'),
(47, 1902, 'Vendor price must be a number.'),
(48, 1903, 'Vendor price must be greater than zero.'),
(49, 2001, 'Total Cost is required.'),
(50, 2002, 'Total Cost must be a number.'),
(51, 2003, 'Total Cost must be greater than zero.');

-- --------------------------------------------------------

--
-- Table structure for table `quote`
--

CREATE TABLE `quote` (
  `quote_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `rfp_id` int(11) NOT NULL,
  `vendor_price` decimal(10,2) NOT NULL,
  `item_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quote`
--

INSERT INTO `quote` (`quote_id`, `vendor_id`, `rfp_id`, `vendor_price`, `item_description`, `quantity`, `total_cost`) VALUES
(1, 2, 2, 120000.00, '10 packet potato', 10, 100000.00),
(2, 5, 6, 2121.00, '23432', 234, 234234.00);

-- --------------------------------------------------------

--
-- Table structure for table `rfp`
--

CREATE TABLE `rfp` (
  `rfp_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `last_date` date NOT NULL,
  `minimum_price` decimal(10,2) NOT NULL,
  `maximum_price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('Open','Closed') NOT NULL DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rfp`
--

INSERT INTO `rfp` (`rfp_id`, `item_name`, `item_description`, `quantity`, `last_date`, `minimum_price`, `maximum_price`, `category_id`, `status`) VALUES
(1, 'Laptops', 'Purchase of 100 Laptops', 100, '2024-05-30', 26000.00, 50000.00, 2, 'Open'),
(2, 'Potato', 'Purchase of 100 packet of potato', 100, '2024-05-26', 1200.00, 1600.00, 1, 'Closed'),
(3, 'Brinjal', '100 packet brinjal', 100, '2024-05-29', 1200.00, 2000.00, 1, 'Open'),
(4, 'Brinjal', '100 packet brinjal', 100, '2024-05-29', 1200.00, 2000.00, 1, 'Open'),
(5, 'adf', 'adsf', 23, '2024-05-29', 123213.00, 12312321.00, 6, 'Open'),
(6, 'fsfs', 'fsdfsdf', 232, '2024-05-29', 3432.00, 23432432.00, 6, 'Open'),
(7, 'sdsdsds', 'sdsdsd', 21, '2024-05-29', 12.00, 21.00, 6, 'Open');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` enum('Admin','Vendor') NOT NULL,
  `status` enum('Approved','Rejected') NOT NULL,
  `reset_token_hash` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_id`, `first_name`, `last_name`, `email`, `password`, `type`, `status`, `reset_token_hash`, `reset_token_expiry`, `date_added`) VALUES
(1, 'jonathan', 'Singh', 'saurav999bdfsr@gmail.com', '2ed5d4a15b8e2e9cc22a941e4d32b2cf', 'Vendor', 'Approved', NULL, NULL, '2024-05-22 12:27:08'),
(2, 'Claire', 'Jennings', 'xibi@mailinator.com', 'f3ed11bbdb94fd9ebdefbaf646ab94d3', 'Admin', 'Rejected', NULL, NULL, '2024-05-22 13:10:17'),
(3, 'Janna', 'Mayer', 'saurav9999bsr@gmail.com', 'supersuper', 'Admin', 'Rejected', NULL, NULL, '2024-05-22 14:59:55'),
(4, 'Chantale', 'Donaldson', 'saurav999dbsr@gmail.com', '2ed5d4a15b8e2e9cc22a941e4d32b2cf', 'Admin', 'Approved', NULL, NULL, '2024-05-22 15:08:44'),
(5, 'Elton', 'Rocha', 'watiw@mailinator.com', 'f3ed11bbdb94fd9ebdefbaf646ab94d3', 'Admin', 'Rejected', 'a8d01fd469dee03ed2c9cc8293113f266e7859e8f553a69b687a7807879903b3', '2024-05-22 13:40:45', '2024-05-22 16:40:45'),
(6, 'Zorita', 'Herrera', 'wofiwetahe@mailinator.com', 'f3ed11bbdb94fd9ebdefbaf646ab94d3', 'Admin', 'Rejected', '4976be0e2407d147c203fc49e64ce78cc0b326dc726b41e5cc9eb2a4c8b7ca3f', '2024-05-22 13:50:17', '2024-05-22 16:50:17'),
(7, 'Cherokee', 'Lara', 'saurabh.singh@velsoddf.com', '2ed5d4a15b8e2e9cc22a941e4d32b2cf', 'Admin', 'Approved', NULL, NULL, '2024-05-23 11:00:31'),
(8, 'Tara', 'Deleon', 'sauradbh.singh@velsof.com', '2ed5d4a15b8e2e9cc22a941e4d32b2cf', 'Vendor', 'Approved', NULL, NULL, '2024-05-23 12:17:46'),
(10, 'julie', 'Singh', 'markfshark002@gmail.com', '', 'Vendor', 'Approved', NULL, NULL, '2024-05-23 15:15:09'),
(11, 'Hasad', 'Gray', 'saurav999bsr@gmail.com', '2ed5d4a15b8e2e9cc22a941e4d32b2cf', 'Vendor', 'Approved', NULL, NULL, '2024-05-27 00:54:56'),
(12, 'Saurabh', 'Singh', 'saurabh.singh@velsof.com', '', 'Vendor', 'Approved', NULL, NULL, '2024-05-27 00:58:16'),
(13, 'Kimberly', 'Monroe', 'markshark002@gmail.com', 'fa7b2b3bb7db535a21c813d38667235c', 'Admin', 'Approved', NULL, NULL, '2024-05-27 01:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_category`
--

CREATE TABLE `vendor_category` (
  `vendor_id` int(11) NOT NULL COMMENT 'Composite primary key with vendor_id and category_id\r\n',
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_category`
--

INSERT INTO `vendor_category` (`vendor_id`, `category_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(2, 4),
(3, 1),
(3, 2),
(3, 5),
(3, 6),
(4, 2),
(4, 5),
(5, 6);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_details`
--

CREATE TABLE `vendor_details` (
  `vendor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `revenue` decimal(15,2) DEFAULT NULL,
  `no_of_employees` int(11) DEFAULT NULL,
  `gst_number` varchar(15) DEFAULT NULL,
  `pan_number` varchar(10) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_details`
--

INSERT INTO `vendor_details` (`vendor_id`, `user_id`, `revenue`, `no_of_employees`, `gst_number`, `pan_number`, `phone_number`) VALUES
(1, 1, 1.00, 55, '06BZAHM6385P6Z2', 'BNZAA2318J', '8382743927'),
(2, 8, 1977.00, 12, '06BZAHM6385P6Z2', 'BNZAA2318J', '9136812895'),
(3, 10, 120000.00, 55, '06BZAHM6385P6Z2', 'BNZAA2318J', '8382743927'),
(4, 11, 1989.00, 12, '22AAAAA0000A1Z5', 'ABCTY1234D', '9015548873'),
(5, 12, 100000.00, 34, '22AAAAA0000A1Z5', 'ABCTY1234D', '9136812895');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_rfp`
--

CREATE TABLE `vendor_rfp` (
  `vendor_rfp_id` int(11) NOT NULL,
  `rfp_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_rfp`
--

INSERT INTO `vendor_rfp` (`vendor_rfp_id`, `rfp_id`, `vendor_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2),
(5, 3, 3),
(6, 4, 3),
(7, 5, 3),
(8, 6, 5),
(9, 7, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`error_id`),
  ADD UNIQUE KEY `error_code` (`error_code`);

--
-- Indexes for table `quote`
--
ALTER TABLE `quote`
  ADD PRIMARY KEY (`quote_id`),
  ADD UNIQUE KEY `vendor_id` (`vendor_id`,`rfp_id`),
  ADD KEY `rfp_id` (`rfp_id`);

--
-- Indexes for table `rfp`
--
ALTER TABLE `rfp`
  ADD PRIMARY KEY (`rfp_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- Indexes for table `vendor_category`
--
ALTER TABLE `vendor_category`
  ADD PRIMARY KEY (`vendor_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `vendor_details`
--
ALTER TABLE `vendor_details`
  ADD PRIMARY KEY (`vendor_id`),
  ADD UNIQUE KEY `user_id_2` (`user_id`),
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `vendor_rfp`
--
ALTER TABLE `vendor_rfp`
  ADD PRIMARY KEY (`vendor_rfp_id`),
  ADD KEY `rfp_id` (`rfp_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `errors`
--
ALTER TABLE `errors`
  MODIFY `error_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `quote`
--
ALTER TABLE `quote`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rfp`
--
ALTER TABLE `rfp`
  MODIFY `rfp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `vendor_details`
--
ALTER TABLE `vendor_details`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendor_rfp`
--
ALTER TABLE `vendor_rfp`
  MODIFY `vendor_rfp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quote`
--
ALTER TABLE `quote`
  ADD CONSTRAINT `quote_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_details` (`vendor_id`),
  ADD CONSTRAINT `quote_ibfk_2` FOREIGN KEY (`rfp_id`) REFERENCES `rfp` (`rfp_id`);

--
-- Constraints for table `rfp`
--
ALTER TABLE `rfp`
  ADD CONSTRAINT `rfp_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `vendor_category`
--
ALTER TABLE `vendor_category`
  ADD CONSTRAINT `vendor_category_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_details` (`vendor_id`),
  ADD CONSTRAINT `vendor_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `vendor_details`
--
ALTER TABLE `vendor_details`
  ADD CONSTRAINT `vendor_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_details` (`user_id`);

--
-- Constraints for table `vendor_rfp`
--
ALTER TABLE `vendor_rfp`
  ADD CONSTRAINT `vendor_rfp_ibfk_1` FOREIGN KEY (`rfp_id`) REFERENCES `rfp` (`rfp_id`),
  ADD CONSTRAINT `vendor_rfp_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendor_details` (`vendor_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
