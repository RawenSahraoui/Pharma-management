# 🚀 Guide d'Installation Rapide - PharmaPro

## ⚡ Installation en 5 minutes

### 1. Prérequis rapides
```bash
# Vérifier PHP
php -v  # Doit être >= 8.2

# Vérifier Composer
composer -V

# Vérifier Node.js
node -v  # Doit être >= 18.x
npm -v
```

### 2. Installation Express

```bash
# 1. Cloner et accéder au projet
git clone https://github.com/votre-compte/pharma-management.git
cd pharma-management

# 2. Installer les dépendances
composer install --no-interaction
npm install

# 3. Configuration rapide
cp .env .env.local
# Éditer .env.local avec vos paramètres de base de données

# 4. Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# 5. Assets
npm run build

# 6. Démarrer
symfony server:start -d
# ou
php -S localhost:8000 -t public/
```

### 3. Première connexion

🌐 **URL**: http://localhost:8000

👤 **Connexion Admin**:
- Email: `admin@pharma.local`
- Mot de passe: `admin123`

## 🔧 Configuration Minimale

### .env.local (Configuration minimale)
```env
APP_ENV=dev
APP_SECRET=changez_cette_cle_en_production

DATABASE_URL="mysql://root:@127.0.0.1:3306/pharma_db?serverVersion=8.0"

MAILER_DSN=smtp://localhost:1025
```

## 📋 Checklist Post-Installation

- [ ] Se connecter avec le compte admin
- [ ] Changer le mot de passe admin
- [ ] Créer des catégories de produits
- [ ] Ajouter quelques produits de test
- [ ] Tester le point de vente
- [ ] Vérifier les alertes

## 🎯 Commandes Essentielles

```bash
# Vider le cache
php bin/console cache:clear

# Créer un utilisateur
php bin/console make:user

# Lancer les tests
php bin/phpunit

# Vérifier la qualité du code
composer phpstan

# Mise à jour des assets
npm run watch  # Mode développement avec auto-reload
```

## 🐛 Dépannage Rapide

### Erreur de connexion à la base de données
```bash
# Vérifier que MySQL est démarré
# Vérifier les credentials dans .env.local
# Créer manuellement la base de données
mysql -u root -p
CREATE DATABASE pharma_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Erreur de permissions
```bash
sudo chmod -R 777 var/
sudo chown -R www-data:www-data var/
```

### Assets non compilés
```bash
rm -rf public/build/
npm run build
```

### Cache corrompu
```bash
rm -rf var/cache/*
php bin/console cache:warmup
```

## 📱 Accès Rapide aux Fonctionnalités

| Module | URL | Raccourci |
|--------|-----|-----------|
| Dashboard | `/` | F1 |
| Produits | `/inventory/product` | - |
| Point de Vente | `/sales/pos` | F2 |
| Ventes | `/sales/history` | - |
| Fournisseurs | `/supplier` | - |
| Rapports | `/report` | - |
| Paramètres | `/settings` | - |

## 🎨 Personnalisation Rapide

### Changer le logo
```
public/images/logo.png
```

### Modifier les couleurs
```css
/* assets/styles/app.css */
:root {
    --primary-color: #votre-couleur;
}
```

### Changer le nom de l'application
```
templates/base.html.twig
config/packages/framework.yaml
```

## 📞 Support Rapide

- 📧 Email: support@pharma.local
- 📚 Documentation complète: `README.md`
- 🐛 Issues: https://github.com/votre-compte/pharma-management/issues

## ✅ Prochaines Étapes

1. ✨ Personnaliser les paramètres de l'application
2. 👥 Créer les comptes utilisateurs
3. 📦 Importer vos produits existants
4. 🚚 Configurer vos fournisseurs
5. 🎯 Commencer à utiliser le système!

---

**Temps d'installation estimé**: 5-10 minutes  
**Niveau de difficulté**: ⭐⭐☆☆☆ (Facile)

🎉 **Félicitations! Votre système PharmaPro est prêt à l'emploi!**
