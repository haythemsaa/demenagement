-- Mises à jour de la base de données pour le support multi-pays
-- Exécuter après database.sql

USE demenagement_db;

-- Ajouter le support multi-pays aux demandes de devis
ALTER TABLE demandes_devis
ADD COLUMN country_code VARCHAR(2) DEFAULT 'FR' AFTER id,
ADD COLUMN language_code VARCHAR(2) DEFAULT 'fr' AFTER country_code,
ADD INDEX idx_country (country_code);

-- Ajouter le support multi-pays aux demandes de rappel
ALTER TABLE demandes_rappel
ADD COLUMN country_code VARCHAR(2) DEFAULT 'FR' AFTER id,
ADD COLUMN language_code VARCHAR(2) DEFAULT 'fr' AFTER country_code,
ADD INDEX idx_country (country_code);

-- Ajouter le support multi-pays aux déménageurs
ALTER TABLE demenageurs
ADD COLUMN country_code VARCHAR(2) DEFAULT 'FR' AFTER id,
ADD INDEX idx_country (country_code);

-- Table des pays (configuration dynamique)
CREATE TABLE IF NOT EXISTS countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(2) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    language_code VARCHAR(2) NOT NULL,

    -- Devise
    currency_code VARCHAR(3) NOT NULL,
    currency_symbol VARCHAR(5) NOT NULL,
    currency_decimals TINYINT DEFAULT 2,
    currency_decimal_sep VARCHAR(1) DEFAULT '.',
    currency_thousand_sep VARCHAR(1) DEFAULT ',',
    currency_symbol_position ENUM('before', 'after') DEFAULT 'after',

    -- Formats
    date_format VARCHAR(20) DEFAULT 'd/m/Y',
    time_format VARCHAR(20) DEFAULT 'H:i',

    -- Téléphone
    phone_code VARCHAR(5),
    phone_format VARCHAR(50),
    phone_regex VARCHAR(255),

    -- Code postal
    postal_code_regex VARCHAR(255),
    postal_code_placeholder VARCHAR(20),

    -- Unités
    unit_distance VARCHAR(10) DEFAULT 'km',
    unit_area VARCHAR(10) DEFAULT 'm²',
    unit_volume VARCHAR(10) DEFAULT 'm³',
    unit_weight VARCHAR(10) DEFAULT 'kg',

    -- Tarification par défaut
    base_price DECIMAL(10, 2) DEFAULT 500.00,
    price_per_sqm DECIMAL(10, 2) DEFAULT 8.00,
    price_per_km DECIMAL(10, 2) DEFAULT 1.50,
    floor_cost_per_level DECIMAL(10, 2) DEFAULT 100.00,
    lift_cost DECIMAL(10, 2) DEFAULT 250.00,
    house_multiplier DECIMAL(3, 2) DEFAULT 1.20,

    -- Statut
    active BOOLEAN DEFAULT TRUE,

    -- Timestamps
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des régions/états par pays
CREATE TABLE IF NOT EXISTS regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_code VARCHAR(2) NOT NULL,
    code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    region_group VARCHAR(100),

    -- Statut
    active BOOLEAN DEFAULT TRUE,

    UNIQUE KEY unique_region (country_code, code),
    FOREIGN KEY (country_code) REFERENCES countries(code) ON DELETE CASCADE,
    INDEX idx_country (country_code),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des traductions
CREATE TABLE IF NOT EXISTS translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    language_code VARCHAR(2) NOT NULL,
    translation_key VARCHAR(255) NOT NULL,
    translation_value TEXT NOT NULL,
    category VARCHAR(50),

    UNIQUE KEY unique_translation (language_code, translation_key),
    INDEX idx_language (language_code),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des paramètres globaux
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    category VARCHAR(50),
    description TEXT,

    INDEX idx_key (setting_key),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer les pays par défaut
INSERT INTO countries (code, name, language_code, currency_code, currency_symbol, phone_code, active, created_at) VALUES
('FR', 'France', 'fr', 'EUR', '€', '+33', TRUE, NOW()),
('US', 'United States', 'en', 'USD', '$', '+1', TRUE, NOW()),
('GB', 'United Kingdom', 'en', 'GBP', '£', '+44', TRUE, NOW()),
('ES', 'España', 'es', 'EUR', '€', '+34', TRUE, NOW()),
('DE', 'Deutschland', 'de', 'EUR', '€', '+49', TRUE, NOW()),
('IT', 'Italia', 'it', 'EUR', '€', '+39', TRUE, NOW()),
('CA', 'Canada', 'en', 'CAD', '$', '+1', TRUE, NOW()),
('AU', 'Australia', 'en', 'AUD', '$', '+61', TRUE, NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insérer quelques régions pour la France (exemple)
INSERT INTO regions (country_code, code, name, region_group) VALUES
('FR', '75', 'Paris', 'Île-de-France'),
('FR', '69', 'Rhône', 'Auvergne-Rhône-Alpes'),
('FR', '13', 'Bouches-du-Rhône', 'Provence-Alpes-Côte d\'Azur'),
('FR', '33', 'Gironde', 'Nouvelle-Aquitaine'),
('FR', '31', 'Haute-Garonne', 'Occitanie'),
('FR', '59', 'Nord', 'Hauts-de-France')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insérer quelques états pour les USA (exemple)
INSERT INTO regions (country_code, code, name, region_group) VALUES
('US', 'CA', 'California', 'West'),
('US', 'TX', 'Texas', 'South'),
('US', 'FL', 'Florida', 'South'),
('US', 'NY', 'New York', 'Northeast'),
('US', 'IL', 'Illinois', 'Midwest')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insérer quelques paramètres par défaut
INSERT INTO settings (setting_key, setting_value, setting_type, category, description) VALUES
('site_name', 'Déménageur.com', 'string', 'general', 'Nom du site'),
('default_country', 'FR', 'string', 'general', 'Pays par défaut'),
('default_language', 'fr', 'string', 'general', 'Langue par défaut'),
('quotes_per_request', '6', 'number', 'business', 'Nombre de devis par demande'),
('response_time_hours', '1', 'number', 'business', 'Temps de réponse en heures'),
('max_savings_percent', '40', 'number', 'business', 'Économies maximales en %'),
('enable_international', 'true', 'boolean', 'features', 'Activer déménagement international'),
('enable_business_moves', 'true', 'boolean', 'features', 'Activer déménagement entreprise'),
('maintenance_mode', 'false', 'boolean', 'system', 'Mode maintenance')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Vue pour les statistiques par pays
CREATE OR REPLACE VIEW stats_by_country AS
SELECT
    country_code,
    COUNT(*) as total_demandes,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as demandes_today,
    COUNT(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE()) THEN 1 END) as demandes_month,
    AVG(estimation_max) as prix_moyen,
    SUM(CASE WHEN statut = 'converti' THEN 1 ELSE 0 END) as conversions
FROM demandes_devis
GROUP BY country_code
ORDER BY total_demandes DESC;

-- Vue pour les déménageurs par pays
CREATE OR REPLACE VIEW movers_by_country AS
SELECT
    country_code,
    COUNT(*) as total_movers,
    COUNT(CASE WHEN actif = TRUE THEN 1 END) as active_movers,
    COUNT(CASE WHEN verifie = TRUE THEN 1 END) as verified_movers,
    AVG(note_moyenne) as avg_rating
FROM demenageurs
GROUP BY country_code
ORDER BY total_movers DESC;

COMMIT;
