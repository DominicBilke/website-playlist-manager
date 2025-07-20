-- Playlist Manager Database Schema
-- This file contains the complete database structure for the Playlist Manager application

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `team` int(11) DEFAULT NULL,
  `office` varchar(100) DEFAULT NULL,
  `days` varchar(50) DEFAULT '1,2,3,4,5',
  `daytime_from` time DEFAULT '09:00:00',
  `daytime_to` time DEFAULT '17:00:00',
  `days_random` tinyint(1) DEFAULT 0,
  `daytime_random` tinyint(1) DEFAULT 0,
  `playlist_id` varchar(255) DEFAULT NULL,
  `apple_playlist_id` varchar(255) DEFAULT NULL,
  `youtube_playlist_id` varchar(255) DEFAULT NULL,
  `amazon_playlist_id` varchar(255) DEFAULT NULL,
  `db_token` varchar(10) DEFAULT '1',
  `login_counter` int(11) DEFAULT 0,
  `playing_time` text DEFAULT NULL,
  `currently_playing` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `team` (`team`),
  KEY `office` (`office`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User settings table for additional user preferences
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `time_range` varchar(20) DEFAULT 'medium_term',
  `active_days` varchar(50) DEFAULT '1,2,3,4,5',
  `auto_play` tinyint(1) DEFAULT 1,
  `shuffle` tinyint(1) DEFAULT 1,
  `repeat` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Playlist history table
CREATE TABLE IF NOT EXISTS `playlist_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `platform` varchar(20) NOT NULL,
  `playlist_id` varchar(255) NOT NULL,
  `playlist_name` varchar(255) DEFAULT NULL,
  `action` varchar(20) NOT NULL,
  `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `platform` (`platform`),
  KEY `timestamp` (`timestamp`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listening statistics table
CREATE TABLE IF NOT EXISTS `listening_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `platform` varchar(20) NOT NULL,
  `playlist_id` varchar(255) DEFAULT NULL,
  `track_id` varchar(255) DEFAULT NULL,
  `track_name` varchar(255) DEFAULT NULL,
  `artist` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `played_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `session_duration` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `platform` (`platform`),
  KEY `played_at` (`played_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- API tokens table for platform integrations
CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `platform` varchar(20) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_platform` (`user_id`, `platform`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System settings table
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default system settings
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('default_play_time', '300', 'Default play time in seconds'),
('default_pause_time', '60', 'Default pause time in seconds'),
('max_play_time', '600', 'Maximum play time in seconds'),
('max_pause_time', '600', 'Maximum pause time in seconds'),
('session_timeout', '3600', 'Session timeout in seconds'),
('maintenance_mode', '0', 'Maintenance mode (0=off, 1=on)');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_users_team` ON `users` (`team`);
CREATE INDEX IF NOT EXISTS `idx_users_office` ON `users` (`office`);
CREATE INDEX IF NOT EXISTS `idx_users_currently_playing` ON `users` (`currently_playing`);
CREATE INDEX IF NOT EXISTS `idx_listening_stats_user_platform` ON `listening_stats` (`user_id`, `platform`);
CREATE INDEX IF NOT EXISTS `idx_listening_stats_played_at` ON `listening_stats` (`played_at`);
CREATE INDEX IF NOT EXISTS `idx_api_tokens_expires_at` ON `api_tokens` (`expires_at`);

-- Add some sample data for testing (optional)
-- INSERT INTO `users` (`login`, `password`, `team`, `office`) VALUES
-- ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Berlin'),
-- ('testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Hamburg'); 