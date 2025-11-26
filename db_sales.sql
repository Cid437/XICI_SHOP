-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 05:50 PM
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
-- Database: `db_sales`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `recipient_name`, `address_line1`, `city`, `province`, `zip_code`, `phone_number`, `is_default`) VALUES
(1, 4, 'Cyrus TO', '12345', 'taguig', 'ncr', '1650', '09876625177', 0);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `lname` varchar(100) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `addressline` varchar(255) NOT NULL,
  `town` varchar(100) NOT NULL,
  `zipcode` varchar(20) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `userId`, `title`, `lname`, `fname`, `addressline`, `town`, `zipcode`, `phone`, `profile_picture`) VALUES
(2, 4, 'sr', 'to', 'Cy', 'Tanyag Taguig City', 'Tanyag', '1', '09876625311', 'uploads/profile_pictures/1763354174_sa.jpg'),
(3, 3, 'adm', 'Pagayunan', 'Cyrus', 'Tanyag Taguig City', 'Tanyag', '1630', '09762801859', 'uploads/profile_pictures/1763354238_1.png'),
(4, 6, 'sir', 'pri', 'jem', '123', 'taguig', '1678', '09865523522', 'uploads/profile_pictures/1763391440_gear-5-luffy-3840x2160-24363.jpg'),
(5, 6, 'sir', 'pri', 'jem', '123', 'taguig', '1678', '09865523522', 'uploads/profile_pictures/1763391440_gear-5-luffy-3840x2160-24363.jpg'),
(6, 7, '', 'to', 'bago', '', '', '', '', 'uploads/profile_pictures/1763481165_newsa.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `long_description` text DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `description`, `long_description`, `cost_price`, `sell_price`, `category`) VALUES
(1, 'Studio Monitor Headphones', 'Closed-back headphones designed for professional monitoring and mixing. Delivers a flat, accurate sound.', 5500.00, 8999.00, 'Studio'),
(2, '2-Channel Audio Interface', 'Connect microphones and instruments to your computer with this high-quality USB audio interface.', 4500.00, 7500.00, 'Studio'),
(5, 'Acoustic Dreadnought Guitar', 'A full-sized steel-string acoustic guitar, perfect for beginners and seasoned players. Rich, resonant tone.', 8000.00, 12500.00, 'Guitars'),
(6, 'Classic Electric Guitar', 'Iconic S-style solid body electric guitar. Versatile tone suitable for rock, blues, and pop. Maple neck.', 15000.00, 24999.00, 'Guitars'),
(7, '4-String Bass Guitar', 'Standard P-style electric bass guitar. Delivers a punchy, deep low-end. Simple and reliable.', 12000.00, 19500.00, 'Guitars'),
(8, '88-Key Digital Piano', 'A fully weighted digital piano with realistic hammer action. Includes stand and three pedals.', 25000.00, 42000.00, 'Keyboards'),
(9, '61-Key Portable Keyboard', 'Lightweight and portable keyboard with hundreds of built-in tones and rhythms. Great for learning.', 5000.00, 8500.00, 'Keyboards'),
(10, 'Synthesizer Workstation', 'Professional synthesizer with advanced sound design capabilities, sequencing, and sampling.', 35000.00, 55000.00, 'Keyboards'),
(11, '5-Piece Drum Kit', 'Complete acoustic drum set including snare, bass drum, two rack toms, a floor tom, cymbals, and hardware.', 22000.00, 35000.00, 'Drums'),
(12, 'Electronic Drum Set', 'A quiet and compact electronic drum kit with mesh heads for a realistic feel. Includes a sound module with various kits.', 28000.00, 45000.00, 'Drums'),
(13, 'Cajon Box Drum', 'A versatile percussion instrument from Peru. Produces deep bass tones and a crisp snare sound.', 3500.00, 5500.00, 'Drums'),
(14, 'Condenser Studio Microphone', 'Large-diaphragm condenser microphone ideal for recording vocals and acoustic instruments with clarity.', 6000.00, 9999.00, 'Studio');

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `image_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`image_id`, `item_id`, `image_path`) VALUES
(4, 4, 'images/1763378315_OIP (1).jpg'),
(7, 3, 'images/1763378487_OIP (2).jpg'),
(8, 3, 'images/1763378514_it apps.png'),
(9, 3, 'images/1763378514_web.jpg'),
(11, 4, 'images/1763391243_campus-2-3.png'),
(12, 4, 'images/1763391243_gear-5-luffy-3840x2160-24363.jpg'),
(13, 4, 'images/1763391243_resizwed.jpg'),
(17, 13, 'images/1763395209_cajon.jpg'),
(18, 13, 'images/1763395209_cajon2.jpg'),
(19, 13, 'images/1763395209_cajon3.jpg'),
(20, 12, 'images/1763395219_edrum.jpg'),
(21, 12, 'images/1763395219_edrum2.jpg'),
(22, 12, 'images/1763395219_edrum3.png'),
(25, 10, 'images/1763395246_synth3.jpg'),
(26, 9, 'images/1763395263_key.png'),
(27, 9, 'images/1763395263_key2.jpg'),
(28, 9, 'images/1763395263_key3.png'),
(31, 11, 'images/1763395277_drum3.png'),
(34, 8, 'images/1763395286_piano3.jpg'),
(35, 7, 'images/1763395297_bass.png'),
(36, 7, 'images/1763395297_bass2.png'),
(37, 7, 'images/1763395297_bass3.jpg'),
(38, 6, 'images/1763395309_eg.jpg'),
(39, 6, 'images/1763395309_eg2.jpg'),
(40, 6, 'images/1763395309_eg3.jpg'),
(42, 5, 'images/1763395319_gui2.jpg'),
(43, 5, 'images/1763395319_gui3.jpg'),
(45, 2, 'images/1763395334_solo2.jpg'),
(46, 2, 'images/1763395334_solo3.jpg'),
(48, 1, 'images/1763395348_head2.jpg'),
(49, 1, 'images/1763395348_head3.jpg'),
(50, 1, 'images/1763396157_head.jpg'),
(51, 2, 'images/1763396197_solo.png'),
(52, 5, 'images/1763396257_gui.jpg'),
(54, 8, 'images/1763396348_piano.jpg'),
(55, 8, 'images/1763396348_piano2.jpg'),
(56, 10, 'images/1763396400_synth.png'),
(57, 10, 'images/1763396400_synth2.jpg'),
(58, 11, 'images/1763396438_drum.png'),
(59, 11, 'images/1763396438_drum2.jpg'),
(60, 14, 'images/1763396470_mic.png'),
(61, 14, 'images/1763396497_mic2 (2).jpg'),
(62, 14, 'images/1763396497_mic2 (3).jpg');

-- --------------------------------------------------------

--
-- Stand-in structure for view `orderdetails`
-- (See below for the actual view)
--
CREATE TABLE `orderdetails` (
`orderinfo_id` int(11)
,`lname` varchar(100)
,`fname` varchar(100)
,`addressline` varchar(255)
,`town` varchar(100)
,`zipcode` varchar(20)
,`phone` varchar(50)
,`sell_price` decimal(10,2)
,`quantity` int(11)
,`description` varchar(255)
,`status` enum('Processing','Delivered','Canceled')
);

-- --------------------------------------------------------

--
-- Table structure for table `orderinfo`
--

CREATE TABLE `orderinfo` (
  `orderinfo_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `shipping_address_id` int(11) DEFAULT NULL,
  `date_placed` datetime NOT NULL,
  `date_shipped` datetime DEFAULT NULL,
  `shipping` decimal(10,2) NOT NULL,
  `status` enum('Processing','Delivered','Canceled') NOT NULL DEFAULT 'Processing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderinfo`
--

INSERT INTO `orderinfo` (`orderinfo_id`, `customer_id`, `shipping_address_id`, `date_placed`, `date_shipped`, `shipping`, `status`) VALUES
(1, 3, NULL, '2025-11-17 12:42:31', NULL, 10.00, 'Delivered'),
(2, 2, NULL, '2025-11-17 14:17:52', NULL, 10.00, 'Delivered'),
(3, 2, NULL, '2025-11-17 14:45:56', NULL, 10.00, 'Canceled'),
(4, 2, 1, '2025-11-17 17:52:13', NULL, 50.00, 'Delivered'),
(5, 2, NULL, '2025-11-18 02:08:40', NULL, 10.00, 'Canceled'),
(6, 2, NULL, '2025-11-18 02:14:52', NULL, 10.00, 'Delivered'),
(7, 3, NULL, '2025-11-18 21:25:30', NULL, 10.00, 'Delivered'),
(8, 3, NULL, '2025-11-19 00:16:08', NULL, 10.00, 'Delivered'),
(9, 6, NULL, '2025-11-19 00:18:43', NULL, 10.00, 'Canceled'),
(10, 6, NULL, '2025-11-19 00:21:09', NULL, 10.00, 'Canceled'),
(11, 2, 1, '2025-11-19 00:43:59', NULL, 50.00, 'Canceled');

-- --------------------------------------------------------

--
-- Table structure for table `orderline`
--

CREATE TABLE `orderline` (
  `orderline_id` int(11) NOT NULL,
  `orderinfo_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderline`
--

INSERT INTO `orderline` (`orderline_id`, `orderinfo_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 3, 2, 1),
(4, 4, 1, 2),
(5, 5, 1, 1),
(6, 6, 5, 1),
(7, 7, 2, 1),
(8, 8, 8, 1),
(9, 9, 7, 1),
(10, 10, 8, 1),
(11, 11, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text NOT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `item_id`, `user_id`, `rating`, `comment`, `review_date`) VALUES
(1, 1, 4, 5, 'maganda naman sya talaga super', '2025-11-17 06:38:29'),
(2, 5, 4, 5, 'ganda nung tunog malupit sya kaso  **** at ****', '2025-11-17 18:15:45'),
(3, 1, 3, 5, 'maangas', '2025-11-18 02:34:55'),
(4, 2, 3, 5, 'maangas', '2025-11-18 13:32:55');

-- --------------------------------------------------------

--
-- Stand-in structure for view `salesperorder`
-- (See below for the actual view)
--
CREATE TABLE `salesperorder` (
`orderId` int(11)
,`total` decimal(42,2)
,`status` enum('Processing','Delivered','Canceled')
);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stock_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`stock_id`, `item_id`, `quantity`) VALUES
(1, 1, 46),
(2, 2, 74),
(5, 5, 15),
(6, 6, 12),
(7, 7, 10),
(8, 8, 7),
(9, 9, 25),
(10, 10, 7),
(11, 11, 10),
(12, 12, 12),
(13, 13, 20),
(14, 14, 29);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `email`, `password`, `role`, `is_active`) VALUES
(3, 'pagayunancyrus@gmail.com', '356a192b7913b04c54574d18c28d46e6395428ab', 'admin', 1),
(4, 'cyrus@email.com', '356a192b7913b04c54574d18c28d46e6395428ab', 'user', 1),
(6, 'akotojem@gmail.com', '356a192b7913b04c54574d18c28d46e6395428ab', 'user', 1),
(7, 'bagongacc@gmail.com', '356a192b7913b04c54574d18c28d46e6395428ab', 'user', 0);

-- --------------------------------------------------------

--
-- Structure for view `orderdetails`
--
DROP TABLE IF EXISTS `orderdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `orderdetails`  AS SELECT `o`.`orderinfo_id` AS `orderinfo_id`, `c`.`lname` AS `lname`, `c`.`fname` AS `fname`, `c`.`addressline` AS `addressline`, `c`.`town` AS `town`, `c`.`zipcode` AS `zipcode`, `c`.`phone` AS `phone`, `i`.`sell_price` AS `sell_price`, `ol`.`quantity` AS `quantity`, `i`.`description` AS `description`, `o`.`status` AS `status` FROM (((`customer` `c` join `orderinfo` `o` on(`c`.`customer_id` = `o`.`customer_id`)) join `orderline` `ol` on(`o`.`orderinfo_id` = `ol`.`orderinfo_id`)) join `item` `i` on(`ol`.`item_id` = `i`.`item_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `salesperorder`
--
DROP TABLE IF EXISTS `salesperorder`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `salesperorder`  AS SELECT `o`.`orderinfo_id` AS `orderId`, sum(`i`.`sell_price` * `ol`.`quantity`) AS `total`, `o`.`status` AS `status` FROM ((`orderinfo` `o` join `orderline` `ol` on(`o`.`orderinfo_id` = `ol`.`orderinfo_id`)) join `item` `i` on(`ol`.`item_id` = `i`.`item_id`)) GROUP BY `o`.`orderinfo_id`, `o`.`status` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `orderinfo`
--
ALTER TABLE `orderinfo`
  ADD PRIMARY KEY (`orderinfo_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `shipping_address_id` (`shipping_address_id`);

--
-- Indexes for table `orderline`
--
ALTER TABLE `orderline`
  ADD PRIMARY KEY (`orderline_id`),
  ADD KEY `orderinfo_id` (`orderinfo_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `orderinfo`
--
ALTER TABLE `orderinfo`
  MODIFY `orderinfo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orderline`
--
ALTER TABLE `orderline`
  MODIFY `orderline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `orderinfo`
--
ALTER TABLE `orderinfo`
  ADD CONSTRAINT `orderinfo_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `orderinfo_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `addresses` (`address_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orderline`
--
ALTER TABLE `orderline`
  ADD CONSTRAINT `orderline_ibfk_1` FOREIGN KEY (`orderinfo_id`) REFERENCES `orderinfo` (`orderinfo_id`),
  ADD CONSTRAINT `orderline_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
