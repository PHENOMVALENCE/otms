-- Multi-user task assignment
-- Run once on existing database. Adds task_assignments and migrates existing data.

-- Drop tasks FK (ignore error if already dropped), then make user_id nullable
ALTER TABLE `tasks` DROP FOREIGN KEY `tasks_ibfk_1`;
ALTER TABLE `tasks` MODIFY `user_id` int(11) DEFAULT NULL;

-- Junction table: many users per task
CREATE TABLE IF NOT EXISTS `task_assignments` (
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`task_id`, `user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `task_assignments_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_assignments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Migrate existing single-assignments
INSERT IGNORE INTO `task_assignments` (`task_id`, `user_id`)
SELECT `id`, `user_id` FROM `tasks` WHERE `user_id` IS NOT NULL;
