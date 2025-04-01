-- -----------------------------------------------------
-- Base de donnees pour la gestion des stages
-- Basee sur la matrice des roles fournie
-- -----------------------------------------------------

DROP DATABASE IF EXISTS gestion_stages;
CREATE DATABASE IF NOT EXISTS gestion_stages CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE gestion_stages;

-- -----------------------------------------------------
-- Table des roles utilisateurs
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL UNIQUE,
  description TEXT,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table des permissions
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  nom VARCHAR(100) NOT NULL,
  description TEXT,
  categorie VARCHAR(100) NOT NULL,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table d'association roles-permissions
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS role_permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY role_permission_unique (role_id, permission_id),
  CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE,
  CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_role` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des entreprises
CREATE TABLE IF NOT EXISTS `entreprises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `secteur_activite` varchar(100) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `pays` varchar(50) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `description` text,
  `logo` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des offres d'emploi/stage liées aux entreprises
CREATE TABLE IF NOT EXISTS `offres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entreprise_id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `type` enum('stage','emploi','alternance') NOT NULL,
  `description` text NOT NULL,
  `competences_requises` text,
  `duree` varchar(50) DEFAULT NULL,
  `date_publication` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_expiration` date DEFAULT NULL,
  `statut` enum('active','inactive','pourvue') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `fk_offres_entreprise` (`entreprise_id`),
  CONSTRAINT `fk_offres_entreprise` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des évaluations d'entreprises
CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entreprise_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note` int(1) NOT NULL CHECK (`note` BETWEEN 1 AND 5),
  `commentaire` text,
  `date_evaluation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_evaluation` (`entreprise_id`, `user_id`),
  KEY `fk_evaluations_entreprise` (`entreprise_id`),
  KEY `fk_evaluations_user` (`user_id`),
  CONSTRAINT `fk_evaluations_entreprise` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_evaluations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données initiales pour les tests
-- Insertion des rôles
INSERT INTO `roles` (`nom`, `description`) VALUES
('admin', 'Administrateur avec tous les droits'),
('pilote', 'Pilote avec droits de gestion des entreprises'),
('etudiant', 'Étudiant avec droits limités');

-- Insertion des permissions
INSERT INTO `permissions` (`nom`, `description`) VALUES
('create_entreprise', 'Créer une entreprise'),
('read_entreprise', 'Voir les détails d'une entreprise'),
('update_entreprise', 'Modifier une entreprise'),
('delete_entreprise', 'Supprimer une entreprise'),
('create_evaluation', 'Créer une évaluation'),
('read_evaluation', 'Voir les évaluations'),
('update_evaluation', 'Modifier ses propres évaluations'),
('delete_evaluation', 'Supprimer ses propres évaluations'),
('manage_users', 'Gérer les utilisateurs'),
('manage_roles', 'Gérer les rôles et permissions');

-- Attribution des permissions aux rôles
-- Admin: toutes les permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT 1, id FROM `permissions`;

-- Pilote: tout sauf manage_roles
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT 2, id FROM `permissions` WHERE nom != 'manage_roles';

-- Étudiant: permissions limitées
INSERT INTO `role_permissions` (`role_id`, `permission_id`) 
SELECT 3, id FROM `permissions` WHERE nom IN ('read_entreprise', 'read_evaluation', 'create_evaluation', 'update_evaluation', 'delete_evaluation');

-- Insérer quelques utilisateurs (mot de passe: 'Password123')
INSERT INTO `users` (`email`, `password`, `role_id`, `nom`, `prenom`) VALUES
('admin@example.com', '$2y$10$rrO59n/2H8LCZAEDv.P24OhqBI9JzBU.rZ6MLiYTKYUw4OHouUOG.', 1, 'Admin', 'Système'),
('pilote@example.com', '$2y$10$rrO59n/2H8LCZAEDv.P24OhqBI9JzBU.rZ6MLiYTKYUw4OHouUOG.', 2, 'Durand', 'Marie'),
('etudiant@example.com', '$2y$10$rrO59n/2H8LCZAEDv.P24OhqBI9JzBU.rZ6MLiYTKYUw4OHouUOG.', 3, 'Martin', 'Thomas');
