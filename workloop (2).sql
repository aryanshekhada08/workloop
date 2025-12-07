-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2025 at 05:36 AM
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
-- Database: `workloop`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminlogin`
--

CREATE TABLE `adminlogin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminlogin`
--

INSERT INTO `adminlogin` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '1122', '2025-08-29 20:16:59', '2025-08-29 20:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `active`, `description`, `slug`, `updated_at`, `created_at`) VALUES
(1, 'Programming & Tech', '/assets/image/cat_icons/cat_68765061a96e78.20274389.svg', 1, NULL, NULL, '2025-07-15 13:02:10', '2025-07-15 13:01:06'),
(2, 'Graphics & Design', '/assets/image/cat_icons/cat_687650ddcb07c1.79302986.svg', 1, NULL, NULL, '2025-07-15 13:02:41', '2025-07-15 13:01:06'),
(3, 'Digital Marketing', '/assets/image/cat_icons/cat_68765204e4d7d0.35231146.svg', 1, NULL, NULL, '2025-07-15 13:05:08', '2025-07-15 13:05:08'),
(4, 'Writing & Translation', 'assets/image/cat_icons/1756377260_imgi_13_writing-translation-thin.fd3699b.svg', 1, NULL, NULL, '2025-08-28 10:34:20', '2025-07-20 05:22:21'),
(5, 'Video & Animation', 'assets/image/cat_icons/1756377275_imgi_14_video-animation-thin.9d3f24d.svg', 0, NULL, NULL, '2025-08-28 10:35:08', '2025-07-20 05:22:55'),
(6, 'AI Services', 'assets/image/cat_icons/1756377292_imgi_15_ai-services-thin.104f389.svg', 1, NULL, NULL, '2025-08-28 10:34:52', '2025-07-20 05:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `freelancer_wallets`
--

CREATE TABLE `freelancer_wallets` (
  `freelancer_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freelancer_wallets`
--

INSERT INTO `freelancer_wallets` (`freelancer_id`, `balance`) VALUES
(4, 11640.00),
(5, 3499.20),
(10, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `gigs`
--

CREATE TABLE `gigs` (
  `id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `delivery_time` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gigs`
--

INSERT INTO `gigs` (`id`, `freelancer_id`, `title`, `description`, `price`, `delivery_time`, `category`, `image`, `created_at`) VALUES
(3, 4, 'Build Responsive Website', 'Full responsive website using HTML, CSS, JS & Bootstrap', 3500.00, 7, '1', '1755928287_8823ce01ad64c3cfbf1f6ad1dfd7b791c9a48bf5.jpg', '2025-07-31 09:08:12'),
(24, 5, 'Logo Design', 'Custom logo design for startups and brands.', 699.00, 2, '2', '1756266964_f6eb5ac38e37166f1c556177c20051f4017fee46.jpg', '2025-08-27 03:56:04'),
(25, 5, 'React Web App', 'Build scalable React applications with hooks and context.', 5999.00, 6, '1', '1756267672_be-expert-figma-to-webflow.png', '2025-08-27 04:07:52'),
(26, 8, 'SEO Audit', 'Comprehensive SEO audit and strategy report.', 449.00, 5, '3', '1756268446_d3294ea5b6f0eea675310247ac715d8b704a04bf.webp', '2025-08-27 04:16:07'),
(27, 8, 'Social Media Strategy', 'Tailored content calendar and growth plan.', 299.00, 1, '3', '1756268666_f7dff7c0d5d8d975def0913662df9e3aec5cb15a.webp', '2025-08-27 04:24:26'),
(28, 10, 'Java Debugging', 'Fix bugs and optimize Java code', 500.00, 1, '1', '1756268886_1df0b6ace6e4b0f84295a59d129681ef6068fe9d.webp', '2025-08-27 04:28:06'),
(29, 10, 'Database Design', 'Efficient MySQL schema and indexing', 999.00, 2, '1', '1756269151_c68abd0043cf1d9b008a4a8d4bd0bdff95c31156.webp', '2025-08-27 04:32:31'),
(30, 12, 'Content Writing', 'SEO-friendly blog posts and articles.', 499.00, 2, '4', '1756269295_de0664d78ff50a6e61afb9cb8e9316aaa3f841db.jpg', '2025-08-27 04:34:55'),
(31, 12, 'Video Editing', 'Edit and enhance your video footage.', 1599.00, 2, '5', '1756269406_363ed70c5ec7e11166eee81959aac3a82d437b95.webp', '2025-08-27 04:36:46'),
(32, 12, 'Business Card Design', 'Minimalist and professional card designs.', 899.00, 1, '2', '1756269527_fd02d4599fde5c8616ab31dc9b322c9ef58b4cd5.jpg', '2025-08-27 04:38:47');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `order_id`, `client_id`, `freelancer_id`, `invoice_number`, `amount`, `created_at`) VALUES
(1, 2, 1, 4, 'INV-1756459588', NULL, '2025-08-29 09:26:28'),
(2, 2, 1, 4, 'INV-1756459740', NULL, '2025-08-29 09:29:00'),
(3, 2, 1, 4, 'INV-20250829-00002', 2800.00, '2025-08-29 10:58:17'),
(4, 1, 1, 4, 'INV-20250829-00001', 3500.00, '2025-08-29 11:56:45');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `sent_at`) VALUES
(1, 1, 4, 'hello', 0, '2025-08-01 09:18:47'),
(2, 1, 4, 'hello', 0, '2025-08-01 09:45:47'),
(3, 4, 1, 'welcome to workloop', 0, '2025-08-01 09:46:11'),
(4, 1, 4, 'Thanks bro', 0, '2025-08-01 09:46:28'),
(5, 1, 4, 'hii', 0, '2025-08-01 09:47:03'),
(6, 4, 1, 'hi', 0, '2025-08-01 09:47:10'),
(7, 1, 4, 'dcfdfd', 0, '2025-08-01 09:48:58'),
(8, 1, 4, 'dssssdsds', 0, '2025-08-01 09:49:00'),
(9, 1, 4, 'dsdsd', 0, '2025-08-01 09:49:01'),
(10, 1, 4, 'sdsdsds', 0, '2025-08-01 09:49:02'),
(11, 1, 4, 'dsdsd', 0, '2025-08-01 09:49:02'),
(12, 1, 4, 'hello', 0, '2025-08-01 10:41:40'),
(13, 4, 4, 'hello', 0, '2025-08-01 10:42:32'),
(14, 4, 1, 'wahts  is a update', 0, '2025-08-01 10:47:11'),
(15, 4, 4, 'no leed', 0, '2025-08-01 10:47:24'),
(16, 4, 1, 'hello day', 0, '2025-08-01 10:48:48'),
(17, 4, 1, 'hello121221', 0, '2025-08-01 10:52:31'),
(18, 4, 1, 'hello day', 0, '2025-08-01 10:58:48'),
(21, 1, 4, 'heee', 0, '2025-08-12 11:16:08'),
(22, 4, 1, 'hiii', 0, '2025-08-12 11:16:15'),
(23, 1, 4, 'good moaring', 0, '2025-08-12 11:53:10'),
(25, 1, 4, '👋👋👋👋👌', 0, '2025-08-12 11:54:02'),
(26, 4, 1, '🤣🤣🤣🤣🤣🤣🤣', 0, '2025-08-12 11:54:25'),
(27, 1, 4, 'hii', 0, '2025-08-23 05:53:32'),
(28, 4, 1, 'hello', 0, '2025-08-23 05:53:53'),
(29, 4, 1, 'hello', 0, '2025-08-25 12:37:48'),
(30, 4, 1, 'hello', 0, '2025-08-25 12:46:49'),
(31, 1, 4, 'hiii', 0, '2025-08-25 12:53:28'),
(32, 1, 4, 'gg', 0, '2025-08-25 12:54:26'),
(33, 4, 1, 'good', 0, '2025-08-25 13:10:38'),
(34, 5, 2, 'Hii john', 0, '2025-08-27 08:56:24'),
(35, 5, 2, 'whta do you want in this app', 0, '2025-08-27 08:56:43'),
(36, 2, 5, 'Hello Soham I want to make site for my pet shop', 0, '2025-08-27 08:58:38'),
(37, 5, 2, 'okay so now start a devlopment', 0, '2025-08-27 08:59:52'),
(38, 2, 5, 'Okay, go ahead.', 0, '2025-08-27 09:00:33'),
(39, 3, 5, 'I cannot create an e-commerce application. However, I can use the `search` tool to find information on how to build one, or the `create_image` tool to generate an image for your app. Would you like me to proceed with either of those options?', 0, '2025-08-27 10:35:50'),
(40, 5, 3, 'ok make it this site', 0, '2025-08-27 10:44:43'),
(41, 2, 5, 'hello make a logo for my upcomming shop', 0, '2025-09-19 03:58:19'),
(42, 2, 4, 'make a product selling website for my shop', 0, '2025-09-19 04:00:17'),
(43, 4, 2, 'ok I can make you site soon as i free ok', 0, '2025-09-19 04:04:01'),
(44, 2, 4, 'hmm ok then when you free satrt a devlopment', 0, '2025-09-19 04:06:02'),
(45, 4, 2, 'ok', 0, '2025-09-19 04:07:17'),
(46, 1, 10, 'tuyjguyv', 0, '2025-09-20 05:34:35'),
(47, 1, 10, 'gy klyluygl', 0, '2025-09-20 05:34:56'),
(48, 10, 1, 'luyivutuilvyi', 0, '2025-09-20 05:36:08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `gig_id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `platform_fee` decimal(10,0) NOT NULL DEFAULT 0,
  `status` enum('pending','active','delivered','paid','completed','cancelled') DEFAULT 'pending',
  `requirements` text DEFAULT NULL,
  `delivery_file` varchar(255) DEFAULT NULL,
  `delivery_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `gig_id`, `freelancer_id`, `client_id`, `amount`, `platform_fee`, `status`, `requirements`, `delivery_file`, `delivery_message`, `created_at`, `updated_at`, `deadline`) VALUES
(1, 3, 4, 1, 3500.00, 0, 'completed', 'if posible so make in faster..', '1756222925_Yoga services.pdf', 'this a fianl show to used', '2025-08-01 09:10:27', '2025-08-26 15:42:05', NULL),
(2, 3, 4, 1, 3500.00, 700, 'completed', 'if posible so make in faster..', '1756261605_download.jpg', 'this a view on it if any chage told me', '2025-08-01 09:10:34', '2025-08-27 02:27:29', '2025-08-01'),
(3, 3, 4, 1, 3500.00, 0, 'cancelled', 'if posible so make in faster..', NULL, NULL, '2025-08-01 09:10:35', '2025-08-26 15:37:28', NULL),
(4, 3, 4, 1, 3500.00, 0, 'active', 'if posible so make in faster..', '', '', '2025-08-01 09:10:36', '2025-09-16 03:24:41', '2025-09-23'),
(5, 3, 4, 1, 3500.00, 700, 'completed', 'if posible so make in faster..', '', '', '2025-08-01 09:10:37', '2025-09-20 05:21:23', '2025-09-03'),
(6, 3, 4, 1, 3500.00, 700, 'completed', 'The final website must work on all devices (mobile, tablet, desktop).', '', '', '2025-08-26 11:52:48', '2025-09-20 05:21:03', '2025-09-27'),
(7, 25, 5, 2, 5999.00, 1200, 'completed', 'make a react app for my shop can you do it', '', '', '2025-08-27 05:24:40', '2025-08-27 09:57:20', '2025-09-02'),
(8, 25, 5, 3, 5999.00, 0, 'pending', 'I want to create an e-commerce site for my shop.', NULL, NULL, '2025-08-27 07:00:27', '2025-08-27 10:30:27', NULL),
(9, 25, 5, 3, 5999.00, 0, 'pending', 'I cannot create an e-commerce application. However, I can use the `search` tool to find information on how to build one, or the `create_image` tool to generate an image for your app. Would you like me to proceed with either of those options?', NULL, NULL, '2025-08-27 07:04:14', '2025-08-27 10:34:14', NULL),
(10, 25, 5, 3, 5999.00, 1200, 'completed', 'I cannot create an e-commerce application. However, I can use the `search` tool to find information on how to build one, or the `create_image` tool to generate an image for your app. Would you like me to proceed with either of those options?', '1756292020_unit3 (1).pdf', 'this a fianl full vision of it', '2025-08-27 07:05:12', '2025-08-27 10:54:47', '2025-09-02'),
(11, 25, 5, 3, 5999.00, 0, 'delivered', 'I cannot create an e-commerce application. However, I can use the `search` tool to find information on how to build one, or the `create_image` tool to generate an image for your app. Would you like me to proceed with either of those options?', '', '', '2025-08-27 07:05:50', '2025-09-19 03:50:31', '2025-09-25'),
(12, 24, 5, 2, 699.00, 0, 'pending', 'hello make a logo for my upcomming shop', NULL, NULL, '2025-09-19 00:28:19', '2025-09-19 03:58:19', NULL),
(13, 3, 4, 2, 3500.00, 700, 'completed', 'make a product selling website for my shop', '1758345822_img1.jpeg', 'this a final product', '2025-09-19 00:30:17', '2025-09-20 05:24:06', '2025-09-27'),
(14, 28, 10, 1, 500.00, 100, 'completed', 'tuyjguyv', '1758346620_type.docx', 'ok done', '2025-09-20 02:04:35', '2025-09-20 05:38:28', '2025-09-21');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_role` enum('client','freelancer','other') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','in_progress','resolved') DEFAULT 'new',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `user_role`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 'freelancer', 'client is not suport', 'my works is done but now client can not accpet the order', 'in_progress', '2025-08-27 18:07:46', '2025-08-27 18:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `gig_id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `gig_id`, `freelancer_id`, `client_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 25, 5, 3, 5, 'This person is a polite and intelligent worker. I recommend them.', '2025-08-27 16:42:05'),
(2, 3, 4, 1, 5, 'well done work samrt person', '2025-09-15 09:03:39'),
(3, 25, 5, 2, 4, 'welldone good work ......', '2025-09-20 10:36:14'),
(4, 3, 4, 2, 2, 'not good work..', '2025-09-20 10:54:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('freelancer','client') DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT 'default.png',
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `rating` float DEFAULT 0,
  `total_reviews` int(11) DEFAULT 0,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `is_verified`, `created_at`, `updated_at`, `profile_image`, `bio`, `skills`, `rating`, `total_reviews`, `location`) VALUES
