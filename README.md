# 🏥 Système de Gestion de Pharmacie - PharmaPro

## 📋 Description

PharmaPro est un système complet de gestion de pharmacie développé avec Symfony 7. Il permet de gérer efficacement tous les aspects d'une pharmacie moderne.

## ✨ Fonctionnalités principales

### 📦 Gestion des Stocks
- ✅ Gestion complète des produits/médicaments
- ✅ Suivi des mouvements de stock (entrées/sorties)
- ✅ Alertes de réapprovisionnement automatiques
- ✅ Gestion des produits périmés
- ✅ Alertes pour dates d'expiration proches
- ✅ Code-barres et SKU
- ✅ Catégorisation des produits
- ✅ Gestion des lots et dates d'expiration

### 🚚 Gestion des Fournisseurs
- ✅ Fiche complète des fournisseurs
- ✅ Gestion des commandes fournisseurs
- ✅ Suivi des livraisons
- ✅ Gestion des factures fournisseurs
- ✅ Historique des transactions
- ✅ Suivi des délais de livraison

### 💊 Gestion des Prescriptions
- ✅ Enregistrement des prescriptions
- ✅ Gestion des patients
- ✅ Base de données des médecins
- ✅ Suivi des délivrances
- ✅ Alertes d'interactions médicamenteuses
- ✅ Vérification des prescriptions obligatoires

### 💰 Point de Vente (POS)
- ✅ Interface de caisse moderne et intuitive
- ✅ Scanner de code-barres
- ✅ Gestion des paiements (espèces, carte, mobile)
- ✅ Génération automatique de factures/reçus
- ✅ Gestion des retours
- ✅ Remises et promotions
- ✅ Statistiques de ventes en temps réel

### 🔔 Notifications & Alertes
- ✅ Stock bas
- ✅ Rupture de stock
- ✅ Produits périmés
- ✅ Dates d'expiration proches
- ✅ Commandes à traiter
- ✅ Notifications par email et SMS

### 📊 Rapports & Statistiques
- ✅ Rapports de ventes
- ✅ Rapports de stock
- ✅ Rapports financiers
- ✅ Tableaux de bord interactifs
- ✅ Export Excel et PDF

### 👥 Gestion des Utilisateurs
- ✅ Système de rôles et permissions
- ✅ Authentification sécurisée
- ✅ Gestion multi-utilisateurs
- ✅ Journal d'activité

## 🛠️ Technologies utilisées

- **Backend**: Symfony 7.0
- **Base de données**: MySQL 8.0 / PostgreSQL 15
- **ORM**: Doctrine
- **Front-end**: Twig, Bootstrap 5, jQuery
- **Assets**: Webpack Encore
- **API**: RESTful API
- **Tests**: PHPUnit
- **Qualité du code**: PHP CS Fixer, PHPStan

## 📋 Prérequis

- PHP >= 8.2
- Composer 2.x
- MySQL 8.0 ou PostgreSQL 15
- Node.js >= 18.x et npm
- Extension PHP: pdo_mysql, intl, gd, mbstring, xml

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/votre-compte/pharma-management.git
cd pharma-management
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Configuration de l'environnement

Créer le fichier `.env.local` à partir de `.env`:

```bash
cp .env .env.local
```

Modifier `.env.local` avec vos configurations:

```env
# Base de données
DATABASE_URL="mysql://user:password@127.0.0.1:3306/pharma_db?serverVersion=8.0&charset=utf8mb4"

# Email
MAILER_DSN=smtp://user:pass@smtp.example.com:587

# Configuration de l'application
APP_ENV=dev
APP_SECRET=votre_cle_secrete_unique_ici
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Charger les données de test (optionnel)

```bash
php bin/console doctrine:fixtures:load
```

### 7. Compiler les assets

```bash
npm run dev
# ou pour la production
npm run build
```

### 8. Démarrer le serveur

```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

L'application sera accessible à l'adresse: `http://localhost:8000`

## 👤 Utilisateurs par défaut

Après avoir chargé les fixtures:

### Administrateur
- **Email**: admin@pharma.local
- **Mot de passe**: admin123
- **Rôle**: ROLE_ADMIN

### Manager
- **Email**: manager@pharma.local
- **Mot de passe**: manager123
- **Rôle**: ROLE_MANAGER

### Pharmacien
- **Email**: pharmacist@pharma.local
- **Mot de passe**: pharma123
- **Rôle**: ROLE_PHARMACIST

### Caissier
- **Email**: cashier@pharma.local
- **Mot de passe**: cashier123
- **Rôle**: ROLE_CASHIER

## 🎯 Utilisation

### Dashboard
Accédez au tableau de bord principal pour avoir une vue d'ensemble:
- Statistiques de ventes
- État des stocks
- Alertes importantes
- Graphiques de performance

### Gestion des Produits
1. Allez dans **Produits** > **Liste des produits**
2. Cliquez sur **Nouveau produit** pour ajouter un produit
3. Remplissez les informations (nom, code-barres, prix, stock, etc.)
4. Définissez les seuils de réapprovisionnement

