# 🏥 SYSTÈME DE GESTION DE PHARMACIE - SYMFONY 7

## 📁 ARBORESCENCE COMPLÈTE DU PROJET

```
pharma-management/
│
├── 📂 config/                          # Configuration de l'application
│   ├── packages/
│   │   ├── doctrine.yaml               # Configuration ORM
│   │   ├── security.yaml               # Sécurité et authentification
│   │   ├── framework.yaml              # Framework Symfony
│   │   ├── messenger.yaml              # Système de messages/jobs
│   │   ├── monolog.yaml                # Logs
│   │   ├── twig.yaml                   # Moteur de templates
│   │   ├── validator.yaml              # Validation
│   │   ├── mailer.yaml                 # Emails
│   │   └── webpack_encore.yaml         # Assets
│   ├── routes/
│   │   ├── annotations.yaml
│   │   └── api.yaml                    # Routes API REST
│   ├── services.yaml                   # Container de services
│   ├── bundles.php
│   └── routes.yaml
│
├── 📂 src/
│   │
│   ├── 📂 Controller/                  # Contrôleurs
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserManagementController.php
│   │   │   └── SystemSettingsController.php
│   │   ├── Inventory/
│   │   │   ├── ProductController.php
│   │   │   ├── StockController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ExpiryController.php
│   │   │   └── StockMovementController.php
│   │   ├── Supplier/
│   │   │   ├── SupplierController.php
│   │   │   ├── OrderController.php
│   │   │   ├── DeliveryController.php
│   │   │   └── InvoiceController.php
│   │   ├── Prescription/
│   │   │   ├── PrescriptionController.php
│   │   │   ├── PatientController.php
│   │   │   ├── DoctorController.php
│   │   │   └── DispenseController.php
│   │   ├── Sales/
│   │   │   ├── POSController.php
│   │   │   ├── SaleController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── InvoiceController.php
│   │   │   └── ReturnController.php
│   │   ├── Notification/
│   │   │   ├── AlertController.php
│   │   │   └── NotificationController.php
│   │   └── Api/
│   │       ├── ProductApiController.php
│   │       ├── StockApiController.php
│   │       ├── SaleApiController.php
│   │       └── SupplierApiController.php
│   │
│   ├── 📂 Entity/                      # Entités Doctrine
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Stock.php
│   │   ├── StockMovement.php
│   │   ├── Supplier.php
│   │   ├── SupplierOrder.php
│   │   ├── SupplierOrderItem.php
│   │   ├── Delivery.php
│   │   ├── DeliveryItem.php
│   │   ├── SupplierInvoice.php
│   │   ├── Patient.php
│   │   ├── Doctor.php
│   │   ├── Prescription.php
│   │   ├── PrescriptionItem.php
│   │   ├── Dispense.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   ├── Payment.php
│   │   ├── CustomerInvoice.php
│   │   ├── Alert.php
│   │   ├── Notification.php
│   │   └── SystemSetting.php
│   │
│   ├── 📂 Repository/                  # Repositories Doctrine
│   │   ├── UserRepository.php
│   │   ├── ProductRepository.php
│   │   ├── StockRepository.php
│   │   ├── StockMovementRepository.php
│   │   ├── SupplierRepository.php
│   │   ├── SupplierOrderRepository.php
│   │   ├── PrescriptionRepository.php
│   │   ├── SaleRepository.php
│   │   └── AlertRepository.php
│   │
│   ├── 📂 Service/                     # Services métier
│   │   ├── Inventory/
│   │   │   ├── StockManagementService.php
│   │   │   ├── ExpiryCheckService.php
│   │   │   ├── ReorderService.php
│   │   │   └── ProductSearchService.php
│   │   ├── Supplier/
│   │   │   ├── OrderManagementService.php
│   │   │   ├── DeliveryProcessService.php
│   │   │   └── InvoiceService.php
│   │   ├── Prescription/
│   │   │   ├── PrescriptionValidationService.php
│   │   │   ├── DrugInteractionService.php
│   │   │   └── DispenseService.php
│   │   ├── Sales/
│   │   │   ├── POSService.php
│   │   │   ├── PaymentService.php
│   │   │   ├── InvoiceGeneratorService.php
│   │   │   └── ReceiptService.php
│   │   ├── Notification/
│   │   │   ├── AlertService.php
│   │   │   ├── EmailNotificationService.php
│   │   │   └── SMSNotificationService.php
│   │   ├── Report/
│   │   │   ├── SalesReportService.php
│   │   │   ├── StockReportService.php
│   │   │   └── FinancialReportService.php
│   │   └── Common/
│   │       ├── PdfGeneratorService.php
│   │       ├── ExcelExportService.php
│   │       └── BarcodeService.php
│   │
│   ├── 📂 Form/                        # Formulaires Symfony
│   │   ├── Product/
│   │   │   ├── ProductType.php
│   │   │   ├── CategoryType.php
│   │   │   └── StockAdjustmentType.php
│   │   ├── Supplier/
│   │   │   ├── SupplierType.php
│   │   │   ├── OrderType.php
│   │   │   └── DeliveryType.php
│   │   ├── Prescription/
│   │   │   ├── PrescriptionType.php
│   │   │   ├── PatientType.php
│   │   │   └── DispenseType.php
│   │   ├── Sales/
│   │   │   ├── SaleType.php
│   │   │   └── PaymentType.php
│   │   └── User/
│   │       ├── UserType.php
│   │       ├── RegistrationType.php
│   │       └── PasswordChangeType.php
│   │
│   ├── 📂 Security/                    # Sécurité
│   │   ├── Voter/
│   │   │   ├── ProductVoter.php
│   │   │   ├── OrderVoter.php
│   │   │   └── SaleVoter.php
│   │   ├── UserAuthenticator.php
│   │   └── AccessDeniedHandler.php
│   │
│   ├── 📂 EventListener/              # Écouteurs d'événements
│   │   ├── StockMovementListener.php
│   │   ├── ExpiryCheckListener.php
│   │   ├── LowStockListener.php
│   │   ├── SaleListener.php
│   │   └── AuditListener.php
│   │
│   ├── 📂 EventSubscriber/            # Souscripteurs
│   │   ├── NotificationSubscriber.php
│   │   ├── ActivityLogSubscriber.php
│   │   └── CacheInvalidationSubscriber.php
│   │
│   ├── 📂 Command/                    # Commandes console
│   │   ├── CheckExpiryCommand.php
│   │   ├── GenerateAlertsCommand.php
│   │   ├── CleanupOldDataCommand.php
│   │   └── ImportProductsCommand.php
│   │
│   ├── 📂 MessageHandler/             # Handlers Messenger
│   │   ├── SendNotificationHandler.php
│   │   ├── GenerateReportHandler.php
│   │   └── ProcessOrderHandler.php
│   │
│   ├── 📂 Message/                    # Messages async
│   │   ├── SendNotificationMessage.php
│   │   ├── GenerateReportMessage.php
│   │   └── ProcessOrderMessage.php
│   │
│   ├── 📂 Validator/                  # Validateurs personnalisés
│   │   ├── Constraints/
│   │   │   ├── UniqueBarcode.php
│   │   │   ├── ValidExpiryDate.php
│   │   │   └── ValidPrescription.php
│   │   └── Validator/
│   │       ├── UniqueBarcodeValidator.php
│   │       ├── ValidExpiryDateValidator.php
│   │       └── ValidPrescriptionValidator.php
│   │
│   ├── 📂 DTO/                        # Data Transfer Objects
│   │   ├── ProductDTO.php
│   │   ├── SaleDTO.php
│   │   ├── OrderDTO.php
│   │   └── ReportDTO.php
│   │
│   ├── 📂 Enum/                       # Énumérations
│   │   ├── StockMovementType.php
│   │   ├── OrderStatus.php
│   │   ├── PaymentMethod.php
│   │   ├── AlertType.php
│   │   └── UserRole.php
│   │
│   ├── 📂 Exception/                  # Exceptions personnalisées
│   │   ├── InsufficientStockException.php
│   │   ├── ExpiredProductException.php
│   │   ├── InvalidPrescriptionException.php
│   │   └── PaymentException.php
│   │
│   └── Kernel.php
│
├── 📂 templates/                      # Templates Twig
│   ├── base.html.twig
│   ├── layout/
│   │   ├── admin.html.twig
│   │   ├── sidebar.html.twig
│   │   ├── navbar.html.twig
│   │   └── footer.html.twig
│   ├── dashboard/
│   │   └── index.html.twig
│   ├── inventory/
│   │   ├── product/
│   │   │   ├── index.html.twig
│   │   │   ├── show.html.twig
│   │   │   ├── edit.html.twig
│   │   │   └── _form.html.twig
│   │   ├── stock/
│   │   │   ├── index.html.twig
│   │   │   ├── movements.html.twig
│   │   │   └── adjustment.html.twig
│   │   └── expiry/
│   │       ├── index.html.twig
│   │       └── report.html.twig
│   ├── supplier/
│   │   ├── index.html.twig
│   │   ├── show.html.twig
│   │   ├── order/
│   │   │   ├── index.html.twig
│   │   │   ├── create.html.twig
│   │   │   └── show.html.twig
│   │   └── delivery/
│   │       ├── index.html.twig
│   │       └── receive.html.twig
│   ├── prescription/
│   │   ├── index.html.twig
│   │   ├── create.html.twig
│   │   ├── show.html.twig
│   │   └── dispense.html.twig
│   ├── sales/
│   │   ├── pos/
│   │   │   └── index.html.twig
│   │   ├── history.html.twig
│   │   └── invoice.html.twig
│   ├── notification/
│   │   └── alerts.html.twig
│   ├── report/
│   │   ├── sales.html.twig
│   │   ├── stock.html.twig
│   │   └── financial.html.twig
│   ├── user/
│   │   ├── login.html.twig
│   │   ├── profile.html.twig
│   │   └── settings.html.twig
│   └── email/
│       ├── alert_notification.html.twig
│       └── order_confirmation.html.twig
│
├── 📂 assets/                         # Assets front-end
│   ├── app.js                         # Point d'entrée JS
│   ├── styles/
│   │   ├── app.css
│   │   ├── admin.css
│   │   ├── pos.css
│   │   └── components/
│   │       ├── buttons.css
│   │       ├── forms.css
│   │       ├── tables.css
│   │       └── modals.css
│   ├── js/
│   │   ├── inventory/
│   │   │   ├── product-manager.js
│   │   │   ├── stock-tracker.js
│   │   │   └── barcode-scanner.js
│   │   ├── sales/
│   │   │   ├── pos.js
│   │   │   ├── cart.js
│   │   │   └── payment.js
│   │   ├── supplier/
│   │   │   └── order-form.js
│   │   ├── prescription/
│   │   │   └── prescription-form.js
│   │   ├── notification/
│   │   │   └── alert-handler.js
│   │   └── common/
│   │       ├── ajax-handler.js
│   │       ├── datatable.js
│   │       └── form-validator.js
│   └── images/
│       ├── logo.png
│       └── icons/
│
├── 📂 migrations/                     # Migrations base de données
│   └── Version20240101000000.php
│
├── 📂 tests/                          # Tests
│   ├── Unit/
│   │   ├── Service/
│   │   │   ├── StockManagementServiceTest.php
│   │   │   └── POSServiceTest.php
│   │   └── Entity/
│   │       └── ProductTest.php
│   ├── Functional/
│   │   └── Controller/
│   │       ├── ProductControllerTest.php
│   │       └── SaleControllerTest.php
│   └── bootstrap.php
│
├── 📂 public/                         # Dossier public
│   ├── index.php
│   ├── build/                         # Assets compilés
│   └── uploads/
│       ├── products/
│       └── documents/
│
├── 📂 var/                           # Fichiers générés
│   ├── cache/
│   ├── log/
│   └── sessions/
│
├── 📂 vendor/                        # Dépendances
│
├── 📄 .env                          # Configuration environnement
├── 📄 .env.local                    # Config locale (git ignoré)
├── 📄 .gitignore
├── 📄 composer.json                 # Dépendances PHP
├── 📄 composer.lock
├── 📄 package.json                  # Dépendances JS
├── 📄 package-lock.json
├── 📄 symfony.lock
├── 📄 webpack.config.js             # Configuration Webpack
├── 📄 phpunit.xml.dist             # Configuration tests
└── 📄 README.md
```