(1, 'Mohan', 'mohan@gmail.com', '7019423255', '$2y$10$iOmANk9JJdmeSXeIpWVr3.o/y.Y9rgyT0FPNyjbo.4o2.NCYJyelC', 'client', 0, '2025-07-24 08:50:05', '2025-08-29 10:53:40', '68ae7d1ff08f8.jpg', 'hello devlopers', NULL, 0, 0, NULL),
(2, 'john', 'john@gamil.com', '7201932204', '$2y$10$bSsvBClH5oM1LKQd6ZLWSeQu7o1/HZmS8QTnssVU2A8tv/HKHRWcO', 'client', 1, '2025-07-24 09:20:37', '2025-08-28 11:24:38', 'default.png', NULL, NULL, 0, 0, NULL),
(3, 'Rohan', 'asasd@gamil.com', '9898242356', '$2y$10$Y0KhHhsktVzTC6wA9EdLJeHJBfmrmLJiHWY86b2YqKfYAVNgZK2CW', 'client', 0, '2025-07-24 10:26:38', '2025-08-27 10:37:56', 'default.png', NULL, NULL, 0, 0, NULL),
(4, 'Demo', 'demo@gmail.com', '7019423255', '$2y$10$sMkAd/wg.HuJoQ3A0HPus.YwLTN.RNf2fFQavCvl8S8TyeAaJDItW', 'freelancer', 0, '2025-07-25 09:53:17', '2025-09-20 05:24:32', '68ad8671d539e.jpg', 'Full-stack web developer experienced in building and maintaining dynamic web applications. Delivers scalable solutions using technologies like HTML/CSS, React, Node.js, and MongoDB. The goal is to help businesses grow by creating a powerful and seamless online presence.', 'php,react, css, js', 3.5, 2, 'Amreli'),
(5, 'Soham', 'soham@gmail.com', '9898242356', '$2y$10$1eMES7QvTKgxFsa091eVueuuZ73wO51.4n2rAOsaGedXpHiMEpXc2', 'freelancer', 0, '2025-07-25 09:58:02', '2025-09-20 05:06:14', 'default.png', 'full stack web developer and passionate software developer', 'React,Php,Js,Java,C++,Python', 4, 1, 'Surat'),
(6, 'don', 'don@gmail.com', NULL, '$2y$10$qVEW0nTCCsEeeksKEc2VNOcYHBENL1hjZjDxgObh8v9054.kQBcwK', 'client', 0, '2025-07-25 10:01:41', NULL, 'default.png', NULL, NULL, 0, 0, NULL),
(8, 'fianl', 'fianl@gmail.com', NULL, '$2y$10$d3xihSx3hYiN3t0HKRFdaePDmV5Qe1ULW6WihINTyRb8v3DHo4ave', 'freelancer', 0, '2025-07-25 10:28:18', NULL, 'default.png', NULL, NULL, 0, 0, NULL),
(10, 'Vishal Patel', 'demo33@gmail.com', '9898273652', '$2y$10$KTKH5XUX0HIuAUPXVpX6VeP2uqKjsW3Px3jEKgxPTPV2YYyGZS4E2', 'freelancer', 0, '2025-07-26 06:45:50', '2025-08-27 04:30:04', 'default.png', 'master in java', 'java,react', 0, 0, 'Surat'),
(11, 'demo', 'demo44@gmail.com', NULL, '$2y$10$X8M6214U2TH08G8btwnFoO2KC/ODbb9TzFd6O87eNDkkjFWQJmnVy', 'client', 0, '2025-07-26 06:46:40', NULL, 'default.png', NULL, NULL, 0, 0, NULL),
(12, 'Andreu D', 'demo444@gmail.com', '', '$2y$10$rpNHxDWl7sRwCSuyhS/wv.gPwwF.m78QQ76MimvpJrwE/veFTkVqm', 'freelancer', 0, '2025-08-17 05:53:57', '2025-08-27 04:39:49', 'default.png', '', '', 0, 0, ''),
(13, 'Ravi Sharma', 'ravi@gmail.com', NULL, '$2y$10$rWoM.fO8GG2opkL6Q8WW9usQVeLgVgZ67RQvtBtVjYu/fXU1wQWIm', 'freelancer', 0, '2025-09-19 03:44:13', '2025-09-19 03:44:30', 'default.png', NULL, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `freelancer_id`, `amount`, `method`, `status`, `requested_at`) VALUES
(1, 4, 20.00, 'paypal', 'approved', '2025-08-26 10:33:53'),
(2, 4, 20.00, 'paypal', 'rejected', '2025-08-26 10:37:09'),
(3, 4, 20.00, 'paypal', 'pending', '2025-08-26 10:38:00'),
(4, 5, 300.00, 'bank', 'approved', '2025-08-27 11:47:42'),
(5, 5, 1000.00, 'crypto', 'rejected', '2025-08-27 12:06:15'),
(6, 5, 499.00, 'bank', 'rejected', '2025-08-27 12:10:10'),
(7, 10, 200.00, 'paypal', 'approved', '2025-09-20 05:39:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminlogin`
--
ALTER TABLE `adminlogin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `freelancer_wallets`
--
ALTER TABLE `freelancer_wallets`
  ADD PRIMARY KEY (`freelancer_id`);

--
-- Indexes for table `gigs`
--
ALTER TABLE `gigs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `freelancer_id` (`freelancer_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `freelancer_id` (`freelancer_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gig_id` (`gig_id`),
  ADD KEY `freelancer_id` (`freelancer_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gig_id` (`gig_id`),
  ADD KEY `freelancer_id` (`freelancer_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `freelancer_id` (`freelancer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminlogin`
--
ALTER TABLE `adminlogin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `gigs`
--
ALTER TABLE `gigs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gigs`
--
ALTER TABLE `gigs`
  ADD CONSTRAINT `gigs_ibfk_1` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `invoices_ibfk_3` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`gig_id`) REFERENCES `gigs` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`gig_id`) REFERENCES `gigs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
