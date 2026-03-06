-- Pharmacy Management System Database Backup for Migration
-- Generated: 2026-03-05 10:55:02
-- Database: pharmacy_db

CREATE DATABASE IF NOT EXISTS pharmacy_db;
USE pharmacy_db;

SET FOREIGN_KEY_CHECKS=0;



-- Table structure for `activity_logs`
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_action` (`user_id`,`action`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `activity_logs`
INSERT INTO `activity_logs` VALUES ('1', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-03 19:45:57');
INSERT INTO `activity_logs` VALUES ('2', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-03 20:11:25');
INSERT INTO `activity_logs` VALUES ('3', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-03 20:51:25');
INSERT INTO `activity_logs` VALUES ('4', '5', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-03 20:51:32');
INSERT INTO `activity_logs` VALUES ('5', '5', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-03 20:51:42');
INSERT INTO `activity_logs` VALUES ('6', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-03 20:51:53');
INSERT INTO `activity_logs` VALUES ('7', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-03 21:08:58');
INSERT INTO `activity_logs` VALUES ('8', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-03 21:14:11');
INSERT INTO `activity_logs` VALUES ('9', '3', 'Database Backup Created', NULL, NULL, 'Backup file: pharmacy_backup_2026-03-03_18-30-13.sql', '::1', '2026-03-03 22:30:13');
INSERT INTO `activity_logs` VALUES ('10', '3', 'Sale Created', 'sales', '6', 'Invoice: INV-20260303-8849, Total: Rs 800.00, Items: 1', '::1', '2026-03-03 22:45:48');
INSERT INTO `activity_logs` VALUES ('11', '3', 'Sale Created', 'sales', '7', 'Invoice: INV-20260303-9187, Total: Rs 50.00, Items: 1', '::1', '2026-03-03 22:57:39');
INSERT INTO `activity_logs` VALUES ('12', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 05:14:47');
INSERT INTO `activity_logs` VALUES ('13', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 05:21:41');
INSERT INTO `activity_logs` VALUES ('14', '3', 'Sale Created', 'sales', '8', 'Invoice: INV-20260304-2527, Total: Rs 7,450.00, Items: 1', '::1', '2026-03-04 05:25:17');
INSERT INTO `activity_logs` VALUES ('15', '3', 'Sale Created', 'sales', '9', 'Invoice: INV-20260304-6204, Total: Rs 12,700.00, Items: 1', '::1', '2026-03-04 12:00:27');
INSERT INTO `activity_logs` VALUES ('16', '3', 'Sale Created', 'sales', '10', 'Invoice: INV-20260304-8801, Total: Rs 1,200.00, Items: 1', '::1', '2026-03-04 12:02:24');
INSERT INTO `activity_logs` VALUES ('17', '3', 'Sale Created', 'sales', '11', 'Invoice: INV-20260304-6703, Total: Rs 100,000.00, Items: 1', '::1', '2026-03-04 15:09:22');
INSERT INTO `activity_logs` VALUES ('18', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-04 15:12:12');
INSERT INTO `activity_logs` VALUES ('19', '5', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 15:12:20');
INSERT INTO `activity_logs` VALUES ('20', '5', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-04 15:13:07');
INSERT INTO `activity_logs` VALUES ('21', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 15:13:22');
INSERT INTO `activity_logs` VALUES ('22', '3', 'Medicine Updated', 'medicines', '6', 'Updated medicine: dr rasheel face wash2 (ID: 6), Quantity: 250', '::1', '2026-03-04 18:49:55');
INSERT INTO `activity_logs` VALUES ('23', '3', 'Medicine Updated', 'medicines', '6', 'Updated medicine: dr rasheel face wash2 (ID: 6), Quantity: 250', '::1', '2026-03-04 18:49:59');
INSERT INTO `activity_logs` VALUES ('24', '3', 'Medicine Updated', 'medicines', '6', 'Updated medicine: dr rasheel face wash2 (ID: 6), Quantity: 250', '::1', '2026-03-04 18:50:10');
INSERT INTO `activity_logs` VALUES ('25', '3', 'Medicine Updated', 'medicines', '6', 'Updated medicine: dr rasheel face wash2 (ID: 6), Quantity: 250', '::1', '2026-03-04 18:50:25');
INSERT INTO `activity_logs` VALUES ('26', '3', 'Medicine Updated', 'medicines', '3', 'Updated medicine: dr rasheel face wash (ID: 3), Quantity: 250', '::1', '2026-03-04 18:50:44');
INSERT INTO `activity_logs` VALUES ('27', '3', 'Medicine Updated', 'medicines', '3', 'Updated medicine: dr rasheel face wash (ID: 3), Quantity: 250', '::1', '2026-03-04 18:50:46');
INSERT INTO `activity_logs` VALUES ('28', '3', 'Medicine Updated', 'medicines', '2', 'Updated medicine: terbisil (ID: 2), Quantity: 0', '::1', '2026-03-04 18:51:18');
INSERT INTO `activity_logs` VALUES ('29', '3', 'Medicine Updated', 'medicines', '2', 'Updated medicine: terbisil (ID: 2), Quantity: 250', '::1', '2026-03-04 18:51:28');
INSERT INTO `activity_logs` VALUES ('30', '3', 'Medicine Updated', 'medicines', '2', 'Updated medicine: terbisil (ID: 2), Quantity: 250', '::1', '2026-03-04 18:51:31');
INSERT INTO `activity_logs` VALUES ('31', '3', 'Medicine Updated', 'medicines', '1', 'Updated medicine: youkan (ID: 1), Quantity: 50', '::1', '2026-03-04 18:51:51');
INSERT INTO `activity_logs` VALUES ('32', '3', 'Sale Created', 'sales', '12', 'Invoice: INV-20260304-2880, Total: Rs 16,500.00, Items: 1', '::1', '2026-03-04 19:15:10');
INSERT INTO `activity_logs` VALUES ('33', '3', 'Sale Created', 'sales', '13', 'Invoice: INV-20260304-1265, Total: Rs 10,000.00, Items: 1', '::1', '2026-03-04 19:16:12');
INSERT INTO `activity_logs` VALUES ('34', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 20:27:32');
INSERT INTO `activity_logs` VALUES ('35', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-04 20:37:03');
INSERT INTO `activity_logs` VALUES ('36', '5', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 20:37:16');
INSERT INTO `activity_logs` VALUES ('37', '5', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-04 20:38:28');
INSERT INTO `activity_logs` VALUES ('38', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 20:38:46');
INSERT INTO `activity_logs` VALUES ('39', '3', 'Database Backup Created', NULL, NULL, 'Backup file: pharmacy_backup_2026-03-04_16-39-02.sql', '::1', '2026-03-04 20:39:02');
INSERT INTO `activity_logs` VALUES ('40', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-04 21:18:26');
INSERT INTO `activity_logs` VALUES ('41', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 21:18:39');
INSERT INTO `activity_logs` VALUES ('42', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-04 21:19:28');
INSERT INTO `activity_logs` VALUES ('43', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 22:09:36');
INSERT INTO `activity_logs` VALUES ('44', '3', 'Database Backup Created', NULL, NULL, 'Backup file: pharmacy_backup_2026-03-04_19-12-15.sql', '::1', '2026-03-04 23:12:15');
INSERT INTO `activity_logs` VALUES ('45', '3', 'Backup Downloaded', NULL, NULL, 'Downloaded file: pharmacy_backup_2026-03-04_19-12-15.sql', '::1', '2026-03-04 23:12:21');
INSERT INTO `activity_logs` VALUES ('46', '5', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 23:52:10');
INSERT INTO `activity_logs` VALUES ('47', '5', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 23:52:10');
INSERT INTO `activity_logs` VALUES ('48', '5', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-04 23:52:26');
INSERT INTO `activity_logs` VALUES ('49', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 23:52:38');
INSERT INTO `activity_logs` VALUES ('50', '3', 'Sale Created', 'sales', '14', 'Invoice: INV-20260304-3470, Total: Rs 3,150.00, Items: 2', '::1', '2026-03-04 23:54:31');
INSERT INTO `activity_logs` VALUES ('51', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-04 23:57:54');
INSERT INTO `activity_logs` VALUES ('52', '3', 'Database Backup Created', NULL, NULL, 'Backup file: pharmacy_backup_2026-03-04_20-04-44.sql', '::1', '2026-03-05 00:04:44');
INSERT INTO `activity_logs` VALUES ('53', '3', 'Backup Downloaded', NULL, NULL, 'Downloaded file: pharmacy_backup_2026-03-04_20-04-44.sql', '::1', '2026-03-05 00:04:54');
INSERT INTO `activity_logs` VALUES ('54', '3', 'Sale Created', 'sales', '15', 'Invoice: INV-20260304-3111, Total: Rs 22,400.00, Items: 2', '::1', '2026-03-05 00:07:10');
INSERT INTO `activity_logs` VALUES ('55', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-05 00:30:11');
INSERT INTO `activity_logs` VALUES ('56', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-05 08:53:49');
INSERT INTO `activity_logs` VALUES ('57', '3', 'logout', NULL, NULL, 'User logged out', '::1', '2026-03-05 10:33:45');
INSERT INTO `activity_logs` VALUES ('58', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-05 10:35:42');
INSERT INTO `activity_logs` VALUES ('59', '3', 'login', NULL, NULL, 'User logged in', '::1', '2026-03-05 12:32:06');
INSERT INTO `activity_logs` VALUES ('60', '3', 'Settings Updated', NULL, NULL, 'Store settings updated', '::1', '2026-03-05 12:40:17');
INSERT INTO `activity_logs` VALUES ('61', '3', 'Settings Updated', NULL, NULL, 'Store settings updated', '::1', '2026-03-05 12:40:43');
INSERT INTO `activity_logs` VALUES ('62', '3', 'Settings Updated', NULL, NULL, 'Store settings updated', '::1', '2026-03-05 14:50:46');
INSERT INTO `activity_logs` VALUES ('63', '3', 'Settings Updated', NULL, NULL, 'Store settings updated', '::1', '2026-03-05 14:51:04');
INSERT INTO `activity_logs` VALUES ('64', '3', 'Settings Updated', NULL, NULL, 'Store settings updated', '::1', '2026-03-05 14:51:25');



-- Table structure for `categories`
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `categories`
INSERT INTO `categories` VALUES ('1', 'Antibiotics', 'Medicines that fight bacterial infections', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('2', 'Pain Relief', 'Analgesics and pain management medicines', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('3', 'Vitamins & Supplements', 'Nutritional supplements and vitamins', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('4', 'Cardiovascular', 'Heart and blood pressure medicines', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('5', 'Diabetes', 'Medicines for diabetes management', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('6', 'Respiratory', 'Medicines for respiratory conditions', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('7', 'Gastrointestinal', 'Digestive system medicines', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('8', 'Dermatology', 'Skin care medicines', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('9', 'Neurology', 'Nervous system medicines', '2026-03-03 19:40:04');
INSERT INTO `categories` VALUES ('10', 'Others', 'Miscellaneous medicines', '2026-03-03 19:40:04');



-- Table structure for `companies`
DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_company_name` (`company_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `companies`
INSERT INTO `companies` VALUES ('1', 'ccl', 'asad ali', '03340540325', 'obaidbalouch1@gmail.com', 'nisthar', 'multan', NULL, NULL, 'active', '2026-03-03 19:48:15');



-- Table structure for `customers`
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `loyalty_points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`phone`),
  KEY `idx_customer_name` (`customer_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for `expenses`
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for `medicines`
DROP TABLE IF EXISTS `medicines`;
CREATE TABLE `medicines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_name` varchar(255) NOT NULL,
  `generic_name` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `manufacturing_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 10,
  `rack_location` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `side_effects` text DEFAULT NULL,
  `dosage` varchar(255) DEFAULT NULL,
  `status` enum('available','out_of_stock','expired') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `company_id` (`company_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_medicine_name` (`medicine_name`),
  KEY `idx_barcode` (`barcode`),
  KEY `idx_expiry` (`expiry_date`),
  KEY `idx_status` (`status`),
  CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medicines_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `medicines`
INSERT INTO `medicines` VALUES ('1', 'youkan', '', '4', '1', '23', '122313', '2026-03-22', '2026-03-30', '100.00', '127.00', '140.00', '50', '10', 'front of the cabin', '', NULL, NULL, 'available', '2026-03-03 19:50:18', '2026-03-04 18:51:51');
INSERT INTO `medicines` VALUES ('2', 'terbisil', 'terbinafine', '8', '1', '23', '11221122', '2026-03-03', '2026-03-03', '30.00', '50.00', '55.00', '250', '10', 'front of the cabin', 'once a day', NULL, NULL, 'available', '2026-03-03 20:06:14', '2026-03-04 18:51:28');
INSERT INTO `medicines` VALUES ('3', 'dr rasheel face wash', 'glutathione', '8', '1', '23', '11023213', '2026-02-22', '2026-07-22', '250.00', '400.00', '425.00', '198', '10', 'front of the cabin', '', NULL, NULL, 'available', '2026-03-03 22:45:12', '2026-03-05 00:07:10');
INSERT INTO `medicines` VALUES ('6', 'dr rasheel face wash2', '2', '8', '1', '23', 'LAY001', '2026-02-22', '2026-03-31', '100.00', '1000.00', '1050.00', '248', '10', '', '', NULL, NULL, 'available', '2026-03-04 12:04:50', '2026-03-05 00:07:10');
INSERT INTO `medicines` VALUES ('7', 'prexal', '', NULL, '1', '', '', '0000-00-00', '0000-00-00', '45.00', '55.00', '60.00', '200', '10', '', '', NULL, NULL, 'available', '2026-03-04 19:09:23', '2026-03-04 23:54:31');
INSERT INTO `medicines` VALUES ('10', 'youkan ijc', '', NULL, '1', NULL, NULL, NULL, NULL, '250.00', '1000.00', '1000.00', '15', '10', NULL, NULL, NULL, NULL, 'available', '2026-03-04 19:14:43', '2026-03-04 19:16:12');



-- Table structure for `purchase_items`
DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `purchase_items`
INSERT INTO `purchase_items` VALUES ('1', '1', '2', '1', '30.00', '30.00');



-- Table structure for `purchases`
DROP TABLE IF EXISTS `purchases`;
CREATE TABLE `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_number` varchar(50) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_status` enum('paid','pending','partial') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_number` (`purchase_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_purchase_date` (`purchase_date`),
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `purchases`
INSERT INTO `purchases` VALUES ('1', 'PUR-20260303-0710', '1', '3', '2026-03-03', '30.00', '0.00', '30.00', 'paid', '', '2026-03-03 20:16:02');



-- Table structure for `sale_items`
DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `medicine_id` (`medicine_id`),
  KEY `idx_sale_id` (`sale_id`),
  CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `sale_items`
INSERT INTO `sale_items` VALUES ('1', '1', '1', '23', '10', '127.00', '1270.00');
INSERT INTO `sale_items` VALUES ('2', '2', '2', '23', '20', '50.00', '1000.00');
INSERT INTO `sale_items` VALUES ('3', '2', '1', '23', '16', '127.00', '2032.00');
INSERT INTO `sale_items` VALUES ('4', '3', '2', '23', '29', '50.00', '1450.00');
INSERT INTO `sale_items` VALUES ('5', '4', '1', '23', '1', '127.00', '127.00');
INSERT INTO `sale_items` VALUES ('6', '4', '2', '23', '1', '50.00', '50.00');
INSERT INTO `sale_items` VALUES ('7', '5', '2', '23', '1', '50.00', '50.00');
INSERT INTO `sale_items` VALUES ('8', '5', '1', '23', '1', '127.00', '127.00');
INSERT INTO `sale_items` VALUES ('9', '6', '3', '23', '2', '400.00', '800.00');
INSERT INTO `sale_items` VALUES ('10', '7', '2', '23', '1', '50.00', '50.00');
INSERT INTO `sale_items` VALUES ('11', '8', '2', '23', '149', '50.00', '7450.00');
INSERT INTO `sale_items` VALUES ('12', '9', '1', '23', '100', '127.00', '12700.00');
INSERT INTO `sale_items` VALUES ('13', '10', '3', '23', '3', '400.00', '1200.00');
INSERT INTO `sale_items` VALUES ('14', '11', '6', '23', '100', '1000.00', '100000.00');
INSERT INTO `sale_items` VALUES ('15', '12', '7', '', '300', '55.00', '16500.00');
INSERT INTO `sale_items` VALUES ('16', '13', '10', '', '10', '1000.00', '10000.00');
INSERT INTO `sale_items` VALUES ('17', '14', '3', '23', '1', '400.00', '400.00');
INSERT INTO `sale_items` VALUES ('18', '14', '7', '', '50', '55.00', '2750.00');
INSERT INTO `sale_items` VALUES ('19', '15', '3', '23', '51', '400.00', '20400.00');
INSERT INTO `sale_items` VALUES ('20', '15', '6', '23', '2', '1000.00', '2000.00');



-- Table structure for `sales`
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `tax_percentage` decimal(5,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','upi','other') DEFAULT 'cash',
  `payment_status` enum('paid','pending','partial') DEFAULT 'paid',
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `change_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `customer_id` (`customer_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_invoice` (`invoice_number`),
  KEY `idx_sale_date` (`sale_date`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `sales`
INSERT INTO `sales` VALUES ('1', 'INV-20260303-3623', NULL, NULL, '3', '2026-03-03 19:55:59', '1270.00', '2.00', '25.40', '0.00', '0.00', '1295.40', 'cash', 'paid', '0.00', '0.00', '');
INSERT INTO `sales` VALUES ('2', 'INV-20260303-0448', NULL, NULL, '3', '2026-03-03 20:07:28', '3032.00', '20.00', '606.40', '16.00', '485.12', '3153.28', 'cash', 'paid', '0.00', '0.00', 'Bulk sale entry');
INSERT INTO `sales` VALUES ('3', 'INV-20260303-7638', NULL, NULL, '3', '2026-03-03 20:07:28', '1450.00', '29.00', '420.50', '0.00', '0.00', '1870.50', 'cash', 'paid', '0.00', '0.00', 'Bulk sale entry');
INSERT INTO `sales` VALUES ('4', 'INV-20260303-2770', NULL, NULL, '3', '2026-03-03 20:11:54', '177.00', '0.00', '0.00', '0.00', '0.00', '177.00', 'cash', 'paid', '250.00', '73.00', '');
INSERT INTO `sales` VALUES ('5', 'INV-20260303-1397', NULL, NULL, '3', '2026-03-03 21:06:26', '177.00', '0.00', '0.00', '0.00', '0.00', '177.00', 'cash', 'paid', '500.00', '323.00', '');
INSERT INTO `sales` VALUES ('6', 'INV-20260303-8849', NULL, NULL, '3', '2026-03-03 22:45:48', '800.00', '0.00', '0.00', '0.00', '0.00', '800.00', 'cash', 'paid', '1000.00', '200.00', '');
INSERT INTO `sales` VALUES ('7', 'INV-20260303-9187', NULL, NULL, '3', '2026-03-03 22:57:39', '50.00', '0.00', '0.00', '0.00', '0.00', '50.00', 'cash', 'paid', '0.00', '0.00', '');
INSERT INTO `sales` VALUES ('8', 'INV-20260304-2527', NULL, NULL, '3', '2026-03-04 05:25:17', '7450.00', '0.00', '0.00', '0.00', '0.00', '7450.00', 'cash', 'paid', '10000.00', '2550.00', '');
INSERT INTO `sales` VALUES ('9', 'INV-20260304-6204', NULL, NULL, '3', '2026-03-04 12:00:27', '12700.00', '0.00', '0.00', '0.00', '0.00', '12700.00', 'cash', 'paid', '0.00', '0.00', '');
INSERT INTO `sales` VALUES ('10', 'INV-20260304-8801', NULL, NULL, '3', '2026-03-04 12:02:24', '1200.00', '0.00', '0.00', '0.00', '0.00', '1200.00', 'cash', 'paid', '0.00', '0.00', '');
INSERT INTO `sales` VALUES ('11', 'INV-20260304-6703', NULL, NULL, '3', '2026-03-04 15:09:22', '100000.00', '0.00', '0.00', '0.00', '0.00', '100000.00', 'cash', 'paid', '0.00', '0.00', '');
INSERT INTO `sales` VALUES ('12', 'INV-20260304-2880', NULL, NULL, '3', '2026-03-04 19:15:10', '16500.00', '0.00', '0.00', '0.00', '0.00', '16500.00', 'cash', 'paid', '20000.00', '3500.00', '');
INSERT INTO `sales` VALUES ('13', 'INV-20260304-1265', NULL, NULL, '3', '2026-03-04 19:16:11', '10000.00', '0.00', '0.00', '0.00', '0.00', '10000.00', 'cash', 'paid', '10000.00', '0.00', '');
INSERT INTO `sales` VALUES ('14', 'INV-20260304-3470', NULL, NULL, '3', '2026-03-04 23:54:31', '3150.00', '0.00', '0.00', '0.00', '0.00', '3150.00', 'cash', 'paid', '4000.00', '850.00', '');
INSERT INTO `sales` VALUES ('15', 'INV-20260304-3111', NULL, NULL, '3', '2026-03-05 00:07:10', '22400.00', '0.00', '0.00', '0.00', '0.00', '22400.00', 'cash', 'paid', '22666.00', '266.00', '');



-- Table structure for `settings`
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `settings`
INSERT INTO `settings` VALUES ('1', 'store_name', 'KamranVaccine', '2026-03-05 12:40:17', '2026-03-05 14:51:25');
INSERT INTO `settings` VALUES ('2', 'store_address', '123 Medical Street', '2026-03-05 12:40:17', '2026-03-05 12:40:17');
INSERT INTO `settings` VALUES ('3', 'store_phone', '00000000', '2026-03-05 12:40:17', '2026-03-05 12:40:17');
INSERT INTO `settings` VALUES ('4', 'store_email', 'info@pharmacy.com', '2026-03-05 12:40:17', '2026-03-05 12:40:17');
INSERT INTO `settings` VALUES ('5', 'store_gst', '29XXXXX1234X1ZX', '2026-03-05 12:40:17', '2026-03-05 12:40:17');
INSERT INTO `settings` VALUES ('6', 'store_tagline', 'Management System', '2026-03-05 12:40:17', '2026-03-05 12:40:17');
INSERT INTO `settings` VALUES ('7', 'receipt_footer', 'Thank you for your business!', '2026-03-05 12:40:17', '2026-03-05 12:40:17');



-- Table structure for `suppliers`
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_supplier_name` (`supplier_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `suppliers`
INSERT INTO `suppliers` VALUES ('1', 'asad ali', '03340540325', '03340540325', 'obaidbalouch1@gmail.com', '', NULL, NULL, NULL, NULL, 'active', '2026-03-03 20:13:01');



-- Table structure for `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','pharmacist','cashier','manager') DEFAULT 'cashier',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username` (`username`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `users`
INSERT INTO `users` VALUES ('3', 'admin', '$2y$10$MsteC5Rp18SHmrARRjgGkeiV57bAkI7P/VMYFB/g9R5UAPGQiOZJe', 'Kamran', '123@gmail.com', '000000000', 'admin', 'active', '2026-03-03 19:45:43', '2026-03-05 12:32:06');
INSERT INTO `users` VALUES ('5', 'obaid', '$2y$10$mCI6agtNV4YhSAXYZkN9cOaMCv2bEIrM4krykj5KUJ4KYNPd/JfKq', 'obaid', 'obaidbalouch1@gmail.com', '', 'pharmacist', 'active', '2026-03-03 20:50:59', '2026-03-04 23:52:10');

SET FOREIGN_KEY_CHECKS=1;
