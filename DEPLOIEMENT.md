# 🚀 Guide de Déploiement Production - Déménageur.com

## 📋 Vue d'ensemble

Ce guide détaille les étapes complètes pour déployer la plateforme Déménageur.com en production.

**Temps estimé :** 4-6 heures
**Difficulté :** Intermédiaire
**Prérequis :** Accès serveur, nom de domaine, compte Stripe

---

## ✅ Prérequis

### Infrastructure Serveur
- **Serveur** : VPS ou dédié (recommandé : 4GB RAM minimum)
- **OS** : Ubuntu 20.04 LTS ou supérieur
- **Accès** : Root ou sudo
- **Nom de domaine** : Configuré et pointant vers le serveur

### Logiciels Requis
```bash
- PHP 7.4+ ou 8.x
- MySQL 8.0+ ou MariaDB 10.5+
- Nginx ou Apache 2.4+
- Composer 2.x
- Git
- Certbot (pour SSL)
```

### Comptes Tiers
- **Stripe** : Compte vérifié avec paiements activés
- **SMTP** : Service email (Gmail, SendGrid, Mailgun, etc.)
- **Sauvegardes** : Espace de stockage externe (optionnel)

---

## 🔧 Étape 1 : Préparation du Serveur

### 1.1 Mise à jour du système

```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Installation de PHP et extensions

```bash
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring \
php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-intl \
php8.1-bcmath php8.1-soap
```

### 1.3 Installation de MySQL

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 1.4 Installation de Nginx

```bash
sudo apt install -y nginx
```

### 1.5 Installation de Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 1.6 Installation de Certbot (SSL)

```bash
sudo apt install -y certbot python3-certbot-nginx
```

---

## 📦 Étape 2 : Déploiement du Code

### 2.1 Créer l'utilisateur de déploiement

```bash
sudo adduser demenageur
sudo usermod -aG www-data demenageur
```

### 2.2 Cloner le repository

```bash
sudo mkdir -p /var/www/demenageur.com
sudo chown -R demenageur:www-data /var/www/demenageur.com

# Se connecter en tant qu'utilisateur demenageur
sudo su - demenageur

cd /var/www/demenageur.com
git clone <URL_REPOSITORY> .
```

### 2.3 Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

### 2.4 Configurer les permissions

```bash
# Revenir en root
exit

sudo chown -R demenageur:www-data /var/www/demenageur.com
sudo chmod -R 755 /var/www/demenageur.com
sudo chmod -R 775 /var/www/demenageur.com/uploads
sudo chmod -R 775 /var/www/demenageur.com/logs
sudo chmod -R 775 /var/www/demenageur.com/cache
```

---

## 🗄️ Étape 3 : Configuration de la Base de Données

### 3.1 Créer la base de données

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE demenagement CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'demenageur_user'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE_SECURISE';
GRANT ALL PRIVILEGES ON demenagement.* TO 'demenageur_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3.2 Importer le schéma de base de données

```bash
mysql -u demenageur_user -p demenagement < /var/www/demenageur.com/database/schema.sql
mysql -u demenageur_user -p demenagement < /var/www/demenageur.com/database/data.sql
```

### 3.3 Créer le premier utilisateur admin

```bash
mysql -u demenageur_user -p demenagement
```

```sql
INSERT INTO admin_users (username, email, password_hash, full_name, role, created_at)
VALUES (
    'admin',
    'admin@demenageur.com',
    '$2y$10$HASH_GENERE_ICI',  -- Utiliser password_hash() en PHP
    'Administrateur',
    'superadmin',
    NOW()
);
```

**Générer le hash du mot de passe :**
```bash
php -r "echo password_hash('VotreMotDePasseSecurise', PASSWORD_DEFAULT);"
```

---

## ⚙️ Étape 4 : Configuration de l'Application

### 4.1 Créer le fichier .env

```bash
cd /var/www/demenageur.com
cp .env.example .env
nano .env
```

### 4.2 Remplir les variables d'environnement

```bash
# Base de données
DB_HOST=localhost
DB_NAME=demenagement
DB_USER=demenageur_user
DB_PASS=MOT_DE_PASSE_SECURISE

# Stripe (mode production)
STRIPE_MODE=live
STRIPE_PUBLIC_KEY_LIVE=pk_live_VOTRE_CLE
STRIPE_SECRET_KEY_LIVE=sk_live_VOTRE_CLE
STRIPE_WEBHOOK_SECRET=whsec_VOTRE_SECRET

