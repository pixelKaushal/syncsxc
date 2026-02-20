-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 05:10 PM
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
-- Database: `syncsxc`
--

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `club_code` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo_path` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`id`, `name`, `email`, `club_code`, `description`, `logo_path`) VALUES
(1, 'The Set Council', 'thesetcouncil@sxc.edu.np', 'SET', 'Where innovation meets impact, SET Council empowers students to explore science, sustainability, and social good. From building solutions to raising awareness, it’s all about learning by doing. Join the force behind the SET Exhibition and Walkathon, where ideas walk, and change begins. ', 'setsxc.png'),
(2, 'SXC SPORTS CLUB', 'sxcsportsclub@sxc.edu.np', 'SPORTS', 'The Sports Club is a vital part of the +2 extracurricular landscape at St. Xavier’s College, Maitighar. Operating under the guidance of the club moderators, the club’s primary goal is to foster physical wellbeing and a sense of healthy competition among students. The club is famously responsible for coordinating the Annual Sports Day and other sports tournaments', 'sportssxc.png'),
(3, 'SXC COMPUTER CLUB', 'sx3c@sxc.edu.np', 'SX3C', 'The SXC Computer Club, popularly known as SX3C, is the premier technology-focused student organization for the +2 level at St. Xavier’s College, Maitighar. Established in 2018, its motto is \"Code, Compute, Create.\" The club serves as a family for students and staff to explore the world of programming, software development, and modern IT trends beyond the standard academic syllabus', 'sx3c.jpg'),
(4, 'Magis Club SXC', 'magis@sxc.edu.np', 'MAGIS', 'SXC MAGIS invites you on a journey of purpose, rooted in Ignatian values and driven by self-discovery. Through service, spirituality, culture, and creativity, students grow deeper in faith, connection, and meaning. Live the Magis spirit—be more, do more, become your best self.', 'magissxc.png'),
(5, 'Art and Culture Club', 'artandculture@sxc.edu.np', 'ART', 'Unleash your creativity and celebrate cultural diversity with the Art and Culture Club! From Talent Hunts to Art Exhibitions and Heritage Walks, we bring imagination to life. Join a community where every brushstroke, beat, and performance tells a story. ', 'artsxc.png'),
(6, 'CLUB DE CHEMIA', 'clubdechemia@sxc.edu.np', 'Chemistry', 'Explore the elements of innovation with Club de Chemia—where chemistry meets creativity. From scientific exploration to community impact, we’re dedicated to serving through chemistry. Join us in shaping the future, one molecule at a time.', 'chemsxc.png'),
(7, 'Alumni club', 'plus2alumni@sxc.edu.np', 'Alumni Clu', 'Once a Xavierian, always a Xavierian, our Alumni Club keeps the legacy alive. We reconnect, celebrate, and support one another across generations. Join a lifelong network where memories meet mentorship and tradition meets tomorrow. ', 'sxcalumini.jpg'),
(8, 'Physics Club', 'sxcphysicsclub@sxc.edu.np', 'Physics', 'Curious about the universe? So are we! The SXC Physics Club is where wonder meets discovery. From epic experiments to mind-bending challenges, we explore physics in the most exciting ways. Join our community of thinkers, tinkerers, and future physicists—let’s question, learn, and thrive together.  ', 'sxcphysics.png'),
(9, 'Universal Solidarity Movement', 'usmn@sxc.edu.np', 'USMN', 'Be the change you wish to see, USMN empowers students to lead with purpose and serve with heart. Rooted in Gandhian values and ethical leadership, we stand for solidarity and social impact. Join the movement where self-transformation sparks real-world change. ', 'usmnsxc.png'),
(10, 'SXC AGORA LITERARY CLUB', 'agoralitclub@sxc.edu.np', 'Agora', 'Words shape worlds—at the Literary Club, we read, write, and revel in the power of expression. From poetry slams to story nights, we celebrate voices, ideas, and imagination. Join our circle of storytellers and let your words leave a lasting mark. ', 'literarysxc.jpg'),
(11, ' Sodalitas de Mathematica', 'sdm@sxc.edu.np', 'Maths club', 'Math is more than numbers—it’s a way of life at Sodalitas de’ Mathematica. From Mathletics to Olympiad prep, we dive deep into logic, learning, and lively problem-solving. Join our community of thinkers where every equation leads to discovery. ', 'mathssxc.jpg'),
(12, 'SXC Ecosphere Club', 'ecosphere@sxc.edu.np', 'Ecosphere', 'Explore nature, raise awareness, and protect the planet with the Ecosphere—where creativity fuels conservation. From eco-drama to environmental art, we engage and inspire sustainable action for a greener future. Join us in making a difference—explore, protect, and live the green life.', 'ecosxc.png');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `proposed_date` date DEFAULT NULL COMMENT 'The date requested by the club',
  `real_date` date DEFAULT NULL COMMENT 'The date confirmed by the college admin',
  `venue` varchar(100) DEFAULT 'TBD',
  `is_multistep` tinyint(1) DEFAULT 0 COMMENT '1 if event has sub-categories like Physics, Biology',
  `is_team_event` tinyint(1) DEFAULT 0,
  `min_team_size` int(11) DEFAULT 1,
  `max_team_size` int(11) DEFAULT 1,
  `approval_status` enum('Draft','Pending','Approved','TBD','Rejected') DEFAULT 'Pending',
  `admin_remarks` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0 COMMENT 'Master switch for visibility on timeline',
  `rulebook_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `club_id`, `title`, `description`, `proposed_date`, `real_date`, `venue`, `is_multistep`, `is_team_event`, `min_team_size`, `max_team_size`, `approval_status`, `admin_remarks`, `is_published`, `rulebook_url`, `created_at`, `price`) VALUES
