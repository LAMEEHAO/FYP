-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 02:50 PM
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
-- Database: `badges_achievement`
--

-- --------------------------------------------------------

--
-- Table structure for table `badge_assignments`
--

CREATE TABLE `badge_assignments` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_score` int(11) NOT NULL,
  `badge_title` varchar(255) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badge_assignments`
--

INSERT INTO `badge_assignments` (`id`, `student_name`, `student_score`, `badge_title`, `quiz_id`, `assigned_at`) VALUES
(6, 'Kim', 34, '🎯 Keep Trying', 2, '2025-05-02 12:44:21'),
(8, 'Ben', 50, '👏 Good Effort', 1, '2025-05-02 12:45:22'),
(9, 'Alice', 90, '🏅 90% and Above', 3, '2025-05-02 12:45:51'),
(10, 'Ethan', 77, '🎖️ Excellent', 4, '2025-05-02 12:46:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badge_assignments`
--
ALTER TABLE `badge_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_quiz_id` (`quiz_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badge_assignments`
--
ALTER TABLE `badge_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `badge_assignments`
--
ALTER TABLE `badge_assignments`
  ADD CONSTRAINT `fk_quiz_id` FOREIGN KEY (`quiz_id`) REFERENCES `quiz_creation`.`quizzes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
