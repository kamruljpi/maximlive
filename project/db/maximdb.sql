-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 08, 2018 at 02:18 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.1.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `maximdb`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBookinAndBuyerDeatils` (IN `booking_order_id` VARCHAR(247))  NO SQL
SELECT mb.season_code,mb.oos_number,mb.style,mb.is_type,mb.id,mb.sku,mb.erp_code,mb.item_code,mb.item_price,mb.item_description, mb.orderDate,mb.orderNo,mb.shipmentDate,mb.poCatNo,mb.others_color ,GROUP_CONCAT(mb.item_size) as itemSize,GROUP_CONCAT(mb.gmts_color) as gmtsColor,GROUP_CONCAT(mb.item_quantity) as quantity,mbd.buyer_name,mbd.Company_name,mbd.C_sort_name,mbd.address_part1_invoice,mbd.address_part2_invoice,mbd.attention_invoice,mbd.mobile_invoice,mbd.telephone_invoice,mbd.fax_invoice,mbd.address_part1_delivery,mbd.address_part2_delivery,mbd.attention_delivery,mbd.mobile_delivery,mbd.telephone_delivery,mbd.fax_delivery,mbd.is_complete,mbd.booking_status,mbd.shipmentDate,mbd.booking_order_id from mxp_booking mb INNER JOIN mxp_bookingbuyer_details mbd on(mbd.booking_order_id = mb.booking_order_id) WHERE mb.booking_order_id = booking_order_id GROUP BY mb.item_code ORDER BY id ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductSizeQuantity` (IN `product_code` VARCHAR(247), IN `order_id` VARCHAR(247))  select mo.item_code,mo.oss,mo.style, mp.unit_price, mp.weight_qty, mp.erp_code, GROUP_CONCAT(mo.item_size) as item_size, GROUP_CONCAT(mo.quantity) as quantity, mo.order_id from mxp_order mo INNER JOIN mxp_product mp on(mo.item_code = mp.product_code) where mo.item_code = product_code AND mo.order_id = order_id GROUP by mo.item_code$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductSizeQuantitybyPrice` (IN `booking_order_id` VARCHAR(100))  NO SQL
SELECT mb.erp_code,mb.item_code,mb.item_price,mb.orderDate,mb.orderNo,mb.shipmentDate,mb.poCatNo,mb.others_color ,GROUP_CONCAT(mb.item_size) as itemSize,GROUP_CONCAT(mb.gmts_color) as gmtsColor,GROUP_CONCAT(mb.item_quantity) as quantity, mbd.* from mxp_booking mb INNER JOIN mxp_bookingBuyer_details mbd on(mbd.booking_order_id = mb.booking_order_id) INNER JOIN mxp_product mp on( mb.item_code = mp.product_code) WHERE mb.booking_order_id = booking_order_id GROUP BY mb.item_code$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getProductSizeQuantityWithConcat` (IN `product_code` VARCHAR(247))  NO SQL
SELECT mp.erp_code,mp.product_id,mp.unit_price,mp.product_name,mp.others_color,mp.product_description ,GROUP_CONCAT(mps.product_size order by product_size) as size,GROUP_CONCAT(mgs.color_name) as color   FROM mxp_product mp 
LEFT JOIN mxp_productsize mps ON (mps.product_code = mp.product_code)
LEFT JOIN mxp_gmts_color mgs ON (mgs.item_code = mps.product_code)
WHERE mp.product_code = product_code and mp.status = 1 GROUP BY mps.product_code, mgs.item_code$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_all_role_list_by_group_id` (IN `grp_id` INT(11))  SELECT GROUP_CONCAT(DISTINCT(c.name)) as c_name,r.* FROM mxp_role r join mxp_companies c on(c.id=r.company_id)
where c.group_id=grp_id GROUP BY r.cm_group_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_all_translation` ()  SELECT tr.*,tk.translation_key FROM mxp_translation_keys tk INNER JOIN mxp_translations tr ON(tr.translation_key_id=tk.translation_key_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_all_translation_with_limit` (IN `startedAt` INT(11), IN `limits` INT(11))  SELECT tr.*,tk.translation_key, ml.lan_name FROM mxp_translation_keys tk INNER JOIN
 mxp_translations tr ON(tr.translation_key_id=tk.translation_key_id) 
 INNER JOIN mxp_languages ml ON(ml.lan_code=tr.lan_code)order by tk.translation_key_id desc limit startedAt,limits$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_child_menu_list` (IN `p_parent_menu_id` INT(11), IN `role_id` INT(11), IN `comp_id` INT(11))  if(comp_id !='') then
SELECT m.* FROM mxp_user_role_menu rm inner JOIN mxp_menu m ON(m.menu_id=rm.menu_id) WHERE rm.role_id=role_id AND rm.company_id=comp_id AND m.parent_id=p_parent_menu_id order by m.order_id ASC;
else
SELECT m.* FROM mxp_user_role_menu rm inner JOIN mxp_menu m ON(m.menu_id=rm.menu_id) WHERE rm.role_id=role_id AND m.parent_id=p_parent_menu_id order by m.order_id ASC;
end if$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_companies_by_group_id` (IN `grp_id` INT(11))  select * from mxp_companies where group_id=grp_id and is_active = 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_permission` (IN `role_id` INT(11), IN `route` VARCHAR(120), IN `comp_id` INT(11))  if(comp_id !='')then
SELECT COUNT(*) as cnt FROM mxp_user_role_menu rm inner JOIN mxp_menu m ON(m.menu_id=rm.menu_id) WHERE m.route_name=route AND rm.role_id=role_id AND rm.company_id=comp_id;
else
SELECT COUNT(*) as cnt FROM mxp_user_role_menu rm inner JOIN mxp_menu m ON(m.menu_id=rm.menu_id) WHERE m.route_name=route AND rm.role_id=role_id ;
end if$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_roles_by_company_id` (IN `cmpny_id` INT(11), IN `cm_grp_id` INT(11))  SELECT rl.name as roleName, cm.name as companyName, cm.id as company_id, rl.cm_group_id, rl.is_active FROM mxp_role rl INNER JOIN mxp_companies cm ON(rl.company_id=cm.id) where cm.group_id = `cmpny_id` and rl.cm_group_id = `cm_grp_id`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_searched_trans_key` (IN `_key` VARCHAR(255))  SELECT distinct(tk.translation_key),tk.translation_key_id, tk.is_active FROM mxp_translation_keys tk
 inner join mxp_translations tr on(tk.translation_key_id = tr.translation_key_id)
 WHERE tk.translation_key LIKE CONCAT('%', _key , '%')$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_translations_by_key_id` (IN `key_id` INT)  select translation_id, translation, lan_code from mxp_translations
 where translation_key_id= `key_id` and is_active = 1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_translations_by_locale` (IN `locale_code` VARCHAR(255))  SELECT tr.translation,tk.translation_key FROM mxp_translation_keys tk INNER JOIN mxp_translations tr ON(tr.translation_key_id=tk.translation_key_id)
WHERE tr.lan_code=locale_code$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_translation_by_key_id` (IN `tr_key_id` INT(11))  SELECT tr.translation,tk.translation_key,tk.translation_key_id,tk.is_active,ln.lan_name FROM mxp_translation_keys tk INNER JOIN mxp_translations tr ON(tr.translation_key_id=tk.translation_key_id)
INNER JOIN mxp_languages ln ON(ln.lan_code=tr.lan_code)
WHERE tr.translation_key_id=tr_key_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_menu_by_role` (IN `role_id` INT(11), IN `comp_id` INT(11))  if(comp_id !='') then
SELECT m.* FROM mxp_user_role_menu rm inner JOIN mxp_menu m ON(m.menu_id=rm.menu_id) WHERE rm.role_id=role_id AND rm.company_id=comp_id;
else
SELECT m.* FROM mxp_user_role_menu rm inner JOIN mxp_menu m ON(m.menu_id=rm.menu_id) WHERE rm.role_id=role_id;
end if$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_files`
--

CREATE TABLE `booking_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_buyer_id` int(11) DEFAULT NULL,
  `file_name_original` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name_server` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ext` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2018_01_11_075242_create_languages_table', 1),
(3, '2018_01_12_081050_create_role_table', 1),
(4, '2018_01_12_084141_create_menu_table', 1),
(5, '2018_01_12_122539_add_column_to_mxp_role', 2),
(6, '2018_01_13_100521_create_mxp_users_table', 2),
(7, '2018_01_15_064427_create_mxp_translation_keys', 3),
(8, '2018_01_15_064518_create_mxp_translations', 3),
(9, '2018_01_15_073009_create_mxp_user_role_menu', 4),
(10, '2018_01_15_081551_update_language_table', 5),
(11, '2018_01_15_130417_create_mxp_trans_keys_table', 6),
(12, '2018_01_15_081806_create_mxp_users_table', 7),
(13, '2018_01_15_095153_add_type_column_after_last_name_of_mxp_users', 7),
(14, '2018_01_16_055331_create_mxp_translation_keys_table', 8),
(15, '2018_01_16_060235_create_mxp_translation_keys_table', 9),
(16, '2018_01_16_064618_update_mxp_translation_keys_table', 10),
(17, '2018_01_22_104053_update_mxp_users_table', 11),
(18, '2018_01_26_060729_add_companyId_to_roles_and_role_menus', 11),
(19, '2018_01_25_130557_create_companies_table', 12),
(20, '2018_01_26_054823_drop_company_column_from_mxp_users_table', 12),
(21, '2018_01_26_071103_add_column_to_mxp_user_table', 13),
(22, '2018_01_26_075012_create_store_pro_get_company_by_group_id', 14),
(24, '2018_01_27_130037_create_store_pro_get_roles_by_company_id', 16),
(25, '2018_01_30_081529_update_mxp_role', 17),
(26, '2018_01_30_093232_create_store_pro_get_all_companies_of_same_name_by_group_id', 17),
(27, '2018_01_30_105605_update_mxp_translations', 17),
(46, '2018_02_06_100944_create_mxp_taxvats_table', 18),
(47, '2018_02_06_103251_create_mxp_taxvat_cals_table', 18),
(48, '2018_04_04_053741_create_mxp_accounts_heads_table', 19),
(49, '2018_04_05_093858_create_store_procedure_get_all_acc_class', 20),
(50, '2018_04_05_123858_create_mxp_acc_head_sub_classes_table', 20),
(51, '2018_04_06_060320_create_store_pro_get_all_sub_class_name', 20),
(52, '2018_04_06_070031_create_store_pro_get_all_chart_of_accounts', 20),
(53, '2018_04_05_125024_create_mxp_chart_of_acc_heads_table', 21),
(78, '2018_01_27_110718_update_mxp_role_table', 22),
(79, '2018_04_10_112500_create_party_product_tablee', 22),
(82, '2018_04_12_130615_create_page_footer_table', 22),
(83, '2018_04_12_130725_create_page_report_footer_table', 22),
(84, '2018_04_16_070741_create_brand_table', 22),
(89, '2018_04_16_095019_create_productSize_table', 23),
(143, '2018_04_11_065758_create_party_table', 24),
(145, '2018_04_23_111907_create_excel_emport_table', 24),
(146, '2018_04_25_164456_create_bill_table', 24),
(147, '2018_05_04_081744_create_challan_table', 24),
(148, '2018_05_04_121456_create_multiple_challan_table', 24),
(150, '2018_05_25_071327_create_order_input_new_table', 24),
(152, '2018_06_06_065708_create_gmts_color_table', 24),
(166, '2018_04_12_130515_create_page_header_table', 25),
(221, '2018_06_21_064357_create_pi_format_data_table_info', 27),
(239, '2018_06_01_090140_create_new_booking_list_table', 28),
(240, '2018_06_08_045630_booking_buyer_deatils_table_create', 28),
(241, '2018_06_23_094814_create_booking_challan_table', 28),
(246, '2018_05_07_060534_create_mxp_ipo_table', 29),
(247, '2018_06_23_131029_create_booking_multiple_challan_table', 29),
(248, '2018_07_10_081809_create_mxp_MRF_table', 29),
(249, '2018_07_17_093951_create_vendor_prices_table', 30),
(250, '2018_07_17_103743_create_mxp_task_table', 30),
(251, '2018_07_18_123833_create_mxp_task_role_table', 30),
(252, '2018_07_19_103016_create_suppliers_table', 30),
(253, '2018_07_20_054043_add_is+delete_column_at_supplier', 30),
(254, '2018_07_20_062001_create_mxp_supplier_prices_table', 30),
(255, '2018_07_17_105255_modify_mxp_vendor_prices', 31),
(256, '2018_07_18_094352_add_column_in_vendor_price', 31),
(257, '2018_07_24_072145_create_mxp_items_qnty_by_booking_challan', 32),
(258, '2018_07_24_073844_create_create_mxp_items_qnty_by_booking_challans_table', 33),
(259, '2018_07_27_121336_update_getProductSizeQuantityWithConcat_store_porceduer', 34),
(260, '2018_07_23_064442_add_product_type_column_at_mxp_product_table', 35),
(261, '2018_07_23_081335_add_supplier_id_mxp_MRF_table', 35),
(262, '2018_07_23_103343_add_booking_status_column_at_mxp_bookingBuyer_details', 35),
(263, '2018_07_30_070914_create_mxp_purchase_orders_table', 36),
(264, '2018_08_13_063411_add_field_mxp_booking_challan', 37),
(265, '2018_07_24_105650_change_initial_increase_column_at__mxp_ipo_table', 38),
(266, '2018_08_13_073137_add_col_to_mxp_multipleChallan', 39),
(267, '2018_08_10_053228_add_to_col_mrf_table', 40),
(268, '2018_07_12_113042_create_mxp_products_sizes_table', 41),
(269, '2018_07_12_113057_create_mxp_products_colors_table', 41),
(270, '2018_08_08_063525_create_booking_files_table', 41),
(271, '2018_08_10_053715_add_item_inc_percentage_column_product_table', 41),
(272, '2018_09_05_064644_add_new_field_mxp_ipo_table', 42),
(273, '2018_09_05_110906_Changes_sku_booking_collum_nullable', 43),
(274, '2018_09_10_091132_add_newfield_booking_buyer_table', 44),
(276, '2018_09_12_060437_create_mxp_pi_table', 45),
(277, '2018_09_12_090059_create_mxp_item_description_table', 45),
(278, '2018_09_12_062052_add_new_field_mxp_booking_table', 46),
(279, '2018_09_15_044812_add_item_id_to_mxp_product_table', 47),
(280, '2018_09_17_071234_add_field_mxp_product_table', 48),
(281, '2018_09_15_060534_create_mxp_buyer_table', 49),
(282, '2018_09_15_060534_create_mxp_userbuyer_table', 49),
(283, '2018_09_18_090400_insert_menu_value', 49),
(284, '2018_09_18_090740_insert_menu_permission_table_value', 49),
(285, '2018_15_09_064644_add_new_field_mxp_party_table', 49),
(286, '2018_15_09_064644_add_new_field_mxp_product_table', 49),
(287, '2018_10_07_080134_create_cost_price_table', 50);

-- --------------------------------------------------------

--
-- Table structure for table `mxp_accounts_heads`
--

CREATE TABLE `mxp_accounts_heads` (
  `accounts_heads_id` int(10) UNSIGNED NOT NULL,
  `head_name_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_accounts_heads`
--

INSERT INTO `mxp_accounts_heads` (`accounts_heads_id`, `head_name_type`, `account_code`, `company_id`, `group_id`, `user_id`, `is_deleted`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Assets', '1010-01', 0, 1, 1, 0, 1, '2018-04-07 03:00:21', '2018-04-07 03:00:56'),
(2, 'Expenses', '1010-02', 0, 1, 1, 0, 1, '2018-04-07 03:01:33', '2018-04-07 03:01:33'),
(3, 'Liability', '1010-03', 0, 1, 1, 0, 1, '2018-04-07 03:02:11', '2018-04-07 03:02:11'),
(4, 'Income', '1010-04', 0, 1, 1, 0, 1, '2018-04-07 03:02:25', '2018-04-07 03:02:25');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_accounts_sub_heads`
--

CREATE TABLE `mxp_accounts_sub_heads` (
  `accounts_sub_heads_id` int(10) UNSIGNED NOT NULL,
  `accounts_heads_id` int(11) UNSIGNED NOT NULL,
  `sub_head` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_accounts_sub_heads`
--

INSERT INTO `mxp_accounts_sub_heads` (`accounts_sub_heads_id`, `accounts_heads_id`, `sub_head`, `company_id`, `group_id`, `user_id`, `is_deleted`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'current asset', 0, 1, 1, 1, 1, '2018-04-05 06:24:28', '2018-04-07 03:03:12'),
(2, 1, 'Current Assets', 0, 1, 1, 0, 1, '2018-04-07 03:03:25', '2018-04-07 03:03:25'),
(3, 1, 'Non Current Assets', 0, 1, 1, 0, 1, '2018-04-07 03:05:40', '2018-04-07 03:05:40'),
(4, 3, 'Current Liabilities', 0, 1, 1, 0, 1, '2018-04-07 03:06:03', '2018-04-07 03:06:03'),
(5, 2, 'Ordinary Expense', 0, 1, 1, 0, 1, '2018-04-07 03:06:37', '2018-04-07 03:06:37'),
(6, 4, 'Ordinary Income', 0, 1, 1, 0, 1, '2018-04-07 03:07:09', '2018-04-07 03:07:09');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_acc_classes`
--

CREATE TABLE `mxp_acc_classes` (
  `mxp_acc_classes_id` int(10) UNSIGNED NOT NULL,
  `head_class_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accounts_heads_id` int(10) UNSIGNED NOT NULL,
  `accounts_sub_heads_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_acc_classes`
--

INSERT INTO `mxp_acc_classes` (`mxp_acc_classes_id`, `head_class_name`, `accounts_heads_id`, `accounts_sub_heads_id`, `company_id`, `group_id`, `user_id`, `is_deleted`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cash & cash equivalents', 1, 2, 0, 1, 1, 0, 1, '2018-04-07 03:07:51', '2018-04-07 03:07:51'),
(2, 'Receivables', 1, 2, 0, 1, 1, 0, 1, '2018-04-07 03:08:23', '2018-04-07 03:08:23'),
(3, 'Dircet Expenses', 2, 5, 0, 1, 1, 0, 1, '2018-04-07 03:08:55', '2018-04-07 03:08:55'),
(4, 'Income from Services', 4, 6, 0, 1, 1, 0, 1, '2018-04-07 03:09:23', '2018-04-07 03:09:23');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_booking`
--

CREATE TABLE `mxp_booking` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matarial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `others_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipmentDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poCatNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sku` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oos_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_type` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_pi_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size_width_height` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_booking`
--

INSERT INTO `mxp_booking` (`id`, `user_id`, `booking_order_id`, `erp_code`, `item_code`, `item_size`, `item_description`, `item_quantity`, `item_price`, `matarial`, `gmts_color`, `others_color`, `orderDate`, `orderNo`, `shipmentDate`, `poCatNo`, `created_at`, `updated_at`, `sku`, `season_code`, `oos_number`, `style`, `is_type`, `is_pi_type`, `item_size_width_height`) VALUES
(1, 49, 'BK-29092018-AB-0001', 'sada', 'sss', 'sds', '1', '123123', '0', NULL, 'sss', '0', '29-09-2018', NULL, '2018-09-30', 'asd', '2018-09-29 01:12:14', '2018-09-29 01:12:14', 'asd', NULL, 'asd', 'sad', 'general', 'unstage', NULL),
(2, 82, 'BK-07102018-Sonia-0003', '01-GY8MHT2**-001', '8MHT2', '0', '0', '10000', '0', NULL, NULL, '0', '07-10-2018', NULL, '2018-10-12', '232', '2018-10-06 22:25:58', '2018-10-06 22:27:32', '10000', 'AW18', '3232', '4541000', 'general', 'non_fsc', '41.4-63.5'),
(3, 49, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', 'Hang tag', '1000', '0.00045345', NULL, 'sss', '0', '08-10-2018', NULL, '2018-10-08', '12', '2018-10-08 01:20:22', '2018-10-08 01:20:22', 'sku', 'AW18', '121', 'style', 'general', 'unstage', '41.4-63.5'),
(4, 49, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '2000', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', '12', '2018-10-08 01:20:22', '2018-10-08 01:20:22', 'sku', 'AW18', '121', 'style', 'general', 'unstage', '41.4-63.5'),
(5, 49, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_3', 'Hang tag', '3000', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', '12', '2018-10-08 01:20:22', '2018-10-08 01:20:22', 'sku', 'AW18', '121', 'style', 'general', 'unstage', '41.4-63.5'),
(6, 49, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', 'Hang tag', '3000', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', '12', '2018-10-08 01:20:23', '2018-10-08 01:20:23', 'sku', 'AW18', '121', 'style', 'general', 'unstage', '41.4-63.5'),
(7, 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', 'Hang tag', '1000', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', '2018-10-08 01:35:19', '2018-10-08 01:37:54', 'sku', 'AW18', 'oos', 'style', 'general', 'fsc', '41.4-63.5'),
(8, 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '2000', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', '2018-10-08 01:35:20', '2018-10-08 01:37:54', 'sku', 'AW18', 'oos', 'style', 'general', 'fsc', '41.4-63.5'),
(9, 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_3', 'Hang tag', '3000', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', 'po', '2018-10-08 01:35:20', '2018-10-08 01:37:54', 'sku', 'AW18', 'oos', 'style', 'general', 'fsc', '41.4-63.5'),
(10, 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', 'Hang tag', '4000', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', 'po', '2018-10-08 01:35:20', '2018-10-08 01:37:35', 'sku', 'AW18', 'oos', 'style', 'general', 'non_fsc', '41.4-63.5');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_bookingbuyer_details`
--

CREATE TABLE `mxp_bookingbuyer_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_sort_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part1_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part2_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part1_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part2_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_complete` int(11) NOT NULL,
  `booking_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shipmentDate` date NOT NULL,
  `status_changes_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_bookingbuyer_details`
--

INSERT INTO `mxp_bookingbuyer_details` (`id`, `user_id`, `booking_order_id`, `Company_name`, `C_sort_name`, `buyer_name`, `address_part1_invoice`, `address_part2_invoice`, `attention_invoice`, `mobile_invoice`, `telephone_invoice`, `fax_invoice`, `address_part1_delivery`, `address_part2_delivery`, `attention_delivery`, `mobile_delivery`, `telephone_delivery`, `fax_delivery`, `is_complete`, `booking_status`, `created_at`, `updated_at`, `shipmentDate`, `status_changes_user_id`) VALUES
(1, 49, 'BK-29092018-AB-0001', 'AB MART FASHION WEAR LTD.', 'AB', 'DARE2B', '786, KAKIL SATAISH BORODAWRA, 13/l PURBOPARA MUDAFFA, TONGI, GAZIPUR', NULL, 'Mr. Shakhawat', '+8801671361818', NULL, NULL, '786, KAKIL SATAISH BORODAWRA, 13/3 PURBOPARA MUDAFFA,TONGI,', NULL, 'Mr. Mohsin', '+8801671361818', NULL, NULL, 0, 'Booked', '2018-09-29 01:12:14', '2018-10-06 22:16:43', '2018-09-30', 84),
(2, 82, 'BK-07102018-Sonia-0002', 'Sonia & Sweaters Ltd.', 'Sonia', 'Gymboree', 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 0, 'Booked', '2018-10-06 22:24:53', '2018-10-06 22:24:53', '2018-10-12', NULL),
(3, 82, 'BK-07102018-Sonia-0003', 'Sonia & Sweaters Ltd.', 'Sonia', 'Gymboree', 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 0, 'Booked', '2018-10-06 22:25:58', '2018-10-06 22:25:58', '2018-10-12', NULL),
(4, 49, 'BK-08102018-Sonia-0004', 'Sonia & Sweaters Ltd.', 'Sonia', 'Gymboree', 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 0, 'Booked', '2018-10-08 01:20:22', '2018-10-08 01:20:22', '2018-10-08', NULL),
(5, 49, 'BK-08102018-Sonia-0005', 'Sonia & Sweaters Ltd.', 'Sonia', 'Gymboree', 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 0, 'Booked', '2018-10-08 01:35:19', '2018-10-08 01:35:19', '2018-10-08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mxp_booking_challan`
--

CREATE TABLE `mxp_booking_challan` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `left_mrf_ipo_quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matarial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `others_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipmentDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poCatNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mrf_quantity` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipo_quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_size_width_height` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season_code` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oos_number` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_booking_challan`
--

INSERT INTO `mxp_booking_challan` (`id`, `user_id`, `job_id`, `booking_order_id`, `erp_code`, `item_code`, `item_size`, `item_description`, `item_quantity`, `left_mrf_ipo_quantity`, `item_price`, `matarial`, `gmts_color`, `others_color`, `orderDate`, `orderNo`, `shipmentDate`, `poCatNo`, `sku`, `created_at`, `updated_at`, `mrf_quantity`, `ipo_quantity`, `item_size_width_height`, `season_code`, `oos_number`, `style`) VALUES
(1, 49, 0, 'BK-29092018-AB-0001', 'sada', 'sss', 'sds', '1', '123123', '0', '0', NULL, 'sss', '0', '29-09-2018', NULL, '2018-09-30', 'asd', 'asd', '2018-09-29 01:12:14', '2018-10-08 00:48:44', '123123', '', NULL, NULL, NULL, NULL),
(2, 82, 0, 'BK-07102018-Sonia-0003', '01-GY8MHT2**-001', '8MHT2', '0', '0', '10000', '10000', '0', NULL, NULL, '0', '07-10-2018', NULL, '2018-10-12', '232', '10000', '2018-10-06 22:25:58', '2018-10-06 22:25:58', NULL, '', '41.4-63.5', NULL, NULL, NULL),
(3, 49, 0, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', 'Hang tag', '1000', '1000', '0.00045345', NULL, 'sss', '0', '08-10-2018', NULL, '2018-10-08', '12', 'sku', '2018-10-08 01:20:22', '2018-10-08 01:20:22', NULL, '', '41.4-63.5', 'AW18', '121', 'style'),
(4, 49, 0, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '2000', '2000', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', '12', 'sku', '2018-10-08 01:20:22', '2018-10-08 01:20:22', NULL, '', '41.4-63.5', 'AW18', '121', 'style'),
(5, 49, 0, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_3', 'Hang tag', '3000', '3000', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', '12', 'sku', '2018-10-08 01:20:22', '2018-10-08 01:20:22', NULL, '', '41.4-63.5', 'AW18', '121', 'style'),
(6, 49, 0, 'BK-08102018-Sonia-0004', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', 'Hang tag', '3000', '3000', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', '12', 'sku', '2018-10-08 01:20:23', '2018-10-08 01:20:23', NULL, '', '41.4-63.5', 'AW18', '121', 'style'),
(7, 49, 7, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', 'Hang tag', '1000', '159', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'sku', '2018-10-08 01:35:20', '2018-10-08 05:29:09', '1040', '801', '41.4-63.5', 'AW18', 'oos', 'style'),
(8, 49, 8, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '2000', '980', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'sku', '2018-10-08 01:35:20', '2018-10-08 05:41:25', '2030', '990', '41.4-63.5', 'AW18', 'oos', 'style'),
(9, 49, 9, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_3', 'Hang tag', '3000', '2860', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'sku', '2018-10-08 01:35:20', '2018-10-08 02:12:31', '3140', '', '41.4-63.5', 'AW18', 'oos', 'style'),
(10, 49, 10, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', 'Hang tag', '4000', '2750', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'sku', '2018-10-08 01:35:20', '2018-10-08 05:02:26', '4150', '1100', '41.4-63.5', 'AW18', 'oos', 'style');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_brand`
--

CREATE TABLE `mxp_brand` (
  `brand_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_buyer`
--

CREATE TABLE `mxp_buyer` (
  `id_mxp_buyer` int(10) UNSIGNED NOT NULL,
  `buyer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_buyer`
--

INSERT INTO `mxp_buyer` (`id_mxp_buyer`, `buyer_name`, `created_at`, `updated_at`) VALUES
(5, 'Primark', NULL, NULL),
(6, 'DARE2B', NULL, NULL),
(7, 'REGATTA', NULL, NULL),
(8, 'CRAGHOPPERS', NULL, NULL),
(9, 'Zara TRF', NULL, NULL),
(10, 'ECI', NULL, NULL),
(11, 'M&S', NULL, NULL),
(12, 'Camicissima', NULL, NULL),
(13, 'DEBENHARMS', NULL, NULL),
(14, 'Harmont & Blaine (H&B)', NULL, NULL),
(15, 'Jhon Lewies', NULL, NULL),
(16, 'Olymp', NULL, NULL),
(17, 'SACOOR BROTHERS', NULL, NULL),
(18, 'TRENDY', NULL, NULL),
(19, 'Woolworth', NULL, NULL),
(20, 'ZARA BOYS', NULL, NULL),
(21, 'ZARA KIDS', NULL, NULL),
(22, 'ADLER\r\n(V BY VERY)', NULL, NULL),
(23, 'ADLER', NULL, NULL),
(24, 'C&A BRAZIL', NULL, NULL),
(25, 'C&A MEXICO', NULL, NULL),
(26, 'Bossini', NULL, NULL),
(27, 'O\'stin', NULL, NULL),
(28, 'Sportsmaster', NULL, NULL),
(29, 'Puma', NULL, NULL),
(30, 'Voice', NULL, NULL),
(31, 'Splash', NULL, NULL),
(32, 'Bjorn Born', NULL, NULL),
(33, 'Eagle Bert', NULL, NULL),
(34, 'Gymboree', NULL, NULL),
(35, 'Bershka', '2018-09-20 00:35:46', '2018-09-20 00:35:46'),
(36, 'Royal Class', '2018-09-20 00:36:08', '2018-09-20 00:36:08'),
(37, 'Jay Jay', '2018-09-20 00:37:08', '2018-09-20 00:37:08'),
(38, 'Aldi', '2018-09-20 00:37:38', '2018-09-20 00:37:38'),
(40, 'SPM', '2018-09-27 03:49:17', '2018-09-27 03:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_challan`
--

CREATE TABLE `mxp_challan` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oss` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_buyer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count_challan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_companies`
--

CREATE TABLE `mxp_companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_companies`
--

INSERT INTO `mxp_companies` (`id`, `group_id`, `name`, `description`, `address`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(17, 49, 'Maxim Label & Packaging (BD) Pvt. Ltd', 'Maxim Label and Packaging is a One-Stop destination for retailers to source good quality garment accessories at reasonable prices. We provide hang tags, price tickets, care labels, adhesives, heat transfers, RFID and packaging solutions.', 'Mollik Tower, 12 th Floor 13-16, Zoo Road, Section-1 Mirpur, Dhaka-1216 Bangladesh', 'T - 029001486', 1, '2018-05-03 02:39:47', '2018-09-10 00:23:42');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_gmts_color`
--

CREATE TABLE `mxp_gmts_color` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action` int(6) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_gmts_color`
--

INSERT INTO `mxp_gmts_color` (`id`, `user_id`, `item_code`, `color_name`, `status`, `created_at`, `updated_at`, `action`) VALUES
(3, 49, 'sad', 'assa', '1', '2018-09-27 01:13:18', '2018-09-27 01:13:18', 0),
(4, 49, NULL, 'sss', '1', '2018-09-27 05:41:07', '2018-09-27 05:41:07', 0),
(8, 49, 'primark', 'sss', '1', '2018-09-29 01:33:54', '2018-09-29 01:33:54', 0),
(10, 49, 'sss', 'sss', '1', '2018-09-29 04:48:40', '2018-09-29 04:48:40', 0),
(11, 49, '234', 'sss', '1', '2018-10-06 11:40:43', '2018-10-06 11:40:43', 0),
(12, 49, NULL, 'color_1', '1', '2018-10-08 01:17:46', '2018-10-08 01:17:46', 0),
(13, 49, NULL, 'color_2', '1', '2018-10-08 01:17:52', '2018-10-08 01:17:52', 0),
(17, 49, '8KMHT2', 'sss', '1', '2018-10-08 01:29:51', '2018-10-08 01:29:51', 0),
(18, 49, '8KMHT2', 'color_1', '1', '2018-10-08 01:29:51', '2018-10-08 01:29:51', 0),
(19, 49, '8KMHT2', 'color_2', '1', '2018-10-08 01:29:51', '2018-10-08 01:29:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `mxp_header`
--

CREATE TABLE `mxp_header` (
  `header_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `header_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_fontsize` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_fontstyle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_colour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_allignment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_header`
--

INSERT INTO `mxp_header` (`header_id`, `user_id`, `header_type`, `header_title`, `header_fontsize`, `header_fontstyle`, `header_colour`, `logo`, `logo_allignment`, `address1`, `address2`, `address3`, `cell_number`, `attention`, `status`, `action`, `created_at`, `updated_at`) VALUES
(1, 49, '11', 'Maxim Label & packaging Bangladesh Pvt; Ltd', 'x-small', 'normal', 'blue', '58040.png', 'left', 'Mollik Tower, 11F', '13-14 Zoo Road Mirpur-1', 'Dhaka, Bangladesh', '0170000001', 'MS.Rita / Mr.Shovon', '', 'update', '2018-06-10 23:07:08', '2018-09-14 21:58:42');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_ipo`
--

CREATE TABLE `mxp_ipo` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(11) NOT NULL,
  `ipo_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `initial_increase` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matarial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `others_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipmentDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poCatNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sku` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipo_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_ipo`
--

INSERT INTO `mxp_ipo` (`id`, `job_id`, `ipo_id`, `user_id`, `booking_order_id`, `initial_increase`, `erp_code`, `item_code`, `item_size`, `item_description`, `item_quantity`, `item_price`, `matarial`, `gmts_color`, `others_color`, `orderDate`, `orderNo`, `shipmentDate`, `poCatNo`, `status`, `created_at`, `updated_at`, `sku`, `ipo_quantity`) VALUES
(1, 0, 'IPO-08102018-0001', 84, 'BK-08102018-Sonia-0005', '34', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', 'Hang tag', '100', '0.00045345', NULL, 'color_2', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'create', '2018-10-08 05:02:26', '2018-10-08 05:02:26', 'sku', '100'),
(2, 7, 'IPO-08102018-0002', 84, 'BK-08102018-Sonia-0005', '50', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', 'Hang tag', '200', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'create', '2018-10-08 05:07:34', '2018-10-08 05:07:34', 'sku', '200'),
(3, 8, 'IPO-08102018-0002', 84, 'BK-08102018-Sonia-0005', '50', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '200', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'create', '2018-10-08 05:07:34', '2018-10-08 05:07:34', 'sku', '200'),
(4, 7, 'IPO-08102018-0004', 84, 'BK-08102018-Sonia-0005', '0', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', 'Hang tag', '1', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'create', '2018-10-08 05:29:09', '2018-10-08 05:29:09', 'sku', '1'),
(5, 8, 'IPO-08102018-0005', 84, 'BK-08102018-Sonia-0005', '33', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '122', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'create', '2018-10-08 05:37:24', '2018-10-08 05:37:24', 'sku', '122'),
(6, 8, 'IPO-08102018-0006', 84, 'BK-08102018-Sonia-0005', '0', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '12', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'create', '2018-10-08 05:38:44', '2018-10-08 05:38:44', 'sku', '12'),
(7, 8, 'IPO-08102018-0007', 84, 'BK-08102018-Sonia-0005', '0', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', 'Hang tag', '56', '0.00045345', NULL, 'color_1', '0', '08-10-2018', NULL, '2018-10-08', 'po', 'create', '2018-10-08 05:41:25', '2018-10-08 05:41:25', 'sku', '56');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_items_details_by_booking_challan`
--

CREATE TABLE `mxp_items_details_by_booking_challan` (
  `items_details_id` int(10) UNSIGNED NOT NULL,
  `booking_challan_id` int(11) NOT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_items_details_by_booking_challan`
--

INSERT INTO `mxp_items_details_by_booking_challan` (`items_details_id`, `booking_challan_id`, `booking_order_id`, `item_code`, `erp_code`, `item_size`, `item_quantity`, `gmts_color`, `created_at`, `updated_at`) VALUES
(1, 1, 'BK-29092018-AB-0001', 'sss', 'sada', 'sds', '123123', 'sss', '2018-09-29 01:12:14', '2018-09-29 01:12:14'),
(2, 2, 'BK-07102018-Sonia-0003', '8MHT2', '01-GY8MHT2**-001', '0', '10000', '', '2018-10-06 22:25:58', '2018-10-06 22:25:58'),
(3, 3, 'BK-08102018-Sonia-0004', '8KMHT2', '01-GY8KMHT2**-001', 'size_1', '1000', 'sss', '2018-10-08 01:20:22', '2018-10-08 01:20:22'),
(4, 4, 'BK-08102018-Sonia-0004', '8KMHT2', '01-GY8KMHT2**-001', 'size_2', '2000', 'color_1', '2018-10-08 01:20:22', '2018-10-08 01:20:22'),
(5, 5, 'BK-08102018-Sonia-0004', '8KMHT2', '01-GY8KMHT2**-001', 'size_3', '3000', 'color_2', '2018-10-08 01:20:23', '2018-10-08 01:20:23'),
(6, 6, 'BK-08102018-Sonia-0004', '8KMHT2', '01-GY8KMHT2**-001', 'size_4', '3000', 'color_2', '2018-10-08 01:20:23', '2018-10-08 01:20:23'),
(7, 7, 'BK-08102018-Sonia-0005', '8KMHT2', '01-GY8KMHT2**-001', 'size_1', '1000', 'color_1', '2018-10-08 01:35:20', '2018-10-08 01:35:20'),
(8, 8, 'BK-08102018-Sonia-0005', '8KMHT2', '01-GY8KMHT2**-001', 'size_2', '2000', 'color_1', '2018-10-08 01:35:20', '2018-10-08 01:35:20'),
(9, 9, 'BK-08102018-Sonia-0005', '8KMHT2', '01-GY8KMHT2**-001', 'size_3', '3000', 'color_2', '2018-10-08 01:35:20', '2018-10-08 01:35:20'),
(10, 10, 'BK-08102018-Sonia-0005', '8KMHT2', '01-GY8KMHT2**-001', 'size_4', '4000', 'color_2', '2018-10-08 01:35:20', '2018-10-08 01:35:20');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_item_cost_price`
--

CREATE TABLE `mxp_item_cost_price` (
  `cost_price_id` int(10) UNSIGNED NOT NULL,
  `id_product` int(11) NOT NULL,
  `price_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_item_cost_price`
--

INSERT INTO `mxp_item_cost_price` (`cost_price_id`, `id_product`, `price_1`, `price_2`, `last_action`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 4, '12', '111', 'update', '2018-10-07 02:37:59', '2018-10-08 01:29:52', '49');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_item_description`
--

CREATE TABLE `mxp_item_description` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_item_description`
--

INSERT INTO `mxp_item_description` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Woven', 1, NULL, NULL),
(3, 'Sticker', 1, NULL, NULL),
(4, 'Care label', 1, NULL, '2018-10-03 00:08:29'),
(9, 'Hang tag', 1, '2018-10-03 00:06:40', '2018-10-03 00:06:40'),
(10, 'Print Label', 1, '2018-10-03 00:07:00', '2018-10-03 00:07:00'),
(11, 'Heat Seal', 1, '2018-10-03 00:07:14', '2018-10-03 00:07:14'),
(12, 'Adhesive', 1, '2018-10-03 00:07:33', '2018-10-03 00:07:58'),
(13, 'Heat transfer label', 1, '2018-10-03 00:08:53', '2018-10-03 00:08:53'),
(14, 'Cotton care label', 1, '2018-10-03 00:09:10', '2018-10-03 00:09:10'),
(15, 'main label', 1, '2018-10-03 00:09:21', '2018-10-03 00:09:21'),
(16, 'Size Label', 1, '2018-10-03 00:09:47', '2018-10-03 00:09:47'),
(17, 'Special Feature Tag', 1, '2018-10-03 00:09:57', '2018-10-03 00:09:57'),
(18, 'Feature tag', 1, '2018-10-03 00:10:09', '2018-10-03 00:10:09');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_languages`
--

CREATE TABLE `mxp_languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `lan_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lan_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_languages`
--

INSERT INTO `mxp_languages` (`id`, `lan_name`, `lan_code`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'English', 'en', '2018-03-06 00:10:25', '2018-03-06 00:10:25', 1),
(2, 'বাংলা', 'bn', '2018-03-06 00:10:57', '2018-03-06 00:10:57', 1),
(3, 'Chinese', 'cn', '2018-09-15 00:08:21', '2018-09-15 00:08:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mxp_maximbill`
--

CREATE TABLE `mxp_maximbill` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oss` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_buyer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_menu`
--

CREATE TABLE `mxp_menu` (
  `menu_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT '0',
  `is_active` int(11) NOT NULL,
  `order_id` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_menu`
--

INSERT INTO `mxp_menu` (`menu_id`, `name`, `route_name`, `description`, `parent_id`, `is_active`, `order_id`, `created_at`, `updated_at`) VALUES
(3, 'LANGUAGE', 'language-chooser_view', 'Language', 0, 1, 0, NULL, NULL),
(4, 'DASHBOARD', 'dashboard_view', 'Super admin Dashboard', 0, 1, 1, NULL, NULL),
(5, 'SETTINGS', '', 'Settings', 0, 1, 2, NULL, NULL),
(6, 'ROLE', '', 'Role Management ', 0, 1, 2, NULL, NULL),
(7, 'ADD ROLE ACTION', 'add_role_action', 'Add new Role', 0, 1, 0, NULL, NULL),
(8, 'Role List', 'role_list_view', 'Role List and manage option', 6, 1, 2, NULL, NULL),
(9, 'ROLE UPDATE FORM', 'role_update_view', 'Show role update Form', 0, 1, 2, NULL, NULL),
(10, 'ROLE DELETE ACTION', 'role_delete_action', 'Delete role', 0, 1, 0, NULL, NULL),
(11, 'UPDATE ROLE ACTION', 'role_update_action', 'Update Role', 0, 1, 0, NULL, NULL),
(12, 'Role Permission ', 'role_permission_view', 'Set Route Access to Role', 6, 1, 3, NULL, NULL),
(13, 'PERMISSION ROLE ACTION', 'role_permission_action', 'Set Route Access to Role', 0, 1, 0, NULL, NULL),
(16, 'ROLE PERMISSION FORM', 'role_permission_update_view', '0', 0, 1, 0, NULL, NULL),
(18, 'Create User', 'create_user_view', 'User Create Form', 5, 1, 1, NULL, NULL),
(19, 'CREATE USER ACTION', 'create_user_action', '', 0, 1, 0, NULL, NULL),
(20, 'User List', 'user_list_view', '', 5, 1, 2, NULL, NULL),
(21, 'USER UPDATE FORM', 'company_user_update_view', '', 0, 1, 0, NULL, NULL),
(22, 'UPDATE USER ACTION', 'company_user_update_action', '', 0, 1, 0, NULL, NULL),
(23, 'DELETE USER ACTION', 'company_user_delete_action', '', 0, 1, 0, NULL, NULL),
(24, 'Manage Langulage', 'manage_language', 'language add and view', 3, 1, 0, NULL, NULL),
(25, 'ADD LANGUAGE ACTION', 'create_locale_action', 'add language', 0, 1, 0, NULL, NULL),
(26, 'UPDATE LOCALE ACTION', 'update_locale_action', 'update language', 0, 1, 0, NULL, NULL),
(27, 'Manage Translation', 'manage_translation', 'manage transaltion', 3, 1, 2, NULL, NULL),
(28, 'CREATE TRANSLATION ACTION', 'create_translation_action', 'create translation', 0, 1, 0, NULL, NULL),
(29, 'UPDATE TRANSLATION ACTION', 'update_translation_action', 'update translation', 0, 1, 0, NULL, NULL),
(30, 'POST UPDATE TRANSLATION ACTION', 'update_translation_key_action', 'post update translaion', 0, 1, 0, NULL, NULL),
(31, 'DELETE TRANSLATION ACTION', 'delete_translation_action', 'delete translation', 0, 1, 0, NULL, NULL),
(32, 'Upload Language File', 'update_language', 'upload language file', 3, 1, 3, NULL, NULL),
(33, 'USER', '', 'User Management', 0, 1, 1, NULL, NULL),
(34, 'Add New Role', 'add_role_view', 'New role adding form', 6, 1, 1, NULL, NULL),
(35, 'Open Company Acc', 'create_company_acc_view', 'Company Account Opening Form', 5, 1, 3, NULL, NULL),
(36, 'OPEN COMPANY ACCOUNT', 'create_company_acc_action', 'Company Acc opening Action', 5, 1, 2, NULL, NULL),
(37, 'Company List', 'company_list_view', 'Company List View', 5, 1, 4, NULL, NULL),
(38, 'PRODUCT', '', 'Product management', 0, 1, 0, NULL, NULL),
(67, 'Add Client', 'client_com_add_view', '', 0, 1, 0, NULL, NULL),
(68, 'CLIENT ADD', 'client_com_add_action', '', 0, 1, 0, NULL, NULL),
(69, 'Client Update', 'client_com_update_view', '', 0, 1, 0, NULL, NULL),
(70, 'CLIENT UPDATE ACTION', 'client_com_update_action', '', 0, 1, 0, NULL, NULL),
(71, 'CLIENT DELETE ACTION', 'client_com_delete_action', '', 0, 1, 0, NULL, NULL),
(72, 'Client List', 'client_com_list_view', 'Show Client List', 5, 1, 5, NULL, NULL),
(75, 'management', '', '', 0, 1, 4, NULL, NULL),
(76, 'Item  List View', 'product_list_view', '', 75, 1, 1, NULL, NULL),
(78, 'Vendor List', 'party_list_view', '', 75, 1, 0, NULL, NULL),
(83, 'page', '', '', 0, 1, 0, NULL, NULL),
(84, 'page header', 'page_header_view', '', 83, 1, 0, NULL, NULL),
(85, 'page footer', 'page_footer_view', '', 83, 1, 2, NULL, NULL),
(86, 'report footer', 'report_footer_view', '', 83, 1, 3, NULL, NULL),
(87, 'brand', 'brand_list_view', '', 7555, 0, 3, NULL, NULL),
(88, 'Item Size view', 'product_size_view', '', 75, 1, 4, NULL, NULL),
(89, 'PRINT', '', 'there r all print file avialbe', 0, 1, 0, NULL, NULL),
(90, 'Bill_copy', 'bill_copy_view', '', 8999, 1, 1, NULL, NULL),
(91, 'all_bill_view', 'all_bill_view', '', 8999, 1, 3, NULL, NULL),
(92, 'Challan Boxing List', 'challan_boxing_list_view', '', 8999, 1, 4, NULL, NULL),
(93, 'order_list_view', 'order_list_view', '', 8999, 1, 2, NULL, NULL),
(94, 'ipo_view', 'ipo_view', '', 8999, 1, 5, NULL, NULL),
(95, 'Order Input', 'order_input_view', '', 8999, 1, 0, NULL, NULL),
(96, 'GMTS Color', 'gmts_color_view', '', 75, 1, 5, NULL, NULL),
(97, 'Production', '', '', 0, 1, 0, NULL, NULL),
(98, 'Booking List View', 'booking_list_view', '', 97, 1, 1, NULL, NULL),
(99, 'MRF List', 'mrf_list_view', 'show all MRF list', 97, 1, 2, NULL, NULL),
(100, 'Challan List', 'challan_list_view', '', 97, 1, 3, NULL, NULL),
(101, 'Suppliers', 'supplier_list_view', '', 75, 1, 0, NULL, NULL),
(102, 'Add Supplier', 'supplier_add_view', '', 0, 1, 0, NULL, NULL),
(103, 'Add Supplier', 'supplier_add_action', '', 0, 1, 0, NULL, NULL),
(104, 'Supplier Update View', 'supplier_update', '', 0, 1, 0, NULL, NULL),
(105, 'Supplier Update Action', 'supplier_update_action', '', 0, 1, 0, NULL, NULL),
(106, 'Supplier Delete Action', 'supplier_delete_action', '', 0, 1, 0, NULL, NULL),
(107, 'permission task assign', 'permission_task_assign', '', 6, 1, 0, NULL, NULL),
(108, 'Purchase Order', 'generate_purchase_order', 'purchase order list', 97, 1, 4, NULL, NULL),
(109, 'Vendor Create', 'party_create', 'Vendor Create', 0, 1, 0, NULL, NULL),
(110, 'Vendor Save Action', 'party_save_action', 'Vendor Save Action', 0, 1, 0, NULL, NULL),
(111, 'Vendor Edit View', 'party_edit_view', '', 0, 1, 0, NULL, NULL),
(112, 'Vendor Edit Action', 'party_edit_action', 'Vendor Edit Action', 0, 1, 0, NULL, NULL),
(113, 'Vendor Delete Action', 'party_delete_action', 'Vendor Delete Action', 0, 1, 0, NULL, NULL),
(114, 'Item Add view', 'add_product_view', 'Item Add view', 0, 1, 0, NULL, NULL),
(115, 'Item Add Action', 'add_product_action', '', 0, 1, 0, NULL, NULL),
(116, 'Item Delete Action', 'delete_product_action', 'Item Delete Action', 0, 1, 0, NULL, NULL),
(117, 'Item Update View', 'update_product_view', 'Item Update View', 0, 1, 0, NULL, NULL),
(118, 'Item Update Action', 'update_product_action', 'Item Update Action', 0, 1, 0, NULL, NULL),
(119, 'Supplier Add View', 'supplier_add_view', 'Supplier Add View', 0, 1, 0, NULL, NULL),
(120, 'Supplier Add Action', 'supplier_add_action', 'Supplier Add Action', 0, 1, 0, NULL, NULL),
(121, 'Supplier Update', 'supplier_update', 'Supplier Update', 0, 1, 0, NULL, NULL),
(122, 'Supplier Update Action', 'supplier_update_action', 'Supplier Update Action', 0, 1, 0, NULL, NULL),
(123, 'Supplier Delete Action', 'supplier_delete_action', 'Supplier Delete Action', 0, 1, 0, NULL, NULL),
(124, 'Supplier Booking Files Download', 'booking_files_download', 'Supplier Booking Files Download', 0, 1, 0, NULL, NULL),
(125, 'Brand Add View', 'addbrand_view', 'Brand Add View', 0, 1, 0, NULL, NULL),
(126, 'Brand Create Action', 'create_brand_action', 'Brand Create Action', 0, 1, 0, NULL, NULL),
(127, 'Brand Update View', 'update_brand_view', 'Brand Update View', 0, 1, 0, NULL, NULL),
(128, 'Brand Update Action', 'update_brand_action', 'Brand Update Action', 0, 1, 0, NULL, NULL),
(129, 'Brand Delete Action', 'delete_brand_action', 'Brand Delete Action', 0, 1, 0, NULL, NULL),
(130, 'Item Size Add View', 'add_size_view', 'Size Add View', 0, 1, 0, NULL, NULL),
(131, 'Item Size Create Action', 'create_size_action', 'Size Create Action', 0, 1, 0, NULL, NULL),
(132, 'Item Size Update Action', 'update_size_action', 'Size Update Action', 0, 1, 0, NULL, NULL),
(133, 'Item Size Delete Action', 'delete_size_action', 'Size Delete Action', 0, 1, 0, NULL, NULL),
(134, 'Item Size Update View', 'update_size_view', 'Size Update View', 0, 1, 0, NULL, NULL),
(135, 'GMT Color Add View', 'add_color_view', 'GMT Color Add View', 0, 1, 0, NULL, NULL),
(136, 'GMT Color Update View', 'update_gmtscolor_view', 'GMT Color Update View', 0, 1, 0, NULL, NULL),
(137, 'GMT Color Add Action', 'add_gmtscolor_action', 'GMT Color Add Action', 0, 1, 0, NULL, NULL),
(138, 'GMT Color Update Action', 'update_gmtscolor_action', 'GMT Color Update Action', 0, 1, 0, NULL, NULL),
(139, 'GMT Color Delete Action', 'delete_gmtscolor_action', 'GMT Color Delete Action', 0, 1, 0, NULL, NULL),
(140, 'Production MRF Report View ', 'mrf_list_action_task', 'Production MRF Report View ', 0, 1, 0, NULL, NULL),
(141, 'Dashboard Task Actions', 'task_action', 'Dashboard Task Actions', 0, 1, 0, NULL, NULL),
(142, 'Booking Order Action', 'booking_order_action', 'Booking Order Action', 0, 1, 0, NULL, NULL),
(143, 'Production Booking List Action Task', 'booking_list_action_task', 'Production Booking List Action Task', 0, 1, 0, NULL, NULL),
(144, 'Production Booking List Details View', 'booking_list_details_view', 'Production Booking List Details View', 0, 1, 0, NULL, NULL),
(145, 'Production Booking List Create Ipo', 'booking_list_create_ipo', 'Production Booking List Create Ipo', 0, 1, 0, NULL, NULL),
(146, 'Production Booking List Create MRF', 'booking_list_create_mrf', 'Production Booking List Create MRF', 0, 1, 0, NULL, NULL),
(147, 'Production Booking File Download', 'booking_files_download', 'Production Booking File Download', 0, 1, 0, NULL, NULL),
(148, 'Dashboard MRF Action Task', 'mrf_action_task', 'Dashboard MRF Action Task', 0, 1, 0, NULL, NULL),
(149, 'Production Challan Report Action', 'challan_list_action_task', 'Production Challan Report Action', 0, 1, 0, NULL, NULL),
(150, 'Page Header Create Page Header Create View', 'page_header_create', 'Page Header Create View', 0, 1, 0, NULL, NULL),
(151, 'Page Header Save', 'page_header_save', 'Page Header Save', 0, 1, 0, NULL, NULL),
(152, 'Page Edit View', 'page_edit_view', 'Page Edit View', 0, 1, 0, NULL, NULL),
(153, 'Page Edit Action', 'page_edit_action', 'Page Edit Action', 0, 1, 0, NULL, NULL),
(154, 'Page Delete Action', 'page_delete_action', 'Page Delete Action', 0, 1, 0, NULL, NULL),
(155, 'Page Footer Add Title View', 'add_footer_title_view', 'Page Footer Add Title View', 0, 1, 0, NULL, NULL),
(156, 'Page Footer Add Acton', 'footer_action', 'Page Footer Add Acton', 0, 1, 0, NULL, NULL),
(157, 'Page Footer Edit View', 'update_title_view', 'Page Footer Edit View', 0, 1, 0, NULL, NULL),
(158, 'Page Footer Update Action', 'updatefooter_action', 'Page Footer Update Action', 0, 1, 0, NULL, NULL),
(159, 'Page Footer Delete Action', 'delete_footer_action', 'Page Footer Delete Action', 0, 1, 0, NULL, NULL),
(160, 'Page Report Footer Add View', 'addreport_footer_view', 'Page Report Footer Add View', 0, 1, 0, NULL, NULL),
(161, 'Page Report Footer Add Action', 'reportfooter_action', 'Page Report Footer Add Action', 0, 1, 0, NULL, NULL),
(162, 'Page Report Footer Update View', 'update_report_view', 'Page Report Footer Update View', 0, 1, 0, NULL, NULL),
(163, 'Page Report Footer Update Action', 'update_report_action', 'Page Report Footer Update Action', 0, 1, 0, NULL, NULL),
(164, 'Page Report Footer Delete Action', 'delete_report_action', 'Page Report Footer Delete Action', 0, 1, 0, NULL, NULL),
(165, 'Challan Multiple Action Task', 'multiple_challan_action_task', 'Challan Multiple Action Task', 0, 1, 0, NULL, NULL),
(166, 'Tracking list Report', 'booking_list_report', 'Tracking list Report', 97, 1, 7, NULL, NULL),
(167, 'PI Generate Report Action', 'pi_generate_action', 'PI Generate Report Action', 0, 1, 0, NULL, NULL),
(168, 'Production PI List View', 'pi_list_view', 'PI List View', 97, 1, 5, NULL, NULL),
(169, 'PI List Report View', 'pi_list_report_view', 'PI list Report View', 0, 1, 0, NULL, NULL),
(170, 'Production Ipo List View', 'ipo_list_view', 'Production Ipo List View', 97, 1, 8, '2018-09-17 18:00:00', '2018-09-17 18:00:00'),
(171, 'Item Description List View', 'description_list_view', 'Item Description List View', 75, 1, 7, '2018-09-24 18:00:00', '2018-09-24 18:00:00'),
(172, 'Item Description Add View', 'addDescription_view', 'Item Description Add View', 0, 1, 0, NULL, NULL),
(173, 'Item Description Add Action', 'create_description_action', 'Item Description Add Action', 0, 1, 0, NULL, NULL),
(174, 'Item Description Update View', 'update_description_view', 'Item Description Update View', 0, 1, 0, NULL, NULL),
(175, 'Item Description Update Action', 'update_description_action', 'Item Description Update Action', 0, 1, 0, NULL, NULL),
(176, 'Item Description Delete Action', 'delete_description_action', 'Item Description Delete Action', 0, 1, 0, NULL, NULL),
(177, 'Buyer List View ', 'buyer_list_view', 'Buyer List View', 75, 1, 8, NULL, NULL),
(178, 'Buyer Add View', 'addbuyer_view', 'Buyer Add View', 0, 1, 0, NULL, NULL),
(179, 'Buyer Add Action', 'create_buyer_action', 'Buyer Add Action', 0, 1, 0, NULL, NULL),
(180, 'Buyer Update View', 'update_buyer_view', 'Buyer Update View', 0, 1, 0, NULL, NULL),
(181, 'Buyer Update Action', 'update_buyer_action', 'Buyer Update Action', 0, 1, 0, NULL, NULL),
(182, 'Buyer Delete Action', 'delete_buyer_action', 'Buyer Delete Action', 0, 1, 0, NULL, NULL),
(183, 'IPO Genarate Action', 'task_ipo_action', 'IPO Genarate Action', 0, 1, 0, NULL, NULL),
(184, 'Ipo List Report View', 'ipo_list_report_view', 'Ipo List Report View', 0, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mxp_mrf_table`
--

CREATE TABLE `mxp_mrf_table` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mrf_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matarial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `others_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipmentDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poCatNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mrf_quantity` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mrf_person_name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mrf_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_mrf_table`
--

INSERT INTO `mxp_mrf_table` (`id`, `job_id`, `user_id`, `mrf_id`, `supplier_id`, `booking_order_id`, `erp_code`, `item_code`, `item_size`, `item_description`, `item_quantity`, `item_price`, `matarial`, `gmts_color`, `others_color`, `orderDate`, `orderNo`, `shipmentDate`, `poCatNo`, `status`, `action`, `created_at`, `updated_at`, `mrf_quantity`, `mrf_person_name`, `mrf_status`) VALUES
(1, 0, 84, 'MRF-08102018-0001', NULL, 'BK-29092018-AB-0001', 'sada', 'sss', 'sds', NULL, '0', '0', NULL, 'sss', NULL, '29-09-2018', NULL, '2018-10-17', 'asd', NULL, 'create', '2018-10-08 00:48:44', '2018-10-08 00:48:44', '123123', NULL, 'Open'),
(2, 0, 84, 'MRF-08102018-0002', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', NULL, '0', '0.00045345', NULL, 'color_1', NULL, '08-10-2018', NULL, '2018-10-17', 'po', NULL, 'create', '2018-10-08 01:53:29', '2018-10-08 01:53:29', '1000', NULL, 'Open'),
(3, 0, 84, 'MRF-08102018-0002', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', NULL, '0', '0.00045345', NULL, 'color_1', NULL, '08-10-2018', NULL, '2018-10-17', 'po', NULL, 'create', '2018-10-08 01:53:29', '2018-10-08 01:53:29', '2000', NULL, 'Open'),
(4, 0, 84, 'MRF-08102018-0002', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_3', NULL, '0', '0.00045345', NULL, 'color_2', NULL, '08-10-2018', NULL, '2018-10-17', 'po', NULL, 'create', '2018-10-08 01:53:29', '2018-10-08 01:53:29', '3000', NULL, 'Open'),
(5, 0, 84, 'MRF-08102018-0002', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', NULL, '0', '0.00045345', NULL, 'color_2', NULL, '08-10-2018', NULL, '2018-10-17', 'po', NULL, 'create', '2018-10-08 01:53:29', '2018-10-08 01:53:29', '4000', NULL, 'Open'),
(6, 0, 84, 'MRF-08102018-0006', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', NULL, '980', '0.00045345', NULL, 'color_1', NULL, '08-10-2018', NULL, '2018-10-31', 'po', NULL, 'create', '2018-10-08 02:00:07', '2018-10-08 02:00:07', '20', NULL, 'Open'),
(7, 0, 84, 'MRF-08102018-0006', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_2', NULL, '1970', '0.00045345', NULL, 'color_1', NULL, '08-10-2018', NULL, '2018-10-31', 'po', NULL, 'create', '2018-10-08 02:00:07', '2018-10-08 02:00:07', '30', NULL, 'Open'),
(8, 0, 84, 'MRF-08102018-0006', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_3', NULL, '2960', '0.00045345', NULL, 'color_2', NULL, '08-10-2018', NULL, '2018-10-31', 'po', NULL, 'create', '2018-10-08 02:00:07', '2018-10-08 02:00:07', '40', NULL, 'Open'),
(9, 0, 84, 'MRF-08102018-0006', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', NULL, '3950', '0.00045345', NULL, 'color_2', NULL, '08-10-2018', NULL, '2018-10-31', 'po', NULL, 'create', '2018-10-08 02:00:07', '2018-10-08 02:00:07', '50', NULL, 'Open'),
(10, 9, 84, 'MRF-08102018-0010', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_3', NULL, '2860', '0.00045345', NULL, 'color_2', NULL, '08-10-2018', NULL, '2018-10-17', 'po', NULL, 'create', '2018-10-08 02:12:32', '2018-10-08 02:12:32', '100', NULL, 'Open'),
(11, 10, 84, 'MRF-08102018-0010', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_4', NULL, '3850', '0.00045345', NULL, 'color_2', NULL, '08-10-2018', NULL, '2018-10-17', 'po', NULL, 'create', '2018-10-08 02:12:32', '2018-10-08 02:12:32', '100', NULL, 'Open'),
(12, 7, 84, 'MRF-08102018-0012', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', NULL, '170', '0.00045345', NULL, 'color_1', NULL, '08-10-2018', NULL, '2018-10-15', 'po', NULL, 'create', '2018-10-08 05:22:01', '2018-10-08 05:22:01', '10', NULL, 'Open'),
(13, 7, 84, 'MRF-08102018-0013', NULL, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'size_1', NULL, '160', '0.00045345', NULL, 'color_1', NULL, '08-10-2018', NULL, '2018-10-15', 'po', NULL, 'create', '2018-10-08 05:24:03', '2018-10-08 05:24:03', '10', NULL, 'Open');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_multiplechallan`
--

CREATE TABLE `mxp_multiplechallan` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `challan_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oss` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_buyer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incrementValue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checking_ids_of_challan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_order`
--

CREATE TABLE `mxp_order` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oss` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incrementValue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_order_input`
--

CREATE TABLE `mxp_order_input` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oss` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incrementValue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_pagefooter`
--

CREATE TABLE `mxp_pagefooter` (
  `footer_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_pageheader`
--

CREATE TABLE `mxp_pageheader` (
  `header_id` int(11) NOT NULL,
  `aaaa` text NOT NULL,
  `aaaav` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_party`
--

CREATE TABLE `mxp_party` (
  `id` int(10) UNSIGNED NOT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_buyer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part1_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part2_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part1_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_part2_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attention_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax_delivery` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buyer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_party`
--

INSERT INTO `mxp_party` (`id`, `party_id`, `user_id`, `name`, `sort_name`, `name_buyer`, `address_part1_invoice`, `address_part2_invoice`, `attention_invoice`, `mobile_invoice`, `telephone_invoice`, `fax_invoice`, `address_part1_delivery`, `address_part2_delivery`, `attention_delivery`, `mobile_delivery`, `telephone_delivery`, `fax_delivery`, `description_1`, `description_2`, `description_3`, `created_at`, `updated_at`, `status`, `id_buyer`) VALUES
(2, '8080', '82', 'AB MART FASHION WEAR LTD.', 'AB', 'DARE2B', '786, KAKIL SATAISH BORODAWRA, 13/l PURBOPARA MUDAFFA, TONGI, GAZIPUR', NULL, 'Mr. Shakhawat', '+8801671361818', NULL, NULL, '786, KAKIL SATAISH BORODAWRA, 13/3 PURBOPARA MUDAFFA,TONGI,', NULL, 'Mr. Mohsin', '+8801671361818', NULL, NULL, NULL, NULL, NULL, NULL, '2018-09-24 23:49:29', '1', 6),
(3, '8081', '49', 'BAYEZID DRESSES (PVT) LTD.', 'BAYEZID', 'DARE2B', 'DELUXE HOUSE#3 (2ND FLOOR), 209/227, KULGAON, BALUCHARA, CHITTAGONG 4214', NULL, 'Mr. Hayder', '+8801984464604', NULL, NULL, 'DELUXE HOUSE#3 (2ND FLOOR), 209/227, KULGAON, BALUCHARA, CHITTAGONG-4214', NULL, 'Mr. HAYDER', '+8801984464604', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 6),
(4, '8082', '49', 'Emaz Fashion Wear Limited.', 'Emaz', 'DARE2B', 'Ashulia Main Road,House No: 119, Block-D,Dhour,Turag, Dhaka-1230,Bangladesh.', NULL, 'Mr. Emon ( Merchadiser)', '+8801988806543', NULL, NULL, 'Ashulia Main Road,House No: 119, Block-D,Dhour,Turag, Dhaka-1230,Bangladesh.', NULL, 'Mr. Emon ( Merchadiser)', '+8801988806543', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 6),
(5, '8084', '49', 'ADOR COMPOSITE LTD.', 'ADOR', 'DARE2B', '1 C&B BAZZAR, GILLARCHALA, SREEPUR, GAZIPUR.', NULL, 'Mr. SHOJIB', '+880 18471275724', NULL, NULL, '1 C&B BAZZAR, GILLARCHALA, SREEPUR, GAZIPUR.', NULL, 'Mr. SHOJIB', '+880 18471275724', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 6),
(6, '9094', '49', 'FASHION POINT LTD.', 'FASHION', 'REGATTA', 'PLOT NO,: S- 41-42, BSCIC INDUSTRIAL ESTATE  KONABARI, GAZIPUR, BANGLADESH', NULL, 'Mr. Farhad', '+8801920872482', NULL, NULL, 'PLOT NO,: S- 41-42, BSCIC INDUSTRIAL ESTATE  KONABARI, GAZIPUR, BANGLADESH', NULL, 'Mr. Farhad', '+8801920872482', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(7, 'CENT-9092', '49', 'JB TEX BD', 'JB', 'REGATTA', '241, 243 SOUTH AZAMPUR DHAKHINKHAN, UTTARA, Dhaka, Bangladesh', NULL, 'MR. MAINUL', '+8801612335036', NULL, NULL, 'COMPANY :CENTEX TEXTILE & APPARELS LTD (JB TEX)                     ADDRESS : C.B.203/3 Kachukhet Puran Bazar, Dhaka Canttanment.               \r\nDHAKA-1206, BANGLADESH', NULL, 'MR. MAINUL', '+8801612335036', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(8, '9099', '49', 'Mojumdar Group', 'Mojumdar', 'REGATTA', '39 purana Pantan, Dhaka', NULL, 'Md.Al-amin', '+8801876034984', NULL, NULL, '113/1, Mudafa, Poschim Para, Tongi, Gazipur.', NULL, 'Md.Al-amin', '+8801876034984', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(9, '9097', '49', 'Polestar fashion design ltd', 'Polestar', 'REGATTA', 'Sharifpur, national university ,tongi, gazipur.', NULL, 'Mr. Akib Hasan', '+8801842662296', NULL, NULL, 'Address: Pole Star Knit Composite. Naibari, Mirer Bazar, Pubail, Gazipur', NULL, 'Mr. Akib Hasan', '+8801842662296', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(10, '9081', '49', 'DONGLIAN FASHION (BD) LTD.', 'DONGLIAN', 'REGATTA', 'HAZI RAFIZ UDDIN TOWER, JAMGORA, ASHULIA, DHAKA-1349', NULL, 'Shajadul Sohag', '+8801738646589', NULL, NULL, 'HAZI RAFIZ UDDIN TOWER, JAMGORA, ASHULIA, DHAKA-1349', NULL, 'Shajadul Sohag', '+8801738646589', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(11, '9080', '49', 'MITALI FASHIONS LTD.', 'MITALI', 'REGATTA', 'HATIMARA ROAD, BARENDA, KASHIMPUR, GAZIPUR CELL', NULL, 'Kamal Uddin Mamun', '+88 01713-478959', NULL, NULL, 'HATIMARA ROAD, BARENDA, KASHIMPUR, GAZIPUR CELL', NULL, 'Kamal Uddin Mamun', '+88 01713-478959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(12, '9090', '49', 'AKH STITCH ART LTD', 'AKH', 'REGATTA', 'AKH STITCH ART LTD    CANDANPUR, RAJFULBARI,HAMAYETPUR,SAVAR, DHAKA- 1340 6', NULL, 'SHYAMAL', '+8801713-47921', NULL, NULL, 'AKH STITCH ART LTD    CANDANPUR, RAJFULBARI,HAMAYETPUR,SAVAR, DHAKA- 1340', NULL, 'SHYAMAL', '+8801713-47921', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(13, '9100', '49', 'AHSIAR FASHIONS LTD', 'AHSIAR', 'REGATTA', '55, SHOVAPUR, RAJFULBARIA, SAVAR, DHAKA- 1347, SAVAR, DHAKA- 1347,', NULL, 'Mr. Masud Rana', '+8801713517049', NULL, NULL, '55, SHOVAPUR, RAJFULBARIA, SAVAR, DHAKA- 1347, SAVAR, DHAKA- 1347', NULL, 'Mr. Masud Rana', '+8801713517049', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(14, '9093', '49', 'K A FASHIONS LTD', 'K', 'REGATTA', 'K A FASHIONS LTD                  75 NO.Sataish Road,Sataish, Tongi,Gazipur-1712', NULL, 'Mr. Linkon', '+8801912150147', NULL, NULL, 'Company: K A DESIGN LTD Address: 75 NO.Sataish Road,Sataish,Tongi,Gazipur-1712', NULL, 'Mr. Linkon', '+8801912150147', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(15, '9095', '49', 'V&R FASHIONS LTD', 'V&R', 'REGATTA', 'V&R FASHIONS LTD  :PLOT# SA-434, RS-567 ENGR. ASHRAFUL BARI MANSION PEYARA BAGAN,VOGRA, GAZIPUR,BANGLADESH', NULL, 'Mr. Titu', '+8801755616447', NULL, NULL, 'V&R FASHIONS LTD  Address:PLOT# SA-434, RS-567 ENGR. ASHRAFUL BARI MANSION PEYARA BAGAN,VOGRA, P.O: NATIONAL UNIVERSITY  GAZIPUR,BANGLADESH', NULL, 'Mr. Titu', '+8801755616447', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(16, 'AKR-9091', '49', 'JAS TEX', 'JAS', 'REGATTA', 'JAS TEX Road:11, House:80/KA, Baridhara, Dhaka-1212,', NULL, 'MR. Mohidul Islam', '+8801701215901', NULL, NULL, 'Company: AKR FASHION LTD , 59/1, Darus Salam, Mirpur road. 209/227, KULGAON, BALUCHARA, Mirpur, Dhaka-1216,Bangladesh', NULL, 'MR. Mohidul Islam', '+8801701215901', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 7),
(17, '7072', '49', 'GEARS GROUP', 'GEARS', 'CRAGHOPPERS', 'CAPITAL  FASHIONS LTD.   \r\nPLOT NO.26 , BLOCK-B \r\nMASIMPUR,TONGI,               \r\nGAZIPUR-1702, BANGLADESH', NULL, 'Mukter', '+8801988459535', NULL, NULL, 'Kaichabari , Baipail, savar', NULL, 'Enamul', '+8801624158492', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 8),
(18, '7073', '49', '4 A YARN DYEING LTD.', '4', 'CRAGHOPPERS', 'Kaichabari , Baipail, savar', NULL, 'Enamul', '+8801624158492', NULL, NULL, 'GRAMTECH \r\nKnit Dyeing Finishing & Garments Ind. Ltd.\r\nDahargoan, Rupgonj, Narayangonj.\r\nBangladesh.', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 8),
(19, '', '49', 'Auko Tex Ltd.', 'Auko', 'CRAGHOPPERS', 'YEASIN KNITTEX IND. LTD. South Panishail, Zirani Bazar (BKSP), Kashimpur, Gazipur, Bangladesh', NULL, 'Mr. Faizur', '+8801955550143', NULL, NULL, 'YEASIN KNITTEX IND. LTD. South Panishail, Zirani Bazar (BKSP), Kashimpur, Gazipur, Bangladesh', NULL, 'Mr. Faizur', '+8801955550143', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 8),
(20, '', '49', 'GRAMTECH \r\nKnit Dyeing Finishing & Garments Ind. Ltd.', 'GRAMTECH', 'CRAGHOPPERS', 'GRAMTECH \r\nKnit Dyeing Finishing & Garments Ind. Ltd.\r\nDahargoan, Rupgonj, Narayangonj.\r\nBangladesh.', NULL, '', '', NULL, NULL, 'GRAMTECH \r\nKnit Dyeing Finishing & Garments Ind. Ltd.\r\nDahargoan, Rupgonj, Narayangonj.\r\nBangladesh.', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 8),
(21, '8197', '49', 'A.J. Super Garments  Ltd (Nassa Group)', 'A.J.', 'Zara TRF', 'A.J. Super Garments  Ltd\r\nGoshbag,  Zirabo,  Savar\r\nDhaka, Bangladesh', NULL, 'Mr. Shemul', '01717676582', NULL, NULL, 'Address: 1\r\nA.J. Super Garments  Ltd\r\nGoshbag,  Zirabo,  Savar\r\nDhaka, Bangladesh\r\nAddress: 2\r\nNassa Basic Garments Ltd. Goshbag, Zirabo,  Savar\r\nDhaka, Bangladesh', NULL, 'Mr. Shemul', '01717676582', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 9),
(22, '5520', '49', 'International Knitwear and Apparels Ltd.\r\n(Beximco)', 'International', 'Zara TRF', 'International Knitwear and Apparels Ltd.\r\nBeximco Industrial Park.\r\nSarabo, Kashimpur, Gazipur, Bangladesh.', NULL, 'Mr. Taimur', '01755533448', NULL, NULL, 'International Knitwear and Apparels Ltd.\r\nBeximco Industrial Park.\r\nSarabo, Kashimpur, Gazipur, Bangladesh.', NULL, 'Mr. Taimur', '01755533448', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 9),
(23, '9123', '49', 'Ananta Group', 'Ananta', 'Zara TRF', 'Ananta Apparels Ltd. 136,Elephant road, Dhaka-1205', NULL, 'Mr. Mahabubul Alam', '01840188426', NULL, NULL, 'Ananta Denim Technology Ltd.\r\nKhaspara, Kanchpur, Sonargaon,\r\nNarayangong-1431', NULL, 'Mr. Mahabubul Alam', '01840188426', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 9),
(24, 'N/A', '49', 'FGS DENIM WEAR', 'FGS', 'ECI', 'HOUSE-25,ROAD-01,HOUSE-25,SECTOR-13,UTTARA,DHAKA,BANGLADESH', NULL, 'Mr. CHARU', '01708831041', NULL, NULL, 'HOUSE-25,ROAD-01,HOUSE-25,SECTOR-13,UTTARA,DHAKA,BANGLADESH', NULL, 'Mr. CHARU', '01708831041', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 10),
(25, 'N/A', '49', 'GMS Composite Knitting Ind. Ltd.', 'GMS', 'ECI', 'House 110, Road 06, New DOHS\r\nMohakhali, Dhaka 1206, Bangladesh', NULL, 'Mr. HIMEL', '+8801755596969', NULL, NULL, 'Sardaganj, Kashimpur\r\nGazipur-1346, Bangladesh', NULL, 'Mr. HIMEL', '+8801755596969', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 10),
(26, 'N/A', '49', 'Tusuka Apparels Ltd.', 'Tusuka', 'ECI', 'Plot# B-91, BSCIC I/A, TONGI, GAZIPUR, 1710, BANGLADESH', NULL, 'RASEL', '+8801755518676', NULL, NULL, 'Plot# B-91, BSCIC I/A, TONGI, GAZIPUR, 1710, BANGLADESH', NULL, 'RASEL', '+8801755518676', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 10),
(27, 'N/A', '49', 'Fancy Fashion Sweaters Limited', 'Fancy', 'ECI', '1153-54, Shahid Siddique Road, Khailkur, Board Bazar', NULL, 'Mr. Amanat', '+88 01926084036', NULL, NULL, '1153-54, Shahid Siddique Road, Khailkur, Board Bazar', NULL, 'Mr. Amanat', '+88 01926084036', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 10),
(28, 'N/A', '49', 'GOOD RICH SWEATERS LIMITED', 'GOOD', 'ECI', 'Palashbari, Ashulia, Savar\r\nDhaka- 1349', NULL, 'Saiful Islam', '880 2 7791659', NULL, NULL, 'Palashbari, Ashulia, Savar\r\nDhaka- 1349', NULL, 'Saiful Islam', '880 2 7791659', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 10),
(29, 'N/A', '49', 'Glaxo Town', 'Glaxo', 'ECI', '20 Km Feroze Pure Road\r\n\r\nLahore-PAKISTAN (54600)', NULL, 'Mubasher Hassan', '+92 42 35457 342-4', NULL, NULL, '20 Km Feroze Pure Road\r\n\r\nLahore-PAKISTAN (54600)', NULL, 'Mubasher Hassan', '+92 42 35457 342-4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 10),
(30, 'N/A', '49', 'Fashion Asia Ltd.', 'Fashion', 'ECI', 'HOLDING NO.: 4/2-A, 135, GOPALPUR, MUNNU NAGAR, TONGI, GAZIPUR.', NULL, 'Md.IQBAL HOSSAIN', '+8801841400505', NULL, NULL, 'HOLDING NO.: 4/2-A, 135, GOPALPUR, MUNNU NAGAR, TONGI, GAZIPUR.', NULL, 'Md.IQBAL HOSSAIN', '+8801841400505', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 10),
(31, 'N/A', '49', 'DEKKO DESIGNS LTD', 'DEKKO', 'M&S', 'Noroshinghpur, Ashulia, Savar, Dhaka, Bangladesh.', NULL, 'Mr. Shahariar', '+88-01841296774', NULL, NULL, 'Noroshinghpur, Ashulia, Savar, Dhaka, Bangladesh.', NULL, 'Mr. Shahariar', '+88-01841296774', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 11),
(32, 'N/A', '49', 'Standard Stitches Ltd. (Unit-2),', 'Standard', 'M&S', 'Plot No. 10/4, Kornopara, Genda, Savar, Dhaka. Bangladesh.', NULL, 'Mr. Rayhanul Islam', '01723724123.', NULL, NULL, 'Plot No. 10/4, Kornopara, Genda, Savar, Dhaka. Bangladesh.', NULL, 'Mr. Rayhanul Islam', '01723724123.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 11),
(33, 'N/A', '49', 'Interstoff Apparels Ltd', 'Interstoff', 'M&S', 'Chandra, Kaliakoir, Gazipur, Bangladesh.', NULL, 'Md. Imranur Hasan', '+88 01709645894', NULL, NULL, 'Chandra, Kaliakoir, Gazipur, Bangladesh.', NULL, 'Md. Imranur Hasan', '+88 01709645894', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 11),
(34, 'N/A', '49', 'Dird Composite Textiles Limited', 'Dird', 'M&S', 'Shatiabari, Rajendrapur, Sreepur, Gazipur 1742, Bangladesh', NULL, 'Sumon Ahmed', '01844018256', NULL, NULL, 'Shatiabari, Rajendrapur, Sreepur, Gazipur 1742, Bangladesh', NULL, 'Sumon Ahmed', '01844018256', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 11),
(35, 'N/A', '49', 'Kenpark Bangladesh Apparel (Pvt) Limited,K-2', 'Kenpark', 'M&S', 'Kenpark Bangladesh Apparel (Pvt) Limited,K-2\r\nPlot No: 6985,Karnaphully Export Processing Zone, Chittagong 4204, Bangaladesh.', NULL, 'Mr.Bashu Deb', '+8801716155369', NULL, NULL, 'Kenpark Bangladesh Apparel (Pvt) Limited,K-2\r\nPlot No: 6985,Karnaphully Export Processing Zone, Chittagong 4204, Bangaladesh.', NULL, 'Mr.Bashu Deb', '+8801716155369', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 11),
(36, 'N/A', '49', 'Epyllion Style Ltd.', 'Epyllion', 'M&S', 'Epyllion Style Ltd.\r\nBahadurpur, P.O.-Vawal Mirzapur, Gazipur Sadar, Gazipur, Bangladesh', NULL, 'Md. Rafiqul Islam Hanif', '+88-01730725934', NULL, NULL, 'Epyllion Style Ltd.\r\nBahadurpur, P.O.-Vawal Mirzapur, Gazipur Sadar, Gazipur, Bangladesh', NULL, 'Md. Rafiqul Islam Hanif', '+88-01730725934', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 11),
(37, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'M&S', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Md. Jahangir', '+88 01716223319', NULL, NULL, 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Habib', '01688055619', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 11),
(38, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'Camicissima', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr.Rony', '+8801714838215', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Akter', '01720463159', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 12),
(39, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'DEBENHARMS', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr.D.M. Ismat Doha\r\nMr. Dipu', '+88 01724026853\r\n+8801673647341', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Belayet', '01721109088', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 13),
(40, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'Harmont & Blaine (H&B)', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr.Foysal\r\nMr.Rony', '+88 0167-5863223\r\n+88017174838215', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Rafiqul Islam', '01816600157', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 14),
(41, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'Jhon Lewies', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr.Yousuf-Al-Karim (Shimul)', '+88 01819-434600', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Belayet', '01721109088', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 15),
(42, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'Olymp', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'F.M.Shahidul Amin (Shahid)', '+88 01717428642\r\n/+88 01913655015', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Ruhel', '01987161935', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 16),
(43, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'SACOOR BROTHERS', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr.Foysal', '+88 0167-5863223', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Akter', '01720463159', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 17),
(44, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'TRENDY', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Md. Noor-A-Alam Siddiky (Rony)', '+8801714838215', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Akter', '01720463159', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 18),
(45, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'Interfab', 'Woolworth', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. MD.Shahadat Hossain\r\n/Md. Sujon mia', '+88 01787282020\r\n/+88 01680099349', NULL, NULL, 'Delivery Address:\r\nInterfab Shirt Manufacturing Limited\r\nVIYELLATEX group,Plot # 302/547 Kunia, Boro Bari,\r\nGasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Belayet', '01721109088', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 19),
(46, 'N/A', '49', 'Amternet Group', 'Amternet', 'ZARA BOYS', 'Power Vantage Wear Ltd\r\nKafrul Tower,234/8 Kachukhate,\r\nDhaka Cantonment,Dhaka - 1206,Bangladesh.\r\nBangladesh.', NULL, 'Mr. Rabiul Islam', '01844053734', NULL, NULL, 'Power Vantage Wear Ltd\r\nKafrul Tower,234/8 Kachukhate,\r\nDhaka Cantonment,Dhaka - 1206,Bangladesh.\r\nBangladesh.', NULL, 'Mr. Rabiul Islam', '01844053734', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 20),
(47, 'N/A', '49', 'Beximco', '', 'ZARA KIDS', 'Bextex Garments Ltd \r\nSARABO,  KASHIMPUR,  \r\nGAZIPUR,BANGLADESH .', NULL, 'Mr. Reza', '01713274346', NULL, NULL, 'Bextex Garments Ltd \r\nSARABO,  KASHIMPUR,  \r\nGAZIPUR,BANGLADESH .', NULL, 'Mr. Reza', '01713274346', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 21),
(48, 'N/A', '49', 'Indesore Trading', 'Indesore', 'ZARA KIDS', 'Tanaz Fashion Ltd\r\nShamim Complex, 216, Sataish Road, Gazipura, Tongi. Gazipur', NULL, 'Mr. Anas', '+880 1712044956', NULL, NULL, 'Indesore Trading Ltd\r\nUnion sports Ltd\r\n116 Pager-Morkon Link road\r\nPagar, Tongi, Gazipur Dhaka Bangladesh', NULL, 'Kamrul', '0196669918', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 21),
(50, 'N/A', '49', 'Brothers Fashion Ltd', 'Brothers', 'ZARA KIDS', 'Brothers Fashion Ltd\r\nHOLDING 01 , BLOCK-B, WARD 15,\r\nVOGRA BYPASS,\r\nGAZIPUR-1704,\r\nBangladesh', NULL, 'Mr. Momen', '01611845511', NULL, NULL, 'Brothers Fashion Ltd\r\nHOLDING 01 , BLOCK-B, WARD 15,\r\nVOGRA BYPASS,\r\nGAZIPUR-1704,\r\nBangladesh', NULL, 'Mr. Momen', '01611845511', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 21),
(51, 'N/A', '49', 'Haesong Korea Ltd', 'Haesong', 'ZARA KIDS', 'Han Complex\r\nBara Rangamatia, Zirabo,Saver,Dhaka Bangladesh', NULL, 'Mr. Maminul Islam', '01732439748', NULL, NULL, 'Han Complex\r\nBara Rangamatia, Zirabo,Saver,Dhaka Bangladesh', NULL, 'Mr. Maminul Islam', '01732439748', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 21),
(52, 'N/A', '49', 'EVITEX DRESS  SHIRT LTD', 'EVITEX', 'ADLER\r\n(V BY VERY)', 'Shirir Chala, Bhabanipur,Joydevpur,Gazipur,Bangladesh.', NULL, 'Mr.Razaunuzzaman', '01727715734', NULL, NULL, 'Shirir Chala, Bhabanipur,Joydevpur,Gazipur,Bangladesh.', NULL, 'Mr.Razaunuzzaman', '01727715734', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 22),
(53, 'N/A', '49', 'AKH ECO APPARELS LTD', 'AKH', 'ADLER', '495 BALITHA, SHAH-BELISHWER, DHAMRAL, DHAKA-1800', NULL, 'Mr. Keron', '01841359696', NULL, NULL, '495 BALITHA, SHAH-BELISHWER, DHAMRAL, DHAKA-1800', NULL, 'Mr. Keron', '01841359696', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(54, 'N/A', '49', 'DYNAMIC SWEATER IND LTD', 'DYNAMIC', 'ADLER', '10,GANDA,SAVAR,DHAKA', NULL, 'Mr. FARUK/OMAR', '+8801710782811', NULL, NULL, '10,GANDA,SAVAR,DHAKA', NULL, 'Mr. FARUK/OMAR', '+8801710782811', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(55, 'N/A', '49', 'APPAREL PLUS LIMITED.', 'APPAREL', 'ADLER', 'OLDING NO. INDUSTRY -18, DILAN COMPLEX\r\nHAKA ROAD, CHANDONA, CHOWRASTA,\r\nGAZIPUR-1702, BANGLADESH', NULL, 'Mr. Monojit', '01747219826', NULL, NULL, 'OLDING NO. INDUSTRY -18, DILAN COMPLEX\r\nHAKA ROAD, CHANDONA, CHOWRASTA,\r\nGAZIPUR-1702, BANGLADESH', NULL, 'Mr. Monojit', '01747219826', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(56, 'N/A', '49', 'The New Delta Apparels Ltd', 'The', 'ADLER', 'Delta tower, Plot no# 6,8,10, Road no 1/A, \r\nTurag Husing, Mohammadpur, Dhaka-1207', NULL, 'Mr.Amin', '01817047277', NULL, NULL, 'Delta tower, Plot no# 6,8,10, Road no 1/A, \r\nTurag Husing, Mohammadpur, Dhaka-1207', NULL, 'Mr.Amin', '01817047277', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(57, 'N/A', '49', 'Meek Sweater Ltd.', 'Meek', 'ADLER', 'Aouchpara Tongi Gazipur.', NULL, 'Mr. Amir', '01678333818', NULL, NULL, 'Aouchpara Tongi Gazipur.', NULL, 'Mr. Amir', '01678333818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(58, 'N/A', '49', 'NORBAN COMTEX. Ltd.', 'NORBAN', 'ADLER', 'Sarabo,Kashimpur.Gazipur, Bangladesh', NULL, 'Mr. Ashik Mahmud Rasel', '01915894590', NULL, NULL, 'Sarabo,Kashimpur.Gazipur, Bangladesh', NULL, 'Mr. Ashik Mahmud Rasel', '01915894590', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(59, 'N/A', '49', 'Piakash Fashion Ltd.', 'Piakash', 'ADLER', '519, Chatar (Choto Bari), POST-DUET, Gazipur-1700.', NULL, 'Ahmed Ali (Sohail)', '+880 1712110657', NULL, NULL, '519, Chatar (Choto Bari), POST-DUET, Gazipur-1700.', NULL, 'Ahmed Ali (Sohail)', '+880 1712110657', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(60, 'N/A', '49', 'SECTION SEVEN APPARELS LTD.', 'SECTION', 'ADLER', 'SECTION SEVEN APPARELS LTD.\r\nCEPZ, CHITTAGONG-4223', NULL, 'Md. Ayoub/ Manir.', '+88 01632352177', NULL, NULL, 'SECTION SEVEN APPARELS LTD.\r\nCEPZ, CHITTAGONG-4223', NULL, 'Md. Ayoub/ Manir.', '+88 01632352177', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(61, 'N/A', '49', 'SONIA & SWEATERS LTD.', 'SONIA', 'ADLER', 'Plot# 604, Kondolbag, Taibpur, Asulia road, Savar, Dhaka.', NULL, 'Mr. Ezaz', '01749285396', NULL, NULL, 'Plot# 604, Kondolbag, Taibpur, Asulia road, Savar, Dhaka.', NULL, 'Mr. Ezaz', '01749285396', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(62, 'N/A', '49', 'HORNBILL APPAREL LIMITED', 'HORNBILL', 'ADLER', 'S.A Plot # 310, Baimail,\r\nS.A Plot # 310, Baimail, P.O # Quashem Cotton Mills, Konabari, Dist # Gazipur.', NULL, 'Mr. Majahar', '01749643138', NULL, NULL, 'S.A Plot # 310, Baimail,\r\nS.A Plot # 310, Baimail, P.O # Quashem Cotton Mills, Konabari, Dist # Gazipur.', NULL, 'Mr. Majahar', '01749643138', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(63, 'N/A', '49', 'SECTION SEVEN INTERNATIONAL LTD.', 'SECTION', 'ADLER', 'SECTION SEVEN INTERNATIONAL LTD.\r\nPLOT# 1-14, SECTOR# 02, SFB# 02 (2DR FLOOR) BLOCK# A,\r\nUTTARA EPZ, NILPHAMARI,', NULL, 'Md. Ayoub/ Manir.', '+88 01761 493 991,\r\n+88 01818 25 09 35', NULL, NULL, 'SECTION SEVEN INTERNATIONAL LTD.\r\nPLOT# 1-14, SECTOR# 02, SFB# 02 (2DR FLOOR) BLOCK# A,\r\nUTTARA EPZ, NILPHAMARI,', NULL, 'Md. Ayoub/ Manir.', '+88 01761 493 991, \r\n+8801818 25 09 35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(64, 'N/A', '49', 'S.M.K GARMENT CO., LTD.', 'S.M.K', 'ADLER', 'PLOT NO.(104), (14) WARD PLOT, SHWETHANLWIN IND USTRIAL ZONE,\r\nHLAINGTHARYAR T/S, YANGON, MYANMAR.', NULL, 'GD STEVE /MS. LUCY', '95-1-685-225/705514', NULL, NULL, 'PLOT NO.(104), (14) WARD PLOT, SHWETHANLWIN IND USTRIAL ZONE,\r\nHLAINGTHARYAR T/S, YANGON, MYANMAR.', NULL, 'GD STEVE /MS. LUCY', '95-1-685-225/705514', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 23),
(65, '77565', '49', 'VISION APPARELS LTD.', 'VISION', 'C&A BRAZIL', 'Plot M-1/3, Section-14, Mirpur, Dhaka-1206, Bangladesh.', NULL, 'Mr. Manna\r\n&\r\nMr. Riyad', '+8801973483606\r\n&\r\n+8801973237906', NULL, NULL, 'Plot M-1/3, Section-14, Mirpur, Dhaka-1206, Bangladesh.\r\n&\r\nB-47, Purbo Rajashan, PS. Savar, Dhaka, Bangladesh', NULL, 'Mr. Manna\r\n&\r\nMr. Riyad', '+8801973483606\r\n&\r\n+8801973237906', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 24),
(66, '77521', '49', 'BHIS APPARELS LTD.', 'BHIS', 'C&A BRAZIL', '671, DATTA PARA, HOSSAIN MARKET, TONGI, GAZIPUR, BANGLADESH.', NULL, 'Mr. SIRAJ UDDIN \r\n&\r\nMr. Ashraf', '+8801683756094', NULL, NULL, '671, DATTA PARA, HOSSAIN MARKET, TONGI, GAZIPUR, BANGLADESH.', NULL, 'Mr. SIRAJ UDDIN \r\n&\r\nMr. Ashraf', '+8801683756094', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 24),
(67, '84708', '49', 'GARMENTS EXPORT VILLAGE LTD', 'GARMENTS', 'C&A BRAZIL', 'MAA TOWER, K.B.M Road, Tongi Industrial Area, Tongi, Gazipur.', NULL, 'Mr. Shafil Khan\r\n&\r\nMr. Rana\r\n&\r\nShaiqur Rahman', '+880l7 14242366\r\n&\r\n+8801830244315\r\n&\r\n+8801844053726', NULL, NULL, 'MAA TOWER, K.B.M Road, Tongi Industrial Area, Tongi, Gazipur.', NULL, 'Mr. Shafil Khan\r\n&\r\nMr. Rana', '+880l7 14242366\r\n&\r\n+8801830244315\r\n&\r\n+8801844053726', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 24),
(68, 'n/a', '49', 'Seowan Bangladesh Ltd', 'Seowan', 'C&A BRAZIL', 'Plot Number - 12, Section -1, Block - E,\r\nMirpur -1, Dhaka -1216, Bangladesh.', NULL, 'Mr. Taufiq', '+8801711-037865', NULL, NULL, 'Plot Number - 12, Section -1, Block - E,\r\nMirpur -1, Dhaka -1216, Bangladesh.', NULL, 'Mr. Taufiq', '+8801711-037865', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 24),
(69, '84791', '49', 'IMPRESS-NEWTEX COMPOSITE TEXTILES LTD', 'IMPRESS-NEWTEX', 'C&A BRAZIL', 'Gorai, Mirzapur, Tangail.', NULL, 'Mr. Probal Kumar\r\n&\r\nMd.Rasel', '+88 01716-244-077', NULL, NULL, 'Gorai, Mirzapur, Tangail.', NULL, 'Mr. Probal Kumar', '+88 01716-244-077', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 24),
(70, 'n/a', '49', 'Esquire Knit Composite Limited.', 'Esquire', 'C&A BRAZIL', 'Plot No.22/58, Kanchpur,Sonargaon, Narayangong, Bangladesh.', NULL, 'Ms. Zerin', '01714168544', NULL, NULL, 'Esquire Tower,21, Shaheed  Tajuddin  Ahmed Sarani, Tejgaon I/A,Dhaka – 1208, Bangladesh', NULL, 'Ms. Zerin', '01714168544', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 24),
(71, '77565', '49', 'VISION APPARELS LTD.', 'VISION', 'C&A MEXICO', 'Plot M-1/3, Section-14, Mirpur, Dhaka-1206, Bangladesh.', NULL, 'Mr. Manna\r\n&\r\nMr. Riyad', '+8801973483606\r\n&\r\n+8801973237906', NULL, NULL, 'Plot M-1/3, Section-14, Mirpur, Dhaka-1206, Bangladesh.\r\n&\r\nB-47, Purbo Rajashan, PS. Savar, Dhaka, Bangladesh', NULL, 'Mr. Manna\r\n&\r\nMr. Riyad', '+8801973483606\r\n&\r\n+8801973237906', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(72, '77521', '49', 'BHIS APPARELS LTD.', 'BHIS', 'C&A MEXICO', '671, DATTA PARA, HOSSAIN MARKET, TONGI, GAZIPUR, BANGLADESH.', NULL, 'Mr. SIRAJ UDDIN \r\n&\r\nMr. Ashraf', '+8801683756094', NULL, NULL, '671, DATTA PARA, HOSSAIN MARKET, TONGI, GAZIPUR, BANGLADESH.', NULL, 'Mr. SIRAJ UDDIN \r\n&\r\nMr. Ashraf', '+8801683756094', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(73, '84708', '49', 'GARMENTS EXPORT VILLAGE LTD', 'GARMENTS', 'C&A MEXICO', 'MAA TOWER, K.B.M Road, Tongi Industrial Area, Tongi, Gazipur.', NULL, 'Mr. Shafil Khan\r\n&\r\nMr. Rana\r\n&\r\nShaiqur Rahman', '+880l7 14242366\r\n&\r\n+8801830244315\r\n&\r\n+8801844053726', NULL, NULL, 'MAA TOWER, K.B.M Road, Tongi Industrial Area, Tongi, Gazipur.', NULL, 'Mr. Shafil Khan\r\n&\r\nMr. Rana', '+880l7 14242366\r\n&\r\n+8801830244315\r\n&\r\n+8801844053726', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(74, 'n/a', '49', 'Seowan Bangladesh Ltd', 'Seowan', 'C&A MEXICO', 'Plot Number - 12, Section -1, Block - E,\r\nMirpur -1, Dhaka -1216, Bangladesh.', NULL, 'Mr. Taufiq', '+8801711-037865', NULL, NULL, 'Plot Number - 12, Section -1, Block - E,\r\nMirpur -1, Dhaka -1216, Bangladesh.', NULL, 'Mr. Taufiq', '+8801711-037865', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(75, '77526', '49', 'Newage Garments Ltd.', 'Newage', 'C&A MEXICO', 'Nischintapur, Ashulia, Dhaka, Bangladesh.', NULL, 'Mr. Firoz Ahmed \r\n&\r\nShahabuddin Bhuiyan\r\n&\r\nMr. Mahabub\r\n&\r\nMr. Ruhil Amin', '+8801730 79 33 47 \r\n+8801730 793361\r\n+8801730793341\r\n+8801730793342', NULL, NULL, 'Nischintapur, Ashulia, Dhaka, Bangladesh.', NULL, 'Mr. Firoz Ahmed \r\n&\r\nShahabuddin Bhuiyan\r\n&\r\nMr. Mahabub\r\n&\r\nMr. Ruhil Amin', '+8801730 79 33 47 \r\n+8801730 793361\r\n+8801730793341\r\n+8801730793342', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(76, '84798', '49', 'Modele Group', 'Modele', 'C&A MEXICO', 'Talla Road, Kha-pur, Fatullah,Narayanganj, Bangladesh', NULL, 'Md.Monirul Haque', '+8801713-146866', NULL, NULL, 'Talla Road, Kha-pur, Fatullah,Narayanganj, Bangladesh', NULL, 'Md.Monirul Haque', '+8801713-146866', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(77, '77522', '49', 'Posh Garments Ltd.', 'Posh', 'C&A MEXICO', '348/A, Tejgaon Industrial Area, Dhaka- 1208, Bangladesh', NULL, 'Mr. Ifthekhar Rajit', '+88 01718127248', NULL, NULL, '348/A, Tejgaon Industrial Area, Dhaka- 1208, Bangladesh', NULL, 'Mr. Ifthekhar Rajit', '+88 01718127248', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(78, '84835', '49', 'Comptex Bangladesh Limited', 'Comptex', 'C&A MEXICO', 'Vhulta, Rupganj, Narayanganj', NULL, 'Mr. Amran', '+8801676589748', NULL, NULL, 'Vhulta, Rupganj, Narayanganj', NULL, 'Mr. Amran', '+8801676589748', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(79, '84791', '49', 'IMPRESS-NEWTEX COMPOSITE TEXTILES LTD', 'IMPRESS-NEWTEX', 'C&A MEXICO', 'Gorai, Mirzapur, Tangail.', NULL, 'Mr. Probal Kumar\r\n&\r\nMd.Rasel', '+88 01716-244-077', NULL, NULL, 'Gorai, Mirzapur, Tangail.', NULL, 'Mr. Probal Kumar', '+88 01716-244-077', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(80, '77545', '49', 'DEBONAIR LTD.', 'DEBONAIR', 'C&A MEXICO', 'Gorat Ashulia, Savar', NULL, 'Mr. Shihan Mahamud', '+8801998700745', NULL, NULL, 'Gorat Ashulia, Savar', NULL, 'Mr. Shihan Mahamud', '+8801998700745', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 25),
(82, '', '49', 'APEX LINGERIE LIMITED', 'APEX', 'Primark', 'APEX LINGERIE LIMITED, CHANDORA, KALIAKOIR, GAZIPUR,BANGLADESH.', NULL, 'Mr. Socretes', '01712743161', NULL, NULL, 'APEX LINGERIE LIMITED, CHANDORA, KALIAKOIR, GAZIPUR,BANGLADESH.', NULL, 'Mr. Socretes', '01712743161', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(83, '', '49', 'Dressmen', '', 'Primark', 'Dressmen Limited, 18 Erectors House, Kamal Ataturk Avenue,Banani, Dhaka', NULL, 'MR. Ashraf', '01835565818', NULL, NULL, 'Dressmen  Ltd, Pallirani, Savar Tangail connecting Road, Shahbajpur, Kaliakoir, Gazipur -1750', NULL, 'MR. Ashraf', '01835565818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(84, '', '49', 'Cassiopea Apparels Ltd', 'Cassiopea', 'Primark', 'BORKAN MONIPUR HOTAPARA, MIRZAPUR UNION, P.S: JOYDEVPUR, GAZIPUR, BANGLADESH.', NULL, 'Mr.NIPEN', '01776198455', NULL, NULL, 'BORKAN MONIPUR HOTAPARA, MIRZAPUR UNION, P.S: JOYDEVPUR, GAZIPUR, BANGLADESH.', NULL, 'Mr.NIPEN', '01776198455', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(86, '', '49', 'ABAAN TEX', 'ABAAN', 'Primark', 'ABAAN TEX, House-91, Road-3/A, Mirpur DOHS, DHAKA-1216', NULL, 'Mr. Nazir Uddin', '01712120487', NULL, NULL, 'ABAAN TEX, House-91, Road-3/A, Mirpur DOHS, DHAKA-1216', NULL, 'Mr. Nazir Uddin', '01712120487', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(87, '', '49', 'Four H Group', 'Four', 'Primark', 'Four H Lingerie Ltd.BRTC Bus Depot, Baluchara,Hathazari Road, Chittagong', NULL, 'Ms. Jinat', '01610959101', NULL, NULL, 'FOUR H , Central store2, North Kattoli ware house , 590 North Colonel Jone Sorok (Besides Cumki Garments),', NULL, 'Ms. Jinat', '01610959101', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(89, '', '49', 'NATURAL SWEATER', 'NATURAL', 'Primark', 'NATURAL SWEATER VILLAGE LTD. ,Raju Tower-Gouripur-Asulia, Savar-Dhaka-1341', NULL, 'Mr. Tareq', '01718749089', NULL, NULL, 'NATURAL SWEATER VILLAGE LTD. ,Raju Tower-Gouripur-Asulia, Savar-Dhaka-1341', NULL, 'Mr. Tareq', '01718749089', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(90, '', '49', 'Simple Approach', 'Simple', 'Primark', 'Tunic Apparels Ltd. Plot No.M-4/3, Road # 07, Section # 07, Mirpur Industrial Area, Dhaka-1216,  Bangladesh.', NULL, 'Mr. Shohag', '01776484887', NULL, NULL, 'Tunic Apparels Ltd. Plot No.M-4/3, Road # 07, Section # 07, Mirpur Industrial Area, Dhaka-1216,  Bangladesh.', NULL, 'Mr. Shohag', '01776484887', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(94, '', '49', 'STARLIGHT SWEATERS LIMITED', 'STARLIGHT', 'Primark', 'STARLIGHT SWEATERS LIMITED,HOLDING 94 ,VOGRA NATIONAL UNIVERSITY , GAZIPUR SADAR ,GAZIPUR, BANGLADESH,', NULL, 'Mr. MAMUNUR RAHMAN', '01715699654', NULL, NULL, 'STARLIGHT SWEATERS LIMITED,HOLDING 94 ,VOGRA NATIONAL UNIVERSITY , GAZIPUR SADAR ,GAZIPUR, BANGLADESH,', NULL, 'Mr. MAMUNUR RAHMAN', '01715699654', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(95, '', '49', 'Golden Stitch Design Limited', 'Golden', 'Primark', 'Golden Stitch Design Limited,Rajaghat , Rajfulbaria,Saver,Dhaka,Bangladesh.Tel: 01819-167479', NULL, 'Mr. Ripon', '01680119276', NULL, NULL, 'Golden Stitch Design Limited,Rajaghat , Rajfulbaria,Saver,Dhaka,Bangladesh.Tel: 01819-167479', NULL, 'Mr. Ripon', '01680119276', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(96, '', '49', 'Shin Shin/Organic/Vancot', 'Shin', 'Primark', 'VANCOT LIMITED ,PLOT NO. 18-20 , SECTOR # 03,KEPZ , NORTH PATENGA ,CHITTAGONG , BANGLADESH.', NULL, 'Abu Sufian', '8801777781841', NULL, NULL, 'VANCOT LIMITED ,PLOT NO. 18-20 , SECTOR # 03,KEPZ , NORTH PATENGA ,CHITTAGONG , BANGLADESH.', NULL, 'Abu Sufian', '8801777781841', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(100, '', '49', 'Ananta Apparels Limited', 'Ananta', 'Primark', 'Ananta Apparels Limited,Ananta Plaza,136, Elephant Road, Dhaka', NULL, '', '', NULL, NULL, 'Ananta Denim Technology LTD,Noyabari, Kanchpur,Sonargoan,Narayanganj, Bangladesh.', NULL, 'Firoz', '88 018 19642190', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(102, '', '49', 'M/S DOREEN GARMENTS LTD.', 'M/S', 'Primark', 'M/S DOREEN GARMENTS LTD.  \r\n50-60, DHAKKHIN PANISHAIL,  \r\nN.K. LINK ROAD, GAZIPUR, BANGLADESH.', NULL, 'Mr. Hrishikesh', '01710310486 /01717642446', NULL, NULL, 'M/S DOREEN GARMENTS LTD.  \r\n50-60, DHAKKHIN PANISHAIL,  \r\nN.K. LINK ROAD, GAZIPUR, BANGLADESH.', NULL, 'Mr. Hrishikesh', '01710310486 /01717642446', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(103, '', '49', 'NATURAL SWEATER', 'NATURAL', 'Primark', 'NATURAL SWEATER VILLAGE LTD. ,Raju Tower-Gouripur-Asulia, Savar-Dhaka-1341', NULL, 'Mr. Tareq', '01718749089', NULL, NULL, 'NATURAL SWEATER VILLAGE LTD. ,Raju Tower-Gouripur-Asulia, Savar-Dhaka-1341', NULL, 'Mr. Tareq', '01718749089', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(104, '', '49', 'Simple Approach', 'Simple', 'Primark', 'Tunic Apparels Ltd. Plot No.M-4/3, Road # 07, Section # 07, Mirpur Industrial Area, Dhaka-1216,  Bangladesh.', NULL, 'Mr. Shohag', '01776484887', NULL, NULL, 'Tunic Apparels Ltd. Plot No.M-4/3, Road # 07, Section # 07, Mirpur Industrial Area, Dhaka-1216,  Bangladesh.', NULL, 'Mr. Shohag', '01776484887', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(108, '', '49', 'STARLIGHT SWEATERS LIMITED', 'STARLIGHT', 'Primark', 'STARLIGHT SWEATERS LIMITED,HOLDING 94 ,VOGRA NATIONAL UNIVERSITY , GAZIPUR SADAR ,GAZIPUR, BANGLADESH,', NULL, 'Mr. MAMUNUR RAHMAN', '01715699654', NULL, NULL, 'STARLIGHT SWEATERS LIMITED,HOLDING 94 ,VOGRA NATIONAL UNIVERSITY , GAZIPUR SADAR ,GAZIPUR, BANGLADESH,', NULL, 'Mr. MAMUNUR RAHMAN', '01715699654', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(109, '', '49', 'A&A GROUP', 'A&A', 'Primark', 'Haribari  Tak , Pubail Collage Gate, Pubail , Gazipur -1721, BANGLADESH', NULL, '', '', NULL, NULL, 'Haribari  Tak , Pubail Collage Gate, Pubail , Gazipur -1721, BANGLADESH', NULL, 'Mr. Hasan', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(111, '', '49', 'Shinest', '', 'Primark', '166-168 MAIN ROAD, HAZI DIL MOHAMMAD AVENUE, DHAKA UDDAN, MOHAMMADPUR, DHAKA-1207, BANGLADESH', NULL, 'Mr. Rasel', '01713274282', NULL, NULL, '166-168 MAIN ROAD, HAZI DIL MOHAMMAD AVENUE, DHAKA UDDAN, MOHAMMADPUR, DHAKA-1207, BANGLADESH', NULL, 'Mr. Rasel', '01713274282', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(114, '', '49', 'Mouchak knit composite Ltd.', 'Mouchak', 'Primark', 'Mouchak,kaliakoir,Gazipur.', NULL, 'Mr. BILLAL', '+8801719212612', NULL, NULL, 'Mouchak,kaliakoir,Gazipur.', NULL, 'Mr. BILLAL', '+8801719212612', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(115, '', '49', 'CHAITY COMPOSITE LIMITED', 'CHAITY', 'Primark', 'CHAITY COMPOSITE LIMITED.\r\nCHOTTO SILMONDI, TRIPURDEE,\r\nSONARGAON,', NULL, 'Mr. Rabbi', '+8801714-637818', NULL, NULL, 'CHAITY COMPOSITE LIMITED.\r\nCHOTTO SILMONDI, TRIPURDEE,\r\nSONARGAON,', NULL, 'Mr. Rabbi', '+8801714-637818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(116, '', '49', 'Fashion Forum LTD', 'Fashion', 'Primark', 'Fashion Forum LTD\r\n53-54, Zamgara,\r\nSavar, Dhaka , bangladesh', NULL, 'Attn : Mr.Faruqe', '01729072028', NULL, NULL, 'Fashion Forum LTD\r\n53-54, Zamgara,\r\nSavar, Dhaka , bangladesh', NULL, 'Attn : Mr.Faruqe', '01729072028', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(117, '', '49', 'Mehnaz Styles & Craft Ltd.', 'Mehnaz', 'Primark', 'Mehnaz Styles & Craft Ltd. \r\nAshulia, Dhaka-1341, Bangladesh \r\nBangabandhu Road, Tongabari,', NULL, 'Mr. Srabon Alam', 'Cell: :8801617089000', NULL, NULL, 'Mehnaz Styles & Craft Ltd. \r\nAshulia, Dhaka-1341, Bangladesh \r\nBangabandhu Road, Tongabari,', NULL, 'Mr. Srabon Alam', 'Cell: :8801617089000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(118, '', '49', 'HASAN TANVIR FASHION WEARS LTD', 'HASAN', 'Primark', 'HASAN TANVIR FASHION WEARS LTD,\r\nJOYDEVPUR ROAD, GAZIPUR\r\nPLOT # 397 , CHANDONA CHOWRASTA', NULL, 'Mr. KAFI', '+8801913440653', NULL, NULL, 'HASAN TANVIR FASHION WEARS LTD,\r\nJOYDEVPUR ROAD, GAZIPUR\r\nPLOT # 397 , CHANDONA CHOWRASTA', NULL, 'Mr. KAFI', '+8801913440653', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(119, '', '49', 'Soorty Textiles (BD) Ltd.', 'Soorty', 'Primark', 'Soorty Textiles (BD) Ltd.\r\nPlot: 220-227.\r\nComilla EPZ,', NULL, 'Mr. Syed', '+8801914-464909', NULL, NULL, 'Soorty Textiles (BD) Ltd.\r\nPlot: 220-227.\r\nComilla EPZ,', NULL, 'Mr. Syed', '+8801914-464909', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(120, '', '49', 'Esquire Knit Composite Limited.', 'Esquire', 'Primark', 'Esquire Knit Composite Limited., \r\n22/58,Kanchpur,Sonargaon.\r\nNarayangonj, Bangladesh.', NULL, 'Mr. Mizan, Manager (Store)', '01730009926', NULL, NULL, 'Esquire Knit Composite Limited., \r\n22/58,Kanchpur,Sonargaon.\r\nNarayangonj, Bangladesh.', NULL, 'Mr. Mizan, Manager (Store)', '01730009926', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 5),
(121, 'N/A', '49', 'VIYELLATEX Limited', 'VIYELLATEX Limited', 'Aldi', 'VIYELLATEX Limited 297, Khairtul, Gazipura, Tongi, Gazipur-1712, Bangladesh', NULL, 'Md. Hedayet Ullah', '+88 01717094526', NULL, NULL, 'VIYELLATEX Limited 297, Khairtul, Gazipura, Tongi, Gazipur-1712, Bangladesh', NULL, 'Md. Hedayet Ullah', '+88 01717094526', NULL, NULL, NULL, NULL, NULL, '2018-09-20 02:45:33', '2018-09-20 02:45:33', '1', 38),
(122, 'ET-RFA', '49', 'RISING  FASHION LTD.', 'RFL', 'Bershka', 'E`TIKET INTERNATIONAL GROUP                                                                                                                                                                                                               Holding # 482, Road #', NULL, 'Mr. Jakaria', '+88 07155509005', NULL, NULL, 'RISING  FASHION LTD. PLOT# I/10, BLOCK - K, RUPNAGAR I/A, SECTION - 2, MIRPUR, DHAKA - 1216', NULL, 'Mr. Sobur (Store  Manager)', NULL, NULL, NULL, NULL, NULL, NULL, '2018-09-20 02:52:40', '2018-09-20 02:52:40', '1', 35),
(125, '694', '49', 'TEXEUROP BD LTD', 'TBL', 'Sportsmaster', 'VOGRA,JOYDEBPUR,GAZIPUR', NULL, 'Mr. Tareq/ Jahanggir/ Ershad', '+8801978686876', NULL, NULL, 'VOGRA,JOYDEBPUR,GAZIPUR', NULL, 'Mr. Tareq/ Jahanggir/ Ershad', '+880 161 9003567', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:15:49', '2018-09-20 03:15:49', '1', 28),
(126, NULL, '49', 'EVER SMART BANGLADESH LTD.', 'ESB', 'Puma', 'Begumpur, PO: Bhabanipur, Hotapara,Gazipur-1740.', NULL, 'Mr.  Afsarul Islam', '+8801847-285709', NULL, NULL, 'Begumpur, PO: Bhabanipur, Hotapara,Gazipur-1740.', NULL, NULL, '01847-285709', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:17:18', '2018-09-20 03:17:18', '1', 29),
(127, '78450', '49', 'BIRDS A & Z LTD', 'A&Z', 'Voice', 'BIRDS A & Z LTD 113, Baipail, Ashulia, Savar, Dhaka 1349 Dhaka Contract Person: Kamrul Hasan Cell No: 01915-397699 E-mail: hasan-merch@birds-group.com', NULL, 'Kamrul Hasan  E-mail: hasan-merch@birds-group.com', '01915-397699', NULL, NULL, 'BIRDS A & Z LTD 113, Baipail, Ashulia, Savar, Dhaka 1349 Dhaka Contract Person: Kamrul Hasan Cell No: 01915-397699 E-mail: hasan-merch@birds-group.com', NULL, 'Kamrul Hasan  E-mail: hasan-merch@birds-group.com', '01915-397699', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:19:04', '2018-09-20 03:19:04', '1', 30),
(128, NULL, '49', 'Asrotex', 'AS', 'Splash', 'Dharmagang, Enayetnagar, Fatullah NARAYANGANJ', NULL, 'Mr.  Pijush', '+880 1755642861', NULL, NULL, 'Dharmagang, Enayetnagar, Fatullah NARAYANGANJ', NULL, 'Mr.  Pijush', '+880 1755642861', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:21:35', '2018-09-20 03:21:35', '1', 31),
(129, NULL, '49', 'Multi Sourcing Asia Ltd.', 'MSAL', 'Bjorn Born', 'Multi Sourcing Asia Ltd.                       Hosna Center(5th Floor), 106 Gulshan Avenue, Dhaka 1212.Bangladesh.', NULL, 'Shakhawat Taluckder Samol', '+ 8801728745480, +880-1730303922', NULL, NULL, 'Base Textile limited                                             106 Gulshan Avenue, Dhaka 1212.Bangladesh.', NULL, 'Mr.Rokon,', '0171910478', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:23:04', '2018-09-20 03:23:04', '1', 32),
(130, NULL, '49', 'Comfit Composite Knit Ltd', 'CCKL', 'Eagle Bert', 'Youth Tower, 822/2 Rokeys Sharani,  Dhaka-1216, Bangladesh', NULL, 'Monjur Uddin Rasel', '+880 182 7777578', NULL, NULL, 'Youth Tower, 822/2 Rokeys Sharani,  Dhaka-1216, Bangladesh', NULL, 'Monjur Uddin Rasel', '+880 182 7777578', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:25:34', '2018-09-20 03:25:34', '1', 33),
(131, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'ISML', 'Camicissima', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr.Rony', '+8801714838215', NULL, NULL, 'Delivery Address: Interfab Shirt Manufacturing Limited VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Akter', '01720463159', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:27:36', '2018-09-20 03:27:36', '1', 12),
(132, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'ISML', 'DEBENHARMS', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr.D.M. Ismat Doha Mr. Dipu', '+88 01724026853 +8801673647341', NULL, NULL, 'Delivery Address: Interfab Shirt Manufacturing Limited VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Belayet', '01721109088', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:29:44', '2018-09-20 03:29:44', '1', 13),
(133, 'N/A', '49', 'Beximco', 'BC', 'ZARA KIDS', 'Bextex Garments Ltd  SARABO,  KASHIMPUR,   GAZIPUR,BANGLADESH .', NULL, 'Mr. Reza', '01713274346', NULL, NULL, 'Bextex Garments Ltd  SARABO,  KASHIMPUR,   GAZIPUR,BANGLADESH .', NULL, 'Mr. Reza', '01713274346', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:32:14', '2018-09-20 03:32:14', '1', 21),
(134, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'ISML', 'M&S', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Md. Jahangir', '+88 01716223319', NULL, NULL, 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Habib', '01688055619', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:34:31', '2018-09-20 03:34:31', '1', 11),
(135, 'N/A', '49', 'Interfab Shirt Manufacturing Limited', 'ISML', 'Woolworth', 'VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. MD.Shahadat Hossain /Md. Sujon mia', '+88 01787282020 /+88 01680099349', NULL, NULL, 'Delivery Address: Interfab Shirt Manufacturing Limited VIYELLATEX group,Plot # 302/547 Kunia, Boro Bari, Gasa Union, P.0. National University, Gazipur, Bangladesh', NULL, 'Mr. Belayet', '01721109088', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:36:26', '2018-09-20 03:36:26', '1', 19),
(136, 'N/A', '49', 'EVITEX APPARELS LTD', 'EAL', 'ADLER', 'Shirir Chala, Bhabanipur,Joydevpur,Gazipur,Bangladesh.', NULL, 'Mr.Razaunuzzaman', '01727715734', NULL, NULL, 'Shirir Chala, Bhabanipur,Joydevpur,Gazipur,Bangladesh.', NULL, 'Mr.Razaunuzzaman', '01727715734', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:38:07', '2018-09-20 03:38:07', '1', 23),
(137, NULL, '49', 'Leeu Fashion Ltd.', 'LFL', 'Jay Jay', 'Meharabari, Hazir Bazar Valuka, Mymensingh', NULL, 'Mr. Shuvo', '1795098105', NULL, NULL, 'Meharabari, Hazir Bazar Valuka, Mymensingh', NULL, 'Mr. Rajon', '1686719396', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:40:51', '2018-09-20 03:40:51', '1', 37),
(138, NULL, '49', 'BIG BOSS CORPORATION LTD', 'BBCL', 'Jay Jay', 'Holding# 30, Sharabo, Kasimpur, Gazipur', NULL, 'Ms. Afroza', '1714073242', NULL, NULL, 'Holding# 30, Sharabo, Kasimpur, Gazipur', NULL, 'Ishita-', '+88 0 172 2984330', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:42:05', '2018-09-20 03:42:05', '1', 37),
(139, 'N/A', '49', 'SECTION SEVEN INTERNATIONAL LTD.', 'SSIL', 'ADLER', 'SECTION SEVEN INTERNATIONAL LTD. PLOT# 1-14, SECTOR# 02, SFB# 02 (2DR FLOOR) BLOCK# A, UTTARA EPZ, NILPHAMARI,', NULL, 'Md. Ayoub/ Manir.', '+88 01761 493 991, +88 01818 25 09 35', NULL, NULL, 'SECTION SEVEN INTERNATIONAL LTD. PLOT# 1-14, SECTOR# 02, SFB# 02 (2DR FLOOR) BLOCK# A, UTTARA EPZ, NILPHAMARI,', NULL, 'Md. Ayoub/ Manir.', '+88 01761 493 991,  +8801818 25 09 35', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:44:21', '2018-09-20 03:44:21', '1', 23),
(142, 'RIL', '49', 'RADIAL INTERNATIONLA LTD.', 'RIL', 'Bershka', 'Radial International Ltd. Unit -2 Zirani Bazar, Kashimpur, Gazipur, (Near BKSP),Bangladesh.', NULL, 'Mr.  Foysal', '+88  01717077148', NULL, NULL, 'Radial International Ltd. Unit -2 Zirani Bazar, Kashimpur, Gazipur, (Near BKSP),Bangladesh.', NULL, 'Mr.  Foysal', '+88  01717077148', NULL, NULL, NULL, NULL, NULL, '2018-09-20 03:57:52', '2018-09-20 03:57:52', '1', 35),
(143, '', '49', 'Multi Sourcing Asia Ltd.', 'Multi', 'Bjorn Born', 'Multi Sourcing Asia Ltd.                       Hosna Center(5th Floor),\r\n106 Gulshan Avenue, Dhaka 1212.Bangladesh.', NULL, 'Shakhawat Taluckder Samol', 'Tel: + 8801728745480, +880-1730303922', NULL, NULL, 'Base Textile limited                                             106 Gulshan Avenue, Dhaka 1212.Bangladesh.', NULL, 'Attn: Mr.Rokon,', 'Tel No.:  0171910478', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 32),
(144, '', '49', 'Multi Sourcing Asia Ltd.', 'Multi', 'Bjorn Born', 'Base Textile limited                        106 Gulshan Avenue, Dhaka 1212.Bangladesh.', NULL, 'Attn: Mr.Rokon,', 'Tel No.:  0171910478', NULL, NULL, 'Base Textile limited                                                 9, CDA I/A, Alamin baria, Kalurghat, Chittagong 4221, Bangladesh.', NULL, 'Attn: Mr.Rokon,', 'Tel No.:  0171910478', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 32),
(145, '', '49', 'Multi Sourcing Asia Ltd.', 'Multi', 'Bjorn Born', '', NULL, '', '', NULL, NULL, 'Multi Sourcing Asia Ltd.                                     Hosna Center(5th Floor),\r\n106 Gulshan Avenue, Dhaka 1212.Bangladesh.', NULL, 'Shakhawat Taluckder Samol', 'Tel: + 8801728745480, +880-1730303922', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 32),
(146, '', '49', 'Comfit Composite Knit Ltd', 'Comfit', 'Eagle Bert', 'Youth Tower, 822/2 Rokeys Sharani,  Dhaka-1216, Bangladesh', NULL, 'Monjur Uddin Rasel', '+880 182 7777578', NULL, NULL, 'Youth Tower, 822/2 Rokeys Sharani,  Dhaka-1216, Bangladesh', NULL, 'Monjur Uddin Rasel', 'Tel No.: +880 182 7777578', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 33),
(147, '', '49', 'Comfit Composite Knit Ltd', 'Comfit', 'Eagle Bert', 'Youth Tower, 822/2 Rokeys Sharani,  Dhaka-1216, Bangladesh', NULL, 'Monjur Uddin Rasel', '+880 182 7777578', NULL, NULL, 'Comfit Composite Knit Ltd. Unit-2, Bishmail Road, Aamtola, Kathgora, Jirabo, Ashulia, Savar, Dhaka', NULL, 'Mr Jillur (Store Incharge)', 'Tel No.: +880 1716586518', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 33),
(148, '', '49', 'EVER SMART BANGLADESH LTD.', 'EVER', 'Puma', 'Begumpur, PO: Bhabanipur, Hotapara,Gazipur-1740.', NULL, 'Mr.  Afsarul Islam', 'Tel: +8801847-285709', NULL, NULL, 'Begumpur, PO: Bhabanipur, Hotapara,Gazipur-1740.', NULL, 'Mr.  Afsarul Islam', 'Tel: +01847-285709', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 29),
(149, '', '49', 'DBL Group/Jinnat Knitwears Limited', 'DBL', 'Puma', 'PUMA Way 1, Herzogenaurach, 91074 Germany', NULL, 'Edmond Chan', '', NULL, NULL, 'Sardaganj, Kashimpur, Gazipur, Bangladesh.', NULL, 'Reza selim', 'Tel: +88 01756-142404', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 29);
INSERT INTO `mxp_party` (`id`, `party_id`, `user_id`, `name`, `sort_name`, `name_buyer`, `address_part1_invoice`, `address_part2_invoice`, `attention_invoice`, `mobile_invoice`, `telephone_invoice`, `fax_invoice`, `address_part1_delivery`, `address_part2_delivery`, `attention_delivery`, `mobile_delivery`, `telephone_delivery`, `fax_delivery`, `description_1`, `description_2`, `description_3`, `created_at`, `updated_at`, `status`, `id_buyer`) VALUES
(150, '', '49', 'Shanta Denims Limited', 'Shanta', 'Puma', 'Plot No 156 & 177; DEPZ Extension Area; Ganakbari, Savar; Dhaka-1349', NULL, 'Md. Zahid Hasan', 'Tel: +8801680-608450', NULL, NULL, 'Plot No 156 & 177; DEPZ Extension Area; Ganakbari, Savar; Dhaka-1349', NULL, 'Md. Zahid Hasan', 'Tel: +8801680-608450', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 29),
(151, '', '49', 'URMI GROUP (Head Office)', 'URMI', 'Puma', 'Sam Tower, Road# 22, House# 04, Level# 5, Gulshan# 1', NULL, 'Mr. Nazmus Sakib', 'Tel: +88 01844190478', NULL, NULL, 'Sam Tower, Road# 22, House# 04, Level# 5, Gulshan# 1', NULL, 'Mr. Nazmus Sakib', 'Tel: +88 01844190478', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 29),
(152, '', '49', 'VIYELLATEX Limited', 'VIYELLATEX', 'Puma', '297, Khortoil, Gazipura, Tongi, Gazipur-1712', NULL, 'Mr. Abusadat Mohammad Sayem', 'Tel: +8801716809734', NULL, NULL, '297, Khortoil, Gazipura, Tongi, Gazipur-1712', NULL, 'Mr. Abusadat Mohammad Sayem', 'Tel: +01716809734', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 29),
(153, '', '49', 'Asrotex', '', 'Splash', 'Dharmagang, Enayetnagar, Fatullah NARAYANGANJ', NULL, 'Mr.  Pijush', 'Tel No.:  +880 1755642861', NULL, NULL, 'Dharmagang, Enayetnagar, Fatullah NARAYANGANJ', NULL, 'Mr.  Pijush', 'Tel No.:  +880 1755642861', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(154, '', '49', 'Basic Shirts Limited', 'Basic', 'Splash', 'Pashchim Para, Mazukhan, Station Road, Tongi, Gazipur', NULL, 'Mr. Suhel Ahammed', 'Tel No.:  +8801716914047', NULL, NULL, 'Pashchim Para, Mazukhan, Station Road, Tongi, Gazipur', NULL, 'MR. Suhel Ahammed', 'Tel No.:  +8801716914047', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(155, '', '49', 'Fariha Knit Tex Ltd', 'Fariha', 'Splash', 'West Masdair, Baroibogh, Enayet Nagar, Fatullah NARAYANGANJ', NULL, 'Mr. Robin', 'Tel No.:  +88 01787673724', NULL, NULL, 'West Masdair, Baroibogh, Enayet Nagar, Fatullah NARAYANGANJ', NULL, 'Mr. Robin', 'Tel No.:  +88 01787673724', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(156, '', '49', 'GBX TRADING FZE', 'GBX', 'Splash', 'JAFZA VIEW 18, OFFICE NO.LB, 18/1803.18TH, FLOOR,P.O.BOX:263281JEBEL ALI,DUBAI.U.A.E.', NULL, 'Mr. Murari', '97148847085', NULL, NULL, 'Rahman Regnum Center (6th Floor), 191/B, Tejgaon Gulshan Link Road, Tejgaon, Dhaka-1208, Bangladesh', NULL, 'Mr. Yeasir Arafat', 'Cell: +8801844148150', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(157, '', '49', 'P. M. KNITTEX (PVT.) LTD.', 'P.', 'Splash', 'CHOWDHURY BARI, GODHNAIL, NARAYANGANJ 1400, BANGLADESH', NULL, 'MR. Anwar', 'Tel No.:  +880 01918979471', NULL, NULL, 'CHOWDHURY BARI, GODHNAIL, NARAYANGANJ 1400, BANGLADESH', NULL, 'MR. Anwar', 'Tel No.:  +880 01918979471', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(158, '', '49', 'PN. Composite.Ltd', 'PN.', 'Splash', 'AMBAG KONABARI, GAZIPUR', NULL, 'Mr. Zahid', 'Phn: +8801713387649', NULL, NULL, 'AMBAG KONABARI, GAZIPUR', NULL, 'Mr.Zahid', 'Phn: +8801713387649', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(159, '', '49', 'ALIF CASUAL WEAR LTD', 'ALIF', 'Splash', '69, Vogra, Gazipur Sadar, Gazipur-1704, Dhaka, Bangladesh.', NULL, 'Mr. Ruhul', 'Cell: +8801674172885', NULL, NULL, '69, Vogra, Gazipur Sadar, Gazipur-1704, Dhaka, Bangladesh.', NULL, 'Mr. Ruhul', 'Cell: +8801674172885', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(160, '', '49', 'DK Textile Ltd', 'DK', 'Splash', 'Zamgara Bazar, Savar, Dhaka', NULL, '', '', NULL, NULL, 'Zamgara Bazar, Savar, Dhaka', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(161, '', '49', 'SM KNITWARE LTD', 'SM', 'Splash', 'SM Tower, House Building, Uttara', NULL, 'Mr. Nazmul', 'Tel No.: +880 01678420515', NULL, NULL, 'SM Tower, House Building, Uttara', NULL, 'MR.Nazmul', 'Tel No.: +880 01678420515', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 31),
(162, '694', '49', 'TEXEUROP BD LTD', 'TEXEUROP', 'SPM', 'VOGRA,JOYDEBPUR,GAZIPUR', NULL, 'Mr. Tareq/ Jahanggir/ Ershad', 'Tel: +8801978686876', NULL, NULL, 'VOGRA,JOYDEBPUR,GAZIPUR', NULL, 'Mr. Tareq/ Jahanggir/ Ershad', 'Tel: +880 161 9003567', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(163, '357', '49', 'Aman Tex Limited.', 'Aman', 'SPM', 'Boiragirchala, Sreepur, Gazipur, Bangladesh.', NULL, 'Mr. Mahbub', 'Tel: +8801716131270', NULL, NULL, 'Boiragirchala, Sreepur, Gazipur, Bangladesh.', NULL, 'Mr. Mahbub', 'Tel: +8801716131270', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(164, '686', '49', 'Comfit Composite Knit Ltd.', 'Comfit', 'SPM', 'Youth Tower, 822/2 Rokeys Sharani, Dhaka-1216, Bangladesh', NULL, 'Attn:  Rifat Rahman', 'Tel: +8801911 278601', NULL, NULL, 'Gorai, Mirzapur, Tangail, Bangladesh', NULL, 'Attn:  Rifat Rahman', 'Tel: +8801911 278601', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(165, '', '49', 'Vision Apparels (pvt)Ltd', 'Vision', 'SPM', 'B-47, Purbo Rajashan, P.S Savar, Dhaka, Bangladesh', NULL, 'Attn:  Mr. Bappi', 'Tel: +8801973-483696, 7741783, 7742871, 7742872', NULL, NULL, 'B-47, Purbo Rajashan, P.S Savar, Dhaka, Bangladesh', NULL, 'Attn:  Mr. Bappi', 'Tel: +8801973-483696, 7741783, 7742871, 7742872', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(166, '', '49', 'DK Knitwear Ltd.', 'DK', 'SPM', 'Zamgora, Ashulia, Savar, Dhaka, Bangladesh', NULL, 'Attn:  Mr. Azad', 'Tel:  +8801718857786 & 01571753763', NULL, NULL, 'Zamgora, Ashulia, Savar, Dhaka, Bangladesh', NULL, 'Attn:  Mr. Azad', 'Tel:  +8801718857786 & 01571753763', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(167, '', '49', 'Mondol Fabrics Ltd.', 'Mondol', 'SPM', 'Nayapara, Kashimpur, Gazipur, Bangladesh.', NULL, 'Attn:  Mr.Babul/Sobuj/Azad/Liton', 'Tel: +88028955000-4, (00880-2) 9297893-96 EXT-318', NULL, NULL, 'Nayapara, Kashimpur, Gazipur, Bangladesh.', NULL, 'Attn:  Mr.Babul/Sobuj/Azad/Liton', 'Tel: +88028955000-4, (00880-2) 9297893-96 EXT-318', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(168, '781', '49', 'Anlima Textile Ltd', 'Anlima', 'SPM', 'Karnapara, Savar,DHAKA,BANGLADESH.', NULL, 'Attn: Mr. Mahtab', 'Tel: +880 1917 565 777', NULL, NULL, 'Karnapara, Savar,DHAKA,BANGLADESH.', NULL, 'Attn: Mr. Mahtab', 'Tel: +880 1917 565 777', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(169, '', '49', 'DADA (Dhaka) Ltd', 'DADA', 'SPM', '40 Jaiymat Khan Road, Pagar,Tongi, Gazipur-1710', NULL, 'Attn:  Mr. Noman', 'Tel: +8801911 098795', NULL, NULL, '40 Jaiymat Khan Road, Pagar,Tongi, Gazipur-1710', NULL, 'Attn:  Mr. Noman', 'Tel: +8801911 098795', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(170, '', '49', 'Sonnet Textile Industries Ltd.', 'Sonnet', 'SPM', '807/859,Barik Mia High School Lane,Goshaildanga,Bander,Chittagong,Bangladesh', NULL, 'Attn:  Mr. Shahid', 'Tel: +8801819391304', NULL, NULL, '807/859,Barik Mia High School Lane,Goshaildanga,Bander,Chittagong,Bangladesh', NULL, 'Attn:  Mr. Shahid', 'Tel: +01819391304', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(171, '', '49', 'PA Knit Composite Ltd  PRAN-RFL Group', 'PA', 'SPM', 'JAMIRDIA, HABIRBARI, VALUKA,MYMENSINGH, Bangladesh.', NULL, 'Attn: Anowar Kamal', 'Tel: +8801709650203', NULL, NULL, 'JAMIRDIA, HABIRBARI, VALUKA,MYMENSINGH, Bangladesh.', NULL, 'Attn: Anowar Kamal', 'Tel: +01709650203', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(172, '', '49', 'MURAD APPARELS LTD', 'MURAD', 'SPM', 'South Gouripur, Ashulia,Dhaka 1341. Bangladesh.', NULL, 'Attn: Mr. Shahjahan Sardar', 'Tel: +8801972554816', NULL, NULL, 'South Gouripur, Ashulia,Dhaka 1341. Bangladesh.', NULL, 'Attn: Mr. Shahjahan Sardar', 'Tel: +8801972554816', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(173, '', '49', 'GMS Composite Knitting', 'GMS', 'SPM', 'House 110, Road 06, New DOHS ,Mohakhali, Dhaka 1206, Bangladesh', NULL, 'Attn: Mr. Hafiz', 'Tel: +8801713141214', NULL, NULL, '(03K61-592)Shardagonj,Kashimpur,Gazipur,Bangladesh', NULL, 'Attn: Mr. Hafiz', 'Tel: +8801713141214', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(174, '', '49', 'JMS', '', 'SPM', 'TVM Europe GmbH,Rheinpromenade 11 | 40789 Monheim, Germany', NULL, 'Attn:   Marina Komogovski / Fred', 'Tel: +49 2173 10939-728.', NULL, NULL, 'Mouchak , Kaliakoir ,Gazipur, Dhaka , Bangladesh.', NULL, 'Attn:  Mr. Siraj', 'Tel: +8801827146636', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(175, '', '49', 'Uniform Textile Ltd.', 'Uniform', 'SPM', 'CB-288 , Tamanna Complex, Dhaka Cantonment , Dhaka -1206, Bangladesh', NULL, 'Attn: Mr.Forhad', 'Tel: +0-2-8750457 /01964550266', NULL, NULL, 'CB-288, Tamanna Complex, Dhaka Comment,Dhaka-1206, Bangladesh', NULL, 'Attn: Mr.Forhad', 'Tel: +0-2-8750457 /01964550266', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(176, '', '49', 'GOLDEN REFIT GARMENTS LTD', 'GOLDEN', 'SPM', '589, Chirirchala, Baghair Bazar, Bhavanipur, Gazipur, Bangladesh.', NULL, 'Attn: Mr. A. H. M. Aminul Islam', 'Tel: +8801911140611', NULL, NULL, '589, Chirirchala, Baghair Bazar, Bhavanipur, Gazipur, Bangladesh.', NULL, 'Attn: Mr. A. H. M. Aminul Islam', 'Tel: +01911140611', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(177, '', '49', 'KIMS CORPORATION LTD', 'KIMS', 'SPM', '16,SUTAR NOARDDA,ARAPARA,SHAVER,DHAKA,BANGLADESH.', NULL, 'Attn: Mr. Saiful', 'Tel: +8801717520091', NULL, NULL, '16,SUTAR NOARDDA,ARAPARA,SHAVER,DHAKA,BANGLADESH.', NULL, 'Attn: Mr. Saiful', 'Tel: +8801717520091', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(178, '', '49', 'Tosrifa Industries Limited', 'Tosrifa', 'SPM', 'Monnu Nagar, Gopalpur, Tongi, Gazipur, Bangladesh.', NULL, 'Attn:  M. WAHIDUZZAMAN/Mr. Jillur Rahman', 'Tel: +8801841121290', NULL, NULL, 'Monnu Nagar, Gopalpur, Tongi, Gazipur, Bangladesh.', NULL, 'Attn:  M. WAHIDUZZAMAN/Mr. Jillur Rahman', 'Tel: +01841121290', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 40),
(180, '00509', '49', 'Misami Garments Ltd.', 'Misami', 'OSTIN', 'Misami Garments Ltd.                     822/3, Begum Rokeya Sharani, Shewrapara, Mirpur, Dhaka-1216', NULL, 'Mr Iftekhar/Mr Kamrul Islam/Mr. Farhad Hasan', 'Tel: +880 161 9003567', NULL, NULL, 'Tarasima Apparels Ltd                                          Vill - Golora PO - Kaitta PS - Saturia Dist – Manikgonj Bangladesh Zip Code: 1800', NULL, 'Mr Iftekhar/Mr Kamrul Islam/Mr. Farhad Hasan', 'Tel: +880 161 9003567', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(181, '', '49', 'Pioneer Casual Wear Ltd.', 'Pioneer', 'OSTIN', 'Zamgara, Earpur Union, Ashulia, Savar, Dhaka-1344, Bangladesh', NULL, 'MR. Mizan', 'Tel: +8801714368135', NULL, NULL, 'Zamgara, Earpur Union, Ashulia, Savar, Dhaka-1344, Bangladesh', NULL, 'Mr. Ritesh Barua', 'Tel: +8801973106105', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(182, '00501', '49', 'Pioneer Knitwears BD Ltd.', 'Pioneer', 'OSTIN', 'Jamirdia, Habirbari, Valuka, Mymensingh, Bangladesh', NULL, 'Mahfuzur Rahman', 'Tel: +8801844083500', NULL, NULL, 'Jamirdia, Habirbari, Valuka, Mymensingh, Bangladesh', NULL, 'Mr. Tauhid', 'Tel: +01680401474', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(183, 'OSN/59/BD/SS18', '49', 'Aleya Apparels Ltd', 'Aleya', 'OSTIN', '244, Singair Road, Hemayetpur, Saver, Dhaka.', NULL, 'Shaikh Humaun Kabir', 'Tel: +8801799982953, 01685491395', NULL, NULL, '244, Singair Road, Hemayetpur, Saver, Dhaka.', NULL, 'Shaikh Humaun Kabir', 'Tel: +8801799982953, 01685491395', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(184, '', '49', 'ALPHA CLOTHING LIMITED', 'ALPHA', 'OSTIN', '39 purana Pantan, Dhaka', NULL, 'Mr. Shamim', 'Tel: +88 01685 696806', NULL, NULL, '113/1, Mudafa, Poschim Para, Tongi, Gazipur.', NULL, 'Md.Al-amin', 'Tel: +8801876034984', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(185, '00304', '49', 'Garments Export Village Ltd/ Amtranet Ltd/ Power Vantage Wear Ltd', 'Garments', 'OSTIN', 'Amtranet Ltd.                                        160 West Rajashon, Savar, Dhaka', NULL, 'Mr. Zasim/ Yousuf', 'Tel: +8801841415362', NULL, NULL, 'Amtranet Ltd.                                                        160 West Rajashon, Savar, Dhaka', NULL, 'Mr. Zasim/ Yousuf', 'Tel: +8801841415362', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(188, '00236', '49', 'APEX TEXTILE PRINTING MILLS LTD.', 'APEX', 'OSTIN', 'CHANDORA, SHAFIPUR, KALIAKOIR, GAZIPUR', NULL, 'Mr. Moin/Mr. Mizan', 'Tel: +8801711-985986 Mob: +8801673926384', NULL, NULL, 'CHANDORA, SHAFIPUR, KALIAKOIR, GAZIPUR', NULL, 'Mr. Moin/Mr. Mizan', 'Tel: +8801711-985986, +8801673926384', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(190, '9093', '49', 'Apparel Plus Ltd.', 'Apparel', 'OSTIN', 'DILAN COMPLEX, DHAKA ROAD,CHANDONA CHOWRASTA, GAZIPUR - 1702', NULL, 'Mr. Nafiz/MD. NAKIB', 'Tel: +88-01717 943110, Tel: +88 0176 5 585 702', NULL, NULL, 'DILAN COMPLEX, DHAKA ROAD,CHANDONA CHOWRASTA, GAZIPUR - 1702', NULL, 'MR. TARUN', 'Tel: +88 01914 757 262', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(191, '00365', '49', 'Chittagong Fashion Specialised Textiles Ltd.', 'Chittagong', 'OSTIN', 'Plot# 26, Road# 04, Sector# 01, Chittagong Export Processing Zone, Chittagong-4223, Bangladesh.', NULL, 'A.B.SIDDIQUE', 'Tel: +8801730035550', NULL, NULL, 'Plot# 26, Road# 04, Sector# 01, Chittagong Export Processing Zone, Chittagong-4223, Bangladesh.', NULL, 'A.B.SIDDIQUE', 'Tel: +01730035550', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(192, '00539', '49', 'Chorka Textile Limited.\r\nPRAN-RFL Group', 'Chorka', 'OSTIN', 'KAZIR CHAR, DANGA, PALASH, NARSHINGDI', NULL, 'Md.Rejaul Hasnat Rubel/Md. Rahat Chowdhury', 'Tel: +8801704134099,M- +88 01924 606550', NULL, NULL, 'KAZIR CHAR, DANGA, PALASH, NARSHINGDI', NULL, 'Md.Rejaul Hasnat Rubel/Md. Rahat Chowdhury', 'Tel: +8801704134099, +88 01924 606550', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(193, '00298', '49', 'Cotton Field BD LTD', 'Cotton', 'OSTIN', 'Shima Complex, Plot # 23 Shahtaish Road. Gazipura. Tongi, Gazipur. Bangladesh', NULL, 'Mr. Shahin/Mr. RIPON', 'Tel: +8801717138114', NULL, NULL, 'Shima Complex, Plot # 23 Shahtaish Road. Gazipura. Tongi, Gazipur. Bangladesh', NULL, 'Mr. Shahin/Mr. RIPON', 'Tel: +8801717138114', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(195, '00482', '49', 'Esquire Knit Composite Ltd.', 'Esquire', 'OSTIN', '22/58 Kanchpur, Sonargaon, Narayongonj.', NULL, 'Mr Mahamudul Islam (Mukti)/Abu Ahmed Ruwel', 'Tel: +880171333097/8801713377992', NULL, NULL, '22/58 Kanchpur, Sonargaon, Narayongonj.', NULL, 'Mohd. Obaidur Rashid/Tanbinur Rahman', 'Tel: +880171333045/8801730700757', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(196, '', '49', 'EURO ARTE APPARELS LTD', 'EURO', 'OSTIN', 'Factory :Plot#346, Kabirpur, Ashulia, Dhaka.\r\nDhaka Office : H # 517/4, R #10, Baridhara DOHS, Dhaka.', NULL, 'Arnab Khan/Md. Saiful Mridha', 'Tel: +8801906202138/8801748995350', NULL, NULL, 'Factory :Plot#346, Kabirpur, Ashulia, Dhaka.\r\nDhaka Office : H # 517/4, R #10, Baridhara DOHS, Dhaka.', NULL, 'MONJORUL ALAM', 'Tel: +8801725662904', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(197, '00427', '49', 'Graphics Textile Ltd', 'Graphics', 'OSTIN', '225, Tejgaon I/A (1st Floor), Dhaka, Bangladesh', NULL, 'Md.Ariful Islam Arif/Shahiduzzaman Khan/Mr. Mainul/ Mr. Zubayer', 'Tel: +880 1858125244', NULL, NULL, '225, Tejgaon I/A (1st Floor), Dhaka, Bangladesh', NULL, 'Mr. Rakib & Mr. Sudev', 'Tel: +8801988885724 /01988885654', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(198, '00478', '49', 'Jerat Fashion Ltd', 'Jerat', 'OSTIN', '1676/U, South Bakalia, Rajakhali, Chittagong,Bangladesh', NULL, 'Mr. Kaisar', 'Tel: +8801755596653', NULL, NULL, '1676/U, South Bakalia, Rajakhali, Chittagong,Bangladesh', NULL, 'Mr. Kaisar', 'Tel: +8801755596653', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(199, '00246', '49', 'Genesis Fashion Ltd.', 'Genesis', 'OSTIN', '126/1, KADDA NANDUN KADDA BAZAR GAZIPUR SADAR, GAZIPUR-1346. BANGLADESH.', NULL, 'Mr. Rafique/Mr. Ahsan', 'Tel: +01557086898, 01709677302, 8802 885 8890, 8801709677314', NULL, NULL, '126/1, KADDA NANDUN KADDA BAZAR GAZIPUR SADAR, GAZIPUR-1346. BANGLADESH.', NULL, 'Mr. Rafique/Mr. Ahsan', 'Tel: +8801709677302, 8802 885 8890, 8801709677314', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(200, '00275', '49', 'MASCO COTTONS LTD/ MPL WEAR LTD', 'MASCO', 'OSTIN', 'MPL WEAR LTD                     KANCHAN, RUPGONG, NARAYANGANJ, BANGLADESH', NULL, 'Mr. Mehedul', 'Tel: +8801678566602', NULL, NULL, 'MPL WEAR LTD                                             KANCHAN, RUPGONG, NARAYANGANJ, BANGLADESH', NULL, 'Raisul Islam/Mr. Mehedul', 'Tel: +8801625348112/01678566602', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(201, '00425', '49', 'Masihata Sweaters Ltd', 'Masihata', 'OSTIN', 'Masihata Sweaters Ltd                  South Panishail, BKSP, Kashimpur, Gazipur-1349, Bangladesh', NULL, 'Md.Khairul hasan (Bapshi)', 'Tel: +88 01708 – 121 122', NULL, NULL, 'Shanon Sweaters Ltd                                      92 Nawzor, Kodda bazar, Gazipur', NULL, 'Md.Khairul hasan (Bapshi)', 'Tel: +88 01708 – 121 122', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(202, '00422', '49', 'METRO KNITTING & DYEING MILLS LTD.', 'METRO', 'OSTIN', 'OSMAN TOWER (1ST FLOOR) 56/1S, M. MALEH ROAD, TANBAZAR, NARAYANGONJ.', NULL, 'MOFIZUR RAHMAN', 'Tel: +8801713441219', NULL, NULL, 'RAMARBAG, KUTUBPUR, FATULLAH, NARAYANGONJ-1400', NULL, 'MR. BALARAM ROY/MD. MOFIZUR RAHMAN', 'Tel: +8801713441219', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(203, '00499', '49', 'MHC (PVT) Ltd.', 'MHC', 'OSTIN', 'Road# 06, House# 365/1, Baridhara,  Dhaka-1213, Bangladesh.', NULL, 'Mr. TUSHERMr. Rifat', 'Tel: +880 1610 894 527', NULL, NULL, '122 (New), 13/1 (Old) Sataish Road Sataish, Tongi, Gazipur', NULL, 'Mr. TUSHERMr. Rifat', 'Tel: +880 1610 894 527', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(204, '00161', '49', 'Minar Group', 'Minar', 'OSTIN', '70-72 BSCIC I/A, Fatullah, Narayanganj.', NULL, 'Golok Kumar Saha', 'Tel: +01716607886', NULL, NULL, '70-72 BSCIC I/A, Fatullah, Narayanganj.', NULL, 'Golok Kumar Saha', 'Tel: +01716607886', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(205, '', '49', 'MN Group', 'MN', 'OSTIN', 'Monir Bhaban, 220, East Rampur                                           \r\nEidgah, D.T .Road,Chittagong, Bangladesh', NULL, 'Mr. Robin', 'Tel: +8801715891044', NULL, NULL, 'Monir Bhaban, 220, East Rampur                                           \r\nEidgah, D.T .Road,Chittagong, Bangladesh', NULL, 'Mr. Robin', 'Tel: +8801715891044', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(206, '00276', '49', 'Natural sweater village ltd.', 'Natural', 'OSTIN', 'Factory : Raju Tower, Gouripur, Ashulia, Dhaka, Bangladesh', NULL, 'Mr. Monju/Jamil ahmed', 'Tel: +8801926-997715, +880 1841997717/01926997717', NULL, NULL, 'Factory : Raju Tower, Gouripur, Ashulia, Dhaka, Bangladesh', NULL, 'Mr. Monju/Jamil ahmed', 'Tel: +8801926-997715, +880 1841997717/01926997717', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(207, '00424', '49', 'Neo Fashion Ltd', 'Neo', 'OSTIN', 'Varari, Rajfulbaria, Tetuljhora, Savar, Dhaka, Bangladesh', NULL, 'Faruq  Hossain', 'Tel: +8801953812264, 01673568644', NULL, NULL, 'Varari, Rajfulbaria, Tetuljhora, Savar, Dhaka, Bangladesh', NULL, 'Faruq  Hossain', 'Tel: +8801953812264, 01673568644', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(208, '', '49', 'Ocean Sweater Ind. (Pvt.) Ltd.', 'Ocean', 'OSTIN', 'Flat # 5A, House # 19, Road # 17, Sector # 4, Uttara,\r\nDhaka-1230, Bangladesh.', NULL, 'Mr. Milon', 'Tel: +880 1676997710', NULL, NULL, 'Flat # 5A, House # 19, Road # 17, Sector # 4, Uttara,\r\nDhaka-1230, Bangladesh.', NULL, 'Mr. Milon', 'Tel: +880 1676997710', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(209, '', '49', 'Platinum Apparel Mfg Co. Ltd', 'Platinum', 'OSTIN', 'Ashulia, Charabagh, Basaid, Saver, Dhaka-1341', NULL, 'Mr. Rafique/Arafat Uddin', 'Tel: +01673871950/01850453325', NULL, NULL, 'Ashulia, Charabagh, Basaid, Saver, Dhaka-1341', NULL, 'Mr. Rafique/Arafat Uddin', 'Tel: +8801673871950/01850453325', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(210, '00277', '49', 'Pioneer Apparels Limited', 'Pioneer', 'OSTIN', 'RAMJANNESSA SUPER MARKET, 142, SENANIBASH ROAD, PALLABI, MIRPUR-12, DHAKA, BANGLADESL', NULL, 'Mr. Monirul/ Mr. Tanvir', 'Tel: +8801970704810 / 01970-704812', NULL, NULL, 'RAMJANNESSA SUPER MARKET, 142, SENANIBASH ROAD, PALLABI, MIRPUR-12, DHAKA, BANGLADESL', NULL, 'Mr. Ehshanul', 'Tel: +88 01970-704883', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(211, '00336', '49', 'PN.COMPOSITE.LTD', '', 'OSTIN', 'AMBAG KONABARI GAZIPUR', NULL, 'Mr. Liton/MD.Riaz Ahmed.', 'Tel: +8801713387683/+88-01713387647', NULL, NULL, 'AMBAG KONABARI GAZIPUR', NULL, 'MD.Riaz Ahmed.', 'Tel: +88-01713387647', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(212, '', '49', 'FASHION KNIT GARMENTS LTD(Pride Group)', 'FASHION', 'OSTIN', 'ULAIL BUS STAND, 4.KARNAPARA,SAVAR,DHAKA,BANGLADESH', NULL, 'Shihab Uddin Bhuiyan', 'Tel: +8801990409054', NULL, NULL, 'ULAIL BUS STAND, 4.KARNAPARA,SAVAR,DHAKA,BANGLADESH', NULL, 'Shihab Uddin Bhuiyan', 'Tel: +8801990409054', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(213, '00302', '49', 'ROSE SWEATERS LTD', 'ROSE', 'OSTIN', 'BIJOY SARAK, MOGOR KHAL, JOYDEVPUR, GAZIPUR, BANGLADESH', NULL, 'Md.Shahin Reza.', 'Tel: +8801841673955/8801713556486', NULL, NULL, 'BIJOY SARAK, MOGOR KHAL, JOYDEVPUR, GAZIPUR, BANGLADESH', NULL, 'Md.Shahin Reza.', 'Tel: +8801841673955/8801713556486', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(214, '00399', '49', 'RUSSEL GARMENTS', 'RUSSEL', 'OSTIN', '56/1 SM MALEH ROAD,NARAYANGANJ,BANGLADESH', NULL, 'MR. FERDAOUS/Md. Zahidul Islam Zahid', 'Tel: +88 01677450854', NULL, NULL, '56/1 SM MALEH ROAD,NARAYANGANJ,BANGLADESH', NULL, 'Md. Zahidul Islam Zahid/Md. Tanvir Ahamed', 'Tel: +88 01677450854/01705362945.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(215, '00320', '49', 'Saadatia Sweaters Ltd.', 'Saadatia', 'OSTIN', 'Samir Plaza (Opposite of Fantasy Kingdom), 2nd Floor, DEPZ Ashulia Tongi Road, Jamgora, Savar, Dhaka.', NULL, 'Gobinda Chandra Bordhon/ Awlad Hossain', 'Tel: +8801684516464, 8801726396930', NULL, NULL, 'Samir Plaza (Opposite of Fantasy Kingdom), 2nd Floor, DEPZ Ashulia Tongi Road, Jamgora, Savar, Dhaka.', NULL, 'Awlad Hossain', 'Tel: +8801726396930', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(216, '00360', '49', 'SEHA INTERNATIONAL BANGLADESH LTD', 'SEHA', 'OSTIN', 'A.G DRESSES LTD                                 PLOT-9 , BLOCK-C , HIMARDIGHI , TONGI INDUSTRIAL AREA, TONGI, GAZIPUR ,BANGLADESH', NULL, 'Mominul Islam (BADAL)/Mr. Shazed', 'Tel: +8801822215788', NULL, NULL, 'A.G DRESSES LTD                                                   PLOT-9 , BLOCK-C , HIMARDIGHI , TONGI INDUSTRIAL AREA, TONGI, GAZIPUR ,BANGLADESH', NULL, 'Mominul Islam (BADAL)/Mr. Shazed', 'Tel: +8801822215788', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(217, '00491', '49', 'TNZ APPARELS LTD', 'TNZ', 'OSTIN', 'Shi-228/04, Bijoy Road, Mogorkhal,Gazipur-1704, Bangladesh', NULL, 'Mr. Motaleb', 'Tel: +88 01711 585929', NULL, NULL, 'Shi-228/04, Bijoy Road, Mogorkhal,Gazipur-1704, Bangladesh', NULL, 'Mr. Motaleb', 'Tel: +88 01711 585929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(218, '00513', '49', 'S. F. Jeans Ltd', 'S.', 'OSTIN', 'Karol, Shurichala, Shafipur Bazar, Kaliakoir, Gazipur.', NULL, 'Mr. Foeyzur / Sazzad', 'Tel: +01715-489249 / 01717-503924', NULL, NULL, 'Karol, Shurichala, Shafipur Bazar, Kaliakoir, Gazipur.', NULL, 'Mr. Aminur Rahman (Amin)', 'Tel:01913-511195', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(219, '', '49', 'SINHA Knit and Denims Limited.', 'SINHA', 'OSTIN', 'Plot No: 102, Mouja: Tanguri, Post: BKSP, \r\nPS: Ashulia, Savar, Dhaka, Bangladesh.', NULL, 'Mr. Habib, Safiul', 'Tel: +8801712541100, 01685389854', NULL, NULL, 'Plot No: 102, Mouja: Tanguri, Post: BKSP, \r\nPS: Ashulia, Savar, Dhaka, Bangladesh.', NULL, 'Mr. Habib', 'Tel: +8801712541100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(220, '00489', '49', 'Toyo', '', 'OSTIN', '01, South Kamalapur, Motijheel C/A, Dhaka-1217, Bangladesh.', NULL, 'Kamuzzaman Taposh, Mahfuzul Haq', 'Tel: +88001992977025, +8801798373124', NULL, NULL, '01, South Kamalapur, Motijheel C/A, Dhaka-1217, Bangladesh.', NULL, 'Kamuzzaman Taposh, Mahfuzul Haq', 'Tel: +88001992977025, +8801798373124', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(221, '00366', '49', 'Vision Garments Ltd.', 'Vision', 'OSTIN', 'B-47 Purbo Rajashan, P.S, Savar,Dhaka', NULL, 'Mr.Rokon Chowdhury, MR. MAHABUB', 'Tel: +8801973239751, 01973-239708', NULL, NULL, 'B-47 Purbo Rajashan, P.S, Savar,Dhaka', NULL, 'Mr.Rokon Chowdhury, MR. MAHABUB', 'Tel: +8801973239751, 01973-239708', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(222, '00492', '49', 'voyager apparels ltd', 'voyager', 'OSTIN', '8, Malibagh Chowdhury para, dhaka-1219', NULL, 'Mr. Islam, Monjurul Karim', 'Tel: +8801914242069, 01914242065', NULL, NULL, '8, Malibagh Chowdhury para, dhaka-1219', NULL, 'Mr. Ruhul amin, Monjurul Karim', 'Tel: +8801914242041, 01914242065', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(223, '00503', '49', 'Crosswear Industries Ltd', 'Crosswear', 'OSTIN', 'Kathgara, P.O.: Zirabo, P.S.: Ashulia, Dhaka-1341. Bangladesh.', NULL, 'ZAHIRUL ISLAM', 'Tel: +8801618780950', NULL, NULL, 'Kathgara, P.O.: Zirabo, P.S.: Ashulia, Dhaka-1341. Bangladesh.', NULL, 'ZAHIRUL ISLAM', 'Tel: +8801618780950', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 27),
(224, '', '49', 'Sonia & Sweaters Ltd.', 'Sonia', 'Gymboree', 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, 'Plot No. # 604( 10th Floor ), Kondolbagh, Taibpur,Ashulia Road, Savar, Dhaka', NULL, 'Mr. Mahfuzur Rahman', '+8801765446574', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(225, '', '49', 'Panwin Design Ltd', 'Panwin', 'Gymboree', 'Plot - 576, Vill - Baniarchala, P.o- Bhabanipur, Gazipur ,Bangladesh.', NULL, 'Mr. Iqbal', '+8801777788882', NULL, NULL, 'Plot - 576, Vill - Baniarchala, P.o- Bhabanipur, Gazipur ,Bangladesh.', NULL, 'Ms. Shemo', '+8801777788882', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(226, '', '49', 'Sirina garments & textile Ltd.', 'Sirina', 'Gymboree', '7/A, Sholoshahar Light Industrial Area,Nasirabad, Baizid Bostami Road, Chittagong. Bangladesh.', NULL, 'Mr. Alamgir', '+8801847187183', NULL, NULL, '171/181, Baizid Bostami Road, Nasirabd I/E, Chittagong.', NULL, 'Mr. Rashed', '+8801717959197', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(227, '', '49', 'Concept Knitting Ltd.', 'Concept', 'Gymboree', 'Tilargati, Sataish, , Tongi, Gazipur,Bangladesh', NULL, 'Mr. Vashani Habib', '+8801678 566 697', NULL, NULL, 'Tilargati, Sataish, , Tongi, Gazipur,Bangladesh', NULL, 'Mr. Kamrul', '+8801923 872 110', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(228, '', '49', 'JK Knit composite Ltd', 'JK', 'Gymboree', 'South Dariapur, Savar, Dhaka-1340, Bangladesh', NULL, 'Mr. Humayun Kabir', '+8801730060890', NULL, NULL, 'South Dariapur, Savar, Dhaka-1340, Bangladesh', NULL, 'Mr. Rackybul Islam', '+8801730060837', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(229, '', '49', 'Noman Fashion Fabrics Ltd.', 'Noman', 'Gymboree', 'Plot No. # 604 ( 10th Floor ), Kondolba, Pagar, Tongi, Gazipur-1710,', NULL, 'Moheuddin Khan', '+8801933 941 042', NULL, NULL, 'Plot No. # 604 ( 10th Floor ), Kondolba, Pagar, Tongi, Gazipur-1710,', NULL, 'Mr. Kibria', '+8801787681994', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(230, '', '49', 'Glory Idustries Ltd', 'Glory', 'Gymboree', '7/A, Sholoshahar Light Industrial Area,Nasirabad, Baizid Bostami Road, Chittagong. Bangladesh.', NULL, 'Mr. Rasel', '+8801678 566 733', NULL, NULL, '7/A, Sholoshahar Light Industrial Area,Nasirabad, Baizid Bostami Road, Chittagong. Bangladesh.', NULL, 'Mr. Sanjay Kumar Barua.', '+8801812653519', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(231, '', '49', 'Next Collection Ltd.', 'Next', 'Gymboree', '1323-1325 Beron, Ashulia, Savar, Dhaka-1341, Bangladesh.', NULL, 'Mr.Zaher', '+8801818365489', NULL, NULL, '1323-1325 Beron, Ashulia, Savar, Dhaka-1341, Bangladesh.', NULL, 'Mr.Zaher', '+8801818365489', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(232, '', '49', 'Tip Top Fashions Ltd', 'Tip', 'Gymboree', 'Industrial Plot-1, Block-E, Ave-1, Sec-11, 5th floor, Mirpur, Dhaka,  Bangladesh.', NULL, 'Mr.Jamal Ahmad', '+8801766665435', NULL, NULL, 'Saiful  Store, Plot No-647-652, Kalma-1, Ward No:-07, Post:-Dairy Farm, Savar, Dhaka-1341.', NULL, 'Mr. Jashimuddin', '+8801909605307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(233, '', '49', 'Tip Top Fashions Ltd', 'Tip', 'Gymboree', 'Industrial Plot-1, Block-E, Ave-1, Sec-11, 5th floor, Mirpur, Dhaka,  Bangladesh.', NULL, 'Waliur Rahman', '+88 01730303529', NULL, NULL, '401/B,Tejgaon I/A, Dhaka-1208..', NULL, 'Mr. Jashimuddin', '+8801909605307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(234, '', '49', 'Mawna Fashion Ltd', 'Mawna', 'Gymboree', 'Tapierbari, Shrepur, Gazipur.', NULL, 'Mr. Shahidur Rahman (Shemul)', '+8801915584349', NULL, NULL, 'Tapierbari, Shrepur, Gazipur.', NULL, 'Mr. Rustom', '+8801844149209', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34),
(235, '', '49', 'Jinnat Knit Wears Ltd', 'Jinnat', 'Gymboree', 'Sardagonj, Kashimpur,Gazipur', NULL, 'Mr. Shahidur Rahman (Shemul)', '+8801915584349', NULL, NULL, 'Sardagonj, Kashimpur,Gazipur', NULL, 'Mr. Dulal', '+8801768589552', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 34);

-- --------------------------------------------------------

--
-- Table structure for table `mxp_pi`
--

CREATE TABLE `mxp_pi` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `p_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oos_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matarial` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `others_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orderDate` datetime DEFAULT NULL,
  `orderNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipmentDate` datetime DEFAULT NULL,
  `poCatNo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `style` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_pi`
--

INSERT INTO `mxp_pi` (`id`, `job_no`, `p_id`, `user_id`, `booking_order_id`, `erp_code`, `item_code`, `item_description`, `oos_number`, `item_quantity`, `item_size`, `item_price`, `matarial`, `gmts_color`, `others_color`, `orderDate`, `orderNo`, `shipmentDate`, `poCatNo`, `sku`, `is_type`, `created_at`, `updated_at`, `style`) VALUES
(1, '2', '07102018-Sonia-0001', 82, 'BK-07102018-Sonia-0003', '01-GY8MHT2**-001', '8MHT2', '0', '3232', '10000', '0', '0', NULL, NULL, '0', '0000-00-00 00:00:00', NULL, '2018-10-12 00:00:00', '232', '10000', 'non_fsc', '2018-10-06 22:27:33', '2018-10-06 22:27:33', '4541000'),
(2, '2', '07102018-Sonia-0002', 82, 'BK-07102018-Sonia-0003', '01-GY8MHT2**-001', '8MHT2', '0', '3232', '10000', '0', '0', NULL, NULL, '0', '0000-00-00 00:00:00', NULL, '2018-10-12 00:00:00', '232', '10000', 'non_fsc', '2018-10-06 22:37:10', '2018-10-06 22:37:10', '4541000'),
(3, '10', '08102018-Sonia-0003', 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'Hang tag', 'oos', '4000', 'size_4', '0.00045345', NULL, 'color_2', '0', '0000-00-00 00:00:00', NULL, '2018-10-08 00:00:00', 'po', 'sku', 'non_fsc', '2018-10-08 01:37:36', '2018-10-08 01:37:36', 'style'),
(4, '7', 'fsc-08102018-Sonia-0004', 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'Hang tag', 'oos', '1000', 'size_1', '0.00045345', NULL, 'color_1', '0', '0000-00-00 00:00:00', NULL, '2018-10-08 00:00:00', 'po', 'sku', 'fsc', '2018-10-08 01:37:54', '2018-10-08 01:37:54', 'style'),
(5, '9', 'fsc-08102018-Sonia-0004', 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'Hang tag', 'oos', '3000', 'size_3', '0.00045345', NULL, 'color_2', '0', '0000-00-00 00:00:00', NULL, '2018-10-08 00:00:00', 'po', 'sku', 'fsc', '2018-10-08 01:37:54', '2018-10-08 01:37:54', 'style'),
(6, '8', 'fsc-08102018-Sonia-0004', 49, 'BK-08102018-Sonia-0005', '01-GY8KMHT2**-001', '8KMHT2', 'Hang tag', 'oos', '2000', 'size_2', '0.00045345', NULL, 'color_1', '0', '0000-00-00 00:00:00', NULL, '2018-10-08 00:00:00', 'po', 'sku', 'fsc', '2018-10-08 01:37:54', '2018-10-08 01:37:54', 'style');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_product`
--

CREATE TABLE `mxp_product` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_inc_percentage` int(11) DEFAULT NULL,
  `item_size_width_height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_qty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_amt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `others_color` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_buyer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_product`
--

INSERT INTO `mxp_product` (`product_id`, `user_id`, `product_code`, `product_name`, `product_type`, `product_description`, `brand`, `erp_code`, `item_inc_percentage`, `item_size_width_height`, `item_description_id`, `unit_price`, `weight_qty`, `weight_amt`, `description_1`, `description_2`, `description_3`, `description_4`, `status`, `created_at`, `updated_at`, `action`, `others_color`, `id_buyer`) VALUES
(4, 49, '8KMHT2', NULL, NULL, 'Hang tag', NULL, '01-GY8KMHT2**-001', NULL, '41.4-63.5', '9', '1.00', NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, '2018-10-08 01:18:41', 'update', NULL, 34),
(5, 49, '8MHT2', NULL, NULL, NULL, NULL, '01-GY8MHT2**-001', NULL, '41.4-63.5', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(6, 49, '8SHPGDYE2', NULL, NULL, NULL, NULL, 'N/A', NULL, '57.15-20.32', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(7, 49, '8BRKRDNM3', NULL, NULL, NULL, NULL, 'N/A', NULL, '57.15-104.9', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(8, 49, '8CMBOLTRD3', NULL, NULL, NULL, NULL, '03-GY8CMBOLTRD3-001', NULL, '41-63.23 ', '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(9, 49, '8LOOPLBTR', NULL, NULL, NULL, NULL, '03-GY8LOOPLBTR-001', NULL, '25.4-70.05', '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(10, 49, '8NLLTR', NULL, NULL, NULL, NULL, '03-GYC88NLLTR/**-001', NULL, '38.11-70.05', '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(11, 49, '8LOOPLBL', NULL, NULL, NULL, NULL, '03-GY8LOOPLBL-001', NULL, '25.4-66.04 ', '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(12, 49, '8HEAT2', NULL, NULL, NULL, NULL, '23-GYC88HEAT2-002', NULL, '29.19-20.9', '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(13, 49, '8HEATF2', NULL, NULL, NULL, NULL, '23-GYC88HEATF2-002', NULL, '36.11-27.42 ', '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(14, 49, '8NLSL5', NULL, NULL, NULL, NULL, '03-GYC8NLSL5*-001', NULL, '25.4-74.7', '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(15, 49, '8NLSL4', NULL, NULL, NULL, NULL, '03-GYC88NLSL4-001', NULL, '25-88.8 ', '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(16, 49, 'FAUXFUR', NULL, NULL, NULL, NULL, '03-GYFAUXFUR-001', NULL, '19-16 ', '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(17, 49, '8LSSZSTK1', NULL, NULL, NULL, NULL, '22-GYM8LSSZSTK1*-01', NULL, '22.13-114.3 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(18, 49, '8SSSZSTK1', NULL, NULL, NULL, NULL, '22-GYM8LSSZSTK1*-01', NULL, '22.13-114.3 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(19, 49, '8STRKRSTK', NULL, NULL, NULL, NULL, '22-GY8STRKRSTK*-01', NULL, '24.1-97.2 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(20, 49, 'BMHT1', NULL, NULL, NULL, NULL, '01-GYRBBMHT1-001', NULL, '28.58-76.2 ', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(21, 49, 'MHTU4', NULL, NULL, NULL, NULL, '01-GYRBMHTU4-001', NULL, '28.58-76.2 ', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(22, 49, 'UACCGEN', NULL, NULL, NULL, NULL, '01-GYUACCGEN-01', NULL, '28.6-152.4 ', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(23, 49, 'BACCSZ1', NULL, NULL, NULL, NULL, '01-GYBACCSZ1-01', NULL, '28.6-152.4 ', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(24, 49, 'HEATSM3', NULL, NULL, NULL, NULL, '23-GYHEATSM3-002', NULL, '26.1-70.6 ', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(25, 49, 'HEATHZF1', NULL, NULL, NULL, NULL, '23-GYHEATHZF1-002', NULL, '27.4-23.4 ', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(26, 49, 'CMBOUL5', NULL, NULL, NULL, NULL, '03-GYMCMBOUL5X-01', NULL, '25-99.68 ', '14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(27, 49, 'GDNMLBL1', NULL, NULL, NULL, NULL, '03-GYMGDNMLBL1-01', NULL, '25-109.65 ', '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(28, 49, 'GSDLBF2', NULL, NULL, NULL, NULL, '03-GYGSDLBF2X-01', NULL, '25-82.235 ', '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(29, 49, 'NEWMT', NULL, NULL, NULL, NULL, 'N/A', NULL, '100.83-39.66 ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(30, 49, 'BLBOUTR', NULL, NULL, NULL, NULL, '03-GYBLBOUTRX*-002', NULL, '16-60.14 ', '15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(31, 49, 'GBBTMLBL', NULL, NULL, NULL, NULL, '03-GYGBBTMLBL*-001', NULL, '44-67 ', '15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(32, 49, 'GKBTMLBL', NULL, NULL, NULL, NULL, '03-GYGKBTMLBLL*-001', NULL, '44-67 ', '15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(33, 49, '', NULL, NULL, NULL, NULL, '03-GYGOUTLBH*-002', NULL, '32-75', '15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(34, 49, 'USZCOO2', NULL, NULL, NULL, NULL, '03-GYUSZCOO2*-001', NULL, '13-44.61 ', '16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(35, 49, 'BLBL1', NULL, NULL, NULL, NULL, '03-GYBLBL1X*-002', NULL, '13-47.34 ', '15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(36, 49, 'GKLBL', NULL, NULL, NULL, NULL, '03-GYRGKLBL**-001', NULL, '13-47.34 ', '15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(37, 49, 'GCLRTRNSF3', NULL, NULL, NULL, NULL, '03-GYGCLRTRNSF3*-001', NULL, '47.6-47.6 ', '17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(38, 49, 'GONEKNDGD1', NULL, NULL, NULL, NULL, '01-GYGONEKNDGD1-001', NULL, '62.7-28.5 ', '18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(39, 49, 'GONEKNDID1', NULL, NULL, NULL, NULL, '01-GYGONEKNDND1-001', NULL, '62.7-28.5 ', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(40, 49, 'GCRPSKST', NULL, NULL, NULL, NULL, '22-GYMGBSKDSTK**-01', NULL, '31.8-120.7 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(41, 49, 'GJEGSZSTK2', NULL, NULL, NULL, NULL, '22-GYMGBSKDSTK**-01', NULL, '31.8-120.7 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(42, 49, 'GSPSKDSTK2', NULL, NULL, NULL, NULL, '22-GYMGBSKDSTK**-01', NULL, '31.8-120.7 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(43, 49, 'GBSKDSTK', NULL, NULL, NULL, NULL, '22-GYMGBSKDSTK**-01', NULL, '31.8-120.7 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(44, 49, 'GBSLCHSZST', NULL, NULL, NULL, NULL, '22-GYMGBSKDSTK**-01', NULL, '31.8-120.7 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(45, 49, 'GBSTSTK', NULL, NULL, NULL, NULL, '22-GYMGBSKDSTK**-01', NULL, '31.8-120.7 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(46, 49, 'OMHT1', NULL, NULL, NULL, NULL, '21-GYOTOMHT1*-001', NULL, '38.1-95.3', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(47, 49, 'OCHKH1', NULL, NULL, NULL, NULL, '21-GYOTOCHKH1*-001', NULL, '38.1-76.2 ', '9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(48, 49, 'OCMBOL3', NULL, NULL, NULL, NULL, '23-GYMOCMBOL3X-01', NULL, '25-91.7', '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(49, 49, 'OHEAT', NULL, NULL, NULL, NULL, '23-GYOTOHEAT-002', NULL, '27.7-16.4 ', '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(50, 49, 'OHEATF', NULL, NULL, NULL, NULL, '23-GYOTOHEATF-002', NULL, '28.7-26.8 ', '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(51, 49, 'OSZSTK', NULL, NULL, NULL, NULL, '22-GYOTOSZSTK-001', NULL, '24.13-80.01', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(52, 49, 'OLSSZSTK', NULL, NULL, NULL, NULL, '02-GYOTSSZSTK-001', NULL, '24.13-90.17', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(53, 49, 'MPP', NULL, NULL, NULL, NULL, '22-GYOTST7660-109', NULL, '76-60', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(54, 49, 'SPP', NULL, NULL, NULL, NULL, '22-GYOTST7651-001', NULL, '76-51 ', '12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, NULL, '', NULL, 34),
(55, 49, '234', NULL, NULL, '3', '', '32432', NULL, '-', '3', '1', NULL, NULL, NULL, NULL, NULL, NULL, '1', '2018-10-06 11:40:43', '2018-10-06 11:40:43', 'create', NULL, 5),
(56, 49, '324', NULL, NULL, 'Heat transfer label', '', '23432', NULL, '-', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', '2018-10-06 11:46:09', '2018-10-06 11:46:09', 'create', NULL, 6);

-- --------------------------------------------------------

--
-- Table structure for table `mxp_productsize`
--

CREATE TABLE `mxp_productsize` (
  `proSize_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_productsize`
--

INSERT INTO `mxp_productsize` (`proSize_id`, `user_id`, `product_code`, `product_size`, `status`, `action`, `created_at`, `updated_at`) VALUES
(3, 49, 'sad', 'asas', '1', 'create', '2018-09-27 01:13:19', '2018-09-27 01:13:19'),
(4, 49, '', 'sds', '1', 'create', '2018-09-27 05:41:13', '2018-09-27 05:41:13'),
(8, 49, 'primark', 'sds', '1', 'create', '2018-09-29 01:33:55', '2018-09-29 01:33:55'),
(10, 49, 'sss', 'sds', '1', 'create', '2018-09-29 04:48:40', '2018-09-29 04:48:40'),
(11, 49, '234', 'sds', '1', 'create', '2018-10-06 11:40:43', '2018-10-06 11:40:43'),
(12, 49, '', 'size_1', '1', 'create', '2018-10-08 01:18:03', '2018-10-08 01:18:03'),
(13, 49, '', 'size_2', '1', 'create', '2018-10-08 01:18:08', '2018-10-08 01:18:08'),
(14, 49, '', 'size_3', '1', 'create', '2018-10-08 01:18:12', '2018-10-08 01:18:12'),
(15, 49, '', 'size_4', '1', 'create', '2018-10-08 01:18:16', '2018-10-08 01:18:16'),
(21, 49, '8KMHT2', 'sds', '1', 'create', '2018-10-08 01:29:52', '2018-10-08 01:29:52'),
(22, 49, '8KMHT2', 'size_1', '1', 'create', '2018-10-08 01:29:52', '2018-10-08 01:29:52'),
(23, 49, '8KMHT2', 'size_2', '1', 'create', '2018-10-08 01:29:52', '2018-10-08 01:29:52'),
(24, 49, '8KMHT2', 'size_3', '1', 'create', '2018-10-08 01:29:52', '2018-10-08 01:29:52'),
(25, 49, '8KMHT2', 'size_4', '1', 'create', '2018-10-08 01:29:52', '2018-10-08 01:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_products_colors`
--

CREATE TABLE `mxp_products_colors` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_products_colors`
--

INSERT INTO `mxp_products_colors` (`id`, `product_id`, `color_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 1, '2018-09-27 01:13:18', '2018-09-27 01:13:18'),
(6, 3, 4, 1, '2018-09-29 01:33:54', '2018-09-29 01:33:54'),
(8, 2, 4, 1, '2018-09-29 04:48:40', '2018-09-29 04:48:40'),
(9, 55, 4, 1, '2018-10-06 11:40:43', '2018-10-06 11:40:43'),
(13, 4, 4, 1, '2018-10-08 01:29:51', '2018-10-08 01:29:51'),
(14, 4, 12, 1, '2018-10-08 01:29:51', '2018-10-08 01:29:51'),
(15, 4, 13, 1, '2018-10-08 01:29:51', '2018-10-08 01:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_products_sizes`
--

CREATE TABLE `mxp_products_sizes` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_products_sizes`
--

INSERT INTO `mxp_products_sizes` (`id`, `product_id`, `size_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 1, '2018-09-27 01:13:19', '2018-09-27 01:13:19'),
(6, 3, 4, 1, '2018-09-29 01:33:55', '2018-09-29 01:33:55'),
(8, 2, 4, 1, '2018-09-29 04:48:40', '2018-09-29 04:48:40'),
(9, 55, 4, 1, '2018-10-06 11:40:43', '2018-10-06 11:40:43'),
(15, 4, 4, 1, '2018-10-08 01:29:51', '2018-10-08 01:29:51'),
(16, 4, 12, 1, '2018-10-08 01:29:52', '2018-10-08 01:29:52'),
(17, 4, 13, 1, '2018-10-08 01:29:52', '2018-10-08 01:29:52'),
(18, 4, 14, 1, '2018-10-08 01:29:52', '2018-10-08 01:29:52'),
(19, 4, 15, 1, '2018-10-08 01:29:52', '2018-10-08 01:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_purchase_orders`
--

CREATE TABLE `mxp_purchase_orders` (
  `po_id` int(10) UNSIGNED NOT NULL,
  `po_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_order_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipment_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `erp_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_quantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmts_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mxp_reportfooter`
--

CREATE TABLE `mxp_reportfooter` (
  `re_footer_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `reportName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_4` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_5` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `siginingPerson_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `siginingPersonSeal_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `siginingSignature_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `siginingPerson_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `siginingSignature_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `siginingPersonSeal_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_reportfooter`
--

INSERT INTO `mxp_reportfooter` (`re_footer_id`, `user_id`, `reportName`, `description_1`, `description_2`, `description_3`, `description_4`, `description_5`, `siginingPerson_1`, `siginingPersonSeal_1`, `siginingSignature_1`, `siginingPerson_2`, `siginingSignature_2`, `siginingPersonSeal_2`, `status`, `created_at`, `updated_at`, `action`) VALUES
(2, 49, 'challan report', '', '', '', '', '', 'Accepted', NULL, NULL, 'For Maxim', NULL, NULL, '1', '2018-04-17 00:14:14', '2018-09-20 04:20:30', 'create');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_role`
--

CREATE TABLE `mxp_role` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` int(11) NOT NULL,
  `cm_group_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_role`
--

INSERT INTO `mxp_role` (`id`, `name`, `company_id`, `cm_group_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 0, '', 1, '2018-01-14 20:58:10', '2018-01-25 04:51:10'),
(36, 'super-admin-idlc', 19, '566', 1, '2018-09-06 04:14:26', '2018-09-06 04:14:26'),
(37, 'Customer Service', 17, '942', 1, '2018-09-09 21:46:02', '2018-09-09 21:46:02'),
(38, 'Planning Team', 17, '757', 1, '2018-09-23 23:07:17', '2018-09-23 23:07:17'),
(39, 'Customer Service ( All Management access)', 17, '169', 1, '2018-09-24 23:30:29', '2018-09-24 23:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_supplier_prices`
--

CREATE TABLE `mxp_supplier_prices` (
  `supplier_price_id` int(10) UNSIGNED NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_price` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_supplier_prices`
--

INSERT INTO `mxp_supplier_prices` (`supplier_price_id`, `supplier_id`, `product_id`, `supplier_price`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1.05, '2018-07-27 03:49:55', '2018-07-27 03:49:55'),
(2, 2, 13, NULL, '2018-09-05 12:03:00', '2018-09-05 12:03:00'),
(3, 3, 13, NULL, '2018-09-05 12:03:00', '2018-09-05 12:03:00'),
(4, 4, 13, 0.10, '2018-09-05 12:03:00', '2018-09-05 12:03:00'),
(5, 2, 14, NULL, '2018-09-09 22:50:46', '2018-09-09 22:50:46'),
(6, 3, 14, NULL, '2018-09-09 22:50:46', '2018-09-09 22:50:46'),
(7, 4, 14, NULL, '2018-09-09 22:50:46', '2018-09-09 22:50:46'),
(8, 2, 14, NULL, '2018-09-14 22:31:24', '2018-09-14 22:31:24'),
(9, 3, 14, NULL, '2018-09-14 22:31:24', '2018-09-14 22:31:24'),
(10, 4, 14, NULL, '2018-09-14 22:31:24', '2018-09-14 22:31:24'),
(11, 2, 15, NULL, '2018-09-14 22:32:39', '2018-09-14 22:32:39'),
(12, 3, 15, NULL, '2018-09-14 22:32:39', '2018-09-14 22:32:39'),
(13, 4, 15, NULL, '2018-09-14 22:32:39', '2018-09-14 22:32:39'),
(14, 1, 1, NULL, '2018-09-15 04:00:58', '2018-09-15 04:00:58'),
(15, 3, 1, NULL, '2018-09-15 04:00:58', '2018-09-15 04:00:58'),
(16, 4, 1, NULL, '2018-09-15 04:00:58', '2018-09-15 04:00:58'),
(17, 2, 1, NULL, '2018-09-15 04:00:58', '2018-09-15 04:00:58'),
(18, 2, 16, 1.20, '2018-09-17 03:26:26', '2018-09-17 03:26:26'),
(19, 3, 16, 1.20, '2018-09-17 03:26:26', '2018-09-17 03:26:26'),
(20, 4, 16, 1.20, '2018-09-17 03:26:26', '2018-09-17 03:26:26'),
(21, 2, 17, NULL, '2018-09-17 03:34:01', '2018-09-17 03:34:01'),
(22, 3, 17, NULL, '2018-09-17 03:34:01', '2018-09-17 03:34:01'),
(23, 4, 17, NULL, '2018-09-17 03:34:01', '2018-09-17 03:34:01'),
(24, 2, 18, NULL, '2018-09-19 00:11:18', '2018-09-19 00:11:18'),
(25, 3, 18, NULL, '2018-09-19 00:11:18', '2018-09-19 00:11:18'),
(26, 4, 18, NULL, '2018-09-19 00:11:18', '2018-09-19 00:11:18'),
(27, 1, 2, NULL, '2018-09-29 01:13:22', '2018-09-29 01:13:22'),
(28, 3, 2, NULL, '2018-09-29 01:13:22', '2018-09-29 01:13:22'),
(29, 4, 2, NULL, '2018-09-29 01:13:22', '2018-09-29 01:13:22'),
(30, 2, 2, NULL, '2018-09-29 01:13:22', '2018-09-29 01:13:22'),
(31, 1, 3, NULL, '2018-09-29 01:33:54', '2018-09-29 01:33:54'),
(32, 3, 3, NULL, '2018-09-29 01:33:54', '2018-09-29 01:33:54'),
(33, 4, 3, NULL, '2018-09-29 01:33:54', '2018-09-29 01:33:54'),
(34, 2, 3, NULL, '2018-09-29 01:33:54', '2018-09-29 01:33:54'),
(35, 5, 55, 1.00, '2018-10-06 11:40:43', '2018-10-06 11:40:43'),
(36, 5, 56, NULL, '2018-10-06 11:46:09', '2018-10-06 11:46:09');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_task`
--

CREATE TABLE `mxp_task` (
  `id_mxp_task` int(10) UNSIGNED NOT NULL,
  `name` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_task`
--

INSERT INTO `mxp_task` (`id_mxp_task`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'booking', 1, NULL, NULL),
(2, 'PI', 1, NULL, NULL),
(3, 'IPO', 1, NULL, NULL),
(4, 'MRF', 1, NULL, NULL),
(5, 'challan', 1, NULL, NULL),
(6, 'bill', 1, '2018-08-07 18:00:00', '2018-08-07 18:00:00'),
(7, 'FSC Booking', 0, '2018-09-11 18:00:00', '2018-09-11 18:00:00'),
(8, 'FSC PI', 1, '2018-09-11 18:00:00', '2018-09-11 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_task_role`
--

CREATE TABLE `mxp_task_role` (
  `id_mxp_task_role` int(10) UNSIGNED NOT NULL,
  `role_id` int(11) NOT NULL,
  `task` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_task_role`
--

INSERT INTO `mxp_task_role` (`id_mxp_task_role`, `role_id`, `task`) VALUES
(1, 1, '1,2,3,4,5,6,8'),
(2, 36, ''),
(3, 37, '1,2,5,6,8'),
(4, 38, '3,4'),
(5, 39, '1,2,5,6,8');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_translations`
--

CREATE TABLE `mxp_translations` (
  `translation_id` int(10) UNSIGNED NOT NULL,
  `translation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `translation_key_id` int(11) DEFAULT NULL,
  `lan_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `same_trans_key_id` int(11) NOT NULL,
  `is_active` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_translations`
--

INSERT INTO `mxp_translations` (`translation_id`, `translation`, `translation_key_id`, `lan_code`, `same_trans_key_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Maxim Order Management System', 1, 'en', 0, 1, '2018-03-05 18:12:49', '2018-09-13 04:21:31'),
(2, 'ম্যাক্সিম অর্ডার ম্যানেজমেন্ট সিস্টেম', 1, 'bn', 0, 1, '2018-03-05 18:12:49', '2018-09-13 04:21:31'),
(3, 'Log In', 2, 'en', 0, 1, '2018-03-05 20:38:51', '2018-03-05 20:39:11'),
(4, 'লগ ইন', 2, 'bn', 0, 1, '2018-03-05 20:38:51', '2018-03-05 20:39:11'),
(5, 'Registration', 3, 'en', 0, 1, '2018-03-05 20:39:27', '2018-03-05 20:41:56'),
(6, 'নিবন্ধন করুন', 3, 'bn', 0, 1, '2018-03-05 20:39:27', '2018-03-05 20:41:56'),
(7, 'Whoops!', 4, 'en', 0, 1, '2018-03-05 20:54:56', '2018-03-05 21:04:24'),
(8, 'উপস!', 4, 'bn', 0, 1, '2018-03-05 20:54:56', '2018-03-05 21:04:24'),
(9, 'There were some problems with your input.', 5, 'en', 0, 1, '2018-03-05 20:56:52', '2018-03-05 21:03:46'),
(10, 'আপনার ইনপুট সঙ্গে কিছু সমস্যা ছিল।', 5, 'bn', 0, 1, '2018-03-05 20:56:52', '2018-03-05 21:03:46'),
(11, 'Or you are not active yet.', 6, 'en', 0, 1, '2018-03-05 20:57:04', '2018-03-05 21:03:01'),
(12, 'অথবা আপনি এখনো সক্রিয় নন', 6, 'bn', 0, 1, '2018-03-05 20:57:04', '2018-03-05 21:03:01'),
(13, 'E-Mail Address', 7, 'en', 0, 1, '2018-03-05 20:57:14', '2018-03-05 20:59:25'),
(14, 'ই-মেইল ঠিকানা', 7, 'bn', 0, 1, '2018-03-05 20:57:14', '2018-03-05 20:59:25'),
(15, 'Password', 8, 'en', 0, 1, '2018-03-05 20:57:22', '2018-03-05 21:00:01'),
(16, 'পাসওয়ার্ড', 8, 'bn', 0, 1, '2018-03-05 20:57:22', '2018-03-05 21:00:01'),
(17, 'Remember me?', 9, 'en', 0, 1, '2018-03-05 20:57:31', '2018-03-05 21:02:15'),
(18, 'আমাকে মনে রাখুন?', 9, 'bn', 0, 1, '2018-03-05 20:57:31', '2018-03-05 21:02:15'),
(19, 'Forgot Your Password?', 10, 'en', 0, 1, '2018-03-05 20:57:39', '2018-03-05 21:00:39'),
(20, 'আপনি কি পাসওয়ার্ড ভুলে গেছেন?', 10, 'bn', 0, 1, '2018-03-05 20:57:39', '2018-03-05 21:00:39'),
(21, 'Dashboard', 11, 'en', 0, 1, '2018-03-05 23:23:51', '2018-03-05 23:32:59'),
(22, 'ড্যাশবোর্ড', 11, 'bn', 0, 1, '2018-03-05 23:23:51', '2018-03-05 23:32:59'),
(23, 'Language List', 12, 'en', 0, 1, '2018-03-05 23:34:35', '2018-03-05 23:35:06'),
(24, 'ভাষা তালিকা', 12, 'bn', 0, 1, '2018-03-05 23:34:35', '2018-03-05 23:35:06'),
(25, 'Serial no.', 13, 'en', 0, 1, '2018-03-05 23:36:43', '2018-03-05 23:37:54'),
(26, 'ক্রমিক নং', 13, 'bn', 0, 1, '2018-03-05 23:36:44', '2018-03-05 23:37:54'),
(27, 'Language Title', 14, 'en', 0, 1, '2018-03-05 23:38:13', '2018-03-05 23:38:37'),
(28, 'ভাষা শিরোনাম', 14, 'bn', 0, 1, '2018-03-05 23:38:13', '2018-03-05 23:38:37'),
(29, 'Language Code', 15, 'en', 0, 1, '2018-03-05 23:38:47', '2018-03-05 23:39:11'),
(30, 'ভাষা কোড', 15, 'bn', 0, 1, '2018-03-05 23:38:47', '2018-03-05 23:39:11'),
(31, 'Status', 16, 'en', 0, 1, '2018-03-05 23:39:23', '2018-03-05 23:40:25'),
(32, 'সাময়িক অবস্থা', 16, 'bn', 0, 1, '2018-03-05 23:39:23', '2018-03-05 23:40:25'),
(33, 'Action', 17, 'en', 0, 1, '2018-03-05 23:40:40', '2018-03-05 23:42:00'),
(34, 'ক্রিয়াকলাপ', 17, 'bn', 0, 1, '2018-03-05 23:40:40', '2018-03-05 23:42:00'),
(35, 'Active', 18, 'en', 0, 1, '2018-03-05 23:43:00', '2018-03-05 23:43:27'),
(36, 'সক্রিয়', 18, 'bn', 0, 1, '2018-03-05 23:43:00', '2018-03-05 23:43:27'),
(37, 'Inactive', 19, 'en', 0, 1, '2018-03-05 23:43:47', '2018-03-05 23:44:13'),
(38, 'নিষ্ক্রিয়', 19, 'bn', 0, 1, '2018-03-05 23:43:47', '2018-03-05 23:44:13'),
(39, 'Add Locale', 20, 'en', 0, 1, '2018-03-05 23:58:03', '2018-03-05 23:59:51'),
(40, 'স্থান যোগ করুন', 20, 'bn', 0, 1, '2018-03-05 23:58:03', '2018-03-05 23:59:52'),
(41, 'edit', 21, 'en', 0, 1, '2018-03-06 00:00:03', '2018-03-06 00:01:53'),
(42, 'পরিবর্তন করুন', 21, 'bn', 0, 1, '2018-03-06 00:00:03', '2018-03-06 00:01:53'),
(43, 'Add new Language', 22, 'en', 0, 1, '2018-03-06 00:14:26', '2018-03-06 00:15:12'),
(44, 'নতুন ভাষা যোগ করুন', 22, 'bn', 0, 1, '2018-03-06 00:14:26', '2018-03-06 00:15:12'),
(45, 'Add Language', 23, 'en', 0, 1, '2018-03-06 00:15:45', '2018-03-06 00:16:16'),
(46, 'ভাষা যোগ করুন', 23, 'bn', 0, 1, '2018-03-06 00:15:45', '2018-03-06 00:16:16'),
(47, 'Enter Language Title', 24, 'en', 0, 1, '2018-03-06 00:16:49', '2018-03-06 00:17:21'),
(48, 'ভাষা শিরোনাম লিখুন', 24, 'bn', 0, 1, '2018-03-06 00:16:49', '2018-03-06 00:17:21'),
(49, 'Enter Language Code', 25, 'en', 0, 1, '2018-03-06 00:17:31', '2018-03-06 00:17:54'),
(50, 'ভাষা কোড লিখুন', 25, 'bn', 0, 1, '2018-03-06 00:17:31', '2018-03-06 00:17:54'),
(51, 'Save', 26, 'en', 0, 1, '2018-03-06 00:18:57', '2018-03-06 00:19:17'),
(52, 'সংরক্ষণ করুন', 26, 'bn', 0, 1, '2018-03-06 00:18:57', '2018-03-06 00:19:17'),
(53, 'Update Locale', 27, 'en', 0, 1, '2018-03-06 00:23:12', '2018-03-06 00:28:13'),
(54, 'লোকেল আপডেট করুন', 27, 'bn', 0, 1, '2018-03-06 00:23:12', '2018-03-06 00:28:13'),
(55, 'Update Language Title', 28, 'en', 0, 1, '2018-03-06 00:28:35', '2018-03-06 00:29:18'),
(56, 'ভাষা শিরোনাম আপডেট করুন', 28, 'bn', 0, 1, '2018-03-06 00:28:36', '2018-03-06 00:29:18'),
(57, 'Update Language Code', 29, 'en', 0, 1, '2018-03-06 00:29:32', '2018-03-06 00:29:55'),
(58, 'ভাষা কোড আপডেট করুন', 29, 'bn', 0, 1, '2018-03-06 00:29:32', '2018-03-06 00:29:55'),
(59, 'Update', 30, 'en', 0, 1, '2018-03-06 00:30:07', '2018-03-06 00:30:52'),
(60, 'পরিবর্তন করুন', 30, 'bn', 0, 1, '2018-03-06 00:30:07', '2018-03-06 00:30:52'),
(61, 'Update Language', 31, 'en', 0, 1, '2018-03-06 00:32:05', '2018-03-06 00:32:45'),
(62, 'ভাষা আপডেট করুন', 31, 'bn', 0, 1, '2018-03-06 00:32:05', '2018-03-06 00:32:45'),
(63, 'Comfirm! you want to upload translation file..', 32, 'en', 0, 1, '2018-03-06 00:34:41', '2018-03-06 00:36:01'),
(64, 'নিশ্চিত করুন! আপনি অনুবাদ ফাইল আপলোড করতে চান ..', 32, 'bn', 0, 1, '2018-03-06 00:34:41', '2018-03-06 00:36:01'),
(65, 'Upload', 33, 'en', 0, 1, '2018-03-06 00:36:42', '2018-03-06 00:37:14'),
(66, 'আপলোড', 33, 'bn', 0, 1, '2018-03-06 00:36:42', '2018-03-06 00:37:14'),
(67, 'Translation List', 34, 'en', 0, 1, '2018-03-06 00:39:26', '2018-03-06 00:49:15'),
(68, 'অনুবাদ তালিকা', 34, 'bn', 0, 1, '2018-03-06 00:39:26', '2018-03-06 00:49:15'),
(69, 'Add new key', 35, 'en', 0, 1, '2018-03-06 00:49:29', '2018-03-06 00:51:01'),
(70, 'নতুন টীকা যোগ করুন', 35, 'bn', 0, 1, '2018-03-06 00:49:29', '2018-03-06 00:51:01'),
(71, 'Search the translation key....', 36, 'en', 0, 1, '2018-03-06 00:51:16', '2018-03-06 00:52:27'),
(72, 'অনুবাদ টীকা অনুসন্ধান করুন ....', 36, 'bn', 0, 1, '2018-03-06 00:51:16', '2018-03-06 00:52:27'),
(73, 'Translation key', 37, 'en', 0, 1, '2018-03-06 00:52:45', '2018-03-06 00:54:17'),
(74, 'অনুবাদ টীকা', 37, 'bn', 0, 1, '2018-03-06 00:52:45', '2018-03-06 00:54:17'),
(75, 'Translation', 38, 'en', 0, 1, '2018-03-06 00:54:31', '2018-03-06 00:55:09'),
(76, 'অনুবাদ', 38, 'bn', 0, 1, '2018-03-06 00:54:31', '2018-03-06 00:55:09'),
(77, 'Language', 39, 'en', 0, 1, '2018-03-06 00:55:21', '2018-03-06 00:55:50'),
(78, 'ভাষা', 39, 'bn', 0, 1, '2018-03-06 00:55:21', '2018-03-06 00:55:50'),
(79, 'Delete', 40, 'en', 0, 1, '2018-03-06 00:56:29', '2018-03-06 00:56:51'),
(80, 'বাদ দিন', 40, 'bn', 0, 1, '2018-03-06 00:56:29', '2018-03-06 00:56:51'),
(81, 'Add new translation key', 41, 'en', 0, 1, '2018-03-06 01:07:29', '2018-03-06 01:08:09'),
(82, 'নতুন অনুবাদ টীকা যোগ করুন', 41, 'bn', 0, 1, '2018-03-06 01:07:29', '2018-03-06 01:08:09'),
(83, 'Enter Translation key', 42, 'en', 0, 1, '2018-03-06 01:08:20', '2018-03-06 01:09:01'),
(84, 'অনুবাদ টীকা প্রবেশ করান', 42, 'bn', 0, 1, '2018-03-06 01:08:20', '2018-03-06 01:09:01'),
(85, 'Update Translation', 43, 'en', 0, 1, '2018-03-06 01:18:54', '2018-03-06 01:19:29'),
(86, 'অনুবাদ আপডেট করুন', 43, 'bn', 0, 1, '2018-03-06 01:18:54', '2018-03-06 01:19:29'),
(87, 'Update Translation key', 44, 'en', 0, 1, '2018-03-06 01:19:50', '2018-03-06 01:20:39'),
(88, 'অনুবাদ টীকা আপডেট করুন', 44, 'bn', 0, 1, '2018-03-06 01:19:50', '2018-03-06 01:20:39'),
(89, 'LANGUAGE', 45, 'en', 0, 1, '2018-03-06 19:21:58', '2018-03-06 19:27:49'),
(90, 'ভাষা', 45, 'bn', 0, 1, '2018-03-06 19:21:58', '2018-03-06 19:27:49'),
(91, 'Manage Language', 46, 'en', 0, 1, '2018-03-06 19:23:15', '2018-03-06 19:24:25'),
(92, 'ভাষা পরিচালনা করুন', 46, 'bn', 0, 1, '2018-03-06 19:23:15', '2018-03-06 19:24:25'),
(93, 'Manage Translation', 47, 'en', 0, 1, '2018-03-06 19:24:37', '2018-03-06 19:25:16'),
(94, 'অনুবাদ পরিচালনা করুন', 47, 'bn', 0, 1, '2018-03-06 19:24:37', '2018-03-06 19:25:17'),
(95, 'Upload Language File', 48, 'en', 0, 1, '2018-03-06 19:25:41', '2018-03-06 19:26:18'),
(96, 'ভাষা ফাইল আপলোড করুন', 48, 'bn', 0, 1, '2018-03-06 19:25:41', '2018-03-06 19:26:18'),
(97, 'ROLE', 49, 'en', 0, 1, '2018-03-06 19:26:59', '2018-03-06 19:27:26'),
(98, 'ভূমিকা', 49, 'bn', 0, 1, '2018-03-06 19:26:59', '2018-03-06 19:27:26'),
(99, 'Add New Role', 50, 'en', 0, 1, '2018-03-06 19:28:03', '2018-03-06 19:29:56'),
(100, 'নতুন ভূমিকা যোগ করুন', 50, 'bn', 0, 1, '2018-03-06 19:28:03', '2018-03-06 19:29:56'),
(101, 'Role List', 51, 'en', 0, 1, '2018-03-06 19:30:11', '2018-03-06 19:30:35'),
(102, 'ভূমিকা তালিকা', 51, 'bn', 0, 1, '2018-03-06 19:30:11', '2018-03-06 19:30:36'),
(103, 'Role Permission', 52, 'en', 0, 1, '2018-03-06 19:30:45', '2018-03-06 19:31:10'),
(104, 'ভূমিকা অনুমতি', 52, 'bn', 0, 1, '2018-03-06 19:30:45', '2018-03-06 19:31:10'),
(105, 'SETTINGS', 53, 'en', 0, 1, '2018-03-06 19:31:22', '2018-03-06 19:31:55'),
(106, 'সেটিংস', 53, 'bn', 0, 1, '2018-03-06 19:31:22', '2018-03-06 19:31:55'),
(107, 'Open Company Account', 54, 'en', 0, 1, '2018-03-06 19:32:15', '2018-03-06 19:34:08'),
(108, 'কোম্পানি অ্যাকাউন্ট খোলা', 54, 'bn', 0, 1, '2018-03-06 19:32:15', '2018-03-06 19:34:08'),
(109, 'Company List', 55, 'en', 0, 1, '2018-03-06 19:34:19', '2018-03-06 19:34:45'),
(110, 'কোম্পানি তালিকা', 55, 'bn', 0, 1, '2018-03-06 19:34:19', '2018-03-06 19:34:45'),
(111, 'Create User', 56, 'en', 0, 1, '2018-03-06 19:34:56', '2018-03-06 19:36:05'),
(112, 'নতুন ব্যবহারকারী সংযোজন', 56, 'bn', 0, 1, '2018-03-06 19:34:56', '2018-03-06 19:36:05'),
(113, 'Create User', 57, 'en', 0, 1, '2018-03-06 19:36:15', '2018-03-06 19:38:03'),
(114, 'নতুন ব্যবহারকারী সংযোজন', 57, 'bn', 0, 1, '2018-03-06 19:36:15', '2018-03-06 19:38:03'),
(115, 'User List', 58, 'en', 0, 1, '2018-03-06 19:39:56', '2018-03-06 19:40:22'),
(116, 'ব্যবহারকারীর তালিকা', 58, 'bn', 0, 1, '2018-03-06 19:39:56', '2018-03-06 19:40:22'),
(117, 'Client List', 59, 'en', 0, 1, '2018-03-06 19:40:33', '2018-03-06 19:41:36'),
(118, 'গ্রাহকের তালিকা', 59, 'bn', 0, 1, '2018-03-06 19:40:33', '2018-03-06 19:41:36'),
(119, 'PRODUCT', 60, 'en', 0, 1, '2018-03-06 19:41:56', '2018-03-06 19:42:18'),
(120, 'পণ্য', 60, 'bn', 0, 1, '2018-03-06 19:41:56', '2018-03-06 19:42:18'),
(121, 'Product\'s Unit', 61, 'en', 0, 1, '2018-03-06 19:42:32', '2018-03-06 19:48:13'),
(122, 'পণ্যের একক', 61, 'bn', 0, 1, '2018-03-06 19:42:32', '2018-03-06 19:48:13'),
(123, 'Product Group', 62, 'en', 0, 1, '2018-03-06 19:48:24', '2018-03-06 19:48:54'),
(124, 'পণ্যের গ্রুপ', 62, 'bn', 0, 1, '2018-03-06 19:48:25', '2018-03-06 19:48:54'),
(125, 'Product Entry', 63, 'en', 0, 1, '2018-03-06 19:49:03', '2018-03-06 19:50:00'),
(126, 'পণ্য সংযোজন', 63, 'bn', 0, 1, '2018-03-06 19:49:03', '2018-03-06 19:50:00'),
(127, 'Product Packing', 64, 'en', 0, 1, '2018-03-06 19:50:09', '2018-03-06 19:50:39'),
(128, 'পণ্য প্যাকেজিং', 64, 'bn', 0, 1, '2018-03-06 19:50:09', '2018-03-06 19:50:39'),
(129, 'Purchase', 65, 'en', 0, 1, '2018-03-06 19:50:54', '2018-03-06 19:51:38'),
(130, 'ক্রয়', 65, 'bn', 0, 1, '2018-03-06 19:50:54', '2018-03-06 19:51:38'),
(131, 'Purchase List', 66, 'en', 0, 1, '2018-03-06 19:51:47', '2018-03-06 19:52:14'),
(132, 'ক্রয় তালিকা', 66, 'bn', 0, 1, '2018-03-06 19:51:48', '2018-03-06 19:52:14'),
(133, 'Update Stock', 67, 'en', 0, 1, '2018-03-06 19:52:27', '2018-03-06 19:53:39'),
(134, 'আপডেট স্টক', 67, 'bn', 0, 1, '2018-03-06 19:52:27', '2018-03-06 19:53:40'),
(135, 'Vat Tax List', 68, 'en', 0, 1, '2018-03-06 19:53:48', '2018-03-06 19:54:15'),
(136, 'ভ্যাট ট্যাক্স তালিকা', 68, 'bn', 0, 1, '2018-03-06 19:53:48', '2018-03-06 19:54:15'),
(137, 'Sale List', 69, 'en', 0, 1, '2018-03-06 19:54:25', '2018-03-06 19:54:55'),
(138, 'বিক্রয় তালিকা', 69, 'bn', 0, 1, '2018-03-06 19:54:25', '2018-03-06 19:54:55'),
(139, 'Save Sale', 70, 'en', 0, 1, '2018-03-06 19:55:15', '2018-03-06 19:56:07'),
(140, 'বিক্রয় যোগ করুন', 70, 'bn', 0, 1, '2018-03-06 19:55:15', '2018-03-06 19:56:07'),
(141, 'Inventory Report', 71, 'en', 0, 1, '2018-03-06 19:56:45', '2018-03-06 19:57:12'),
(142, 'পরিসংখ্যা প্রতিবেদন', 71, 'bn', 0, 1, '2018-03-06 19:56:45', '2018-03-06 19:57:12'),
(143, 'STOCK MANAGEMENT', 72, 'en', 0, 1, '2018-03-06 19:57:21', '2018-03-06 19:57:51'),
(144, 'স্টক ব্যবস্থাপনা', 72, 'bn', 0, 1, '2018-03-06 19:57:21', '2018-03-06 19:57:51'),
(145, 'Store', 73, 'en', 0, 1, '2018-03-06 19:58:01', '2018-03-06 19:58:42'),
(146, 'ভাণ্ডার', 73, 'bn', 0, 1, '2018-03-06 19:58:01', '2018-03-06 19:58:42'),
(147, 'Stock', 74, 'en', 0, 1, '2018-03-06 19:58:53', '2018-03-06 19:59:16'),
(148, 'স্টক', 74, 'bn', 0, 1, '2018-03-06 19:58:53', '2018-03-06 19:59:17'),
(151, 'Company/Client Name', 76, 'en', 0, 1, '2018-03-06 20:57:06', '2018-03-06 20:57:55'),
(152, 'কোম্পানী / গ্রাহকের নাম', 76, 'bn', 0, 1, '2018-03-06 20:57:06', '2018-03-06 20:57:55'),
(153, 'Role Name', 77, 'en', 0, 1, '2018-03-06 21:05:38', '2018-03-06 21:06:30'),
(154, 'ভূমিকার নাম', 77, 'bn', 0, 1, '2018-03-06 21:05:38', '2018-03-06 21:06:30'),
(155, 'Select Company/Client', 78, 'en', 0, 1, '2018-03-06 21:06:59', '2018-03-06 21:07:40'),
(156, 'কোম্পানী / ক্লায়েন্ট নির্বাচন করুন', 78, 'bn', 0, 1, '2018-03-06 21:06:59', '2018-03-06 21:07:40'),
(157, 'Select Role', 79, 'en', 0, 1, '2018-03-06 21:08:51', '2018-03-06 21:09:15'),
(158, 'ভূমিকা নির্বাচন করুন', 79, 'bn', 0, 1, '2018-03-06 21:08:51', '2018-03-06 21:09:15'),
(159, 'Select All', 80, 'en', 0, 1, '2018-03-06 21:11:57', '2018-03-06 21:12:22'),
(160, 'সব নির্বাচন করুন', 80, 'bn', 0, 1, '2018-03-06 21:11:57', '2018-03-06 21:12:23'),
(161, 'Unselect all', 81, 'en', 0, 1, '2018-03-06 21:12:36', '2018-03-06 21:12:57'),
(162, 'সরিয়ে ফেলুন সব', 81, 'bn', 0, 1, '2018-03-06 21:12:36', '2018-03-06 21:12:57'),
(163, 'SET', 82, 'en', 0, 1, '2018-03-06 21:14:03', '2018-03-06 21:14:34'),
(164, 'নিযুক্ত করা', 82, 'bn', 0, 1, '2018-03-06 21:14:03', '2018-03-06 21:14:34'),
(165, 'Assign Role', 83, 'en', 0, 1, '2018-03-06 21:15:41', '2018-03-06 21:16:07'),
(166, 'ভূমিকা অর্পণ', 83, 'bn', 0, 1, '2018-03-06 21:15:41', '2018-03-06 21:16:07'),
(167, 'Role Permission List', 84, 'en', 0, 1, '2018-03-06 21:19:23', '2018-03-06 21:19:45'),
(168, 'ভূমিকা অনুমতি তালিকা', 84, 'bn', 0, 1, '2018-03-06 21:19:23', '2018-03-06 21:19:45'),
(169, 'Permitted Route List', 85, 'en', 0, 1, '2018-03-06 21:19:57', '2018-03-06 21:20:30'),
(170, 'অনুমোদিত রুট তালিকা', 85, 'bn', 0, 1, '2018-03-06 21:19:57', '2018-03-06 21:20:30'),
(171, 'Update Role', 86, 'en', 0, 1, '2018-03-06 21:36:58', '2018-03-06 21:37:20'),
(172, 'আপডেট ভূমিকা', 86, 'bn', 0, 1, '2018-03-06 21:36:58', '2018-03-06 21:37:20'),
(173, 'Add Stock', 87, 'en', 0, 1, '2018-03-06 22:00:58', '2018-03-06 22:01:24'),
(174, 'স্টক যোগ করুন', 87, 'bn', 0, 1, '2018-03-06 22:00:58', '2018-03-06 22:01:24'),
(175, 'Item Name', 88, 'en', 0, 1, '2018-03-06 22:01:41', '2018-07-09 01:32:55'),
(176, 'পণ্য  নাম', 88, 'bn', 0, 1, '2018-03-06 22:01:41', '2018-07-09 01:32:55'),
(177, 'Product/Particular Group', 89, 'en', 0, 1, '2018-03-06 22:02:40', '2018-03-06 22:03:10'),
(178, 'পণ্য / বিশেষ গ্রুপ', 89, 'bn', 0, 1, '2018-03-06 22:02:40', '2018-03-06 22:03:10'),
(179, 'Quantity', 90, 'en', 0, 1, '2018-03-06 22:03:38', '2018-03-06 22:04:05'),
(180, 'পরিমাণ', 90, 'bn', 0, 1, '2018-03-06 22:03:38', '2018-03-06 22:04:05'),
(181, 'Select Location', 91, 'en', 0, 1, '2018-03-06 22:04:43', '2018-03-06 22:05:00'),
(182, 'স্থান নির্বাচন করুন', 91, 'bn', 0, 1, '2018-03-06 22:04:43', '2018-03-06 22:05:01'),
(187, 'Add new Store', 94, 'en', 0, 1, '2018-03-06 22:21:41', '2018-03-06 22:22:04'),
(188, 'নতুন স্টোর যোগ করুন', 94, 'bn', 0, 1, '2018-03-06 22:21:41', '2018-03-06 22:22:04'),
(189, 'Add store', 95, 'en', 0, 1, '2018-03-06 22:22:14', '2018-03-06 22:22:58'),
(190, 'স্টোর যোগ করুন', 95, 'bn', 0, 1, '2018-03-06 22:22:14', '2018-03-06 22:22:58'),
(191, 'Enter Store Name', 96, 'en', 0, 1, '2018-03-06 22:23:21', '2018-03-06 22:23:42'),
(192, 'স্টোর নাম লিখুন', 96, 'bn', 0, 1, '2018-03-06 22:23:21', '2018-03-06 22:23:42'),
(193, 'Enter Store Location', 97, 'en', 0, 1, '2018-03-06 22:23:51', '2018-03-06 22:24:16'),
(194, 'দোকানের অবস্থান লিখুন', 97, 'bn', 0, 1, '2018-03-06 22:23:51', '2018-03-06 22:24:16'),
(195, 'Update Store', 98, 'en', 0, 1, '2018-03-06 22:27:47', '2018-03-06 22:28:16'),
(196, 'আপডেট স্টোর', 98, 'bn', 0, 1, '2018-03-06 22:27:47', '2018-03-06 22:28:16'),
(199, 'Store List', 100, 'en', 0, 1, '2018-03-06 22:34:46', '2018-03-06 22:36:17'),
(200, 'স্টোর তালিকা', 100, 'bn', 0, 1, '2018-03-06 22:34:46', '2018-03-06 22:36:17'),
(201, 'Store Name', 101, 'en', 0, 1, '2018-03-06 22:36:32', '2018-03-06 22:37:16'),
(202, 'স্টোরের নাম', 101, 'bn', 0, 1, '2018-03-06 22:36:32', '2018-03-06 22:37:16'),
(203, 'Store Location', 102, 'en', 0, 1, '2018-03-06 22:37:36', '2018-03-06 22:38:13'),
(204, 'স্টোরের অবস্থান', 102, 'bn', 0, 1, '2018-03-06 22:37:36', '2018-03-06 22:38:13'),
(205, 'List of Responsible people', 103, 'en', 0, 1, '2018-03-06 22:45:51', '2018-03-06 22:46:15'),
(206, 'দায়ী ব্যক্তিদের তালিকা', 103, 'bn', 0, 1, '2018-03-06 22:45:51', '2018-03-06 22:46:15'),
(207, 'Company/Client Phone Number', 104, 'en', 0, 1, '2018-03-07 21:50:23', '2018-03-07 21:51:13'),
(208, 'কোম্পানী / ক্লায়েন্ট ফোন নম্বর', 104, 'bn', 0, 1, '2018-03-07 21:50:23', '2018-03-07 21:51:13'),
(209, 'Company/Client Address', 105, 'en', 0, 1, '2018-03-07 21:51:29', '2018-03-07 21:51:58'),
(210, 'কোম্পানী / ক্লায়েন্ট ঠিকানা', 105, 'bn', 0, 1, '2018-03-07 21:51:29', '2018-03-07 21:51:58'),
(211, 'Company/Client Description', 106, 'en', 0, 1, '2018-03-07 21:52:22', '2018-03-07 21:52:55'),
(212, 'কোম্পানি / ক্লায়েন্ট বর্ণনা', 106, 'bn', 0, 1, '2018-03-07 21:52:22', '2018-03-07 21:52:55'),
(213, 'Employee Name', 107, 'en', 0, 1, '2018-03-07 23:00:58', '2018-03-07 23:02:22'),
(214, 'কর্মকর্তার নাম', 107, 'bn', 0, 1, '2018-03-07 23:00:58', '2018-03-07 23:02:22'),
(215, 'Personal Phone Number', 108, 'en', 0, 1, '2018-03-07 23:02:33', '2018-03-07 23:03:02'),
(216, 'ব্যক্তিগত ফোন নম্বর', 108, 'bn', 0, 1, '2018-03-07 23:02:33', '2018-03-07 23:03:02'),
(217, 'Employee Address', 109, 'en', 0, 1, '2018-03-07 23:03:16', '2018-03-07 23:03:38'),
(218, 'কর্মচারী ঠিকানা', 109, 'bn', 0, 1, '2018-03-07 23:03:16', '2018-03-07 23:03:38'),
(219, 'Password Confirmation', 110, 'en', 0, 1, '2018-03-07 23:03:52', '2018-03-07 23:04:14'),
(220, 'পাসওয়ার্ড নিশ্চিতকরণ', 110, 'bn', 0, 1, '2018-03-07 23:03:52', '2018-03-07 23:04:14'),
(221, 'Search', 111, 'en', 0, 1, '2018-03-07 23:11:42', '2018-03-07 23:11:59'),
(222, 'অনুসন্ধান', 111, 'bn', 0, 1, '2018-03-07 23:11:43', '2018-03-07 23:11:59'),
(223, 'Company', 112, 'en', 0, 1, '2018-03-07 23:21:05', '2018-03-07 23:21:36'),
(224, 'কোম্পানি', 112, 'bn', 0, 1, '2018-03-07 23:21:05', '2018-03-07 23:21:36'),
(225, 'Add Client/Company', 113, 'en', 0, 1, '2018-03-07 23:52:58', '2018-03-07 23:53:35'),
(226, 'ক্লায়েন্ট / কোম্পানি যোগ করুন', 113, 'bn', 0, 1, '2018-03-07 23:52:58', '2018-03-07 23:53:35'),
(227, 'Update Company/Client', 114, 'en', 0, 1, '2018-03-08 17:19:08', '2018-03-08 17:27:08'),
(228, 'আপডেট কোম্পানী / ক্লায়েন্ট', 114, 'bn', 0, 1, '2018-03-08 17:19:08', '2018-03-08 17:27:08'),
(229, 'Add Packet', 115, 'en', 0, 1, '2018-03-09 17:02:11', '2018-03-09 17:02:56'),
(230, 'প্যাকেট যোগ করুন', 115, 'bn', 0, 1, '2018-03-09 17:02:11', '2018-03-09 17:02:56'),
(231, 'Select Unit', 116, 'en', 0, 1, '2018-03-09 17:04:20', '2018-03-09 17:04:45'),
(232, 'ইউনিট নির্বাচন করুন', 116, 'bn', 0, 1, '2018-03-09 17:04:20', '2018-03-09 17:04:45'),
(233, 'Packet Name', 117, 'en', 0, 1, '2018-03-09 17:06:17', '2018-03-09 17:06:34'),
(234, 'প্যাকেট নাম', 117, 'bn', 0, 1, '2018-03-09 17:06:17', '2018-03-09 17:06:34'),
(235, 'Unit Quantity', 118, 'en', 0, 1, '2018-03-09 17:07:27', '2018-03-09 17:07:48'),
(236, 'একক পরিমাণ', 118, 'bn', 0, 1, '2018-03-09 17:07:27', '2018-03-09 17:07:48'),
(237, 'Update Packet', 119, 'en', 0, 1, '2018-03-09 17:13:42', '2018-03-09 17:14:04'),
(238, 'আপডেট প্যাকেট', 119, 'bn', 0, 1, '2018-03-09 17:13:42', '2018-03-09 17:14:04'),
(239, 'Unit', 120, 'en', 0, 1, '2018-03-09 17:18:32', '2018-03-09 17:18:51'),
(240, 'একক', 120, 'bn', 0, 1, '2018-03-09 17:18:32', '2018-03-09 17:18:51'),
(241, 'Packet List', 121, 'en', 0, 1, '2018-03-09 17:24:19', '2018-03-09 17:24:43'),
(242, 'প্যাকেট তালিকা', 121, 'bn', 0, 1, '2018-03-09 17:24:19', '2018-03-09 17:24:43'),
(243, 'Add new Product', 122, 'en', 0, 1, '2018-03-09 17:52:50', '2018-03-09 17:53:11'),
(244, 'নতুন পণ্য যোগ করুন', 122, 'bn', 0, 1, '2018-03-09 17:52:50', '2018-03-09 17:53:11'),
(247, 'Packet details', 124, 'en', 0, 1, '2018-03-09 17:56:43', '2018-03-09 17:56:59'),
(248, 'প্যাকেট বিস্তারিত', 124, 'bn', 0, 1, '2018-03-09 17:56:43', '2018-03-09 17:56:59'),
(249, 'Item Code', 125, 'en', 0, 1, '2018-03-09 18:02:50', '2018-07-09 01:32:39'),
(250, 'পণ্য কোড', 125, 'bn', 0, 1, '2018-03-09 18:02:50', '2018-07-09 01:32:39'),
(251, 'Update Product', 126, 'en', 0, 1, '2018-03-09 18:09:32', '2018-03-09 18:10:24'),
(252, 'আপডেট পণ্য', 126, 'bn', 0, 1, '2018-03-09 18:09:33', '2018-03-09 18:10:24'),
(253, 'Edit product', 127, 'en', 0, 1, '2018-03-09 18:10:38', '2018-03-09 18:11:58'),
(254, 'পণ্য সংস্করণ', 127, 'bn', 0, 1, '2018-03-09 18:10:38', '2018-03-09 18:11:58'),
(255, 'Product Group Name', 128, 'en', 0, 1, '2018-03-09 18:26:17', '2018-03-09 18:26:37'),
(256, 'পণ্য গ্রুপ নাম', 128, 'bn', 0, 1, '2018-03-09 18:26:17', '2018-03-09 18:26:37'),
(257, 'Add product group', 129, 'en', 0, 1, '2018-03-09 18:26:52', '2018-03-09 18:27:11'),
(258, 'পণ্য গ্রুপ যোগ করুন', 129, 'bn', 0, 1, '2018-03-09 18:26:52', '2018-03-09 18:27:11'),
(259, 'Add new product group', 130, 'en', 0, 1, '2018-03-09 18:27:22', '2018-03-09 18:27:45'),
(260, 'নতুন পণ্য গ্রুপ যোগ করুন', 130, 'bn', 0, 1, '2018-03-09 18:27:22', '2018-03-09 18:27:45'),
(261, 'Update Product Group', 131, 'en', 0, 1, '2018-03-09 18:34:53', '2018-03-09 18:35:12'),
(262, 'আপডেট পণ্য গ্রুপ', 131, 'bn', 0, 1, '2018-03-09 18:34:53', '2018-03-09 18:35:12'),
(263, 'Edit product group', 132, 'en', 0, 1, '2018-03-09 18:35:57', '2018-03-09 18:36:25'),
(264, 'পণ্য গ্রুপ সম্পাদনা করুন', 132, 'bn', 0, 1, '2018-03-09 18:35:57', '2018-03-09 18:36:25'),
(265, 'Product Group List', 133, 'en', 0, 1, '2018-03-09 18:39:48', '2018-03-09 18:40:05'),
(266, 'পণ্য গ্রুপ তালিকা', 133, 'bn', 0, 1, '2018-03-09 18:39:48', '2018-03-09 18:40:05'),
(267, 'Unit name', 134, 'en', 0, 1, '2018-03-09 19:00:04', '2018-03-09 19:00:25'),
(268, 'ইউনিটের নাম', 134, 'bn', 0, 1, '2018-03-09 19:00:04', '2018-03-09 19:00:25'),
(269, 'Add unit', 135, 'en', 0, 1, '2018-03-09 19:00:51', '2018-03-09 19:01:55'),
(270, 'ইউনিট যোগ করুন', 135, 'bn', 0, 1, '2018-03-09 19:00:51', '2018-03-09 19:01:55'),
(271, 'Add new Unit', 136, 'en', 0, 1, '2018-03-09 19:02:17', '2018-03-09 19:02:40'),
(272, 'নতুন ইউনিট যোগ করুন', 136, 'bn', 0, 1, '2018-03-09 19:02:17', '2018-03-09 19:02:40'),
(273, 'Update Unit', 137, 'en', 0, 1, '2018-03-09 19:04:46', '2018-03-09 19:05:07'),
(274, 'আপডেট ইউনিট', 137, 'bn', 0, 1, '2018-03-09 19:04:46', '2018-03-09 19:05:07'),
(275, 'Edit Unit', 138, 'en', 0, 1, '2018-03-09 19:05:18', '2018-03-09 19:05:36'),
(276, 'ইউনিট সম্পাদনা করুন', 138, 'bn', 0, 1, '2018-03-09 19:05:18', '2018-03-09 19:05:37'),
(277, 'Company Name', 139, 'en', 0, 1, '2018-03-09 19:09:56', '2018-06-21 00:33:35'),
(278, 'কোমপানির নাম', 139, 'bn', 0, 1, '2018-03-09 19:09:56', '2018-06-21 00:33:35'),
(279, 'Add Vat Tax', 140, 'en', 0, 1, '2018-03-09 19:11:03', '2018-03-09 19:11:22'),
(280, 'ভ্যাট ট্যাক্স যোগ করুন', 140, 'bn', 0, 1, '2018-03-09 19:11:03', '2018-03-09 19:11:22'),
(281, 'Select Product', 141, 'en', 0, 1, '2018-03-09 19:13:30', '2018-03-09 19:20:25'),
(282, 'পণ্য নির্বাচন করুন', 141, 'bn', 0, 1, '2018-03-09 19:13:30', '2018-03-09 19:20:25'),
(283, 'Report', 142, 'en', 0, 1, '2018-03-09 19:18:16', '2018-03-09 19:18:36'),
(284, 'প্রতিবেদন', 142, 'bn', 0, 1, '2018-03-09 19:18:16', '2018-03-09 19:18:36'),
(285, 'Available Quantity', 143, 'en', 0, 1, '2018-03-09 19:24:36', '2018-03-09 19:25:10'),
(286, 'উপলব্ধ পরিমাণ', 143, 'bn', 0, 1, '2018-03-09 19:24:36', '2018-03-09 19:25:10'),
(287, 'Sale Quantity', 144, 'en', 0, 1, '2018-03-09 19:25:47', '2018-03-09 19:26:05'),
(288, 'বিক্রয় পরিমাণ', 144, 'bn', 0, 1, '2018-03-09 19:25:47', '2018-03-09 19:26:05'),
(289, 'Total Quantity', 145, 'en', 0, 1, '2018-03-09 19:26:25', '2018-03-09 19:26:44'),
(290, 'মোট পরিমাণ', 145, 'bn', 0, 1, '2018-03-09 19:26:25', '2018-03-09 19:26:44'),
(291, 'Select Invoice', 146, 'en', 0, 1, '2018-03-09 19:44:45', '2018-03-09 19:45:42'),
(292, 'চালান নির্বাচন করুন', 146, 'bn', 0, 1, '2018-03-09 19:44:45', '2018-03-09 19:45:42'),
(293, 'Search date....', 147, 'en', 0, 1, '2018-03-09 19:45:57', '2018-03-09 19:46:17'),
(294, 'তারিখ অনুসন্ধান করুন ....', 147, 'bn', 0, 1, '2018-03-09 19:45:57', '2018-03-09 19:46:17'),
(295, 'Date', 148, 'en', 0, 1, '2018-03-09 19:47:32', '2018-03-09 19:47:48'),
(296, 'তারিখ', 148, 'bn', 0, 1, '2018-03-09 19:47:32', '2018-03-09 19:47:48'),
(297, 'Challan No', 149, 'en', 0, 1, '2018-03-09 19:48:38', '2018-03-09 19:49:45'),
(298, 'চালান নং', 149, 'bn', 0, 1, '2018-03-09 19:48:38', '2018-03-09 19:49:45'),
(299, 'Quantity/Kg', 150, 'en', 0, 1, '2018-03-09 19:50:42', '2018-03-09 19:50:58'),
(300, 'পরিমাণ / কেজি', 150, 'bn', 0, 1, '2018-03-09 19:50:42', '2018-03-09 19:50:58'),
(301, 'Unit Price/Kg', 151, 'en', 0, 1, '2018-03-09 19:51:26', '2018-03-09 19:51:44'),
(302, 'ইউনিট মূল্য / কেজি', 151, 'bn', 0, 1, '2018-03-09 19:51:26', '2018-03-09 19:51:45'),
(303, 'Total Up to Date Amount', 152, 'en', 0, 1, '2018-03-09 19:52:14', '2018-03-09 19:54:53'),
(304, 'সর্বমোট পরিমাণ', 152, 'bn', 0, 1, '2018-03-09 19:52:14', '2018-03-09 19:54:53'),
(305, 'User List', 153, 'en', 0, 1, '2018-03-11 17:00:41', '2018-03-11 17:01:04'),
(306, 'ব্যবহারকারীর তালিকা', 153, 'bn', 0, 1, '2018-03-11 17:00:41', '2018-03-11 17:01:04'),
(307, 'Local purchase', 154, 'en', 0, 1, '2018-03-21 01:37:13', '2018-03-21 01:37:34'),
(308, NULL, 154, 'bn', 0, 1, '2018-03-21 01:37:13', '2018-03-21 01:37:34'),
(309, 'LC Purchase', 155, 'en', 0, 1, '2018-03-21 01:54:39', '2018-03-21 01:55:01'),
(310, NULL, 155, 'bn', 0, 1, '2018-03-21 01:54:39', '2018-03-21 01:55:01'),
(311, 'view result', 156, 'en', 0, 1, '2018-04-02 06:48:56', '2018-04-02 06:49:13'),
(312, NULL, 156, 'bn', 0, 1, '2018-04-02 06:48:57', '2018-04-02 06:49:14'),
(313, 'Management', 157, 'en', 0, 1, '2018-04-10 00:01:48', '2018-04-16 06:00:36'),
(314, 'ম্যানেজমেন্ট', 157, 'bn', 0, 1, '2018-04-10 00:01:48', '2018-04-16 06:00:36'),
(315, 'Item Maintenance List', 158, 'en', 0, 1, '2018-04-10 00:38:18', '2018-09-14 22:21:40'),
(316, 'পণ্য তালিকা', 158, 'bn', 0, 1, '2018-04-10 00:38:18', '2018-09-14 22:21:40'),
(317, 'Item Description', 159, 'en', 0, 1, '2018-04-10 04:32:01', '2018-07-09 01:33:13'),
(318, 'পণ্যের বর্ণনা', 159, 'bn', 0, 1, '2018-04-10 04:32:01', '2018-07-09 01:33:14'),
(319, 'Brand', 160, 'en', 0, 1, '2018-04-10 04:34:38', '2018-05-13 23:26:45'),
(320, 'তরবার', 160, 'bn', 0, 1, '2018-04-10 04:34:38', '2018-05-13 23:26:45'),
(321, 'ERP Code', 161, 'en', 0, 1, '2018-04-10 04:41:38', '2018-05-13 23:27:40'),
(322, 'ইআরপি কোড', 161, 'bn', 0, 1, '2018-04-10 04:41:38', '2018-05-13 23:27:40'),
(323, 'Unit Price', 162, 'en', 0, 1, '2018-04-10 04:43:37', '2018-05-13 23:28:07'),
(324, 'একক দাম', 162, 'bn', 0, 1, '2018-04-10 04:43:37', '2018-05-13 23:28:07'),
(325, 'Unit', 163, 'en', 0, 1, '2018-04-10 04:46:17', '2018-09-15 00:19:35'),
(326, 'ওজন পরিমাণ', 163, 'bn', 0, 1, '2018-04-10 04:46:18', '2018-09-15 00:19:35'),
(327, 'Weight Amt', 164, 'en', 0, 1, '2018-04-10 04:46:54', '2018-05-13 23:29:01'),
(328, 'ওজন পরিমাণ', 164, 'bn', 0, 1, '2018-04-10 04:46:54', '2018-05-13 23:29:01'),
(329, 'Description 1', 165, 'en', 0, 1, '2018-04-10 04:51:05', '2018-04-10 04:52:20'),
(330, 'বিবরণ 1', 165, 'bn', 0, 1, '2018-04-10 04:51:05', '2018-04-10 04:52:20'),
(331, 'Description 2', 166, 'en', 0, 1, '2018-04-10 04:51:29', '2018-04-10 04:55:18'),
(332, 'বিবরণ 2', 166, 'bn', 0, 1, '2018-04-10 04:51:29', '2018-04-10 04:55:18'),
(333, 'Description 3', 167, 'en', 0, 1, '2018-04-10 04:54:30', '2018-04-10 04:55:30'),
(334, 'বিবরণ 3', 167, 'bn', 0, 1, '2018-04-10 04:54:30', '2018-04-10 04:55:30'),
(335, 'Description 4', 168, 'en', 0, 1, '2018-04-10 04:54:44', '2018-04-10 04:56:56'),
(336, 'বিবরণ 4', 168, 'bn', 0, 1, '2018-04-10 04:54:44', '2018-04-10 04:56:56'),
(337, 'Vendor', 169, 'en', 0, 1, '2018-04-12 00:30:29', '2018-07-09 00:02:01'),
(338, 'বিক্রেতা', 169, 'bn', 0, 1, '2018-04-12 00:30:29', '2018-07-09 00:02:01'),
(339, 'Vendor ID', 170, 'en', 0, 1, '2018-04-12 00:34:45', '2018-07-09 00:31:28'),
(340, 'বিক্রেতা সনাক্তকরন সংখ্যা', 170, 'bn', 0, 1, '2018-04-12 00:34:45', '2018-07-09 00:31:28'),
(341, 'Brand Name', 171, 'en', 0, 1, '2018-04-12 00:35:35', '2018-09-16 23:40:29'),
(342, 'ক্রেতা নাম', 171, 'bn', 0, 1, '2018-04-12 00:35:35', '2018-09-16 23:40:29'),
(343, 'Address -1', 172, 'en', 0, 1, '2018-04-12 00:36:08', '2018-05-11 00:48:16'),
(344, 'ঠিকানা 1', 172, 'bn', 0, 1, '2018-04-12 00:36:08', '2018-05-11 00:48:16'),
(345, 'Address -2', 173, 'en', 0, 1, '2018-04-12 00:37:03', '2018-05-11 00:48:41'),
(346, 'ঠিকানা 2', 173, 'bn', 0, 1, '2018-04-12 00:37:03', '2018-05-11 00:48:41'),
(347, 'Attention', 174, 'en', 0, 1, '2018-04-12 00:38:52', '2018-05-11 00:49:08'),
(348, 'মনোযোগ', 174, 'bn', 0, 1, '2018-04-12 00:38:52', '2018-05-11 00:49:08'),
(349, 'Mobile', 175, 'en', 0, 1, '2018-04-12 00:39:26', '2018-05-11 00:49:33'),
(350, 'মোবাইল', 175, 'bn', 0, 1, '2018-04-12 00:39:26', '2018-05-11 00:49:33'),
(351, 'Telephone', 176, 'en', 0, 1, '2018-04-12 00:40:01', '2018-05-11 00:49:58'),
(352, 'টেলিফোন', 176, 'bn', 0, 1, '2018-04-12 00:40:01', '2018-05-11 00:49:58'),
(353, 'Fax', 177, 'en', 0, 1, '2018-04-12 00:40:51', '2018-05-11 00:50:28'),
(354, 'ফ্যাক্স', 177, 'bn', 0, 1, '2018-04-12 00:40:51', '2018-05-11 00:50:28'),
(355, 'Address -1', 178, 'en', 0, 1, '2018-04-12 00:41:25', '2018-05-11 00:51:57'),
(356, 'ঠিকানা 1', 178, 'bn', 0, 1, '2018-04-12 00:41:25', '2018-05-11 00:51:57'),
(357, 'Address -2', 179, 'en', 0, 1, '2018-04-12 00:41:54', '2018-05-11 00:52:16'),
(358, 'ঠিকানা 2', 179, 'bn', 0, 1, '2018-04-12 00:41:54', '2018-05-11 00:52:17'),
(359, 'Attention', 180, 'en', 0, 1, '2018-04-12 00:42:23', '2018-05-11 00:52:39'),
(360, 'মনোযোগ', 180, 'bn', 0, 1, '2018-04-12 00:42:23', '2018-05-11 00:52:39'),
(361, 'Mobile', 181, 'en', 0, 1, '2018-04-12 00:42:51', '2018-05-11 00:53:11'),
(362, 'মোবাইল', 181, 'bn', 0, 1, '2018-04-12 00:42:51', '2018-05-11 00:53:11'),
(363, 'Telephone', 182, 'en', 0, 1, '2018-04-12 00:43:14', '2018-05-11 00:53:34'),
(364, 'টেলিফোন', 182, 'bn', 0, 1, '2018-04-12 00:43:14', '2018-05-11 00:53:35'),
(365, 'Fax', 183, 'en', 0, 1, '2018-04-12 00:43:40', '2018-05-11 00:54:00'),
(366, 'ফ্যাক্স', 183, 'bn', 0, 1, '2018-04-12 00:43:40', '2018-05-11 00:54:00'),
(367, 'Description -1', 184, 'en', 0, 1, '2018-04-12 00:44:02', '2018-04-12 00:44:17'),
(368, NULL, 184, 'bn', 0, 1, '2018-04-12 00:44:02', '2018-04-12 00:44:17'),
(369, 'Description -2', 185, 'en', 0, 1, '2018-04-12 00:44:29', '2018-04-12 00:44:41'),
(370, NULL, 185, 'bn', 0, 1, '2018-04-12 00:44:29', '2018-04-12 00:44:41'),
(371, 'Description -3', 186, 'en', 0, 1, '2018-04-12 00:44:53', '2018-04-12 00:45:07'),
(372, NULL, 186, 'bn', 0, 1, '2018-04-12 00:44:53', '2018-04-12 00:45:07'),
(373, 'Add vendor', 187, 'en', 0, 1, '2018-04-12 01:09:15', '2018-07-09 00:02:54'),
(374, 'বিক্রেতা যোগ করুন', 187, 'bn', 0, 1, '2018-04-12 01:09:16', '2018-07-09 00:02:55'),
(375, 'Page header', 188, 'en', 0, 1, '2018-04-12 04:03:01', '2018-04-16 05:59:47'),
(376, 'পেজের উপরের অংশ', 188, 'bn', 0, 1, '2018-04-12 04:03:01', '2018-04-16 05:59:47'),
(377, 'Header Title', 189, 'en', 0, 1, '2018-04-12 04:16:18', '2018-05-11 02:33:53'),
(378, 'শিরোলেখ শিরোনাম', 189, 'bn', 0, 1, '2018-04-12 04:16:18', '2018-05-11 02:33:53'),
(379, 'Header font size', 190, 'en', 0, 1, '2018-04-12 04:19:01', '2018-04-12 04:19:22'),
(380, NULL, 190, 'bn', 0, 1, '2018-04-12 04:19:01', '2018-04-12 04:19:22'),
(381, 'Font style', 191, 'en', 0, 1, '2018-04-12 04:21:39', '2018-04-16 06:10:57'),
(382, 'ফন্ট শৈলী', 191, 'bn', 0, 1, '2018-04-12 04:21:39', '2018-04-16 06:10:57'),
(383, 'Header color', 192, 'en', 0, 1, '2018-04-12 04:26:04', '2018-04-12 04:26:12'),
(384, NULL, 192, 'bn', 0, 1, '2018-04-12 04:26:04', '2018-04-12 04:26:12'),
(385, 'Address -1', 193, 'en', 0, 1, '2018-04-12 04:26:24', '2018-04-16 06:15:11'),
(386, 'ঠিকানা 1', 193, 'bn', 0, 1, '2018-04-12 04:26:24', '2018-04-16 06:15:11'),
(387, 'Address -3', 194, 'en', 0, 1, '2018-04-12 04:29:35', '2018-05-10 23:27:52'),
(388, 'ঠিকানা 3', 194, 'bn', 0, 1, '2018-04-12 04:29:35', '2018-05-10 23:27:52'),
(389, 'Header logo aligment', 195, 'en', 0, 1, '2018-04-12 04:30:05', '2018-04-12 04:55:00'),
(390, NULL, 195, 'bn', 0, 1, '2018-04-12 04:30:05', '2018-04-12 04:55:00'),
(391, 'Address -2', 196, 'en', 0, 1, '2018-04-12 04:30:39', '2018-05-10 23:27:38'),
(392, 'ঠিকানা 2', 196, 'bn', 0, 1, '2018-04-12 04:30:39', '2018-05-10 23:27:38'),
(393, 'Logo', 197, 'en', 0, 1, '2018-04-12 04:31:04', '2018-04-16 07:57:34'),
(394, 'লোগো', 197, 'bn', 0, 1, '2018-04-12 04:31:04', '2018-04-16 07:57:34'),
(395, 'Page', 198, 'en', 0, 1, '2018-04-12 05:31:26', '2018-04-16 06:01:07'),
(396, 'পৃষ্ঠা', 198, 'bn', 0, 1, '2018-04-12 05:31:26', '2018-04-16 06:01:07'),
(397, 'Page footer', 199, 'en', 0, 1, '2018-04-12 05:38:56', '2018-05-03 03:59:42'),
(398, 'পৃষ্ঠার পাদলেখ', 199, 'bn', 0, 1, '2018-04-12 05:38:56', '2018-05-03 03:59:43'),
(399, 'Add Page Footer Title', 200, 'en', 0, 1, '2018-04-12 06:07:56', '2018-05-11 02:45:21'),
(400, 'পৃষ্ঠার শিরোনাম শিরোনাম যোগ করুন', 200, 'bn', 0, 1, '2018-04-12 06:07:57', '2018-05-11 02:45:21'),
(401, 'Add a title', 201, 'en', 0, 1, '2018-04-12 06:09:53', '2018-04-12 06:10:06'),
(402, NULL, 201, 'bn', 0, 1, '2018-04-12 06:09:53', '2018-04-12 06:10:06'),
(403, 'Enter footer title', 202, 'en', 0, 0, '2018-04-12 06:10:56', '2018-05-10 23:30:10'),
(404, 'পাদচরণ শিরোনাম লিখুন', 202, 'bn', 0, 0, '2018-04-12 06:10:56', '2018-05-10 23:30:10'),
(405, 'Update footer title', 203, 'en', 0, 1, '2018-04-13 02:05:45', '2018-05-11 02:44:39'),
(406, 'পাদলেখ শিরোনাম আপডেট করুন', 203, 'bn', 0, 1, '2018-04-13 02:05:45', '2018-05-11 02:44:39'),
(407, 'Report footer', 204, 'en', 0, 1, '2018-04-13 02:35:41', '2018-05-03 03:57:07'),
(408, 'পাদটীকা প্রতিবেদন করুন', 204, 'bn', 0, 1, '2018-04-13 02:35:42', '2018-05-03 03:57:07'),
(409, 'Add report', 205, 'en', 0, 1, '2018-04-13 04:43:27', '2018-05-10 23:36:24'),
(410, 'রিপোর্ট যোগ করুন', 205, 'bn', 0, 1, '2018-04-13 04:43:27', '2018-05-10 23:36:24'),
(411, 'Report Name', 206, 'en', 0, 1, '2018-04-13 04:44:39', '2018-04-16 00:13:17'),
(412, 'প্রতিবেদন নাম', 206, 'bn', 0, 1, '2018-04-13 04:44:39', '2018-04-16 00:13:17'),
(413, 'Description -3', 207, 'en', 0, 1, '2018-04-13 04:48:23', '2018-04-13 04:48:33'),
(414, NULL, 207, 'bn', 0, 1, '2018-04-13 04:48:23', '2018-04-13 04:48:33'),
(415, 'Description -1', 208, 'en', 0, 1, '2018-04-13 04:49:10', '2018-04-13 04:49:16'),
(416, NULL, 208, 'bn', 0, 1, '2018-04-13 04:49:10', '2018-04-13 04:49:16'),
(417, 'Description -4', 209, 'en', 0, 1, '2018-04-13 04:50:50', '2018-04-13 04:51:01'),
(418, NULL, 209, 'bn', 0, 1, '2018-04-13 04:50:50', '2018-04-13 04:51:01'),
(419, 'Description -2', 210, 'en', 0, 1, '2018-04-13 04:51:12', '2018-04-13 04:51:19'),
(420, NULL, 210, 'bn', 0, 1, '2018-04-13 04:51:12', '2018-04-13 04:51:19'),
(421, 'Description -5', 211, 'en', 0, 1, '2018-04-13 04:51:28', '2018-04-13 04:51:37'),
(422, NULL, 211, 'bn', 0, 1, '2018-04-13 04:51:29', '2018-04-13 04:51:37'),
(423, 'Sigining Person -1', 212, 'en', 0, 1, '2018-04-13 04:56:18', '2018-04-16 00:15:35'),
(424, 'সাইন ইন ব্যক্তি -1', 212, 'bn', 0, 1, '2018-04-13 04:56:18', '2018-04-16 00:15:35'),
(425, 'Sigining Person -2', 213, 'en', 0, 1, '2018-04-13 04:56:26', '2018-04-16 00:15:43'),
(426, 'সাইন ইন ব্যক্তি -2', 213, 'bn', 0, 1, '2018-04-13 04:56:26', '2018-04-16 00:15:43'),
(427, 'Signature', 214, 'en', 0, 1, '2018-04-13 07:03:30', '2018-04-16 00:14:17'),
(428, 'স্বাক্ষর', 214, 'bn', 0, 1, '2018-04-13 07:03:30', '2018-04-16 00:14:17'),
(429, 'Seal', 215, 'en', 0, 1, '2018-04-13 07:04:58', '2018-04-16 00:13:38'),
(430, 'সীল', 215, 'bn', 0, 1, '2018-04-13 07:04:58', '2018-04-16 00:13:39'),
(431, 'Name', 216, 'en', 0, 1, '2018-04-13 07:26:33', '2018-04-13 07:26:52'),
(432, 'নাম', 216, 'bn', 0, 1, '2018-04-13 07:26:33', '2018-04-13 07:26:52'),
(433, 'Brand List', 217, 'en', 0, 1, '2018-04-16 00:43:54', '2018-04-16 00:44:17'),
(434, 'ব্র্যান্ড তালিকা', 217, 'bn', 0, 1, '2018-04-16 00:43:54', '2018-04-16 00:44:17'),
(435, 'Add Brand', 218, 'en', 0, 1, '2018-04-16 01:36:20', '2018-04-16 01:36:40'),
(436, 'ব্র্যান্ড যোগ করুন', 218, 'bn', 0, 1, '2018-04-16 01:36:20', '2018-04-16 01:36:40'),
(437, 'Brand Name', 219, 'en', 0, 1, '2018-04-16 01:42:14', '2018-04-16 01:42:42'),
(438, 'পরিচিতিমুলক নাম', 219, 'bn', 0, 1, '2018-04-16 01:42:14', '2018-04-16 01:42:42'),
(439, 'Product size list', 220, 'en', 0, 1, '2018-04-16 02:25:24', '2018-06-05 22:35:38'),
(440, 'পণ্য আকার তালিকা', 220, 'bn', 0, 1, '2018-04-16 02:25:24', '2018-06-05 22:35:38'),
(441, 'Add Product Size', 221, 'en', 0, 1, '2018-04-16 04:19:18', '2018-04-16 04:19:42'),
(442, 'পণ্য আকার যোগ করুন', 221, 'bn', 0, 1, '2018-04-16 04:19:18', '2018-04-16 04:19:42'),
(443, 'Size', 222, 'en', 0, 1, '2018-04-16 04:21:37', '2018-04-16 04:21:56'),
(444, 'আয়তন', 222, 'bn', 0, 1, '2018-04-16 04:21:37', '2018-04-16 04:21:56'),
(445, 'Add Size', 223, 'en', 0, 1, '2018-04-16 04:23:26', '2018-04-16 04:23:47'),
(446, 'আকার জুড়ুন', 223, 'bn', 0, 1, '2018-04-16 04:23:26', '2018-04-16 04:23:47'),
(447, 'Font Size', 224, 'en', 0, 1, '2018-04-16 06:09:18', '2018-04-16 06:09:53'),
(448, 'অক্ষরের আকার', 224, 'bn', 0, 1, '2018-04-16 06:09:18', '2018-04-16 06:09:54'),
(449, 'Font Color', 225, 'en', 0, 1, '2018-04-16 06:11:24', '2018-04-16 06:12:18'),
(450, 'ফন্টের রং', 225, 'bn', 0, 1, '2018-04-16 06:11:24', '2018-04-16 06:12:19'),
(451, 'Logo Alignment', 226, 'en', 0, 1, '2018-04-16 06:12:38', '2018-04-17 02:22:44'),
(452, 'লোগো সংলগ্ন', 226, 'bn', 0, 1, '2018-04-16 06:12:38', '2018-04-17 02:22:44'),
(453, 'Print file', 227, 'en', 0, 1, '2018-04-17 05:31:18', '2018-04-17 05:31:50'),
(454, 'মুদ্রণ ফাইল', 227, 'bn', 0, 1, '2018-04-17 05:31:18', '2018-04-17 05:31:50'),
(455, 'Order Entry', 228, 'en', 0, 1, '2018-04-17 05:32:54', '2018-05-09 04:24:57'),
(456, 'অর্ডার এন্ট্রি', 228, 'bn', 0, 1, '2018-04-17 05:32:54', '2018-05-09 04:24:57'),
(457, 'Search bill', 229, 'en', 0, 1, '2018-04-25 22:24:36', '2018-05-03 00:39:41'),
(458, 'অনুসন্ধান বিল', 229, 'bn', 0, 1, '2018-04-25 22:24:36', '2018-05-03 00:39:41'),
(459, 'Search bill', 230, 'en', 0, 1, '2018-05-03 00:37:53', '2018-05-03 00:42:01'),
(460, 'অনুসন্ধান বিল', 230, 'bn', 0, 1, '2018-05-03 00:37:53', '2018-05-03 00:42:01'),
(461, 'Invo No', 231, 'en', 0, 1, '2018-05-03 00:43:16', '2018-05-03 00:45:04'),
(462, 'ইনভো সংখ্যা', 231, 'bn', 0, 1, '2018-05-03 00:43:16', '2018-05-03 00:45:04'),
(463, 'Search', 232, 'en', 0, 1, '2018-05-03 02:54:24', '2018-05-03 02:54:42'),
(464, 'অনুসন্ধান', 232, 'bn', 0, 1, '2018-05-03 02:54:24', '2018-05-03 02:54:42'),
(465, 'Genarate', 233, 'en', 0, 1, '2018-05-03 02:59:29', '2018-05-03 03:00:12'),
(466, 'জেনারেট করুন', 233, 'bn', 0, 1, '2018-05-03 02:59:29', '2018-05-03 03:00:12'),
(467, 'New Challan Create', 234, 'en', 0, 1, '2018-05-03 03:48:13', '2018-05-07 00:29:58'),
(468, 'নতুন চালান তৈরি করুন', 234, 'bn', 0, 1, '2018-05-03 03:48:13', '2018-05-07 00:29:58'),
(469, 'Challan Search', 235, 'en', 0, 1, '2018-05-06 23:53:37', '2018-05-06 23:56:13'),
(470, 'চালান অনুসন্ধান', 235, 'bn', 0, 1, '2018-05-06 23:53:37', '2018-05-06 23:56:13'),
(471, 'Challan No', 236, 'en', 0, 1, '2018-05-06 23:58:00', '2018-05-07 00:01:36'),
(472, 'চালান সংখ্যা', 236, 'bn', 0, 1, '2018-05-06 23:58:00', '2018-05-07 00:01:36'),
(473, 'Order List', 237, 'en', 0, 1, '2018-05-07 00:53:50', '2018-05-07 00:54:20'),
(474, 'অর্ডার তালিকা', 237, 'bn', 0, 1, '2018-05-07 00:53:50', '2018-05-07 00:54:20'),
(475, 'Order List', 238, 'en', 0, 1, '2018-05-07 01:00:57', '2018-05-07 01:01:34'),
(476, 'অর্ডার তালিকা', 238, 'bn', 0, 1, '2018-05-07 01:00:57', '2018-05-07 01:01:34'),
(477, 'Create IPO', 239, 'en', 0, 1, '2018-05-07 01:58:13', '2018-05-07 01:59:32'),
(478, 'আইপিও তৈরি করুন', 239, 'bn', 0, 1, '2018-05-07 01:58:13', '2018-05-07 01:59:32'),
(479, 'Initial Increase', 240, 'en', 0, 1, '2018-05-07 02:01:49', '2018-05-07 02:02:28'),
(480, 'প্রাথমিক বৃদ্ধি', 240, 'bn', 0, 1, '2018-05-07 02:01:49', '2018-05-07 02:02:28'),
(481, 'Update Header', 241, 'en', 0, 1, '2018-05-10 03:00:27', '2018-05-10 03:01:24'),
(482, 'হেডার আপডেট করুন', 241, 'bn', 0, 1, '2018-05-10 03:00:27', '2018-05-10 03:01:25'),
(483, 'Report footer list', 242, 'en', 0, 1, '2018-05-10 23:31:45', '2018-05-10 23:32:22'),
(484, 'পাদলেখ তালিকাটি প্রতিবেদন করুন', 242, 'bn', 0, 1, '2018-05-10 23:31:46', '2018-05-10 23:32:22'),
(485, 'Update report', 243, 'en', 0, 1, '2018-05-10 23:37:20', '2018-05-10 23:37:48'),
(486, 'আপডেট রিপোর্ট', 243, 'bn', 0, 1, '2018-05-10 23:37:20', '2018-05-10 23:37:48'),
(487, 'Update brand', 244, 'en', 0, 1, '2018-05-11 00:34:47', '2018-05-11 00:36:44'),
(488, 'ব্র্যান্ড আপডেট করুন', 244, 'bn', 0, 1, '2018-05-11 00:34:47', '2018-05-11 00:36:45'),
(489, 'Brand List', 245, 'en', 0, 1, '2018-05-11 00:37:57', '2018-05-11 00:38:16'),
(490, 'ব্র্যান্ড তালিকা', 245, 'bn', 0, 1, '2018-05-11 00:37:57', '2018-05-11 00:38:16'),
(491, 'Vendor List', 246, 'en', 0, 1, '2018-05-11 00:41:44', '2018-07-09 00:03:52'),
(492, 'বিক্রেতা তালিকা', 246, 'bn', 0, 1, '2018-05-11 00:41:44', '2018-07-09 00:03:52'),
(493, 'Status', 247, 'en', 0, 1, '2018-05-11 00:45:50', '2018-05-11 00:46:05'),
(494, 'অবস্থা', 247, 'bn', 0, 1, '2018-05-11 00:45:50', '2018-05-11 00:46:05'),
(495, 'Invoice', 248, 'en', 0, 1, '2018-05-11 00:46:18', '2018-05-11 00:46:37'),
(496, 'চালান', 248, 'bn', 0, 1, '2018-05-11 00:46:18', '2018-05-11 00:46:37'),
(497, 'Shipment', 249, 'en', 0, 1, '2018-05-11 00:46:44', '2018-07-09 00:15:26'),
(498, 'Shipment', 249, 'bn', 0, 1, '2018-05-11 00:46:45', '2018-07-09 00:15:27'),
(499, 'Company Sort name', 250, 'en', 0, 1, '2018-05-11 00:55:04', '2018-06-21 00:34:12'),
(500, 'কোম্পানি সাজানোর নাম', 250, 'bn', 0, 1, '2018-05-11 00:55:05', '2018-06-21 00:34:12'),
(501, 'Header list', 251, 'en', 0, 1, '2018-05-11 02:29:51', '2018-05-11 02:30:16'),
(502, 'শিরোনাম তালিকা', 251, 'bn', 0, 1, '2018-05-11 02:29:51', '2018-05-11 02:30:16'),
(503, 'Add header', 252, 'en', 0, 1, '2018-05-11 02:31:28', '2018-05-11 02:31:43'),
(504, 'শিরোলেখ যোগ করুন', 252, 'bn', 0, 1, '2018-05-11 02:31:28', '2018-05-11 02:31:43'),
(505, 'Address', 253, 'en', 0, 1, '2018-05-11 02:40:55', '2018-05-11 02:41:13'),
(506, 'ঠিকানা', 253, 'bn', 0, 1, '2018-05-11 02:40:55', '2018-05-11 02:41:13'),
(507, 'Footer Title', 254, 'en', 0, 1, '2018-05-11 02:43:38', '2018-05-11 02:43:55'),
(508, 'পাদটীকা শিরোনাম', 254, 'bn', 0, 1, '2018-05-11 02:43:38', '2018-05-11 02:43:55'),
(509, 'Update party', 255, 'en', 0, 1, '2018-05-11 02:47:49', '2018-05-11 02:48:07'),
(510, 'পার্টি আপডেট করুন', 255, 'bn', 0, 1, '2018-05-11 02:47:49', '2018-05-11 02:48:07'),
(511, 'Item Maintenance List', 256, 'en', 0, 1, '2018-05-11 02:49:33', '2018-09-15 00:26:08'),
(512, 'পণ্য তালিকা', 256, 'bn', 0, 1, '2018-05-11 02:49:33', '2018-09-15 00:26:09'),
(513, 'Product size list', 257, 'en', 0, 1, '2018-05-13 23:30:27', '2018-05-13 23:30:45'),
(514, 'পণ্য আকার তালিকা', 257, 'bn', 0, 1, '2018-05-13 23:30:27', '2018-05-13 23:30:45'),
(515, 'Order Input', 258, 'en', 0, 1, '2018-05-16 00:09:31', '2018-05-16 00:10:04'),
(516, 'অর্ডার ইনপুট', 258, 'bn', 0, 1, '2018-05-16 00:09:31', '2018-05-16 00:10:04'),
(517, 'Task', 259, 'en', 0, 1, '2018-06-05 22:36:08', '2018-06-05 22:36:37'),
(518, 'কার্য', 259, 'bn', 0, 1, '2018-06-05 22:36:08', '2018-06-05 22:36:37'),
(519, 'GMTS color', 260, 'en', 0, 1, '2018-06-05 23:27:03', '2018-06-05 23:27:44'),
(520, 'জিএমএসএস রং', 260, 'bn', 0, 1, '2018-06-05 23:27:03', '2018-06-05 23:27:44'),
(521, 'GMTS Color List', 261, 'en', 0, 1, '2018-06-05 23:34:38', '2018-06-05 23:35:46'),
(522, 'জিএমএসএস রঙ তালিকা', 261, 'bn', 0, 1, '2018-06-05 23:34:38', '2018-06-05 23:35:46'),
(523, 'Add Color', 262, 'en', 0, 1, '2018-06-05 23:42:36', '2018-06-05 23:42:54'),
(524, 'রঙ যোগ করুন', 262, 'bn', 0, 1, '2018-06-05 23:42:36', '2018-06-05 23:42:54'),
(525, 'Add GMTS Color', 263, 'en', 0, 1, '2018-06-05 23:54:08', '2018-06-05 23:54:37'),
(526, 'GMTS রঙ যোগ করুন', 263, 'bn', 0, 1, '2018-06-05 23:54:08', '2018-06-05 23:54:37'),
(527, 'Color', 264, 'en', 0, 1, '2018-06-06 01:57:57', '2018-06-06 01:58:08'),
(528, 'Color', 264, 'bn', 0, 1, '2018-06-06 01:57:57', '2018-06-06 01:58:08'),
(529, 'Update GMTS color', 265, 'en', 0, 1, '2018-06-06 02:33:37', '2018-06-06 02:34:02'),
(530, 'আপডেট GMTS রঙ', 265, 'bn', 0, 1, '2018-06-06 02:33:37', '2018-06-06 02:34:02'),
(531, 'Update color', 266, 'en', 0, 1, '2018-06-06 02:34:28', '2018-06-06 02:34:49'),
(532, 'রঙ আপডেট করুন', 266, 'bn', 0, 1, '2018-06-06 02:34:28', '2018-06-06 02:34:49'),
(533, 'Header Type', 267, 'en', 0, 1, '2018-06-08 02:58:26', '2018-06-08 02:58:49'),
(534, 'শিরোলেখ প্রকার', 267, 'bn', 0, 1, '2018-06-08 02:58:26', '2018-06-08 02:58:49'),
(535, 'Cell Number', 268, 'en', 0, 1, '2018-06-08 03:06:08', '2018-06-08 03:06:29'),
(536, 'সেল নম্বর', 268, 'bn', 0, 1, '2018-06-08 03:06:08', '2018-06-08 03:06:29'),
(537, 'Attention', 269, 'en', 0, 1, '2018-06-08 03:06:38', '2018-06-08 03:07:02'),
(538, 'মনোযোগ', 269, 'bn', 0, 1, '2018-06-08 03:06:38', '2018-06-08 03:07:02'),
(539, 'Production', 270, 'en', 0, 1, '2018-06-20 06:29:05', '2018-06-20 06:29:17'),
(540, 'Production', 270, 'bn', 0, 1, '2018-06-20 06:29:05', '2018-06-20 06:29:17'),
(541, 'Booking List', 271, 'en', 0, 1, '2018-06-20 06:29:40', '2018-06-20 06:29:51'),
(542, 'Booking List', 271, 'bn', 0, 1, '2018-06-20 06:29:40', '2018-06-20 06:29:51'),
(543, 'Booking List', 272, 'en', 0, 1, '2018-06-21 04:07:20', '2018-06-21 04:07:35'),
(544, 'Booking List', 272, 'bn', 0, 1, '2018-06-21 04:07:20', '2018-06-21 04:07:35'),
(545, 'Add Item Maintenance', 273, 'en', 0, 1, '2018-07-09 01:31:09', '2018-09-14 22:19:02'),
(546, 'আইটেম যোগ করুন', 273, 'bn', 0, 1, '2018-07-09 01:31:09', '2018-09-14 22:19:02'),
(547, 'Update Item', 274, 'en', 0, 1, '2018-07-09 01:40:20', '2018-07-09 01:42:12'),
(548, 'আইটেম আপডেট করুন', 274, 'bn', 0, 1, '2018-07-09 01:40:20', '2018-07-09 01:42:12'),
(549, 'Others Color', 275, 'en', 0, 1, '2018-07-10 01:18:23', '2018-07-10 01:18:46'),
(550, 'অন্যান্য রঙ', 275, 'bn', 0, 1, '2018-07-10 01:18:23', '2018-07-10 01:18:46'),
(551, 'New MRF Create', 276, 'en', 0, 1, '2018-07-10 02:31:49', '2018-07-10 02:32:36'),
(552, 'নতুন এমআরএফ তৈরি করুন', 276, 'bn', 0, 1, '2018-07-10 02:31:49', '2018-07-10 02:32:36'),
(553, 'Create', 277, 'en', 0, 1, '2018-07-10 02:46:42', '2018-07-10 02:46:56'),
(554, 'সৃষ্টি', 277, 'bn', 0, 1, '2018-07-10 02:46:42', '2018-07-10 02:46:56'),
(555, 'MRF List', 278, 'en', 0, 1, '2018-07-10 06:40:06', '2018-07-10 06:40:28'),
(556, 'এমআরএফ তালিকা', 278, 'bn', 0, 1, '2018-07-10 06:40:06', '2018-07-10 06:40:28'),
(557, 'Challan List', 279, 'en', 0, 1, '2018-07-13 05:04:03', '2018-07-13 05:04:52'),
(558, 'Challan List', 279, 'bn', 0, 1, '2018-07-13 05:04:03', '2018-07-13 05:04:52'),
(559, 'Generate Purchase Order', 280, 'en', 0, 1, '2018-07-28 01:46:36', '2018-07-28 01:47:12'),
(560, 'ক্রয় অর্ডার তৈরি করুন', 280, 'bn', 0, 1, '2018-07-28 01:46:36', '2018-07-28 01:47:12'),
(561, 'Purchase Order List', 281, 'en', 0, 1, '2018-07-28 03:30:47', '2018-07-28 03:31:20'),
(562, 'অর্ডার তালিকা ক্রয়', 281, 'bn', 0, 1, '2018-07-28 03:30:47', '2018-07-28 03:31:20'),
(563, 'Product Type', 282, 'en', 0, 1, '2018-09-05 12:01:06', '2018-09-05 12:01:21'),
(564, 'Product Type', 282, 'bn', 0, 1, '2018-09-05 12:01:06', '2018-09-05 12:01:21'),
(565, 'Suppliers', 283, 'en', 0, 1, '2018-09-05 12:02:01', '2018-09-05 12:02:12'),
(566, 'Suppliers', 283, 'bn', 0, 1, '2018-09-05 12:02:01', '2018-09-05 12:02:13'),
(567, 'Product Status', 284, 'en', 0, 1, '2018-09-05 12:02:36', '2018-09-05 12:02:52'),
(568, 'Product Status', 284, 'bn', 0, 1, '2018-09-05 12:02:36', '2018-09-05 12:02:52'),
(569, 'Increase Percentage', 285, 'en', 0, 1, '2018-09-05 12:06:27', '2018-09-05 12:06:41'),
(570, 'Increase Percentage', 285, 'bn', 0, 1, '2018-09-05 12:06:27', '2018-09-05 12:06:41'),
(571, 'Booking Details', 286, 'en', 0, 1, '2018-09-05 12:09:24', '2018-09-05 12:09:37'),
(572, 'Booking Details', 286, 'bn', 0, 1, '2018-09-05 12:09:24', '2018-09-05 12:09:37'),
(573, 'Task Assign', 287, 'en', 0, 1, '2018-09-13 04:22:29', '2018-09-13 04:26:23'),
(574, 'টাস্ক অ্যাসাইন', 287, 'bn', 0, 1, '2018-09-13 04:22:29', '2018-09-13 04:26:23'),
(575, 'Vendor', 288, 'en', 0, 1, '2018-09-13 04:28:16', '2018-09-15 00:11:11'),
(576, 'বিক্রেতা', 288, 'bn', 0, 1, '2018-09-13 04:28:16', '2018-09-15 00:11:11'),
(577, '供应商', 288, 'cn', 0, 1, '2018-09-15 00:09:07', '2018-09-15 00:11:11'),
(578, NULL, 163, 'cn', 0, 1, '2018-09-15 00:19:28', '2018-09-15 00:19:35'),
(579, NULL, 256, 'cn', 0, 1, '2018-09-15 00:25:14', '2018-09-15 00:26:09'),
(580, NULL, 171, 'cn', 0, 1, '2018-09-16 23:40:22', '2018-09-16 23:40:29'),
(581, 'Tracking List Report', 289, 'en', 0, 1, '2018-09-17 03:49:48', '2018-09-17 03:50:19'),
(582, 'Tracking List Report', 289, 'bn', 0, 1, '2018-09-17 03:49:48', '2018-09-17 03:50:19'),
(583, 'Tracking List Report', 289, 'cn', 0, 1, '2018-09-17 03:49:48', '2018-09-17 03:50:19'),
(584, 'PI List', 290, 'en', 0, 1, '2018-09-17 03:51:06', '2018-09-17 03:51:22'),
(585, 'PI List', 290, 'bn', 0, 1, '2018-09-17 03:51:06', '2018-09-17 03:51:22'),
(586, 'PI List', 290, 'cn', 0, 1, '2018-09-17 03:51:06', '2018-09-17 03:51:22'),
(587, 'Ipo List', 291, 'en', 0, 1, '2018-09-18 03:23:41', '2018-09-18 03:23:56'),
(588, 'Ipo List', 291, 'bn', 0, 1, '2018-09-18 03:23:41', '2018-09-18 03:23:56'),
(589, 'Ipo List', 291, 'cn', 0, 1, '2018-09-18 03:23:41', '2018-09-18 03:23:56'),
(590, 'Item Maintenance List', 292, 'en', 0, 1, '2018-09-24 23:58:57', '2018-09-24 23:59:43'),
(591, 'Item Maintenance List', 292, 'bn', 0, 1, '2018-09-24 23:58:57', '2018-09-24 23:59:43'),
(592, 'Item Maintenance List', 292, 'cn', 0, 1, '2018-09-24 23:58:57', '2018-09-24 23:59:43'),
(593, 'Item Size', 293, 'en', 0, 1, '2018-09-25 00:00:12', '2018-09-25 00:00:36'),
(594, 'Item Size', 293, 'bn', 0, 1, '2018-09-25 00:00:12', '2018-09-25 00:00:36'),
(595, 'Item Size', 293, 'cn', 0, 1, '2018-09-25 00:00:12', '2018-09-25 00:00:36'),
(596, 'Booking List', 294, 'en', 0, 1, '2018-09-25 00:20:54', '2018-09-25 00:21:27'),
(597, 'Booking List', 294, 'bn', 0, 1, '2018-09-25 00:20:54', '2018-09-25 00:21:27'),
(598, 'Booking List', 294, 'cn', 0, 1, '2018-09-25 00:20:54', '2018-09-25 00:21:27'),
(599, 'Item Description', 295, 'en', 0, 1, '2018-09-25 03:02:59', '2018-09-25 03:03:30'),
(600, 'Item Description', 295, 'bn', 0, 1, '2018-09-25 03:02:59', '2018-09-25 03:03:31');
INSERT INTO `mxp_translations` (`translation_id`, `translation`, `translation_key_id`, `lan_code`, `same_trans_key_id`, `is_active`, `created_at`, `updated_at`) VALUES
(601, 'Item Description', 295, 'cn', 0, 1, '2018-09-25 03:02:59', '2018-09-25 03:03:31'),
(602, 'Add Description', 296, 'en', 0, 1, '2018-09-25 03:04:16', '2018-09-25 03:04:34'),
(603, 'Add Description', 296, 'bn', 0, 1, '2018-09-25 03:04:16', '2018-09-25 03:04:34'),
(604, 'Add Description', 296, 'cn', 0, 1, '2018-09-25 03:04:16', '2018-09-25 03:04:34'),
(605, 'Item Description List', 297, 'en', 0, 1, '2018-09-25 03:05:29', '2018-09-25 03:05:53'),
(606, 'Item Description List', 297, 'bn', 0, 1, '2018-09-25 03:05:30', '2018-09-25 03:05:53'),
(607, 'Item Description List', 297, 'cn', 0, 1, '2018-09-25 03:05:30', '2018-09-25 03:05:53'),
(608, 'Description', 298, 'en', 0, 1, '2018-09-25 03:07:10', '2018-09-25 03:07:25'),
(609, 'Description', 298, 'bn', 0, 1, '2018-09-25 03:07:10', '2018-09-25 03:07:25'),
(610, 'Description', 298, 'cn', 0, 1, '2018-09-25 03:07:10', '2018-09-25 03:07:25'),
(611, 'Update Description', 299, 'en', 0, 1, '2018-09-25 03:07:50', '2018-09-25 03:08:04'),
(612, 'Update Description', 299, 'bn', 0, 1, '2018-09-25 03:07:50', '2018-09-25 03:08:04'),
(613, 'Update Description', 299, 'cn', 0, 1, '2018-09-25 03:07:50', '2018-09-25 03:08:04'),
(614, 'Brand List', 300, 'en', 0, 1, '2018-09-25 03:16:27', '2018-09-25 03:18:47'),
(615, 'Brand List', 300, 'bn', 0, 1, '2018-09-25 03:16:27', '2018-09-25 03:18:47'),
(616, 'Brand List', 300, 'cn', 0, 1, '2018-09-25 03:16:27', '2018-09-25 03:18:47'),
(617, 'Brand List', 301, 'en', 0, 1, '2018-09-25 03:20:29', '2018-09-25 03:20:46'),
(618, 'Brand List', 301, 'bn', 0, 1, '2018-09-25 03:20:29', '2018-09-25 03:20:46'),
(619, 'Brand List', 301, 'cn', 0, 1, '2018-09-25 03:20:30', '2018-09-25 03:20:46'),
(620, 'Add Brand', 302, 'en', 0, 1, '2018-09-25 03:21:07', '2018-09-25 03:21:24'),
(621, 'Add Brand', 302, 'bn', 0, 1, '2018-09-25 03:21:07', '2018-09-25 03:21:24'),
(622, 'Add Brand', 302, 'cn', 0, 1, '2018-09-25 03:21:07', '2018-09-25 03:21:24'),
(623, 'Brand Name', 303, 'en', 0, 1, '2018-09-25 03:21:52', '2018-09-25 03:22:07'),
(624, 'Brand Name', 303, 'bn', 0, 1, '2018-09-25 03:21:52', '2018-09-25 03:22:07'),
(625, 'Brand Name', 303, 'cn', 0, 1, '2018-09-25 03:21:52', '2018-09-25 03:22:07'),
(626, 'Update buyer', 304, 'en', 0, 1, '2018-09-25 03:22:37', '2018-09-25 03:22:49'),
(627, 'Update buyer', 304, 'bn', 0, 1, '2018-09-25 03:22:37', '2018-09-25 03:22:49'),
(628, 'Update buyer', 304, 'cn', 0, 1, '2018-09-25 03:22:37', '2018-09-25 03:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_translation_keys`
--

CREATE TABLE `mxp_translation_keys` (
  `translation_key_id` int(10) UNSIGNED NOT NULL,
  `is_active` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `translation_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_translation_keys`
--

INSERT INTO `mxp_translation_keys` (`translation_key_id`, `is_active`, `created_at`, `updated_at`, `translation_key`) VALUES
(1, 1, '2018-03-05 18:12:49', '2018-03-05 18:12:49', 'company_name'),
(2, 1, '2018-03-05 20:38:51', '2018-03-05 20:38:51', 'login_label'),
(3, 1, '2018-03-05 20:39:27', '2018-03-05 20:39:27', 'register_label'),
(4, 1, '2018-03-05 20:54:56', '2018-03-05 20:54:56', 'validationerror_woops'),
(5, 1, '2018-03-05 20:56:52', '2018-03-05 20:56:52', 'validationerror_there_were_some_problems_with_your_input'),
(6, 1, '2018-03-05 20:57:04', '2018-03-05 20:57:04', 'validationerror_or_you_are_not_active_yet'),
(7, 1, '2018-03-05 20:57:14', '2018-03-05 20:57:14', 'enter_email_address'),
(8, 1, '2018-03-05 20:57:22', '2018-03-05 20:57:22', 'enter_password'),
(9, 1, '2018-03-05 20:57:31', '2018-03-05 20:57:31', 'login_rememberme_label'),
(10, 1, '2018-03-05 20:57:39', '2018-03-05 20:57:39', 'forgot_your_password'),
(11, 1, '2018-03-05 23:23:50', '2018-03-05 23:23:50', 'dashboard_label'),
(12, 1, '2018-03-05 23:34:35', '2018-03-05 23:34:35', 'language_list_label'),
(13, 1, '2018-03-05 23:36:43', '2018-03-05 23:36:43', 'serial_no_label'),
(14, 1, '2018-03-05 23:38:13', '2018-03-05 23:38:13', 'language_title_label'),
(15, 1, '2018-03-05 23:38:47', '2018-03-05 23:38:47', 'language_code_label'),
(16, 1, '2018-03-05 23:39:23', '2018-03-05 23:39:23', 'status_label'),
(17, 1, '2018-03-05 23:40:40', '2018-03-05 23:40:40', 'action_label'),
(18, 1, '2018-03-05 23:43:00', '2018-03-05 23:43:00', 'action_active_label'),
(19, 1, '2018-03-05 23:43:47', '2018-03-05 23:43:47', 'action_inactive_label'),
(20, 1, '2018-03-05 23:58:03', '2018-03-05 23:58:03', 'add_locale_button'),
(21, 1, '2018-03-06 00:00:03', '2018-03-06 00:00:03', 'edit_button'),
(22, 1, '2018-03-06 00:14:26', '2018-03-06 00:14:26', 'add_new_language_label'),
(23, 1, '2018-03-06 00:15:45', '2018-03-06 00:15:45', 'add_language_label'),
(24, 1, '2018-03-06 00:16:49', '2018-03-06 00:16:49', 'enter_language_title'),
(25, 1, '2018-03-06 00:17:31', '2018-03-06 00:17:31', 'enter_language_code'),
(26, 1, '2018-03-06 00:18:57', '2018-03-06 00:18:57', 'save_button'),
(27, 1, '2018-03-06 00:23:12', '2018-03-06 00:23:12', 'update_locale_label'),
(28, 1, '2018-03-06 00:28:35', '2018-03-06 00:28:35', 'update_language_title'),
(29, 1, '2018-03-06 00:29:32', '2018-03-06 00:29:32', 'update_language_code'),
(30, 1, '2018-03-06 00:30:07', '2018-03-06 00:30:07', 'update_button'),
(31, 1, '2018-03-06 00:32:05', '2018-03-06 00:32:05', 'update_language_label'),
(32, 1, '2018-03-06 00:34:41', '2018-03-06 00:34:41', 'mxp_upload_file_rechecking_label'),
(33, 1, '2018-03-06 00:36:42', '2018-03-06 00:36:42', 'upload_button'),
(34, 1, '2018-03-06 00:39:26', '2018-03-06 00:39:26', 'translation_list_label'),
(35, 1, '2018-03-06 00:49:29', '2018-03-06 00:49:29', 'add_new_key_label'),
(36, 1, '2018-03-06 00:51:16', '2018-03-06 00:51:16', 'search_the_translation_key_placeholder'),
(37, 1, '2018-03-06 00:52:45', '2018-03-06 00:52:45', 'translation_key_label'),
(38, 1, '2018-03-06 00:54:31', '2018-03-06 00:54:31', 'translation_label'),
(39, 1, '2018-03-06 00:55:21', '2018-03-06 00:55:21', 'language_label'),
(40, 1, '2018-03-06 00:56:29', '2018-03-06 00:56:29', 'delete_button'),
(41, 1, '2018-03-06 01:07:29', '2018-03-06 01:07:29', 'add_new_translation_key_label'),
(42, 1, '2018-03-06 01:08:20', '2018-03-06 01:08:20', 'enter_translation_key'),
(43, 1, '2018-03-06 01:18:54', '2018-03-06 01:18:54', 'update_translation_label'),
(44, 1, '2018-03-06 01:19:50', '2018-03-06 01:19:50', 'update_translation_key_label'),
(45, 1, '2018-03-06 19:21:58', '2018-03-06 19:21:58', 'mxp_menu_language'),
(46, 1, '2018-03-06 19:23:15', '2018-03-06 19:23:15', 'mxp_menu_manage_langulage'),
(47, 1, '2018-03-06 19:24:37', '2018-03-06 19:24:37', 'mxp_menu_manage_translation'),
(48, 1, '2018-03-06 19:25:41', '2018-03-06 19:25:41', 'mxp_menu_upload_language_file'),
(49, 1, '2018-03-06 19:26:59', '2018-03-06 19:26:59', 'mxp_menu_role'),
(50, 1, '2018-03-06 19:28:03', '2018-03-06 19:28:03', 'mxp_menu_add_new_role'),
(51, 1, '2018-03-06 19:30:11', '2018-03-06 19:30:11', 'mxp_menu_role_list'),
(52, 1, '2018-03-06 19:30:45', '2018-03-06 19:30:45', 'mxp_menu_role_permission_'),
(53, 1, '2018-03-06 19:31:22', '2018-03-06 19:31:22', 'mxp_menu_settings'),
(54, 1, '2018-03-06 19:32:15', '2018-03-06 19:32:15', 'mxp_menu_open_company_acc'),
(55, 1, '2018-03-06 19:34:19', '2018-03-06 19:34:19', 'mxp_menu_company_list'),
(56, 1, '2018-03-06 19:34:56', '2018-03-06 19:34:56', 'mxp_menu_open_company_account'),
(57, 1, '2018-03-06 19:36:15', '2018-03-06 19:36:15', 'mxp_menu_create_user'),
(58, 1, '2018-03-06 19:39:56', '2018-03-06 19:39:56', 'mxp_menu_user_list'),
(59, 1, '2018-03-06 19:40:33', '2018-03-06 19:40:33', 'mxp_menu_client_list'),
(60, 1, '2018-03-06 19:41:56', '2018-03-06 19:41:56', 'mxp_menu_product'),
(61, 1, '2018-03-06 19:42:32', '2018-03-06 19:42:32', 'mxp_menu_unit'),
(62, 1, '2018-03-06 19:48:24', '2018-03-06 19:48:24', 'mxp_menu_product_group'),
(63, 1, '2018-03-06 19:49:03', '2018-03-06 19:49:03', 'mxp_menu_product_entry'),
(64, 1, '2018-03-06 19:50:09', '2018-03-06 19:50:09', 'mxp_menu_product_packing'),
(65, 1, '2018-03-06 19:50:54', '2018-03-06 19:50:54', 'mxp_menu_purchase'),
(66, 1, '2018-03-06 19:51:47', '2018-03-06 19:51:47', 'mxp_menu_purchase_list'),
(67, 1, '2018-03-06 19:52:27', '2018-03-06 19:52:27', 'mxp_menu_update_stocks_action'),
(68, 1, '2018-03-06 19:53:48', '2018-03-06 19:53:48', 'mxp_menu_vat_tax_list'),
(69, 1, '2018-03-06 19:54:25', '2018-03-06 19:54:25', 'mxp_menu_sale_list'),
(70, 1, '2018-03-06 19:55:15', '2018-03-06 19:55:15', 'mxp_menu_save_sale_'),
(71, 1, '2018-03-06 19:56:45', '2018-03-06 19:56:45', 'mxp_menu_inventory_report'),
(72, 1, '2018-03-06 19:57:21', '2018-03-06 19:57:21', 'mxp_menu_stock_management'),
(73, 1, '2018-03-06 19:58:01', '2018-03-06 19:58:01', 'mxp_menu_store'),
(74, 1, '2018-03-06 19:58:53', '2018-03-06 19:58:53', 'mxp_menu_stock'),
(76, 1, '2018-03-06 20:57:06', '2018-03-06 20:57:06', 'company_name_label'),
(77, 1, '2018-03-06 21:05:38', '2018-03-06 21:05:38', 'role_name_placeholder'),
(78, 1, '2018-03-06 21:06:59', '2018-03-06 21:06:59', 'select_company_option_label'),
(79, 1, '2018-03-06 21:08:51', '2018-03-06 21:08:51', 'select_role_option_label'),
(80, 1, '2018-03-06 21:11:57', '2018-03-06 21:11:57', 'select_all_label'),
(81, 1, '2018-03-06 21:12:36', '2018-03-06 21:12:36', 'unselect_all_label'),
(82, 1, '2018-03-06 21:14:03', '2018-03-06 21:14:03', 'set_button'),
(83, 1, '2018-03-06 21:15:41', '2018-03-06 21:15:41', 'heading_role_assign_label'),
(84, 1, '2018-03-06 21:19:23', '2018-03-06 21:19:23', 'heading_role_permission_list_label'),
(85, 1, '2018-03-06 21:19:57', '2018-03-06 21:19:57', 'option_permitted_route_list_label'),
(86, 1, '2018-03-06 21:36:58', '2018-03-06 21:36:58', 'heading_update_role_label'),
(87, 1, '2018-03-06 22:00:58', '2018-03-06 22:00:58', 'heading_add_stock_label'),
(88, 1, '2018-03-06 22:01:41', '2018-03-06 22:01:41', 'product_name_label'),
(89, 1, '2018-03-06 22:02:40', '2018-03-06 22:02:40', 'product_group_label'),
(90, 1, '2018-03-06 22:03:37', '2018-03-06 22:03:37', 'quantity_label'),
(91, 1, '2018-03-06 22:04:43', '2018-03-06 22:04:43', 'option_select_location_label'),
(94, 1, '2018-03-06 22:21:41', '2018-03-06 22:21:41', 'heading_add_new_stock_label'),
(95, 1, '2018-03-06 22:22:14', '2018-03-06 22:22:14', 'add_stock_label'),
(96, 1, '2018-03-06 22:23:21', '2018-03-06 22:23:21', 'enter_store_name_label'),
(97, 1, '2018-03-06 22:23:51', '2018-03-06 22:23:51', 'enter_store_location_label'),
(98, 1, '2018-03-06 22:27:47', '2018-03-06 22:27:47', 'heading_update_store_label'),
(100, 1, '2018-03-06 22:34:46', '2018-03-06 22:34:46', 'heading_store_list_label'),
(101, 1, '2018-03-06 22:36:32', '2018-03-06 22:36:32', 'store_name_label'),
(102, 1, '2018-03-06 22:37:36', '2018-03-06 22:37:36', 'store_location_label'),
(103, 1, '2018-03-06 22:45:51', '2018-03-06 22:45:51', 'list_of_responsible_person_label'),
(104, 1, '2018-03-07 21:50:23', '2018-03-07 21:50:23', 'company_phone_number_label'),
(105, 1, '2018-03-07 21:51:29', '2018-03-07 21:51:29', 'company_address_label'),
(106, 1, '2018-03-07 21:52:22', '2018-03-07 21:52:22', 'company_description_label'),
(107, 1, '2018-03-07 23:00:57', '2018-03-07 23:00:57', 'employee_name_label'),
(108, 1, '2018-03-07 23:02:33', '2018-03-07 23:02:33', 'personal_phone_number_label'),
(109, 1, '2018-03-07 23:03:16', '2018-03-07 23:03:16', 'employee_address_label'),
(110, 1, '2018-03-07 23:03:52', '2018-03-07 23:03:52', 'password_confirmation_label'),
(111, 1, '2018-03-07 23:11:42', '2018-03-07 23:11:42', 'search_placeholder'),
(112, 1, '2018-03-07 23:21:05', '2018-03-07 23:21:05', 'company_label'),
(113, 1, '2018-03-07 23:52:58', '2018-03-07 23:52:58', 'add_company_label'),
(114, 1, '2018-03-08 17:19:08', '2018-03-08 17:19:08', 'update_company_button'),
(115, 1, '2018-03-09 17:02:10', '2018-03-09 17:02:10', 'add_packet_button'),
(116, 1, '2018-03-09 17:04:20', '2018-03-09 17:04:20', 'option_select_unit_label'),
(117, 1, '2018-03-09 17:06:17', '2018-03-09 17:06:17', 'packet_name_label'),
(118, 1, '2018-03-09 17:07:27', '2018-03-09 17:07:27', 'unit_quantity_label'),
(119, 1, '2018-03-09 17:13:41', '2018-03-09 17:13:41', 'update_packet_button'),
(120, 1, '2018-03-09 17:18:32', '2018-03-09 17:18:32', 'unit_label'),
(121, 1, '2018-03-09 17:24:19', '2018-03-09 17:24:19', 'heading_packet_list'),
(122, 1, '2018-03-09 17:52:50', '2018-03-09 17:52:50', 'heading_add_new_packet_label'),
(124, 1, '2018-03-09 17:56:43', '2018-03-09 17:56:43', 'packet_details_label'),
(125, 1, '2018-03-09 18:02:50', '2018-03-09 18:02:50', 'product_code_label'),
(126, 1, '2018-03-09 18:09:32', '2018-03-09 18:09:32', 'heading_update_product_label'),
(127, 1, '2018-03-09 18:10:38', '2018-03-09 18:10:38', 'edit_product_label'),
(128, 1, '2018-03-09 18:26:17', '2018-03-09 18:26:17', 'product_group_name_label'),
(129, 1, '2018-03-09 18:26:52', '2018-03-09 18:26:52', 'add_product_group_label'),
(130, 1, '2018-03-09 18:27:22', '2018-03-09 18:27:22', 'add_new_product_group_label'),
(131, 1, '2018-03-09 18:34:53', '2018-03-09 18:34:53', 'edit_new_product_group_label'),
(132, 1, '2018-03-09 18:35:57', '2018-03-09 18:35:57', 'edit_product_group_label'),
(133, 1, '2018-03-09 18:39:48', '2018-03-09 18:39:48', 'heading_product_group_list_label'),
(134, 1, '2018-03-09 19:00:04', '2018-03-09 19:00:04', 'unit_name_label'),
(135, 1, '2018-03-09 19:00:51', '2018-03-09 19:00:51', 'add_unit_label'),
(136, 1, '2018-03-09 19:02:17', '2018-03-09 19:02:17', 'add_new_unit_label'),
(137, 1, '2018-03-09 19:04:46', '2018-03-09 19:04:46', 'update_unit_label'),
(138, 1, '2018-03-09 19:05:17', '2018-03-09 19:05:17', 'edit_unit_label'),
(139, 1, '2018-03-09 19:09:55', '2018-03-09 19:09:55', 'party_name_label'),
(140, 1, '2018-03-09 19:11:03', '2018-03-09 19:11:03', 'add_vat_tax_label'),
(141, 1, '2018-03-09 19:13:30', '2018-03-09 19:13:30', 'option_select_product_label'),
(142, 1, '2018-03-09 19:18:16', '2018-03-09 19:18:16', 'heading_report_label'),
(143, 1, '2018-03-09 19:24:36', '2018-03-09 19:24:36', 'available_quantity_label'),
(144, 1, '2018-03-09 19:25:47', '2018-03-09 19:25:47', 'sale_quantity_label'),
(145, 1, '2018-03-09 19:26:25', '2018-03-09 19:26:25', 'total_quantity_label'),
(146, 1, '2018-03-09 19:44:45', '2018-03-09 19:44:45', 'option_select_invoice_label'),
(147, 1, '2018-03-09 19:45:57', '2018-03-09 19:45:57', 'search_date_placeholder'),
(148, 1, '2018-03-09 19:47:32', '2018-03-09 19:47:32', 'date_label'),
(149, 1, '2018-03-09 19:48:38', '2018-03-09 19:48:38', 'invoice_no_label'),
(150, 1, '2018-03-09 19:50:42', '2018-03-09 19:50:42', 'quantity_per_kg_label'),
(151, 1, '2018-03-09 19:51:26', '2018-03-09 19:51:26', 'unit_price_per_kg_label'),
(152, 1, '2018-03-09 19:52:14', '2018-03-09 19:52:14', 'total_uptodate_quantity_label'),
(153, 1, '2018-03-11 17:00:41', '2018-03-11 17:00:41', 'heading_user_list_label'),
(154, 1, '2018-03-21 01:37:13', '2018-03-21 01:37:13', 'mxp_menu_local_purchase'),
(155, 1, '2018-03-21 01:54:39', '2018-03-21 01:54:39', 'mxp_menu_lc_purchase'),
(156, 1, '2018-04-02 06:48:56', '2018-04-02 06:48:56', 'mxp_view_btn'),
(157, 1, '2018-04-10 00:01:48', '2018-04-10 00:01:48', 'mxp_menu_management'),
(158, 1, '2018-04-10 00:38:18', '2018-04-10 00:38:18', 'mxp_menu_product_list'),
(159, 1, '2018-04-10 04:32:01', '2018-04-10 04:32:01', 'product_description_label'),
(160, 1, '2018-04-10 04:34:38', '2018-04-10 04:34:38', 'product_brand_label'),
(161, 1, '2018-04-10 04:41:38', '2018-04-10 04:41:38', 'product_erp_code_label'),
(162, 1, '2018-04-10 04:43:37', '2018-04-10 04:43:37', 'product_unit_price_label'),
(163, 1, '2018-04-10 04:46:17', '2018-04-10 04:46:17', 'product_weight_qty_label'),
(164, 1, '2018-04-10 04:46:54', '2018-04-10 04:46:54', 'product_weight_amt_label'),
(165, 1, '2018-04-10 04:51:05', '2018-04-10 04:51:05', 'product_description1_label'),
(166, 1, '2018-04-10 04:51:29', '2018-04-10 04:51:29', 'product_description2_label'),
(167, 1, '2018-04-10 04:54:29', '2018-04-10 04:54:29', 'product_description3_label'),
(168, 1, '2018-04-10 04:54:44', '2018-04-10 04:54:44', 'product_description4_label'),
(169, 1, '2018-04-12 00:30:29', '2018-04-12 00:30:29', 'mxp_menu_party_list'),
(170, 1, '2018-04-12 00:34:45', '2018-04-12 00:34:45', 'party_id_label'),
(171, 1, '2018-04-12 00:35:35', '2018-04-12 00:35:35', 'name_buyer_label'),
(172, 1, '2018-04-12 00:36:08', '2018-04-12 00:36:08', 'address_part_1_invoice_label'),
(173, 1, '2018-04-12 00:37:02', '2018-04-12 00:37:02', 'address_part_2_invoice_label'),
(174, 1, '2018-04-12 00:38:52', '2018-04-12 00:38:52', 'attention_invoice_label'),
(175, 1, '2018-04-12 00:39:26', '2018-04-12 00:39:26', 'mobile_invoice_label'),
(176, 1, '2018-04-12 00:40:01', '2018-04-12 00:40:01', 'telephone_invoice_label'),
(177, 1, '2018-04-12 00:40:51', '2018-04-12 00:40:51', 'fax_invoice_label'),
(178, 1, '2018-04-12 00:41:25', '2018-04-12 00:41:25', 'address_part1_delivery_label'),
(179, 1, '2018-04-12 00:41:54', '2018-04-12 00:41:54', 'address_part2_delivery_label'),
(180, 1, '2018-04-12 00:42:23', '2018-04-12 00:42:23', 'attention_delivery_label'),
(181, 1, '2018-04-12 00:42:51', '2018-04-12 00:42:51', 'mobile_delivery_label'),
(182, 1, '2018-04-12 00:43:13', '2018-04-12 00:43:13', 'telephone_delivery_label'),
(183, 1, '2018-04-12 00:43:40', '2018-04-12 00:43:40', 'fax_delivery_label'),
(184, 1, '2018-04-12 00:44:02', '2018-04-12 00:44:02', 'description1_label'),
(185, 1, '2018-04-12 00:44:29', '2018-04-12 00:44:29', 'description2_label'),
(186, 1, '2018-04-12 00:44:53', '2018-04-12 00:44:53', 'description3_label'),
(187, 1, '2018-04-12 01:09:15', '2018-04-12 01:09:15', 'add_party_label'),
(188, 1, '2018-04-12 04:03:01', '2018-04-12 04:03:01', 'mxp_menu_page_header'),
(189, 1, '2018-04-12 04:16:18', '2018-04-12 04:16:18', 'header_title_label'),
(190, 1, '2018-04-12 04:19:01', '2018-04-12 04:19:01', 'header_fontsize_label'),
(191, 1, '2018-04-12 04:21:38', '2018-04-12 04:21:38', 'header_font_style_label'),
(192, 1, '2018-04-12 04:26:04', '2018-04-12 04:26:04', 'header_color_label'),
(193, 1, '2018-04-12 04:26:24', '2018-04-12 04:26:24', 'header_address1_label'),
(194, 1, '2018-04-12 04:29:35', '2018-04-12 04:29:35', 'header_address3_label'),
(195, 1, '2018-04-12 04:30:05', '2018-04-12 04:30:05', 'header_logo_aligment_label'),
(196, 1, '2018-04-12 04:30:39', '2018-04-12 04:30:39', 'header_address2_label'),
(197, 1, '2018-04-12 04:31:03', '2018-04-12 04:31:03', 'header_logo_label'),
(198, 1, '2018-04-12 05:31:25', '2018-04-12 05:31:25', 'mxp_menu_page'),
(199, 1, '2018-04-12 05:38:56', '2018-04-12 05:38:56', 'mxp_menu_page_footer'),
(200, 1, '2018-04-12 06:07:56', '2018-04-12 06:07:56', 'add_page_footer_title_label'),
(201, 1, '2018-04-12 06:09:53', '2018-04-12 06:09:53', 'page_footer_title_label'),
(202, 0, '2018-04-12 06:10:56', '2018-04-12 06:10:56', 'enter_title_label'),
(203, 1, '2018-04-13 02:05:45', '2018-04-13 02:05:45', 'update_page_footer_title_label'),
(204, 1, '2018-04-13 02:35:41', '2018-04-13 02:35:41', 'mxp_menu_report_footer'),
(205, 1, '2018-04-13 04:43:27', '2018-04-13 04:43:27', 'add_report_footer_label'),
(206, 1, '2018-04-13 04:44:39', '2018-04-13 04:44:39', 'report_name_label'),
(207, 1, '2018-04-13 04:48:23', '2018-04-13 04:48:23', 're_fo_des3_label'),
(208, 1, '2018-04-13 04:49:10', '2018-04-13 04:49:10', 're_fo_des1_label'),
(209, 1, '2018-04-13 04:50:50', '2018-04-13 04:50:50', 're_fo_des4_label'),
(210, 1, '2018-04-13 04:51:12', '2018-04-13 04:51:12', 're_fo_des2_label'),
(211, 1, '2018-04-13 04:51:28', '2018-04-13 04:51:28', 're_fo_des5_label'),
(212, 1, '2018-04-13 04:56:18', '2018-04-13 04:56:18', 're_fo_siginingPerson_1_label'),
(213, 1, '2018-04-13 04:56:26', '2018-04-13 04:56:26', 're_fo_siginingPerson_2_label'),
(214, 1, '2018-04-13 07:03:29', '2018-04-13 07:03:29', 'person_1_signature'),
(215, 1, '2018-04-13 07:04:58', '2018-04-13 07:04:58', 'persion_seal_label'),
(216, 1, '2018-04-13 07:26:33', '2018-04-13 07:26:33', 'person_name_label'),
(217, 1, '2018-04-16 00:43:54', '2018-04-16 00:43:54', 'mxp_menu_brand'),
(218, 1, '2018-04-16 01:36:20', '2018-04-16 01:36:20', 'add_brand_label'),
(219, 1, '2018-04-16 01:42:14', '2018-04-16 01:42:14', 'brand_name_label'),
(220, 1, '2018-04-16 02:25:24', '2018-04-16 02:25:24', 'mxp_menu_product_size'),
(221, 1, '2018-04-16 04:19:18', '2018-04-16 04:19:18', 'add_product_size_label'),
(222, 1, '2018-04-16 04:21:37', '2018-04-16 04:21:37', 'product_size_label'),
(223, 1, '2018-04-16 04:23:26', '2018-04-16 04:23:26', 'add_size_label'),
(224, 1, '2018-04-16 06:09:18', '2018-04-16 06:09:18', 'header_font_size_label'),
(225, 1, '2018-04-16 06:11:24', '2018-04-16 06:11:24', 'header_colour_label'),
(226, 1, '2018-04-16 06:12:38', '2018-04-16 06:12:38', 'logo_allignment_label'),
(227, 1, '2018-04-17 05:31:18', '2018-04-17 05:31:18', 'mxp_menu_print'),
(228, 1, '2018-04-17 05:32:54', '2018-04-17 05:32:54', 'mxp_menu_bill_copy'),
(229, 1, '2018-04-25 22:24:35', '2018-04-25 22:24:35', 'mxp_menu_all_bill_view'),
(230, 1, '2018-05-03 00:37:53', '2018-05-03 00:37:53', 'add_searchbill_label'),
(231, 1, '2018-05-03 00:43:16', '2018-05-03 00:43:16', 'bill_invo_no_label'),
(232, 1, '2018-05-03 02:54:24', '2018-05-03 02:54:24', 'search_button'),
(233, 1, '2018-05-03 02:59:29', '2018-05-03 02:59:29', 'genarate_bill_button'),
(234, 1, '2018-05-03 03:48:13', '2018-05-03 03:48:13', 'mxp_menu_challan_boxing_list'),
(235, 1, '2018-05-06 23:53:37', '2018-05-06 23:53:37', 'mxp_menu_multiple_challan_search'),
(236, 1, '2018-05-06 23:58:00', '2018-05-06 23:58:00', 'challan_no_label'),
(237, 1, '2018-05-07 00:53:50', '2018-05-07 00:53:50', 'mxp_menu_order_list_view'),
(238, 1, '2018-05-07 01:00:57', '2018-05-07 01:00:57', 'mxp_menu_order_list'),
(239, 1, '2018-05-07 01:58:12', '2018-05-07 01:58:12', 'mxp_menu_ipo_view'),
(240, 1, '2018-05-07 02:01:49', '2018-05-07 02:01:49', 'initial_increase_label'),
(241, 1, '2018-05-10 03:00:27', '2018-05-10 03:00:27', 'update_ueader'),
(242, 1, '2018-05-10 23:31:45', '2018-05-10 23:31:45', 'report_footer_list'),
(243, 1, '2018-05-10 23:37:20', '2018-05-10 23:37:20', 'update_report_footer_label'),
(244, 1, '2018-05-11 00:34:47', '2018-05-11 00:34:47', 'update_brand_label'),
(245, 1, '2018-05-11 00:37:57', '2018-05-11 00:37:57', 'brand_list_label'),
(246, 1, '2018-05-11 00:41:44', '2018-05-11 00:41:44', 'party_list_label'),
(247, 1, '2018-05-11 00:45:50', '2018-05-11 00:45:50', 'header_status_label'),
(248, 1, '2018-05-11 00:46:18', '2018-05-11 00:46:18', 'invoice_label'),
(249, 1, '2018-05-11 00:46:44', '2018-05-11 00:46:44', 'delivery_label'),
(250, 1, '2018-05-11 00:55:04', '2018-05-11 00:55:04', 'sort_name_label'),
(251, 1, '2018-05-11 02:29:51', '2018-05-11 02:29:51', 'header_list_label'),
(252, 1, '2018-05-11 02:31:28', '2018-05-11 02:31:28', 'add_header_label'),
(253, 1, '2018-05-11 02:40:55', '2018-05-11 02:40:55', 'header_address_label'),
(254, 1, '2018-05-11 02:43:38', '2018-05-11 02:43:38', 'footer_title_label'),
(255, 1, '2018-05-11 02:47:49', '2018-05-11 02:47:49', 'update_party_label'),
(256, 1, '2018-05-11 02:49:32', '2018-05-11 02:49:32', 'product_list_label'),
(257, 1, '2018-05-13 23:30:27', '2018-05-13 23:30:27', 'product_size_list'),
(258, 1, '2018-05-16 00:09:31', '2018-05-16 00:09:31', 'mxp_menu_order_input'),
(259, 1, '2018-06-05 22:36:08', '2018-06-05 22:36:08', 'task_label'),
(260, 1, '2018-06-05 23:27:03', '2018-06-05 23:27:03', 'mxp_menu_gmts_color'),
(261, 1, '2018-06-05 23:34:38', '2018-06-05 23:34:38', 'Gmts_color_list_label'),
(262, 1, '2018-06-05 23:42:36', '2018-06-05 23:42:36', 'add_color_label'),
(263, 1, '2018-06-05 23:54:08', '2018-06-05 23:54:08', 'add_gmts_color_label'),
(264, 1, '2018-06-06 01:57:57', '2018-06-06 01:57:57', 'gmts_color_label'),
(265, 1, '2018-06-06 02:33:37', '2018-06-06 02:33:37', 'update_gmts_color_label'),
(266, 1, '2018-06-06 02:34:28', '2018-06-06 02:34:28', 'update_color_label'),
(267, 1, '2018-06-08 02:58:26', '2018-06-08 02:58:26', 'header_type_label'),
(268, 1, '2018-06-08 03:06:08', '2018-06-08 03:06:08', 'header_cell_number_label'),
(269, 1, '2018-06-08 03:06:38', '2018-06-08 03:06:38', 'header_attention_label'),
(270, 1, '2018-06-20 06:29:05', '2018-06-20 06:29:05', 'mxp_menu_production'),
(271, 1, '2018-06-20 06:29:40', '2018-06-20 06:29:40', 'mxp_menu_booking'),
(272, 1, '2018-06-21 04:07:20', '2018-06-21 04:07:20', 'mxp_menu_booking_list'),
(273, 1, '2018-07-09 01:31:09', '2018-07-09 01:31:09', 'add_product_label'),
(274, 1, '2018-07-09 01:40:20', '2018-07-09 01:40:20', 'update_product_label'),
(275, 1, '2018-07-10 01:18:23', '2018-07-10 01:18:23', 'others_color_label'),
(276, 1, '2018-07-10 02:31:49', '2018-07-10 02:31:49', 'new_mrf_create_label'),
(277, 1, '2018-07-10 02:46:42', '2018-07-10 02:46:42', 'create_button_lable'),
(278, 1, '2018-07-10 06:40:06', '2018-07-10 06:40:06', 'mxp_menu_mrf_list'),
(279, 1, '2018-07-13 05:04:03', '2018-07-13 05:04:03', 'mxp_menu_challan_list'),
(280, 1, '2018-07-28 01:46:36', '2018-07-28 01:46:36', 'mxp_menu_purchase_order'),
(281, 1, '2018-07-28 03:30:47', '2018-07-28 03:30:47', 'mxp_menu_purchase_order_list'),
(282, 1, '2018-09-05 12:01:06', '2018-09-05 12:01:06', 'product_type_label'),
(283, 1, '2018-09-05 12:02:01', '2018-09-05 12:02:01', 'mxp_menu_suppliers'),
(284, 1, '2018-09-05 12:02:36', '2018-09-05 12:02:36', 'add_product_status'),
(285, 1, '2018-09-05 12:06:27', '2018-09-05 12:06:27', 'ipo_increase_percentage'),
(286, 1, '2018-09-05 12:09:23', '2018-09-05 12:09:23', 'mxp_menu_booking_view_details'),
(287, 1, '2018-09-13 04:22:29', '2018-09-13 04:22:29', 'mxp_menu_permission_task_assign'),
(288, 1, '2018-09-13 04:28:16', '2018-09-13 04:28:16', 'mxp_menu_vendor_list'),
(289, 1, '2018-09-17 03:49:48', '2018-09-17 03:49:48', 'mxp_menu_tracking_list_report'),
(290, 1, '2018-09-17 03:51:06', '2018-09-17 03:51:06', 'mxp_menu_production_pi_list_view'),
(291, 1, '2018-09-18 03:23:41', '2018-09-18 03:23:41', 'mxp_menu_production_ipo_list_view'),
(292, 1, '2018-09-24 23:58:57', '2018-09-24 23:58:57', 'mxp_menu_item__list_view'),
(293, 1, '2018-09-25 00:00:12', '2018-09-25 00:00:12', 'mxp_menu_item_size_view'),
(294, 1, '2018-09-25 00:20:54', '2018-09-25 00:20:54', 'mxp_menu_booking_list_view'),
(295, 1, '2018-09-25 03:02:59', '2018-09-25 03:02:59', 'mxp_menu_item_description_list_view'),
(296, 1, '2018-09-25 03:04:16', '2018-09-25 03:04:16', 'add_description_label'),
(297, 1, '2018-09-25 03:05:29', '2018-09-25 03:05:29', 'item_description_list_label'),
(298, 1, '2018-09-25 03:07:10', '2018-09-25 03:07:10', 'description_name_label'),
(299, 1, '2018-09-25 03:07:50', '2018-09-25 03:07:50', 'update_description_label'),
(300, 1, '2018-09-25 03:16:26', '2018-09-25 03:16:26', 'mxp_menu_buyer_list_view_'),
(301, 1, '2018-09-25 03:20:29', '2018-09-25 03:20:29', 'buyer_list_label'),
(302, 1, '2018-09-25 03:21:07', '2018-09-25 03:21:07', 'add_buyer_label'),
(303, 1, '2018-09-25 03:21:52', '2018-09-25 03:21:52', 'buyer_name_label'),
(304, 1, '2018-09-25 03:22:37', '2018-09-25 03:22:37', 'update_buyer_label');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_userbuyer`
--

CREATE TABLE `mxp_userbuyer` (
  `id_userbuyer` int(10) UNSIGNED NOT NULL,
  `id_buyer` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_userbuyer`
--

INSERT INTO `mxp_userbuyer` (`id_userbuyer`, `id_buyer`, `id_user`, `created_at`, `updated_at`) VALUES
(3, 1, 87, '2018-09-19 00:53:15', '2018-09-19 00:53:15'),
(4, 2, 87, '2018-09-19 00:53:15', '2018-09-19 00:53:15'),
(5, 3, 87, '2018-09-19 00:53:15', '2018-09-19 00:53:15'),
(14, 26, 81, '2018-09-19 22:36:10', '2018-09-19 22:36:10'),
(15, 34, 80, '2018-09-19 22:37:03', '2018-09-19 22:37:03'),
(16, 34, 79, '2018-09-19 22:37:34', '2018-09-19 22:37:34'),
(17, 5, 78, '2018-09-19 22:38:47', '2018-09-19 22:38:47'),
(18, 27, 77, '2018-09-19 22:39:39', '2018-09-19 22:39:39'),
(19, 29, 76, '2018-09-19 22:44:38', '2018-09-19 22:44:38'),
(20, 30, 76, '2018-09-19 22:44:38', '2018-09-19 22:44:38'),
(21, 31, 76, '2018-09-19 22:44:38', '2018-09-19 22:44:38'),
(22, 32, 76, '2018-09-19 22:44:38', '2018-09-19 22:44:38'),
(23, 34, 73, '2018-09-19 22:46:54', '2018-09-19 22:46:54'),
(35, 26, 71, '2018-09-19 22:55:36', '2018-09-19 22:55:36'),
(36, 6, 70, '2018-09-19 22:58:16', '2018-09-19 22:58:16'),
(37, 7, 70, '2018-09-19 22:58:16', '2018-09-19 22:58:16'),
(38, 5, 69, '2018-09-19 22:58:53', '2018-09-19 22:58:53'),
(41, 5, 67, '2018-09-19 23:02:11', '2018-09-19 23:02:11'),
(42, 27, 66, '2018-09-19 23:02:54', '2018-09-19 23:02:54'),
(43, 7, 65, '2018-09-19 23:03:59', '2018-09-19 23:03:59'),
(44, 27, 64, '2018-09-19 23:05:08', '2018-09-19 23:05:08'),
(45, 28, 64, '2018-09-19 23:05:08', '2018-09-19 23:05:08'),
(46, 31, 64, '2018-09-19 23:05:08', '2018-09-19 23:05:08'),
(47, 32, 64, '2018-09-19 23:05:08', '2018-09-19 23:05:08'),
(48, 9, 62, '2018-09-19 23:07:01', '2018-09-19 23:07:01'),
(60, 6, 59, '2018-09-19 23:12:56', '2018-09-19 23:12:56'),
(61, 7, 59, '2018-09-19 23:12:56', '2018-09-19 23:12:56'),
(62, 7, 58, '2018-09-19 23:14:33', '2018-09-19 23:14:33'),
(63, 8, 58, '2018-09-19 23:14:33', '2018-09-19 23:14:33'),
(64, 23, 57, '2018-09-19 23:15:12', '2018-09-19 23:15:12'),
(65, 27, 56, '2018-09-19 23:16:59', '2018-09-19 23:16:59'),
(66, 28, 56, '2018-09-19 23:16:59', '2018-09-19 23:16:59'),
(67, 29, 56, '2018-09-19 23:16:59', '2018-09-19 23:16:59'),
(68, 30, 56, '2018-09-19 23:16:59', '2018-09-19 23:16:59'),
(69, 31, 56, '2018-09-19 23:16:59', '2018-09-19 23:16:59'),
(70, 32, 56, '2018-09-19 23:16:59', '2018-09-19 23:16:59'),
(71, 5, 55, '2018-09-19 23:17:38', '2018-09-19 23:17:38'),
(72, 5, 54, '2018-09-19 23:18:14', '2018-09-19 23:18:14'),
(73, 34, 53, '2018-09-19 23:19:14', '2018-09-19 23:19:14'),
(74, 6, 52, '2018-09-19 23:20:20', '2018-09-19 23:20:20'),
(75, 7, 52, '2018-09-19 23:20:20', '2018-09-19 23:20:20'),
(76, 8, 52, '2018-09-19 23:20:20', '2018-09-19 23:20:20'),
(77, 10, 68, '2018-09-19 23:47:17', '2018-09-19 23:47:17'),
(78, 11, 68, '2018-09-19 23:47:17', '2018-09-19 23:47:17'),
(79, 24, 68, '2018-09-19 23:47:17', '2018-09-19 23:47:17'),
(80, 25, 68, '2018-09-19 23:47:17', '2018-09-19 23:47:17'),
(81, 9, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(82, 10, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(83, 11, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(84, 12, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(85, 13, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(86, 14, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(87, 15, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(88, 17, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(89, 18, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(90, 21, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(91, 23, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(92, 24, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(93, 25, 61, '2018-09-19 23:48:19', '2018-09-19 23:48:19'),
(105, 11, 72, '2018-09-20 02:47:45', '2018-09-20 02:47:45'),
(106, 12, 72, '2018-09-20 02:47:45', '2018-09-20 02:47:45'),
(107, 13, 72, '2018-09-20 02:47:45', '2018-09-20 02:47:45'),
(108, 14, 72, '2018-09-20 02:47:45', '2018-09-20 02:47:45'),
(109, 15, 72, '2018-09-20 02:47:45', '2018-09-20 02:47:45'),
(110, 17, 72, '2018-09-20 02:47:45', '2018-09-20 02:47:45'),
(111, 18, 72, '2018-09-20 02:47:45', '2018-09-20 02:47:45'),
(112, 19, 72, '2018-09-20 02:47:46', '2018-09-20 02:47:46'),
(113, 21, 72, '2018-09-20 02:47:46', '2018-09-20 02:47:46'),
(114, 23, 72, '2018-09-20 02:47:46', '2018-09-20 02:47:46'),
(115, 38, 72, '2018-09-20 02:47:46', '2018-09-20 02:47:46'),
(116, 5, 82, '2018-09-24 23:32:52', '2018-09-24 23:32:52'),
(117, 6, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(118, 7, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(119, 8, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(120, 9, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(121, 10, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(122, 11, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(123, 12, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(124, 13, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(125, 14, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(126, 15, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(127, 16, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(128, 17, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(129, 18, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(130, 19, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(131, 20, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(132, 21, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(133, 22, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(134, 23, 82, '2018-09-24 23:32:53', '2018-09-24 23:32:53'),
(135, 24, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(136, 25, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(137, 26, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(138, 27, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(139, 28, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(140, 29, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(141, 30, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(142, 31, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(143, 32, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(144, 33, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(145, 34, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(146, 35, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(147, 36, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(148, 37, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54'),
(149, 38, 82, '2018-09-24 23:32:54', '2018-09-24 23:32:54');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_users`
--

CREATE TABLE `mxp_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` int(100) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `user_role_id` int(11) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_users`
--

INSERT INTO `mxp_users` (`user_id`, `first_name`, `middle_name`, `last_name`, `address`, `type`, `group_id`, `company_id`, `email`, `password`, `phone_no`, `remember_token`, `is_active`, `user_role_id`, `verified`, `verification_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'middle', 'last', NULL, 'super_admin', 0, 0, 'sajibg7@gmail.com', '$2y$10$BIvmvrQf1a5G3mrmHlrN9ulYV1fKtgUoJaK968BJ2foPBTkVjWn7S', '123456789', 'iIHmBELR0m6a2Kx9pFX1pKmYj1Qp3blvIcmp263AnSGZQo56ySbMCnoCwfO3', 1, 1, 0, '0', '2018-01-15 01:37:15', '2018-03-05 13:29:32'),
(24, 'Beximco user', 'moinul', 'sajibg', NULL, 'super_admin', 0, 0, 'sajibg7+1@gmail.com', '$2y$10$voCXiMsv.R.X.pl6F8DbnuFyIiwyhrpYB.na/FITZNz7ZIGyLVmfC', '01674898148', '5zesn2ucLuXz1fN1tVBETDkjrIEBqG38fFiglfuVQzr4BcAbECNiV67d3xKI', 1, 1, 0, '0', '2018-01-29 06:36:28', '2018-01-29 06:36:28'),
(26, 'company-a-user', NULL, NULL, NULL, 'company_user', 1, 11, 'sajibg7+3@gmail.com', '$2y$10$gxTBxp.V1v2TJphLkJWmLuqIwhdNu0WxUZSDgnkNyc0D/.YnhCkc2', '12143234235', 'TezCzw56wUjAeVkvits9Zkaj5ZVLjRNYAauQDTh0DmT6AdtSiXY5Qs8CcGPu', 1, 21, 0, '0', '2018-01-29 06:44:07', '2018-01-29 06:44:07'),
(27, 'Sumit Power user', 'moinul', 'sajibg', NULL, 'super_admin', 0, 0, 'sajibg7+4@gmail.com', '$2y$10$DYvlonHYz7onBx3U743LoeSQX166D4Y.EFxJDI33WfbUFuHvvUrZ.', '01674898148', 'kcraPAbsogfCaWXXzizdBCRSYOIqrplPy77x3qrT', 0, 1, 0, '0', '2018-01-30 00:16:13', '2018-01-30 00:16:13'),
(36, 'Sumit Power user-2', 'moinul', 'sajibg', NULL, 'super_admin', 0, 0, 'sajibg7+5@gmail.com', '$2y$10$9PUEtsR3rv82eJ7TFyG/wOEuTtbXUbcTJWZ0Wz1EBFRnNLqzHROje', '01674898148', 'kcraPAbsogfCaWXXzizdBCRSYOIqrplPy77x3qrT', 0, 1, 0, '0', '2018-01-30 00:32:37', '2018-01-30 00:32:37'),
(38, 'Sumit Power user-22', 'moinul', 'sajibg', NULL, 'super_admin', 0, 0, 'sajibg7+23@gmail.com', '$2y$10$0.jZXV4ihdxJKIqI3STDb.4QB3.fd2szjsQLUCeijhVXSyuzQw0gy', '01674898148', 'DORz0nqgyRNUEPWahczArNAlVYTil0mFXMniff6BAaVmMLjO2sywBn0BvHS5', 1, 1, 0, '0', '2018-01-31 02:56:31', '2018-01-31 02:56:31'),
(39, 'mxp_name', NULL, NULL, NULL, 'company_user', 38, 13, 'sajibg7+77@gmail.com', '$2y$10$O4ZTP39xhT2NtkYcAE1I1u3ZVfn/CA4PC5954PJVYP92yQ1e3oJSG', '2222222222', 'zJ9Fq0pgJp1Ffo1AljnyQS2IHKDKgD59zDokr5ufo7wzNjjNAG5zHgX2w9kw', 1, 25, 0, '0', '2018-01-31 03:00:36', '2018-01-31 03:00:36'),
(40, 'mxp_name', NULL, NULL, NULL, 'company_user', 38, 14, 'sajibg7+78@gmail.com', '$2y$10$/RIWK3dmNz5i0RO6p.b8h.fIgPVOukwUUVdydW4zuqjDYZgnuFT3y', '2222222222', 'CMIeb4F5GnV3Gvzeq6n7FvUwdCN8DM1NPoEwkVaHyLwYPSnc7U2P52xLfX1R', 1, 26, 0, '0', '2018-01-31 03:00:53', '2018-01-31 03:00:53'),
(41, 'Beximco', NULL, NULL, '56,gazipur', 'client_com', 1, 10, 'beximco@beximco.com', NULL, '21321564654687987', NULL, 1, NULL, 0, '0', '2018-02-02 06:14:45', '2018-02-02 06:14:45'),
(42, 'New Admin', 'Middle', 'Last', NULL, 'super_admin', 0, 0, 'newadmin@mail.com', '$2y$10$x1yzwN3LXrb8fkXSCg9Roeu.EBlSQpJf1U.ouqzdOi1F5z2robRd2', '1234567890', 'I500mFPOncDcawx0KwHnzx35J0rH1TUOIT6m4omT', 1, 1, 0, '0', '2018-02-09 01:58:04', '2018-02-09 01:58:04'),
(43, 'New Client', NULL, NULL, NULL, 'client_com', 42, 16, 'newclient@mail.com', NULL, '1234567890', NULL, 1, NULL, 0, '0', '2018-02-09 02:09:35', '2018-02-09 02:09:35'),
(48, 'test user', NULL, NULL, NULL, 'company_user', 1, 10, 'sajibg7+09@gmail.com', '$2y$10$NItNEFuZfxtXosv7iRoU0utNjKMIijcYPFTj5J/r26AY86hZg2w6W', '123456', NULL, 1, 29, 0, '0', '2018-04-09 01:58:28', '2018-04-09 01:58:28'),
(49, 'shohidur', NULL, 'Rahman', NULL, 'super_admin', 0, 0, 'sohidurr49@gmail.com', '$2y$10$.JwEQcEC.OTXRG4aP/PsU.iomnby.5ndA35BeOVrh2Mb03x1LMlsS', '01792755683', 'N2qmbObK5V6IlqyJZ0G6p5C6u06oFRhvXz0vRbTWuDiIE4hhNQw8sohfJTdI', 1, 1, 0, '0', '2018-04-09 04:17:47', '2018-04-09 04:17:47'),
(50, 'Shohid', NULL, NULL, NULL, 'company_user', 49, 18, 'test111@mail.com', '$2y$10$lhlWW/5g71MYtdPWgcGLbOlCEzeVRcVlhmab7KGhHEI7.n2EtmC.O', '1234567890', 'I0QouznQ2v43e8SflagFdAzhvPGLvm3328IZLS76Yt1PZJny12BmolxXhNg9', 1, 31, 0, '0', '2018-05-10 00:10:17', '2018-05-10 00:10:17'),
(51, 'shohidur', NULL, NULL, NULL, 'company_user', 49, 19, 'abc2@gmail.com', '$2y$10$mgh9bfjNxFzzwwa9cLkOqu/VJQJA2eLCL8SgLQPlRa1mBWJr/Gn7.', '01723232232', 'z8QLd0EhlOwruI1KWevQeIvNYqbtdrFu5xCMfJ7VdelqigNN9FDBM3SoMyS2', 1, 36, 0, '0', '2018-09-06 04:16:20', '2018-09-06 04:16:20'),
(52, 'Shohan', NULL, NULL, NULL, 'company_user', 49, 17, 'sales-2bd@maxim-group.com', '$2y$10$YOYHDA/ponNSLPkVS54zROMH5RkS/Ct4iKSLRdIP54FIFyiTr9vMC', '01713154860', 'fXcPL2D0WZ84Xzu4NC2JhGc2BZYFsWgr5wVYRyKvzLhU04reAdguxu64usuC', 1, 37, 0, '0', '2018-09-09 21:54:47', '2018-09-12 05:20:24'),
(53, 'Asraf', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-1bd@maxim-group.com', '$2y$10$XKvmgruJJhSNZQ1/Tt02UuC4SPhBdduKLRwuk8it4ONGtOtzqhIXW', '01740331629', '9AgQ0SUbijUfFYJNgEAHpKRpZGL9ZGNqH90QZ9dapid46JctslRXETo0LEDU', 1, 37, 0, '0', '2018-09-11 04:01:48', '2018-09-19 23:19:13'),
(54, 'Sujon', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-2bd@maxim-group.com', '$2y$10$hKdhEI5.nNoRS6Ga5bPJNeYEuAggJj.ZL2DdQzjRdCTb4lkh8CJQ.', '0', '00NpHYjQtA1ZRHITRknpLt6TbqZtaiMKaUxLnlYlNiMndFZOj7dNqBtCdBjN', 1, 37, 0, '0', '2018-09-11 04:03:51', '2018-09-11 04:08:12'),
(55, 'Bashar', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-3bd@maxim-group.com', '$2y$10$tvykh0hXTzM/PKCF.L3T/.nbUacxBfxg9kN3afAr2Eh7s2tAXKYBq', '\'01799926476', NULL, 1, 37, 0, '0', '2018-09-11 04:09:56', '2018-09-11 04:09:56'),
(56, 'Jewel', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-4bd@maxim-group.com', '$2y$10$KymLyat1m2Yj7alsbBiTx.K1xPIYq2rgR5jMTfXEYvjk2rfCYD/iW', '01676596644', NULL, 1, 37, 0, '0', '2018-09-11 04:11:04', '2018-09-11 04:11:04'),
(57, 'Pias', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-6bd@maxim-group.com', '$2y$10$N2TXaHY62EmCGyXjXY7B/OJpDCzTaS2Pbl7YZ4xswfgAWe9SzxBsq', '0', NULL, 1, 37, 0, '0', '2018-09-11 04:12:12', '2018-09-19 23:15:12'),
(58, 'Sumiya', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-7bd@maxim-group.com', '$2y$10$gostFknpFNUmIOFpg5ATEOB8k5GjBgtw4VulEatfXzZhQLGPFq1uG', '01754151057', NULL, 1, 37, 0, '0', '2018-09-11 04:14:51', '2018-09-11 04:14:51'),
(59, 'Jasim', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-8bd@maxim-group.com', '$2y$10$uWOxoBETwCRREgyAlk7bB.zLIqVq5bZAgqnZhER7fi70vX7cRqcU2', '01760129694', NULL, 1, 37, 0, '0', '2018-09-11 04:16:23', '2018-09-11 04:16:23'),
(60, 'Shamim', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-9bd@maxim-group.com', '$2y$10$Yn42MU/9m51abYo7VwM2b..eVaUDCFPhag4JrTgaI.TPtNEzIym.G', '01711164976', NULL, 1, 37, 0, '0', '2018-09-11 04:17:26', '2018-09-11 04:17:26'),
(61, 'Tanvir', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-10bd@maxim-group.com', '$2y$10$7KRYjdwkAbY0Jt9l1TL9luBmRuwHp3u0XRSCn2F7BIHWleP94K6CS', '01713154857', 'Jhb9rYRfR7pxhAf64hg3cloQv3PB52Ar1tLUSdi4E5M0o5g549dBvoPkFBvm', 1, 37, 0, '0', '2018-09-11 04:18:21', '2018-09-11 04:18:21'),
(62, 'Pia', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-12bd@maxim-group.com', '$2y$10$CvWK7Mhk4OLM4X4SaHy4luInKZZ9DcZTRcRuK8sQryj.5yARQcse2', '01821864228', NULL, 1, 37, 0, '0', '2018-09-11 04:19:17', '2018-09-11 04:19:17'),
(63, 'Anis', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-13bd@maxim-group.com', '$2y$10$ouoMTeoMJZaT8ctCHH7MCOmo2tg/HXPXYhEVo75/7W.u3xnfHr85e', '01799926491', NULL, 1, 37, 0, '0', '2018-09-11 04:20:15', '2018-09-11 04:20:15'),
(64, 'Ebon', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-14bd@maxim-group.com', '$2y$10$/qiLNWXM6QQGrhiJxJZdZuZcC0F1wYvF4Fq4SdoPYD/QJWl20FB..', '01788873676', 'dWjuLqzpV6CfI27KIpYj0ipJoKubRHgtHqRgaTTlFemc2I37b4etJhobNL50', 1, 37, 0, '0', '2018-09-11 04:21:16', '2018-09-11 04:21:16'),
(65, 'Rifath', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-15bd@maxim-group.com', '$2y$10$FxPOnCHID0z/xZ2UivgsjOxNP2CbnfizyF4TC8vEqBDXRjC7Vykxq', '01731222204', NULL, 1, 37, 0, '0', '2018-09-11 04:22:27', '2018-09-19 23:03:58'),
(66, 'Sagor', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-16bd@maxim-group.com', '$2y$10$Sh5zn3slZkESlyzOJrmiJeL8R5/P1VEGiDeDL4tUgF8Q/KFASId9G', '01788873676', 'hNOIPwJ7m1qRqYbEkCnDcXymW1Xeb8n1zsvgydkjib9L8fklPqmEG2Jl7MSy', 1, 37, 0, '0', '2018-09-11 04:24:29', '2018-09-11 04:24:29'),
(67, 'Pabel', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-17bd@maxim-group.com', '$2y$10$MT2GhDL7C2IG.ynWRV35v./xneJtT9HfqWwy2d6t012hQhOoroLh.', '0179992 6476', NULL, 1, 37, 0, '0', '2018-09-11 04:26:12', '2018-09-11 04:26:12'),
(68, 'Rashid', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-18bd@maxim-group.com', '$2y$10$jheyjh8oDBjaOhndZSLbG.Ph84DKVB/zpOsjAjmB9vwVJnKacAJy6', '01850559011', NULL, 1, 37, 0, '0', '2018-09-11 04:29:13', '2018-09-11 04:29:13'),
(69, 'Saily', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-19bd@maxim-group.com', '$2y$10$qCdYlVIJt/go1OlwEO5s/u1EwNlc8i6sWBUugcwP72d5nSX2Wn1Fa', '01799926476', NULL, 1, 37, 0, '0', '2018-09-11 04:30:45', '2018-09-11 04:30:45'),
(70, 'Faruk', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-20bd@maxim-group.com', '$2y$10$8SC1LQxllJUlO.CYe9JIzeN32iJ6f0l.8N/mxfKZ0l8rAW9YsVUAW', '01531773905', NULL, 1, 37, 0, '0', '2018-09-11 04:31:32', '2018-09-11 04:31:32'),
(71, 'Aymon', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-21bd@maxim-group.com', '$2y$10$yW18325DMnI/UcTIYQplyuVtgan252Ykj6iU4oUDBvVZPPsRAJruO', '01884629529', NULL, 1, 37, 0, '0', '2018-09-11 04:33:05', '2018-09-11 04:33:05'),
(72, 'Bijoy', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-22bd@maxim-group.com', '$2y$10$Jl6LPTurDYjP4kyLZi3ly.TM74EFwaqeoISMkYWc686bHs/rQdnSu', '01711092406', NULL, 1, 37, 0, '0', '2018-09-11 04:34:12', '2018-09-11 04:34:12'),
(73, 'Rahat', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-23bd@maxim-group.com', '$2y$10$vbA/oD91.vsjyu.97khQRebZNkMpgBvOELc98pTmk.gcOboYQ94q6', '01911077887', 't2uX3hODhCot1LWWpXaeEXdAYHbY846fu3arjn6O16BsGSBdi2FgSNitfRGG', 1, 37, 0, '0', '2018-09-11 04:35:15', '2018-09-11 04:35:15'),
(74, 'Munmun', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-25bd@maxim-group.com', '$2y$10$FRRJKxvxT128tCSs/DpjoOs5HpQDJEWnpjqUSX2LewzJglATm..0W', '01729955480', NULL, 1, 37, 0, '0', '2018-09-11 04:37:15', '2018-09-11 04:37:15'),
(75, 'Zarin', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-26bd@maxim-group.com', '$2y$10$gYpQbfFUPPM5JVuoHqXEB.1/FAVduj2aC723KisjR.gQR2zlLV.2e', '01708681652', NULL, 1, 37, 0, '0', '2018-09-11 04:38:27', '2018-09-11 04:38:27'),
(76, 'Sharmin', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-27bd@maxim-group.com', '$2y$10$KFlUEFMSQgCJym6keTBoGeZvSGjx5GDqVMzFAdkNP0CSmQdEaVtSO', '01615-572572', NULL, 1, 37, 0, '0', '2018-09-11 04:39:27', '2018-09-11 04:39:27'),
(77, 'Parvej', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-28bd@maxim-group.com', '$2y$10$1My4nk7yQ90VFDe8dA2Iee4sdpe/Fw.q0kKSeiyNLJT8UflerJzpi', '01783862751', NULL, 1, 37, 0, '0', '2018-09-11 04:40:42', '2018-09-11 04:40:42'),
(78, 'Sarbojit', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-29bd@maxim-group.com', '$2y$10$uIFd4Agxrzg23ZD2JCJHNubxzIRWmRC4l1U7NvMKQhSZe7W0tWC3.', '0', NULL, 1, 37, 0, '0', '2018-09-11 04:41:33', '2018-09-19 22:38:47'),
(79, 'Akram', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-30bd@maxim-group.com', '$2y$10$uNBXDtmgupr3q5N5bCT5iO3TbyFi41UlaKA.r1.et12IVhK3zNQru', '01674293832', NULL, 1, 37, 0, '0', '2018-09-11 04:45:26', '2018-09-19 22:37:34'),
(80, 'Nowreen', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-31bd@maxim-group.com', '$2y$10$IIynmxptiGMEsEdOSNyyc.N5lWp3RH2QZP73XiwqpE5P0CzSmR4AK', '01763745496', NULL, 1, 37, 0, '0', '2018-09-11 04:46:18', '2018-09-11 04:46:18'),
(81, 'Nayim', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-33bd@maxim-group.com', '$2y$10$ULzK/qLd8vG/Vls5CqBf8OlBoWd1pWEd1Tuz3EoLEuHE7454ZdbHe', '01713154867', NULL, 1, 37, 0, '0', '2018-09-11 04:47:07', '2018-09-11 04:47:07'),
(82, 'Feroze', NULL, NULL, NULL, 'company_user', 49, 17, 'cs-11bd@maxim-group.com', '$2y$10$p5TZJ7g6.dGn.DoPysKhXeaxVJ.6ips08grpmzQFQMxMHSUG1oOii', '01670250963', 'mZaYLszHgQLqUY6KJwFYlj7UY98SwVXfXcvluu1Fcc4AHNfYn17531CqE9vH', 1, 39, 0, '0', '2018-09-13 00:47:26', '2018-09-24 23:32:52'),
(83, 'Sheefa', NULL, 'Haque', NULL, 'super_admin', 0, 0, 'cs-manager@maxim-group.com', '$2y$10$.JwEQcEC.OTXRG4aP/PsU.iomnby.5ndA35BeOVrh2Mb03x1LMlsS', '01790288907', 'QGygqVpV1Th0P9yLeVisd3dmEjMKjJE2fX2tMHTyqyDcf9568ru2ck3g844t', 1, 1, 0, '0', '2018-09-12 18:00:00', '2018-09-12 18:00:00'),
(84, 'management-1bd', NULL, NULL, NULL, 'company_user', 49, 17, 'management-1bd@maxim-group.com', '$2y$10$9MBsnIHtTYlnkPz6QZceQeOuGkz3Yy4ZaYXYE3u983mKzmOxI8Ik.', NULL, '81FOwi6CKW8mVQmWXsfpfaWRxgzRj5msy2y8CNBjX963afcKahBIWfnBnGGy', 1, 38, 0, '0', '2018-09-23 23:09:33', '2018-09-23 23:09:33');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_user_role_menu`
--

CREATE TABLE `mxp_user_role_menu` (
  `role_menu_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_user_role_menu`
--

INSERT INTO `mxp_user_role_menu` (`role_menu_id`, `role_id`, `menu_id`, `company_id`, `is_active`, `created_at`, `updated_at`) VALUES
(185, 1, 25, 0, 0, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(186, 1, 7, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(187, 1, 34, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(188, 1, 28, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(189, 1, 19, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(190, 1, 37, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(191, 1, 18, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(192, 1, 4, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(193, 1, 31, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(194, 1, 23, 0, 1, '2018-01-26 12:24:42', '2018-01-26 12:24:42'),
(195, 1, 3, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(196, 1, 24, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(197, 1, 27, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(198, 1, 36, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(199, 1, 35, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(200, 1, 13, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(201, 1, 30, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(202, 1, 6, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(203, 1, 10, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(204, 1, 16, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(205, 1, 9, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(206, 1, 8, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(207, 1, 12, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(208, 1, 5, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(209, 1, 26, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(210, 1, 11, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(211, 1, 29, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(212, 1, 22, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(213, 1, 33, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(214, 1, 21, 0, 1, '2018-01-26 12:24:43', '2018-01-26 12:24:43'),
(313, 21, 4, 0, 1, '2018-01-28 12:42:45', '2018-01-28 12:42:45'),
(314, 21, 31, 0, 1, '2018-01-28 12:42:46', '2018-01-28 12:42:46'),
(315, 21, 3, 0, 1, '2018-01-28 12:42:46', '2018-01-28 12:42:46'),
(316, 21, 24, 0, 1, '2018-01-28 12:42:46', '2018-01-28 12:42:46'),
(317, 21, 27, 0, 1, '2018-01-28 12:42:46', '2018-01-28 12:42:46'),
(318, 21, 5, 0, 1, '2018-01-28 12:42:46', '2018-01-28 12:42:46'),
(319, 21, 32, 0, 1, '2018-01-28 12:42:46', '2018-01-28 12:42:46'),
(349, 26, 34, 14, 1, '2018-01-30 09:00:07', '2018-01-30 09:00:07'),
(350, 26, 13, 14, 1, '2018-01-30 09:00:07', '2018-01-30 09:00:07'),
(351, 26, 6, 14, 1, '2018-01-30 09:00:07', '2018-01-30 09:00:07'),
(352, 26, 10, 14, 1, '2018-01-30 09:00:07', '2018-01-30 09:00:07'),
(353, 26, 16, 14, 1, '2018-01-30 09:00:08', '2018-01-30 09:00:08'),
(354, 26, 9, 14, 1, '2018-01-30 09:00:08', '2018-01-30 09:00:08'),
(355, 26, 8, 14, 1, '2018-01-30 09:00:08', '2018-01-30 09:00:08'),
(356, 26, 12, 14, 1, '2018-01-30 09:00:08', '2018-01-30 09:00:08'),
(357, 26, 11, 14, 1, '2018-01-30 09:00:08', '2018-01-30 09:00:08'),
(358, 25, 19, 13, 1, '2018-01-30 10:23:24', '2018-01-30 10:23:24'),
(359, 25, 37, 13, 1, '2018-01-30 10:23:24', '2018-01-30 10:23:24'),
(360, 25, 18, 13, 1, '2018-01-30 10:23:24', '2018-01-30 10:23:24'),
(361, 25, 5, 13, 1, '2018-01-30 10:23:24', '2018-01-30 10:23:24'),
(362, 25, 22, 13, 1, '2018-01-30 10:23:24', '2018-01-30 10:23:24'),
(363, 25, 33, 13, 1, '2018-01-30 10:23:25', '2018-01-30 10:23:25'),
(364, 25, 21, 13, 1, '2018-01-30 10:23:25', '2018-01-30 10:23:25'),
(365, 25, 20, 13, 1, '2018-01-30 10:23:25', '2018-01-30 10:23:25'),
(366, 1, 32, 0, 1, NULL, NULL),
(367, 1, 20, 0, 1, '2018-01-30 10:23:25', '2018-01-30 10:23:25'),
(401, 1, 38, 0, 1, NULL, NULL),
(402, 1, 39, 0, 1, NULL, NULL),
(403, 1, 40, 0, 1, NULL, NULL),
(404, 1, 41, 0, 1, NULL, NULL),
(405, 1, 42, 0, 1, NULL, NULL),
(406, 1, 43, 0, 1, NULL, NULL),
(407, 1, 44, 0, 1, NULL, NULL),
(414, 1, 52, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(415, 1, 53, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(416, 1, 54, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(417, 1, 55, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(418, 1, 56, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(419, 1, 54, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(420, 1, 57, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(421, 1, 58, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(422, 1, 59, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(423, 1, 60, 0, 1, '2018-01-31 06:00:00', '2018-01-31 06:00:00'),
(424, 1, 61, 0, 1, NULL, NULL),
(425, 1, 62, 0, 1, NULL, NULL),
(426, 1, 63, 0, 1, NULL, NULL),
(427, 1, 64, 0, 1, NULL, NULL),
(428, 1, 65, 0, 1, NULL, NULL),
(429, 1, 66, 0, 1, NULL, NULL),
(430, 1, 67, 0, 1, NULL, NULL),
(431, 1, 68, 0, 1, NULL, NULL),
(432, 1, 69, 0, 1, NULL, NULL),
(433, 1, 70, 0, 1, NULL, NULL),
(434, 1, 71, 0, 1, NULL, NULL),
(435, 1, 72, 0, 1, NULL, NULL),
(482, 1, 73, 0, 1, NULL, NULL),
(486, 1, 77, 0, 1, NULL, NULL),
(487, 1, 78, 0, 1, NULL, NULL),
(488, 1, 79, 0, 1, NULL, NULL),
(489, 1, 80, 0, 1, NULL, NULL),
(490, 1, 81, 0, 1, NULL, NULL),
(491, 1, 82, 0, 1, NULL, NULL),
(492, 1, 83, 0, 1, NULL, NULL),
(493, 1, 88, 0, 1, NULL, NULL),
(494, 1, 89, 0, 1, NULL, NULL),
(495, 1, 90, 0, 1, NULL, NULL),
(496, 1, 91, 0, 1, NULL, NULL),
(497, 1, 92, 0, 1, NULL, NULL),
(498, 1, 84, 0, 1, NULL, NULL),
(499, 1, 93, 0, 1, NULL, NULL),
(500, 1, 94, 0, 1, NULL, NULL),
(501, 1, 95, 0, 1, NULL, NULL),
(502, 1, 96, 0, 1, NULL, NULL),
(503, 1, 97, 0, 1, NULL, NULL),
(504, 1, 98, 0, 1, NULL, NULL),
(505, 1, 99, 0, 1, NULL, NULL),
(506, 1, 100, 0, 1, NULL, NULL),
(507, 1, 101, 0, 1, NULL, NULL),
(508, 1, 102, 0, 1, NULL, NULL),
(509, 27, 102, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(510, 27, 98, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(511, 27, 43, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(512, 27, 25, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(513, 27, 57, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(514, 27, 90, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(515, 27, 67, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(516, 27, 56, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(517, 27, 54, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(518, 27, 53, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(519, 27, 44, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(520, 27, 41, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(521, 27, 40, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(522, 27, 89, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(523, 27, 68, 10, 1, '2018-04-02 00:40:56', '2018-04-02 00:40:56'),
(524, 27, 71, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(525, 27, 70, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(526, 27, 28, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(527, 27, 19, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(528, 27, 72, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(529, 27, 69, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(530, 27, 18, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(531, 27, 4, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(532, 27, 58, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(533, 27, 66, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(534, 27, 31, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(535, 27, 63, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(536, 27, 91, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(537, 27, 99, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(538, 27, 101, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(539, 27, 3, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(540, 27, 100, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(541, 27, 73, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(542, 27, 24, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(543, 27, 27, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(544, 27, 30, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(545, 27, 38, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(546, 27, 74, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(547, 27, 76, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(548, 27, 52, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(549, 27, 42, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(550, 27, 55, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(551, 27, 75, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(552, 27, 92, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(553, 27, 6, 10, 1, '2018-04-02 00:40:57', '2018-04-02 00:40:57'),
(554, 27, 8, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(555, 27, 93, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(556, 27, 5, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(557, 27, 83, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(558, 27, 79, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(559, 27, 82, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(560, 27, 81, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(561, 27, 96, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(562, 27, 97, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(563, 27, 84, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(564, 27, 77, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(565, 27, 78, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(566, 27, 80, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(567, 27, 26, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(568, 27, 60, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(569, 27, 65, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(570, 27, 95, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(571, 27, 29, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(572, 27, 62, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(573, 27, 33, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(574, 27, 39, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(575, 27, 64, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(576, 27, 94, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(577, 27, 61, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(578, 27, 32, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(579, 27, 20, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(580, 27, 59, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(581, 27, 88, 10, 1, '2018-04-02 00:40:58', '2018-04-02 00:40:58'),
(728, 20, 102, 10, 1, '2018-04-02 00:51:20', '2018-04-02 00:51:20'),
(729, 20, 98, 10, 1, '2018-04-02 00:51:20', '2018-04-02 00:51:20'),
(730, 20, 43, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(731, 20, 25, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(732, 20, 57, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(733, 20, 90, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(734, 20, 67, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(735, 20, 56, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(736, 20, 54, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(737, 20, 53, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(738, 20, 44, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(739, 20, 41, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(740, 20, 40, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(741, 20, 89, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(742, 20, 68, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(743, 20, 71, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(744, 20, 70, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(745, 20, 28, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(746, 20, 19, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(747, 20, 72, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(748, 20, 69, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(749, 20, 18, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(750, 20, 4, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(751, 20, 58, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(752, 20, 66, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(753, 20, 31, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(754, 20, 63, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(755, 20, 91, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(756, 20, 99, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(757, 20, 101, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(758, 20, 3, 10, 1, '2018-04-02 00:51:21', '2018-04-02 00:51:21'),
(759, 20, 100, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(760, 20, 73, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(761, 20, 24, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(762, 20, 27, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(763, 20, 30, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(764, 20, 38, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(765, 20, 74, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(766, 20, 76, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(767, 20, 52, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(768, 20, 42, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(769, 20, 55, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(770, 20, 75, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(771, 20, 92, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(772, 20, 6, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(773, 20, 8, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(774, 20, 93, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(775, 20, 5, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(776, 20, 83, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(777, 20, 79, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(778, 20, 82, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(779, 20, 81, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(780, 20, 96, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(781, 20, 97, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(782, 20, 84, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(783, 20, 77, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(784, 20, 78, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(785, 20, 80, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(786, 20, 26, 10, 1, '2018-04-02 00:51:22', '2018-04-02 00:51:22'),
(787, 20, 60, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(788, 20, 65, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(789, 20, 95, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(790, 20, 29, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(791, 20, 62, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(792, 20, 33, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(793, 20, 39, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(794, 20, 64, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(795, 20, 94, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(796, 20, 61, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(797, 20, 32, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(798, 20, 20, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(799, 20, 59, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(800, 20, 88, 10, 1, '2018-04-02 00:51:23', '2018-04-02 00:51:23'),
(801, 1, 103, 0, 1, NULL, NULL),
(802, 1, 104, 0, 1, NULL, NULL),
(805, 1, 105, 0, 1, NULL, NULL),
(806, 1, 105, 0, 1, NULL, NULL),
(807, 1, 105, 0, 1, NULL, NULL),
(808, 1, 105, 0, 1, NULL, NULL),
(809, 1, 105, 0, 1, NULL, NULL),
(810, 1, 105, 0, 1, NULL, NULL),
(811, 1, 106, 0, 1, NULL, NULL),
(812, 1, 107, 0, 1, NULL, NULL),
(813, 1, 108, 0, 1, NULL, NULL),
(814, 1, 109, 0, 1, NULL, NULL),
(815, 1, 110, 0, 1, NULL, NULL),
(816, 1, 111, 0, 1, NULL, NULL),
(817, 1, 112, 0, 1, NULL, NULL),
(818, 1, 113, 0, 1, NULL, NULL),
(819, 1, 114, 0, 1, NULL, NULL),
(820, 1, 115, 0, 1, NULL, NULL),
(821, 1, 116, 0, 1, NULL, NULL),
(822, 1, 116, 0, 1, NULL, NULL),
(823, 1, 118, 0, 1, NULL, NULL),
(824, 1, 119, 0, 1, NULL, NULL),
(825, 1, 120, 0, 1, NULL, NULL),
(826, 1, 121, 0, 1, NULL, NULL),
(827, 1, 122, 0, 1, NULL, NULL),
(828, 1, 123, 0, 1, NULL, NULL),
(829, 1, 124, 0, 1, NULL, NULL),
(830, 1, 125, 0, 1, NULL, NULL),
(831, 1, 126, 0, 1, NULL, NULL),
(832, 1, 127, 0, 1, NULL, NULL),
(833, 1, 128, 0, 1, NULL, NULL),
(834, 1, 129, 0, 1, NULL, NULL),
(835, 1, 130, 0, 1, NULL, NULL),
(836, 1, 131, 0, 1, NULL, NULL),
(837, 1, 132, 0, 1, NULL, NULL),
(838, 1, 133, 0, 1, NULL, NULL),
(839, 1, 134, 0, 1, NULL, NULL),
(840, 1, 135, 0, 1, NULL, NULL),
(841, 1, 136, 0, 1, NULL, NULL),
(842, 1, 137, 0, 1, NULL, NULL),
(843, 1, 138, 0, 1, NULL, NULL),
(844, 29, 25, 10, 1, '2018-04-09 01:57:55', '2018-04-09 01:57:55'),
(845, 29, 67, 10, 1, '2018-04-09 01:57:56', '2018-04-09 01:57:56'),
(846, 29, 68, 10, 1, '2018-04-09 01:57:56', '2018-04-09 01:57:56'),
(847, 29, 71, 10, 1, '2018-04-09 01:57:56', '2018-04-09 01:57:56'),
(848, 29, 70, 10, 1, '2018-04-09 01:57:56', '2018-04-09 01:57:56'),
(849, 29, 28, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(850, 29, 19, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(851, 29, 72, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(852, 29, 69, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(853, 29, 18, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(854, 29, 4, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(855, 29, 31, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(856, 29, 3, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(857, 29, 24, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(858, 29, 27, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(859, 29, 30, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(860, 29, 38, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(861, 29, 6, 10, 1, '2018-04-09 01:57:57', '2018-04-09 01:57:57'),
(862, 29, 8, 10, 1, '2018-04-09 01:57:58', '2018-04-09 01:57:58'),
(863, 29, 5, 10, 1, '2018-04-09 01:57:58', '2018-04-09 01:57:58'),
(864, 29, 26, 10, 1, '2018-04-09 01:57:58', '2018-04-09 01:57:58'),
(865, 29, 29, 10, 1, '2018-04-09 01:57:58', '2018-04-09 01:57:58'),
(866, 29, 33, 10, 1, '2018-04-09 01:57:58', '2018-04-09 01:57:58'),
(867, 29, 32, 10, 1, '2018-04-09 01:57:58', '2018-04-09 01:57:58'),
(868, 29, 20, 10, 1, '2018-04-09 01:57:58', '2018-04-09 01:57:58'),
(869, 1, 73, 0, 1, NULL, NULL),
(870, 1, 74, 0, 1, NULL, NULL),
(871, 1, 75, 0, 1, NULL, NULL),
(872, 1, 76, 0, 1, NULL, NULL),
(885, 1, 85, 0, 1, NULL, NULL),
(886, 1, 86, 0, 1, NULL, NULL),
(887, 1, 87, 0, 1, NULL, NULL),
(1023, 31, 90, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1024, 31, 4, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1025, 31, 38, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1026, 31, 88, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1027, 31, 91, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1028, 31, 87, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1029, 31, 92, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1030, 31, 94, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1031, 31, 75, 18, 1, '2018-05-10 02:03:27', '2018-05-10 02:03:27'),
(1032, 31, 93, 18, 1, '2018-05-10 02:03:28', '2018-05-10 02:03:28'),
(1033, 31, 83, 18, 1, '2018-05-10 02:03:28', '2018-05-10 02:03:28'),
(1034, 31, 85, 18, 1, '2018-05-10 02:03:28', '2018-05-10 02:03:28'),
(1035, 31, 84, 18, 1, '2018-05-10 02:03:28', '2018-05-10 02:03:28'),
(1036, 31, 89, 18, 1, '2018-05-10 02:03:28', '2018-05-10 02:03:28'),
(1037, 31, 86, 18, 1, '2018-05-10 02:03:28', '2018-05-10 02:03:28'),
(1079, 36, 68, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1080, 36, 71, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1081, 36, 70, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1082, 36, 19, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1083, 36, 72, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1084, 36, 69, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1085, 36, 18, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1086, 36, 5, 19, 1, '2018-09-06 04:15:29', '2018-09-06 04:15:29'),
(1704, 1, 168, 0, 1, NULL, NULL),
(1705, 1, 170, 0, 1, NULL, NULL),
(1757, 1, 166, 0, 1, NULL, NULL),
(2478, 37, 90, 17, 1, '2018-09-25 00:22:42', '2018-09-25 00:22:42'),
(2479, 37, 98, 17, 1, '2018-09-25 00:22:42', '2018-09-25 00:22:42'),
(2480, 37, 142, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2481, 37, 70, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2482, 37, 92, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2483, 37, 100, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2484, 37, 165, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2485, 37, 4, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2486, 37, 148, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2487, 37, 141, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2488, 37, 135, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2489, 37, 136, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2490, 37, 167, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2491, 37, 169, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2492, 37, 97, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2493, 37, 147, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2494, 37, 143, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2495, 37, 144, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2496, 37, 149, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2497, 37, 168, 17, 1, '2018-09-25 00:22:43', '2018-09-25 00:22:43'),
(2498, 37, 5, 17, 1, '2018-09-25 00:22:44', '2018-09-25 00:22:44'),
(2499, 37, 166, 17, 1, '2018-09-25 00:22:44', '2018-09-25 00:22:44'),
(2500, 37, 91, 17, 1, '2018-09-25 00:22:44', '2018-09-25 00:22:44'),
(2501, 37, 94, 17, 1, '2018-09-25 00:22:44', '2018-09-25 00:22:44'),
(2502, 37, 75, 17, 1, '2018-09-25 00:22:44', '2018-09-25 00:22:44'),
(2503, 37, 93, 17, 1, '2018-09-25 00:22:44', '2018-09-25 00:22:44'),
(2504, 1, 171, 0, 1, '2018-09-24 18:00:00', '2018-09-24 18:00:00'),
(2566, 1, 177, 0, 1, '2018-09-24 18:00:00', NULL),
(2634, 39, 90, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2635, 39, 98, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2636, 39, 142, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2637, 39, 125, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2638, 39, 126, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2639, 39, 129, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2640, 39, 128, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2641, 39, 127, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2642, 39, 179, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2643, 39, 178, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2644, 39, 182, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2645, 39, 177, 17, 1, '2018-09-26 00:38:38', '2018-09-26 00:38:38'),
(2646, 39, 181, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2647, 39, 180, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2648, 39, 92, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2649, 39, 100, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2650, 39, 69, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2651, 39, 4, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2652, 39, 141, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2653, 39, 137, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2654, 39, 135, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2655, 39, 139, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2656, 39, 138, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2657, 39, 136, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2658, 39, 96, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2659, 39, 76, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2660, 39, 115, 17, 1, '2018-09-26 00:38:39', '2018-09-26 00:38:39'),
(2661, 39, 114, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2662, 39, 116, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2663, 39, 173, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2664, 39, 172, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2665, 39, 176, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2666, 39, 171, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2667, 39, 175, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2668, 39, 174, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2669, 39, 130, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2670, 39, 131, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2671, 39, 133, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2672, 39, 132, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2673, 39, 134, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2674, 39, 88, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2675, 39, 118, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2676, 39, 117, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2677, 39, 95, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2678, 39, 167, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2679, 39, 169, 17, 1, '2018-09-26 00:38:40', '2018-09-26 00:38:40'),
(2680, 39, 38, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2681, 39, 97, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2682, 39, 147, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2683, 39, 143, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2684, 39, 144, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2685, 39, 149, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2686, 39, 168, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2687, 39, 124, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2688, 39, 104, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2689, 39, 166, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2690, 39, 109, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2691, 39, 113, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2692, 39, 112, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2693, 39, 111, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2694, 39, 78, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2695, 39, 110, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2696, 39, 91, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2697, 39, 87, 17, 1, '2018-09-26 00:38:41', '2018-09-26 00:38:41'),
(2698, 39, 75, 17, 1, '2018-09-26 00:38:42', '2018-09-26 00:38:42'),
(2699, 39, 93, 17, 1, '2018-09-26 00:38:42', '2018-09-26 00:38:42'),
(2714, 38, 98, 17, 1, '2018-10-08 05:16:53', '2018-10-08 05:16:53'),
(2715, 38, 4, 17, 1, '2018-10-08 05:16:53', '2018-10-08 05:16:53'),
(2716, 38, 148, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2717, 38, 141, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2718, 38, 183, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2719, 38, 184, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2720, 38, 99, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2721, 38, 97, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2722, 38, 147, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2723, 38, 143, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2724, 38, 145, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2725, 38, 146, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2726, 38, 144, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2727, 38, 170, 17, 1, '2018-10-08 05:16:54', '2018-10-08 05:16:54'),
(2728, 38, 140, 17, 1, '2018-10-08 05:16:55', '2018-10-08 05:16:55');

-- --------------------------------------------------------

--
-- Table structure for table `mxp_vendor_prices`
--

CREATE TABLE `mxp_vendor_prices` (
  `price_id` int(10) UNSIGNED NOT NULL,
  `party_table_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `vendor_com_price` double(8,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mxp_vendor_prices`
--

INSERT INTO `mxp_vendor_prices` (`price_id`, `party_table_id`, `product_id`, `vendor_com_price`, `created_at`, `updated_at`) VALUES
(1, 224, 4, 0.00045345, '2018-10-07 00:43:09', '2018-10-07 00:47:48'),
(2, 225, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:44:11'),
(3, 226, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(4, 227, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(5, 228, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(6, 229, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(7, 230, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(8, 231, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(9, 232, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(10, 233, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(11, 234, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09'),
(12, 235, 4, NULL, '2018-10-07 00:43:09', '2018-10-07 00:43:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_delete` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `name`, `phone`, `address`, `is_delete`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MyName', 'Contact', 'Address', 1, 1, '2018-07-21 01:58:35', '2018-08-30 06:38:05'),
(2, 'new Supllier', '01743654654', 'Address', 1, 1, '2018-08-07 00:25:31', '2018-09-27 00:51:46'),
(3, 'Name', '1715627724', 'village: kalikabari danggapara, Union : Boro Chondipur (5), Post :Havra', 1, 1, '2018-08-30 02:16:20', '2018-09-27 00:51:48'),
(4, 'maximum', '1715627724', 'village: kalikabari danggapara, Union : Boro Chondipur (5), Post :Havra', 1, 1, '2018-09-05 11:58:37', '2018-09-27 00:51:49'),
(5, 'Shohidur Rahman', '1715627724', 'village: kalikabari danggapara, Union : Boro Chondipur (5), Post :Havra', 0, 1, '2018-10-04 05:12:48', '2018-10-04 05:12:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_files`
--
ALTER TABLE `booking_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_accounts_heads`
--
ALTER TABLE `mxp_accounts_heads`
  ADD PRIMARY KEY (`accounts_heads_id`);

--
-- Indexes for table `mxp_accounts_sub_heads`
--
ALTER TABLE `mxp_accounts_sub_heads`
  ADD PRIMARY KEY (`accounts_sub_heads_id`);

--
-- Indexes for table `mxp_acc_classes`
--
ALTER TABLE `mxp_acc_classes`
  ADD PRIMARY KEY (`mxp_acc_classes_id`);

--
-- Indexes for table `mxp_booking`
--
ALTER TABLE `mxp_booking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_bookingbuyer_details`
--
ALTER TABLE `mxp_bookingbuyer_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_booking_challan`
--
ALTER TABLE `mxp_booking_challan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_brand`
--
ALTER TABLE `mxp_brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `mxp_buyer`
--
ALTER TABLE `mxp_buyer`
  ADD PRIMARY KEY (`id_mxp_buyer`);

--
-- Indexes for table `mxp_challan`
--
ALTER TABLE `mxp_challan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_companies`
--
ALTER TABLE `mxp_companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_gmts_color`
--
ALTER TABLE `mxp_gmts_color`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_header`
--
ALTER TABLE `mxp_header`
  ADD PRIMARY KEY (`header_id`);

--
-- Indexes for table `mxp_ipo`
--
ALTER TABLE `mxp_ipo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_items_details_by_booking_challan`
--
ALTER TABLE `mxp_items_details_by_booking_challan`
  ADD PRIMARY KEY (`items_details_id`);

--
-- Indexes for table `mxp_item_cost_price`
--
ALTER TABLE `mxp_item_cost_price`
  ADD PRIMARY KEY (`cost_price_id`);

--
-- Indexes for table `mxp_item_description`
--
ALTER TABLE `mxp_item_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_languages`
--
ALTER TABLE `mxp_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_maximbill`
--
ALTER TABLE `mxp_maximbill`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_menu`
--
ALTER TABLE `mxp_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `mxp_mrf_table`
--
ALTER TABLE `mxp_mrf_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_multiplechallan`
--
ALTER TABLE `mxp_multiplechallan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_order`
--
ALTER TABLE `mxp_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_order_input`
--
ALTER TABLE `mxp_order_input`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_pagefooter`
--
ALTER TABLE `mxp_pagefooter`
  ADD PRIMARY KEY (`footer_id`);

--
-- Indexes for table `mxp_pageheader`
--
ALTER TABLE `mxp_pageheader`
  ADD PRIMARY KEY (`header_id`);

--
-- Indexes for table `mxp_party`
--
ALTER TABLE `mxp_party`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_pi`
--
ALTER TABLE `mxp_pi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_product`
--
ALTER TABLE `mxp_product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `mxp_productsize`
--
ALTER TABLE `mxp_productsize`
  ADD PRIMARY KEY (`proSize_id`);

--
-- Indexes for table `mxp_products_colors`
--
ALTER TABLE `mxp_products_colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_products_sizes`
--
ALTER TABLE `mxp_products_sizes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_purchase_orders`
--
ALTER TABLE `mxp_purchase_orders`
  ADD PRIMARY KEY (`po_id`);

--
-- Indexes for table `mxp_reportfooter`
--
ALTER TABLE `mxp_reportfooter`
  ADD PRIMARY KEY (`re_footer_id`);

--
-- Indexes for table `mxp_role`
--
ALTER TABLE `mxp_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mxp_supplier_prices`
--
ALTER TABLE `mxp_supplier_prices`
  ADD PRIMARY KEY (`supplier_price_id`);

--
-- Indexes for table `mxp_task`
--
ALTER TABLE `mxp_task`
  ADD PRIMARY KEY (`id_mxp_task`);

--
-- Indexes for table `mxp_task_role`
--
ALTER TABLE `mxp_task_role`
  ADD PRIMARY KEY (`id_mxp_task_role`);

--
-- Indexes for table `mxp_translations`
--
ALTER TABLE `mxp_translations`
  ADD PRIMARY KEY (`translation_id`);

--
-- Indexes for table `mxp_translation_keys`
--
ALTER TABLE `mxp_translation_keys`
  ADD PRIMARY KEY (`translation_key_id`);

--
-- Indexes for table `mxp_userbuyer`
--
ALTER TABLE `mxp_userbuyer`
  ADD PRIMARY KEY (`id_userbuyer`);

--
-- Indexes for table `mxp_users`
--
ALTER TABLE `mxp_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `mxp_user_role_menu`
--
ALTER TABLE `mxp_user_role_menu`
  ADD PRIMARY KEY (`role_menu_id`);

--
-- Indexes for table `mxp_vendor_prices`
--
ALTER TABLE `mxp_vendor_prices`
  ADD PRIMARY KEY (`price_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_files`
--
ALTER TABLE `booking_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=288;

--
-- AUTO_INCREMENT for table `mxp_accounts_heads`
--
ALTER TABLE `mxp_accounts_heads`
  MODIFY `accounts_heads_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mxp_accounts_sub_heads`
--
ALTER TABLE `mxp_accounts_sub_heads`
  MODIFY `accounts_sub_heads_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mxp_acc_classes`
--
ALTER TABLE `mxp_acc_classes`
  MODIFY `mxp_acc_classes_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mxp_booking`
--
ALTER TABLE `mxp_booking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mxp_bookingbuyer_details`
--
ALTER TABLE `mxp_bookingbuyer_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mxp_booking_challan`
--
ALTER TABLE `mxp_booking_challan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mxp_brand`
--
ALTER TABLE `mxp_brand`
  MODIFY `brand_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_buyer`
--
ALTER TABLE `mxp_buyer`
  MODIFY `id_mxp_buyer` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `mxp_challan`
--
ALTER TABLE `mxp_challan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_companies`
--
ALTER TABLE `mxp_companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `mxp_gmts_color`
--
ALTER TABLE `mxp_gmts_color`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `mxp_header`
--
ALTER TABLE `mxp_header`
  MODIFY `header_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mxp_ipo`
--
ALTER TABLE `mxp_ipo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mxp_items_details_by_booking_challan`
--
ALTER TABLE `mxp_items_details_by_booking_challan`
  MODIFY `items_details_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mxp_item_cost_price`
--
ALTER TABLE `mxp_item_cost_price`
  MODIFY `cost_price_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mxp_item_description`
--
ALTER TABLE `mxp_item_description`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `mxp_languages`
--
ALTER TABLE `mxp_languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mxp_maximbill`
--
ALTER TABLE `mxp_maximbill`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_menu`
--
ALTER TABLE `mxp_menu`
  MODIFY `menu_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `mxp_mrf_table`
--
ALTER TABLE `mxp_mrf_table`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mxp_multiplechallan`
--
ALTER TABLE `mxp_multiplechallan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_order`
--
ALTER TABLE `mxp_order`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_order_input`
--
ALTER TABLE `mxp_order_input`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_pagefooter`
--
ALTER TABLE `mxp_pagefooter`
  MODIFY `footer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_pageheader`
--
ALTER TABLE `mxp_pageheader`
  MODIFY `header_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_party`
--
ALTER TABLE `mxp_party`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `mxp_pi`
--
ALTER TABLE `mxp_pi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mxp_product`
--
ALTER TABLE `mxp_product`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `mxp_productsize`
--
ALTER TABLE `mxp_productsize`
  MODIFY `proSize_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `mxp_products_colors`
--
ALTER TABLE `mxp_products_colors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `mxp_products_sizes`
--
ALTER TABLE `mxp_products_sizes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `mxp_purchase_orders`
--
ALTER TABLE `mxp_purchase_orders`
  MODIFY `po_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mxp_reportfooter`
--
ALTER TABLE `mxp_reportfooter`
  MODIFY `re_footer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mxp_role`
--
ALTER TABLE `mxp_role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `mxp_supplier_prices`
--
ALTER TABLE `mxp_supplier_prices`
  MODIFY `supplier_price_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `mxp_task`
--
ALTER TABLE `mxp_task`
  MODIFY `id_mxp_task` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mxp_task_role`
--
ALTER TABLE `mxp_task_role`
  MODIFY `id_mxp_task_role` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mxp_translations`
--
ALTER TABLE `mxp_translations`
  MODIFY `translation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=629;

--
-- AUTO_INCREMENT for table `mxp_translation_keys`
--
ALTER TABLE `mxp_translation_keys`
  MODIFY `translation_key_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- AUTO_INCREMENT for table `mxp_userbuyer`
--
ALTER TABLE `mxp_userbuyer`
  MODIFY `id_userbuyer` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `mxp_users`
--
ALTER TABLE `mxp_users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `mxp_user_role_menu`
--
ALTER TABLE `mxp_user_role_menu`
  MODIFY `role_menu_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2729;

--
-- AUTO_INCREMENT for table `mxp_vendor_prices`
--
ALTER TABLE `mxp_vendor_prices`
  MODIFY `price_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
