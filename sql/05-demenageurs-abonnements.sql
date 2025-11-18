-- =====================================================
-- SYSTÈME DÉMÉNAGEURS & ABONNEMENTS
-- =====================================================

-- Table des déménageurs professionnels
CREATE TABLE IF NOT EXISTS demenageurs (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Informations entreprise
    company_name VARCHAR(200) NOT NULL,
    siret VARCHAR(14) UNIQUE NOT NULL,
    legal_form ENUM('SARL', 'SAS', 'SASU', 'EURL', 'EI', 'SA', 'AUTRE') DEFAULT 'SARL',

    -- Contact principal
    contact_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    mobile VARCHAR(20),

    -- Adresse siège social
    address VARCHAR(255) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(2) DEFAULT 'FR',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),

    -- Zones de couverture (JSON array de codes postaux/départements)
    coverage_zones TEXT, -- JSON: ["75", "92", "93", "94", "95"]
    max_distance_km INT DEFAULT 50, -- Distance maximale d'intervention

    -- Capacités et spécialités
    fleet_size INT DEFAULT 1, -- Nombre de véhicules
    staff_count INT DEFAULT 2, -- Nombre d'employés
    specialties TEXT, -- JSON: ["piano", "oeuvres_art", "international", "entreprise"]
    services_offered TEXT, -- JSON: ["emballage", "montage", "demontage", "stockage", "lift"]

    -- Certifications et assurances
    certifications TEXT, -- JSON: ["ISO9001", "Qualicert", "FIDI"]
    insurance_amount DECIMAL(12, 2) DEFAULT 30000.00,
    insurance_company VARCHAR(100),
    insurance_expiry DATE,

    -- Statut et validation
    status ENUM('pending', 'active', 'suspended', 'rejected') DEFAULT 'pending',
    verified BOOLEAN DEFAULT FALSE,
    verification_date DATETIME,
    verified_by INT, -- admin_id

    -- Statistiques
    total_quotes_sent INT DEFAULT 0,
    total_quotes_won INT DEFAULT 0,
    average_rating DECIMAL(3, 2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    response_rate DECIMAL(5, 2) DEFAULT 0.00, -- Pourcentage
    average_response_time INT DEFAULT 0, -- En heures

    -- Documents
    logo_url VARCHAR(255),
    kbis_url VARCHAR(255),
    insurance_url VARCHAR(255),
    rcp_url VARCHAR(255),

    -- Préférences notifications
    email_notifications BOOLEAN DEFAULT TRUE,
    sms_notifications BOOLEAN DEFAULT FALSE,
    instant_notifications BOOLEAN DEFAULT TRUE,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME,

    INDEX idx_postal_code (postal_code),
    INDEX idx_city (city),
    INDEX idx_status (status),
    INDEX idx_verified (verified),
    INDEX idx_location (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plans d'abonnement disponibles
CREATE TABLE IF NOT EXISTS subscription_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(50) NOT NULL, -- 'Gratuit', 'Basic', 'Pro', 'Premium'
    slug VARCHAR(50) UNIQUE NOT NULL,

    -- Tarification
    price_monthly DECIMAL(10, 2) DEFAULT 0.00,
    price_yearly DECIMAL(10, 2) DEFAULT 0.00,
    setup_fee DECIMAL(10, 2) DEFAULT 0.00,

    -- Limites
    leads_per_month INT DEFAULT 0, -- 0 = illimité
    price_per_extra_lead DECIMAL(8, 2) DEFAULT 0.00,
    coverage_zones_max INT DEFAULT 1, -- Nombre de zones couvertes

    -- Fonctionnalités (JSON)
    features TEXT, -- {"priority": 1, "analytics": true, "api_access": false}

    -- Priorité dans le matching (1 = plus haute priorité)
    matching_priority INT DEFAULT 5,

    -- Visibilité
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,

    -- Description
    description TEXT,
    features_list TEXT, -- JSON array pour affichage

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Abonnements actifs des déménageurs
CREATE TABLE IF NOT EXISTS demenageur_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,

    demenageur_id INT NOT NULL,
    plan_id INT NOT NULL,

    -- Période
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    billing_cycle ENUM('monthly', 'yearly') DEFAULT 'monthly',

    -- Statut
    status ENUM('active', 'cancelled', 'expired', 'suspended') DEFAULT 'active',
    auto_renew BOOLEAN DEFAULT TRUE,

    -- Facturation
    amount_paid DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50), -- 'stripe', 'paypal', 'bank_transfer', 'check'
    payment_reference VARCHAR(100),
    last_payment_date DATE,
    next_billing_date DATE,

    -- Consommation
    leads_used_this_month INT DEFAULT 0,
    leads_reset_date DATE,
    extra_leads_purchased INT DEFAULT 0,

    -- Tracking
    cancelled_at DATETIME,
    cancelled_by VARCHAR(50), -- 'user', 'admin', 'payment_failed'
    cancellation_reason TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (demenageur_id) REFERENCES demenageurs(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    INDEX idx_demenageur (demenageur_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leads (devis) envoyés aux déménageurs
CREATE TABLE IF NOT EXISTS demenageur_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,

    quote_request_id INT NOT NULL, -- ID de la demande de devis client
    demenageur_id INT NOT NULL,

    -- Statut du lead
    status ENUM('sent', 'viewed', 'quoted', 'won', 'lost', 'expired', 'declined') DEFAULT 'sent',

    -- Prix facturé au déménageur pour ce lead
    lead_cost DECIMAL(8, 2) DEFAULT 0.00,
    is_charged BOOLEAN DEFAULT FALSE,

    -- Détails envoyés (snapshot pour historique)
    lead_details TEXT, -- JSON avec toutes les infos du devis

    -- Tracking
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    viewed_at DATETIME,
    quoted_at DATETIME,
    quote_amount DECIMAL(10, 2), -- Montant du devis envoyé par le déménageur
    quote_details TEXT, -- JSON

    response_time_hours INT, -- Temps de réponse en heures

    -- Résultat
    won_at DATETIME,
    lost_reason VARCHAR(255),
    client_feedback TEXT,

    -- Expiration
    expires_at DATETIME, -- Lead expire après X jours

    INDEX idx_quote (quote_request_id),
    INDEX idx_demenageur (demenageur_id),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at),

    FOREIGN KEY (quote_request_id) REFERENCES quote_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (demenageur_id) REFERENCES demenageurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Avis clients sur les déménageurs
CREATE TABLE IF NOT EXISTS demenageur_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,

    demenageur_id INT NOT NULL,
    client_id INT, -- Peut être NULL si client non inscrit
    lead_id INT, -- Lead/devis concerné

    -- Évaluation
    overall_rating INT NOT NULL CHECK (overall_rating BETWEEN 1 AND 5),
    professionalism_rating INT CHECK (professionalism_rating BETWEEN 1 AND 5),
    punctuality_rating INT CHECK (punctuality_rating BETWEEN 1 AND 5),
    care_rating INT CHECK (care_rating BETWEEN 1 AND 5),
    price_rating INT CHECK (price_rating BETWEEN 1 AND 5),

    -- Commentaire
    review_title VARCHAR(200),
    review_text TEXT,

    -- Infos client (si non inscrit)
    client_name VARCHAR(100),
    client_email VARCHAR(255),

    -- Recommandation
    would_recommend BOOLEAN,
    moving_date DATE,

    -- Validation
    status ENUM('pending', 'approved', 'rejected', 'reported') DEFAULT 'pending',
    moderation_notes TEXT,
    moderated_by INT, -- admin_id
    moderated_at DATETIME,

    -- Réponse du déménageur
    response_text TEXT,
    response_at DATETIME,

    -- Visibilité
    is_visible BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (demenageur_id) REFERENCES demenageurs(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (lead_id) REFERENCES demenageur_leads(id) ON DELETE SET NULL,

    INDEX idx_demenageur (demenageur_id),
    INDEX idx_status (status),
    INDEX idx_rating (overall_rating),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Historique des paiements
CREATE TABLE IF NOT EXISTS demenageur_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    demenageur_id INT NOT NULL,
    subscription_id INT,

    -- Type de paiement
    payment_type ENUM('subscription', 'extra_leads', 'setup_fee', 'refund') NOT NULL,

    -- Montants
    amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,

    -- Méthode et référence
    payment_method VARCHAR(50),
    payment_provider VARCHAR(50), -- 'stripe', 'paypal'
    transaction_id VARCHAR(255),
    invoice_number VARCHAR(50),

    -- Statut
    status ENUM('pending', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',

    -- Détails
    description TEXT,
    metadata TEXT, -- JSON

    -- Dates
    payment_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (demenageur_id) REFERENCES demenageurs(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES demenageur_subscriptions(id) ON DELETE SET NULL,

    INDEX idx_demenageur (demenageur_id),
    INDEX idx_status (status),
    INDEX idx_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES PAR DÉFAUT - PLANS D'ABONNEMENT
-- =====================================================

INSERT INTO subscription_plans (name, slug, price_monthly, price_yearly, leads_per_month, price_per_extra_lead, coverage_zones_max, matching_priority, features, features_list, description, display_order, is_active) VALUES

-- Plan Gratuit (pour tester)
('Gratuit', 'free', 0.00, 0.00, 5, 15.00, 1, 5,
'{"priority": 5, "analytics": false, "api_access": false, "support": "email", "response_guarantee": false}',
'["5 leads par mois", "1 zone de couverture", "Support email", "Profil basique"]',
'Idéal pour tester notre plateforme sans engagement', 1, TRUE),

-- Plan Basic (débutants)
('Basic', 'basic', 79.00, 790.00, 20, 12.00, 2, 4,
'{"priority": 4, "analytics": true, "api_access": false, "support": "email", "response_guarantee": false, "badge": "verified"}',
'["20 leads par mois", "2 zones de couverture", "Statistiques basiques", "Badge \"Vérifié\"", "Support email prioritaire", "12€/lead supplémentaire"]',
'Pour les petites entreprises qui démarrent', 2, TRUE),

-- Plan Pro (le plus populaire)
('Pro', 'pro', 199.00, 1990.00, 60, 10.00, 5, 2,
'{"priority": 2, "analytics": true, "api_access": true, "support": "phone", "response_guarantee": true, "badge": "premium", "featured_listing": true}',
'["60 leads par mois", "5 zones de couverture", "Priorité dans le matching", "Badge \"Premium\"", "Analytics avancées", "API access", "Support téléphonique", "Mise en avant profil", "10€/lead supplémentaire"]',
'Le meilleur rapport qualité/prix - Recommandé', 3, TRUE),

-- Plan Premium (gros volumes)
('Premium', 'premium', 399.00, 3990.00, 150, 8.00, 20, 1,
'{"priority": 1, "analytics": true, "api_access": true, "support": "dedicated", "response_guarantee": true, "badge": "elite", "featured_listing": true, "custom_branding": true, "priority_support": true}',
'["150 leads par mois", "Couverture nationale (jusqu\'à 20 zones)", "PRIORITÉ MAXIMALE", "Badge \"Elite\"", "Analytics complètes + export", "API access avancée", "Account manager dédié", "Branding personnalisé", "Support 7j/7", "8€/lead supplémentaire"]',
'Pour les grandes entreprises multi-sites', 4, TRUE),

-- Plan Entreprise (sur mesure)
('Entreprise', 'enterprise', 0.00, 0.00, 0, 0.00, 999, 1,
'{"priority": 1, "analytics": true, "api_access": true, "support": "dedicated", "response_guarantee": true, "badge": "elite", "featured_listing": true, "custom_branding": true, "priority_support": true, "white_label": true, "custom_integration": true}',
'["Leads illimités", "Couverture européenne", "Solution sur mesure", "White label possible", "Intégration CRM", "Formation équipe", "SLA garanti", "Tarification négociée"]',
'Solution personnalisée pour les grands groupes', 5, TRUE);

-- =====================================================
-- DONNÉES DE TEST - DÉMÉNAGEURS
-- =====================================================

INSERT INTO demenageurs (company_name, siret, contact_name, email, password_hash, phone, address, postal_code, city, latitude, longitude, coverage_zones, fleet_size, staff_count, specialties, services_offered, certifications, status, verified) VALUES

('DéménaPro Paris', '12345678901234', 'Jean Dupont', 'contact@demenapro-paris.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01 42 12 34 56', '123 Avenue de la République', '75011', 'Paris', 48.8566, 2.3522, '["75", "92", "93", "94"]', 5, 12, '["piano", "oeuvres_art"]', '["emballage", "montage", "demontage", "stockage", "lift"]', '["ISO9001", "Qualicert"]', 'active', TRUE),

('TransDem Lyon', '23456789012345', 'Marie Martin', 'contact@transdem-lyon.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '04 78 23 45 67', '45 Rue de la Part-Dieu', '69003', 'Lyon', 45.7640, 4.8357, '["69", "42", "01"]', 3, 8, '["international", "entreprise"]', '["emballage", "montage", "stockage"]', '["FIDI"]', 'active', TRUE),

('Déménagement Sud', '34567890123456', 'Pierre Lefebvre', 'contact@demenagement-sud.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '04 91 34 56 78', '78 Boulevard Michelet', '13008', 'Marseille', 43.2965, 5.3698, '["13", "83", "84"]', 4, 10, '["entreprise"]', '["emballage", "montage", "demontage"]', '["ISO9001"]', 'active', TRUE),

('MoveFast Bordeaux', '45678901234567', 'Sophie Dubois', 'contact@movefast-bordeaux.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '05 56 45 67 89', '12 Cours de l\'Intendance', '33000', 'Bordeaux', 44.8378, -0.5792, '["33", "40", "47"]', 2, 6, '["piano"]', '["emballage", "montage"]', NULL, 'active', TRUE),

('Express Déménagement', '56789012345678', 'Thomas Rousseau', 'contact@express-demenagement.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '04 76 56 78 90', '23 Avenue Alsace-Lorraine', '38000', 'Grenoble', 45.1885, 5.7245, '["38", "73", "74"]', 3, 7, '["international"]', '["emballage", "stockage"]', '["Qualicert"]', 'active', TRUE);
