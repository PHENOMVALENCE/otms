-- OTMS Schema Updates
-- Run this on existing database to add new features

-- Tasks: add title and priority (run once; ignore errors if columns exist)
ALTER TABLE `tasks`
  ADD COLUMN `title` VARCHAR(255) DEFAULT NULL AFTER `user_id`;
ALTER TABLE `tasks`
  ADD COLUMN `priority` VARCHAR(20) DEFAULT 'Medium' AFTER `status`;

-- Leaves: add applied_at
ALTER TABLE `leaves`
  ADD COLUMN `applied_at` TIMESTAMP NULL DEFAULT NULL;

-- Backfill applied_at for existing rows
UPDATE `leaves` SET `applied_at` = NOW() WHERE `applied_at` IS NULL;

-- Feedback table for contact form
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `priority` varchar(20) DEFAULT 'Medium',
  `contact_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
