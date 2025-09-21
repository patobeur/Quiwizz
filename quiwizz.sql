SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données : `quiwizz`
--

-- --------------------------------------------------------

--
-- Structure de la table `badges`
--

CREATE TABLE `badges` (
  `id` int NOT NULL,
  `slug` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sondages`
--

CREATE TABLE `sondages` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` enum('draft','active','finished') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_admin` int NOT NULL DEFAULT '0',
  `total_points` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `pseudo`, `first_name`, `last_name`, `password_hash`, `reset_token`, `reset_expires_at`, `created_at`, `updated_at`, `is_admin`, `total_points`) VALUES
(3, 'quiwizz@quiwizz.fr', 'admin', 'admin', 'istrateur', '$2y$10$CYsB/e/mroa4UJPmp7Y5Me4eKadWUGbAOHpbCDbUzrjfom.HDI5uy', NULL, NULL, '2025-09-20 14:23:57', '2025-09-20 14:23:57', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `user_badges`
--

CREATE TABLE `user_badges` (
  `user_id` int NOT NULL,
  `badge_id` int NOT NULL,
  `level` int DEFAULT '1',
  `awarded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `sondages`
--
ALTER TABLE `sondages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- Index pour la table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`user_id`,`badge_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sondages`
--
ALTER TABLE `sondages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_badges_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE;
COMMIT;