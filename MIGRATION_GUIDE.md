# Plan de Migration : Jessica MLM (JTWC)
## Symfony 4.3 / PHP 7.4 -> Symfony 6.4 LTS / PHP 8.2

---

## Contexte

Le projet Jessica MLM est une application de marketing multi-niveaux (MLM) en production sur Symfony 4.3 avec PHP 7.4, deux versions en fin de vie (EOL) ne recevant plus de correctifs de securite. L'objectif est de migrer progressivement vers **Symfony 6.4 LTS** (supporte jusqu'en nov. 2027) et **PHP 8.2**, tout en conservant toutes les fonctionnalites operationnelles (paiements Dohone, arbre genealogique binaire, calculs de bonus, generation PDF).

## Inventaire du projet

| Categorie | Nombre | Emplacement |
|---|---|---|
| Entites Doctrine | 53 | `src/Entity/` |
| Controllers (Back Web) | 39 | `src/Controller/Back/WebController/` |
| Controllers (Security) | 1 | `src/Controller/SecurityController.php` |
| Form Types | 51+ | `src/Form/` |
| Repositories | 45 | `src/Repository/` |
| Services | 34+ | `src/Services/` |
| Event Listeners/Subscribers | 13 | `src/EventListener/` |
| Entity Listeners | 5 | `src/EntityListener/` |
| Events | 13 | `src/Event/` |
| Voters | 1 | `src/Voter/` |
| Custom Validators | 7 paires | `src/Validator/Constraints/` |
| Templates Twig | 124 | `templates/` |
| Annotations `@Route` | 209 | 40 fichiers controller |
| Annotations `@Security` | 116 | 36 fichiers controller |
| Webpack Encore entries | 19 | `webpack.config.js` |
| Migrations DB | 48 | `migrations/` |

---

## PHASE 0 : Preparation (Sauvegardes, Tests de fumee) - TERMINEE

- [x] MIGRATION_GUIDE.md cree
- [x] Tag `v1.0-pre-migration` pousse
- [x] Branche `migration/symfony-6.4` creee
- [x] Tests de fumee crees
- [x] Health check script pret

## PHASE 1 : Symfony 4.3 -> 4.4 LTS - TERMINEE (commit `97bd717`)

- [x] `composer update` reussi
- [x] `cache:clear` reussi
- [x] Application fonctionnelle
- [x] Commit

## PHASE 2 : Compatibilite PHP 8.2 - TERMINEE (commit `9906378`)

- [x] Application demarre avec PHP 8.2
- [x] Aucune erreur `Serializable` deprecated
- [x] Commit

## PHASE 3 : Symfony 4.4 -> 5.4 LTS - TERMINEE (commit `9906378`)

- [x] SwiftMailer -> Symfony Mailer
- [x] LoginFormAuthenticator reecrit (AbstractLoginFormAuthenticator)
- [x] `UserPasswordEncoderInterface` -> `UserPasswordHasherInterface`
- [x] `security.yaml` refactorise (enable_authenticator_manager)
- [x] `bootstrap.php` supprime, runtime installe
- [x] `doctrine/cache` supprime
- [x] `FlashBagInterface` -> `RequestStack`
- [x] Application fonctionnelle
- [x] Commit

## PHASE 4 : Symfony 5.4 -> 6.4 LTS - TERMINEE (commits `7fa0d3b`, `23b8989`)

- [x] `sensio/framework-extra-bundle` supprime
- [x] 209 `@Route` -> `#[Route]`
- [x] 116 `@Security` -> `#[IsGranted]`
- [x] 53 entites : annotations -> attributs PHP 8
- [x] Gedmo Tree : annotations -> attributs
- [x] Roles : `simple_array` -> `json` + migration SQL executee
- [x] `api-platform` supprime (non utilise dans le code source)
- [x] `nelmio/cors-bundle` ajoute comme dependance explicite
- [x] `doctrine.yaml` : mapping type `annotation` -> `attribute`
- [x] `framework.yaml` : `handle_all_throwables: true`, `http_method_override: false`
- [x] `services.yaml` : `PdoSessionHandler` utilise DSN string (lock_mode: 0)
- [x] `User` implemente `PasswordAuthenticatedUserInterface`
- [x] Sessions fichier en dev (PdoSessionHandler incompatible avec serveur PHP built-in)
- [x] `.env` nettoye (doublons Flex supprimes)
- [x] `ProductClientPrice` mapping `inversedBy` corrige
- [x] Migrations deplacees vers `migrations/`
- [x] `composer.json` : tous les `symfony/*` en `6.4.*`
- [x] `composer.lock` regenere compatible PHP 8.2
- [x] Login fonctionne (teste avec succes)
- [x] `cache:clear` OK
- [x] `lint:container` OK
- [x] `doctrine:mapping:info` : 47 entites OK
- [x] Commits

## PHASE 5 : Modernisation des Dependances - TERMINEE

