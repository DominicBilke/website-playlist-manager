-- Playlist Manager Database Schema
-- This file contains the complete database structure for the Playlist Manager application

-- Users table with admin functionality
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `team` int(11) DEFAULT NULL,
  `office` varchar(100) DEFAULT NULL,
  `role` enum('user','admin','super_admin') DEFAULT 'user',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
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
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `team` (`team`),
  KEY `office` (`office`),
  KEY `role` (`role`),
  KEY `status` (`status`)
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

-- Admin audit log table
CREATE TABLE IF NOT EXISTS `admin_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User sessions table for better session management
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `user_id` (`user_id`),
  KEY `last_activity` (`last_activity`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default system settings
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('default_play_time', '300', 'Default play time in seconds'),
('default_pause_time', '60', 'Default pause time in seconds'),
('max_play_time', '600', 'Maximum play time in seconds'),
('max_pause_time', '600', 'Maximum pause time in seconds'),
('session_timeout', '3600', 'Session timeout in seconds'),
('maintenance_mode', '0', 'Maintenance mode (0=off, 1=on)'),
('registration_enabled', '1', 'User registration enabled (0=off, 1=on)'),
('email_verification_required', '0', 'Email verification required (0=off, 1=on)'),
('max_login_attempts', '5', 'Maximum login attempts before lockout'),
('lockout_duration', '900', 'Account lockout duration in seconds');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_users_team` ON `users` (`team`);
CREATE INDEX IF NOT EXISTS `idx_users_office` ON `users` (`office`);
CREATE INDEX IF NOT EXISTS `idx_users_currently_playing` ON `users` (`currently_playing`);
CREATE INDEX IF NOT EXISTS `idx_listening_stats_user_platform` ON `listening_stats` (`user_id`, `platform`);
CREATE INDEX IF NOT EXISTS `idx_listening_stats_played_at` ON `listening_stats` (`played_at`);
CREATE INDEX IF NOT EXISTS `idx_api_tokens_expires_at` ON `api_tokens` (`expires_at`);
CREATE INDEX IF NOT EXISTS `idx_admin_audit_log_admin_action` ON `admin_audit_log` (`admin_id`, `action`);

-- Insert default admin user (password: admin123)
INSERT IGNORE INTO `users` (`login`, `password`, `email`, `team`, `office`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@playlist-manager.de', 1, 'Berlin', 'super_admin', 'active');

-- Insert sample user for testing (password: test123)
INSERT IGNORE INTO `users` (`login`, `password`, `email`, `team`, `office`, `role`, `status`) VALUES
('testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test@example.com', 2, 'Hamburg', 'user', 'active'); 