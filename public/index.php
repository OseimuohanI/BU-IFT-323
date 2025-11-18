<?php
require __DIR__ . '/../src/bootstrap.php';

use Service\Auth;

$requestedController = strtolower($_GET['controller'] ?? 'student');
$requestedAction = strtolower($_GET['action'] ?? 'index');

if (!Auth::check() && !($requestedController === 'auth' && $requestedAction === 'login')) {
    header('Location: ' . BASE_URL . '/?controller=auth&action=login&redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$controllerName = ucfirst(strtolower($_GET['controller'] ?? 'student')) . 'Controller';
$action = $_GET['action'] ?? 'index';

$controllerFile = __DIR__ . '/../src/Controller/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "Controller not found.";
    exit;
}

require_once $controllerFile;
$cn = 'Controller\\' . str_replace('.php', '', $controllerName);
$controller = new $cn();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action not found.";
    exit;
}

$controller->$action();