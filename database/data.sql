-- ============================================
-- Données Initiales - Déménageur.com
-- Version: 1.0
-- Date: 2024-11-19
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- 1. PLANS D'ABONNEMENT
-- ============================================

INSERT INTO `subscription_plans` (`id`, `name`, `slug`, `description`, `price_monthly`, `price_yearly`, `leads_per_month`, `features_list`, `is_active`, `display_order`) VALUES
(1, 'Basic', 'basic', 'Parfait pour démarrer votre activité', 49.00, 490.00, 20, '["20 leads qualifiés par mois","Dashboard de suivi","Support par email","Profil déménageur vérifié","Notifications par email"]', 1, 1),
(2, 'Pro', 'pro', 'Pour les professionnels en croissance', 99.00, 990.00, 50, '["50 leads qualifiés par mois","Dashboard avec statistiques","Support prioritaire","Badge PRO sur votre profil","Notifications SMS + email","Placement prioritaire","Export des données"]', 1, 2),
(3, 'Premium', 'premium', 'Solution complète pour grandes entreprises', 199.00, 1990.00, NULL, '["Leads illimités","Dashboard analytics avancé","Support dédié 7j/7","Badge PREMIUM","Multi-utilisateurs","API d\\'intégration","Formation personnalisée","Placement en tête","Manager de compte dédié"]', 1, 3);

-- ============================================
-- 2. PAYS SUPPORTÉS
-- ============================================

INSERT INTO `countries` (`id`, `code`, `name`, `name_fr`, `is_active`, `display_order`) VALUES
(1, 'FR', 'France', 'France', 1, 1),
(2, 'BE', 'Belgium', 'Belgique', 1, 2),
(3, 'CH', 'Switzerland', 'Suisse', 1, 3),
(4, 'LU', 'Luxembourg', 'Luxembourg', 1, 4),
(5, 'ES', 'Spain', 'Espagne', 0, 5),
(6, 'IT', 'Italy', 'Italie', 0, 6),
(7, 'DE', 'Germany', 'Allemagne', 0, 7),
(8, 'GB', 'United Kingdom', 'Royaume-Uni', 0, 8);

-- ============================================
-- 3. RÉGIONS FRANÇAISES
-- ============================================

INSERT INTO `regions` (`id`, `country_code`, `name`, `slug`, `is_active`) VALUES
(1, 'FR', 'Île-de-France', 'ile-de-france', 1),
(2, 'FR', 'Auvergne-Rhône-Alpes', 'auvergne-rhone-alpes', 1),
(3, 'FR', 'Provence-Alpes-Côte d\'Azur', 'provence-alpes-cote-azur', 1),
(4, 'FR', 'Occitanie', 'occitanie', 1),
(5, 'FR', 'Nouvelle-Aquitaine', 'nouvelle-aquitaine', 1),
(6, 'FR', 'Pays de la Loire', 'pays-de-la-loire', 1),
(7, 'FR', 'Bretagne', 'bretagne', 1),
(8, 'FR', 'Hauts-de-France', 'hauts-de-france', 1),
(9, 'FR', 'Grand Est', 'grand-est', 1),
(10, 'FR', 'Normandie', 'normandie', 1),
(11, 'FR', 'Bourgogne-Franche-Comté', 'bourgogne-franche-comte', 1),
(12, 'FR', 'Centre-Val de Loire', 'centre-val-de-loire', 1),
(13, 'FR', 'Corse', 'corse', 1);

-- ============================================
-- 4. RÉGIONS BELGES
-- ============================================

INSERT INTO `regions` (`id`, `country_code`, `name`, `slug`, `is_active`) VALUES
(14, 'BE', 'Bruxelles-Capitale', 'bruxelles-capitale', 1),
(15, 'BE', 'Flandre', 'flandre', 1),
(16, 'BE', 'Wallonie', 'wallonie', 1);

-- ============================================
-- 5. RÉGIONS SUISSES
-- ============================================

INSERT INTO `regions` (`id`, `country_code`, `name`, `slug`, `is_active`) VALUES
(17, 'CH', 'Zurich', 'zurich', 1),
(18, 'CH', 'Genève', 'geneve', 1),
(19, 'CH', 'Vaud', 'vaud', 1),
(20, 'CH', 'Berne', 'berne', 1),
(21, 'CH', 'Valais', 'valais', 1);

-- ============================================
-- 6. PARAMÈTRES SYSTÈME
-- ============================================

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'Déménageur.com', 'string', 'Nom du site'),
('site_email', 'contact@demenageur.com', 'string', 'Email de contact principal'),
('support_email', 'support@demenageur.com', 'string', 'Email du support'),
('admin_email', 'admin@demenageur.com', 'string', 'Email admin pour notifications'),
('site_url', 'https://www.demenageur.com', 'string', 'URL principale du site'),
('maintenance_mode', '0', 'boolean', 'Mode maintenance activé'),
('allow_registrations', '1', 'boolean', 'Autoriser nouvelles inscriptions'),
('trial_duration_days', '30', 'integer', 'Durée de l\'essai gratuit en jours'),
('trial_leads_count', '5', 'integer', 'Nombre de leads offerts pendant l\'essai'),
('max_demenageurs_per_lead', '5', 'integer', 'Nombre max de déménageurs par lead'),
('lead_match_radius_km', '50', 'integer', 'Rayon de recherche pour le matching (km)'),
('lead_expiry_days', '30', 'integer', 'Durée de validité d\'un lead (jours)'),
('commission_rate', '15', 'integer', 'Taux de commission sur les conversions (%)'),
('min_password_length', '8', 'integer', 'Longueur minimale du mot de passe'),
('max_login_attempts', '5', 'integer', 'Nombre max de tentatives de connexion'),
('session_lifetime', '7200', 'integer', 'Durée de session en secondes'),
('enable_sms_notifications', '0', 'boolean', 'Activer notifications SMS'),
('enable_email_notifications', '1', 'boolean', 'Activer notifications email'),
('stripe_mode', 'test', 'string', 'Mode Stripe (test/live)'),
('google_analytics_id', '', 'string', 'ID Google Analytics'),
('facebook_pixel_id', '', 'string', 'ID Facebook Pixel'),
('contact_phone', '01 XX XX XX XX', 'string', 'Téléphone de contact'),
('company_name', 'Déménageur SAS', 'string', 'Nom de la société'),
('company_address', '123 Avenue Example, 75001 Paris', 'string', 'Adresse de la société'),
('company_siret', 'XXX XXX XXX XXXXX', 'string', 'SIRET de la société'),
('company_vat', 'FR XX XXX XXX XXX', 'string', 'N° TVA intracommunautaire');

