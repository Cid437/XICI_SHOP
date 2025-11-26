-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2023 at 08:11 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `db_sales2023`
--

-- --------------------------------------------------------

--
-- Table structure for table `barcode`
--

CREATE TABLE `barcode` (
  `barcode_ean` char(13) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `barcode`
--

INSERT INTO `barcode` (`barcode_ean`, `item_id`) VALUES
('2239872376872', 11),
('3453458677628', 5),
('4587263646878', 9),
('6241234586487', 8),
('6241527746363', 4),
('6241527836173', 1),
('6241574635234', 2),
('6264537836173', 3),
('6434564564544', 6),
('8476736836876', 7),
('9473625532534', 8),
('9473627464543', 8),
('9879879837489', 11);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `title` char(4) DEFAULT NULL,
  `fname` varchar(32) DEFAULT NULL,
  `lname` varchar(32) NOT NULL,
  `addressline` varchar(64) DEFAULT NULL,
  `town` varchar(32) DEFAULT NULL,
  `zipcode` char(10) NOT NULL,
  `phone` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `title`, `fname`, `lname`, `addressline`, `town`, `zipcode`, `phone`) VALUES
(1, 'Miss', 'jenny', 'stones', '27 Rowan Avenue', 'hightown', 'NT21AQ', '023 9876'),
(2, 'Mr', 'Andrew', 'stones', '52 The willows', 'lowtown', 'LT57RA', '876 3527'),
(3, 'Miss', 'Alex', 'Matthew', '4 The Street', 'Nicetown', 'NT22TX', '010 4567'),
(4, 'Mr', 'Adrian', 'MAtthew', 'The Barn', 'Yuleville', 'YV672WR', '487 3871'),
(5, 'Mr', 'Simon', 'Cozens', '7 Shady Lane', 'Oahenham', 'OA36Qw', '514 5926'),
(6, 'Mr', 'Neil', 'Matther', '5 Pasture Lane', 'Nicetown', 'NT37RT', '267 1232'),
(7, 'Mr', 'Richard', 'stones', '34 Holly Way', 'Bingham', 'BG42WE', '342 5982'),
(8, 'Mrs', 'Ann', 'stones', '34 Holly Way', 'Bingham', 'BG42WE', '342 5982'),
(9, 'Mrs', 'Christine', 'Hickman', '36 Queen Street', 'Histon', 'HT35EM', '342 5432'),
(10, 'Mr', 'Mike', 'Howard', '86 Dysart Street', 'Tibsville', 'TB37FG', '505 5482'),
(11, 'Mr', 'Dave', 'Jones', '54 Vale Rise', 'Bingham', 'BG38GD', '342 8264'),
(12, 'Mr', 'Richard', 'Neil', '42 Thached Way', 'Winersbay', 'WB36GQ', '505 6482'),
(13, 'Mrs', 'Laura', 'Hendy', '73 MArgaritta Way', 'Oxbridge', 'OX23HX', '821 2335'),
(14, 'Mr', 'Bill', 'ONeil', '2 Beamer Street', 'Welltown', 'WT38GM', '435 1234'),
(15, 'Mr', 'David', 'Hudson', '4 The Square', 'Milltown', 'MT26RT', '961 4526');





--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` varchar(64) NOT NULL,
  `cost_price` decimal(7,2) DEFAULT NULL,
  `sell_price` decimal(7,2) DEFAULT NULL,
  `image_path` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `title`, `description`, `cost_price`, `sell_price`, `image_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '', 'Wood Puzzel', '15.23', '21.95', '', NULL, NULL, NULL),
