-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 24, 2026 at 12:59 PM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `a1676fyx_visionexim_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT IGNORE INTO `admin_users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Vision Exim Admin', 'admin@visionexim.com', '$2y$10$UkQDlDXbTz8OebwjzUHUuu9KYfisov/Tj/kCgZzjTwQdfpPl7x9Ny', 'admin', '2026-05-26 10:15:34'),
(2, 'Admin', 'admin@king.com', '$2y$10$Kx0AZ/9du6pXBCOZ5BL4UeLncZOfZ2rZ5gTIrfJJfKB55X5Rk2Iue', 'admin', '2026-05-30 07:06:07');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT IGNORE INTO `categories` (`id`, `name`, `slug`, `parent_id`, `description`, `status`, `sort_order`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Spices', 'spices', NULL, 'Premium quality whole and ground Indian spices, processed to retain their natural aroma, color, and intense flavor.', 'active', 1, NULL, '2026-05-28 11:31:14', '2026-06-24 07:08:49'),
(2, 'Pulses', 'pulses', NULL, 'Nutritious, high-protein pulses and lentils sourced from premium farms, cleaned and graded for export markets.', 'active', 2, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(4, 'Rice', 'rice', NULL, 'Premium aromatic Basmati and long-grain non-Basmati rice, aged to perfection and milled with advanced technology.', 'active', 4, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(5, 'Grains', 'grains', NULL, 'High-quality agricultural feed grains, yellow maize, wheat, and millet sourced directly from major harvesting regions.', 'active', 5, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(6, 'Flour', 'flour', NULL, 'Premium grade wheat flour, corn flour, and other grain flours, processed for excellent texture and nutritional value.', 'active', 6, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(7, 'Penuts', 'penuts', NULL, 'Bold and Java peanut varieties, sortex-cleaned and graded for moisture and size to meet stringent international standards.', 'active', 7, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(9, 'Whole Spices', 'whole-spices', 1, 'whole spices are directly coming from farm to use', 'active', 2, NULL, '2026-06-24 07:10:35', '2026-06-24 07:11:59');

-- --------------------------------------------------------

--
-- Table structure for table `harvest_calendar`
--

CREATE TABLE IF NOT EXISTS `harvest_calendar` (
  `id` int(11) NOT NULL,
  `spice_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jan` tinyint(1) NOT NULL DEFAULT '0',
  `feb` tinyint(1) NOT NULL DEFAULT '0',
  `mar` tinyint(1) NOT NULL DEFAULT '0',
  `apr` tinyint(1) NOT NULL DEFAULT '0',
  `may` tinyint(1) NOT NULL DEFAULT '0',
  `jun` tinyint(1) NOT NULL DEFAULT '0',
  `jul` tinyint(1) NOT NULL DEFAULT '0',
  `aug` tinyint(1) NOT NULL DEFAULT '0',
  `sep` tinyint(1) NOT NULL DEFAULT '0',
  `oct` tinyint(1) NOT NULL DEFAULT '0',
  `nov` tinyint(1) NOT NULL DEFAULT '0',
  `dec_month` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_id` int(11) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `harvest_calendar`
--

INSERT IGNORE INTO `harvest_calendar` (`id`, `spice_name`, `jan`, `feb`, `mar`, `apr`, `may`, `jun`, `jul`, `aug`, `sep`, `oct`, `nov`, `dec_month`, `sort_order`, `created_at`, `updated_at`, `product_id`, `image`) VALUES
(1, 'Black Pepper', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, '2026-05-26 10:15:54', '2026-06-24 06:58:47', NULL, NULL),
(2, 'Cardamom', 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 2, '2026-05-26 10:15:54', '2026-05-30 12:40:48', NULL, 'upload/products/harvest_6a1adacff36f3.webp'),
(3, 'Chillies', 1, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 1, 3, '2026-05-26 10:15:54', '2026-05-26 10:15:54', NULL, NULL),
(4, 'Coriander', 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4, '2026-05-26 10:15:54', '2026-05-26 10:15:54', NULL, NULL),
(5, 'Cumin', 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, '2026-05-26 10:15:54', '2026-05-26 10:15:54', NULL, NULL),
(6, 'Fennel', 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6, '2026-05-26 10:15:54', '2026-05-26 10:15:54', NULL, NULL),
(7, 'Fenugreek', 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 7, '2026-05-26 10:15:54', '2026-05-27 09:57:44', NULL, NULL),
(8, 'Turmeric', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, '2026-05-26 10:15:54', '2026-05-26 10:15:54', NULL, NULL),
(9, 'Mustard', 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 9, '2026-05-26 10:15:54', '2026-05-26 10:15:54', NULL, NULL),
(10, 'Garlic', 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 10, '2026-05-26 10:15:54', '2026-05-26 10:15:54', NULL, NULL),
(11, 'Ginger', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11, '2026-05-26 10:15:54', '2026-05-27 09:58:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int(11) NOT NULL,
  `company_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_flag` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requested_product` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `internal_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT IGNORE INTO `inquiries` (`id`, `company_name`, `contact_name`, `email`, `phone`, `country_flag`, `country_name`, `city`, `requested_product`, `quantity`, `message`, `source`, `status`, `internal_notes`, `created_at`, `updated_at`) VALUES
(1, 'Ahmed Trading Co.', 'Ahmed Al-Mansoori', 'ahmed@trading.ae', '+971 50 123 4567', '🇦🇪', 'UAE', 'Deira, Dubai', 'Turmeric Powder', '500 KG', 'Dear Vision Exim Team, we are interested in importing organic Salem Turmeric Powder with minimum 4.5% curcumin content. Please quote your best FOB Mundra Port price for a 500 KG trial shipment. Also, let us know about the packaging options available. Thank you.', 'website', 'closed', 'Buyer wants to pay 30% advance and 70% against BL scan. Curcumin content report must be shared before container loading.', '2026-05-26 10:15:34', '2026-05-26 10:46:20'),
(2, 'Global Foods Inc.', 'Johnathan Smith', 'procurement@globalfoods.com', '+1 212 987 6543', '🇺🇸', 'USA', 'New York', 'Red Chili Powder', '1 Ton', 'We require 1 Ton of high heat Guntur Red Chili Powder (Teja quality, stemless) for spice blending in the US. Please provide CIF New York port pricing and specify if certificate of analysis is provided with each batch.', 'whatsapp', 'progress', 'Requested Teja chili specs. Looking to close pricing next week.', '2026-05-26 10:15:34', '2026-05-26 10:15:34'),
(3, 'Spice Paradise LLC', 'Elena Rostova', 'info@spiceparadise.co.uk', '+44 20 7946 0958', '🇬🇧', 'UK', 'London', 'Cumin Seeds', '2 Tons', 'Hello, could you please quote machine cleaned cumin seeds Singapore Quality 99% purity. We need 2 Tons trial shipment shipped to London Gateway Port. Please share your product catalog too.', 'email', 'replied', 'Quotation sent via email. Volatile oil specification shared.', '2026-05-26 10:15:34', '2026-05-26 10:15:34'),
(4, 'Middle East Spices FZE', 'Mohammed bin Rashid', 'purchase@mespices.ae', '+971 6 544 3322', '🇦🇪', 'UAE', 'Sharjah', 'Fenugreek Seeds', '800 KG', 'Looking for fresh harvest premium Rajasthan Fenugreek Seeds. Trial order of 800 KG needed for UAE market. Quote FOB Mumbai.', 'website', 'new', 'Requires grade A certificate.', '2026-05-26 10:15:34', '2026-05-26 10:15:34'),
(5, 'EuroSpice Import', 'Hans Schmidt', 'contact@eurospice.de', '+49 89 2345 6789', '🇩🇪', 'Germany', 'Hamburg', 'Black Pepper Whole', '5 Tons', 'Dear sales, please send quotation for Malabar bold black pepper 550g/l density. Trial of 5 Tons CIF Hamburg. We need standard organic certifications.', 'email', 'new', 'Lost deal due to competitor providing cheaper logistics options.', '2026-05-26 10:15:34', '2026-05-26 10:56:10'),
(11, 'E_S_S', 'test1', 'test1@gmail.com', '9876543210', NULL, 'india,hindu', NULL, 'Green Cardamom', '800KG', 'helllllloooo', 'website', 'new', NULL, '2026-05-27 05:40:51', '2026-05-27 09:10:51'),
(12, 'EMPEROR', 'harsh', 'harsh123@gmail.com', '9876543265', NULL, '', NULL, 'Multi Grains', '', 'tetetetetetetetetetetee', 'website', 'new', 'ffwcwd', '2026-05-27 07:42:49', '2026-05-27 12:00:35'),
(13, 'testcompany', 'tushar', 'tushar1@gmail.com', '9874563210', NULL, 'india', NULL, 'Cumin Seeds', '100kg', 'related products', 'website', 'new', NULL, '2026-06-23 23:58:13', '2026-06-24 05:28:13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `hs_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `full_description` text COLLATE utf8mb4_unicode_ci,
  `moq` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packaging` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quality_standard` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'India',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery_images` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT IGNORE INTO `products` (`id`, `name`, `slug`, `category_id`, `hs_code`, `short_description`, `full_description`, `moq`, `packaging`, `quality_standard`, `origin_state`, `origin_country`, `image`, `gallery_images`, `status`, `sort_order`, `is_featured`, `seo_title`, `seo_description`, `created_at`, `updated_at`) VALUES
(1, 'Turmeric Powder', 'turmeric-powder', 1, '09103020', 'Premium high-curcumin turmeric powder with vibrant golden-yellow color and rich earthy flavor.', 'Our Turmeric Powder is sourced from the finest turmeric finger harvesting regions of India. It has high curcumin content, offering excellent natural color, aroma, and health properties. It is processed under strict quality control to guarantee purity and zero adulteration.', '99.5% Purity', '25kg / 50kg PP Bags or Paper Bags', 'Grade A', 'Maharashtra & Andhra Pradesh', 'India', 'upload/products/prod_6a3b81b11cde6.jpeg', NULL, 'active', 0, 1, '', '', '2026-05-28 11:31:14', '2026-06-24 07:05:21'),
(2, 'Cumin Seeds', 'cumin-seeds', 1, '09093120', 'Authentic, sortex-cleaned cumin seeds with strong warm aroma and rich flavor profile.', 'Highly aromatic Indian Cumin Seeds (Jeera), sortex-cleaned to remove impurities. Known for its intense aroma, warm flavor, and therapeutic properties, ideal for culinary uses globally.', '99% Pure', '25kg PP Bags', 'Singapore Quality (99%)', 'Gujarat & Rajasthan', 'India', 'upload/products/prod_6a3b8128a772e.jpg', NULL, 'active', 0, 1, '', '', '2026-05-28 11:31:14', '2026-06-24 07:03:04'),
(3, 'Green Mung Beans', 'green-mung-beans', 2, '07133100', 'Export-grade whole green mung beans, highly nutritious and uniform in size.', 'Our Green Mung Beans are meticulously cleaned, machine-polished, and color sortex-sorted. Rich in protein, dietary fiber, and essential minerals. Perfect for cooking and sprouting.', '98.5% Purity', '25kg / 50kg Bags', 'Premium Export Quality', 'Madhya Pradesh & Maharashtra', 'India', '', NULL, 'active', 0, 1, NULL, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(5, 'Premium Basmati Rice', 'premium-basmati-rice', 4, '10063020', 'Aromatic, extra long-grain traditional basmati rice, aged to achieve unmatched aroma and taste.', 'Experience the royalty of traditional Basmati Rice. Aged for 1-2 years to reduce moisture and enhance elongation during cooking. Non-sticky, fluffy texture with an captivating aroma.', '95% Purity (Average grain length 8.3mm+)', '10kg / 20kg / 25kg / 50kg Non-Woven Bags', 'Premium Extra Long Grain', 'Haryana & Punjab', 'India', '', NULL, 'active', 0, 1, NULL, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(6, 'Yellow Maize (Corn)', 'yellow-maize', 5, '10059000', 'Premium quality yellow maize, ideal for animal feed and human consumption.', 'Our Yellow Maize is sourced from premium agricultural belts, dried naturally to standard moisture levels, and machine-cleaned. Low aflatoxin content and high nutritional energy value.', 'Standard Feed Grade', '50kg Jute or PP Bags', 'Export Grade A', 'Karnataka & Maharashtra', 'India', '', NULL, 'active', 0, 1, NULL, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(7, 'Premium Wheat Flour (Atta)', 'premium-wheat-flour', 6, '11010000', 'Stone-ground wheat flour (Chakki Atta) with all natural bran and nutrients intact.', 'Processed from selected high-gluten wheat grains. Perfectly milled to make soft, nutritious flatbreads (chapatis/rotis) and baked items. Highly hygienic packaging to prevent contamination.', '100% Whole Wheat', '5kg / 10kg / 25kg Multi-layer Bags', 'Grade A (No Additives)', 'Madhya Pradesh', 'India', '', NULL, 'active', 0, 1, NULL, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(8, 'Bold Peanut Kernels', 'bold-peanut-kernels', 7, '12024200', 'Sortex-cleaned bold peanut kernels, rich in oil content and perfectly graded.', 'Indian groundnuts / peanuts (Bold variety) graded by count per ounce (e.g. 38/42, 40/50, 50/60). Well-dried, low moisture content, aflatoxin controlled, and excellent sweet nutty flavor.', '99.9% Cleaned', '25kg / 50kg Jute Bags or Vacuum Bags', 'Premium (Count 40/50)', 'Gujarat', 'India', '', NULL, 'active', 0, 1, NULL, NULL, '2026-05-28 11:31:14', '2026-05-28 11:31:14'),
(10, 'test1', 'test1', 6, '63200', 'test1', 'test1', '500', '25', '', '', 'India', NULL, NULL, 'active', 2, 0, '', '', '2026-05-29 08:41:24', '2026-05-29 09:07:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `harvest_calendar`
--
ALTER TABLE `harvest_calendar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_spice` (`spice_name`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `harvest_calendar`
--
ALTER TABLE `harvest_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
