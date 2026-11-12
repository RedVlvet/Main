-- Import this file in phpMyAdmin to create the application database and table.
CREATE DATABASE IF NOT EXISTS `kmk_reservations`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `kmk_reservations`;

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_key` char(64) NOT NULL,
  `service` enum('restaurants', 'rooms', 'drivers') NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `reservation_date` date NOT NULL,
  `checkout_date` date DEFAULT NULL,
  `reservation_time` time NOT NULL,
  `adults` tinyint unsigned NOT NULL,
  `children` tinyint unsigned NOT NULL DEFAULT 0,
  `rooms` tinyint unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reservations_session_date` (`session_key`, `reservation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
