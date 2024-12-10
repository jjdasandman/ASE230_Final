-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 10:38 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clothing_db_final`
--

-- --------------------------------------------------------

--
-- Table structure for table `clothingpost`
--

CREATE TABLE `clothingpost` (
  `post_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `color_id` int(11) NOT NULL,
  `clothing_type_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clothingpost`
--

INSERT INTO `clothingpost` (`post_id`, `username`, `title`, `photo_url`, `color_id`, `clothing_type_id`, `material_id`, `description`, `created_at`) VALUES
(1, 'john_doe', 'Casual Red Dress', 'red_dress.jpg', 1, 1, 1, 'A comfortable red cotton shirt perfect for casual outings.', '2024-12-08 14:29:40'),
(2, 'jane_smith', 'BELT', 'belt.jpg', 2, 5, 2, 'BLAH BLAH BLAH', '2024-12-08 14:29:40'),
(3, 'john_doe', 'Warm Green Jacket', 'green_jacket.jpg', 3, 2, 4, 'A warm green wool jacket ideal for cold weather.', '2024-12-08 14:29:40'),
(4, 'admin_user', 'Classic Black Sneakers', 'vans.jpg', 4, 3, 5, 'Stylish black Vans for any occasion.', '2024-12-08 14:29:40'),
(5, 'jane_smith', 'White Sweater', 'white_sweater.jpg', 5, 4, 1, 'A cozy white cotton sweater for winter days.', '2024-12-08 14:29:40'),
(7, 'admin_user', 'test', '../uploads/test.png', 2, 1, 3, 'testing', '2024-12-09 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `clothingtype`
--

CREATE TABLE `clothingtype` (
  `clothing_type_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clothingtype`
--

INSERT INTO `clothingtype` (`clothing_type_id`, `type`) VALUES
(1, 'Shirt'),
(2, 'Jacket'),
(3, 'Pants'),
(4, 'Sweater'),
(5, 'Dress');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `color_id` int(11) NOT NULL,
  `color` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`color_id`, `color`) VALUES
(1, 'Red'),
(2, 'Blue'),
(3, 'Green'),
(4, 'Black'),
(5, 'White');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `material_id` int(11) NOT NULL,
  `material` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`material_id`, `material`) VALUES
(1, 'Cotton'),
(2, 'Polyester'),
(3, 'Leather'),
(4, 'Wool'),
(5, 'Denim');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role`) VALUES
(1, 'user'),
(2, 'admin'),
(3, 'guest');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `role_id`) VALUES
('admin_user', 'adminpass', 2),
('jane_smith', 'securepass', 1),
('john_doe', 'password123', 1),
('test', 'test', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clothingpost`
--
ALTER TABLE `clothingpost`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `clothing_type_id` (`clothing_type_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `idx_username_clothingpost` (`username`);

--
-- Indexes for table `clothingtype`
--
ALTER TABLE `clothingtype`
  ADD PRIMARY KEY (`clothing_type_id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clothingpost`
--
ALTER TABLE `clothingpost`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `clothingtype`
--
ALTER TABLE `clothingtype`
  MODIFY `clothing_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clothingpost`
--
ALTER TABLE `clothingpost`
  ADD CONSTRAINT `clothingpost_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON UPDATE CASCADE,
  ADD CONSTRAINT `clothingpost_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`color_id`),
  ADD CONSTRAINT `clothingpost_ibfk_3` FOREIGN KEY (`clothing_type_id`) REFERENCES `clothingtype` (`clothing_type_id`),
  ADD CONSTRAINT `clothingpost_ibfk_4` FOREIGN KEY (`material_id`) REFERENCES `materials` (`material_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
