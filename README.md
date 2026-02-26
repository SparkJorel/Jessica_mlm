# Jessica MLM (JTWC)

Plateforme de gestion MLM (Multi-Level Marketing) complète, développée avec Symfony 4.3. Le système gère l'ensemble du cycle de vie d'un réseau de distribution : inscription des membres, gestion des produits, commandes, arbre généalogique binaire, calcul automatique des commissions et bonus, et paiement via Dohone (Mobile Money).

---

## Stack technique

| Composant         | Version / Détail                      |
|-------------------|---------------------------------------|
| **Framework**     | Symfony 4.3                           |
| **PHP**           | ^7.2.3 (recommandé : 7.4)            |
| **Base de données** | MySQL / MariaDB                     |
| **ORM**           | Doctrine ORM 2.6+                     |
| **Cache**         | Redis (Predis) / Filesystem en dev    |
| **Frontend**      | Webpack Encore, jQuery, DataTables    |
| **Paiement**      | Dohone (Mobile Money Cameroun)        |
| **Images**        | VichUploaderBundle + LiipImagineBundle|
| **PDF**           | KnpSnappyBundle                       |
| **Éditeur WYSIWYG** | FOSCKEditorBundle                  |
| **API**           | API Platform                          |
| **Sérialisation** | JMS Serializer                        |
| **Pagination**    | KnpPaginatorBundle                    |

---

## Architecture du projet

```
src/
├── AbstractModel/          # Classes abstraites de base
├── Command/                # Commandes console (ex: suppression paniers expirés)
├── Controller/
│   ├── Back/               # Back-office (administration)
│   │   ├── WebController/  # 39 contrôleurs (gestion produits, grades, bonus, cycles...)
│   │   └── RestController/ # API REST back-office
│   ├── Front/              # Front-office (utilisateurs/distributeurs)
│   │   ├── WebController/  # Interface utilisateur
│   │   └── RestController/ # API REST front
│   └── SecurityController.php  # Authentification (login/logout)
├── DTO/                    # Data Transfer Objects
├── Entity/                 # 53 entités Doctrine
├── EntityListener/         # Listeners Doctrine sur les entités
├── Event/                  # 13 événements métier
├── EventListener/          # 13 subscribers (réactions aux événements)
├── Exception/              # Exceptions personnalisées
├── Factory/                # Factories
├── Form/                   # 51 types de formulaires
├── Hydrators/              # Hydratation personnalisée Doctrine
├── Manager/                # CartUserLoggedManager
├── Message/                # Messages asynchrones (Messenger)
├── MessageHandler/         # Handlers des messages
├── Migrations/             # 47 migrations Doctrine
├── Repository/             # Repositories Doctrine
├── Security/               # Authentification Guard + UserChecker
├── Services/               # 34 services métier
│   ├── Payment/            # Intégration Dohone (PayIn)
│   ├── ModelHandlers/      # Handlers de modèles
│   ├── BonusBinary.php     # Calcul bonus binaire
│   ├── BonusSummary.php    # Récapitulatif des bonus
│   ├── CloseCycle.php      # Clôture de cycle
│   ├── GenerationalBonus.php   # Bonus générationnel
│   ├── IndirectBonusService.php # Bonus indirect
│   ├── MonthlyTurnOver.php # Chiffre d'affaires mensuel
│   ├── UserBinaryService.php   # Gestion arbre binaire
│   └── ...
├── Storage/                # Gestion du stockage fichiers
├── Validator/              # Validateurs personnalisés
└── Voter/                  # AddMemberVoter (contrôle d'accès)
```

---

## Entités (53)

### Utilisateurs & Réseau
| Entité | Description |
|--------|-------------|
| `User` | Utilisateur/distributeur (identifiant, parrainage, arbre binaire) |
| `AddressUser` | Adresse de l'utilisateur |
| `UserBinaryCycle` | Position dans l'arbre binaire par cycle |
| `UserGrade` | Grade atteint par l'utilisateur |
| `UserMonthCarryOver` | Report mensuel (carry-over) |
| `SearchUser` | Recherche d'utilisateurs |

### Produits & Commandes
| Entité | Description |
|--------|-------------|
| `Product` | Produit du catalogue |
| `ProductClientPrice` | Prix client d'un produit |
| `ProductDistributorPrice` | Prix distributeur d'un produit |
| `ProductCote` | Cotation/valeur d'un produit |
| `ProductSV` | Volume SV (Sales Volume) d'un produit |
| `CommandProducts` | Produits dans une commande |
| `UserCommands` | Commandes utilisateur |
| `UserCommandPackPromo` | Commandes de packs promo |
| `PurchaseSummary` | Résumé des achats |
| `FiltreProduct` | Filtrage produits |