# IDs des produits Stripe (à créer sur dashboard.stripe.com)
STRIPE_PRICE_BASIC_MONTHLY=price_XXXXX
STRIPE_PRICE_BASIC_YEARLY=price_XXXXX
STRIPE_PRICE_PRO_MONTHLY=price_XXXXX
STRIPE_PRICE_PRO_YEARLY=price_XXXXX
STRIPE_PRICE_PREMIUM_MONTHLY=price_XXXXX
STRIPE_PRICE_PREMIUM_YEARLY=price_XXXXX

# Email SMTP
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=noreply@demenageur.com
SMTP_PASS=mot_de_passe_app_gmail
SMTP_FROM_EMAIL=noreply@demenageur.com
SMTP_FROM_NAME=Déménageur.com

# URLs
APP_URL=https://www.demenageur.com
APP_ENV=production

# Emails admin
ADMIN_EMAIL=admin@demenageur.com
SUPPORT_EMAIL=support@demenageur.com
```

### 4.3 Sécuriser le fichier .env

```bash
sudo chmod 600 /var/www/demenageur.com/.env
sudo chown demenageur:www-data /var/www/demenageur.com/.env
```

---

## 🌐 Étape 5 : Configuration Nginx

### 5.1 Créer la configuration du site

```bash
sudo nano /etc/nginx/sites-available/demenageur.com
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name www.demenageur.com demenageur.com;

    root /var/www/demenageur.com;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/demenageur.com.access.log;
    error_log /var/log/nginx/demenageur.com.error.log;

    # Sécurité : Cacher la version Nginx
    server_tokens off;

    # Protection fichiers sensibles
    location ~ /\. {
        deny all;
    }

    location ~ ^/(config|classes|includes|vendor|logs|cache)/ {
        deny all;
    }

    # PHP
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Fichiers statiques
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Redirection vers index.php si fichier inexistant
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Taille max upload
    client_max_body_size 20M;
}
```

### 5.2 Activer le site

```bash
sudo ln -s /etc/nginx/sites-available/demenageur.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5.3 Obtenir le certificat SSL

```bash
sudo certbot --nginx -d demenageur.com -d www.demenageur.com
```

Certbot modifiera automatiquement la configuration Nginx pour ajouter le SSL.

---

## 💳 Étape 6 : Configuration Stripe

### 6.1 Créer les produits sur Stripe

1. Aller sur https://dashboard.stripe.com/products
2. Créer 3 produits : Basic, Pro, Premium
3. Pour chaque produit, créer 2 prix : Monthly et Yearly
4. Copier les IDs `price_XXXXX` dans le fichier `.env`

### 6.2 Configurer le webhook

1. Aller sur https://dashboard.stripe.com/webhooks
2. Créer un endpoint : `https://www.demenageur.com/webhooks/stripe.php`
3. Sélectionner les événements :
   - `checkout.session.completed`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
4. Copier le secret webhook `whsec_XXXXX` dans `.env`

---

## 📧 Étape 7 : Configuration Email

### 7.1 Avec Gmail (recommandé pour tests)

1. Activer l'authentification 2FA sur votre compte Gmail
2. Générer un mot de passe d'application : https://myaccount.google.com/apppasswords
3. Utiliser ce mot de passe dans `SMTP_PASS`

### 7.2 Avec SendGrid (recommandé pour production)

```bash
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_USER=apikey
SMTP_PASS=VOTRE_API_KEY_SENDGRID
```

### 7.3 Tester l'envoi d'email

```bash
cd /var/www/demenageur.com
php -r "
require 'vendor/autoload.php';
require 'includes/helpers.php';
send_email('votre@email.com', 'Test', '<h1>Test email</h1>');
echo 'Email envoyé\n';
"
```

---

## 🔒 Étape 8 : Sécurisation

### 8.1 Configurer le pare-feu

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 8.2 Sécuriser PHP

```bash
sudo nano /etc/php/8.1/fpm/php.ini
```

```ini
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
max_execution_time = 30
max_input_time = 60
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
session.cookie_httponly = 1
session.cookie_secure = 1
```

```bash
sudo mkdir -p /var/log/php
sudo chown www-data:www-data /var/log/php
sudo systemctl restart php8.1-fpm
```

### 8.3 Désactiver l'indexation des répertoires

Déjà configuré dans Nginx ci-dessus avec `autoindex off;`

---

## 📊 Étape 9 : Monitoring et Logs

### 9.1 Configurer la rotation des logs

```bash
sudo nano /etc/logrotate.d/demenageur
```

```
/var/www/demenageur.com/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 demenageur www-data
    sharedscripts
}
```

### 9.2 Créer un script de monitoring

```bash
sudo nano /usr/local/bin/check-demenageur.sh
```

