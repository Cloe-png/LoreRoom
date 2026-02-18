-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Dim 15 Février 2026 à 22:20
-- Version du serveur :  5.6.20-log
-- Version de PHP :  5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `loreroom`
--

-- --------------------------------------------------------

--
-- Structure de la table `characters`
--

CREATE TABLE IF NOT EXISTS `characters` (
`id` bigint(20) unsigned NOT NULL,
  `world_id` bigint(20) unsigned NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aliases` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `death_date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vivant',
  `has_children` tinyint(1) NOT NULL DEFAULT '0',
  `father_id` bigint(20) unsigned DEFAULT NULL,
  `mother_id` bigint(20) unsigned DEFAULT NULL,
  `spouse_id` bigint(20) unsigned DEFAULT NULL,
  `birth_place_id` bigint(20) unsigned DEFAULT NULL,
  `residence_place_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_term_goal` text COLLATE utf8mb4_unicode_ci,
  `long_term_goal` text COLLATE utf8mb4_unicode_ci,
  `secrets` text COLLATE utf8mb4_unicode_ci,
  `secrets_is_private` tinyint(1) NOT NULL DEFAULT '1',
  `has_power` tinyint(1) NOT NULL DEFAULT '0',
  `power_level` tinyint(3) unsigned DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `character_events`
--

CREATE TABLE IF NOT EXISTS `character_events` (
`id` bigint(20) unsigned NOT NULL,
  `character_id` bigint(20) unsigned NOT NULL,
  `event_date` date DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `character_gallery_images`
--

CREATE TABLE IF NOT EXISTS `character_gallery_images` (
`id` bigint(20) unsigned NOT NULL,
  `character_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `character_items`
--

CREATE TABLE IF NOT EXISTS `character_items` (
`id` bigint(20) unsigned NOT NULL,
  `character_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rarity` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Structure de la table `character_jobs`
--

CREATE TABLE IF NOT EXISTS `character_jobs` (
`id` bigint(20) unsigned NOT NULL,
  `character_id` bigint(20) unsigned NOT NULL,
  `job_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_year` smallint(5) unsigned DEFAULT NULL,
  `end_year` smallint(5) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `character_relations`
--

CREATE TABLE IF NOT EXISTS `character_relations` (
`id` bigint(20) unsigned NOT NULL,
  `from_character_id` bigint(20) unsigned NOT NULL,
  `to_character_id` bigint(20) unsigned NOT NULL,
  `relation_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `intensity` tinyint(3) unsigned DEFAULT NULL,
  `is_bidirectional` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=108 ;

-- --------------------------------------------------------

--
-- Structure de la table `chronicles`
--

CREATE TABLE IF NOT EXISTS `chronicles` (
`id` bigint(20) unsigned NOT NULL,
  `world_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE IF NOT EXISTS `failed_jobs` (
`id` bigint(20) unsigned NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `imaginary_maps`
--

CREATE TABLE IF NOT EXISTS `imaginary_maps` (
`id` bigint(20) unsigned NOT NULL,
  `world_id` bigint(20) unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `map_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
`id` int(10) unsigned NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=18 ;

--
-- Contenu de la table `migrations`
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

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
`id` bigint(20) unsigned NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `places`
--

CREATE TABLE IF NOT EXISTS `places` (
`id` bigint(20) unsigned NOT NULL,
  `world_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=4 ;

--
-- Contenu de la table `places`
--

INSERT INTO `places` (`id`, `world_id`, `name`, `region`, `summary`, `created_at`, `updated_at`) VALUES
(1, 1, 'Laboratoire Akiyama', 'Akiyama', NULL, '2026-02-11 13:47:27', '2026-02-11 13:47:27'),
(3, 1, 'Île Aria', 'Aria', NULL, '2026-02-11 13:47:45', '2026-02-11 13:47:45');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `worlds`
--

CREATE TABLE IF NOT EXISTS `worlds` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `geography_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pays',
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `map_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `worlds`
--

INSERT INTO `worlds` (`id`, `name`, `geography_type`, `slug`, `map_path`, `summary`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Aria', 'ile', 'aria', NULL, NULL, 'active', '2026-02-11 09:42:33', '2026-02-11 09:42:33');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `characters`
--
ALTER TABLE `characters`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_characters_world` (`world_id`), ADD KEY `fk_characters_father` (`father_id`), ADD KEY `fk_characters_mother` (`mother_id`), ADD KEY `characters_birth_place_id_foreign` (`birth_place_id`), ADD KEY `characters_residence_place_id_foreign` (`residence_place_id`), ADD KEY `fk_characters_spouse` (`spouse_id`);

--
-- Index pour la table `character_events`
--
ALTER TABLE `character_events`
 ADD PRIMARY KEY (`id`), ADD KEY `character_events_character_id_foreign` (`character_id`);

--
-- Index pour la table `character_gallery_images`
--
ALTER TABLE `character_gallery_images`
 ADD PRIMARY KEY (`id`), ADD KEY `character_gallery_images_character_id_foreign` (`character_id`);

--
-- Index pour la table `character_items`
--
ALTER TABLE `character_items`
 ADD PRIMARY KEY (`id`), ADD KEY `character_items_character_id_foreign` (`character_id`);

--
-- Index pour la table `character_jobs`
--
ALTER TABLE `character_jobs`
 ADD PRIMARY KEY (`id`), ADD KEY `character_jobs_character_id_foreign` (`character_id`);

--
-- Index pour la table `character_relations`
--
ALTER TABLE `character_relations`
 ADD PRIMARY KEY (`id`), ADD KEY `character_relations_to_character_id_foreign` (`to_character_id`), ADD KEY `character_relations_from_character_id_to_character_id_index` (`from_character_id`,`to_character_id`);

--
-- Index pour la table `chronicles`
--
ALTER TABLE `chronicles`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_chronicles_world` (`world_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `imaginary_maps`
--
ALTER TABLE `imaginary_maps`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_maps_world` (`world_id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
 ADD KEY `password_resets_email_index` (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`), ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `places`
--
ALTER TABLE `places`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_places_world` (`world_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Index pour la table `worlds`
--
ALTER TABLE `worlds`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `worlds_slug_unique` (`slug`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `characters`
--
ALTER TABLE `characters`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `character_events`
--
ALTER TABLE `character_events`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `character_gallery_images`
--
ALTER TABLE `character_gallery_images`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `character_items`
--
ALTER TABLE `character_items`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `character_jobs`
--
ALTER TABLE `character_jobs`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `character_relations`
--
ALTER TABLE `character_relations`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=108;
--
-- AUTO_INCREMENT pour la table `chronicles`
--
ALTER TABLE `chronicles`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `imaginary_maps`
--
ALTER TABLE `imaginary_maps`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `places`
--
ALTER TABLE `places`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `worlds`
--
ALTER TABLE `worlds`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- Contraintes pour les tables exportées
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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
