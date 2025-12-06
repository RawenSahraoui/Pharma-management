# 📘 GUIDE COMPLET D'UTILISATION - PharmaPro

## 🎯 Table des matières
1. [Configuration initiale](#configuration-initiale)
2. [Comprendre le fichier .env](#comprendre-le-fichier-env)
3. [Installation pas à pas](#installation-pas-à-pas)
4. [Création de la base de données](#création-de-la-base-de-données)
5. [Premier utilisateur](#premier-utilisateur)
6. [Structure des fichiers](#structure-des-fichiers)
7. [Commandes essentielles](#commandes-essentielles)
8. [Problèmes courants](#problèmes-courants)

---

## 1. Configuration initiale

### Prérequis vérifiés ✅
```bash
# Vérifier PHP
php -v
# Doit afficher: PHP 8.2 ou supérieur

# Vérifier Composer
composer --version

# Vérifier Node.js
node -v
npm -v

# Vérifier MySQL
mysql --version
```

---

## 2. Comprendre le fichier .env

### 📝 Le fichier .env expliqué ligne par ligne

#### A. Copier le fichier d'exemple
```bash
cp .env .env.local
```

#### B. Ouvrir .env.local et modifier:

**1. APP_ENV**
```env
APP_ENV=dev
```
- `dev` = Mode développement (recommandé pour débuter)
- `prod` = Mode production (quand le site est en ligne)

**2. APP_SECRET**
```env
APP_SECRET=changez_cette_cle_secrete_123456
```
⚠️ **IMPORTANT**: Changez cette valeur! Utilisez n'importe quelle chaîne aléatoire longue.

**3. DATABASE_URL** (LE PLUS IMPORTANT!)

### Pour XAMPP (Windows/Mac):
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

### Pour WAMP (Windows):
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

### Pour MAMP (Mac):
```env
DATABASE_URL="mysql://root:root@127.0.0.1:8889/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

### Si vous avez un mot de passe MySQL:
```env
DATABASE_URL="mysql://root:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

### Explication de DATABASE_URL:
```
mysql://     [utilisateur] : [mot_de_passe] @ [hôte] : [port] / [nom_base]
             └─────────────┘ └──────────────┘ └──────┘ └────┘ └─────────┘
                root              vide        127.0.0.1  3306   pharma_db
```

---

## 3. Installation pas à pas

### Étape 1: Extraire le projet
```bash
# Dézipper le dossier téléchargé
# Ouvrir le terminal dans ce dossier
cd pharma-project-complete
```

### Étape 2: Installer les dépendances
```bash
# Installer les dépendances PHP
composer install

# Si erreur de mémoire:
php -d memory_limit=-1 /usr/local/bin/composer install
```

### Étape 3: Installer les dépendances JavaScript
```bash
npm install

# Si erreur, essayez:
npm install --legacy-peer-deps
```

### Étape 4: Configurer l'environnement
```bash
# Copier le fichier .env
cp .env .env.local

# Éditer .env.local avec votre éditeur
# Windows: notepad .env.local
# Mac: open .env.local
# Linux: nano .env.local
```

---

## 4. Création de la base de données

### Méthode automatique (RECOMMANDÉE):
```bash
# Tout en une commande
php bin/console doctrine:database:create && \
php bin/console make:migration && \
php bin/console doctrine:migrations:migrate --no-interaction
```

### Méthode pas à pas:

**1. Créer la base de données**
```bash
php bin/console doctrine:database:create
```
✅ Succès: "Created database `pharma_db`"
❌ Erreur: Vérifier DATABASE_URL dans .env.local

**2. Générer la migration**
```bash
php bin/console make:migration
```
✅ Un fichier sera créé dans `migrations/`

**3. Appliquer la migration**
```bash
php bin/console doctrine:migrations:migrate
```
✅ Les tables seront créées dans la base de données

**4. Compiler les assets**
```bash
npm run build
```

---

## 5. Premier utilisateur

### Créer un compte administrateur

**Option 1: Via commande Symfony**
```bash
php bin/console make:user
# Suivre les instructions
```

**Option 2: Via SQL (rapide)**
```sql
-- Ouvrir phpMyAdmin ou MySQL
-- Exécuter cette commande:

INSERT INTO user (email, roles, password, first_name, last_name, is_active, created_at) 
VALUES (
    'admin@pharma.local',
    '["ROLE_ADMIN"]',
    '$2y$13$hash_du_mot_de_passe',  -- admin123
    'Admin',
    'PharmaPro',
    1,
    NOW()
);
```

**Option 3: Créer un fichier de fixtures**
Créer `src/DataFixtures/UserFixtures.php`:
```php
<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@pharma.local');
        $admin->setFirstName('Admin');
        $admin->setLastName('PharmaPro');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        
        $manager->persist($admin);
        $manager->flush();
    }
}
```

Puis charger:
```bash
composer require --dev doctrine/doctrine-fixtures-bundle
php bin/console doctrine:fixtures:load
```

---

## 6. Structure des fichiers

### Fichiers ESSENTIELS à connaître:

```
pharma-project-complete/
│
├── .env.local                 ⚠️ À CRÉER - Configuration locale
├── composer.json             📦 Dépendances PHP
├── package.json              📦 Dépendances JavaScript
│
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml    🗄️ Configuration base de données
│   │   └── security.yaml    🔒 Configuration sécurité
│   └── services.yaml        ⚙️ Services de l'application
│
├── src/
│   ├── Entity/              📊 Modèles de données
│   ├── Controller/          🎮 Contrôleurs (logique)
│   ├── Repository/          🔍 Requêtes base de données
│   └── Service/             💼 Services métier
│
├── templates/               🎨 Vues (HTML)
├── assets/                  🎯 JavaScript et CSS
└── public/                  🌐 Point d'entrée web
```

---

## 7. Commandes essentielles

### Développement quotidien

```bash
# Démarrer le serveur
symfony server:start
# OU
php -S localhost:8000 -t public/

# Compiler les assets en mode watch
npm run watch

# Vider le cache
php bin/console cache:clear
```

### Base de données

```bash
# Créer une nouvelle entité
php bin/console make:entity NomEntite

# Générer une migration
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Voir l'état des migrations
php bin/console doctrine:migrations:status
```

### Création de code

```bash
# Créer un contrôleur
php bin/console make:controller NomController

# Créer un formulaire
php bin/console make:form NomFormType

# Créer une commande
php bin/console make:command app:nom-commande

# Créer un CRUD complet
php bin/console make:crud NomEntite
```

---

## 8. Problèmes courants

### ❌ Erreur: "Connection refused" lors de la création de la base

**Solution:**
```bash
# Vérifier que MySQL est démarré
# Windows (XAMPP): Ouvrir XAMPP Control Panel et démarrer MySQL
# Mac (MAMP): Ouvrir MAMP et démarrer les serveurs
# Linux: sudo service mysql start
```

### ❌ Erreur: "Access denied for user 'root'@'localhost'"

**Solution:**
```bash
# Vérifier le mot de passe MySQL dans .env.local
# Pour XAMPP par défaut: pas de mot de passe
DATABASE_URL="mysql://root:@127.0.0.1:3306/pharma_db?..."

# Si vous avez un mot de passe:
DATABASE_URL="mysql://root:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/pharma_db?..."
```

### ❌ Erreur: "No such file or directory: vendor/autoload.php"

**Solution:**
```bash
composer install
```

### ❌ Erreur: "Module npm not found"

**Solution:**
```bash
npm install
```

### ❌ Page blanche après installation

**Solution:**
```bash
# Vérifier les logs
tail -f var/log/dev.log

# Vider le cache
php bin/console cache:clear

# Vérifier les permissions
chmod -R 777 var/
```

### ❌ Erreur: "Route not found"

**Solution:**
```bash
# Lister toutes les routes
php bin/console debug:router

# Vider le cache du routeur
php bin/console cache:clear --env=dev
```

---

## 🎯 Workflow quotidien recommandé

```bash
# 1. Démarrer MySQL (XAMPP/MAMP/WAMP)

# 2. Démarrer le serveur Symfony
symfony server:start -d

# 3. Compiler les assets en mode watch (dans un autre terminal)
npm run watch

# 4. Ouvrir le navigateur
# http://localhost:8000

# 5. Se connecter
# Email: admin@pharma.local
# Mot de passe: admin123
```

---

## 📞 Besoin d'aide?

1. Consulter les logs: `var/log/dev.log`
2. Vider le cache: `php bin/console cache:clear`
3. Vérifier la configuration: `php bin/console debug:config`
4. Lister les routes: `php bin/console debug:router`

---

## ✅ Checklist après installation

- [ ] .env.local créé et configuré
- [ ] `composer install` exécuté sans erreur
- [ ] `npm install` exécuté sans erreur
- [ ] Base de données créée
- [ ] Migrations appliquées
- [ ] Assets compilés (`npm run build`)
- [ ] Utilisateur administrateur créé
- [ ] Serveur démarré
- [ ] Page de login accessible
- [ ] Connexion réussie

---

**🎉 Félicitations! Vous êtes prêt à utiliser PharmaPro!**
