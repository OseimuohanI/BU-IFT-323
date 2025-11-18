<?php

if (php_sapi_name() === 'cli') {
    echo "Open the application in your browser at the 'public/' folder.\n";
    exit;
}

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($scriptName, '/public/') !== false || strpos($requestUri, '/public/') !== false) {
    $publicIndex = __DIR__ . '/public/index.php';
    if (file_exists($publicIndex)) {
        require $publicIndex;
        exit;
    }
    http_response_code(500);
    echo "Public index not found. Please ensure public/index.php exists.";
    exit;
}

$rootDir = rtrim(dirname($scriptName), '/');
$destPath = ($rootDir === '' ? '' : $rootDir) . '/public/';
$query = $_SERVER['QUERY_STRING'] ?? '';
if ($query !== '') {
    $destPath .= '?' . $query;
}

header('Location: ' . $destPath, true, 302);
exit;