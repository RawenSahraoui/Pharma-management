# 🚀 COMMENCEZ ICI - PharmaPro

## ✅ Votre projet complet est prêt !

Vous avez téléchargé **46 fichiers** constituant un projet Symfony 7 professionnel.

---

## 📋 ÉTAPES RAPIDES (5 minutes)

### 1️⃣ Configuration de la base de données

**Ouvrir le fichier `.env.local` (à créer)**

```bash
# Copier le fichier d'exemple
cp .env .env.local
```

**Modifier UNIQUEMENT cette ligne dans .env.local:**

### 👉 Pour XAMPP (le plus courant):
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

### 👉 Pour WAMP:
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

### 👉 Pour MAMP:
```env
DATABASE_URL="mysql://root:root@127.0.0.1:8889/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

### 👉 Si vous avez un mot de passe MySQL:
```env
DATABASE_URL="mysql://root:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/pharma_db?serverVersion=8.0&charset=utf8mb4"
```

**C'est tout pour la configuration ! 🎉**

---

### 2️⃣ Installation automatique

```bash
# Rendre le script exécutable
chmod +x INSTALL.sh

# Lancer l'installation
./INSTALL.sh
```

Le script va:
- ✅ Installer les dépendances PHP
- ✅ Installer les dépendances JavaScript  
- ✅ Créer la base de données
- ✅ Créer les tables
- ✅ Compiler les assets

---

### 3️⃣ Démarrer le serveur

```bash
# Option 1: Avec Symfony CLI (recommandé)
symfony server:start

# Option 2: Avec PHP
php -S localhost:8000 -t public/
```

**Accéder à:** http://localhost:8000

---

## 🔑 Connexion

Pour vous connecter, vous devez d'abord créer un utilisateur.

### Méthode rapide - Créer un admin:

```bash
php bin/console make:user

# Répondre aux questions:
# Email: admin@pharma.local
# Password: admin123
# Roles: ROLE_ADMIN
```

Ou créer un fichier de fixtures (voir COMMENT-UTILISER.md)

---

## 📁 Fichiers importants à connaître

### 📖 Documentation:
- **COMMENT-UTILISER.md** ⭐ - Guide détaillé pas à pas
- **INSTALLATION_RAPIDE.md** - Installation en 5 min
- **PRESENTATION.md** - Vue d'ensemble du projet
- **.env.EXEMPLE-AVEC-EXPLICATIONS** - Explications complètes du .env

### ⚙️ Configuration:
- **.env.local** - À CRÉER - Votre configuration locale
- **composer.json** - Dépendances PHP
- **package.json** - Dépendances JavaScript

### 💻 Code:
- **src/Entity/** - Modèles de données
- **src/Controller/** - Logique de l'application
- **templates/** - Pages web (HTML)

---

## ✨ Ce qui est déjà fait

### ✅ Entités créées (4):
- User (utilisateurs)
- Product (produits)
- Category (catégories)
- StockMovement (mouvements de stock)

### ✅ Contrôleurs créés (4):
- SecurityController (login/logout)
- DashboardController (tableau de bord)
- ProductController (gestion produits)
- POSController (point de vente)

### ✅ Templates créés (5):
- Login (page de connexion)
- Dashboard (tableau de bord)
- Liste des produits
- Point de vente (POS)
- Template de base (sidebar, navbar)

### ✅ Configuration:
- Sécurité (authentification, rôles)
- Base de données (Doctrine)
- Webpack (assets)
- Services

---

## 🎯 Que faire ensuite?

### Après l'installation:

1. **Tester la connexion**
   - Créer un utilisateur admin
   - Se connecter à http://localhost:8000/login

2. **Explorer l'interface**
   - Tableau de bord: `/`
   - Produits: `/inventory/product`
   - Point de vente: `/sales/pos`

3. **Ajouter des données**
   - Créer des catégories
   - Ajouter des produits
   - Tester le point de vente

4. **Personnaliser**
   - Modifier les couleurs dans `assets/styles/app.css`
   - Ajouter votre logo
   - Personnaliser les templates

---

## 🐛 Problèmes courants

### ❌ "Access denied" à la base de données
**Solution:** Vérifier le mot de passe MySQL dans `.env.local`

### ❌ Page blanche
**Solution:** 
```bash
php bin/console cache:clear
chmod -R 777 var/
```

### ❌ "vendor/autoload.php not found"
**Solution:** `composer install`

### ❌ Assets non chargés
**Solution:** `npm run build`

---

## 📚 Documentation complète

Pour tout savoir en détail, consultez:

1. **COMMENT-UTILISER.md** - Guide complet pas à pas
2. **INSTALLATION_RAPIDE.md** - Installation rapide
3. **DOCUMENTATION_COMPLETE.md** - Documentation technique

---

## 🆘 Aide

Tous les fichiers sont commentés et documentés.

En cas de problème:
1. Consulter `var/log/dev.log`
2. Lire COMMENT-UTILISER.md section "Problèmes courants"
3. Vérifier que MySQL est démarré

---

## ✅ Checklist rapide

- [ ] Fichier .env.local créé
- [ ] DATABASE_URL configuré
- [ ] `./INSTALL.sh` exécuté
- [ ] Base de données créée
- [ ] Utilisateur admin créé
- [ ] Serveur démarré
- [ ] Connexion réussie

---

## 🎉 C'est parti !

```bash
# Résumé complet en 4 commandes:
cp .env .env.local              # 1. Créer la config
# Éditer .env.local              # 2. Configurer MySQL
./INSTALL.sh                    # 3. Installer
symfony server:start            # 4. Démarrer
```

**Votre application sera accessible à: http://localhost:8000**

---

**Bon développement ! 🚀**

*Pour toute question, consultez COMMENT-UTILISER.md*