### Point de Vente
1. Allez dans **Ventes** > **Point de Vente**
2. Recherchez ou scannez les produits
3. Ajoutez-les au panier
4. Choisissez le mode de paiement
5. Validez la vente
6. Imprimez le reçu

### Gestion des Commandes Fournisseurs
1. Allez dans **Fournisseurs** > **Commandes**
2. Créez une nouvelle commande
3. Sélectionnez le fournisseur
4. Ajoutez les produits
5. Suivez l'état de la commande
6. Réceptionnez la livraison

### Alertes et Notifications
- Les alertes sont générées automatiquement
- Consultez-les dans la section **Alertes**
- Configurez les notifications par email dans **Paramètres**

## 📱 API REST

L'application expose une API REST pour l'intégration avec d'autres systèmes.

### Endpoints principaux

```
GET    /api/products              - Liste des produits
GET    /api/products/{id}         - Détails d'un produit
POST   /api/products              - Créer un produit
PUT    /api/products/{id}         - Modifier un produit
DELETE /api/products/{id}         - Supprimer un produit

GET    /api/sales                 - Liste des ventes
POST   /api/sales                 - Créer une vente

GET    /api/stock/movements       - Mouvements de stock
POST   /api/stock/movements       - Enregistrer un mouvement
```

### Authentification API
Utilisez un token JWT pour l'authentification:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin@pharma.local","password":"admin123"}'
```

## 🧪 Tests

### Lancer tous les tests

```bash
php bin/phpunit
```

### Tests spécifiques

```bash
# Tests unitaires
php bin/phpunit tests/Unit

# Tests fonctionnels
php bin/phpunit tests/Functional
```

### Couverture de code

```bash
XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/coverage
```

## 🔧 Commandes utiles

### Vérifier les produits périmés

```bash
php bin/console app:check-expiry
```

### Générer les alertes

```bash
php bin/console app:generate-alerts
```

### Nettoyer les anciennes données

```bash
php bin/console app:cleanup-old-data
```

### Importer des produits depuis un fichier CSV

```bash
php bin/console app:import-products fichier.csv
```

## 📚 Documentation

### Structure du projet

```
src/
├── Controller/      # Contrôleurs
├── Entity/          # Entités Doctrine
├── Repository/      # Repositories
├── Service/         # Services métier
├── Form/            # Formulaires Symfony
├── Security/        # Sécurité et authentification
└── EventListener/   # Écouteurs d'événements

templates/           # Templates Twig
assets/             # Assets front-end (JS, CSS)
config/             # Configuration
migrations/         # Migrations de base de données
tests/              # Tests
```

### Ajout de nouvelles fonctionnalités

1. Créer l'entité:
```bash
php bin/console make:entity NomEntite
```

2. Créer le contrôleur:
```bash
php bin/console make:controller NomController
```

3. Créer le formulaire:
```bash
php bin/console make:form NomType
```

4. Créer la migration:
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 🔒 Sécurité

### Rôles et Permissions

- **ROLE_USER**: Accès de base
- **ROLE_CASHIER**: Accès au point de vente
- **ROLE_PHARMACIST**: Gestion des prescriptions
- **ROLE_MANAGER**: Gestion des stocks et rapports
- **ROLE_ADMIN**: Administration complète

### Bonnes pratiques

- Changez les mots de passe par défaut
- Utilisez HTTPS en production
- Configurez les sauvegardes automatiques
- Activez les logs de sécurité
- Mettez à jour régulièrement

## 🐛 Débogage

### Mode debug

```bash
APP_ENV=dev php bin/console cache:clear
```

### Consulter les logs

```bash
tail -f var/log/dev.log
```

### Profiler Symfony

Activé automatiquement en mode dev à l'adresse:
`http://localhost:8000/_profiler`

## 📦 Déploiement en production

### 1. Optimiser l'autoloader

```bash
composer install --no-dev --optimize-autoloader
```

### 2. Compiler les assets

```bash
npm run build
```

### 3. Vider le cache

```bash
APP_ENV=prod php bin/console cache:clear
```

### 4. Configurer les permissions

```bash
chmod -R 777 var/cache var/log
```

### 5. Configurer le serveur web

Exemple de configuration Apache:

```apache
<VirtualHost *:80>
    ServerName pharma.example.com
    DocumentRoot /var/www/pharma/public

    <Directory /var/www/pharma/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 🤝 Contribution

Les contributions sont les bienvenues! Merci de:
1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📧 Contact

Pour toute question ou support:
- Email: support@pharma.local
- Documentation: https://docs.pharma.local
- Issues: https://github.com/votre-compte/pharma-management/issues

## 🙏 Remerciements

- [Symfony](https://symfony.com/)
- [Doctrine](https://www.doctrine-project.org/)
- [Bootstrap](https://getbootstrap.com/)
- [Font Awesome](https://fontawesome.com/)
- Toute la communauté open source

---

**Développé avec ❤️ pour les pharmacies modernes**
