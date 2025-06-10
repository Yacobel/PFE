-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 04 juin 2025 à 09:13
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `task_platform`
--

-- --------------------------------------------------------

--
-- Structure de la table `bids`
--

CREATE TABLE `bids` (
  `bid_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `executor_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `proposal_text` text DEFAULT NULL,
  `bid_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected','cancelled','done') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `bids`
--

INSERT INTO `bids` (`bid_id`, `task_id`, `executor_id`, `bid_amount`, `proposal_text`, `bid_date`, `status`) VALUES
(51, 39, 4, 9.99, 'fsthf', '2025-06-03 17:10:02', 'accepted'),
(52, 40, 4, 42.00, 'shegdjgh', '2025-06-03 17:50:23', 'accepted');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `icon`) VALUES
(1, 'Cleaning', 'House cleaning and organization tasks', 'clean.png'),
(2, 'Delivery', 'Pickup and delivery services', 'delivery.png'),
(3, 'Programming', 'Software development and coding tasks', 'code.png'),
(4, 'Design', 'Graphic design and creative work', 'design.png'),
(5, 'Handyman', 'Home repairs and maintenance', 'tools.png'),
(6, 'Virtual Assistant', 'Administrative and online support', 'assistant.png'),
(7, 'Moving', 'Help with moving homes or offices', 'moving.png'),
(8, 'Translation', 'Document and content translation', 'language.png');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `payer_id` int(11) NOT NULL,
  `payee_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','refunded','failed') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewee_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `executor_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `budget` decimal(10,2) NOT NULL,
  `deadline` datetime NOT NULL,
  `status` enum('posted','assigned','in_progress','completed','cancelled') DEFAULT 'posted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tasks`
--

INSERT INTO `tasks` (`task_id`, `client_id`, `executor_id`, `title`, `description`, `category_id`, `budget`, `deadline`, `status`, `created_at`, `accepted_at`, `location`, `image_url`, `payment_status`) VALUES
(39, 1, 4, 'dghjt', 'sdfhg', 3, 10.00, '2025-06-21 00:00:00', 'completed', '2025-06-03 17:07:41', '2025-06-03 17:10:27', 'Marocco tanger', 'uploads/tasks/task_683f2bdd4f6ff2.67293534.png', 'unpaid'),
(40, 1, 4, 'ksdyu', 'oaziuls', 7, 10.00, '2025-06-24 10:48:00', 'completed', '2025-06-03 17:48:49', '2025-06-03 17:51:01', 'Marocco tanger', 'uploads/tasks/task_683f35812a2d04.95945467.png', 'unpaid');

-- --------------------------------------------------------

--
-- Structure de la table `task_cancellations`
--

CREATE TABLE `task_cancellations` (
  `cancellation_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `executor_id` int(11) NOT NULL,
  `cancellation_date` datetime NOT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','executor') NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `name`, `email`, `password`, `role`, `registration_date`, `profile_picture`) VALUES
(1, 'YAAKOUB ELHAOUARI', 'yaakoubelhaouari040@gmail.com', '$2y$10$zJ/Q72wi/tTKYQ0a6Zq8.eDmHB.LHonVySDETPycqWC5ONJotHPcG', 'client', '2025-05-23 18:15:39', NULL),
(2, 'Test User', 'test@example.com', '$2y$10$W7ub0mKItIITUu2rH03Mde3OLVsNyJkgw4LgedRCY8IwA8/2K1pXu', 'client', '2025-05-23 18:22:29', NULL),
(3, 'YAAKOUB ELHAOUARI', 'yaakoubelhaouari00@gmail.com', '$2y$10$Q0Ob0chaaS5T0hSPxoNJQeQyOnNnGaXLYkbH30YmXsikcd/e8crga', 'client', '2025-05-24 10:19:01', NULL),
(4, 'ana', 'y@y.com', '$2y$10$7fUnJmpyCUnv9PqNeZyVsu6.A.588ahPDxyBvFxEJJomdZlby27Bm', 'executor', '2025-05-27 21:57:23', NULL),
(5, 'YAAKOUB ELHAOUARI', 'yacobelhaouariii@gmail.com', '$2y$10$QzOyHn1IyQX7WGey5l6WAug/1R0b7iGdrfnKMiPzrcQbHWw2Q0.8O', 'executor', '2025-05-31 21:35:28', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`bid_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `executor_id` (`executor_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Index pour la table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `payer_id` (`payer_id`),
  ADD KEY `payee_id` (`payee_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewee_id` (`reviewee_id`);

--
-- Index pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `executor_id` (`executor_id`);

--
-- Index pour la table `task_cancellations`
--
ALTER TABLE `task_cancellations`
  ADD PRIMARY KEY (`cancellation_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `executor_id` (`executor_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bids`
--
ALTER TABLE `bids`
  MODIFY `bid_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `task_cancellations`
--
ALTER TABLE `task_cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`executor_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payer_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`payee_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`reviewee_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`executor_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Contraintes pour la table `task_cancellations`
--
ALTER TABLE `task_cancellations`
  ADD CONSTRAINT `task_cancellations_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_cancellations_ibfk_2` FOREIGN KEY (`executor_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
