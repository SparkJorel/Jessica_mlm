<?php
/**
 * Test all GET endpoints after login - using exact router paths
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = tempnam(sys_get_temp_dir(), 'jtwc_cookie_');

// ---- LOGIN ----
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/login",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_TIMEOUT => 30,
]);
$html = curl_exec($ch);
preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $html, $m);
$csrf = $m[1] ?? '';

curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/login",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_username' => 'tiomelajorel@gmail.com',
        '_password' => 'jorel5168',
        '_csrf_token' => $csrf,
    ]),
    CURLOPT_FOLLOWLOCATION => false,
]);
curl_exec($ch);
$location = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

if (strpos($location, '/login') !== false) {
    echo "ECHEC LOGIN - Abandon\n";
    exit(1);
}
echo "LOGIN OK -> $location\n\n";

// ---- ENDPOINTS (exact paths from debug:router) ----
$endpoints = [
    // Auth & Navigation
    ['/', 'Redirect accueil'],
    ['/admin/genealogy', 'Arbre genealogique'],
    ['/admin/change/password', 'Changement mot de passe'],
    ['/admin/change/username', 'Changement username'],
    ['/admin/new-user/update', 'Nouveau user update'],
    ['/admin/user/update/details', 'Update details user'],
    ['/admin/testjson', 'Test JSON'],

    // Users
    ['/admin/users', 'Liste utilisateurs'],
    ['/admin/users/all', 'Tous les utilisateurs'],
    ['/admin/user/tiomelajorel@gmail.com', 'Profil utilisateur'],
    ['/admin/users/grade/list', 'Grades utilisateurs'],
    ['/admin/personal/grade/list', 'Grade personnel'],
    ['/admin/users/bonus/paid', 'Bonus payes'],
    ['/admin/users/generate-code', 'Generer codes'],
    ['/admin/recover/genealogy', 'Recuperer genealogie'],

    // Bonus
    ['/admin/bonus/sponsorship', 'Bonus parrainage'],
    ['/admin/bonus/personal-purchase', 'Bonus achat personnel'],
    ['/admin/bonus/binary', 'Bonus binaire'],
    ['/admin/bonus/generational', 'Bonus generationnel'],
    ['/admin/bonus/sponsoring/cycle', 'Bonus sponsoring cycle'],
    ['/admin/bonus/personal-purchase/cycle', 'Bonus achat cycle'],
    ['/admin/bonus/binary/cycle', 'Bonus binaire cycle'],
    ['/admin/bonus-sponsoring/paid', 'Statut bonus sponsoring'],
    ['/admin/user-indirect-bonus', 'Bonus indirect user'],
    ['/admin/network-indirect-bonus', 'Bonus indirect reseau'],

    // Products
    ['/admin/products', 'Liste produits'],
    ['/admin/products/all', 'Tous les produits'],
    ['/admin/products/new', 'Nouveau produit'],
    ['/admin/product-cotes', 'Cotes produits'],
    ['/admin/products-sv', 'SV produits'],
    ['/admin/client-prices', 'Prix clients'],
    ['/admin/distributor-prices', 'Prix distributeurs'],

    // Memberships / Packs
    ['/admin/memberships', 'Liste packs'],
    ['/admin/packs/view/all', 'Voir tous les packs'],
    ['/admin/memberships/new', 'Nouveau pack'],
    ['/admin/membership-costs', 'Couts packs'],
    ['/admin/membership-bonus', 'Pourcentages bonus'],
    ['/admin/membership-svs', 'SV packs'],
    ['/admin/tvcs-pack', 'TVC Packs'],

    // Subscriptions
    ['/admin/membership-subscriptions/all', 'Tous abonnements'],
    ['/admin/membership-subscriptions/personal', 'Mes abonnements'],
    ['/admin/membership-subscriptions/upgrade', 'Upgrade abonnement'],
    ['/admin/unpaid-subscription-command', 'Abonnements impayes'],
    ['/admin/membership-subscription/cart/summary/', 'Resume panier abo'],

    // Commands
    ['/admin/commands', 'Commandes utilisateur'],
    ['/admin/commands/personal', 'Mes commandes'],
    ['/admin/user-commands/all', 'Toutes les commandes'],
    ['/admin/commands/new', 'Nouvelle commande'],
    ['/admin/cart/view', 'Panier'],
    ['/admin/commands/save', 'Sauvegarder commandes'],
    ['/admin/unpaid-command', 'Commandes impayees'],

    // Cycles
    ['/admin/cycles', 'Liste cycles'],
    ['/admin/cycles/create', 'Nouveau cycle'],
    ['/admin/cycle/list', 'Fermeture cycles'],

    // Services
    ['/admin/services', 'Services'],

    // Grades
    ['/admin/grades', 'Grades'],
    ['/admin/grades/create', 'Nouveau grade'],
    ['/admin/grade-levels/list', 'Niveaux grades'],
    ['/admin/grade-bgs/list', 'Grades BG'],
    ['/admin/grade-svs/list', 'Grades SV'],
    ['/admin/grade-maintenances/list', 'Maintenance grades'],

    // Indirect Bonus config
    ['/admin/indirect-bonus-memberships', 'Bonus indirect packs'],
    ['/admin/indirect-bonus-products', 'Bonus indirect produits'],

    // Level Bonus
    ['/admin/level-bonus-generationnels', 'Bonus generationnels'],

    // Promotions
    ['/admin/pack-promos', 'Promotions packs'],
    ['/admin/promo-bonus-special', 'Bonus speciaux promo'],

    // Config
    ['/admin/param-configs', 'Parametres config'],
    ['/admin/analyse-fonctionnelle-systematiques', 'Analyses fonctionnelles'],
    ['/admin/pack-name-composition', 'Composition packs'],

    // Turnover
    ['/admin/admin/turn-over/monthly', 'Chiffre affaires mensuel'],

    // Recap
    ['/admin/view/recap', 'Recap'],
    ['/admin/view/own/recap', 'Mon recap'],

    // Payment callbacks (public)
    ['/admin/payment-product/success', 'Paiement produit succes'],
    ['/admin/payment-product/fail', 'Paiement produit echec'],
    ['/admin/payment-subscription/success', 'Paiement abo succes'],
    ['/admin/payment-subscription/fail', 'Paiement abo echec'],
];

// ---- TEST EACH ENDPOINT ----
$results = ['ok' => [], 'redirect' => [], 'forbidden' => [], 'not_found' => [], 'server_error' => []];
$total = count($endpoints);
$i = 0;

foreach ($endpoints as [$path, $label]) {
    $i++;
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl$path",
        CURLOPT_POST => false,
        CURLOPT_HTTPGET => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
    ]);
    $body = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

    $icon = '';
    $detail = '';

    if ($httpCode === 200) {
        $icon = 'OK';
        $results['ok'][] = $label;
    } elseif ($httpCode >= 300 && $httpCode < 400) {
        $shortRedirect = str_replace($baseUrl, '', $redirectUrl);
        $icon = "-> $shortRedirect";
        $results['redirect'][] = "$label ($path -> $shortRedirect)";
    } elseif ($httpCode === 403) {
        $icon = 'INTERDIT';
        $results['forbidden'][] = "$label ($path)";
    } elseif ($httpCode === 404) {
        $icon = '404';
        $results['not_found'][] = "$label ($path)";
    } elseif ($httpCode >= 500) {
        $errorMsg = '';
        if (preg_match('/exception-message[^>]*>([^<]+)/s', $body, $em)) {
            $errorMsg = html_entity_decode(trim($em[1]));
        }
        $icon = "ERREUR 500";
        $detail = $errorMsg ? " | $errorMsg" : '';
        $results['server_error'][] = "$label ($path) - $errorMsg";
    } else {
        $icon = "HTTP $httpCode";
    }

    printf("[%2d/%d] %3d  %-50s %s%s\n", $i, $total, $httpCode, $label, $icon, $detail);
}

curl_close($ch);
unlink($cookieFile);

// ---- SUMMARY ----
echo "\n" . str_repeat('=', 80) . "\n";
echo "RESUME\n";
echo str_repeat('=', 80) . "\n";
$okCount = count($results['ok']);
$redirectCount = count($results['redirect']);
$forbiddenCount = count($results['forbidden']);
$notFoundCount = count($results['not_found']);
$errorCount = count($results['server_error']);

echo "  OK (200)         : $okCount\n";
echo "  Redirections     : $redirectCount\n";
echo "  Interdit (403)   : $forbiddenCount\n";
echo "  Non trouve (404) : $notFoundCount\n";
echo "  Erreurs (500)    : $errorCount\n";
echo "  TOTAL            : $total\n";

if ($forbiddenCount > 0) {
    echo "\n--- ACCES INTERDIT (403) - normal si role insuffisant ---\n";
    foreach ($results['forbidden'] as $e) echo "  - $e\n";
}
if ($errorCount > 0) {
    echo "\n--- ERREURS SERVEUR (500) - A CORRIGER ---\n";
    foreach ($results['server_error'] as $e) echo "  * $e\n";
}
if ($notFoundCount > 0) {
    echo "\n--- NON TROUVE (404) ---\n";
    foreach ($results['not_found'] as $e) echo "  ? $e\n";
}
echo "\n";
