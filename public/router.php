<?php
// Router file for PHP built-in server
// Simulates Apache mod_rewrite behavior

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the file exists, serve it directly (CSS, JS, images, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Otherwise, route everything through index.php (Symfony front controller)
require __DIR__ . '/index.php';
