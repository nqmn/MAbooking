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

INSERT INTO `#__mabooking_venues` (`title`, `alias`, `description`, `ordering`, `state`, `created`) VALUES
('Grand Ballroom', 'grand-ballroom', 'Main ballroom venue', 1, 1, NOW()),
('Exhibition Hall', 'exhibition-hall', 'Large-scale exhibition venue', 2, 1, NOW()),
('Bougainvillea Room', 'bougainvillea-room', 'Breakout and meeting rooms', 3, 1, NOW()),
('Town Hall', 'town-hall', 'Town Hall venue areas', 4, 1, NOW());

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Section 1', 'section-1', 400, 600, '10,300 sq ft', 'Grand Ballroom section 1', 1, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'grand-ballroom';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Section 2', 'section-2', 400, 600, '10,300 sq ft', 'Grand Ballroom section 2', 2, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'grand-ballroom';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Section 3', 'section-3', 400, 600, '10,300 sq ft', 'Grand Ballroom section 3', 3, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'grand-ballroom';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Exhibition Hall', 'exhibition-hall-main', 2000, 3000, '50,000 sq ft', 'Single large exhibition space', 1, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'exhibition-hall';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Room 1', 'room-1', 50, 80, '1,200 sq ft', 'Bougainvillea room 1', 1, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'bougainvillea-room';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Room 2', 'room-2', 50, 80, '1,200 sq ft', 'Bougainvillea room 2', 2, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'bougainvillea-room';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Room 3', 'room-3', 50, 80, '1,200 sq ft', 'Bougainvillea room 3', 3, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'bougainvillea-room';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Room 4', 'room-4', 50, 80, '1,200 sq ft', 'Bougainvillea room 4', 4, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'bougainvillea-room';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Room 5', 'room-5', 50, 80, '1,200 sq ft', 'Bougainvillea room 5', 5, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'bougainvillea-room';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Room 6', 'room-6', 50, 80, '1,200 sq ft', 'Bougainvillea room 6', 6, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'bougainvillea-room';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Main Hall', 'main-hall', 800, 1000, '8,500 sq ft', 'Town Hall main hall', 1, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'town-hall';

INSERT INTO `#__mabooking_spaces` (`venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`, `created`)
SELECT v.id, 'Level 1', 'level-1', 200, 300, '3,500 sq ft', 'Town Hall level 1', 2, 1, NOW()
FROM `#__mabooking_venues` v WHERE v.alias = 'town-hall';
