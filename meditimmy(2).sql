-- phpMyAdmin SQL Dump
-- version 5.2.2deb1+deb13u1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 28 avr. 2026 à 12:07
-- Version du serveur : 11.8.6-MariaDB-0+deb13u1 from Debian
-- Version de PHP : 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `meditimmy`
--

-- --------------------------------------------------------

--
-- Structure de la table `consultations`
--

CREATE TABLE `consultations` (
  `id` int(11) NOT NULL,
  `rendez_vous_id` int(11) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `praticien_id` int(11) NOT NULL,
  `date_consultation` datetime NOT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `examen_clinique` text DEFAULT NULL,
  `diagnostic` text DEFAULT NULL,
  `traitement_prescrit` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `consultations`
--

INSERT INTO `consultations` (`id`, `rendez_vous_id`, `patient_id`, `praticien_id`, `date_consultation`, `motif`, `examen_clinique`, `diagnostic`, `traitement_prescrit`, `notes`, `created_at`) VALUES
(111, NULL, 4, 2, '2026-04-24 13:33:00', 'Rdv', 'exam', 'diag', 'trt', 'nt', '2026-04-24 13:35:27');

-- --------------------------------------------------------

--
-- Structure de la table `dossiers`
--

CREATE TABLE `dossiers` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `antecedents` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `traitements_en_cours` text DEFAULT NULL,
  `groupe_sanguin` varchar(10) DEFAULT NULL,
  `notes_generales` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `dossiers`
--

INSERT INTO `dossiers` (`id`, `patient_id`, `antecedents`, `allergies`, `traitements_en_cours`, `groupe_sanguin`, `notes_generales`, `created_at`, `updated_at`) VALUES
(1, 4, 'Rage\r\nPeste\r\nCollerat\r\nGrippe Espagnole\r\nVariole du singe\r\nTuberculose\r\nRhume\r\nGrippe aviaire\r\nLa vache folle\r\nVIH*2', 'Gluten\r\nSoleil\r\nEau\r\nViande Rouges (suite à piquire de tiqueà', 'Monster 3* par semaines.', 'O+', 'Garder sous la main, groupe sanguin intéressant.', '2026-04-24 12:56:50', '2026-04-24 12:56:50');

-- --------------------------------------------------------

--
-- Structure de la table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nom` varchar(80) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `nom`, `prenom`, `date_naissance`, `email`, `telephone`, `adresse`, `created_at`) VALUES
(1, 4, 'Durand', 'Pierre', '1985-03-12', 'pierre.durand@email.fr', '0612345678', NULL, '2026-04-23 09:13:12'),
(2, 5, 'Bernard', 'Lucie', '1992-07-24', 'lucie.bernard@email.fr', '0698765432', NULL, '2026-04-23 09:13:12'),
(3, 6, 'Morgan', 'Morgan', '2023-05-12', 'morgan@re.re', '0604040404', 'chez lui', '2026-04-23 09:16:00'),
(4, 7, 'Motard', 'Anton1', '0012-12-12', 'anto@n1.fr', '0673273212', 'chez lui sous les pierres', '2026-04-24 12:50:49');

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `code` varchar(60) NOT NULL,
  `label` varchar(120) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `label`, `description`) VALUES
(1, 'voir_dashboard', 'Voir le tableau de bord', 'Accès à la page principale après connexion'),
(2, 'voir_agenda', 'Voir l\'agenda', 'Consultation de l\'agenda / calendrier RDV'),
(3, 'gerer_agenda', 'Gérer l\'agenda', 'Créer, modifier, déplacer, annuler des RDV'),
(4, 'voir_patients', 'Voir les fiches patients (admin)', 'Accès aux informations administratives patient'),
(5, 'modifier_patients', 'Modifier les fiches patients', 'Modifier les informations administratives patient'),
(6, 'voir_dossiers', 'Voir les dossiers médicaux', 'Accès aux dossiers médicaux complets'),
(7, 'modifier_dossiers', 'Modifier les dossiers médicaux', 'Créer / enrichir les dossiers médicaux'),
(8, 'creer_consultation', 'Saisir une consultation', 'Enregistrer les notes de consultation'),
(9, 'voir_mes_rdv', 'Voir mes rendez-vous', 'Patient : liste de ses propres RDV'),
(10, 'voir_parametres', 'Voir les paramètres', 'Accès à la page paramètres personnels'),
(11, 'gerer_comptes', 'Gérer les comptes utilisateurs', 'Admin : créer, désactiver, réinitialiser'),
(12, 'gerer_roles', 'Gérer les rôles', 'Admin : créer des rôles et attribuer permissions');

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `praticien_id` int(11) NOT NULL,
  `date_heure` datetime NOT NULL,
  `duree_minutes` int(11) DEFAULT 30,
  `motif` varchar(255) DEFAULT NULL,
  `statut` enum('planifie','confirme','annule','termine') DEFAULT 'planifie',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `patient_id`, `praticien_id`, `date_heure`, `duree_minutes`, `motif`, `statut`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-04-24 11:13:12', 30, 'Consultation générale', 'planifie', NULL, 3, '2026-04-23 09:13:12', '2026-04-23 09:13:12'),
(2, 1, 2, '2026-04-26 11:13:12', 30, 'Suivi traitement', 'planifie', NULL, 3, '2026-04-23 09:13:12', '2026-04-23 09:13:12'),
(3, 2, 2, '2026-04-25 11:13:12', 45, 'Bilan annuel', 'confirme', NULL, 3, '2026-04-23 09:13:12', '2026-04-23 09:13:12'),
(4, 1, 2, '2026-04-18 11:13:12', 30, 'Grippe', 'termine', NULL, 3, '2026-04-23 09:13:12', '2026-04-23 09:13:12'),
(5, 2, 2, '2026-04-13 11:13:12', 30, 'Renouvellement ordonnance', 'termine', NULL, 3, '2026-04-23 09:13:12', '2026-04-23 09:13:12');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(60) NOT NULL,
  `label` varchar(120) NOT NULL,
  `est_systeme` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `label`, `est_systeme`, `created_at`) VALUES
(1, 'admin', 'Administrateur local', 1, '2026-04-23 09:13:12'),
(2, 'medecin', 'Médecin', 1, '2026-04-23 09:13:12'),
(3, 'infirmier', 'Infirmier', 1, '2026-04-23 09:13:12'),
(4, 'praticien', 'Autre praticien', 1, '2026-04-23 09:13:12'),
(5, 'secretaire', 'Secrétaire', 1, '2026-04-23 09:13:12'),
(6, 'patient', 'Patient', 1, '2026-04-23 09:13:12');

-- --------------------------------------------------------

--
-- Structure de la table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(2, 4),
(3, 4),
(4, 4),
(5, 4),
(2, 5),
(3, 5),
(4, 5),
(5, 5),
(2, 6),
(3, 6),
(4, 6),
(2, 7),
(3, 7),
(4, 7),
(2, 8),
(3, 8),
(4, 8),
(6, 9),
(1, 10),
(2, 10),
(3, 10),
(4, 10),
(5, 10),
(6, 10),
(1, 11),
(1, 12);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nom` varchar(80) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  `email` varchar(180) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `mot_de_passe_temp` tinyint(1) DEFAULT 0,
  `derniere_activite` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `nom`, `prenom`, `email`, `telephone`, `role_id`, `actif`, `mot_de_passe_temp`, `derniere_activite`, `created_at`) VALUES
