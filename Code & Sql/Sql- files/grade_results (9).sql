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
-- Database: `grade_results`
--

-- --------------------------------------------------------

--
-- Table structure for table `grade_results`
--

CREATE TABLE `grade_results` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `grade` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_results`
--

INSERT INTO `grade_results` (`id`, `student_name`, `quiz_id`, `grade`) VALUES
(10, 'Blue', 1, 34),
(21, 'Kim', 2, 50),
(22, 'Alice', 3, 90),
(23, 'Ethan', 4, 77);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grade_results`
--
ALTER TABLE `grade_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_quiz_id` (`quiz_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grade_results`
--
ALTER TABLE `grade_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grade_results`
--
ALTER TABLE `grade_results`
  ADD CONSTRAINT `fk_quiz_id` FOREIGN KEY (`quiz_id`) REFERENCES `quiz_creation`.`quizzes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
