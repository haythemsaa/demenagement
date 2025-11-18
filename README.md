# Déménageur.com - Site de Comparaison de Devis de Déménagement

Site web professionnel de comparaison de devis de déménagement développé en PHP avec MySQL.

## 🚀 Fonctionnalités

### ✅ Fonctionnalités Principales

- **Formulaire de devis multi-étapes** : Collecte complète des informations de déménagement
- **Simulateur interactif** : Estimation en temps réel du coût et volume du déménagement
- **Système de rappel** : Demande de rappel gratuit avec gestion des créneaux horaires
- **Couverture géographique complète** : 95 départements + DOM-TOM
- **Services additionnels** : Estimation immobilière, transport spécialisé, déménagement international, etc.
- **FAQ interactive** : Accordion avec les questions fréquentes
- **Témoignages clients** : Section d'avis et notes
- **Design responsive** : Compatible mobile, tablette et desktop

### 🎨 Interface Utilisateur

- Design moderne et professionnel
- Animations fluides au scroll
- Navigation intuitive
- Formulaires validés en temps réel
- Messages de confirmation/erreur
- Bouton de retour en haut de page

### 💾 Backend PHP

- Architecture MVC propre
- Base de données MySQL bien structurée
- API REST pour les formulaires
- Validation des données côté serveur
- Protection contre les injections SQL (PDO)
- Système de logging des activités
- Gestion des emails de notification

## 📋 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur / MariaDB 10.2+
- Serveur web Apache ou Nginx
- Extensions PHP requises :
  - PDO
  - pdo_mysql
  - mbstring
  - json

## 🔧 Installation

### 1. Cloner le projet

```bash
git clone [url-du-repo]
cd demenagement
```

### 2. Configuration de la base de données

```bash
# Créer la base de données et importer le schéma
mysql -u root -p < database.sql
```

### 3. Configuration du fichier de connexion

Éditez le fichier `config/database.php` et ajustez les paramètres :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'demenagement_db');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

### 4. Configuration du serveur web

#### Apache

Le fichier `.htaccess` est déjà configuré. Assurez-vous que `mod_rewrite` est activé :

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx

Exemple de configuration :

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/demenagement;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\. {
        deny all;
    }
}
```

### 5. Permissions des fichiers

```bash
# Permissions pour Apache/Nginx
sudo chown -R www-data:www-data /var/www/demenagement
sudo chmod -R 755 /var/www/demenagement
```

## 🗄️ Structure de la Base de Données

### Tables principales :

- **demandes_devis** : Stocke toutes les demandes de devis
- **demandes_rappel** : Gère les demandes de rappel
- **demenageurs** : Informations des déménageurs partenaires
- **devis** : Devis envoyés par les déménageurs
- **avis** : Avis et notes des clients
- **admins** : Comptes administrateurs
- **activity_logs** : Logs d'activité

## 📁 Structure du Projet

```
demenagement/
├── index.php              # Page d'accueil
├── styles.css            # Styles CSS
├── script.js             # JavaScript interactif
├── database.sql          # Schéma de base de données
├── .htaccess            # Configuration Apache
├── README.md            # Ce fichier
├── config/
│   └── database.php     # Configuration DB
└── api/
    ├── submit-quote.php    # API pour les devis
    └── submit-callback.php # API pour les rappels
```

## 🔐 Sécurité

### Mesures de sécurité implémentées :

- ✅ Protection contre les injections SQL (PDO avec requêtes préparées)
- ✅ Validation des données côté serveur
- ✅ Protection CSRF (à activer en production)
- ✅ En-têtes de sécurité HTTP
- ✅ Protection des fichiers sensibles (.htaccess)
- ✅ Hashage des mots de passe (bcrypt)
- ✅ Limitation des tentatives de connexion
- ✅ Logging des activités

### Recommandations pour la production :

1. Activer HTTPS (SSL/TLS)
2. Configurer les en-têtes CSP (Content Security Policy)
3. Mettre en place un système de backup automatique
4. Activer le mode de production PHP (display_errors = Off)
5. Configurer un système de monitoring
6. Mettre en place un firewall (ModSecurity)

## 📧 Configuration des Emails

Pour activer l'envoi d'emails, décommentez les lignes `mail()` dans :
- `api/submit-quote.php`
- `api/submit-callback.php`

Ou configurez un service SMTP comme PHPMailer :

```bash
composer require phpmailer/phpmailer
```

## 🎯 API Endpoints

### POST /api/submit-quote.php
Soumet une demande de devis

**Paramètres :**
- depart-address, depart-postal
- arrivee-address, arrivee-postal
- type-depart, superficie, pieces
- etage-depart, ascenseur-depart
- etage-arrivee, ascenseur-arrivee
- monte-charge
- nom, prenom, email, telephone
- date-demenagement, commentaires

**Réponse :**
```json
{
  "success": true,
  "message": "Votre demande a été envoyée avec succès !",
  "devis_id": 123,
  "estimation": {
    "min": 800,
    "max": 1200,
    "volume": 35,
    "distance": 100
  }
}
```

### POST /api/submit-callback.php
Soumet une demande de rappel

**Paramètres :**
- callback-nom
- callback-tel
- callback-creneau

**Réponse :**
```json
{
  "success": true,
  "message": "Votre demande de rappel a été enregistrée !",
  "rappel_id": 45
}
```

## 🧪 Tests

### Test manuel :

1. Accéder à `http://localhost/demenagement/`
2. Remplir le formulaire de devis
3. Tester le simulateur
4. Soumettre une demande de rappel
5. Vérifier les données dans la base de données

### Données de test :

La base de données contient déjà :
- 4 déménageurs partenaires
- 1 compte admin (email: admin@demenageur.com, mot de passe: admin123)

## 📊 Statistiques et Rapports

Des vues SQL sont disponibles pour les statistiques :
- `stats_demandes` : Statistiques journalières des demandes
- `stats_demenageurs` : Performance des déménageurs

## 🔄 Mises à Jour Futures

### Fonctionnalités planifiées :

- [ ] Espace client (suivi des demandes)
- [ ] Espace déménageur (gestion des devis)
- [ ] Panel d'administration complet
- [ ] API de géolocalisation pour calcul précis des distances
- [ ] Système de paiement en ligne
- [ ] Chat en direct
- [ ] Application mobile
- [ ] Système de réservation de créneaux
- [ ] Intégration calendrier
- [ ] Export PDF des devis

## 🤝 Support

Pour toute question ou assistance :
- Email : contact@demenageur.com
- Téléphone : 09 78 45 02 18

## 📄 Licence

© 2024 Déménageur.com - Tous droits réservés

## 👨‍💻 Développement

Développé avec :
- PHP 8.x
- MySQL 8.0
- HTML5, CSS3, JavaScript (Vanilla)
- Font : Roboto (Google Fonts)

---

**Note :** Ce site est une plateforme complète de mise en relation entre particuliers et professionnels du déménagement, avec toutes les fonctionnalités nécessaires pour gérer les demandes de devis, les rappels clients, et la gestion des déménageurs partenaires.
