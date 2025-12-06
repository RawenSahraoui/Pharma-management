#!/bin/bash

echo "╔══════════════════════════════════════════════════════════╗"
echo "║          Installation PharmaPro                          ║"
echo "║     Système de Gestion de Pharmacie - Symfony 7         ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}📦 Installation des dépendances PHP...${NC}"
composer install --no-interaction

echo ""
echo -e "${BLUE}📦 Installation des dépendances JavaScript...${NC}"
npm install

echo ""
echo -e "${BLUE}⚙️  Configuration de l'environnement...${NC}"
if [ ! -f .env.local ]; then
    cp .env .env.local
    echo -e "${GREEN}✓ Fichier .env.local créé${NC}"
    echo -e "${RED}⚠️  N'oubliez pas de configurer votre base de données dans .env.local${NC}"
else
    echo -e "${GREEN}✓ Fichier .env.local existe déjà${NC}"
fi

echo ""
read -p "Voulez-vous créer la base de données maintenant? (o/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[OoYy]$ ]]; then
    echo -e "${BLUE}🗄️  Création de la base de données...${NC}"
    php bin/console doctrine:database:create --if-not-exists
    
    echo -e "${BLUE}🔄 Application des migrations...${NC}"
    php bin/console doctrine:migrations:migrate --no-interaction
    
    echo -e "${GREEN}✓ Base de données configurée${NC}"
fi

echo ""
echo -e "${BLUE}🎨 Compilation des assets...${NC}"
npm run build

echo ""
echo -e "${BLUE}🧹 Nettoyage du cache...${NC}"
php bin/console cache:clear

echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║              Installation terminée! 🎉                   ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""
echo -e "${GREEN}✓ Projet installé avec succès${NC}"
echo ""
echo "Pour démarrer le serveur:"
echo "  symfony server:start"
echo ""
echo "Ou avec PHP:"
echo "  php -S localhost:8000 -t public/"
echo ""
echo "Accédez ensuite à: http://localhost:8000"
echo ""
