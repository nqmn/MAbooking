CREATE TABLE IF NOT EXISTS `#__mabooking_venues` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT NULL,
  `ordering` INT NOT NULL DEFAULT 0,
  `state` TINYINT NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__mabooking_spaces` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `venue_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `capacity_min` INT NOT NULL DEFAULT 0,
  `capacity_max` INT NOT NULL DEFAULT 0,
  `size_label` VARCHAR(255) NOT NULL DEFAULT '',
  `details` TEXT NULL,
  `ordering` INT NOT NULL DEFAULT 0,
  `state` TINYINT NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_iccbooking_spaces_venue` (`venue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__mabooking_bookings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_title` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL DEFAULT '',
  `booking_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `venue_id` INT UNSIGNED NOT NULL,
  `space_id` INT UNSIGNED NOT NULL,
  `client_name` VARCHAR(255) NOT NULL,
  `client_phone` VARCHAR(255) NOT NULL,
  `client_email` VARCHAR(255) NOT NULL,
  `attendees` INT NOT NULL DEFAULT 0,
  `status` VARCHAR(32) NOT NULL DEFAULT 'pending',
  `source` VARCHAR(32) NOT NULL DEFAULT 'admin',
  `article_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `state` TINYINT NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_iccbooking_bookings_date` (`booking_date`),
  KEY `idx_iccbooking_bookings_space` (`space_id`),
  KEY `idx_iccbooking_bookings_status` (`status`),
  KEY `idx_mabooking_bookings_article` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