(2, '', 'Rubik Cube', '7.45', '11.49', '', NULL, NULL, NULL),
(3, '', 'Linux CD', '1.99', '2.49', '', NULL, NULL, NULL),
(4, '', 'Tissues', '2.11', '3.99', '', NULL, NULL, NULL),
(5, '', 'PIcture Frame', '7.54', '9.95', '', NULL, NULL, NULL),
(6, '', 'Fan Small', '9.23', '15.75', '', NULL, NULL, NULL),
(7, '', 'Fan Large', '13.36', '19.95', '', NULL, NULL, NULL),
(8, '', 'ToothBrush', '0.75', '1.45', '', NULL, NULL, NULL),
(9, '', 'Roman Coin', '2.34', '2.45', '', NULL, NULL, NULL),
(10, '', 'Carrier Bag', '0.01', '0.00', '', NULL, NULL, NULL),
(11, '', 'Speakers', '19.73', '25.32', '', NULL, NULL, NULL),
(12, 'Harry Potter', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit', '10.00', '15.00', 'https://images.freeimages.com/images/large-previews/c0d/little-jars-with-stones-on-the-white-background-1641022.jpg', '2023-03-07 23:11:48', '2023-03-07 23:11:48', NULL),
(13, 'Harry Potter', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit', '10.00', '15.00', 'https://images.freeimages.com/images/large-previews/c0d/little-jars-with-stones-on-the-white-background-1641022.jpg', '2023-03-07 23:11:48', '2023-03-07 23:11:48', NULL),
(14, 'Harry Potter', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit', '10.00', '15.00', 'https://images.freeimages.com/images/large-previews/c0d/little-jars-with-stones-on-the-white-background-1641022.jpg', '2023-03-07 23:11:48', '2023-03-07 23:11:48', NULL),
(15, 'Harry Potter', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit', '10.00', '15.00', 'https://images.freeimages.com/images/large-previews/c0d/little-jars-with-stones-on-the-white-background-1641022.jpg', '2023-03-07 23:11:48', '2023-03-07 23:11:48', NULL),
(16, 'Harry Potter', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit', '200.00', '15.00', 'https://images.freeimages.com/images/large-previews/c0d/little-jars-with-stones-on-the-white-background-1641022.jpg', '2023-03-07 23:11:48', '2023-03-07 23:11:48', NULL);

-- --------------------------------------------------------









-- --------------------------------------------------------

--
-- Table structure for table `orderinfo`
--

CREATE TABLE `orderinfo` (
  `orderinfo_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_placed` date NOT NULL,
  `date_shipped` date DEFAULT NULL,
  `shipping` decimal(7,2) DEFAULT NULL,
  `status` enum('Processing','Delivered','Canceled') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orderinfo`
--

INSERT INTO `orderinfo` (`orderinfo_id`, `customer_id`, `date_placed`, `date_shipped`, `shipping`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, '2000-03-13', '2000-03-17', '2.99', 'Processing', NULL, NULL),
(2, 8, '2000-06-23', '2000-06-23', '0.00', 'Processing', NULL, NULL),
(3, 15, '2000-09-02', '2000-09-12', '3.99', 'Processing', NULL, NULL),
(4, 13, '2000-09-03', '2000-09-10', '2.99', 'Processing', NULL, NULL),
(5, 8, '2000-07-21', '2000-07-24', '0.00', 'Processing', NULL, NULL),
(15, 1, '2023-03-09', '2023-03-09', '10.00', 'Processing', NULL, NULL),
(16, 1, '2023-03-09', '2023-03-09', '10.00', 'Processing', NULL, NULL),
(18, 1, '2023-03-10', '2023-03-10', '10.00', 'Processing', '2023-03-09 22:57:10', '2023-03-09 22:57:10'),
(21, 1, '2023-03-10', '2023-03-10', '10.00', 'Processing', '2023-03-09 23:20:35', '2023-03-09 23:20:35'),
(22, 1, '2023-03-10', '2023-03-10', '10.00', 'Processing', '2023-03-09 23:21:13', '2023-03-09 23:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `orderline`
--

CREATE TABLE `orderline` (
  `orderinfo_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orderline`
--

INSERT INTO `orderline` (`orderinfo_id`, `item_id`, `quantity`) VALUES
(1, 4, 1),
(1, 7, 1),
(1, 9, 1),
(2, 1, 1),
(2, 10, 1),
(2, 7, 2),
(2, 4, 2),
(3, 2, 1),
(3, 1, 1),
(4, 5, 2),
(5, 1, 1),
(5, 3, 1),
(15, 1, 1),
(15, 2, 1),
(15, 4, 1),
(16, 1, 3),
(16, 2, 2),
(18, 1, 2),
(18, 2, 2),
(18, 4, 2),
(21, 4, 1),
(21, 1, 1),
(22, 1, 1);






--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`item_id`, `quantity`) VALUES
(1, 6),
(2, 8),
(4, 7),
(5, 3),
(7, 8),
(8, 18),
(10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

-- Indexes for dumped tables
--

--
-- Indexes for table `barcode`
--
ALTER TABLE `barcode`
  ADD PRIMARY KEY (`barcode_ean`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);



--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `migrations`

-- Indexes for table `orderinfo`
--
ALTER TABLE `orderinfo`
  ADD PRIMARY KEY (`orderinfo_id`);



--
-- Indexes for table `personal_access_tokens`

-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`item_id`);

--

-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--

-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;



--
-- AUTO_INCREMENT for table `orderinfo`
--
ALTER TABLE `orderinfo`
  MODIFY `orderinfo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;



--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
