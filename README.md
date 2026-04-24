# MediTimmy — Guide d'installation

## Prérequis
- PHP 8.1+
- MySQL 8.0+ / MariaDB 10.6+
- Apache avec mod_rewrite **ou** Nginx

---

## 1. Installation

```bash
# Placer le projet dans votre répertoire web
cp -r meditimmy/ /var/www/html/meditimmy

# Donner les droits si nécessaire
chmod -R 755 /var/www/html/meditimmy
```

---

## 2. Base de données

```bash
# Créer la base et importer le schéma
mysql -u root -p < database.sql
```

Modifier `config/database.php` avec vos identifiants MySQL :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'meditimmy');
define('DB_USER', 'root');
define('DB_PASS', 'votre_mot_de_passe');
```

---

## 3. Configuration Apache

Créer un `.htaccess` à la racine du projet :

```apache
RewriteEngine On
RewriteBase /meditimmy/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=$1 [QSA,L]
```

Activer mod_rewrite :
```bash
a2enmod rewrite
service apache2 restart
```

---

## 4. Configuration Nginx (alternative)

```nginx
location /meditimmy/ {
    root /var/www/html;
    index index.php;
    try_files $uri $uri/ /meditimmy/index.php?$query_string;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

## 5. BASE_URL

Si votre projet n'est pas dans un sous-dossier `/meditimmy`, modifier dans `config/app.php` :
```php
define('BASE_URL', '');  // racine du domaine
// ou
define('BASE_URL', '/mon-dossier');  // sous-dossier
```

---

## 6. Comptes de démonstration

| Rôle        | Identifiant  | Mot de passe |
|-------------|-------------|--------------|
| Admin local | `admin`     | `Admin1234!` |
| Médecin     | `dr.martin` | `Medecin1!`  |
| Secrétaire  | `sec.dupont`| `Secret1!`   |

> ⚠️ Changez ces mots de passe en production !

---

## 7. Structure des fichiers

```
meditimmy/
├── index.php              ← Routeur principal
├── database.sql           ← Schéma + données de démo
├── config/
│   ├── app.php            ← Configuration générale
│   └── database.php       ← Connexion PDO
├── controllers/           ← Logique métier
├── models/                ← Accès données (PDO)
├── views/                 ← Templates PHP
├── middleware/            ← Auth + permissions
└── public/
    ├── css/app.css        ← Styles MediTimmy
    └── img/logo.png       ← Logo
```

---

## 8. Fonctionnalités clés

- **Agenda FullCalendar** avec drag & drop (praticiens/secrétaire)
- **Patients** : lecture seule pour les patients, pas de modification possible
- **Dossiers médicaux** : accessible uniquement aux praticiens
- **Secrétaire** : agenda + fiches administratives, sans accès médical (US-12 / US-25)
- **Admin local** : gestion des comptes + rôles personnalisables avec permissions granulaires
- **Création de compte** patient : username auto-généré (prenom.nom), mot de passe temporaire affiché
- **Session timeout** : déconnexion automatique après 30 minutes d'inactivité (US-07)
- **CSRF** : protection sur tous les formulaires POST

---

## 9. Sécurité

- Mots de passe hashés avec `password_hash()` (bcrypt, coût 12)
- Protection CSRF sur toutes les actions POST
- Validation des permissions côté serveur à chaque requête
- Sessions PHP sécurisées avec timeout automatique
- Échappement HTML systématique via `e()`

---

*MediTimmy v1.0 — Architecture MVC PHP — 33 User Stories couvertes*
