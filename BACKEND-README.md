# 🌍 Backend Multi-Pays & Administration - Déménageur.com

Documentation complète du backend internationalisé et du panel d'administration.

---

## 📦 **NOUVEAU : Fonctionnalités Backend**

### ✅ **Système d'internationalisation (i18n) Complet**

Le site est maintenant **100% international** et peut être déployé dans **n'importe quel pays** !

#### **Fonctionnalités i18n**
- ✅ Support de **8+ langues** (FR, EN, ES, DE, IT, PT, NL, PL)
- ✅ Détection automatique de la langue du navigateur
- ✅ Système de traductions complet avec fichiers séparés
- ✅ Configuration spécifique par pays
- ✅ Formats de date/heure personnalisés par pays
- ✅ Devises et symboles monétaires par pays
- ✅ Unités de mesure (métrique/impérial) par pays
- ✅ Validation de données (téléphone, code postal) par pays
- ✅ Tarification adaptée par pays

---

## 🏗️ **Architecture Backend**

### **Structure des Fichiers**

```
demenagement/
├── classes/
│   └── i18n.php                    # Système d'internationalisation
├── languages/
│   ├── fr.php                      # Traductions françaises
│   ├── en.php                      # Traductions anglaises
│   ├── es.php                      # Traductions espagnoles
│   ├── de.php                      # Traductions allemandes
│   └── ...                         # Autres langues
├── config/
│   ├── config.php                  # Configuration générale
│   ├── database.php                # Connexion base de données
│   └── countries/
│       ├── FR.php                  # Configuration France
│       ├── US.php                  # Configuration USA
│       ├── GB.php                  # Configuration UK
│       ├── ES.php                  # Configuration Espagne
│       └── ...                     # Autres pays
├── admin/
│   ├── login.php                   # Authentification admin
│   ├── dashboard.php               # Tableau de bord
│   ├── quotes.php                  # Gestion des devis
│   ├── movers.php                  # Gestion des déménageurs
│   ├── countries.php               # Gestion des pays
│   ├── settings.php                # Paramètres globaux
│   ├── callbacks.php               # Demandes de rappel
│   ├── logout.php                  # Déconnexion
│   ├── css/
│   │   └── admin.css              # Styles admin
│   └── includes/
│       ├── header.php             # En-tête admin
│       └── footer.php             # Pied de page admin
├── database.sql                    # Schéma de base original
└── database-multi-country.sql      # Extensions multi-pays
```

---

## 🌐 **Configuration Multi-Pays**

### **Ajouter un Nouveau Pays**

#### 1. Créer le fichier de configuration

Créez `/config/countries/XX.php` (XX = code ISO du pays) :

```php
<?php
return [
    'code' => 'XX',
    'name' => 'Nom du pays',
    'language' => 'xx',  // Code langue ISO

    'currency' => [
        'code' => 'XXX',
        'symbol' => 'X',
        'decimals' => 2,
        'decimal_separator' => '.',
        'thousand_separator' => ',',
        'symbol_position' => 'before'  // ou 'after'
    ],

    'date_format' => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i',
    'time_format' => 'H:i',

    'phone' => [
        'code' => '+XX',
        'format' => '## ## ## ## ##',
        'regex' => '/^[0-9]{10}$/',
        'placeholder' => '06 12 34 56 78'
    ],

    'postal_code' => [
        'regex' => '/^[0-9]{5}$/',
        'placeholder' => '12345'
    ],

    'units' => [
        'distance' => 'km',    // ou 'miles'
        'area' => 'm²',        // ou 'sqft'
        'volume' => 'm³',      // ou 'cu ft'
        'weight' => 'kg'       // ou 'lbs'
    ],

    'pricing' => [
        'base_price' => 500,
        'price_per_sqm' => 8,
        'price_per_km' => 1.5,
        'floor_cost_per_level' => 100,
        'lift_cost' => 250,
        'house_multiplier' => 1.2,

        'formulas' => [
            'eco' => [
                'name' => 'Formule Éco',
                'multiplier' => 0.6,
                'description' => 'Description'
            ],
            // ...
        ]
    ],

    'regions' => [
        'Région 1' => ['code1', 'code2'],
        'Région 2' => ['code3', 'code4'],
        // ...
    ],

    'contact' => [
        'phone' => 'XX XX XX XX XX',
        'email' => 'contact@site.com',
        'address' => 'Adresse complète'
    ],

    'legal' => [
        'company_name' => 'Nom de la société',
        'registration' => 'Numéro d\'enregistrement',
        'vat' => 'Numéro TVA',
        'capital' => '50 000 €'
    ],

    'seo' => [
        'title_suffix' => ' - Site.com',
        'description' => 'Description SEO par défaut'
    ]
];
```