### Adhésions & Abonnements
| Entité | Description |
|--------|-------------|
| `Membership` | Type d'adhésion |
| `MembershipSubscription` | Souscription à une adhésion |
| `MembershipProduct` | Produits inclus dans une adhésion |
| `MembershipProducts` | Association membership-produits |
| `MembershipCost` | Coût d'une adhésion |
| `MembershipSV` | Volume SV d'une adhésion |
| `MembershipBonusPourcentage` | Pourcentage de bonus par adhésion |
| `CompositionMembershipProductName` | Composition nommée d'une adhésion |

### Grades & Niveaux
| Entité | Description |
|--------|-------------|
| `Grade` | Grade dans le réseau MLM |
| `GradeLevel` | Niveau de grade |
| `GradeBG` | Grade Business Group |
| `GradeSV` | Grade basé sur le Sales Volume |
| `GradeMaintenance` | Conditions de maintien de grade |

### Bonus & Commissions
| Entité | Description |
|--------|-------------|
| `SponsoringBonus` | Bonus de parrainage |
| `BonusSpecial` | Bonus spéciaux |
| `CollectionBonusSpecial` | Collection de bonus spéciaux |
| `PromoBonusSpecial` | Bonus promotionnels |
| `CommissionIndirectBonus` | Commission sur bonus indirect |
| `IndirectBonusMembership` | Bonus indirect sur adhésion |
| `IndirectBonusProduct` | Bonus indirect sur produit |
| `LevelBonusGenerationnel` | Bonus générationnel par niveau |
| `UserBonusSpecial` | Bonus spéciaux attribués à un utilisateur |
| `UserPaidBonus` | Bonus payés à un utilisateur |
| `SummaryCommission` | Résumé des commissions |

### Cycles & Configuration
| Entité | Description |
|--------|-------------|
| `Cycle` | Cycle commercial (période) |
| `FiltreCycle` | Filtrage par cycle |
| `ParameterConfig` | Paramètres de configuration globaux |
| `AnalyseFonctionnelleSystematique` | Analyse fonctionnelle du système |

### Packs & Promotions
| Entité | Description |
|--------|-------------|
| `TVCPack` | Packs TVC |
| `PackPromo` | Packs promotionnels |
| `PromoPackProduct` | Produits dans un pack promo |
| `UserPackComposition` | Composition de pack d'un utilisateur |

### Services & Notifications
| Entité | Description |
|--------|-------------|
| `Service` | Services proposés |
| `PrestationService` | Prestations de service |
| `Notification` | Notifications système |
| `UpdateCartProductNotification` | Notifications de mise à jour panier |
| `SendSMSPayment` | Envoi SMS de paiement |

---

## Événements métier (Event-Driven)

Le système utilise une architecture événementielle pour découpler la logique métier :

| Événement | Description |
|-----------|-------------|
| `MembershipSubscriptionActivatedEvent` | Déclenchée à l'activation d'un abonnement |
| `ReferralBonusEvent` | Calcul du bonus de parrainage |
| `UserBinaryCreatedEvent` | Placement dans l'arbre binaire |
| `UserGradeReachedEvent` | Atteinte d'un nouveau grade |
| `ChangeGradeEvent` | Changement de grade |
| `ActivateUpgradeEvent` | Activation d'un upgrade |
| `CodeCommandeEvent` | Génération code commande |
| `PVCEvent` | Événement PVC (Point Volume Commissionable) |
| `PromotionTriggeredEvent` | Déclenchement d'une promotion |
| `ServiceMlmItemActivatedEvent` | Activation d'un item MLM |
| `UplineSearchEvent` | Recherche dans la ligne ascendante |
| `PrestationServiceActivatedEvent` | Activation d'une prestation |
| `AddUserGradeEvent` | Ajout d'un grade utilisateur |

### Subscribers

| Subscriber | Rôle |
|------------|------|
| `ActivateMembershipSubscriptionSubscriber` | Active les abonnements |
| `ActivateUpgradeSubscriber` | Gère les upgrades |
| `AddNewProductSubscriber` | Réagit à l'ajout de produit |
| `AddNewUserGradeSubscriber` | Attribution de grade |
| `ChangeGradeSubscriber` | Logique de changement de grade |
| `CloseMembershipSubscriptionSubscriber` | Clôture d'abonnement |
| `ComputeDateOperationSubscriber` | Calcul des dates d'opération |
| `CycleSubscriber` | Gestion des cycles |
| `GenerateCodeSubscriber` | Génération de codes |
| `MemberSubscriptionSubscriber` | Souscription membre |
| `ReferralBonusSubscriber` | Calcul bonus parrainage |
| `RemoveFileEventSubscriber` | Suppression de fichiers |
| `UserGradeReachedSubscriber` | Réaction à l'atteinte d'un grade |

---

## Sécurité & Authentification

- **Guard Authentication** : `LoginFormAuthenticator` pour l'authentification par formulaire
- **UserChecker** : Vérifie que le compte est activé, non supprimé et non expiré avant connexion
- **Encodage des mots de passe** :
  - Utilisateurs : bcrypt (coût 15)
  - Administrateurs : argon2i
- **Voter** : `AddMemberVoter` pour le contrôle d'accès à l'ajout de membres
- **Sessions** : Stockées en base de données via `PdoSessionHandler`

