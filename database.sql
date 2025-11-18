-- Base de données pour le site Déménageur.com
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS demenagement_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE demenagement_db;

-- Table des demandes de devis
CREATE TABLE IF NOT EXISTS demandes_devis (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Informations de déménagement
    depart_address VARCHAR(255) NOT NULL,
    depart_postal VARCHAR(5) NOT NULL,
    arrivee_address VARCHAR(255) NOT NULL,
    arrivee_postal VARCHAR(5) NOT NULL,

    -- Détails du logement
    type_depart ENUM('appartement', 'maison') NOT NULL,
    superficie INT NOT NULL,
    pieces INT NOT NULL,

    -- Accessibilité
    etage_depart INT DEFAULT 0,
    ascenseur_depart ENUM('oui', 'non') DEFAULT 'non',
    etage_arrivee INT DEFAULT 0,
    ascenseur_arrivee ENUM('oui', 'non') DEFAULT 'non',
    monte_charge ENUM('oui', 'non') DEFAULT 'non',

    -- Informations client
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(10) NOT NULL,

    -- Détails supplémentaires
    date_demenagement DATE NULL,
    commentaires TEXT,

    -- Estimations
    estimation_min DECIMAL(10, 2),
    estimation_max DECIMAL(10, 2),
    volume_estime INT,

    -- Métadonnées
    ip_address VARCHAR(45),
    user_agent TEXT,
    statut ENUM('nouveau', 'en_cours', 'devis_envoyes', 'converti', 'annule') DEFAULT 'nouveau',

    -- Timestamps
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    -- Index pour améliorer les performances
    INDEX idx_email (email),
    INDEX idx_telephone (telephone),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at),
    INDEX idx_depart_postal (depart_postal),
    INDEX idx_arrivee_postal (arrivee_postal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des demandes de rappel
CREATE TABLE IF NOT EXISTS demandes_rappel (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Informations client
    nom VARCHAR(100) NOT NULL,
    telephone VARCHAR(10) NOT NULL,
    creneau ENUM('matin', 'aprem', 'soir', '') DEFAULT '',

    -- Métadonnées
    ip_address VARCHAR(45),
    user_agent TEXT,
    statut ENUM('nouveau', 'rappele', 'annule') DEFAULT 'nouveau',

    -- Timestamps
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    rappele_at DATETIME NULL,

    -- Index
    INDEX idx_telephone (telephone),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des déménageurs partenaires
CREATE TABLE IF NOT EXISTS demenageurs (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Informations entreprise
    nom_entreprise VARCHAR(255) NOT NULL,
    siret VARCHAR(14) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(10) NOT NULL,

    -- Adresse
    adresse VARCHAR(255),
    code_postal VARCHAR(5),
    ville VARCHAR(100),
    departement VARCHAR(3),

    -- Informations supplémentaires
    logo_url VARCHAR(255),
    site_web VARCHAR(255),
    description TEXT,

    -- Services proposés
    service_eco BOOLEAN DEFAULT TRUE,
    service_standard BOOLEAN DEFAULT TRUE,
    service_premium BOOLEAN DEFAULT TRUE,
    demenagement_international BOOLEAN DEFAULT FALSE,
    demenagement_entreprise BOOLEAN DEFAULT FALSE,
    garde_meubles BOOLEAN DEFAULT FALSE,

    -- Zones de couverture (départements séparés par des virgules)
    zones_couverture TEXT,

    -- Note et avis
    note_moyenne DECIMAL(3, 2) DEFAULT 0,
    nombre_avis INT DEFAULT 0,

    -- Statut
    actif BOOLEAN DEFAULT TRUE,
    verifie BOOLEAN DEFAULT FALSE,

    -- Timestamps
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    -- Index
    INDEX idx_email (email),
    INDEX idx_code_postal (code_postal),
    INDEX idx_departement (departement),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des devis envoyés par les déménageurs
CREATE TABLE IF NOT EXISTS devis (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Relations
    demande_id INT NOT NULL,
    demenageur_id INT NOT NULL,

    -- Détails du devis
    montant DECIMAL(10, 2) NOT NULL,
    formule ENUM('eco', 'standard', 'premium') NOT NULL,
    description TEXT,
    validite_jours INT DEFAULT 30,

    -- Statut
    statut ENUM('envoye', 'consulte', 'accepte', 'refuse', 'expire') DEFAULT 'envoye',

    -- Timestamps
    created_at DATETIME NOT NULL,
    consulte_at DATETIME NULL,
    accepte_at DATETIME NULL,
    expire_at DATETIME NULL,

    -- Clés étrangères
    FOREIGN KEY (demande_id) REFERENCES demandes_devis(id) ON DELETE CASCADE,
    FOREIGN KEY (demenageur_id) REFERENCES demenageurs(id) ON DELETE CASCADE,

    -- Index
    INDEX idx_demande_id (demande_id),
    INDEX idx_demenageur_id (demenageur_id),
    INDEX idx_statut (statut),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des avis clients
CREATE TABLE IF NOT EXISTS avis (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Relations
    demande_id INT NOT NULL,
    demenageur_id INT NOT NULL,

    -- Détails de l'avis
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    titre VARCHAR(255),
    commentaire TEXT,

    -- Informations client (optionnelles)
    nom_client VARCHAR(100),
    ville_client VARCHAR(100),

    -- Statut
    approuve BOOLEAN DEFAULT FALSE,

    -- Timestamps
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    -- Clés étrangères
    FOREIGN KEY (demande_id) REFERENCES demandes_devis(id) ON DELETE CASCADE,
    FOREIGN KEY (demenageur_id) REFERENCES demenageurs(id) ON DELETE CASCADE,

    -- Index
    INDEX idx_demenageur_id (demenageur_id),
    INDEX idx_note (note),
    INDEX idx_approuve (approuve),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des administrateurs
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Informations de connexion
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,

    -- Rôle
    role ENUM('super_admin', 'admin', 'support') DEFAULT 'admin',

    -- Statut
    actif BOOLEAN DEFAULT TRUE,

    -- Timestamps
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME NULL,

    -- Index
    INDEX idx_email (email),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des logs d'activité
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Informations de l'action
    user_type ENUM('client', 'demenageur', 'admin') NOT NULL,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,

    -- Métadonnées
    ip_address VARCHAR(45),
    user_agent TEXT,

    -- Timestamp
    created_at DATETIME NOT NULL,

    -- Index
    INDEX idx_user_type_id (user_type, user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer quelques données de test pour les déménageurs
INSERT INTO demenageurs (nom_entreprise, siret, email, telephone, adresse, code_postal, ville, departement, description, service_eco, service_standard, service_premium, zones_couverture, note_moyenne, nombre_avis, actif, verifie, created_at) VALUES
('Déménagements Express Paris', '12345678901234', 'contact@express-paris.fr', '0145678901', '15 Rue de Rivoli', '75001', 'Paris', '75', 'Spécialiste du déménagement en Île-de-France depuis 15 ans', TRUE, TRUE, TRUE, '75,77,78,91,92,93,94,95', 4.5, 127, TRUE, TRUE, NOW()),
('Lyon Déménagement Pro', '23456789012345', 'contact@lyon-demenagement.fr', '0478123456', '50 Avenue des Champs', '69001', 'Lyon', '69', 'Votre déménagement en toute sérénité dans la région Rhône-Alpes', TRUE, TRUE, TRUE, '01,38,42,69,73,74', 4.7, 89, TRUE, TRUE, NOW()),
('Marseille Move', '34567890123456', 'info@marseille-move.fr', '0491234567', '120 La Canebière', '13001', 'Marseille', '13', 'Déménageur certifié pour particuliers et entreprises', TRUE, TRUE, FALSE, '04,05,06,13,83,84', 4.3, 56, TRUE, TRUE, NOW()),
('Bordeaux Transports', '45678901234567', 'contact@bordeaux-transports.fr', '0556789012', '30 Cours de l\'Intendance', '33000', 'Bordeaux', '33', 'Solutions de déménagement sur mesure', TRUE, TRUE, TRUE, '17,24,33,40,47,64', 4.6, 72, TRUE, TRUE, NOW());

-- Insérer un administrateur de test (mot de passe: admin123)
-- Note: dans un environnement de production, utiliser un hash sécurisé
INSERT INTO admins (email, password_hash, nom, prenom, role, actif, created_at) VALUES
('admin@demenageur.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Super', 'super_admin', TRUE, NOW());

-- Vue pour les statistiques
CREATE OR REPLACE VIEW stats_demandes AS
SELECT
    DATE(created_at) as date,
    COUNT(*) as nombre_demandes,
    AVG(estimation_max) as prix_moyen,
    COUNT(DISTINCT CONCAT(depart_postal, '-', arrivee_postal)) as trajets_uniques
FROM demandes_devis
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- Vue pour le tableau de bord des déménageurs
CREATE OR REPLACE VIEW stats_demenageurs AS
SELECT
    d.id,
    d.nom_entreprise,
    d.ville,
    d.note_moyenne,
    d.nombre_avis,
    COUNT(dv.id) as nombre_devis,
    SUM(CASE WHEN dv.statut = 'accepte' THEN 1 ELSE 0 END) as devis_acceptes,
    AVG(dv.montant) as montant_moyen_devis
FROM demenageurs d
LEFT JOIN devis dv ON d.id = dv.demenageur_id
WHERE d.actif = TRUE
GROUP BY d.id
ORDER BY d.note_moyenne DESC, d.nombre_avis DESC;
