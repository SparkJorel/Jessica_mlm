# Roadmap de correction des bugs — Jessica MLM (JTWC)
# Branche : fix/bugs-workflows

## Phase 1 — CRITIQUES (crashes immediats en production)

- [x] BUG-01 | PaymentController.php       | Variable $file non definie dans sendSMSConfirmation()
- [x] BUG-02 | PaymentController.php       | notifyCommandProduct() ne retourne rien si montant incorrect
- [x] BUG-03 | PaymentController.php       | mb_convert_encoding('OLD-ENCODING') invalide crashe le webhook
- [x] BUG-04 | GenerationalBonus.php       | $users[$i-1] devrait etre $users[$i] -> crash bonus generationnel
- [x] BUG-05 | UserHandler.php             | Bouton submit_and_paid retourne null -> crash inscription rapide
- [x] BUG-06 | ComputeDateOperation.php    | Mutation DateTime::add()/sub() corrompt les dates du cycle

## Phase 2 — MAJEURS (comportements incorrects sans crash)

- [x] BUG-07 | ReferralBonusSubscriber.php + PaymentController.php | Double bonus parrainage possible
- [x] BUG-08 | CloseCycleController.php    | Double cloture possible -> bonus doubles en base
- [x] BUG-09 | CloseCycleController.php    | autoSave jamais mis a true -> rapport toujours recalcule
- [x] BUG-10 | BonusBinary.php             | handleSavedCarryOver : $results['side'] recoit un float
- [x] BUG-11 | BonusBinary.php             | personalNetworkActivity : achats du mauvais utilisateur
- [x] BUG-12 | CloseCycle.php + IndirectBonusService.php | Dernier groupe utilisateur parfois manquant dans rapport
- [x] BUG-13 | UserHandler.php             | MembershipSubscription sans createdBy dans saveEntity()
- [x] BUG-14 | UserController.php          | uplineAutocomplete inaccessible aux admins

## Phase 3 — MINEURS / Qualite

- [x] BUG-15 | PaymentController.php       | Log sensible ecrit avant verification du hash
- [x] BUG-16 | UserPaidBonusController.php | Pas de validation des parametres POST (null crash + route non validee)
- [x] BUG-17 | BonusBinary.php             | Filtre recap binaire trop restrictif (AND au lieu de OR)
- [x] BUG-18 | Plusieurs fichiers          | Logs de debug (dump/dd/print_r/die) laisses en production
- [x] BUG-19 | UserCommandsController.php  | Route save_commands accepte GET pour une operation d'ecriture
- [x] BUG-20 | UserHandler.php             | updateUserProfile active le compte sans verification de paiement

## Phase 4 — MLM Business Logic (activation manuelle admin)

Regle metier : activation manuelle admin = paiement manuel
- [x] MLM-A  | UserHandler.php             | activate() : marquer souscription paid + paidAt + startedAt + code distributeur + bonus parrainage (idempotent)
- [x] MLM-B  | UserCommandsHandler.php     | setPaidToTrue() : protection idempotence ajoutee
- [x] MLM-C  | PaymentController.php       | Webhook Dohone : protection anti-replay via isPaid() confirmee presente