(1, 1, 'THE SET EXHIBITION', 'The SET Exhibition (Social Service, Environment, and Technology) is a multidisciplinary festival where students from both the Science and Management streams collaborate to present projects that solve real-world problems. It serves as a platform for students to apply classroom theories to practical, tangible models.', '2027-02-09', NULL, 'St. Xavier\'s College, Maitighar', 1, 1, 1, 5, 'Approved', NULL, 0, NULL, '2026-02-09 15:29:05', '1500'),
(2, 3, 'SXC Science Fest', 'The Science Fest is usually a collaborative effort between the departmental clubs. It aims to push the boundaries of the +2 science curriculum by challenging students with complex, out-of-the-box scientific tasks.', '2026-08-29', NULL, 'St. Xavier\'s College', 1, 1, 3, 3, 'Approved', NULL, 0, NULL, '2026-02-09 15:33:03', '450'),
(3, 2, 'Annual Sports Day', 'The SXC Annual Sports Week is where \'Live for God, Lead for Nepal\' meets the field. Over 48 hours, students compete in 15+ sporting categories to bring glory to their House. Track your House\'s progress in real-time on the SyncSXC Leaderboard and never miss a match schedule again.', '2026-12-27', NULL, 'TBD', 0, 0, 1, 1, 'Approved', NULL, 0, NULL, '2026-02-09 15:36:55', '100'),
(4, 3, 'InnoVenture', 'Where innovation meets venture. Pitch your tech-business ideas to a panel of experts.', '2026-09-05', NULL, 'LCR', 1, 1, 3, 3, 'Approved', NULL, 0, NULL, '2026-02-10 13:28:21', '150'),
(5, 11, 'Integration Bee', 'The ultimate calculus showdown. Solve integrals faster than the clock to win the title.', '2026-06-12', NULL, 'Loyola Hall', 0, 0, 1, 1, 'Approved', NULL, 0, NULL, '2026-02-10 13:28:58', '0'),
(6, 6, 'Chemical Industry Visit', 'An industrial tour to observe large-scale chemical processes in action.', '2026-03-20', NULL, 'Industrial Area (Off-Campus)', 0, 0, 1, 1, 'Pending', NULL, 0, NULL, '2026-02-10 13:28:58', '0'),
(7, 4, 'Magis Hike', 'A spiritual and physical journey through nature. Finding the \"Magis\" in the hills of Kathmandu.', '2026-10-15', NULL, 'TBD', 0, 0, 1, 1, 'Pending', NULL, 0, NULL, '2026-02-10 13:28:58', '0'),
(8, 11, 'Mathletics', 'Who says math can\'t be active? Join us for a day of math-based puzzles and physical challenges.', '2026-07-22', NULL, 'St. Xavier\'s College', 0, 1, 3, 3, 'Pending', NULL, 0, NULL, '2026-02-10 13:28:58', '0'),
(9, 5, 'SXC Art Exhibition', 'A showcase of creativity featuring the best paintings, sketches, and sculptures by Xavierians.', '2026-11-05', NULL, 'watrin hall', 1, 0, 1, 1, 'Pending', NULL, 0, NULL, '2026-02-10 13:28:58', '0'),
(10, 7, 'Grand Alumni Meet', 'Reconnect with old friends and network with successful Xavierian graduates.', '2026-12-28', NULL, 'st. xavier\'s college', 0, 0, 1, 1, 'Pending', NULL, 0, NULL, '2026-02-10 13:28:58', '0'),
(12, 1, 'SXC SET WALKATHON', 'walking for a cause.', '2027-01-03', NULL, 'St. Xavier\'s College', 0, 0, 1, 1, 'Approved', NULL, 0, NULL, '2026-02-11 16:02:03', '300'),
(18, 10, 'pravaha kavi ghosti', 'lets listen to poems', '2026-10-22', NULL, 'St. Xavier\'s College', 0, 0, 1, 1, 'Pending', NULL, 0, NULL, '2026-02-12 16:19:52', '0');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `status` enum('registered','attended','cancelled','no_show') DEFAULT 'registered',
  `payment_status` enum('pending','paid','free','refunded') DEFAULT 'pending',
  `payment_amount` decimal(10,2) DEFAULT 0.00,
  `team_name` varchar(100) DEFAULT NULL,
  `team_members` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`team_members`)),
  `checked_in_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `user_id`, `event_id`, `club_id`, `registration_date`, `status`, `payment_status`, `payment_amount`, `team_name`, `team_members`, `checked_in_at`, `cancelled_at`, `cancellation_reason`, `notes`, `updated_at`) VALUES
(1, 1, 2, 3, '2026-02-14 12:43:45', 'registered', 'pending', 450.00, 'sobozlaiis', '[{\"email\":\"024neb903@sxc.edu.np\"},{\"email\":\"sx3c@sxc.edu.np\"}]', NULL, NULL, NULL, NULL, '2026-02-14 12:43:45'),
(2, 13, 1, 1, '2026-02-14 12:49:46', 'attended', 'paid', 1500.00, 'dominiks', '[{\"email\":\"sx3c@sxc.edu.np\"}]', '2026-02-14 13:56:34', NULL, NULL, NULL, '2026-02-20 11:46:17'),
(4, 13, 5, 11, '2026-02-14 13:22:31', 'registered', 'paid', 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 21:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `primary_email` varchar(150) NOT NULL,
  `recovery_email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `club_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `primary_email`, `recovery_email`, `password`, `role`, `created_at`, `club_id`) VALUES
(1, 'thesetcouncil@sxc.edu.np', 'testingplease@gmail.com', '$2y$10$b/y1dRhsE9LaiGaqTPRQnOcF0JgFDI8fwo4dqAham8.kUEiKfUsqW', 'admin', '2026-02-11 15:37:39', 1),
(2, 'sxcsportsclub@sxc.edu.np', 'test@gmail.com', '$2y$10$sedNIWt0hbI0A7RZZZDnRuEC8jPgwvheqgR6BCjefQ5ewRSolaKqO', 'admin', '2026-02-11 15:40:34', 2),
(3, 'sx3c@sxc.edu.np', 'test@gmail.com', '$2y$10$SzPDglO.D.AUXupzeME8FOxosnQFXK7WU6CSgwrawB4mzzCOmBzxy', 'admin', '2026-02-11 15:45:39', 3),
(4, 'magis@sxc.edu.np', 'test@gmail.com', '$2y$10$8VQQMbJsX.uyefEUJxWgWue/dM583JZKxkn11OkG8SXanx7yP/2Hu', 'admin', '2026-02-11 15:47:26', 4),
(5, 'artandculture@sxc.edu.np', 'test@gmail.com', '$2y$10$bSBw0/.UgwGrkxmiLAXeXe4A2P/cFVvrBaIRcKdiZm.DZ0ah7yhCC', 'admin', '2026-02-11 15:47:47', 5),
(6, 'clubdechemia@sxc.edu.np', 'test@gmail.com', '$2y$10$sRRDjsxaLTq/Sx7udTaV9ugAluti4F8qeSTLlazhXV.qebrjGBJ4q', 'admin', '2026-02-11 15:48:03', 6),
(7, 'plus2alumni@sxc.edu.np', 'test@gmail.com', '$2y$10$93J1g6.B01DSbfRWhAJaXOHG8SSkturrnylzcFCjbarM4z93/zOjy', 'admin', '2026-02-11 15:48:20', 7),
(8, 'sxcphysicsclub@sxc.edu.np', 'test@gmail.com', '$2y$10$uiiZvS9pPDgoIUcse.vINeyHZHuUutvcBQWnQ3rmpoMJoKpTiH.cq', 'admin', '2026-02-11 15:48:39', 8),
(9, 'usmn@sxc.edu.np', 'test@gmail.com', '$2y$10$D9esND9fwLIOEnCA2PS9yOxlnx/DsLW2bs7HkHPwZkqDva8DzU05a', 'admin', '2026-02-11 15:49:12', 9),
(10, 'agoralitclub@sxc.edu.np', 'test@gmail.com', '$2y$10$rG.he2OBLIutkBIvoG1u..lYmUP21daxIMjimhIm5WOeJhQd316Hi', 'admin', '2026-02-11 15:49:27', 10),
(11, 'sdm@sxc.edu.np', 'test@gmail.com', '$2y$10$t1i4I9rOUN5cWneAl1SNAerrNj2LbXW9j1K7sBJ/ZOJVfWoLkjm1G', 'admin', '2026-02-11 15:49:53', 11),
(12, 'ecosphere@sxc.edu.np', 'test@gmail.com', '$2y$10$JlphXlv0uRpWKtfdz0aXneQsw6pSPUp/xlJQdzDoXiSRlqiPkDOEi', 'admin', '2026-02-11 15:50:13', 12),
(13, '024neb903@sxc.edu.np', 'gautamkaushal381@gmail.com', '$2y$10$ogH07rj5HXu/SvU5zGsyauw19zvx1r79TM1kR64yPqggwK1oMWYmK', 'student', '2026-02-12 14:54:04', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `primary_email` (`primary_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
