<?php
/**
 * Front controller — routes all public requests.
 */


require_once __DIR__ . '/includes/init.php';

// --- Route parsing ---
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Strip the base path (folder where app lives)
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$base   = rtrim(str_replace('\\', '/', dirname($script)), '/');

if ($base !== '' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}

$uri = trim($uri, '/');
$segments = $uri === '' ? [] : explode('/', $uri);

$route = $segments[0] ?? 'home';
$param = $segments[1] ?? null;

// --- Route table ---
$routes = [
    ''           => 'home.php',
    'home'       => 'home.php',
    'properties' => 'properties.php',
    'property'   => 'property.php',
    'about'      => 'about.php',
    'contact'    => 'contact.php',
    'login'      => 'login.php',
];

// Special: /index.php should just be the home page
if ($route === 'index.php') {
    $route = 'home';
}

$file = $routes[$route] ?? null;

if (!$file || !file_exists(__DIR__ . '/pages/' . $file)) {
    http_response_code(404);
    require __DIR__ . '/pages/404.php';
    exit;
}

require __DIR__ . '/pages/' . $file;