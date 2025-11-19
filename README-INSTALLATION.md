# 🚀 Guide d'Installation - Déménageur.com

## Installation Rapide (5 minutes)

### Méthode 1 : Installateur Web ⭐ RECOMMANDÉ

1. **Télécharger et extraire les fichiers sur votre serveur**

2. **Accéder à l'installateur**
```
http://votre-domaine.com/install.php
```

3. **Suivre les 3 étapes guidées**
   - Étape 1 : Configuration base de données
   - Étape 2 : Création compte administrateur  
   - Étape 3 : Terminé !

4. **IMPORTANT : Supprimer le fichier install.php après installation**
```bash
rm install.php
```

### Méthode 2 : Installation Manuelle

```bash
# 1. Créer la base de données
mysql -u root -p
CREATE DATABASE demenagement CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 2. Importer le schéma
mysql -u root -p demenagement < database/schema.sql
mysql -u root -p demenagement < database/data.sql

# 3. Installer les dépendances
composer install --no-dev --optimize-autoloader

# 4. Configurer l'environnement
cp .env.example .env
nano .env  # Remplir les variables

# 5. Permissions
chmod 755 -R .
chmod 775 -R uploads/ logs/ cache/
```

## Configuration Post-Installation

### 1. Configurer Stripe

1. Créer 3 produits sur https://dashboard.stripe.com/products
   - Basic : 49€/mois
   - Pro : 99€/mois
   - Premium : 199€/mois

2. Créer 2 prix pour chaque produit (monthly + yearly)

3. Copier les IDs dans `.env`

4. Configurer le webhook : `https://votre-domaine.com/webhooks/stripe.php`

### 2. Configurer les Emails

Dans `.env` :
```
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=votre@email.com
SMTP_PASS=mot_de_passe_app_gmail
```

### 3. Configurer les Tâches Cron

```bash
crontab -e
```

Ajouter :
```
0 9 * * * php /var/www/demenageur.com/cron/check-trial-expiration.php
1 0 1 * * php /var/www/demenageur.com/cron/reset-monthly-leads.php
0 2 * * * php /var/www/demenageur.com/cron/cleanup-expired-leads.php
```

## Accès au Système

- **Admin** : https://votre-domaine.com/admin/login.php
- **Déménageur** : https://votre-domaine.com/demenageur/login.php
- **Site Public** : https://votre-domaine.com

## Support

- Voir `DEPLOIEMENT.md` pour le guide complet
- Email : support@demenageur.com
