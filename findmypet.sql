-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 06:58 PM
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
-- Database: `findmypet`
--

-- --------------------------------------------------------

--
-- Table structure for table `found_pets`
--

CREATE TABLE `found_pets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pet_name` varchar(255) NOT NULL,
  `breed` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `last_seen_location` varchar(255) NOT NULL,
  `last_seen_date` date NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `found_pets`
--

INSERT INTO `found_pets` (`id`, `user_id`, `pet_name`, `breed`, `description`, `last_seen_location`, `last_seen_date`, `photo`, `created_at`, `latitude`, `longitude`) VALUES
(1, 3, 'T Rex', 'Large Theropod', 'Tyrannosaurus rex, often shortened to T. rex, was a large, bipedal carnivorous dinosaur that lived during the Late Cretaceous period, approximately 66 to 68 million years ago.', 'Location not shared', '2025-08-10', 'uploads/6898d92e453c2_mkzm86rrnso012.jpg', '2025-08-10 17:38:54', NULL, NULL),
(7, NULL, 'Venom', 'Symbiote', 'The character is a sentient alien symbiote with an amorphous, liquid-like form, who survives by bonding with a host, usually human.', 'Location not shared', '2025-08-11', 'uploads/6898fb466433e_wallpaperflare.com_wallpaper.jpg', '2025-08-10 20:04:22', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `found_report_comments`
--

CREATE TABLE `found_report_comments` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `commented_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `found_report_comments`
--

INSERT INTO `found_report_comments` (`id`, `report_id`, `user_id`, `comment`, `commented_at`) VALUES
(1, 2, 3, 'Kai here!', '2025-08-09 02:56:26'),
(3, 2, 2, 'Yo Kai, what\'s up homie?', '2025-08-09 02:57:28'),
(12, 1, 2, 'Hey :)', '2025-08-10 19:17:48'),
(13, 1, 2, 'Hello, T Rex', '2025-08-10 19:28:57'),
(17, 7, 5, 'Scary :)', '2025-08-10 20:22:01'),
(18, 7, 7, 'Wow', '2025-08-12 15:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `lost_pet_comments`
--

CREATE TABLE `lost_pet_comments` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `commented_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lost_pet_comments`
--

INSERT INTO `lost_pet_comments` (`id`, `pet_id`, `user_id`, `comment`, `commented_at`) VALUES
(1, 1, 3, 'Poor Teddy :(', '2025-08-10 16:48:00'),
(2, 2, 2, 'Hey :)', '2025-08-10 18:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pet_name` varchar(100) DEFAULT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `last_seen_location` varchar(255) DEFAULT NULL,
  `last_seen_date` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `user_id`, `pet_name`, `breed`, `description`, `last_seen_location`, `last_seen_date`, `photo`, `created_at`) VALUES
(1, 1, 'Teddy', 'Unknown', 'Teddy is a brown, knitted teddy bear. He has white, button eyes and a black nose. His arms and legs are long and sausage like, with white hands.', 'London, England', '2025-07-19', 'uploads/1753032916_1O6ie0Jzx.jpg', '2025-07-20 17:35:16'),
(2, 3, 'Fang', 'Unknown', 'Fang, also known as Tutati is a black dobermann pinscher with a docked tail and cropped ears, who is owned by the Bruiser Family. He often antagonizes Mr. Bean and sometimes Scrapper.', 'London, England', '2025-08-08', 'uploads/1754641599_ChatGPT Image Aug 8, 2025, 02_26_16 PM.png', '2025-08-08 08:26:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `bio`, `password`, `profile_photo`, `created_at`) VALUES
(1, 'Mr Bean', 'mr.bean@gmail.com', '+447508507511', NULL, '$2y$10$xMkOvojRzPZaOXmvOZnt/eno/31glutvrKnExdEpegsSY/8BuvB8u', 'uploads/1752919356_Rowan_Atkinson.jpg', '2025-07-19 10:02:36'),
(2, 'iShowSpeed', 'ishowspeed@gmail.com', '+447508507512', NULL, '$2y$10$Jvco3oGRB2wXatnVqnUC4.QsjeyIkVsvsLQwp9KhyJrikDrfxIPFa', 'uploads/1752920954_shake.jpg', '2025-07-19 10:29:14'),
(3, 'Kai Cenat', 'kai.cenat@yahoo.com', '+447508507513', 'I love gaming :)', '$2y$10$8PDkK1RR0bgTZsUDRPMlzujm3fLktCHVm8L3pyioD9/unKhwSkw1e', 'uploads/1754637224_41za3BX5TELx.jpg', '2025-08-08 07:13:44'),
(4, 'Sophie Rain', 'sophie.rain@onlyfans.com', '+447508507514', '', '$2y$10$w.B/u2q8QkK3faJyVKuNLOkNx9b59FzX4.YHUBrkDi.x6oOL68g7S', 'uploads/profile_pics/1754706093_674df3f7e51a69d33be97dbf_Sophie Rain.png', '2025-08-09 01:56:43'),
(5, 'Cigarette Islam', 'abc@gmail.com', '+880 123456789', '', '$2y$10$QcJm1LZjjq1J0orP6PsfSuqcJqJwo/mPQs5VGG5ka3deKpjxPLK8y', 'uploads/profile_pics/1754849450_ae4a78a1-3034-4f9e-ba9a-545548f3604a.gif', '2025-08-10 17:54:32'),
(6, 'Test', 'test@gmail.com', '1234', NULL, '$2y$10$YWQGtrz3uQ1ogTzXFLlb0OxYoutOUhEL0DZsvMWFckf10rzfr.aL2', '', '2025-08-11 07:19:37'),
(7, 'Daiyan Ahmed', 'daiyan.ahmed@gmail.com', '+8801740110210', NULL, '$2y$10$8qPoIQAm/g8Krfcatdhca.IJNBUS7DkcoY3QKz17Fjzs75cjTvLLW', 'uploads/1755011199_scream.gif', '2025-08-12 15:06:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `found_pets`
--
ALTER TABLE `found_pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `found_report_comments`
--
ALTER TABLE `found_report_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lost_pet_comments`
--
ALTER TABLE `lost_pet_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `found_pets`
--
ALTER TABLE `found_pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `found_report_comments`
--
ALTER TABLE `found_report_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lost_pet_comments`
--
ALTER TABLE `lost_pet_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `found_pets`
--
ALTER TABLE `found_pets`
  ADD CONSTRAINT `found_pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `found_report_comments`
--
ALTER TABLE `found_report_comments`
  ADD CONSTRAINT `found_report_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lost_pet_comments`
--
ALTER TABLE `lost_pet_comments`
  ADD CONSTRAINT `lost_pet_comments_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`),
  ADD CONSTRAINT `lost_pet_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
