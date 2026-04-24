ALTER TABLE `#__mabooking_bookings`
	ADD COLUMN `article_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `source`;

ALTER TABLE `#__mabooking_bookings`
	ADD KEY `idx_mabooking_bookings_article` (`article_id`);
