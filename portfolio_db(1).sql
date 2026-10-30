-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2026 at 03:00 PM
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
-- Database: `portfolio_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_info`
--

CREATE TABLE `about_info` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` varchar(150) NOT NULL,
  `bio` text NOT NULL,
  `cv_link` varchar(255) DEFAULT 'files/Mrunal_CV.pdf',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_info`
--

INSERT INTO `about_info` (`id`, `full_name`, `role`, `bio`, `cv_link`, `created_at`, `updated_at`, `email`, `phone`, `location`) VALUES
(1, 'Mrunal Hyalis', 'Web Developer|B.Sc. Computer Science', 'I am Mrunal Hyalis, a Web Developer and B.Sc. Computer Science graduate from Malegaon, Maharashtra. I specialize in building responsive, clean, and user-friendly websites with a focus on performance and modern design. Proficient in front-end technologies and passionate about transforming ideas into functional digital solutions, I am continuously enhancing my skills to deliver efficient and scalable web applications.', 'files/Mrunal_CV.pdf', '2026-10-10 11:14:17', '2026-10-22 13:28:42', 'manu@gmail.com', '8354648648', 'Malegaon Maharashtra');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'Mrunal_admin', 'e6e061838856bf47e1de730719fb2609', '2026-10-09 12:07:12');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Manu', 'manu@gmail.com', '8765434523', 'python', 'Hii....!!', 1, '2026-10-15 13:54:10'),
(2, 'riya', 'riya@gmail.com', '9876543217', 'web', 'Hii....', 1, '2026-10-18 13:09:37'),
(3, 'swami', 'swami@gmail.com', '6432456764', 'java', 'GM', 1, '2026-10-22 13:55:03');

-- --------------------------------------------------------

--
-- Table structure for table `cv`
--

CREATE TABLE `cv` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `education` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cv`
--

INSERT INTO `cv` (`id`, `full_name`, `email`, `phone`, `address`, `education`, `experience`, `skills`, `cv_file`, `created_at`) VALUES
(1, 'Mrunal Hyalis', 'mrunalhyalis@gmail.com', '7038501734', 'Malegaon Maharashtra', 'B.Sc Computer Science Pune university', '1 experience', 'HTML ,CSS ,Web ,PHP', NULL, '2026-10-22 17:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `home_content`
--

CREATE TABLE `home_content` (
  `id` int(11) NOT NULL,
  `section` varchar(50) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `extra_data` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_content`
--

INSERT INTO `home_content` (`id`, `section`, `title`, `subtitle`, `description`, `icon`, `extra_data`, `display_order`, `created_at`) VALUES
(1, 'hero', 'Mrunal Hyalis', 'Web Developer', 'Welcome to my professional portfolio. I am passionate about technology, web development and creating modern, responsive and user-friendly websites.', NULL, 'email:hyalismrunal306@gmail.com|phone:+91 70385 01758|location:Malegaon, Maharashtra|education:B.Sc. Computer Science|image:images/Profile.jpg', 0, '2026-10-10 11:58:22'),
(2, 'intro', 'Web Development', NULL, 'I enjoy developing modern, responsive and user-friendly websites.', 'fa-solid fa-laptop-code', NULL, 1, '2026-10-10 11:58:22'),
(3, 'intro', 'Programming', NULL, 'I enjoy learning programming, technologies and developing useful applications.', 'fa-solid fa-code', NULL, 2, '2026-10-10 11:58:22'),
(4, 'intro', 'Continuous Learning', NULL, 'I continuously improve my technical knowledge by learning new technologies.', 'fa-solid fa-lightbulb', NULL, 3, '2026-10-10 11:58:22'),
(5, 'cta', 'Let\'s Build Something Great Together', NULL, 'Have a project or opportunity? Feel free to contact me.', NULL, 'button:Get In Touch|link:contact.php', 0, '2026-10-10 11:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'web',
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'fa-solid fa-briefcase',
  `icon_color` varchar(100) DEFAULT 'linear-gradient(135deg, #3b82f6, #8b5cf6)',
  `tech_stack` varchar(255) DEFAULT NULL,
  `live_link` varchar(255) DEFAULT NULL,
  `code_link` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `icon` varchar(10) DEFAULT '?',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `skill_name`, `icon`, `display_order`, `created_at`) VALUES
(2, 'HTML', '', 0, '2026-10-22 17:34:50'),
(3, 'CSS', '', 0, '2026-10-24 17:48:47'),
(4, 'Java', '', 0, '2026-10-24 17:49:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_info`
--
ALTER TABLE `about_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cv`
--
ALTER TABLE `cv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_content`
--
ALTER TABLE `home_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_info`
--
ALTER TABLE `about_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cv`
--
ALTER TABLE `cv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `home_content`
--
ALTER TABLE `home_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