-- ============================================
-- 7. DÉPARTEMENTS FRANÇAIS (pour zones de couverture)
-- ============================================

-- Cette table sera utilisée pour le sélecteur de zones de couverture
-- Créons une table temporaire pour stocker les départements
CREATE TEMPORARY TABLE IF NOT EXISTS `temp_departments` (
  `code` varchar(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`code`)
);

INSERT INTO `temp_departments` (`code`, `name`) VALUES
('01', 'Ain'),
('02', 'Aisne'),
('03', 'Allier'),
('04', 'Alpes-de-Haute-Provence'),
('05', 'Hautes-Alpes'),
('06', 'Alpes-Maritimes'),
('07', 'Ardèche'),
('08', 'Ardennes'),
('09', 'Ariège'),
('10', 'Aube'),
('11', 'Aude'),
('12', 'Aveyron'),
('13', 'Bouches-du-Rhône'),
('14', 'Calvados'),
('15', 'Cantal'),
('16', 'Charente'),
('17', 'Charente-Maritime'),
('18', 'Cher'),
('19', 'Corrèze'),
('21', 'Côte-d\'Or'),
('22', 'Côtes-d\'Armor'),
('23', 'Creuse'),
('24', 'Dordogne'),
('25', 'Doubs'),
('26', 'Drôme'),
('27', 'Eure'),
('28', 'Eure-et-Loir'),
('29', 'Finistère'),
('2A', 'Corse-du-Sud'),
('2B', 'Haute-Corse'),
('30', 'Gard'),
('31', 'Haute-Garonne'),
('32', 'Gers'),
('33', 'Gironde'),
('34', 'Hérault'),
('35', 'Ille-et-Vilaine'),
('36', 'Indre'),
('37', 'Indre-et-Loire'),
('38', 'Isère'),
('39', 'Jura'),
('40', 'Landes'),
('41', 'Loir-et-Cher'),
('42', 'Loire'),
('43', 'Haute-Loire'),
('44', 'Loire-Atlantique'),
('45', 'Loiret'),
('46', 'Lot'),
('47', 'Lot-et-Garonne'),
('48', 'Lozère'),
('49', 'Maine-et-Loire'),
('50', 'Manche'),
('51', 'Marne'),
('52', 'Haute-Marne'),
('53', 'Mayenne'),
('54', 'Meurthe-et-Moselle'),
('55', 'Meuse'),
('56', 'Morbihan'),
('57', 'Moselle'),
('58', 'Nièvre'),
('59', 'Nord'),
('60', 'Oise'),
('61', 'Orne'),
('62', 'Pas-de-Calais'),
('63', 'Puy-de-Dôme'),
('64', 'Pyrénées-Atlantiques'),
('65', 'Hautes-Pyrénées'),
('66', 'Pyrénées-Orientales'),
('67', 'Bas-Rhin'),
('68', 'Haut-Rhin'),
('69', 'Rhône'),
('70', 'Haute-Saône'),
('71', 'Saône-et-Loire'),
('72', 'Sarthe'),
('73', 'Savoie'),
('74', 'Haute-Savoie'),
('75', 'Paris'),
('76', 'Seine-Maritime'),
('77', 'Seine-et-Marne'),
('78', 'Yvelines'),
('79', 'Deux-Sèvres'),
('80', 'Somme'),
('81', 'Tarn'),
('82', 'Tarn-et-Garonne'),
('83', 'Var'),
('84', 'Vaucluse'),
('85', 'Vendée'),
('86', 'Vienne'),
('87', 'Haute-Vienne'),
('88', 'Vosges'),
('89', 'Yonne'),
('90', 'Territoire de Belfort'),
('91', 'Essonne'),
('92', 'Hauts-de-Seine'),
('93', 'Seine-Saint-Denis'),
('94', 'Val-de-Marne'),
('95', 'Val-d\'Oise');

