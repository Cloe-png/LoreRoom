-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 13 fév. 2026 à 07:47
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `loreroom`
--

-- --------------------------------------------------------

--
-- Structure de la table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aliases` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `death_date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vivant',
  `has_children` tinyint(1) NOT NULL DEFAULT '0',
  `father_id` bigint UNSIGNED DEFAULT NULL,
  `mother_id` bigint UNSIGNED DEFAULT NULL,
  `spouse_id` bigint UNSIGNED DEFAULT NULL,
  `birth_place_id` bigint UNSIGNED DEFAULT NULL,
  `residence_place_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_term_goal` text COLLATE utf8mb4_unicode_ci,
  `long_term_goal` text COLLATE utf8mb4_unicode_ci,
  `secrets` text COLLATE utf8mb4_unicode_ci,
  `secrets_is_private` tinyint(1) NOT NULL DEFAULT '1',
  `has_power` tinyint(1) NOT NULL DEFAULT '0',
  `power_level` tinyint UNSIGNED DEFAULT NULL,
  `power_description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `silhouette` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hair_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eye_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hair_eyes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posture` text COLLATE utf8mb4_unicode_ci,
  `marks` text COLLATE utf8mb4_unicode_ci,
  `clothing_style` text COLLATE utf8mb4_unicode_ci,
  `qualities` text COLLATE utf8mb4_unicode_ci,
  `flaws` text COLLATE utf8mb4_unicode_ci,
  `psychology_notes` text COLLATE utf8mb4_unicode_ci,
  `voice_tics` text COLLATE utf8mb4_unicode_ci,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_characters_world` (`world_id`),
  KEY `fk_characters_father` (`father_id`),
  KEY `fk_characters_mother` (`mother_id`),
  KEY `characters_birth_place_id_foreign` (`birth_place_id`),
  KEY `characters_residence_place_id_foreign` (`residence_place_id`),
  KEY `fk_characters_spouse` (`spouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `characters`
--

