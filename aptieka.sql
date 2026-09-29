-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 27, 2026 at 07:13 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aptieka`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Pretsāpju līdzekļi', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(2, 'Vitamīni un minerālvielas', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(3, 'Alerģijas līdzekļi', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(4, 'Saaukstēšanās līdzekļi', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(5, 'Kuņģa-zarnu trakta līdzekļi', '2026-09-26 21:33:45', '2026-09-26 21:33:45');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_substance` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosage_form` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strength` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `prescription_required` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `category_id`, `name`, `active_substance`, `manufacturer`, `dosage_form`, `strength`, `price`, `stock_quantity`, `prescription_required`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Paracetamol', 'Paracetamols', 'Olainfarm', 'Tabletes', '500 mg', 2.49, 120, 0, NULL, '2026-09-26 21:33:45', '2026-09-27 15:38:57'),
(2, 1, 'Ibuprofen', 'Ibuprofēns', 'Grindeks', 'Tabletes', '400 mg', 3.19, 85, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(3, 2, 'Vitamin D3', 'Holekalciferols', 'Walmark', 'Kapsulas', '2000 SV', 6.99, 60, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(4, 3, 'Cetirizin', 'Cetirizīns', 'Teva', 'Tabletes', '10 mg', 5.10, 42, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(5, 4, 'Otrivin', 'Ksilometazolīns', 'Haleon', 'Deguna aerosols', '0.1%', 4.89, 35, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(6, 4, 'Strepsils', 'Amilmetakrezols', 'Reckitt', 'Pastilas', '-', 5.49, 55, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(7, 5, 'Smecta', 'Diosmektīts', 'Ipsen', 'Pulveris', '3 g', 7.20, 25, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(8, 1, 'Voltaren', 'Diklofenaks', 'Haleon', 'Gēls', '2%', 8.90, 18, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(9, 2, 'Magne B6', 'Magnijs un B6 vitamīns', 'Sanofi', 'Tabletes', '-', 9.40, 44, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(10, 3, 'Suprastin', 'Hloropiramīns', 'Egis', 'Tabletes', '25 mg', 4.20, 20, 1, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(11, 4, 'Septolete', 'Benzidamīns', 'KRKA', 'Pastilas', '-', 5.99, 38, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(12, 1, 'No-Spa', 'Drotaverīns', 'Sanofi', 'Tabletes', '40 mg', 4.70, 47, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(13, 1, 'Aspirin', 'Acetilsalicilskābe', 'Bayer', 'Tabletes', '500 mg', 3.50, 70, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(14, 4, 'ACC', 'Acetilcisteīns', 'Sandoz', 'Putojošās tabletes', '600 mg', 7.80, 30, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(15, 2, 'Vitamin C', 'Askorbīnskābe', 'Walmark', 'Tabletes', '500 mg', 4.99, 90, 0, NULL, '2026-09-26 21:33:45', '2026-09-26 21:33:45');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Jauns','Apstrādē','Gatavs','Izsniegts','Atcelts') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Jauns',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `status`, `total`, `created_at`, `updated_at`) VALUES
(1, 'Klients 1', 'klients1@example.com', 'Jauns', 5.68, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(2, 'Klients 2', 'klients2@example.com', 'Apstrādē', 6.99, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(3, 'Klients 3', 'klients3@example.com', 'Gatavs', 10.20, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(4, 'Klients 4', 'klients4@example.com', 'Izsniegts', 4.89, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(5, 'Klients 5', 'klients5@example.com', 'Jauns', 12.69, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(6, 'Klients 6', 'klients6@example.com', 'Apstrādē', 8.90, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(7, 'Klients 7', 'klients7@example.com', 'Gatavs', 9.40, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(8, 'Klients 8', 'klients8@example.com', 'Izsniegts', 5.99, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(9, 'Klients 9', 'klients9@example.com', 'Jauns', 7.80, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(10, 'Klients 10', 'klients10@example.com', 'Atcelts', 4.99, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(11, 'Klients 11', 'klients11@example.com', 'Jauns', 9.38, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(12, 'Klients 12', 'klients12@example.com', 'Apstrādē', 11.48, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(13, 'Klients 13', 'klients13@example.com', 'Gatavs', 7.00, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(14, 'Klients 14', 'klients14@example.com', 'Izsniegts', 13.98, '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(15, 'Klients 15', 'klients15@example.com', 'Jauns', 15.60, '2026-09-26 21:33:45', '2026-09-26 21:33:45');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `medicine_id` int UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `medicine_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 1, 2.49),
(2, 1, 2, 1, 3.19),
(3, 2, 3, 1, 6.99),
(4, 3, 4, 2, 5.10),
(5, 4, 5, 1, 4.89),
(6, 5, 6, 1, 5.49),
(7, 5, 7, 1, 7.20),
(8, 6, 8, 1, 8.90),
(9, 7, 9, 1, 9.40),
(10, 8, 11, 1, 5.99),
(11, 9, 14, 1, 7.80),
(12, 10, 15, 1, 4.99),
(13, 11, 12, 2, 4.69),
(14, 12, 1, 1, 2.49),
(15, 12, 13, 1, 3.50),
(16, 12, 6, 1, 5.49),
(17, 13, 13, 2, 3.50),
(18, 14, 3, 2, 6.99),
(19, 15, 14, 2, 7.80);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int UNSIGNED NOT NULL,
  `prescription_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `doctor_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_at` date NOT NULL,
  `valid_until` date NOT NULL,
  `status` enum('Aktīva','Izmantota','Beidzies termiņš','Atcelta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktīva',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `prescription_number`, `patient_name`, `doctor_name`, `issued_at`, `valid_until`, `status`, `created_at`, `updated_at`) VALUES
(1, 'R-2026-0001', 'Testa Pacients 1', 'Dr. Bērziņš', '2026-09-01', '2026-10-01', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(2, 'R-2026-0002', 'Testa Pacients 2', 'Dr. Ozola', '2026-09-02', '2026-10-02', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(3, 'R-2026-0003', 'Testa Pacients 3', 'Dr. Kalniņš', '2026-09-03', '2026-10-03', 'Izmantota', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(4, 'R-2026-0004', 'Testa Pacients 4', 'Dr. Liepa', '2026-09-04', '2026-10-04', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(5, 'R-2026-0005', 'Testa Pacients 5', 'Dr. Jansons', '2026-09-05', '2026-10-05', 'Atcelta', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(6, 'R-2026-0006', 'Testa Pacients 6', 'Dr. Bērziņš', '2026-09-06', '2026-10-06', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(7, 'R-2026-0007', 'Testa Pacients 7', 'Dr. Ozola', '2026-09-07', '2026-10-07', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(8, 'R-2026-0008', 'Testa Pacients 8', 'Dr. Kalniņš', '2026-09-08', '2026-10-08', 'Izmantota', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(9, 'R-2026-0009', 'Testa Pacients 9', 'Dr. Liepa', '2026-09-09', '2026-10-09', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(10, 'R-2026-0010', 'Testa Pacients 10', 'Dr. Jansons', '2026-09-10', '2026-10-10', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(11, 'R-2026-0011', 'Testa Pacients 11', 'Dr. Bērziņš', '2026-09-11', '2026-10-11', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(12, 'R-2026-0012', 'Testa Pacients 12', 'Dr. Ozola', '2026-09-12', '2026-10-12', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(13, 'R-2026-0013', 'Testa Pacients 13', 'Dr. Kalniņš', '2026-09-13', '2026-10-13', 'Izmantota', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(14, 'R-2026-0014', 'Testa Pacients 14', 'Dr. Liepa', '2026-09-14', '2026-10-14', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45'),
(15, 'R-2026-0015', 'Testa Pacients 15', 'Dr. Jansons', '2026-09-15', '2026-10-15', 'Aktīva', '2026-09-26 21:33:45', '2026-09-26 21:33:45');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` int UNSIGNED NOT NULL,
  `prescription_id` int UNSIGNED NOT NULL,
  `medicine_id` int UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `medicine_id`, `quantity`) VALUES
(1, 1, 10, 1),
(2, 2, 4, 1),
(3, 3, 10, 2),
(4, 4, 4, 1),
(5, 5, 10, 1),
(6, 6, 4, 2),
(7, 7, 10, 1),
(8, 8, 4, 1),
(9, 9, 10, 1),
(10, 10, 4, 2),
(11, 11, 10, 1),
(12, 12, 4, 1),
(13, 13, 10, 1),
(14, 14, 4, 2),
(15, 15, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'employee',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_medicines_category` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_medicine` (`medicine_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prescription_number` (`prescription_number`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prescription_items_prescription` (`prescription_id`),
  ADD KEY `fk_prescription_items_medicine` (`medicine_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medicines`
--
ALTER TABLE `medicines`
  ADD CONSTRAINT `fk_medicines_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `fk_prescription_items_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_prescription_items_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
