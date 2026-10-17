-- FraudGuard Sentinel - Database Schema (MySQL / MariaDB / SQLite compatible)
-- Database Name: fraud_sentinel

CREATE TABLE IF NOT EXISTS `incidents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `reference_id` VARCHAR(32) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `category` VARCHAR(64) NOT NULL,
    `severity` VARCHAR(32) NOT NULL DEFAULT 'Medium',
    `status` VARCHAR(32) NOT NULL DEFAULT 'New',
    `amount_lost` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
    `incident_date` DATETIME NOT NULL,
    `reporter_name` VARCHAR(128) NULL,
    `reporter_email` VARCHAR(160) NULL,
    `reporter_phone` VARCHAR(64) NULL,
    `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
    `suspect_details` TEXT NULL,
    `description` TEXT NOT NULL,
    `assigned_investigator` VARCHAR(128) NOT NULL DEFAULT 'Unassigned',
    `risk_score` INT NOT NULL DEFAULT 50,
    `ip_address` VARCHAR(45) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_reference` (`reference_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_severity` (`severity`),
    INDEX `idx_category` (`category`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `incident_timeline` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `incident_id` INT NOT NULL,
    `author` VARCHAR(128) NOT NULL,
    `action_type` VARCHAR(64) NOT NULL,
    `note` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_incident_timeline` (`incident_id`),
    CONSTRAINT `fk_incident_timeline` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(128) NOT NULL,
    `email` VARCHAR(160) NOT NULL UNIQUE,
    `username` VARCHAR(64) NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(32) NOT NULL DEFAULT 'user',
    `avatar` VARCHAR(8) NOT NULL DEFAULT 'U',
    `department` VARCHAR(128) NULL DEFAULT 'General',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login` DATETIME NULL,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_username` (`username`),
    INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `action` VARCHAR(64) NOT NULL,
    `ip_address` VARCHAR(45),
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_sessions_user` (`user_id`),
    CONSTRAINT `fk_user_sessions` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