#### 2. Ajouter le pays dans la base de données

```sql
INSERT INTO countries (code, name, language_code, currency_code, currency_symbol, phone_code, active, created_at)
VALUES ('XX', 'Nom du pays', 'xx', 'XXX', 'X', '+XX', TRUE, NOW());
```

#### 3. Ajouter les régions/états

```sql
INSERT INTO regions (country_code, code, name, region_group) VALUES
('XX', 'R1', 'Région 1', 'Groupe Nord'),
('XX', 'R2', 'Région 2', 'Groupe Sud');
```

---

## 🔑 **Utilisation du Système i18n**

### **Dans le Code PHP**

```php
<?php
// Charger le système i18n
require_once 'classes/i18n.php';
$i18n = i18n::getInstance();

// Obtenir une traduction
echo __('site.name');  // Fonction helper
echo $i18n->translate('hero.title');
echo $i18n->t('form.submit');  // Alias court

// Avec paramètres
echo __('messages.welcome', ['name' => 'John']);
// Template: 'Bienvenue :name !'

// Obtenir la langue actuelle
$lang = $i18n->getLanguage();  // 'fr', 'en', etc.

// Changer de langue
$i18n->setLanguage('en');

// Obtenir le pays actuel
$country = $i18n->getCountry();  // 'FR', 'US', etc.

// Changer de pays
$i18n->setCountry('US');

// Obtenir une config pays
$currency = $i18n->getCountryConfig('currency.code');  // 'EUR'
$dateFormat = $i18n->getCountryConfig('date_format');  // 'd/m/Y'

// Formater un prix selon le pays
echo $i18n->formatPrice(1500);  // '1 500,00 €' (FR) ou '$1,500.00' (US)

// Formater une date selon le pays
echo $i18n->formatDate(new DateTime());

// Obtenir config pays complète
$config = countryConfig();  // Fonction helper
```

### **Changer de Langue/Pays via URL**

```
# Changer la langue
https://site.com/?lang=en

# Changer le pays
https://site.com/?country=US

# Les deux
https://site.com/?lang=en&country=US
```

---

## 👮 **Panel d'Administration**

### **Accès**

- **URL :** `/admin/login.php`
- **Identifiants par défaut :**
  - Email : `admin@demenageur.com`
  - Mot de passe : `admin123` (⚠️ À changer immédiatement !)

### **Fonctionnalités Admin**

#### **📊 Dashboard**
- Statistiques en temps réel
- Demandes du jour/mois
- Rappels en attente
- Déménageurs actifs
- Dernières demandes de devis

#### **📝 Gestion des Devis**
- Liste complète des demandes
- Filtres (statut, date, recherche)
- Détails complets
- Export possible
- Changement de statut

#### **🚚 Gestion des Déménageurs**
- Liste des partenaires
- Ajout/modification
- Activation/désactivation
- Notes et avis
- Services proposés
- Zones de couverture

#### **🌍 Gestion des Pays**
- Liste des pays actifs
- Configuration par pays
- Devises et formats
- Régions/États
- Tarification

#### **📞 Demandes de Rappel**
- Liste des demandes
- Tri par statut
- Créneaux horaires
- Appel direct (tel:)

#### **⚙️ Paramètres**
- Configuration globale
- Emails
- Sécurité
- Tarification
- Langues disponibles
- Infos système

---

## 🗄️ **Base de Données Multi-Pays**

### **Tables Principales**

#### **countries** - Pays disponibles
```sql
- code (VARCHAR(2))          # Code ISO pays
- name (VARCHAR(100))        # Nom du pays
- language_code (VARCHAR(2)) # Langue par défaut
- currency_*                 # Configuration devise
- date_format, time_format   # Formats
- phone_*, postal_code_*     # Validation
- unit_*                     # Unités de mesure
- base_price, price_per_*    # Tarification
- active (BOOLEAN)           # Statut
```

#### **regions** - Régions/États par pays
```sql
- country_code (VARCHAR(2))  # Référence pays
- code (VARCHAR(10))         # Code région
- name (VARCHAR(100))        # Nom région
- region_group (VARCHAR(100)) # Groupe (optionnel)
```

#### **translations** - Traductions dynamiques
```sql
- language_code (VARCHAR(2)) # Code langue
- translation_key (VARCHAR(255))
- translation_value (TEXT)
- category (VARCHAR(50))
```

