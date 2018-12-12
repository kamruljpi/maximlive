-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 12, 2018 at 08:52 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.1.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `maxim`
--

-- --------------------------------------------------------

--
-- Table structure for table `mxp_draft`
--

CREATE TABLE `mxp_draft` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `others_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oos_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_pi_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size_width_height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderNo` int(11) DEFAULT NULL,
  `poCatNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_user_id` int(11) NOT NULL DEFAULT '0',
  `deleted_date_at` date DEFAULT NULL,
  `last_action_at` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderDate` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipmentDate` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_draft`
--

INSERT INTO `mxp_draft` (`id`, `user_id`, `vendor_id`, `booking_order_id`, `erp_code`, `item_code`, `item_size`, `item_description`, `item_quantity`, `item_price`, `material`, `gmts_color`, `others_color`, `season_code`, `oos_number`, `sku`, `style`, `is_type`, `is_pi_type`, `item_size_width_height`, `orderNo`, `poCatNo`, `is_deleted`, `deleted_user_id`, `deleted_date_at`, `last_action_at`, `orderDate`, `shipmentDate`, `created_at`, `updated_at`) VALUES
(1, 49, 122, 'BK-11122018-RFL-0063', 'N/A', '8SHPGDYE2', 'XXS', NULL, '10000', NULL, NULL, NULL, NULL, 'AW18', 'asas', 'sdsad', NULL, 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-12', '2018-12-11 05:40:08', '2018-12-11 05:40:08'),
(2, 49, 122, 'BK-11122018-RFL-0064', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', NULL, NULL, 'a', NULL, 'AW18', 'asas', 'sdsad', NULL, 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-12', '2018-12-11 05:44:31', '2018-12-11 05:44:31'),
(3, 49, 122, 'BK-11122018-RFL-0065', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', NULL, 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-14', '2018-12-11 05:46:00', '2018-12-11 05:46:00'),
(4, 49, 122, 'BK-11122018-RFL-0066', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-14', '2018-12-11 05:46:48', '2018-12-11 05:46:48'),
(5, 49, 122, 'BK-11122018-RFL-0067', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-14', '2018-12-11 05:48:35', '2018-12-11 05:48:35'),
(6, 49, 227, 'BK-11122018-SGTL-0068', 'N/A', '8SHPGDYE2', '4T', 'Hang Tag', '10000', 'N/A', NULL, NULL, NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-13', '2018-12-11 05:48:41', '2018-12-11 05:48:41'),
(7, 49, 227, 'BK-11122018-SGTL-0068', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', 'N/A', NULL, NULL, NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-13', '2018-12-11 05:48:41', '2018-12-11 05:48:41'),
(8, 49, 122, 'BK-11122018-RFL-0069', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '0000-00-00', '2018-12-14', '2018-12-11 05:49:12', '2018-12-11 05:49:12'),
(9, 49, 122, 'BK-11122018-RFL-0070', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, '0000-00-00', '11-12-2018', '2018-12-14', '2018-12-11 05:50:14', '2018-12-11 05:50:14'),
(10, 49, 122, 'BK-11122018-RFL-0071', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, '0', 0, 0, NULL, 'create', '11-12-2018', '2018-12-14', '2018-12-11 05:51:09', '2018-12-11 05:51:09'),
(11, 49, 122, 'BK-11122018-RFL-0072', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, 'asa', 0, 0, NULL, 'create', '11-12-2018', '2018-12-14', '2018-12-11 05:53:36', '2018-12-11 05:53:36'),
(12, 49, 122, 'BK-11122018-RFL-0073', 'N/A', '8SHPGDYE2', 'XXS', 'Hang Tag', '10000', '2', NULL, 'a', NULL, 'AW18', 'asdsad', 'sdsad', 'asdsad', 'general', 'unstage', '57.15-20.32', NULL, 'asa', 0, 0, NULL, 'create', '11-12-2018', '2018-12-14', '2018-12-11 06:03:58', '2018-12-11 06:03:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mxp_draft`
--
ALTER TABLE `mxp_draft`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mxp_draft`
--
ALTER TABLE `mxp_draft`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