(1, 'admin', '$2y$12$3VZrw9kc/wB0mjyOBmznO.LjazBVDa4Fl.QL11DEMbSP0fwJGLWCy', 'Admin', 'Local', 'admin@meditimmy.fr', NULL, 1, 1, 0, NULL, '2026-04-23 09:13:12'),
(2, 'dr.martin', '$2y$12$TJLpNwn6JOy9QWzYikeRNOEvOWi0HgHdtRrNHAOi6h/vJqGjYJqZS', 'Martin', 'Sophie', 'sophie.martin@meditimmy.fr', NULL, 2, 1, 0, NULL, '2026-04-23 09:13:12'),
(3, 'sec.dupont', '$2y$12$s3.HpOAOHHnl.gInplXO2.U38zyLm595OL4Xgl/y3V4HUNJzBcrvi', 'Dupont', 'Marie', 'marie.dupont@meditimmy.fr', NULL, 5, 1, 0, NULL, '2026-04-23 09:13:12'),
(4, 'p.durand', '$2y$12$tXfUUed9ZFPVYOL6nXuasOpxXjwH2L1mZng2A4EaIxAiut5mIVLYy', 'Durand', 'Pierre', 'pierre.durand@email.fr', NULL, 6, 1, 0, NULL, '2026-04-23 09:13:12'),
(5, 'l.bernard', '$2y$12$Q6kY/gnFdqaBBYgUo6tequIKJ6lUeSlbRyop/W90qcSavWECHV29y', 'Bernard', 'Lucie', 'lucie.bernard@email.fr', NULL, 6, 1, 1, NULL, '2026-04-23 09:13:12'),
(6, 'm.morgan', '$2y$12$5MKeDGjXfLlnhAKjnC5h6expwfBErms9qpJ5MhaBm/B.WqSf3VWlK', 'Morgan', 'Morgan', 'morgan@re.re', NULL, 6, 1, 0, NULL, '2026-04-23 09:16:00'),
(7, 'a.motard', '$2y$12$N3tQ.Qk5FD0DRxyV5jCViOf2Q2uIuACNK1YpzOPM0z46F9D1lgMp2', 'Motard', 'Anton1', 'anto@n1.fr', NULL, 6, 1, 0, NULL, '2026-04-24 12:50:49');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rendez_vous_id` (`rendez_vous_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `praticien_id` (`praticien_id`);

--
-- Index pour la table `dossiers`
--
ALTER TABLE `dossiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`);

--
-- Index pour la table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `praticien_id` (`praticien_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT pour la table `dossiers`
--
ALTER TABLE `dossiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `consultations_ibfk_3` FOREIGN KEY (`praticien_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `dossiers`
--
ALTER TABLE `dossiers`
  ADD CONSTRAINT `dossiers_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Contraintes pour la table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `rendez_vous_ibfk_2` FOREIGN KEY (`praticien_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rendez_vous_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
