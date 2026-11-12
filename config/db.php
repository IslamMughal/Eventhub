<?php
// ====== SETTINGS ======
if (!defined('BASE_URL')) {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($script, '/event_hub/') !== false || strpos($uri, '/event_hub/') !== false) {
        define('BASE_URL', '/event_hub/');
    } else {
        define('BASE_URL', '/');
    }
}
define('SITE_NAME', 'Eventify');
define('SITE_EMAIL', 'info@eventify.com');            // footer me dikhta hai
define('SITE_PHONE', '+92 300 0000000');
define('SITE_ADDRESS', 'Gujranwala, Punjab, Pakistan');

$pdo = new PDO('mysql:host=localhost;dbname=citypulse_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
