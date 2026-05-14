-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 30 avr. 2026 à 12:27
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

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `sp_world_dashboard`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_world_dashboard` (IN `p_world_id` BIGINT UNSIGNED)   BEGIN
    SELECT
        w.id,
        w.name,
        w.status,
        fn_world_character_count(w.id) AS character_count,
        (SELECT COUNT(*) FROM places WHERE world_id = w.id) AS place_count,
        (SELECT COUNT(*) FROM chronicles WHERE world_id = w.id) AS chronicle_count,
        (SELECT COUNT(*) FROM factions WHERE world_id = w.id) AS faction_count
    FROM worlds w
    WHERE w.id = p_world_id
    LIMIT 1;
END$$

--
-- Fonctions
--
DROP FUNCTION IF EXISTS `fn_world_character_count`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_world_character_count` (`p_world_id` BIGINT UNSIGNED) RETURNS INT DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_total INT DEFAULT 0;

    SELECT COUNT(*)
      INTO v_total
      FROM characters
     WHERE world_id = p_world_id;

    RETURN v_total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `family_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aliases` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `death_date` date DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vivant',
  `has_children` tinyint(1) NOT NULL DEFAULT '0',
  `has_brother_sister` tinyint(1) NOT NULL DEFAULT '0',
  `father_id` bigint UNSIGNED DEFAULT NULL,
  `mother_id` bigint UNSIGNED DEFAULT NULL,
  `spouse_id` bigint UNSIGNED DEFAULT NULL,
  `birth_place_id` bigint UNSIGNED DEFAULT NULL,
  `residence_place_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_term_goal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `long_term_goal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `secrets` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `secrets_is_private` tinyint(1) NOT NULL DEFAULT '1',
  `has_power` tinyint(1) NOT NULL DEFAULT '0',
  `power_level` tinyint UNSIGNED DEFAULT NULL,
  `power_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `silhouette` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hair_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eye_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hair_eyes` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posture` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `marks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `clothing_style` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `qualities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `flaws` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `psychology_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `voice_tics` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `voice_audio_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voice_youtube_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_characters_world` (`world_id`),
  KEY `fk_characters_father` (`father_id`),
  KEY `fk_characters_mother` (`mother_id`),
  KEY `characters_birth_place_id_foreign` (`birth_place_id`),
  KEY `characters_residence_place_id_foreign` (`residence_place_id`),
  KEY `fk_characters_spouse` (`spouse_id`),
  KEY `characters_family_name_index` (`family_name`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `characters`
--

INSERT INTO `characters` (`id`, `world_id`, `first_name`, `last_name`, `family_name`, `aliases`, `gender`, `birth_date`, `death_date`, `status`, `has_children`, `has_brother_sister`, `father_id`, `mother_id`, `spouse_id`, `birth_place_id`, `residence_place_id`, `name`, `role`, `short_term_goal`, `long_term_goal`, `secrets`, `secrets_is_private`, `has_power`, `power_level`, `power_description`, `image_path`, `preferred_color`, `height`, `silhouette`, `hair_color`, `eye_color`, `hair_eyes`, `posture`, `marks`, `clothing_style`, `qualities`, `flaws`, `psychology_notes`, `voice_tics`, `voice_audio_path`, `voice_youtube_url`, `summary`, `created_at`, `updated_at`) VALUES
(8, 1, 'Maxime', 'Akiyama', 'Akiyama', 'Akira - Michael Tanaka', 'homme', '2005-05-08', '2042-12-24', 'vivant', 1, 1, 17, 18, 9, NULL, 3, 'Maxime Akiyama', 'Personnage principal', NULL, NULL, 'Aku', 1, 1, 9, 'Glace\r\nFeu', 'characters/96POOvpPhCCAsPCkJjoF6azwLi2JALTkDzLKWTtR.webp', '#1D7BC3', '1m57', NULL, 'Bleu avec des mèches rouges', 'Bleu', NULL, NULL, 'Laboratoire\r\nŒil droit\r\nCicatrices dans le dos\r\nBrûlure à la main droite', 'Tee-shirt blanc, simple et efficace', 'Protecteur (à sa manière)\r\nDrôle, sarcastique\r\nTêtu', 'Tête en l\'air\r\nTête brûlé', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=LAjiTn5t78U&pp=ygURZW5lbWllcyB0aGUgc2NvcmU%3D', 'https://docs.google.com/document/d/18J-8dF0AuD86O1byzTV3U53ajEMfPYLc_sdvB-9va5E/edit?usp=drive_link', '2026-02-18 10:03:06', '2026-03-12 13:32:05'),
(9, 1, 'Margot', 'Akiyama', 'Akiyama', NULL, 'femme', '2005-06-26', '2061-12-25', 'vivant', 1, 0, NULL, NULL, 8, NULL, 3, 'Margot Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 9, 'Foudre', 'characters/fLJH3sXcBkIEsSFh3V3czN98EKGDrYpCT73ZvPFC.webp', '#D4D71D', '1m67', NULL, 'Brune avec des mèches violettes', 'Brun', NULL, NULL, 'Laboratoire', 'Petite chemise blanc avec un pull noir', 'Force mentale impressionnante\r\n\r\nDéterminée (quand elle veut quelque chose, elle ne lâche pas)\r\n\r\nCombattante redoutable\r\n\r\nCapable d’endurer la douleur sans flancher\r\n\r\nLoyale, même si elle ne le montre pas toujours', 'Difficulté à exprimer ses sentiments\r\n\r\nTendance à tout garder pour elle\r\n\r\nPeut devenir froide, presque distante\r\n\r\nAussi attirée par le danger que Maxime\r\n\r\nPardonne difficilement', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=MSDygYZWP2E&pp=ygUYc2hhbmdyaSBsYSBmcm9udGllciBvcCA00gcJCYcKAYcqIYzv', NULL, '2026-02-18 10:04:50', '2026-02-19 17:57:55'),
(10, 1, 'Martin', 'Akiyama', 'Akiyama', NULL, 'homme', '2027-09-03', '2170-01-23', 'vivant', 1, 1, 8, 9, 20, 1, 3, 'Martin Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 9, 'Contrôle des plantes', 'characters/1QygYrL7w3kLLNFOeTzzQ4CzlDRGmXRPKqkOPbVi.webp', '#1AA845', '1m74', NULL, 'Bleu avec des mèches vertes', 'Bleu', NULL, NULL, 'Laboratoire', NULL, 'Protecteur\r\n\r\nLoyal', 'Colère contenue\r\n\r\nDifficulté à gérer la vérité sur son père\r\n\r\nPeut agir impulsivement sous pression\r\n\r\nTrop dur avec lui-même', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=xFGE8BuOn0g&pp=ygUWc29tZXdoZXJlIG9ubHkgd2Uga25vdw%3D%3D', NULL, '2026-02-18 10:05:56', '2026-03-12 13:32:05'),
(11, 1, 'Mickey', 'Akiyama', 'Akiyama', NULL, 'homme', '2027-09-03', '2061-12-25', 'vivant', 0, 1, 8, 9, 29, 1, 3, 'Mickey Akiyama', 'Antagoniste', NULL, NULL, 'Aki', 1, 1, 10, 'Foudre - Manipulation', 'characters/aHtDoM08xjazTQD0SOn5najlht1s2XHTbdCHIYbe.webp', '#D41111', '1m76', NULL, 'Brun avec des mèches rouges', 'Brun', NULL, NULL, 'Laboratoire\r\nCoup\r\nBras', NULL, NULL, 'Rongé par la haine\r\n\r\nImpitoyable\r\n\r\nOrgueilleux\r\n\r\nIncapable de pardonner\r\n\r\nDestructeur, même envers lui-même\r\n\r\nFaible mentalement', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=m0BFZkPsoWY&pp=ygUJRHJhZyBwYXRo', NULL, '2026-02-18 11:56:31', '2026-03-19 11:32:18'),
(12, 1, 'Maëlle', 'Akiyama', 'Akiyama', NULL, 'femme', '2028-02-04', '2210-09-03', 'vivant', 1, 1, 8, 9, 25, 1, 3, 'Maëlle Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 6, 'Possibilité de devenir du gaz', 'characters/cUbI3TkwT13B8fFkX46veOibNqx3IFEANHjl1gio.webp', '#800698', '1m67', NULL, 'Violet avec des mèches bleu', 'Bleu', NULL, NULL, 'Laboratoire\r\nNée avec une jambe (prothèse)', NULL, 'Empathique\r\n\r\nDouce mais pas faible\r\n\r\nSoutien émotionnel fort\r\n\r\nIntuitive', 'Trop confiante\r\n\r\nPeut se sacrifier pour les autres\r\n\r\nSensible à la manipulation\r\n\r\nÉvite les conflits directs', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=Qb98LD-fxko&pp=ygUHaW1wdWxzZQ%3D%3D', NULL, '2026-02-18 11:56:57', '2026-03-12 13:44:36'),
(13, 1, 'Mitsuki', 'Akiyama', 'Akiyama', NULL, 'homme', '2030-12-31', '2183-09-21', 'vivant', 0, 1, 8, 9, NULL, 3, 3, 'Mitsuki Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 8, 'Peau d\'eau', 'characters/soS79rDw7XizwgDVNzlB5YY2jMFPaSNiiVB0eKsi.webp', '#47B8B0', '1m78', NULL, 'Bleu avec des mèches blanche', 'Bleu', NULL, NULL, 'Laboratoire', NULL, 'Charismatique sur scène\r\n\r\nVoix marquante / reconnaissable\r\n\r\nSensible et expressif (il met ses émotions dans ses chansons)\r\n\r\nCréatif (écriture, mélodies, univers artistique)\r\n\r\nTravailleur quand il croit en un projet', 'Hypersensible (prend tout à cœur)\r\n\r\nPeut douter énormément de lui-même\r\n\r\nBesoin de validation (public, groupe, proches)\r\n\r\nÉmotionnellement instable quand la pression monte\r\n\r\nPeut fuir les conflits plutôt que les affronter', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=Kzu0_h8YXns&pp=ygUjdHJvcGljYWwgdGhlcmFweSBvbmUgb2sgcm9jayBseXJpY3PSBwkJhwoBhyohjO8%3D', NULL, '2026-02-18 11:57:23', '2026-03-12 13:32:05'),
(14, 1, 'Mitsuya', 'Akiyama', 'Akiyama', NULL, 'homme', '2031-06-17', '2134-04-26', 'vivant', 1, 1, 8, 9, 30, 3, 3, 'Mitsuya Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 9, 'Glace', 'characters/fSjMt9TXmXE54TpBc7hGZJvkWohQrb7q0OXCgFNf.webp', '#092D9A', '1m79', NULL, 'Cheveux blanc avec une mèche rouge', 'Bleu', NULL, NULL, NULL, NULL, 'Stratège\r\n\r\nObservateur\r\n\r\nCalme sous pression\r\n\r\nLogique', 'Froid dans ses décisions\r\n\r\nPeut paraître insensible\r\n\r\nPriorise l’efficacité aux émotions\r\n\r\nDifficulté à s’attacher réellement', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=GspDybPhOeY&pp=ygUIU29sZCBvdXQ%3D', NULL, '2026-02-18 11:57:58', '2026-03-19 12:01:59'),
(15, 1, 'Mizuki', 'Akiyama', 'Akiyama', NULL, 'femme', '2033-01-15', '2207-05-18', 'vivant', 1, 1, 8, 9, 31, 3, 3, 'Mizuki Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, NULL, 'Capacité de prendre l\'apparence d\'une personne à condition de la dessiner', 'characters/gD2tZB5A2VcYBKQ6UYhWaiywUZbmJjKyjJ47l3Pz.webp', '#23599F', '1m59', NULL, 'Brune', 'Brun', NULL, NULL, NULL, NULL, 'Discrète mais efficace\r\n\r\nCréative\r\n\r\nFidèle\r\n\r\nAdaptable', 'Se sous-estime\r\n\r\nReste souvent dans l’ombre\r\n\r\nPeut exploser après avoir trop accumulé\r\n\r\nDifficulté à dire non', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=YDLafQ-Rg-k&pp=ygURZ2lybHMgYmFuZCBjcnkgb3A%3D', NULL, '2026-02-18 11:58:32', '2026-03-19 12:09:48'),
(16, 1, 'Isan', 'Suuta', 'Suuta', NULL, 'homme', '2005-02-21', '2113-02-08', 'vivant', 1, 0, NULL, NULL, NULL, NULL, 3, 'Isan Suuta', 'Mentor', NULL, NULL, 'Garde du corps de la princesse Laure Kaminomizu', 1, 1, NULL, 'Super vitesse', 'characters/SToqfAEedFYlro4iGYBYvnGJzWXXGYpoq78JSmWx.webp', '#641499', '1m88', NULL, 'Violet', 'Rose', NULL, NULL, 'Laboratoire \r\nDans le dos', NULL, 'Extrêmement loyal\r\n\r\nDiscipliné\r\n\r\nRapide, précis, efficace\r\n\r\nProtecteur sans être envahissant (T\'inquiète)', 'Difficulté à montrer ses émotions\r\n\r\nTendance à s’effacer derrière son rôle de soldat', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=LiXYi-_MVa4&pp=ygUJYmJubyQgdHdv', NULL, '2026-02-18 12:00:09', '2026-03-06 09:45:29'),
(17, 1, 'Maxence', 'Tanaka', 'Tanaka', NULL, 'homme', '1984-06-16', '2014-10-28', 'mort', 1, 1, NULL, NULL, 18, NULL, NULL, 'Maxence Tanaka', 'Personnage secondaire', NULL, NULL, 'Aku', 1, 1, 2, 'Télékinésie', 'characters/PVoOPNU7W9Ewz3A7wXjNd6C63ZPC6E6lmPvBdHrO.jpg', '#8F6A3A', '1m75', NULL, 'Cheveux brun avec des mèches rouges', 'Brun', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19 18:53:55', '2026-02-19 18:58:32'),
(18, 1, 'Marie', 'Tanaka', 'Tanaka', NULL, 'femme', '1984-01-05', '2041-11-06', 'vivant', 1, 0, NULL, NULL, 17, NULL, NULL, 'Marie Tanaka', 'Personnage secondaire', NULL, NULL, NULL, 0, 0, NULL, NULL, 'characters/oSVqIFve5YBR9YvuWGO6hzIUk2qL1ZjPThpaP0oX.jpg', '#921C1C', '1m64', NULL, 'Brune', 'Bleu', NULL, NULL, NULL, 'Elégante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19 18:57:32', '2026-02-19 18:57:32'),
(19, 1, 'Michael', 'Tanaka', 'Tanaka', NULL, 'homme', '2008-03-14', '2028-03-30', 'vivant', 1, 1, 17, 18, NULL, NULL, NULL, 'Michael Tanaka', 'Personnage secondaire', NULL, NULL, 'Aku', 1, 1, 2, NULL, 'characters/U2tzIfBppjTvG35RG3Y4WHNy2DkNeGW9ne495PLs.jpg', '#FF1414', '1m82', NULL, 'Brun avec des mèches rouges', 'Brun', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-19 19:09:15', '2026-03-06 19:34:33'),
(20, 1, 'Ingrid', 'Nelson', 'Nelson', NULL, 'femme', '2027-07-16', '2061-12-24', 'vivant', 1, 0, NULL, NULL, 10, 3, 3, 'Ingrid Nelson', 'Personnage secondaire', NULL, NULL, NULL, 0, 0, NULL, NULL, 'characters/duN89G2dz7fdYZ0g8Nyo6vhWA2jbIGpfmtEUq85X.jpg', '#FF94EB', '1m63', NULL, 'Rose Sakura', 'Rose', NULL, NULL, NULL, 'Elégante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-04 09:44:45', '2026-03-04 09:44:45'),
(21, 1, 'Owen', 'Akiyama', 'Akiyama', NULL, 'homme', '2053-10-25', '2216-09-06', 'vivant', 1, 1, 10, 20, 35, 3, 3, 'Owen Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, NULL, 'Nature', 'characters/4vwOl5FQW82hMfStfK9IxNrm3rcY2NaSOjEF5B4v.webp', '#44B43C', '1m80', NULL, 'Cheveux bleu avec des mèches vertes', 'Bleu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-04 09:49:17', '2026-03-23 09:42:20'),
(22, 1, 'Sasha', 'Akiyama', 'Akiyama', NULL, 'homme', '2053-10-31', '2216-03-17', 'vivant', 1, 1, 10, 20, 36, 3, 3, 'Sasha Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 0, NULL, NULL, 'characters/CV8VPg8Yt4qo3NTCVtzFVZNnG2Tap2M8fB4QRTzq.webp', '#C171B2', '1m79', NULL, 'Cheveux bleu rose sakura', 'Bleu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-04 09:51:18', '2026-03-23 09:42:37'),
(24, 1, 'Hayate', 'Kaze', 'Kaze', NULL, 'homme', '2030-01-24', '2189-06-07', 'vivant', 0, 0, NULL, NULL, NULL, NULL, 3, 'Hayate Kaze', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 8, 'Rafale de vent', 'characters/cTeUTMQf8jTXibuxqAfOjLQNx7NyiV1p2VJleeVe.jpg', '#F89F2A', '1m78', NULL, 'Roux', 'Bleu', NULL, NULL, NULL, 'Chill', NULL, 'Timide\r\nLa batterie sociale descend très vite', NULL, NULL, NULL, 'https://www.youtube.com/watch?v=gNg2Qw5R-Q4', NULL, '2026-03-12 13:28:48', '2026-03-12 13:28:48'),
(25, 1, 'Haru', 'Kado', 'Kado', NULL, 'homme', '2030-05-29', '2175-06-07', 'vivant', 0, 0, NULL, NULL, 12, 3, 3, 'Haru Kado', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 7, 'Manipulation des vibrations', 'characters/TqlUvWBpuech7j2YebbSoY6yY1h96W4Xg1ZBEzCE.jpg', '#BBB6AF', '1m77', NULL, 'Blanc avec des mèches noirs', 'Gris', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-12 13:44:36', '2026-03-12 13:44:36'),
(26, 1, 'Hiruma', 'Kikyo', 'Kikyo', NULL, 'homme', '2030-11-23', '2152-05-06', 'vivant', 0, 0, NULL, NULL, NULL, NULL, NULL, 'Hiruma Kikyo', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 6, 'Manipulation des ombres', 'characters/IRyhkFJAxj77dWEWrBQb0mGdPbyav3pG9jSPFNK1.jpg', '#BDC82D', '1m83', NULL, 'Blond', 'Noisette', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-18 11:58:47', '2026-03-18 11:59:57'),
(27, 1, 'Kôta', 'Aoki', 'Aoki', NULL, 'homme', '2028-10-18', '2221-09-21', 'vivant', 1, 0, NULL, NULL, NULL, NULL, NULL, 'Kôta Aoki', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 8, 'Super force', 'characters/7MoalEHI43BVuygHWkMHofjKoe9p4zQbpRG6k6tk.webp', '#64E15B', '1m77', NULL, 'Vert', 'Bleu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-18 12:17:49', '2026-03-18 12:39:48'),
(28, 1, 'Aiko', 'Aoki', 'Aoki', NULL, 'femme', '2058-03-16', '2222-03-12', 'vivant', 0, 0, 27, 12, NULL, 3, 3, 'Aiko Aoki', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 9, 'Super force', 'characters/Cr9InFkZdzyuVtHF8RxaKsDHXRQz03nXdLKzhOgd.jpg', '#39A746', '1m60', NULL, 'Vert et violet', 'Bleu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 06:38:00', '2026-03-19 06:38:56'),
(29, 1, 'Hôra', 'Fukashi', 'Fukashi', NULL, 'femme', '2027-05-05', '2153-02-13', 'vivant', 1, 0, NULL, NULL, 11, 3, 3, 'Hôra Fukashi', 'Antagoniste', NULL, NULL, NULL, 0, 1, 9, 'Super vision', 'characters/QL84LTqCIH2mqKvpZDHPmtUOHpoBhAnfMHYiROg1.jpg', '#8D3A6A', '1m67', NULL, 'Rose et violet', 'Jaune', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 11:32:18', '2026-03-19 11:32:18'),
(30, 1, 'Annie', 'Abiko', 'Abiko', NULL, 'femme', '2031-04-25', '2134-12-13', 'vivant', 1, 0, NULL, NULL, 14, 3, 3, 'Annie Abiko', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 7, 'Forte voix', 'characters/Ms2MUtLKcMZWZKQklAlnWUDrqrB47lKUtKVTydPq.jpg', '#C70F0F', '1m63', NULL, 'Rouge', 'Bleu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 12:00:39', '2026-03-19 12:01:59'),
(31, 1, 'Reiko', 'Hamada', 'Hamada', NULL, 'homme', '2033-11-20', '2205-05-14', 'vivant', 1, 0, NULL, NULL, 15, 3, 3, 'Reiko Hamada', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 9, 'Capable de visualiser les souvenirs des autres', 'characters/us7IiFcayTjuWqngPHED1YGQY2kGHRgn0gSyM8or.webp', '#8F6A3A', '1m69', NULL, 'Blond', 'Brun', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 12:09:48', '2026-03-23 14:37:18'),
(32, 1, 'Chrôme', 'Akiyama', 'Akiyama', NULL, 'homme', '2053-06-03', '2216-09-03', 'vivant', 1, 0, 11, 29, 33, 3, 3, 'Chrôme Akiyama', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 7, 'Capable de changer sa taille', 'characters/lgUSiKdSKJiyIbHKajByL2jD9gGvKcv2UHJU8mqB.jpg', '#A81515', '1m72', NULL, 'Brun avec des mèches rouges', 'Brun', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 12:21:03', '2026-03-19 12:29:39'),
(33, 1, 'Azumy', 'Masuda', 'Masuda', NULL, 'femme', '2053-02-23', '2227-08-12', 'vivant', 1, 0, NULL, NULL, 32, 3, 3, 'Azumy Masuda', 'Personnage secondaire', NULL, NULL, NULL, 0, 0, NULL, NULL, 'characters/dwwo9Ykqkz5yT7HAIC8sp3uW9udysgnfeAXG6ObG.jpg', '#CC80FF', '1m60', NULL, 'Gris avec des mèches violette', 'Bleu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 12:29:39', '2026-03-19 12:29:59'),
(34, 1, 'Tetsuya', NULL, NULL, NULL, 'homme', '2054-09-23', '2254-04-23', 'vivant', 0, 0, 13, NULL, NULL, NULL, 3, 'Tetsuya', 'Personnage secondaire', NULL, NULL, NULL, 0, 0, NULL, NULL, 'characters/wbLLWH3McocZHXEFiV9KaUU6at2rucYarrEqtY5L.webp', '#3A8D88', '1m79', NULL, 'Noir', 'Rouge', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 12:34:20', '2026-03-19 12:34:20'),
(35, 1, 'Natsuki', 'Hitomori', 'Hitomori', NULL, 'femme', '2053-10-12', '2217-12-27', 'vivant', 1, 0, NULL, NULL, 21, NULL, 3, 'Natsuki Hitomori', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 2, 'Feu', 'characters/wHDgkJqo2rFMYGI3Fij9zFmzZXGC5iXJed5m9ZiV.jpg', '#252422', '1m67', NULL, 'Noir', 'Brun', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 12:37:22', '2026-03-19 12:38:16'),
(36, 1, 'Olympe', 'Suuta', 'Suuta', NULL, 'femme', '2051-08-27', '2235-12-06', 'vivant', 1, 1, 16, NULL, 22, 3, 3, 'Olympe Suuta', 'Personnage secondaire', NULL, NULL, NULL, 0, 1, 9, 'Vitesse', 'characters/0GpHncIxKNi7HzasjU2jiPIwZGjUjUGoq7VUDfSq.jpg', '#92659F', '1m66', NULL, 'Brune avec des mèches violettes', 'Violet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-19 12:48:00', '2026-03-19 12:48:01');

-- --------------------------------------------------------

--
-- Structure de la table `character_educations`
--

DROP TABLE IF EXISTS `character_educations`;
CREATE TABLE IF NOT EXISTS `character_educations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `faction_id` bigint UNSIGNED NOT NULL,
  `diploma_id` bigint UNSIGNED DEFAULT NULL,
  `field` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_year` int DEFAULT NULL,
  `end_year` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_educations_character_id_foreign` (`character_id`),
  KEY `character_educations_faction_id_foreign` (`faction_id`),
  KEY `character_educations_diploma_id_foreign` (`diploma_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `character_events`
--

DROP TABLE IF EXISTS `character_events`;
CREATE TABLE IF NOT EXISTS `character_events` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `event_date` date DEFAULT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_events_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `character_exes`
--

DROP TABLE IF EXISTS `character_exes`;
CREATE TABLE IF NOT EXISTS `character_exes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `ex_character_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `character_exes_character_id_ex_character_id_unique` (`character_id`,`ex_character_id`),
  KEY `character_exes_ex_character_id_foreign` (`ex_character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_exes`
--

INSERT INTO `character_exes` (`id`, `character_id`, `ex_character_id`, `created_at`, `updated_at`) VALUES
(4, 12, 27, '2026-03-18 12:39:48', '2026-03-18 12:39:48'),
(3, 27, 12, '2026-03-18 12:39:48', '2026-03-18 12:39:48');

-- --------------------------------------------------------

--
-- Structure de la table `character_gallery_images`
--

DROP TABLE IF EXISTS `character_gallery_images`;
CREATE TABLE IF NOT EXISTS `character_gallery_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_gallery_images_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_gallery_images`
--

INSERT INTO `character_gallery_images` (`id`, `character_id`, `image_path`, `caption`, `sort_order`, `created_at`, `updated_at`) VALUES
(4, 8, 'characters/gallery/yXlRJUzDoxcHABSa8bT4ufDxnbQYzQcyhUDU3TKT.webp', 'Michael Tanaka', 1, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(5, 8, 'characters/gallery/TAQWExoSqWHOQSSUWPFcTwbKf6Do6LYvFXaScAkx.webp', 'Akira', 2, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(6, 8, 'characters/gallery/nZUom5vAavoMvwfix6aOPpTqDPkoQWWSPObpBlf1.jpg', 'En laboratoire avec Margot', 3, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(7, 8, 'characters/gallery/K8r1MN16fE0COTgxeVtsltKOGNWgCAPYZ87m6BCC.webp', 'En laboratoire avec Isan', 4, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(8, 8, 'characters/gallery/ML8K4G3AQTZfRfRwMil3fSpWnj5WEmVV2wg4zArq.webp', '6 ans', 5, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(9, 8, 'characters/gallery/YRj72ytQ9M3bYdIJ465CI6utbrAgqmjIuqQ2JchN.webp', '9 ans', 6, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(10, 8, 'characters/gallery/fGWFaIBUiEZxhnLJ8ccYkoGgHFx3KbqwlQ5p1UI5.webp', '23 ans', 7, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(11, 8, 'characters/gallery/USntNiN7uOTygSiCUs95502XwuZ20MyQYz63K4LM.webp', '37 ans', 8, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(12, 8, 'characters/gallery/eeRki5QsYKhjpF0z99BTiBcDsaocCIbnrOrkqhz3.png', '\"Première\" version', 9, '2026-02-19 11:21:13', '2026-02-19 11:21:13'),
(13, 13, 'characters/gallery/HzzWx8xRKY4By6KOGTh4YO1JvCOS9ZcPBh2TXuxe.png', 'Première version de Mitsuki', 1, '2026-02-19 13:54:06', '2026-02-19 13:54:06'),
(14, 13, 'characters/gallery/UC8otf7ETNpqNCcpyYA43Rr8S3MC1QswvO7dDQ4t.webp', 'En mode chat', 2, '2026-02-19 13:54:06', '2026-02-19 13:54:06'),
(15, 13, 'characters/gallery/FcaVZphQsDjuPSkvB6GQpvkS1mJgIF8UdfFI0ulT.webp', 'Avec le groupe', 3, '2026-02-19 13:54:06', '2026-02-19 13:54:06'),
(16, 10, 'characters/gallery/T3X7hheobUIilcmvICK3JZSz1vh7w8fojDOFftxe.png', 'Première version de Martin', 1, '2026-02-19 14:05:01', '2026-02-19 14:05:01'),
(17, 10, 'characters/gallery/wG1yKKLD0ViLk2bfLfKPDA3Q8uol2kcQOB4WYQDU.webp', 'Adulte', 2, '2026-02-19 14:05:01', '2026-02-19 14:05:01'),
(18, 10, 'characters/gallery/Lvd0w8AchDwNfb8IpBpvgezBLG4mKvN5g1qAUNK7.webp', 'Avec sa petite sœur préférée', 3, '2026-02-19 14:05:01', '2026-02-19 14:05:01'),
(19, 9, 'characters/gallery/mGdBgf8y2j8J3BN6N595bfUPPULDJV5cSHD2Vfe5.png', 'Première version de Margot', 1, '2026-02-19 18:00:53', '2026-02-19 18:00:53'),
(20, 11, 'characters/gallery/m47aJE6b35ff8ONX8VezOMLYOTkfqepdohlGXSGx.png', 'Première version de Mickey', 1, '2026-02-19 18:20:48', '2026-02-19 18:20:48'),
(21, 11, 'characters/gallery/86p2Hcr8dxWzouOrqhAubd1uexonPeaQ2oSJSIBn.jpg', 'Secret', 2, '2026-02-19 18:20:48', '2026-02-19 18:20:48'),
(22, 12, 'characters/gallery/ScOKfbewznhlzVIbuT7R004lJDRk4zLvrfVAa4uB.png', 'Première version de Maëlle', 1, '2026-02-19 18:25:37', '2026-02-19 18:25:37'),
(23, 14, 'characters/gallery/ehwWSJCCGQcox1aUO3lbST4WiuYvSGBasw3YsBlM.png', 'Première version de Mitsuya', 1, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(24, 15, 'characters/gallery/vZy5Cdx51L8CvXxqJOgNWk34TLFjwdIyQdNpu2wr.jpg', 'Première version de Mizuki', 1, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(25, 16, 'characters/gallery/CodcppcbYVM7l6LtgW9sZP9PFlR6fEgf2PkG4rXY.png', 'Première version d\'Isan', 1, '2026-02-19 18:50:35', '2026-02-19 18:50:35'),
(26, 17, 'characters/gallery/g2KfrCcTjoA67mndH0Zatdjxlz8ZVxIMKsXMS4Hp.png', 'Première version de Maxence', 1, '2026-02-19 18:53:55', '2026-02-19 18:53:55'),
(27, 18, 'characters/gallery/mvY4JSHttBLi90qMxWlCxkCb9Oz5bc451PTtYkBp.jpg', 'Première version de Marie', 1, '2026-02-19 18:57:32', '2026-02-19 18:57:32');

-- --------------------------------------------------------

--
-- Structure de la table `character_items`
--

DROP TABLE IF EXISTS `character_items`;
CREATE TABLE IF NOT EXISTS `character_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rarity` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_items_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_items`
--

INSERT INTO `character_items` (`id`, `character_id`, `name`, `rarity`, `notes`, `created_at`, `updated_at`) VALUES
(18, 8, 'Katana', 'commun', NULL, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(15, 9, 'Katana', 'commun', NULL, '2026-02-19 18:00:53', '2026-02-19 18:00:53'),
(16, 10, 'Katana', 'commun', NULL, '2026-02-19 18:02:38', '2026-02-19 18:02:38');

-- --------------------------------------------------------

--
-- Structure de la table `character_jobs`
--

DROP TABLE IF EXISTS `character_jobs`;
CREATE TABLE IF NOT EXISTS `character_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `job_id` bigint UNSIGNED DEFAULT NULL,
  `job_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_year` smallint UNSIGNED DEFAULT NULL,
  `end_year` smallint UNSIGNED DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_jobs_character_id_foreign` (`character_id`),
  KEY `character_jobs_job_id_foreign` (`job_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_jobs`
--

INSERT INTO `character_jobs` (`id`, `character_id`, `job_id`, `job_name`, `start_year`, `end_year`, `notes`, `created_at`, `updated_at`) VALUES
(67, 8, NULL, 'Héros - Vice-Capitaine de la première division', 2029, 2042, NULL, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(47, 9, NULL, 'Héros', 2028, 2061, NULL, '2026-02-19 18:00:53', '2026-02-19 18:00:53'),
(65, 8, NULL, 'Héros', 2028, 2028, NULL, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(66, 8, NULL, 'Héros - Capitaine de la première division', 2029, 2042, NULL, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(43, 13, NULL, 'Vendeur', NULL, NULL, NULL, '2026-02-19 13:54:05', '2026-02-19 13:54:05'),
(44, 13, NULL, 'Chanteur', 2046, 2173, NULL, '2026-02-19 13:54:05', '2026-02-19 13:54:05'),
(48, 10, NULL, 'Héros', 2048, 2156, NULL, '2026-02-19 18:02:38', '2026-02-19 18:02:38'),
(57, 11, NULL, 'Gouverneur', 2042, 2061, NULL, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(50, 12, NULL, 'Chanteuse', 2040, 2042, NULL, '2026-02-19 18:25:37', '2026-02-19 18:25:37'),
(51, 12, NULL, 'Boulangère', 2064, 2189, NULL, '2026-02-19 18:25:37', '2026-02-19 18:25:37'),
(52, 14, NULL, 'Pilote (motocross)', 2043, 2050, NULL, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(53, 14, NULL, 'Policier', NULL, 2061, NULL, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(54, 14, NULL, 'Héros - Capitaine de la première division', 2061, 2100, NULL, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(55, 15, NULL, 'Artiste', 2044, 2199, NULL, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(62, 16, NULL, 'Garde du corps', 2028, 2113, NULL, '2026-03-06 09:45:29', '2026-03-06 09:45:29'),
(64, 24, NULL, 'Chanteur/Guitariste', 2044, 2140, 'Groupe fondé en 2044 avec Haru, puis Hiruma et Mitsuki se sont joint à l\'aventure. Smile Sound', '2026-03-12 13:29:36', '2026-03-12 13:29:36'),
(68, 8, NULL, 'Barman', 2029, 2042, NULL, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(69, 25, NULL, 'Batteur', 2044, 2061, NULL, '2026-03-12 13:44:36', '2026-03-12 13:44:36'),
(71, 26, 135, 'Chanteur', 2044, 2061, NULL, '2026-03-18 11:59:57', '2026-03-18 11:59:57');

-- --------------------------------------------------------

--
-- Structure de la table `character_relations`
--

DROP TABLE IF EXISTS `character_relations`;
CREATE TABLE IF NOT EXISTS `character_relations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_character_id` bigint UNSIGNED NOT NULL,
  `to_character_id` bigint UNSIGNED NOT NULL,
  `relation_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `relation_category` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sibling_kind` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `intensity` tinyint UNSIGNED DEFAULT NULL,
  `is_bidirectional` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `character_relations_to_character_id_foreign` (`to_character_id`),
  KEY `character_relations_from_character_id_to_character_id_index` (`from_character_id`,`to_character_id`),
  KEY `character_relations_relation_category_index` (`relation_category`),
  KEY `character_relations_sibling_kind_index` (`sibling_kind`)
) ENGINE=MyISAM AUTO_INCREMENT=1788 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_relations`
--

INSERT INTO `character_relations` (`id`, `from_character_id`, `to_character_id`, `relation_type`, `relation_category`, `sibling_kind`, `description`, `intensity`, `is_bidirectional`, `created_at`, `updated_at`) VALUES
(1637, 8, 16, 'Frère de cœur', 'custom', NULL, NULL, NULL, 1, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1656, 17, 19, 'père', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1772, 10, 22, 'père', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1230, 9, 8, 'epoux/epouse', 'family_couple', NULL, NULL, NULL, 1, '2026-02-19 18:00:53', '2026-02-19 18:00:53'),
(1419, 13, 15, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1779, 22, 20, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1417, 12, 15, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1732, 13, 34, 'père', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-19 12:34:21', '2026-03-19 12:34:21'),
(1733, 34, 13, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-19 12:34:21', '2026-03-19 12:34:21'),
(1504, 11, 14, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1380, 14, 13, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(1731, 33, 32, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 12:30:00', '2026-03-19 12:30:00'),
(1378, 14, 12, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(1379, 12, 14, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(1499, 10, 11, 'jumeau', 'family_sibling', 'twin', '[AUTO_SIBLING] twin', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1500, 11, 12, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1503, 13, 11, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1502, 11, 13, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1413, 10, 15, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1501, 12, 11, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1773, 22, 10, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1777, 21, 20, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1782, 36, 22, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1783, 22, 36, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1340, 12, 13, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:25:37', '2026-02-19 18:25:37'),
(1341, 13, 12, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:25:37', '2026-02-19 18:25:37'),
(1418, 15, 13, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1505, 14, 11, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1420, 15, 14, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1506, 11, 15, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1780, 20, 10, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1416, 15, 12, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1702, 12, 25, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 06:38:56', '2026-03-19 06:38:56'),
(1699, 28, 27, 'fille', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-19 06:38:56', '2026-03-19 06:38:56'),
(1374, 14, 10, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(1264, 10, 13, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:02:38', '2026-02-19 18:02:38'),
(1265, 13, 10, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:02:38', '2026-02-19 18:02:38'),
(1336, 12, 10, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:25:37', '2026-02-19 18:25:37'),
(1421, 14, 15, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1412, 15, 10, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:43:30', '2026-02-19 18:43:30'),
(1507, 15, 11, 'soeur', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1498, 11, 10, 'jumeau', 'family_sibling', 'twin', '[AUTO_SIBLING] twin', 9, 0, '2026-03-04 09:24:36', '2026-03-04 09:24:36'),
(1337, 10, 12, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:25:37', '2026-02-19 18:25:37'),
(1231, 9, 16, 'Meilleur ami', 'custom', NULL, NULL, NULL, 1, '2026-02-19 18:00:53', '2026-02-19 18:00:53'),
(1381, 13, 14, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(1678, 9, 8, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1375, 10, 14, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-02-19 18:32:39', '2026-02-19 18:32:39'),
(1229, 9, 16, 'Meilleur ami', 'custom', NULL, NULL, NULL, 1, '2026-02-19 18:00:53', '2026-02-19 18:00:53'),
(1605, 16, 8, 'Meilleur ami', 'custom', NULL, NULL, NULL, 1, '2026-03-06 09:45:29', '2026-03-06 09:45:29'),
(1722, 29, 11, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 12:21:03', '2026-03-19 12:21:03'),
(1786, 15, 31, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 14:37:18', '2026-03-23 14:37:18'),
(1689, 26, 25, 'Ami', 'social', NULL, NULL, NULL, 1, '2026-03-18 11:59:57', '2026-03-18 11:59:57'),
(1606, 16, 9, 'Meilleur ami', 'custom', NULL, NULL, NULL, 1, '2026-03-06 09:45:29', '2026-03-06 09:45:29'),
(1664, 18, 17, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1710, 14, 30, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 12:01:59', '2026-03-19 12:01:59'),
(1690, 26, 24, 'Ami', 'social', NULL, NULL, NULL, 1, '2026-03-18 11:59:57', '2026-03-18 11:59:57'),
(1691, 26, 13, 'Ami', 'social', NULL, NULL, NULL, 1, '2026-03-18 11:59:57', '2026-03-18 11:59:57'),
(1665, 17, 18, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1657, 19, 17, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1663, 19, 18, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1662, 18, 19, 'mère', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1661, 8, 18, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1660, 18, 8, 'mère', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1639, 19, 8, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1679, 8, 9, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1766, 22, 21, 'jumeau', 'family_sibling', 'twin', '[AUTO_SIBLING] twin', 9, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1767, 21, 22, 'jumeau', 'family_sibling', 'twin', '[AUTO_SIBLING] twin', 9, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1765, 21, 35, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 09:42:21', '2026-03-23 09:42:21'),
(1764, 35, 21, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 09:42:21', '2026-03-23 09:42:21'),
(1778, 20, 22, 'mère', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1776, 20, 21, 'mère', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1723, 11, 29, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 12:21:03', '2026-03-19 12:21:03'),
(1787, 31, 15, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 14:37:18', '2026-03-23 14:37:18'),
(1781, 10, 20, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1638, 8, 19, 'frere', 'family_sibling', 'full', '[AUTO_SIBLING] full', 9, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1654, 17, 8, 'père', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1655, 8, 17, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-12 13:32:05', '2026-03-12 13:32:05'),
(1771, 21, 10, 'fils', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1711, 30, 14, 'epouse', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 12:01:59', '2026-03-19 12:01:59'),
(1770, 10, 21, 'père', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-23 09:42:37', '2026-03-23 09:42:37'),
(1636, 24, 13, 'Ami', 'social', NULL, NULL, NULL, 1, '2026-03-12 13:29:36', '2026-03-12 13:29:36'),
(1680, 25, 24, 'Meilleur ami', 'custom', NULL, NULL, NULL, 1, '2026-03-12 13:44:36', '2026-03-12 13:44:36'),
(1681, 25, 13, 'Ami', 'social', NULL, NULL, NULL, 1, '2026-03-12 13:44:36', '2026-03-12 13:44:36'),
(1703, 25, 12, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 06:38:56', '2026-03-19 06:38:56'),
(1698, 27, 28, 'père', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-19 06:38:56', '2026-03-19 06:38:56'),
(1701, 28, 12, 'fille', 'family_lineage', NULL, '[AUTO_FAMILY] enfant->parent', 10, 0, '2026-03-19 06:38:56', '2026-03-19 06:38:56'),
(1700, 12, 28, 'mère', 'custom', NULL, '[AUTO_FAMILY] parent->enfant', 10, 0, '2026-03-19 06:38:56', '2026-03-19 06:38:56'),
(1730, 32, 33, 'epoux', 'family_couple', NULL, '[AUTO_FAMILY] spouse', 10, 0, '2026-03-19 12:30:00', '2026-03-19 12:30:00');

--
-- Déclencheurs `character_relations`
--
DROP TRIGGER IF EXISTS `trg_character_relations_before_insert`;
DELIMITER $$
CREATE TRIGGER `trg_character_relations_before_insert` BEFORE INSERT ON `character_relations` FOR EACH ROW BEGIN
    IF NEW.from_character_id = NEW.to_character_id THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Un personnage ne peut pas etre en relation avec lui-meme.';
    END IF;

    IF EXISTS (
        SELECT 1
        FROM character_relations
        WHERE from_character_id = NEW.from_character_id
          AND to_character_id = NEW.to_character_id
          AND relation_type = NEW.relation_type
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cette relation existe deja pour ce duo de personnages.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `character_species`
--

DROP TABLE IF EXISTS `character_species`;
CREATE TABLE IF NOT EXISTS `character_species` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `species_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `character_species_character_id_species_id_unique` (`character_id`,`species_id`),
  KEY `character_species_species_id_foreign` (`species_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `character_species`
--

INSERT INTO `character_species` (`id`, `character_id`, `species_id`, `created_at`, `updated_at`) VALUES
(1, 26, 1, '2026-03-18 11:58:48', '2026-03-18 11:58:48'),
(2, 27, 1, '2026-03-18 12:17:49', '2026-03-18 12:17:49'),
(3, 28, 1, '2026-03-19 06:38:00', '2026-03-19 06:38:00'),
(4, 29, 1, '2026-03-19 11:32:18', '2026-03-19 11:32:18'),
(5, 30, 1, '2026-03-19 12:00:39', '2026-03-19 12:00:39'),
(6, 31, 1, '2026-03-19 12:09:48', '2026-03-19 12:09:48'),
(7, 32, 1, '2026-03-19 12:21:03', '2026-03-19 12:21:03'),
(8, 33, 1, '2026-03-19 12:29:39', '2026-03-19 12:29:39'),
(9, 34, 1, '2026-03-19 12:34:21', '2026-03-19 12:34:21'),
(10, 35, 1, '2026-03-19 12:37:22', '2026-03-19 12:37:22'),
(11, 36, 1, '2026-03-19 12:48:01', '2026-03-19 12:48:01');

-- --------------------------------------------------------

--
-- Structure de la table `chronicles`
--

DROP TABLE IF EXISTS `chronicles`;
CREATE TABLE IF NOT EXISTS `chronicles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `event_place_id` bigint UNSIGNED DEFAULT NULL,
  `event_location` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chronicles_world` (`world_id`),
  KEY `chronicles_event_place_id_foreign` (`event_place_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chronicles`
--

INSERT INTO `chronicles` (`id`, `world_id`, `title`, `event_date`, `end_date`, `event_place_id`, `event_location`, `summary`, `content`, `status`, `created_at`, `updated_at`) VALUES
(3, 1, 'Première guerre', '2042-12-24', '2043-09-09', 3, 'Aria', 'La guerre éclate entre les Kitsunai et les Takunai. Des milliers de personnes perdent la vie', NULL, 'draft', '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(4, 1, 'Last Concert', '2061-12-24', '2061-12-24', NULL, NULL, 'Le dernier concert organisé par Smile Sound. Ils révèleront leur existence au monde entiers. Ceci fait partit d\'un plan dirigé par Mitsuki afin d\'évacuer des millier de Kitsunai vers deux portails et de rentrer sur l\'île sans perdre personne. Le plan fonctionnera, le seul blessé sera Mitsuki qui s\'est prit une balle dans le flan mais un fan apprenti médecin l\'a secouru, ils sont devenu par la suite ami.', NULL, 'draft', '2026-03-12 13:16:59', '2026-03-12 13:16:59');

-- --------------------------------------------------------

--
-- Structure de la table `chronicle_character`
--

DROP TABLE IF EXISTS `chronicle_character`;
CREATE TABLE IF NOT EXISTS `chronicle_character` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `chronicle_id` bigint UNSIGNED NOT NULL,
  `character_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chronicle_character_chronicle_id_character_id_unique` (`chronicle_id`,`character_id`),
  KEY `chronicle_character_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chronicle_character`
--

INSERT INTO `chronicle_character` (`id`, `chronicle_id`, `character_id`, `created_at`, `updated_at`) VALUES
(1, 2, 8, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(2, 2, 9, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(3, 2, 10, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(4, 2, 13, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(5, 2, 16, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(6, 2, 12, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(7, 2, 11, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(8, 2, 14, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(9, 2, 15, '2026-02-19 17:51:17', '2026-02-19 17:51:17'),
(10, 3, 16, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(11, 3, 8, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(12, 3, 9, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(13, 3, 20, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(14, 3, 10, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(15, 3, 11, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(16, 3, 12, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(17, 3, 13, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(18, 3, 14, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(19, 3, 15, '2026-03-06 18:48:19', '2026-03-06 18:48:19'),
(20, 4, 12, '2026-03-12 13:16:59', '2026-03-12 13:16:59'),
(21, 4, 13, '2026-03-12 13:16:59', '2026-03-12 13:16:59'),
(22, 4, 14, '2026-03-12 13:16:59', '2026-03-12 13:16:59'),
(23, 4, 15, '2026-03-12 13:16:59', '2026-03-12 13:16:59');

-- --------------------------------------------------------

--
-- Structure de la table `diplomas`
--

DROP TABLE IF EXISTS `diplomas`;
CREATE TABLE IF NOT EXISTS `diplomas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `faction_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diplomas_faction_id_foreign` (`faction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `factions`
--

DROP TABLE IF EXISTS `factions`;
CREATE TABLE IF NOT EXISTS `factions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `motto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `founded_at` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader_id` bigint UNSIGNED DEFAULT NULL,
  `co_leader_id` bigint UNSIGNED DEFAULT NULL,
  `founder_id` bigint UNSIGNED DEFAULT NULL,
  `logo_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factions_world_id_foreign` (`world_id`),
  KEY `factions_leader_id_foreign` (`leader_id`),
  KEY `factions_co_leader_id_foreign` (`co_leader_id`),
  KEY `factions_founder_id_foreign` (`founder_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faction_memberships`
--

DROP TABLE IF EXISTS `faction_memberships`;
CREATE TABLE IF NOT EXISTS `faction_memberships` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `faction_id` bigint UNSIGNED NOT NULL,
  `character_id` bigint UNSIGNED NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joined_at` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faction_memberships_faction_id_character_id_unique` (`faction_id`,`character_id`),
  KEY `faction_memberships_character_id_foreign` (`character_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `faction_memberships`
--

INSERT INTO `faction_memberships` (`id`, `faction_id`, `character_id`, `role`, `grade`, `joined_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 'Soldat', 'Première division', '2028-09-21', 'actif', '2026-03-17 11:52:48', '2026-03-17 11:52:48');

-- --------------------------------------------------------

--
-- Structure de la table `faction_relations`
--

DROP TABLE IF EXISTS `faction_relations`;
CREATE TABLE IF NOT EXISTS `faction_relations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `faction_id` bigint UNSIGNED NOT NULL,
  `related_faction_id` bigint UNSIGNED NOT NULL,
  `relation_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_bidirectional` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faction_relations_faction_id_foreign` (`faction_id`),
  KEY `faction_relations_related_faction_id_foreign` (`related_faction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_world_id_name_index` (`world_id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=335 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `world_id`, `name`, `description`, `is_default`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Agriculteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(2, NULL, 'Apiculteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(3, NULL, 'Arboriculteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(4, NULL, 'Éleveur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(5, NULL, 'Berger', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(6, NULL, 'Viticulteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(7, NULL, 'Vigneron', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(8, NULL, 'Maraîcher', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(9, NULL, 'Bûcheron', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(10, NULL, 'Pêcheur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(11, NULL, 'Boulanger', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(12, NULL, 'Pâtissier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(13, NULL, 'Fromager', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(14, NULL, 'Charcutier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(15, NULL, 'Boucher', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(16, NULL, 'Poissonnier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(17, NULL, 'Traiteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(18, NULL, 'Cuisinier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(19, NULL, 'Chef', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(20, NULL, 'Sommelier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(21, NULL, 'Serveur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(22, NULL, 'Barman', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(23, NULL, 'Brasseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(24, NULL, 'Distillateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(25, NULL, 'Chocolatier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(26, NULL, 'Confiseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(27, NULL, 'Glacier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(28, NULL, 'Meunier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(29, NULL, 'Maltier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(30, NULL, 'Forgeron', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(31, NULL, 'Armurier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(32, NULL, 'Ferronnier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(33, NULL, 'Orfèvre', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(34, NULL, 'Bijoutier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(35, NULL, 'Horloger', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(36, NULL, 'Tailleur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(37, NULL, 'Cordonnier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(38, NULL, 'Tanneur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(39, NULL, 'Tisseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(40, NULL, 'Tisserand', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(41, NULL, 'Teinturier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(42, NULL, 'Brodeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(43, NULL, 'Couturier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(44, NULL, 'Modiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(45, NULL, 'Sellier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(46, NULL, 'Charpentier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(47, NULL, 'Menuisier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(48, NULL, 'Ébéniste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(49, NULL, 'Maçon', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(50, NULL, 'Tailleur de pierre', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(51, NULL, 'Couvreur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(52, NULL, 'Zingueur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(53, NULL, 'Plâtrier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(54, NULL, 'Peintre en bâtiment', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(55, NULL, 'Verrier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(56, NULL, 'Céramiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(57, NULL, 'Potier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(58, NULL, 'Pipier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(59, NULL, 'Ingénieur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(60, NULL, 'Architecte', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(61, NULL, 'Urbaniste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(62, NULL, 'Géomètre', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(63, NULL, 'Cartographe', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(64, NULL, 'Arpenteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(65, NULL, 'Topographe', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(66, NULL, 'Scientifique', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(67, NULL, 'Physicien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(68, NULL, 'Chimiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(69, NULL, 'Biologiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(70, NULL, 'Botaniste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(71, NULL, 'Zoologiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(72, NULL, 'Astronome', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(73, NULL, 'Mathématicien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(74, NULL, 'Statisticien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(75, NULL, 'Inventeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(76, NULL, 'Technicien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(77, NULL, 'Mécanicien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(78, NULL, 'Électricien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(79, NULL, 'Électronicien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(80, NULL, 'Automaticien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(81, NULL, 'Roboticien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(82, NULL, 'Programmeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(83, NULL, 'Développeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(84, NULL, 'Analyste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(85, NULL, 'Data scientist', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(86, NULL, 'Testeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(87, NULL, 'Admin système', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(88, NULL, 'DevOps', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(89, NULL, 'Sécuritaire informatique', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(90, NULL, 'Hacker éthique', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(91, NULL, 'Médecin', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(92, NULL, 'Chirurgien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(93, NULL, 'Infirmier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(94, NULL, 'Sage-femme', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(95, NULL, 'Apothicaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(96, NULL, 'Herboriste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(97, NULL, 'Pharmacien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(98, NULL, 'Dentiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(99, NULL, 'Vétérinaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(100, NULL, 'Psychologue', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(101, NULL, 'Psychiatre', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(102, NULL, 'Kinésithérapeute', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(103, NULL, 'Ostéopathe', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(104, NULL, 'Ergothérapeute', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(105, NULL, 'Orthophoniste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(106, NULL, 'Ambulancier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(107, NULL, 'Guérisseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(108, NULL, 'Alchimiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(109, NULL, 'Nécromancien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(110, NULL, 'Prêtre', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(111, NULL, 'Moine', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(112, NULL, 'Pasteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(113, NULL, 'Imam', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(114, NULL, 'Rabbin', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(115, NULL, 'Chaman', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(116, NULL, 'Oracle', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(117, NULL, 'Prophète', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(118, NULL, 'Exorciste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(119, NULL, 'Missionnaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(120, NULL, 'Théologien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(121, NULL, 'Archiviste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(122, NULL, 'Historien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(123, NULL, 'Bibliothécaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(124, NULL, 'Scribe', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(125, NULL, 'Copiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(126, NULL, 'Chroniqueur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(127, NULL, 'Journaliste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(128, NULL, 'Écrivain', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(129, NULL, 'Poète', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(130, NULL, 'Dramaturge', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(131, NULL, 'Scénariste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(132, NULL, 'Acteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(133, NULL, 'Metteur en scène', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(134, NULL, 'Musicien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(135, NULL, 'Chanteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(136, NULL, 'Compositeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(137, NULL, 'Danseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(138, NULL, 'Peintre', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(139, NULL, 'Sculpteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(140, NULL, 'Illustrateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(141, NULL, 'Graveur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(142, NULL, 'Photographe', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(143, NULL, 'Cinéaste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(144, NULL, 'Animateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(145, NULL, 'Artisan', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(146, NULL, 'Designer', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(147, NULL, 'Styliste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(148, NULL, 'Militaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(149, NULL, 'Soldat', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(150, NULL, 'Officier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(151, NULL, 'Général', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(152, NULL, 'Capitaine', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(153, NULL, 'Marin', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(154, NULL, 'Pirate', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(155, NULL, 'Corsaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(156, NULL, 'Mercenaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(157, NULL, 'Chevalier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(158, NULL, 'Garde', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(159, NULL, 'Garde du corps', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(160, NULL, 'Archer', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(161, NULL, 'Piquier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(162, NULL, 'Lancier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(163, NULL, 'Cavalier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(164, NULL, 'Éclaireur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(165, NULL, 'Espion', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(166, NULL, 'Assassin', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(167, NULL, 'Tueur à gages', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(168, NULL, 'Détective', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(169, NULL, 'Enquêteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(170, NULL, 'Policier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(171, NULL, 'Gendarme', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(172, NULL, 'Shérif', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(173, NULL, 'Juge', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(174, NULL, 'Avocat', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(175, NULL, 'Procureur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(176, NULL, 'Notaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(177, NULL, 'Greffier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(178, NULL, 'Maître d’armes', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(179, NULL, 'Instructeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(180, NULL, 'Maître d’entraînement', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(181, NULL, 'Gladiateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(182, NULL, 'Dresseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(183, NULL, 'Chasseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(184, NULL, 'Trappeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(185, NULL, 'Pisteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(186, NULL, 'Marchand', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(187, NULL, 'Négociant', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(188, NULL, 'Commerçant', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(189, NULL, 'Épicier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(190, NULL, 'Libraire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(191, NULL, 'Antiquaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(192, NULL, 'Vendeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(193, NULL, 'Courtier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(194, NULL, 'Banquier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(195, NULL, 'Comptable', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(196, NULL, 'Trésorier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(197, NULL, 'Contrôleur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(198, NULL, 'Assureur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(199, NULL, 'Usurier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(200, NULL, 'Changeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(201, NULL, 'Explorateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(202, NULL, 'Aventurier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(203, NULL, 'Pionnier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(204, NULL, 'Guide', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(205, NULL, 'Pilote', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(206, NULL, 'Capitaine de navire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(207, NULL, 'Navigateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(208, NULL, 'Mécano', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(209, NULL, 'Exploitant minier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(210, NULL, 'Mineur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(211, NULL, 'Prospecteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(212, NULL, 'Géologue', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(213, NULL, 'Carrier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(214, NULL, 'Charretier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(215, NULL, 'Cocher', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(216, NULL, 'Messager', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(217, NULL, 'Postier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(218, NULL, 'Facteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(219, NULL, 'Coursier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(220, NULL, 'Forain', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(221, NULL, 'Saltimbanque', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(222, NULL, 'Jongleur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(223, NULL, 'Acrobate', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(224, NULL, 'Magicien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(225, NULL, 'Illusionniste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(226, NULL, 'Conteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(227, NULL, 'Barde', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(228, NULL, 'Maire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(229, NULL, 'Gouverneur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(230, NULL, 'Ministre', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(231, NULL, 'Conseiller', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(232, NULL, 'Diplomate', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(233, NULL, 'Ambassadeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(234, NULL, 'Administrateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(235, NULL, 'Fonctionnaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(236, NULL, 'Sénateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(237, NULL, 'Député', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(238, NULL, 'Roi', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(239, NULL, 'Reine', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(240, NULL, 'Prince', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(241, NULL, 'Princesse', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(242, NULL, 'Noble', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(243, NULL, 'Seigneur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(244, NULL, 'Intendant', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(245, NULL, 'Entrepreneur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(246, NULL, 'PDG', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(247, NULL, 'Directeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(248, NULL, 'Manager', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(249, NULL, 'Chef de projet', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(250, NULL, 'Responsable RH', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(251, NULL, 'Recruteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(252, NULL, 'Formateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(253, NULL, 'Coach', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(254, NULL, 'Gardien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(255, NULL, 'Concierge', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(256, NULL, 'Chauffeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(257, NULL, 'Pilote de course', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(258, NULL, 'Mécanicien avion', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(259, NULL, 'Capitaine de port', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(260, NULL, 'Douanier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(261, NULL, 'Géant de foire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(262, NULL, 'Pêcheur perlier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(263, NULL, 'Scaphandrier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(264, NULL, 'Plongeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(265, NULL, 'Explorateur spatial', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(266, NULL, 'Astronaute', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(267, NULL, 'Ingénieur spatial', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(268, NULL, 'Terraformeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(269, NULL, 'Xénobiologiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(270, NULL, 'Xénolinguiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(271, NULL, 'Cybernéticien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(272, NULL, 'Bio-ingénieur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(273, NULL, 'Généticien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(274, NULL, 'Clonologue', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(275, NULL, 'Surveillant', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(276, NULL, 'Éducateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(277, NULL, 'Professeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(278, NULL, 'Instituteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(279, NULL, 'Recteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(280, NULL, 'Doyen', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(281, NULL, 'Chercheur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(282, NULL, 'Étudiant', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(283, NULL, 'Architecte naval', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(284, NULL, 'Chantier naval', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(285, NULL, 'Charpentier naval', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(286, NULL, 'Matelot', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(287, NULL, 'Harponeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(288, NULL, 'Whaler', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(289, NULL, 'Gardien de prison', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(290, NULL, 'Geôlier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(291, NULL, 'Bourreau', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(292, NULL, 'Exécuteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(293, NULL, 'Révolutionnaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(294, NULL, 'Saboteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(295, NULL, 'Résistant', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(296, NULL, 'Pompier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(297, NULL, 'Sauveteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(298, NULL, 'Secouriste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(299, NULL, 'Survivaliste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(300, NULL, 'Maître d’hôtel', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(301, NULL, 'Technomancien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(302, NULL, 'Sorcière', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(303, NULL, 'Sorcier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(304, NULL, 'Mage', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(305, NULL, 'Enchanteur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(306, NULL, 'Évocateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(307, NULL, 'Invocationniste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(308, NULL, 'Druide', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(309, NULL, 'Ranger', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(310, NULL, 'Paladin', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(311, NULL, 'Clerc', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(312, NULL, 'Moine guerrier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(313, NULL, 'Samouraï', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(314, NULL, 'Ninja', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(315, NULL, 'Ronin', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(316, NULL, 'Pilier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(317, NULL, 'Dockeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(318, NULL, 'Manutentionnaire', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(319, NULL, 'Ouvrier', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(320, NULL, 'Soudeur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(321, NULL, 'Tourneur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(322, NULL, 'Fraiseur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(323, NULL, 'Usineur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(324, NULL, 'Chasseur de primes', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(325, NULL, 'Explorateur de ruines', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(326, NULL, 'Archéologue', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(327, NULL, 'Paléontologue', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(328, NULL, 'Conservateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(329, NULL, 'Gourou', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(330, NULL, 'Influenceur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(331, NULL, 'Orateur', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(332, NULL, 'Propagandiste', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(333, NULL, 'Stratège', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26'),
(334, NULL, 'Tacticien', NULL, 1, '2026-03-13 13:27:26', '2026-03-13 13:27:26');

-- --------------------------------------------------------

--
-- Structure de la table `lore_entries`
--

DROP TABLE IF EXISTS `lore_entries`;
CREATE TABLE IF NOT EXISTS `lore_entries` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `tags` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lore_entries_world_id_foreign` (`world_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(17, '2026_02_11_000130_create_character_jobs_table', 8),
(18, '2026_02_11_000140_add_spouse_id_to_characters_table', 9),
(19, '2026_02_13_000150_add_family_and_relation_metadata', 9),
(20, '2026_02_13_000160_add_has_brother_sister_to_characters_table', 9),
(21, '2026_02_18_120000_add_role_to_users_table', 10),
(22, '2026_02_18_130000_create_trends_table', 11),
(23, '2026_02_19_120000_create_chronicle_character_table', 12),
(24, '2026_02_19_190000_add_end_date_to_chronicles_table', 13),
(25, '2026_02_19_200000_add_event_location_to_chronicles_table', 14),
(26, '2026_02_19_210000_add_event_place_id_to_chronicles_table', 15),
(27, '2026_02_19_230000_add_voice_audio_path_to_characters_table', 16),
(28, '2026_02_19_231000_add_voice_youtube_url_to_characters_table', 17),
(29, '2026_02_20_000000_add_login_token_to_users_table', 18),
(30, '2026_03_06_100000_create_user_world_table', 19),
(31, '2026_03_06_110000_add_current_world_id_to_users_table', 19),
(32, '2026_03_06_120000_create_character_exes_table', 20),
(33, '2026_03_06_130000_add_type_to_places_table', 21),
(34, '2026_03_06_140000_add_coordinates_and_image_to_places_table', 22),
(35, '2026_03_06_141000_create_place_gallery_images_table', 22),
(36, '2026_03_06_142000_drop_coordinates_from_places_table', 23),
(37, '2026_03_06_150000_add_image_path_to_imaginary_maps_table', 24),
(38, '2026_03_06_160000_remove_maps_feature', 25),
(39, '2026_03_13_100000_create_factions_table', 26),
(40, '2026_03_13_110000_create_faction_memberships_table', 26),
(41, '2026_03_13_120000_create_faction_relations_table', 26),
(42, '2026_03_13_130000_create_diplomas_table', 27),
(43, '2026_03_13_140000_create_character_educations_table', 27),
(44, '2026_03_13_150000_add_details_to_factions_table', 27),
(45, '2026_03_13_160000_add_details_to_faction_memberships_table', 27),
(46, '2026_03_13_170000_create_lore_entries_table', 28),
(47, '2026_03_13_180000_create_species_table', 29),
(48, '2026_03_13_190000_create_character_species_table', 29),
(49, '2026_03_13_200000_create_jobs_table', 30),
(50, '2026_03_13_210000_add_job_id_to_character_jobs_table', 30),
(51, '2026_04_27_120000_create_oral_database_objects', 31);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `tokenable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_places_world` (`world_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `places`
--

INSERT INTO `places` (`id`, `world_id`, `name`, `type`, `region`, `summary`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'Laboratoire Akiyama', NULL, 'Akiyama', NULL, NULL, '2026-02-11 13:47:27', '2026-02-11 13:47:27'),
(3, 1, 'Aria', 'ile', 'Aria', NULL, NULL, '2026-02-11 13:47:45', '2026-03-06 18:08:06');

-- --------------------------------------------------------

--
-- Structure de la table `place_gallery_images`
--

DROP TABLE IF EXISTS `place_gallery_images`;
CREATE TABLE IF NOT EXISTS `place_gallery_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `place_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `place_gallery_images_place_id_foreign` (`place_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `species`
--

DROP TABLE IF EXISTS `species`;
CREATE TABLE IF NOT EXISTS `species` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `world_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `characteristics` text COLLATE utf8mb4_unicode_ci,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `lifespan` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `species_world_id_foreign` (`world_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `species`
--

INSERT INTO `species` (`id`, `world_id`, `name`, `characteristics`, `abilities`, `lifespan`, `origin`, `created_at`, `updated_at`) VALUES
(1, 1, 'Kitsunai', 'Animaux x Humain t\'inquiète', 'Pouvoir', 'Centaine d\'années', NULL, '2026-03-17 09:43:32', '2026-03-17 09:43:32');

-- --------------------------------------------------------

--
-- Structure de la table `trends`
--

DROP TABLE IF EXISTS `trends`;
CREATE TABLE IF NOT EXISTS `trends` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `character_id` bigint UNSIGNED NOT NULL,
  `title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `media_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trends_character_id_status_index` (`character_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','chanceux','utilisateur') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'utilisateur',
  `current_world_id` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_token_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_current_world_id_foreign` (`current_world_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `current_world_id`, `remember_token`, `login_token_hash`, `login_token_expires_at`, `created_at`, `updated_at`) VALUES
(1, 'Cloe-png', 'maximeblogueurs@gmail.com', NULL, '$2y$10$EisCPoGS32N/U/aIL90/luGcemb1wRHbJdekSvn2Y6z2ecrWCcMoK', 'admin', 1, NULL, '448bf3f101a7ab0fa857ad49ae645ab6eff2c2187c7e9ebd2237dbe6adba8ef1', '2026-04-29 15:24:04', '2026-02-18 14:01:50', '2026-04-29 07:24:04');

-- --------------------------------------------------------

--
-- Structure de la table `worlds`
--

DROP TABLE IF EXISTS `worlds`;
CREATE TABLE IF NOT EXISTS `worlds` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `geography_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pays',
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `worlds_slug_unique` (`slug`),
  KEY `worlds_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `worlds`
--

INSERT INTO `worlds` (`id`, `user_id`, `name`, `geography_type`, `slug`, `summary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Aria', 'ile', 'aria', NULL, 'active', '2026-02-11 09:42:33', '2026-02-11 09:42:33');

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
  ADD CONSTRAINT `chronicles_event_place_id_foreign` FOREIGN KEY (`event_place_id`) REFERENCES `places` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_chronicles_world` FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `places`
--
ALTER TABLE `places`
  ADD CONSTRAINT `fk_places_world` FOREIGN KEY (`world_id`) REFERENCES `worlds` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_current_world_id_foreign` FOREIGN KEY (`current_world_id`) REFERENCES `worlds` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `worlds`
--
ALTER TABLE `worlds`
  ADD CONSTRAINT `worlds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
