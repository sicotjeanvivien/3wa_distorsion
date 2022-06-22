SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `distorsion` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `distorsion`;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_category_id` (`name`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int unsigned NOT NULL,
  `content` varchar(4095) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `pinned` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;