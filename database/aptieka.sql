-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 01, 2026 at 07:28 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

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
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int UNSIGNED DEFAULT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `username`, `role`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'kristovskis', 'admin', 'CREATE', 'user', 2, 'Izveidots moderators: moder', '::1', '2026-09-30 21:55:54'),
(2, 1, 'kristovskis', 'admin', 'LOGIN', 'user', 1, 'Veiksmīga autorizācija', '::1', '2026-09-30 22:05:54'),
(3, 1, 'kristovskis', 'admin', 'UPDATE', 'order', 22, 'Mainīts pasūtījuma statuss: Jauns → Apstrādē', '::1', '2026-09-30 22:21:33'),
(4, 1, 'kristovskis', 'admin', 'UPDATE', 'prescription', 19, 'Rediģēta recepte: R-2026-29, pacients: Maksims Bratkovs', '::1', '2026-09-30 22:21:49'),
(5, 2, 'moder', 'moderator', 'LOGIN', 'user', 2, 'Veiksmīga autorizācija', '::1', '2026-10-01 08:36:38'),
(6, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 13, 'Rediģētas zāles: Aspirin', '::1', '2026-10-01 08:37:51'),
(7, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 13, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 08:37:51'),
(8, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 10, 'Rediģētas zāles: Suprastin', '::1', '2026-10-01 08:39:07'),
(9, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 10, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 08:39:07'),
(10, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 4, 'Rediģētas zāles: Cetirizins', '::1', '2026-10-01 08:39:55'),
(11, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 4, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 08:39:55'),
(12, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 13, 'Rediģētas zāles: Aspirin', '::1', '2026-10-01 08:40:08'),
(13, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 2, 'Rediģētas zāles: Ibuprofen', '::1', '2026-10-01 08:45:18'),
(14, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 2, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 08:45:18'),
(15, 2, 'moder', 'moderator', 'CREATE', 'order', 23, 'Izveidots manuāls pasūtījums #23, klients: Maksims Bratkovs, zāles: С4 × 2', '::1', '2026-10-01 09:18:37'),
(16, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 9, 'Rediģētas zāles: Magne B6', '::1', '2026-10-01 09:24:36'),
(17, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 9, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 09:24:36'),
(18, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 12, 'Rediģētas zāles: No-Spa', '::1', '2026-10-01 09:25:18'),
(19, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 12, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 09:25:18'),
(20, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 5, 'Rediģētas zāles: Otrivin', '::1', '2026-10-01 09:25:55'),
(21, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 5, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 09:25:55'),
(22, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 1, 'Rediģētas zāles: Paracetamol', '::1', '2026-10-01 09:26:51'),
(23, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 1, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 09:26:51'),
(24, 2, 'moder', 'moderator', 'UPDATE', 'medicine', 11, 'Rediģētas zāles: Septolete', '::1', '2026-10-01 09:28:02'),
(25, 2, 'moder', 'moderator', 'UPLOAD_IMAGE', 'medicine', 11, 'Augšupielādēts zāļu attēls', '::1', '2026-10-01 09:28:03'),
(26, 2, 'moder', 'moderator', 'CREATE', 'order', 24, 'Izveidots un apmaksāts pirkums aptiekā #24, klients: Aleksandrs Denisenko, zāles: Ibuprofen × 1, Magne B6 × 1, summa: 12.59 EUR', '::1', '2026-10-01 09:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_substance` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_search_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosage_form` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strength` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `prescription_required` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `category_id`, `name`, `active_substance`, `api_search_name`, `manufacturer`, `dosage_form`, `strength`, `price`, `stock_quantity`, `prescription_required`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Paracetamol', 'Paracetamols', 'paracetamol', 'Olainfarm', 'Tabletes', '500 mg', 2.49, 120, 0, 'uploads/medicines/07e8ccbfe031edf176d5e6e3c1a823d7.png', '2026-09-26 21:33:45', '2026-10-01 09:26:51', NULL),
(2, 1, 'Ibuprofen', 'Ibuprofēns', 'ibuprofen', 'Grindeks', 'Tabletes', '400 mg', 3.19, 83, 0, 'uploads/medicines/f825409059deba75a5835c7fb74be6fd.jpg', '2026-09-26 21:33:45', '2026-10-01 09:34:17', NULL),
(3, 2, 'Vitamin D3', 'Holekalciferols', 'cholecalciferol', 'Walmark', 'Kapsulas', '2000 SV', 6.99, 60, 0, NULL, '2026-09-26 21:33:45', '2026-09-28 09:52:43', NULL),
(4, 3, 'Cetirizins', 'Cetirizīns', 'cetirizine', 'Teva', 'Tabletes', '10 mg', 5.10, 41, 0, 'uploads/medicines/c5f00227c340cdfc0cc7d17fda545aac.png', '2026-09-26 21:33:45', '2026-10-01 08:39:55', NULL),
(5, 4, 'Otrivin', 'Ksilometazolīns', 'xylometazoline', 'Haleon', 'Deguna aerosols', '0.1%', 4.89, 35, 0, 'uploads/medicines/b6e311229d4896a35217d0829027b03e.png', '2026-09-26 21:33:45', '2026-10-01 09:25:55', NULL),
(6, 4, 'Strepsils', 'Amilmetakrezols', 'amylmetacresol', 'Reckitt', 'Pastilas', '-', 5.49, 55, 0, NULL, '2026-09-26 21:33:45', '2026-09-28 09:52:43', NULL),
(7, 5, 'Smecta', 'Diosmektīts', 'diosmectite', 'Ipsen', 'Pulveris', '3 g', 7.20, 25, 0, NULL, '2026-09-26 21:33:45', '2026-09-28 09:52:43', NULL),
(8, 1, 'Voltaren', 'Diklofenaks', 'diclofenac', 'Haleon', 'Gēls', '2%', 8.90, 18, 0, NULL, '2026-09-26 21:33:45', '2026-09-28 09:52:43', NULL),
(9, 2, 'Magne B6', 'Magnijs un B6 vitamīns', 'pyridoxine', 'Sanofi', 'Tabletes', '-', 9.40, 43, 0, 'uploads/medicines/09995f13c0a0d6ee87fbf8658c210e24.png', '2026-09-26 21:33:45', '2026-10-01 09:34:17', NULL),
(10, 3, 'Suprastin', 'Hloropiramīns', 'chloropyramine', 'Egis', 'Tabletes', '25 mg', 4.20, 18, 1, 'uploads/medicines/c7150d6166a50dac60a4403432b89f61.jpg', '2026-09-26 21:33:45', '2026-10-01 08:39:07', NULL),
(11, 4, 'Septolete', 'Benzidamīns', 'benzydamine', 'KRKA', 'Pastilas', '-', 5.99, 37, 0, 'uploads/medicines/44f854812ae7d3745329e3d1b226a42c.png', '2026-09-26 21:33:45', '2026-10-01 09:28:03', NULL),
(12, 1, 'No-Spa', 'Drotaverīns', 'drotaverine', 'Sanofi', 'Tabletes', '40 mg', 4.70, 47, 0, 'uploads/medicines/e6838d6fac829dd798ca35a9ca6d3ce4.jpg', '2026-09-26 21:33:45', '2026-10-01 09:25:18', NULL),
(13, 1, 'Aspirin', 'Acetilsalicilskābe', 'acetylsalicylic acid', 'Bayer', 'Tabletes', '500 mg', 3.50, 69, 1, 'uploads/medicines/dccf9245ff7c90eb674265203deb0d4e.jpg', '2026-09-26 21:33:45', '2026-10-01 08:40:08', NULL),
(14, 4, 'ACC2', 'Acetilcisteīns', 'acetylcysteine', 'Sandoz', 'Putojošās tabletes', '600 mg', 7.80, 30, 0, NULL, '2026-09-26 21:33:45', '2026-09-30 09:57:49', '2026-09-30 09:57:49'),
(15, 2, 'Vitamin C', 'Askorbīnskābe', 'ascorbic acid', 'Walmark', 'Tabletes', '500 mg', 4.99, 89, 0, NULL, '2026-09-26 21:33:45', '2026-09-30 11:52:36', NULL),
(16, 3, 'С4', 'CIFOR', 'C4', 'TER', 'Tabletes', '500 mg', 5.00, 3, 0, NULL, '2026-09-29 10:42:48', '2026-09-29 10:42:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `customer_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_country` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Jauns','Apstrādē','Gatavs','Izsniegts','Atcelts') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Jauns',
  `payment_status` enum('Neapmaksāts','Apmaksāts','Neizdevās') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Neapmaksāts',
  `stripe_session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `delivery_name`, `delivery_address`, `delivery_city`, `delivery_postal_code`, `delivery_country`, `status`, `payment_status`, `stripe_session_id`, `total`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Klients 1', 'klients1@example.com', NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', NULL, 5.68, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(2, 'Klients 2', 'klients2@example.com', NULL, NULL, NULL, NULL, NULL, 'Apstrādē', 'Neapmaksāts', NULL, 6.99, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(3, 'Klients 3', 'klients3@example.com', NULL, NULL, NULL, NULL, NULL, 'Gatavs', 'Neapmaksāts', NULL, 10.20, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(4, 'Klients 4', 'klients4@example.com', NULL, NULL, NULL, NULL, NULL, 'Izsniegts', 'Neapmaksāts', NULL, 4.89, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(5, 'Klients 5', 'klients5@example.com', NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', NULL, 12.69, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(6, 'Klients 6', 'klients6@example.com', NULL, NULL, NULL, NULL, NULL, 'Apstrādē', 'Neapmaksāts', NULL, 8.90, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(7, 'Klients 7', 'klients7@example.com', NULL, NULL, NULL, NULL, NULL, 'Gatavs', 'Neapmaksāts', NULL, 9.40, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(8, 'Klients 8', 'klients8@example.com', NULL, NULL, NULL, NULL, NULL, 'Izsniegts', 'Neapmaksāts', NULL, 5.99, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(9, 'Klients 9', 'klients9@example.com', NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', NULL, 7.80, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(10, 'Klients 10', 'klients10@example.com', NULL, NULL, NULL, NULL, NULL, 'Atcelts', 'Neapmaksāts', NULL, 4.99, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(11, 'Klients 11', 'klients11@example.com', NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', NULL, 9.38, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(12, 'Klients 12', 'klients12@example.com', NULL, NULL, NULL, NULL, NULL, 'Apstrādē', 'Neapmaksāts', NULL, 11.48, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(13, 'Klients 13', 'klients13@example.com', NULL, NULL, NULL, NULL, NULL, 'Gatavs', 'Neapmaksāts', NULL, 7.00, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(14, 'Klients 14', 'klients14@example.com', NULL, NULL, NULL, NULL, NULL, 'Izsniegts', 'Neapmaksāts', NULL, 13.98, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(15, 'Klients 15', 'klients15@example.com', NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', NULL, 15.60, '2026-09-26 21:33:45', '2026-09-26 21:33:45', NULL),
(16, 'Aleksandrs Denisenko', 'aominebas2006@gmail.com', NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Apmaksāts', 'cs_test_b1wt8ZsfBCSH7fqhvRarvBjlSMlUOMv4pgKYSrjybiIcOx5CbIq9taw9cq', 12.80, '2026-09-30 11:23:57', '2026-09-30 11:24:29', NULL),
(17, 'Klients', NULL, NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', 'cs_test_a1mUFOBaLeBOZBWuBK2p8NWwxDlan1xD5rQ19SFdCygpzdA0enyQB0WJFA', 4.99, '2026-09-30 11:26:08', '2026-09-30 11:26:09', NULL),
(18, 'Klients', NULL, NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', 'cs_test_a1QUH37uVcYnpACkhBFFulNzsO8bO6IeVcUl54cJ00ILWQy2nRy4dEV0KK', 4.99, '2026-09-30 11:26:59', '2026-09-30 11:27:00', NULL),
(19, 'Klients', NULL, NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Neapmaksāts', 'cs_test_a1OSBSByhIGIANrFVJ8tx7aimWfbpiu7nO1LYNHxj7byGFAE0UIgS0P9FC', 4.99, '2026-09-30 11:35:22', '2026-09-30 11:35:22', NULL),
(20, 'Klients', 'aominebas2006@gmail.com', NULL, NULL, NULL, NULL, NULL, 'Jauns', 'Apmaksāts', 'cs_test_b124EZPf9vdIeZCucF3EHezHPL4B4kV4aVFz62EmdGAcPuHiPD35hSFuTV', 10.98, '2026-09-30 11:52:15', '2026-09-30 11:52:36', NULL),
(21, 'Klients', 'aominebas2006@gmail.com', 'Aleksandrs Denisenko', 'Ģenerāļa Baloža iela 5-16', 'Liepāja', 'LV-3414', 'LV', 'Jauns', 'Apmaksāts', 'cs_test_a1hujDqxwLRpSsAmipZPwC0PNZTdiTc978Nb2dVDSmhE5CEUdqYn7d6RFp', 3.19, '2026-09-30 11:55:29', '2026-09-30 11:55:51', NULL),
(22, 'Aleksandrs Denisenko', 'aominebas2006@gmail.com', 'Aleksandrs Denisenko', 'Ģenerāļa Baloža iela 5-16', 'Liepāja', 'LV-3414', 'LV', 'Apstrādē', 'Apmaksāts', 'cs_test_a1GO8P58YwKfInupJr1tZneQKKKsRz3RzJSIjBj4SVD36zG1KZq8cXuZdB', 4.20, '2026-09-30 12:00:00', '2026-09-30 22:21:33', NULL),
(23, 'Maksims Bratkovs', 'aominebas2006@gmail.com', 'Maksims Bratkovs', 'Polk', 'Liepāja', 'LV-3414', 'LV', 'Jauns', 'Neapmaksāts', NULL, 10.00, '2026-10-01 09:18:37', '2026-10-01 09:18:37', NULL),
(24, 'Aleksandrs Denisenko', NULL, NULL, NULL, NULL, NULL, NULL, 'Izsniegts', 'Apmaksāts', NULL, 12.59, '2026-10-01 09:34:17', '2026-10-01 09:34:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `medicine_id` int UNSIGNED NOT NULL,
  `prescription_id` int UNSIGNED DEFAULT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `medicine_id`, `prescription_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, NULL, 1, 2.49),
(2, 1, 2, NULL, 1, 3.19),
(3, 2, 3, NULL, 1, 6.99),
(4, 3, 4, NULL, 2, 5.10),
(5, 4, 5, NULL, 1, 4.89),
(6, 5, 6, NULL, 1, 5.49),
(7, 5, 7, NULL, 1, 7.20),
(8, 6, 8, NULL, 1, 8.90),
(9, 7, 9, NULL, 1, 9.40),
(10, 8, 11, NULL, 1, 5.99),
(11, 9, 14, NULL, 1, 7.80),
(12, 10, 15, NULL, 1, 4.99),
(13, 11, 12, NULL, 2, 4.69),
(14, 12, 1, NULL, 1, 2.49),
(15, 12, 13, NULL, 1, 3.50),
(16, 12, 6, NULL, 1, 5.49),
(17, 13, 13, NULL, 2, 3.50),
(18, 14, 3, NULL, 2, 6.99),
(19, 15, 14, NULL, 2, 7.80),
(20, 16, 4, NULL, 1, 5.10),
(21, 16, 13, NULL, 1, 3.50),
(22, 16, 10, 17, 1, 4.20),
(23, 17, 15, NULL, 1, 4.99),
(24, 18, 15, NULL, 1, 4.99),
(25, 19, 15, NULL, 1, 4.99),
(26, 20, 15, NULL, 1, 4.99),
(27, 20, 11, NULL, 1, 5.99),
(28, 21, 2, NULL, 1, 3.19),
(29, 22, 10, 18, 1, 4.20),
(30, 23, 16, NULL, 2, 5.00),
(31, 24, 2, NULL, 1, 3.19),
(32, 24, 9, NULL, 1, 9.40);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int UNSIGNED NOT NULL,
  `prescription_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `patient_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_code` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `doctor_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_at` date NOT NULL,
  `valid_until` date NOT NULL,
  `status` enum('Aktīva','Izmantota','Beidzies termiņš','Atcelta') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktīva',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `prescription_number`, `patient_name`, `personal_code`, `doctor_name`, `issued_at`, `valid_until`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'R-2026-0001', 'Maksims Bratkovs', '999999-00001', 'Dr. Bērziņš', '2026-09-01', '2026-10-01', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', '2026-09-29 10:33:56'),
(2, 'R-2026-0002', 'Testa Pacients 2', '999999-00002', 'Dr. Ozola', '2026-09-02', '2026-10-02', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(3, 'R-2026-0003', 'Testa Pacients 3', '999999-00003', 'Dr. Kalniņš', '2026-09-03', '2026-10-03', 'Izmantota', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(4, 'R-2026-0004', 'Testa Pacients 4', '999999-00004', 'Dr. Liepa', '2026-09-04', '2026-10-04', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(5, 'R-2026-0005', 'Testa Pacients 5', '999999-00005', 'Dr. Jansons', '2026-09-05', '2026-10-05', 'Atcelta', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(6, 'R-2026-0006', 'Testa Pacients 6', '999999-00006', 'Dr. Bērziņš', '2026-09-06', '2026-10-06', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(7, 'R-2026-0007', 'Testa Pacients 7', '999999-00007', 'Dr. Ozola', '2026-09-07', '2026-10-07', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(8, 'R-2026-0008', 'Testa Pacients 8', '999999-00008', 'Dr. Kalniņš', '2026-09-08', '2026-10-08', 'Izmantota', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(9, 'R-2026-0009', 'Testa Pacients 9', '999999-00009', 'Dr. Liepa', '2026-09-09', '2026-10-09', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(10, 'R-2026-0010', 'Testa Pacients 10', '999999-00010', 'Dr. Jansons', '2026-09-10', '2026-10-10', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(11, 'R-2026-0011', 'Testa Pacients 11', '999999-00011', 'Dr. Bērziņš', '2026-09-11', '2026-10-11', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(12, 'R-2026-0012', 'Testa Pacients 12', '999999-00012', 'Dr. Ozola', '2026-09-12', '2026-10-12', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(13, 'R-2026-0013', 'Testa Pacients 13', '999999-00013', 'Dr. Kalniņš', '2026-09-13', '2026-10-13', 'Izmantota', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(14, 'R-2026-0014', 'Testa Pacients 14', '999999-00014', 'Dr. Liepa', '2026-09-14', '2026-10-14', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(15, 'R-2026-0015', 'Testa Pacients 15', '999999-00015', 'Dr. Jansons', '2026-09-15', '2026-10-15', 'Aktīva', '2026-09-26 21:33:45', '2026-09-30 10:13:07', NULL),
(16, 'R-2026-0027', 'Aleksandrs Deņisenko', '270606-10803', 'Popova', '2006-06-27', '2036-08-27', 'Aktīva', '2026-09-29 09:12:43', '2026-09-30 10:14:15', NULL),
(17, 'R-2026-27', 'Aleksandrs Denisenko', '270606-10803', 'Popova', '2006-06-27', '2030-06-30', 'Izmantota', '2026-09-30 11:07:29', '2026-09-30 11:24:29', NULL),
(18, 'R-2026-28', 'Aleksandrs Denisenko', '270606-10803', 'Popova', '2009-06-27', '2030-09-30', 'Aktīva', '2026-09-30 11:57:02', '2026-09-30 22:07:01', NULL),
(19, 'R-2026-29', 'Maksims Bratkovs', '191207-10803', 'Popova', '2009-06-27', '2030-09-30', 'Aktīva', '2026-09-30 22:09:03', '2026-09-30 22:09:03', NULL);

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
(15, 15, 10, 1),
(17, 16, 8, 1),
(18, 1, 10, 1),
(20, 17, 10, 1),
(23, 18, 10, 2),
(25, 19, 10, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','moderator') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moderator',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(1, 'kristovskis', '$2y$10$qtBvhQ2qHtG54HJiudaIdeSHXEn64KdanD41hksC7RrTEXF2DDz42', 'admin', '2026-09-28 10:08:33', '2026-09-30 21:45:50'),
(2, 'moder', '$2y$10$L6AVXaaFZyWDDQYHKRpouuwUmYwlWj1T0eCmJhKEuPBjF4V2JvRoC', 'moderator', '2026-09-30 21:55:54', '2026-09-30 21:55:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user_id` (`user_id`),
  ADD KEY `idx_audit_created_at` (`created_at`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_orders_stripe_session_id` (`stripe_session_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_medicine` (`medicine_id`),
  ADD KEY `idx_order_items_prescription` (`prescription_id`);

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
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
