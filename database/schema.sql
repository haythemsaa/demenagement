-- ============================================
-- Schéma de Base de Données - Déménageur.com
-- Version: 1.0
-- Date: 2024-11-19
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- 1. TABLES UTILISATEURS
-- ============================================

-- Table des utilisateurs finaux (clients particuliers)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `email_verified` (`email_verified`),
  KEY `verification_token` (`verification_token`),
  KEY `reset_token` (`reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des administrateurs
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','moderator') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. TABLES DEMANDES & LEADS
-- ============================================

-- Table des demandes de devis
CREATE TABLE `quote_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `move_type` enum('apartment','house','office','storage','international') NOT NULL,
  `from_address` text NOT NULL,
  `from_city` varchar(100) NOT NULL,
  `from_postal_code` varchar(10) NOT NULL,
  `from_country` varchar(2) DEFAULT 'FR',
  `to_address` text NOT NULL,
  `to_city` varchar(100) NOT NULL,
  `to_postal_code` varchar(10) NOT NULL,
  `to_country` varchar(2) DEFAULT 'FR',
  `move_date` date DEFAULT NULL,
  `surface_area` int(11) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `has_elevator` tinyint(1) DEFAULT NULL,
  `has_heavy_items` tinyint(1) DEFAULT 0,
  `needs_packing` tinyint(1) DEFAULT 0,
  `needs_storage` tinyint(1) DEFAULT 0,
  `message` text DEFAULT NULL,
  `status` enum('pending','assigned','quoted','accepted','rejected','completed') DEFAULT 'pending',
  `estimated_volume` decimal(10,2) DEFAULT NULL,
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `move_date` (`move_date`),
  KEY `from_country` (`from_country`),
  KEY `to_country` (`to_country`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `quote_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des demandes de rappel
CREATE TABLE `callback_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `preferred_time` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','called','completed','cancelled') DEFAULT 'pending',
  `called_at` datetime DEFAULT NULL,
  `called_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `called_by` (`called_by`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `callback_requests_ibfk_1` FOREIGN KEY (`called_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABLES DÉMÉNAGEURS PROFESSIONNELS
-- ============================================

-- Table principale des déménageurs
CREATE TABLE `demenageurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `siret` varchar(14) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `country` varchar(2) DEFAULT 'FR',
  `website` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo_url` varchar(512) DEFAULT NULL,
  `insurance_number` varchar(100) DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `fleet_size` int(11) DEFAULT 1,
  `staff_count` int(11) DEFAULT 1,
  `max_distance_km` int(11) DEFAULT 50,
  `status` enum('pending','active','suspended','inactive','rejected') DEFAULT 'pending',
  `verified` tinyint(1) DEFAULT 0,
  `verification_date` datetime DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `rating_average` decimal(3,2) DEFAULT NULL,
  `total_reviews` int(11) DEFAULT 0,
  `total_moves_completed` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `siret` (`siret`),
  KEY `status` (`status`),
  KEY `verified` (`verified`),
  KEY `city` (`city`),
  KEY `postal_code` (`postal_code`),
  KEY `country` (`country`),
  KEY `verified_by` (`verified_by`),
  CONSTRAINT `demenageurs_ibfk_1` FOREIGN KEY (`verified_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des zones de couverture des déménageurs
CREATE TABLE `demenageur_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demenageur_id` int(11) NOT NULL,
  `department_code` varchar(3) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `demenageur_id` (`demenageur_id`),
  KEY `department_code` (`department_code`),
  CONSTRAINT `demenageur_zones_ibfk_1` FOREIGN KEY (`demenageur_id`) REFERENCES `demenageurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des services proposés par les déménageurs
CREATE TABLE `demenageur_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demenageur_id` int(11) NOT NULL,
  `service_type` enum('packing','unpacking','furniture_assembly','storage','piano','antiques','international','office') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `demenageur_id` (`demenageur_id`),
  KEY `service_type` (`service_type`),
  CONSTRAINT `demenageur_services_ibfk_1` FOREIGN KEY (`demenageur_id`) REFERENCES `demenageurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. TABLES ABONNEMENTS & PAIEMENTS
-- ============================================

-- Table des plans d'abonnement
CREATE TABLE `subscription_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price_monthly` decimal(10,2) NOT NULL,
  `price_yearly` decimal(10,2) DEFAULT NULL,
  `leads_per_month` int(11) DEFAULT NULL COMMENT 'NULL = illimité',
  `features_list` text DEFAULT NULL COMMENT 'JSON array',
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des abonnements actifs
CREATE TABLE `demenageur_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demenageur_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `stripe_subscription_id` varchar(255) DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `status` enum('trial','active','past_due','canceled','incomplete','paused') DEFAULT 'trial',
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `next_billing_date` datetime DEFAULT NULL,
  `auto_renew` tinyint(1) DEFAULT 1,
  `leads_used_this_month` int(11) DEFAULT 0,
  `leads_reset_date` datetime DEFAULT NULL,
  `payment_failed_count` int(11) DEFAULT 0,
  `cancel_at_period_end` tinyint(1) DEFAULT 0,
  `canceled_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `demenageur_id` (`demenageur_id`),
  KEY `plan_id` (`plan_id`),
  KEY `status` (`status`),
  KEY `stripe_subscription_id` (`stripe_subscription_id`),
  KEY `stripe_customer_id` (`stripe_customer_id`),
  CONSTRAINT `demenageur_subscriptions_ibfk_1` FOREIGN KEY (`demenageur_id`) REFERENCES `demenageurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `demenageur_subscriptions_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des transactions
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demenageur_id` int(11) NOT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `stripe_payment_id` varchar(255) DEFAULT NULL,
  `stripe_invoice_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'EUR',
  `status` enum('pending','succeeded','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `metadata` text DEFAULT NULL COMMENT 'JSON',
  `failed_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `demenageur_id` (`demenageur_id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `stripe_payment_id` (`stripe_payment_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`demenageur_id`) REFERENCES `demenageurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`subscription_id`) REFERENCES `demenageur_subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. TABLES LEADS & ATTRIBUTION
-- ============================================

-- Table des leads attribués aux déménageurs
CREATE TABLE `demenageur_leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demenageur_id` int(11) NOT NULL,
  `quote_request_id` int(11) NOT NULL,
  `status` enum('new','viewed','contacted','quoted','won','lost','expired') DEFAULT 'new',
  `price_quoted` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `viewed_at` datetime DEFAULT NULL,
  `contacted_at` datetime DEFAULT NULL,
  `quoted_at` datetime DEFAULT NULL,
  `response_time_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `demenageur_id` (`demenageur_id`),
  KEY `quote_request_id` (`quote_request_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `demenageur_leads_ibfk_1` FOREIGN KEY (`demenageur_id`) REFERENCES `demenageurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `demenageur_leads_ibfk_2` FOREIGN KEY (`quote_request_id`) REFERENCES `quote_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réponses aux leads
CREATE TABLE `lead_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_id` int(11) NOT NULL,
  `demenageur_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `quoted_price` decimal(10,2) DEFAULT NULL,
  `attachment_url` varchar(512) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  KEY `demenageur_id` (`demenageur_id`),
  CONSTRAINT `lead_responses_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `demenageur_leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_responses_ibfk_2` FOREIGN KEY (`demenageur_id`) REFERENCES `demenageurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. TABLES PARTENAIRES & GÉOGRAPHIE
-- ============================================

-- Table des partenaires déménageurs (ancienne version)
CREATE TABLE `movers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'FR',
  `region` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_url` varchar(512) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `country` (`country`),
  KEY `region` (`region`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des pays supportés
CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_fr` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des régions
CREATE TABLE `regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(2) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `country_code` (`country_code`),
  KEY `slug` (`slug`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`country_code`) REFERENCES `countries` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. TABLES SYSTÈME
-- ============================================

-- Table des paramètres système
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des logs d'activité
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` enum('admin','demenageur','user') NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_type` (`user_type`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `entity_type` (`entity_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

-- ============================================
-- INDEX SUPPLÉMENTAIRES POUR PERFORMANCE
-- ============================================

-- Index composites pour requêtes fréquentes
ALTER TABLE `demenageur_leads`
  ADD INDEX `idx_demenageur_status` (`demenageur_id`, `status`),
  ADD INDEX `idx_created_status` (`created_at`, `status`);

ALTER TABLE `quote_requests`
  ADD INDEX `idx_status_date` (`status`, `move_date`),
  ADD INDEX `idx_country_city` (`from_country`, `from_city`);

ALTER TABLE `demenageur_subscriptions`
  ADD INDEX `idx_demenageur_status` (`demenageur_id`, `status`),
  ADD INDEX `idx_next_billing` (`next_billing_date`, `status`);

-- ============================================
-- FIN DU SCHÉMA
-- ============================================