#### **settings** - Paramètres globaux
```sql
- setting_key (VARCHAR(100))
- setting_value (TEXT)
- setting_type (ENUM)
- category (VARCHAR(50))
```

### **Colonnes Ajoutées aux Tables Existantes**

```sql
-- demandes_devis
ALTER TABLE demandes_devis ADD COLUMN country_code VARCHAR(2) DEFAULT 'FR';
ALTER TABLE demandes_devis ADD COLUMN language_code VARCHAR(2) DEFAULT 'fr';

-- demandes_rappel
ALTER TABLE demandes_rappel ADD COLUMN country_code VARCHAR(2) DEFAULT 'FR';
ALTER TABLE demandes_rappel ADD COLUMN language_code VARCHAR(2) DEFAULT 'fr';

-- demenageurs
ALTER TABLE demenageurs ADD COLUMN country_code VARCHAR(2) DEFAULT 'FR';
```

---

## 🚀 **Déploiement International**

### **Étapes pour un Nouveau Pays**

1. **Créer le fichier de configuration** (`config/countries/XX.php`)
2. **Ajouter le fichier de langue** (`languages/xx.php`)
3. **Insérer le pays en base** (SQL `INSERT INTO countries...`)
4. **Ajouter les régions** (SQL `INSERT INTO regions...`)
5. **Configurer les déménageurs locaux**
6. **Adapter les templates emails** (si nécessaire)
7. **Tester toutes les fonctionnalités**
8. **Activer le pays** dans l'admin

### **Pays Actuellement Configurés**

| Code | Pays | Langue | Devise | Statut |
|------|------|--------|--------|--------|
| FR | France | Français | EUR (€) | ✅ Complet |
| US | United States | English | USD ($) | ✅ Complet |
| GB | United Kingdom | English | GBP (£) | ✅ Complet |
| ES | España | Español | EUR (€) | 🔄 Basique |
| DE | Deutschland | Deutsch | EUR (€) | 🔄 Basique |
| IT | Italia | Italiano | EUR (€) | 🔄 Basique |
| CA | Canada | English | CAD ($) | 🔄 Basique |
| AU | Australia | English | AUD ($) | 🔄 Basique |

---

## 🔐 **Sécurité Backend**

### **Authentification Admin**
- Sessions sécurisées
- Hash bcrypt pour mots de passe
- Protection contre brute force
- Logging des connexions
- Expiration automatique

### **Protection des Données**
- Validation multi-niveaux
- Échappement HTML
- Requêtes préparées PDO
- Protection CSRF
- XSS prevention

---

## 📚 **API Functions**

### **Helpers i18n Disponibles**

```php
// Traduction
__($key, $params = [])

// Config pays
countryConfig($key = null)

// Formatage
formatPrice($amount)
formatDate($date, $format = null)
formatPhone($phone)

// Validation
isValidEmail($email)
isValidPhone($phone)
isValidPostalCode($postalCode)
```

---

## 🎯 **Exemples d'Utilisation**

### **Exemple 1 : Afficher un Prix**

```php
<?php
$i18n = i18n::getInstance();
$price = 1500;

// France
$i18n->setCountry('FR');
echo $i18n->formatPrice($price);  // 1 500,00 €

// USA
$i18n->setCountry('US');
echo $i18n->formatPrice($price);  // $1,500.00

// UK
$i18n->setCountry('GB');
echo $i18n->formatPrice($price);  // £1,500.00
```

### **Exemple 2 : Formulaire Multi-Langue**

```php
<form>
    <label><?= __('form.your_contact') ?></label>

    <input type="text"
           placeholder="<?= __('form.lastname') ?>"
           name="nom">

    <input type="email"
           placeholder="<?= __('form.email') ?>"
           name="email">

    <button><?= __('form.submit') ?></button>
</form>
```

### **Exemple 3 : Calcul de Prix par Pays**

```php
<?php
$superficie = 70;  // m²
$distance = 100;   // km

// Récupérer les tarifs du pays actuel
$basePrice = countryConfig('pricing.base_price');
$pricePerSqm = countryConfig('pricing.price_per_sqm');
$pricePerKm = countryConfig('pricing.price_per_km');

$total = $basePrice + ($superficie * $pricePerSqm) + ($distance * $pricePerKm);

echo $i18n->formatPrice($total);
```

---

## 📖 **Documentation Complète**

Pour plus d'informations :
- Configuration : `/config/config.php`
- i18n : `/classes/i18n.php`
- Helpers : `/includes/helpers.php`
- Database : `/database-multi-country.sql`

---

**🎉 Le backend est maintenant 100% internationalisé et prêt pour un déploiement mondial !**