## 🎯 MODULES PRINCIPAUX

### 1. **Gestion des Stocks (Inventory)**
- Produits/Médicaments
- Catégories
- Mouvements de stock (entrées/sorties)
- Alertes de réapprovisionnement
- Gestion des périmés

### 2. **Gestion des Fournisseurs (Suppliers)**
- Fiche fournisseur
- Commandes
- Livraisons
- Factures
- Suivi des délais

### 3. **Gestion des Prescriptions**
- Patients
- Médecins
- Prescriptions
- Délivrance
- Alertes d'interactions

### 4. **Point de Vente (POS)**
- Caisse
- Ventes
- Paiements (espèces, carte, mobile)
- Facturation
- Retours

### 5. **Notifications & Alertes**
- Stock bas
- Produits périmés
- Dates d'expiration proches
- Commandes à traiter

### 6. **Rapports & Statistiques**
- Ventes
- Stocks
- Financier
- Export Excel/PDF

## 📦 TECHNOLOGIES UTILISÉES

- **Backend**: Symfony 7.x
- **Base de données**: MySQL/PostgreSQL
- **ORM**: Doctrine
- **Front-end**: Twig + Bootstrap 5 + JavaScript/jQuery
- **Assets**: Webpack Encore
- **API**: API Platform (optionnel)
- **Jobs async**: Symfony Messenger
- **Tests**: PHPUnit
- **Qualité code**: PHP CS Fixer, PHPStan

## 🚀 PROCHAINES ÉTAPES

Je vais maintenant créer le contenu détaillé de chaque fichier important.