---

## Paiement - Dohone

Intégration avec la passerelle de paiement **Dohone** (Mobile Money - Cameroun) :

- `PayInInterface` : Interface de paiement
- `PayInContext` : Contexte de paiement (pattern Strategy)
- `PayInWithDohone` : Implémentation Dohone
- Support des paiements par Mobile Money (Orange Money, MTN Mobile Money)

---

## Formulaires (51)

Le système comprend 51 types de formulaires Symfony couvrant :
- Gestion des utilisateurs (`UserType`, `AddNewUserType`, `UserProfileType`)
- Produits et prix (`ProductType`, `ProductClientPriceType`, `ProductDistributorPriceType`)
- Adhésions (`MembershipType`, `MembershipSubscriptionType`, `MembershipCostType`)
- Commandes et panier (`CartType`, `CartItemType`, `AddToCartType`, `CommandProductsType`)
- Grades (`GradeType`, `GradeBGType`, `GradeLevelType`, `GradeSVType`, `GradeMaintenanceType`)
- Bonus (`BonusSpecialType`, `BonusGenerationnelLevelType`, `PromoBonusSpecialType`)
- Configuration (`ParameterConfigType`, `CycleType`, `FiltreCycleType`)
- Sécurité (`ChangePasswordType`, `ChangeUsernameType`)

---

## Back-office (39 contrôleurs)

L'administration permet de gérer :

| Module | Contrôleur |
|--------|-----------|
| Utilisateurs | `UserController`, `UserGradeController`, `UserCommandsController`, `UserPaidBonusController` |
| Produits | `ProductController`, `ProductClientPriceController`, `ProductDistributorPriceController`, `ProductCoteController`, `ProductSVController` |
| Adhésions | `MembershipController`, `MembershipProductController`, `MembershipSubscriptionController`, `MembershipCostController`, `MembershipSVController`, `MembershipBonusPourcentageController`, `CompositionMembershipProductNameController` |
| Grades | `GradeController`, `GradeBGController`, `GradeLevelController`, `GradeMaintenanceController`, `GradeSVController` |
| Bonus & Commissions | `BonusController`, `SponsoringBonusController`, `IndirectBonusController`, `IndirectBonusMembershipController`, `IndirectBonusProductController`, `LevelBonusGenerationnelController`, `PromoBonusSpecialController` |
| Cycles | `CycleController`, `CloseCycleController`, `TurnOverController` |
| Paiements | `PaymentController`, `CartNotificationController` |
| Packs & Promos | `PackPromoController`, `TVCPackController` |
| Services | `ServiceController`, `PrestationServiceController` |
| Configuration | `ParameterConfigController`, `AnalyseFonctionnelleSystematiqueController` |

---

## Prérequis

- **PHP** 7.4.x
- **MySQL** 5.7+ ou **MariaDB** 10.4+
- **Composer** 2.x
- **Node.js** + npm (pour Webpack Encore, si besoin de recompiler les assets)
- **Redis** (optionnel, remplacé par filesystem en dev)

---

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/SparkJorel/Jessica_mlm.git
cd Jessica_mlm
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Copier et adapter le fichier `.env` :

```dotenv
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL=mysql://root:@127.0.0.1:3306/db_jessica_mlm?serverVersion=mariadb-10.4.32
REDIS_URL=redis://127.0.0.1:6379
```

### 4. Créer et importer la base de données

```bash
php bin/console doctrine:database:create
mysql -u root db_jessica_mlm < chemin/vers/dump.sql
```

### 5. Compiler les assets (si nécessaire)

```bash
npm install
npm run dev
```

### 6. Lancer le serveur

```bash
php -S localhost:8080 -t public public/router.php
```

Le site est accessible sur `http://localhost:8080`.

---

## Structure des dossiers

```
Jessica_mlm/
├── bin/                    # Console Symfony
├── config/                 # Configuration (packages, routes, services)
│   ├── packages/           # Configuration des bundles
│   │   ├── dev/            # Config spécifique dev
│   │   └── prod/           # Config spécifique prod
│   ├── routes/             # Définition des routes
│   └── services.yaml       # Déclaration des services
├── public/                 # Point d'entrée web
│   ├── index.php           # Front controller
│   ├── router.php          # Router pour le serveur PHP intégré
│   ├── build/              # Assets compilés (JS, CSS)
│   └── uploads/            # Fichiers uploadés
├── src/                    # Code source de l'application
├── templates/              # Templates Twig
│   ├── back/               # Templates back-office
│   ├── form/               # Templates de formulaires
│   ├── security/           # Templates login/sécurité
│   └── base.html.twig      # Layout principal
├── tests/                  # Tests
├── translations/           # Fichiers de traduction
├── var/                    # Cache et logs (ignoré par git)
├── vendor/                 # Dépendances (ignoré par git)
├── composer.json           # Dépendances PHP
└── webpack.config.js       # Configuration Webpack Encore
```

---

## Licence

Propriétaire - Tous droits réservés.