- [x] Dependances majeures a jour (Symfony 6.4, Doctrine ORM 2.14+)
- [x] Migrations dans `migrations/`
- [x] `prod/doctrine.yaml` : cache DoctrineProvider -> type pool (Symfony 6 compatible)
- [ ] Deprecation warnings a corriger (Vich annotations, Constraint::getTargets, etc.)

## PHASE 6 : Docker, CI/CD, Infrastructure - TERMINEE

- [x] Dockerfile PHP 8.2-apache
  - Extensions : pdo_mysql, intl, gd, opcache, zip, mbstring, sodium, bcmath, redis
  - Composer install en mode prod (--no-dev --optimize-autoloader)
  - `.env` modifie en build pour APP_ENV=prod
- [x] docker-compose.yml
  - `jessica_web` : PHP 8.2 + Apache (port 8080)
  - `jessica_db` : MariaDB 10.4
  - `jessica_redis` : Redis 7 Alpine
  - Volumes : db_data, redis_data, uploads
- [x] `docker/entrypoint.sh` automatise :
  - Ecriture `.env.local` depuis les variables Docker
  - Attente DB (30 tentatives max)
  - Migration one-shot roles CSV -> JSON (marqueur `var/.migration_v6_done`)
  - `doctrine:schema:update --force`
  - `doctrine:migrations:migrate`
  - Cache clear + warmup
  - Fix permissions www-data
- [x] CI/CD GitHub Actions (`.github/workflows/deploy.yml`)
  - Deploiement auto sur push `main`
  - SSH → pull → build → down → up → verify
- [x] Nginx reverse proxy (HTTPS Let's Encrypt)
- [x] Deploiement production reussi
  - URL : https://jessica-mlm.duckdns.org
  - Serveur : 83.228.193.57 (user ubuntu)
  - 410 roles utilisateurs migres automatiquement
  - Schema DB mis a jour
  - Page login : 200 OK

---

## Risques et Mitigations

| Risque | Impact | Statut |
|--------|--------|--------|
| Migration roles `simple_array` -> `json` | CRITIQUE | FAIT - 410 users migres (auto entrypoint) |
| Refonte authentification Guard -> Authenticator | CRITIQUE | FAIT - Login teste OK local + prod |
| Double encoder argon2i/bcrypt -> auto | ELEVE | OK - Rehash automatique au prochain login |
| Invalidation sessions (Serializable) | MOYEN | OK - Sessions fichier en dev, PdoSessionHandler en prod |
| Migration Gedmo annotations -> attributes | ELEVE | FAIT - mapping:info OK |
| Paiements Dohone | CRITIQUE | A tester en staging |
| PdoSessionHandler + serveur PHP built-in | MOYEN | Resolu - sessions fichier en dev |
| Docker env vars non transmises a PHP/Apache | MOYEN | Resolu - .env.local genere par entrypoint |
| DoctrineProvider supprime en Symfony 6 | ELEVE | Resolu - cache type pool |
| DB MySQL 5.7 -> MariaDB 10.4 | MOYEN | Resolu - dump + restore |

---

## Architecture de deploiement

```
Internet
   |
   v
Nginx (port 80/443, HTTPS Let's Encrypt)
   |  reverse proxy
   v
Docker: jessica_web (PHP 8.2 + Apache, port 8080)
   |
   +--> Docker: jessica_db (MariaDB 10.4)
   +--> Docker: jessica_redis (Redis 7)
```

**Fichiers de configuration serveur :**
- `/etc/nginx/sites-enabled/jessica-mlm` : reverse proxy HTTPS
- `/home/ubuntu/jessica_mlm/.env.docker` : variables d'env production
- `/home/ubuntu/db_backup_before_migration.sql` : backup DB pre-migration

---

## Commandes utiles

```bash
# === Developpement local ===

# Lancer le serveur de dev
/c/xampp/php/php.exe -S localhost:8080 -t public public/router.php

# Valider le mapping Doctrine
/c/xampp/php/php.exe bin/console doctrine:mapping:info
/c/xampp/php/php.exe bin/console doctrine:schema:validate --skip-sync

# Vider le cache
/c/xampp/php/php.exe bin/console cache:clear

# Synchroniser le schema DB
/c/xampp/php/php.exe bin/console doctrine:schema:update --dump-sql
/c/xampp/php/php.exe bin/console doctrine:schema:update --force

# === Production (serveur) ===

# Connexion SSH
ssh jessica

# Deployer manuellement
cd /home/ubuntu/jessica_mlm
git pull origin main
docker compose --env-file .env.docker build --no-cache web
docker compose --env-file .env.docker up -d

# Voir les logs
docker logs jessica_web
docker logs jessica_db

# Executer une commande Symfony en prod
docker exec jessica_web php bin/console <commande> --env=prod --no-debug

# Backup DB
docker exec jessica_db mysqldump -u root -p<password> db_jessica_mlm > backup.sql

# Restaurer DB
docker exec -i jessica_db mysql -u root -p<password> db_jessica_mlm < backup.sql
```
