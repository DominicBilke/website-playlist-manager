-- OAuth Database Schema
-- Additional tables for OAuth authentication and platform connections

-- OAuth connections table for linking OAuth accounts to users
CREATE TABLE IF NOT EXISTS `oauth_connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` varchar(20) NOT NULL,
  `provider_user_id` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `picture` varchar(500) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provider_user` (`provider`, `provider_user_id`),
  KEY `user_id` (`user_id`),
  KEY `provider` (`provider`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- OAuth state table for CSRF protection
CREATE TABLE IF NOT EXISTS `oauth_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` varchar(255) NOT NULL,
  `provider` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `state` (`state`),
  KEY `provider` (`provider`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- OAuth login attempts table for security
CREATE TABLE IF NOT EXISTS `oauth_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` varchar(20) NOT NULL,
  `provider_user_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `success` tinyint(1) DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `provider` (`provider`),
  KEY `provider_user_id` (`provider_user_id`),
  KEY `email` (`email`),
  KEY `ip_address` (`ip_address`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_oauth_connections_user_provider` ON `oauth_connections` (`user_id`, `provider`);
CREATE INDEX IF NOT EXISTS `idx_oauth_connections_provider_user_id` ON `oauth_connections` (`provider`, `provider_user_id`);
CREATE INDEX IF NOT EXISTS `idx_oauth_states_created_at` ON `oauth_states` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_oauth_login_attempts_provider_created` ON `oauth_login_attempts` (`provider`, `created_at`);

-- Insert default system settings for OAuth
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('oauth_google_enabled', '0', 'Enable Google OAuth login'),
('oauth_apple_enabled', '0', 'Enable Apple OAuth login'),
('oauth_auto_link_email', '1', 'Automatically link OAuth accounts with matching email'),
('oauth_require_email', '1', 'Require email for OAuth registration'),
('oauth_default_team', '1', 'Default team for OAuth users'),
('oauth_default_office', 'OAuth', 'Default office for OAuth users');

-- Clean up old OAuth states (older than 1 hour)
DELETE FROM `oauth_states` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 1 HOUR); 