-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-12-09 04:08:39
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `roomfinder`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `target_type`, `target_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 23, 'verify_user', 'user', 21, 'User verified', NULL, '2025-11-21 02:18:28'),
(2, 23, 'unverify_user', 'user', 21, 'User verification removed', NULL, '2025-11-21 03:17:35'),
(3, 23, 'verify_user', 'user', 21, 'User verified', NULL, '2025-11-21 03:17:46'),
(4, 23, 'reject_property', 'property', 17, 'Property rejected', NULL, '2025-12-01 04:39:46'),
(5, 23, 'approve_property', 'property', 17, 'Property approved', NULL, '2025-12-02 02:33:27'),
(6, 23, 'unverify_user', 'user', 21, 'User verification removed', NULL, '2025-12-05 04:18:18'),
(7, 23, 'verify_user', 'user', 23, 'User verified', NULL, '2025-12-05 04:18:23'),
(8, 23, 'verify_user', 'user', 21, 'User verified', NULL, '2025-12-05 04:18:25');

-- --------------------------------------------------------

--
-- テーブルの構造 `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'RoomFinder', '2025-11-21 01:47:59'),
(2, 'site_email', 'admin@roomfinder.com', '2025-11-21 01:47:59'),
(3, 'items_per_page', '20', '2025-11-21 01:47:59'),
(4, 'auto_approve_properties', '0', '2025-11-21 01:47:59');

-- --------------------------------------------------------

--
-- テーブルの構造 `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(13, 'HEMANT KHATRI', 'aakash@gmail.com', 'Test Message', '2025-11-14 07:07:42');

-- --------------------------------------------------------

--
-- テーブルの構造 `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `inquiries`
--

INSERT INTO `inquiries` (`id`, `room_id`, `name`, `email`, `phone`, `visit_date`, `message`, `created_at`) VALUES
(3, 17, 'JIBRAJ KC', 'kchemant073@gmail.com', '07091514944', '2025-11-22', 'When i will come to visit ? this time is Ok', '2025-11-14 05:40:46'),
(4, 17, 'HEMANT KHATRI', 'test@gmail.com', '07091519944', '2025-11-29', 'Check Message', '2025-11-14 06:56:35'),
(5, 17, 'PRabin', 'test@gmail.com', '07091519944', '2025-11-20', 'TEST MEssage', '2025-11-18 01:56:01'),
(6, 17, 'HEMANT KHATRI', 'admin@npltrader.com', '+8107091519944', '2025-12-02', 'this is for test message', '2025-12-01 04:37:12');

-- --------------------------------------------------------

--
-- テーブルの構造 `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `room_id`, `subject`, `message`, `is_read`, `created_at`) VALUES
(5, 22, 21, 17, 'Inquiry about: BEST ROOM IN YOKOHAMA', 'Hello i need details room of this,,, can you expalin more details', 1, '2025-11-21 00:29:56'),
(6, 21, 22, NULL, NULL, 'yes, ofcourse,, you can choose anything,, for furthere details , conatc me in this message or , direct you can email for me', 1, '2025-11-21 00:50:19'),
(7, 22, 21, NULL, NULL, 'ok,, than i will contact you', 1, '2025-11-21 01:00:28'),
(8, 22, 21, NULL, NULL, 'Hello i need details room of this,,, can you expalin more details', 1, '2025-11-21 01:39:17'),
(9, 21, 22, NULL, NULL, 'test message', 1, '2025-11-21 03:20:50'),
(10, 21, 22, NULL, NULL, 'chat', 1, '2025-11-21 05:43:40'),
(11, 21, 22, NULL, NULL, 'Hello i need details room of this,,, can you expalin more details', 1, '2025-12-01 04:43:37');

-- --------------------------------------------------------

--
-- テーブルの構造 `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `utilities_cost` decimal(10,2) DEFAULT 0.00,
  `management_fee` decimal(10,2) DEFAULT 0.00,
  `deposit` decimal(10,2) DEFAULT 0.00,
  `key_money` decimal(10,2) DEFAULT 0.00,
  `type` varchar(50) DEFAULT NULL,
  `train_station` text NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(11) NOT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `properties`
--

INSERT INTO `properties` (`id`, `user_id`, `title`, `location`, `price`, `utilities_cost`, `management_fee`, `deposit`, `key_money`, `type`, `train_station`, `description`, `status`, `is_approved`, `image_url`, `created_at`) VALUES
(17, 21, 'BEST ROOM IN YOKOHAMA', 'JAPAN, YOKOHAMA', 45000, 0.00, 0.00, 0.00, 0.00, 'double', 'SAKURAGITCHYO', 'Best for sharing people', 'Reserved', 1, 'uploads/room_1763098778_5856.jpg', '2025-11-14 05:39:38');

-- --------------------------------------------------------

--
-- テーブルの構造 `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `rooms`
--

INSERT INTO `rooms` (`id`, `user_id`, `title`, `location`, `price`, `type`, `description`, `image`, `created_at`) VALUES
(19, 15, 'room', 'yokohama', 40000, 'double', 'test', 'uploads/room_1758168976_9794.jpg', '2025-09-18 04:16:16'),
(20, 15, 'Cheap Room in Japan', 'Hokkaido', 30000, 'single', 'Best Room', 'uploads/room_1758169011_6153.jpg', '2025-09-18 04:16:51');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('owner','seeker') DEFAULT 'seeker',
  `is_verified` tinyint(1) DEFAULT 0,
  `is_admin` tinyint(1) DEFAULT 0,
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_verified`, `is_admin`, `profile_photo`) VALUES
(21, 'HEMANT KHATRI', 'khatrihemant100@gmail.com', '$2y$10$6xe8G7NLK03h6kVsSedi5eXazvkT79aZFYJAXwLcSSBGOhatw5Fr.', 'owner', 1, 0, 'profile_21_1763434685.jpg'),
(22, 'ROOM FINDER', 'test@gmail.com', '$2y$10$ApW3XEpcdlnYsqfD/IqOc.T.BuxKTOw23JqhojU8GffPiAebdrSU2', 'seeker', 0, 0, NULL),
(23, 'HEMANT KHATRI', 'admin@gmail.com', '$2y$10$oEY08gqdap3qStjWbb8PK.HAAl1ilWeutdmLLVB9dCnyDAswmmMdW', 'owner', 1, 1, 'profile_23_1764564035.jpg'),
(24, 'yse', 'youhei_yoshikawa@yse-c.net', '$2y$10$aW0Eyw5HoFcV6d2Vv0LipOF/dkeC8HcbzNCshgjcq.OCxrj1b/e0m', 'seeker', 0, 0, NULL);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `created_at` (`created_at`);

--
-- テーブルのインデックス `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- テーブルのインデックス `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- テーブルのインデックス `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `idx_messages_conversation` (`sender_id`,`receiver_id`,`created_at`),
  ADD KEY `idx_messages_unread` (`receiver_id`,`is_read`,`created_at`);

--
-- テーブルのインデックス `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_properties_approved` (`is_approved`,`created_at`);

--
-- テーブルのインデックス `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_verified` (`is_verified`,`role`),
  ADD KEY `idx_users_admin` (`is_admin`,`role`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- テーブルの AUTO_INCREMENT `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- テーブルの AUTO_INCREMENT `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- テーブルの AUTO_INCREMENT `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL;

--
-- テーブルの制約 `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