INSERT INTO `characters` (`id`, `world_id`, `first_name`, `last_name`, `aliases`, `gender`, `birth_date`, `death_date`, `status`, `has_children`, `father_id`, `mother_id`, `spouse_id`, `birth_place_id`, `residence_place_id`, `name`, `role`, `short_term_goal`, `long_term_goal`, `secrets`, `secrets_is_private`, `has_power`, `power_level`, `power_description`, `image_path`, `preferred_color`, `height`, `silhouette`, `hair_color`, `eye_color`, `hair_eyes`, `posture`, `marks`, `clothing_style`, `qualities`, `flaws`, `psychology_notes`, `voice_tics`, `summary`, `created_at`, `updated_at`) VALUES
(1, 1, 'Maxime', 'Akiyama', NULL, NULL, '2005-05-08', '2042-12-24', 'mort', 1, NULL, NULL, 3, NULL, 3, 'Maxime Akiyama', 'Personnage principal', NULL, NULL, NULL, 1, 1, NULL, 'Glace : Main uniquement\r\nFeu : Très peu utilisé dû à sa santé', 'characters/iCTzwKZciZEZHWbfemtHeuumXHUuiQPvjTtjLA9Q.webp', NULL, '1m57', '/', 'Bleu avec des mèches rouges', 'Bleu', 'Bleu avec des mèches rouges / Bleu', '/', 'Cicatrice qui traverse son œil droit. Des cicatrices un peu partout sur le corps', 'Tee-shirt blanc avec un jean simple', '/', '/', '/', NULL, '/', '2026-02-11 09:45:26', '2026-02-12 11:50:35'),
(2, 1, 'Martin', 'Akiyama', '/', NULL, '2027-09-03', '2170-01-23', 'vivant', 0, 1, 3, NULL, 1, 3, 'Martin Akiyama', 'Fils du personnage principal', '/', '/', '/', 1, 1, 7, 'Nature', 'characters/0UK5itWr9XKd8AleyjH3Y2uYU7CcosVOBBP8V4Cp.webp', NULL, '1m74', '/', 'Bleu avec des mèches vertes', 'Bleu', 'Bleu avec des mèches vertes / Bleu', '/', '/', '/', '/', '/', '/', '/', '/', '2026-02-11 11:54:28', '2026-02-12 11:50:35'),
(3, 1, 'Margot', 'Akiyama', NULL, 'femme', '2005-09-26', '2061-12-25', 'mort', 1, NULL, NULL, 1, NULL, 3, 'Margot Akiyama', 'Femme du personnage principal', '/', '/', '/', 1, 0, NULL, NULL, 'characters/gPq8sXzPDB4NsHPueC3leYdYYUcguyynlfdlCXs8.webp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-11 12:47:59', '2026-02-12 11:50:35'),
(5, 1, 'Maëlle', 'Akiyama', NULL, 'femme', '2028-02-04', '2210-09-03', 'vivant', 1, 1, 3, NULL, 1, 3, 'Maëlle Akiyama', NULL, NULL, NULL, NULL, 1, 1, 5, 'Peut se transformer en gaz', 'characters/OcvzmelBEiQulTijBf630Z6Wi4kEH2PeddOtCt2j.webp', NULL, '1m67', NULL, 'Violet avec des mèches bleu', 'Bleu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-12 11:46:55', '2026-02-12 12:05:01'),
(6, 1, 'Mickey', 'Agawa', NULL, 'homme', '2027-09-03', '2150-12-21', 'vivant', 1, 1, 3, NULL, 1, 3, 'Mickey Agawa', 'Antagoniste', NULL, NULL, NULL, 1, 1, 10, 'Foudre\r\nManipulation', 'characters/RhX8uyzXl88RydN4EPhJo6RTMmC434AnaLNAD1DU.webp', NULL, '1m76', NULL, 'Brun avec des mèches rouges', 'Brun', NULL, NULL, 'Dans le cou et les bras', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-12 14:11:30', '2026-02-12 14:11:30'),
(7, 1, 'Mitsuki', 'Akiyama', NULL, 'homme', '2030-12-31', '2183-09-21', 'vivant', 1, 1, 3, NULL, 3, 3, 'Mitsuki Akiyama', NULL, NULL, NULL, NULL, 1, 1, 4, 'Peau d\'eau', 'characters/Iks1oYfqzkJQ5KuaPPmFuJ0nWDTIHCJHJA5u6X1H.webp', NULL, '1m78', NULL, 'Bleu avec des mèches blanche', 'Bleu', NULL, NULL, 'Tatouage du laboratoire', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-12 14:15:43', '2026-02-12 14:15:43');

-- --------------------------------------------------------

--
-- Structure de la table `character_events`
--

DROP TABLE IF EXISTS `character_events`;
CREATE TABLE IF NOT EXISTS `character_events` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `event_date` date DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_events_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_events`
--

INSERT INTO `character_events` (`id`, `character_id`, `event_date`, `title`, `details`, `created_at`, `updated_at`) VALUES
(7, 2, '2027-09-03', 'Anniversaire Martin', '/', '2026-02-12 12:06:59', '2026-02-12 12:06:59');

-- --------------------------------------------------------

--
-- Structure de la table `character_gallery_images`
--

DROP TABLE IF EXISTS `character_gallery_images`;
CREATE TABLE IF NOT EXISTS `character_gallery_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_gallery_images_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_gallery_images`
--

INSERT INTO `character_gallery_images` (`id`, `character_id`, `image_path`, `caption`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 2, 'characters/gallery/FJdhN9sRKGRjIJvoMNtGjsKAUASO61L04Q4VBp6o.webp', NULL, 1, '2026-02-11 11:54:28', '2026-02-11 11:54:28'),
(2, 2, 'characters/gallery/TLobrzbmlaWkb2trtGeElh0BFmoxW2rERFNIhvPQ.webp', NULL, 2, '2026-02-11 11:54:28', '2026-02-11 11:54:28'),
(3, 5, 'characters/gallery/Dk4yOmfMPIS2kHEJPILzAfAezuuLSwapdVcnmP53.webp', 'Petite', 1, '2026-02-12 12:05:01', '2026-02-12 12:05:01');

-- --------------------------------------------------------

--
-- Structure de la table `character_items`
--

DROP TABLE IF EXISTS `character_items`;
CREATE TABLE IF NOT EXISTS `character_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rarity` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_items_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_items`
--

INSERT INTO `character_items` (`id`, `character_id`, `name`, `rarity`, `notes`, `created_at`, `updated_at`) VALUES
(7, 2, 'Katana', NULL, '/', '2026-02-12 12:06:59', '2026-02-12 12:06:59');

-- --------------------------------------------------------

--
-- Structure de la table `character_jobs`
--

DROP TABLE IF EXISTS `character_jobs`;
CREATE TABLE IF NOT EXISTS `character_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `job_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_year` smallint UNSIGNED DEFAULT NULL,
  `end_year` smallint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_jobs_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_jobs`
--

INSERT INTO `character_jobs` (`id`, `character_id`, `job_name`, `start_year`, `end_year`, `notes`, `created_at`, `updated_at`) VALUES
(5, 3, 'Héros', 2028, 2061, NULL, '2026-02-11 13:56:08', '2026-02-11 13:56:08'),
(7, 5, 'Boulangère', 2064, 2200, NULL, '2026-02-12 12:24:09', '2026-02-12 12:24:09'),
(8, 6, 'Gouverneur', 2042, 2061, NULL, '2026-02-12 14:11:30', '2026-02-12 14:11:30'),
(9, 7, 'Chanteur', 2056, 2183, NULL, '2026-02-12 14:15:43', '2026-02-12 14:15:43');

-- --------------------------------------------------------

--
-- Structure de la table `character_relations`
--

DROP TABLE IF EXISTS `character_relations`;
CREATE TABLE IF NOT EXISTS `character_relations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_character_id` bigint UNSIGNED NOT NULL,
  `to_character_id` bigint UNSIGNED NOT NULL,
  `relation_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `intensity` tinyint UNSIGNED DEFAULT NULL,
  `is_bidirectional` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_relations_to_character_id_foreign` (`to_character_id`),
  KEY `character_relations_from_character_id_to_character_id_index` (`from_character_id`,`to_character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_relations`
--

INSERT INTO `character_relations` (`id`, `from_character_id`, `to_character_id`, `relation_type`, `description`, `intensity`, `is_bidirectional`, `created_at`, `updated_at`) VALUES
(94, 1, 7, 'père', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(93, 6, 1, 'fils', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(105, 7, 3, 'fils', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(92, 1, 6, 'père', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(91, 5, 1, 'fille', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(104, 3, 7, 'mère', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(103, 6, 3, 'fils', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(100, 3, 5, 'mère', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(90, 1, 5, 'père', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(102, 3, 6, 'mère', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(101, 5, 3, 'fille', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(89, 2, 1, 'fils/fille', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(88, 1, 2, 'père', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(95, 7, 1, 'fils', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(99, 2, 3, 'fils/fille', '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(98, 3, 2, 'mère', '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-02-12 14:15:43', '2026-02-12 14:15:43'),
(106, 3, 1, 'épouse', '[AUTO_FAMILY] spouse', 10, 0, '2026-02-12 14:15:44', '2026-02-12 14:15:44'),
(107, 1, 3, 'époux/épouse', '[AUTO_FAMILY] spouse', 10, 0, '2026-02-12 14:15:44', '2026-02-12 14:15:44');

-- --------------------------------------------------------

--
-- Structure de la table `chronicles`
--

DROP TABLE IF EXISTS `chronicles`;
CREATE TABLE IF NOT EXISTS `chronicles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chronicles_world` (`world_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `imaginary_maps`
--

DROP TABLE IF EXISTS `imaginary_maps`;
CREATE TABLE IF NOT EXISTS `imaginary_maps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `map_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_maps_world` (`world_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_02_11_000010_create_worlds_table', 1),
(6, '2026_02_11_000020_create_characters_table', 1),
(7, '2026_02_11_000030_create_places_table', 1),
(8, '2026_02_11_000040_create_chronicles_table', 1),
(9, '2026_02_11_000050_create_imaginary_maps_table', 1),
(10, '2026_02_11_000060_enhance_characters_profile_table', 2),
(11, '2026_02_11_000070_create_character_relations_table', 3),
(12, '2026_02_11_000080_add_preferred_color_to_characters_table', 4),
(13, '2026_02_11_000090_add_map_path_to_worlds_table', 4),
(14, '2026_02_11_000100_add_geography_type_to_worlds_table', 5),
(15, '2026_02_11_000110_enrich_characters_and_add_character_assets', 6),
(16, '2026_02_11_000120_split_hair_and_eyes_on_characters', 7),
(17, '2026_02_11_000130_create_character_jobs_table', 8);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `places`
--

DROP TABLE IF EXISTS `places`;
CREATE TABLE IF NOT EXISTS `places` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_places_world` (`world_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `places`
--

INSERT INTO `places` (`id`, `world_id`, `name`, `region`, `summary`, `created_at`, `updated_at`) VALUES
(1, 1, 'Laboratoire Akiyama', 'Akiyama', NULL, '2026-02-11 13:47:27', '2026-02-11 13:47:27'),
(3, 1, 'Île Aria', 'Aria', NULL, '2026-02-11 13:47:45', '2026-02-11 13:47:45');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `worlds`
--

DROP TABLE IF EXISTS `worlds`;
CREATE TABLE IF NOT EXISTS `worlds` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geography_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pays',
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `map_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `worlds_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `worlds`
--

INSERT INTO `worlds` (`id`, `name`, `geography_type`, `slug`, `map_path`, `summary`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Aria', 'ile', 'aria', NULL, NULL, 'active', '2026-02-11 09:42:33', '2026-02-11 09:42:33');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `characters_birth_place_id_foreign` FOREIGN KEY (`birth_place_id`) REFERENCES `places` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `characters_residence_place_id_foreign` FOREIGN KEY (`residence_place_id`) REFERENCES `places` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_characters_father` FOREIGN KEY (`father_id`) REFERENCES `characters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_characters_mother` FOREIGN KEY (`mother_id`) REFERENCES `characters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_characters_spouse` FOREIGN KEY (`spouse_id`) REFERENCES `characters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_characters_world` FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `chronicles`
--
ALTER TABLE `chronicles`
  ADD CONSTRAINT `fk_chronicles_world` FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `imaginary_maps`
--
ALTER TABLE `imaginary_maps`
  ADD CONSTRAINT `fk_maps_world` FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `places`
--
ALTER TABLE `places`
  ADD CONSTRAINT `fk_places_world` FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