-- ============================================
-- 8. UTILISATEUR ADMIN PAR DÉFAUT
-- ============================================

-- IMPORTANT : Changer le mot de passe après la première connexion !
-- Mot de passe par défaut : Admin123!
-- Hash généré avec : password_hash('Admin123!', PASSWORD_DEFAULT)

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `is_active`, `created_at`) VALUES
(1, 'admin', 'admin@demenageur.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur Principal', 'superadmin', 1, NOW());

-- ============================================
-- 9. EXEMPLES DE DONNÉES (OPTIONNEL - POUR DEV)
-- ============================================

-- Décommenter ces lignes pour avoir des données de test

/*
-- Exemple de déménageur
INSERT INTO `demenageurs` (`email`, `password_hash`, `company_name`, `siret`, `contact_name`, `phone`, `city`, `postal_code`, `country`, `status`, `verified`) VALUES
('demo@demenageur.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Déménagement Express', '12345678901234', 'Jean Dupont', '0612345678', 'Paris', '75001', 'FR', 'active', 1);

-- Abonnement pour le déménageur de test
INSERT INTO `demenageur_subscriptions` (`demenageur_id`, `plan_id`, `status`, `start_date`, `next_billing_date`, `leads_reset_date`) VALUES
(1, 2, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), NOW());

-- Zones de couverture du déménageur de test
INSERT INTO `demenageur_zones` (`demenageur_id`, `department_code`, `department_name`) VALUES
(1, '75', 'Paris'),
(1, '92', 'Hauts-de-Seine'),
(1, '93', 'Seine-Saint-Denis'),
(1, '94', 'Val-de-Marne');

-- Services proposés par le déménageur de test
INSERT INTO `demenageur_services` (`demenageur_id`, `service_type`) VALUES
(1, 'packing'),
(1, 'unpacking'),
(1, 'furniture_assembly'),
(1, 'storage');
*/

COMMIT;

-- ============================================
-- NOTES IMPORTANTES
-- ============================================

-- 1. SÉCURITÉ :
--    - Changez IMMÉDIATEMENT le mot de passe de l'admin par défaut
--    - Utilisez un mot de passe fort (16+ caractères)
--    - Générez le hash avec : php -r "echo password_hash('VotreMotDePasse', PASSWORD_DEFAULT);"

-- 2. CONFIGURATION :
--    - Mettez à jour les paramètres dans system_settings selon votre environnement
--    - Configurez les emails (support_email, admin_email)
--    - Configurez l'URL du site (site_url)

-- 3. STRIPE :
--    - Créez les 3 produits dans Stripe
--    - Créez les 6 prix (3 monthly + 3 yearly)
--    - Mettez à jour les IDs dans votre fichier .env

-- 4. PRODUCTION :
--    - Supprimez ou commentez les données de test
--    - Passez stripe_mode à 'live'
--    - Configurez Google Analytics et Facebook Pixel si nécessaire

-- ============================================
-- FIN DES DONNÉES INITIALES
-- ============================================
