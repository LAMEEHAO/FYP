-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 03:31 PM
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
-- Database: `quiz_creation`
--

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, '6', 0),
(2, 1, '8', 1),
(3, 1, '7', 0),
(4, 1, '9', 0),
(5, 2, 'H2O', 1),
(6, 2, 'O2', 0),
(7, 2, 'CO2', 0),
(8, 2, 'H2O2', 0),
(9, 3, '50', 0),
(10, 3, '60', 1),
(11, 3, '70', 0),
(12, 3, '55', 0),
(13, 4, '5', 1),
(14, 4, '10', 0),
(15, 4, '7.5', 0),
(16, 4, '20', 0),
(17, 5, '8cm²', 0),
(18, 5, '12cm²', 0),
(19, 5, '16cm²', 1),
(20, 5, '20cm²', 0),
(21, 6, '9', 0),
(22, 6, '10', 0),
(23, 6, '11', 1),
(24, 6, '12', 0),
(25, 7, 'France', 0),
(26, 7, 'Brazil', 0),
(27, 7, 'Argentina', 1),
(28, 7, 'Germany', 0),
(29, 8, 'Zero', 0),
(30, 8, 'Nil', 0),
(31, 8, 'Love', 1),
(32, 8, 'Blank', 0),
(33, 9, 'Au', 1),
(34, 9, 'Ag', 0),
(35, 9, 'Fe', 0),
(36, 9, 'Pb', 0),
(37, 10, 'Venus', 0),
(38, 10, 'Mars', 1),
(39, 10, 'Jupiter', 0),
(40, 10, 'Saturn', 0),
(41, 11, 'Nucleus', 0),
(42, 11, 'Mitochondria', 1),
(43, 11, 'Ribosome', 0),
(44, 11, 'Cell membrane', 0),
(45, 12, '206', 1),
(46, 12, '300', 0),
(47, 12, '150', 0),
(48, 12, '412', 0),
(49, 13, 'Liver', 0),
(50, 13, 'Pancreas', 1),
(51, 13, 'Kidney', 0),
(52, 13, 'Heart', 0),
(53, 14, 'Liver', 0),
(54, 14, 'Brain', 0),
(55, 14, 'Skin', 1),
(56, 14, 'Lungs', 0);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `points` int(11) NOT NULL,
  `type` enum('objective','subjective') NOT NULL,
  `correct_answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `points`, `type`, `correct_answer`) VALUES
(1, 1, 'What is 5 + 3?', 1, 'objective', '8'),
(2, 2, 'What is the chemical formula of water?', 2, 'objective', 'H2O'),
(3, 1, 'What is 12 × 5?', 1, 'objective', '60'),
(4, 1, 'Solve for x: 2x + 5 = 15', 2, 'objective', '5'),
(5, 1, 'What is the area of a square with side length 4cm?', 1, 'objective', '16cm²'),
(6, 3, 'How many players are on a soccer team?', 1, 'objective', '11'),
(7, 3, 'Which country won the 2022 FIFA World Cup?', 1, 'objective', 'Argentina'),
(8, 3, 'In tennis, what is a zero score called?', 1, 'objective', 'Love'),
(9, 2, 'What is the chemical symbol for gold?', 1, 'objective', 'Au'),
(10, 2, 'Which planet is known as the Red Planet?', 1, 'objective', 'Mars'),
(11, 2, 'What is the powerhouse of the cell?', 1, 'objective', 'Mitochondria'),
(12, 4, 'How many bones are in the adult human body?', 1, 'objective', '206'),
(13, 4, 'Which organ produces insulin?', 1, 'objective', 'Pancreas'),
(14, 4, 'What is the largest organ in the human body?', 1, 'objective', 'Skin');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `subject_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `quiz_title`, `subject_id`) VALUES
(1, 'Basic Arithmetic', 1),
(2, 'Chemical Formula', 2),
(3, 'Sports Trivia', 4),
(4, 'Human Biology', 2);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_modes`
--

CREATE TABLE `quiz_modes` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `room_type` varchar(50) NOT NULL,
  `player_mode` varchar(50) NOT NULL,
  `difficulty_level` varchar(50) NOT NULL,
  `quiz_title` varchar(255) DEFAULT NULL,
  `quiz_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_modes`
--

INSERT INTO `quiz_modes` (`id`, `quiz_id`, `room_type`, `player_mode`, `difficulty_level`, `quiz_title`, `quiz_description`, `created_at`) VALUES
(1, 1, 'Public Room', 'Multiplayer', 'Hard', 'Basic Arithmetic', 'A basic algebra and geometry quiz for beginners.', '2025-04-29 02:44:03'),
(5, 2, 'Public Room', 'Solo Mode', 'Easy', 'Chemical Formula', 'Introduction to chemical formula', '2025-04-29 02:47:27'),
(6, 3, 'Public Room', 'Multiplayer', 'Medium', 'Sports Trivia', 'Test your knowledge of various sports', '2025-04-30 09:00:00'),
(7, 4, 'Public Room', 'Multiplayer', 'Medium', 'Human Biology', 'Test your knowledge of human anatomy and physiology', '2025-05-01 01:42:39'),
(9, 4, 'Public Room', 'Multiplayer', 'Medium', 'Human Biology', 'Test your knowledge of human anatomy', '2025-05-01 01:44:10');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`) VALUES
(1, 'Mathematics'),
(2, 'Science'),
(4, 'Sports'),
(5, 'Computer Science');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `options_ibfk_1` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_ibfk_1` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quizzes_ibfk_1` (`subject_id`);

--
-- Indexes for table `quiz_modes`
--
ALTER TABLE `quiz_modes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `quiz_modes`
--
ALTER TABLE `quiz_modes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `quiz_modes`
--
ALTER TABLE `quiz_modes`
  ADD CONSTRAINT `quiz_modes_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
