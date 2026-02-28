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
| Migrations DB | 48 | `src/Migrations/` |

---

## PHASE 0 : Preparation (Sauvegardes, Tests de fumee)

- [x] MIGRATION_GUIDE.md cree
- [x] Tag `v1.0-pre-migration` pousse
- [x] Branche `migration/symfony-6.4` creee
- [x] Tests de fumee crees
- [x] Health check script pret

## PHASE 1 : Symfony 4.3 -> 4.4 LTS

- [ ] `composer update` reussi
- [ ] `cache:clear` reussi
- [ ] Application fonctionnelle
- [ ] Tests de fumee passent
- [ ] Commit

## PHASE 2 : Compatibilite PHP 8.1

- [ ] Application demarre avec PHP 8.2
- [ ] Aucune erreur `Serializable` deprecated
- [ ] Tests de fumee passent
- [ ] Commit

## PHASE 3 : Symfony 4.4 -> 5.4 LTS

- [ ] `web-server-bundle` supprime
- [ ] SwiftMailer -> Symfony Mailer
- [ ] LoginFormAuthenticator reecrit
- [ ] `UserPasswordEncoderInterface` -> `UserPasswordHasherInterface`
- [ ] `User.php` refactorise
- [ ] `security.yaml` refactorise
- [ ] `Kernel.php` refactorise
- [ ] `bootstrap.php` supprime, runtime installe
- [ ] `doctrine/cache` supprime
- [ ] `FlashBagInterface` -> `RequestStack`
- [ ] Application fonctionnelle
- [ ] Commit

## PHASE 4 : Symfony 5.4 -> 6.4 LTS

- [ ] `sensio/framework-extra-bundle` supprime
- [ ] 209 `@Route` -> `#[Route]`
- [ ] 116 `@Security` -> `#[IsGranted]`
- [ ] 53 entites : annotations -> attributs PHP 8
- [ ] Roles : `simple_array` -> `json` + migration SQL
- [ ] Application fonctionnelle
- [ ] Commit

## PHASE 5 : Modernisation des Dependances

- [ ] Toutes les dependances a jour
- [ ] Migrations deplacees vers `migrations/`
- [ ] Application fonctionnelle
- [ ] Commit

## PHASE 6 : Docker, CI/CD, Infrastructure

- [ ] Dockerfile PHP 8.2
- [ ] docker-compose.yml a jour
- [ ] Build Docker reussi
- [ ] CI/CD mis a jour
- [ ] Commit

---

## Risques et Mitigations

| Risque | Impact | Mitigation |
|--------|--------|------------|
| Migration roles `simple_array` -> `json` | CRITIQUE | Tester SQL sur copie de prod |
| Refonte authentification Guard -> Authenticator | CRITIQUE | Tester avec base de prod en local |
| Double encoder argon2i/bcrypt -> auto | ELEVE | Rehash automatique au prochain login |
| Invalidation sessions (Serializable) | MOYEN | TRUNCATE sessions hors heures de pointe |
| Migration Gedmo annotations -> attributes | ELEVE | Valider `doctrine:schema:validate` |
| Paiements Dohone | CRITIQUE | Tester flux complet en staging |