```bash
#!/bin/bash
# Vérifier que le site répond
curl -f https://www.demenageur.com > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "Le site ne répond pas!" | mail -s "ALERTE Déménageur.com" admin@demenageur.com
fi

# Vérifier l'espace disque
USAGE=$(df -h / | grep / | awk '{print $5}' | sed 's/%//g')
if [ $USAGE -gt 90 ]; then
    echo "Espace disque critique: $USAGE%" | mail -s "ALERTE Disque" admin@demenageur.com
fi
```

```bash
sudo chmod +x /usr/local/bin/check-demenageur.sh
```

### 9.3 Ajouter au cron

```bash
sudo crontab -e
```

```
# Vérifier le site toutes les 5 minutes
*/5 * * * * /usr/local/bin/check-demenageur.sh

# Sauvegarder la BDD tous les jours à 2h du matin
0 2 * * * mysqldump -u demenageur_user -pMOT_DE_PASSE demenagement | gzip > /var/backups/demenagement_$(date +\%Y\%m\%d).sql.gz
```

---

## 🔄 Étape 10 : Tâches Automatisées

### 10.1 Script d'expiration des essais gratuits

```bash
nano /var/www/demenageur.com/cron/check-trial-expiration.php
```

```php
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance()->getConnection();

// Trouver les essais qui expirent dans 7 jours
$stmt = $db->query("
    SELECT d.*, ds.end_date
    FROM demenageurs d
    INNER JOIN demenageur_subscriptions ds ON d.id = ds.demenageur_id
    WHERE ds.status = 'trial'
    AND DATEDIFF(ds.end_date, NOW()) = 7
");

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $dem) {
    // Envoyer email d'avertissement
    // ... code d'envoi email ...
}
```

### 10.2 Ajouter au cron

```bash
0 9 * * * php /var/www/demenageur.com/cron/check-trial-expiration.php
```

---

## ✅ Étape 11 : Tests de Production

### 11.1 Checklist de tests

- [ ] Page d'accueil se charge correctement
- [ ] Formulaire de devis fonctionne
- [ ] Inscription déménageur fonctionne
- [ ] Login admin fonctionne
- [ ] Dashboard admin accessible
- [ ] Paiement Stripe (mode test) fonctionne
- [ ] Webhook Stripe reçoit les événements
- [ ] Emails sont bien envoyés
- [ ] SSL actif (cadenas vert)
- [ ] Logs se créent correctement

### 11.2 Test de paiement en mode test

1. Utiliser les cartes de test Stripe : `4242 4242 4242 4242`
2. Vérifier que l'abonnement est créé dans la BDD
3. Vérifier que l'email de confirmation est envoyé
4. Vérifier que le webhook est appelé (dans les logs Stripe)

---

## 🚀 Étape 12 : Mise en Production

### 12.1 Passer Stripe en mode live

```bash
nano /var/www/demenageur.com/.env
```

```bash
STRIPE_MODE=live
# Remplacer les clés test par les clés live
```

### 12.2 Activer Google Analytics (optionnel)

Ajouter le code GA dans `includes/header.php`

### 12.3 Annoncer le lancement

- [ ] Email aux beta testeurs
- [ ] Annonce sur réseaux sociaux
- [ ] Communiqué de presse
- [ ] Campagne SEO/SEM

---

## 📚 Maintenance Continue

### Tâches Quotidiennes
- Vérifier les logs d'erreur
- Répondre aux demandes de support
- Modérer les nouveaux déménageurs

### Tâches Hebdomadaires
- Vérifier les sauvegardes
- Analyser les performances
- Examiner les métriques Stripe

### Tâches Mensuelles
- Mettre à jour les dépendances : `composer update`
- Vérifier les mises à jour de sécurité PHP/Nginx
- Analyser les statistiques de conversion

---

## 🆘 Dépannage

### Le site ne se charge pas
```bash
sudo nginx -t
sudo systemctl status nginx
sudo systemctl status php8.1-fpm
tail -f /var/log/nginx/error.log
```

### Erreur 500
```bash
tail -f /var/log/php/error.log
tail -f /var/www/demenageur.com/logs/error.log
```

### Problème de connexion BDD
```bash
mysql -u demenageur_user -p
SHOW DATABASES;
```

### Webhook Stripe ne fonctionne pas
1. Vérifier les logs : `/var/www/demenageur.com/logs/stripe_webhooks.log`
2. Tester le webhook dans le dashboard Stripe
3. Vérifier que l'URL est accessible publiquement

---

## 📞 Support

**Email** : dev@demenageur.com
**Documentation** : https://docs.demenageur.com
**Status** : https://status.demenageur.com

---

✅ **Félicitations ! Votre plateforme Déménageur.com est maintenant en production !**
