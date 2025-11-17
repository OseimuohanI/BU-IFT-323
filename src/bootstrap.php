<?php
$config = require __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// load composer autoloader if present
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
}

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require $file;
});

use Model\Database;

// initialize DB connection singleton
Database::init($config['db']);
define('BASE_URL', rtrim($config['base_url'], '/'));