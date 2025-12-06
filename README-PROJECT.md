# 🏥 PharmaPro - Système de Gestion de Pharmacie

![Symfony](https://img.shields.io/badge/Symfony-7.0-black?logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?logo=php)
![License](https://img.shields.io/badge/License-MIT-green)

## 📋 À propos

PharmaPro est un système complet de gestion de pharmacie développé avec **Symfony 7**. Il permet de gérer tous les aspects d'une pharmacie moderne : stocks, ventes, prescriptions, fournisseurs, et bien plus.

## ✨ Fonctionnalités principales

- ✅ **Gestion des stocks** - Produits, catégories, mouvements, alertes
- ✅ **Point de vente (POS)** - Interface moderne avec scanner de code-barres
- ✅ **Gestion des fournisseurs** - Commandes, livraisons, factures
- ✅ **Prescriptions** - Patients, médecins, délivrance
- ✅ **Notifications** - Stock bas, produits périmés, alertes diverses
- ✅ **Rapports** - Ventes, stocks, analyses financières
- ✅ **Multi-utilisateurs** - Système de rôles et permissions

## 🚀 Installation rapide

### Prérequis
- PHP >= 8.2
- Composer 2.x
- MySQL 8.0 ou PostgreSQL 15
- Node.js >= 18.x et npm

### Installation

```bash
# 1. Installer les dépendances PHP
composer install

# 2. Installer les dépendances JavaScript
npm install

# 3. Configurer l'environnement
cp .env .env.local
# Modifier .env.local avec vos paramètres de base de données

# 4. Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Compiler les assets
npm run build

# 6. Démarrer le serveur
symfony server:start
# ou
php -S localhost:8000 -t public/
```

## 📖 Documentation complète

- **LISEZ-MOI-EN-PREMIER.md** - À lire en premier ⭐
- **INSTALLATION_RAPIDE.md** - Guide d'installation en 5 minutes
- **DOCUMENTATION_COMPLETE.md** - Documentation détaillée
- **ARBORESCENCE_PROJET.md** - Structure du projet

## 🛠️ Technologies

- **Backend**: Symfony 7.0, PHP 8.2+, Doctrine ORM
- **Frontend**: Bootstrap 5, jQuery, Webpack Encore
- **Base de données**: MySQL 8.0 / PostgreSQL 15
- **UI/UX**: DataTables, Select2, Chart.js, Toastr, SweetAlert2

## 📂 Structure du projet

```
pharma-project-complete/
├── config/              # Configuration Symfony
├── src/                 # Code source PHP
│   ├── Controller/      # Contrôleurs
│   ├── Entity/          # Entités Doctrine
│   ├── Service/         # Services métier
│   └── Repository/      # Repositories
├── templates/           # Templates Twig
├── assets/              # Assets front-end (JS, CSS)
├── public/              # Point d'entrée web
└── migrations/          # Migrations base de données
```

## 👤 Comptes par défaut

Après avoir chargé les fixtures:

- **Admin**: admin@pharma.local / admin123
- **Manager**: manager@pharma.local / manager123
- **Pharmacien**: pharmacist@pharma.local / pharma123
- **Caissier**: cashier@pharma.local / cashier123

## 🧪 Tests

```bash
# Lancer tous les tests
php bin/phpunit

# Tests avec couverture
XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/coverage
```

## 📝 Commandes utiles

```bash
# Vérifier les produits périmés
php bin/console app:check-expiry

# Générer les alertes
php bin/console app:generate-alerts

# Nettoyer le cache
php bin/console cache:clear
```

## 🤝 Contribution

Les contributions sont les bienvenues! Merci de consulter le guide de contribution.

## 📄 Licence

Ce projet est sous licence MIT.

## 📧 Contact

Pour toute question: support@pharma.local

---

**Développé avec ❤️ pour les pharmacies modernes**
