-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 06:28 PM
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
-- Database: `ecoclub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `target_goal` decimal(10,2) NOT NULL DEFAULT 50.00,
  `event_date` date NOT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `status` enum('open','ongoing','closed','cancelled') DEFAULT 'open',
  `organizer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `target_goal`, `event_date`, `event_time`, `venue`, `status`, `organizer_id`) VALUES
(1, 'Beach Cleanup', NULL, 50.00, '2026-05-20', NULL, NULL, 'closed', 4),
(2, 'Campus Recycling Awareness Campaign', 'This event is to raise the awarness of student about the importance of recycling !!', 50.00, '2026-02-12', '00:55:00', 'MPH', 'ongoing', 4),
(3, 'Campus Stray Cats Shelter', 'Build the campus kitty a shelter', 50.00, '2026-03-02', '14:00:00', 'MMU Land ', 'ongoing', 5),
(4, 'Green Collect Recycling Initiative', 'This initiative focuses on collecting recyclable materials to minimize waste and support a cleaner, more sustainable campus environment.', 15.00, '2026-02-07', '17:03:00', 'MMU Land ', 'ongoing', 5);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `photo_evidence` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `points_awarded` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `user_id`, `event_id`, `category`, `weight`, `photo_evidence`, `status`, `submission_date`, `points_awarded`) VALUES
(3, 1, 1, 'Plastic', 10.00, 'EcoClub\\uploads\\1770112858_1.png', 'approved', '2026-02-03 10:00:58', 10),
(4, 1, 2, 'Paper', 5.00, 'uploads/1770136436_1.png', 'rejected', '2026-02-03 16:33:56', NULL),
(5, 1, 2, 'Plastic', 5.00, 'uploads/1770137231_1.png', 'approved', '2026-02-03 16:47:11', 5),
(6, 2, 1, 'Tin', 10.00, 'uploads/1770139677_2.png', 'approved', '2026-02-03 17:27:57', 10),
(7, 2, 1, 'Aluminium', 7.00, 'uploads/1770139707_2.png', 'approved', '2026-02-03 17:28:27', 7),
(8, 1, 2, 'Paper', 5.00, 'uploads/1770184304_1.png', 'approved', '2026-02-04 05:51:44', 5),
(9, 2, 1, 'Aluminium', 2.00, 'uploads/1770196679_2.png', 'approved', '2026-02-04 09:17:59', 2),
(10, 2, 1, 'Plastic', 10.00, 'uploads/1770344319_2.png', 'approved', '2026-02-06 02:18:39', 10),
(11, 1, 1, 'Glass', 11.00, 'uploads/1770367353_1.png', 'approved', '2026-02-06 08:42:33', 11),
(12, 6, 4, 'Glass', 10.00, 'uploads/1770397639_6.png', 'rejected', '2026-02-06 17:07:19', NULL),
(13, 6, 4, 'Glass', 7.00, 'uploads/1770397685_6.png', 'approved', '2026-02-06 17:08:05', 7),
(14, 2, 4, 'Aluminium', 5.00, 'uploads/1770398268_2.png', 'pending', '2026-02-06 17:17:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'URGENT UPDATE', 'ssss', 1, '2026-01-31 12:25:17'),
(2, 2, 'Great Job ', 'Your log for Beach Cleanup was approved.', 1, '2026-01-22 21:28:53'),
(3, 1, 'Log Approved', 'Your recycling log was approved! You earned 5 points.', 1, '2026-02-04 05:51:58'),
(4, 1, 'HAH', 'yaaaa', 1, '2026-02-06 02:24:14'),
(5, 2, 'HAH', 'yaaaa', 1, '2026-02-06 02:24:14'),
(6, 2, 'Log Approved', 'Your recycling log was approved! You earned 10 points.', 1, '2026-02-06 02:26:33'),
(7, 2, 'Log Approved', 'Your recycling log was approved! You earned 2 points.', 1, '2026-02-06 08:37:06'),
(10, 2, 'Log Approved', 'Your recycling log was approved! You earned 10 points.', 1, '2026-02-06 08:55:11'),
(14, 6, 'Log Approved', 'Your recycling log was approved! You earned 7 points.', 1, '2026-02-06 17:08:23'),
(15, 1, 'Log Approved', 'Your recycling log was approved! You earned 11 points.', 1, '2026-02-06 17:14:58'),
(16, 1, 'Event Goal Reached! ', 'We hit our goal of 50 kg for \'Beach Cleanup\'! The event is now closed.', 1, '2026-02-06 17:14:58'),
(17, 2, 'Event Goal Reached! ', 'We hit our goal of 50 kg for \'Beach Cleanup\'! The event is now closed.', 1, '2026-02-06 17:14:58'),
(18, 6, 'Event Goal Reached! ', 'We hit our goal of 50 kg for \'Beach Cleanup\'! The event is now closed.', 1, '2026-02-06 17:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

CREATE TABLE `proposals` (
  `proposal_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `target_goal` decimal(10,2) NOT NULL DEFAULT 50.00,
  `organizer_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposals`
--

INSERT INTO `proposals` (`proposal_id`, `title`, `event_date`, `event_time`, `venue`, `description`, `target_goal`, `organizer_id`, `status`) VALUES
(1, 'Campus Recycling Awareness Campaign', '2026-02-12', '00:55:00', 'MPH', 'This event is to raise the awarness of student about the importance of recycling !!', 50.00, 4, 'Approved'),
(2, 'LakeSide cleanup ', '2026-02-18', '23:14:00', 'Cyberjaya lake', 'Help to provide a clean and comfort to the people who do exercise there', 50.00, 4, 'Rejected'),
(3, 'Campus Stray Cats Shelter', '2026-03-02', '14:00:00', 'MMU Land ', 'Build the campus kitty a shelter', 50.00, 5, 'Approved'),
(5, 'Clean-up Drives', '2026-02-16', '04:48:00', 'MPH', 'The Campus Clean-Up Drive helps keep the campus clean while promoting environmental awareness and sustainability.', 10.00, 4, 'pending'),
(6, 'Green Collect Recycling Initiative', '2026-02-07', '17:03:00', 'MMU Land ', 'This initiative focuses on collecting recyclable materials to minimize waste and support a cleaner, more sustainable campus environment.', 15.00, 5, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('volunteer','participant') NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `task_or_instruction` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `event_id`, `user_id`, `role`, `registered_at`, `task_or_instruction`) VALUES
(1, 1, 1, 'participant', '2026-02-03 09:58:50', 'Clean the longkang'),
(2, 1, 2, 'participant', '2026-02-03 10:58:58', 'get outtt'),
(3, 2, 1, 'volunteer', '2026-02-03 15:37:48', NULL),
(4, 2, 2, 'volunteer', '2026-02-03 17:27:30', NULL),
(5, 3, 1, 'volunteer', '2026-02-06 15:10:05', 'Recycle the cat\'s old shelter box, give the new one to them'),
(6, 1, 6, 'volunteer', '2026-02-06 16:10:15', NULL),
(7, 2, 6, 'participant', '2026-02-06 16:10:26', NULL),
(8, 3, 2, 'volunteer', '2026-02-06 16:23:20', 'Bring all the cat food tin to recycle'),
(9, 4, 6, 'volunteer', '2026-02-06 17:06:37', 'DO SOMETHING'),
(10, 4, 2, 'participant', '2026-02-06 17:17:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(225) NOT NULL,
  `role` enum('admin','eo','student') NOT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `points`) VALUES
(1, 'Ali', '123', 'ali@test.com', 'student', 31),
(2, 'khoo', '123', 'khoo@gmail.com', 'student', 29),
(3, 'ilie', '123', 'ilie@gmail.com', 'admin', 0),
(4, 'cincau', '123', 'cincau@gmail.com', 'eo', 0),
(5, 'Heng', '123', 'heng@gmail.com', 'eo', 0),
(6, 'taru', '123', 'nadi@gmail.com', 'student', 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `student_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `student_id` (`user_id`);

--
-- Indexes for table `proposals`
--
ALTER TABLE `proposals`
  ADD PRIMARY KEY (`proposal_id`),
  ADD KEY `fk_proposals_users` (`organizer_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `fk_registrations_users` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `proposals`
--
ALTER TABLE `proposals`
  MODIFY `proposal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `proposals`
--
ALTER TABLE `proposals`
  ADD CONSTRAINT `fk_proposals_users` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `fk_registrations_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
