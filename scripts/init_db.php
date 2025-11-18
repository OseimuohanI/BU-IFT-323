<?php
// Initialize database if it doesn't exist and run schema + seed scripts.
// Run from CLI: php scripts\init_db.php

if (php_sapi_name() !== 'cli') {
    exit("This script must be run from the command line.\n");
}

// load config
$config = require __DIR__ . '/../config/config.php';
$dbCfg = $config['db'];

$host = $dbCfg['host'] ?? '127.0.0.1';
$charset = $dbCfg['charset'] ?? 'utf8mb4';
$dbname = $dbCfg['dbname'] ?? 'disciplinary';
$user = $dbCfg['user'] ?? 'root';
$pass = $dbCfg['pass'] ?? '';

try {
    // connect without specifying database so we can create it
    $dsn = "mysql:host={$host};charset={$charset}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected to MySQL at {$host}.\n";

    // create database if not exists
    $collation = 'utf8mb4_unicode_ci';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET {$charset} COLLATE {$collation}");
    echo "Database `{$dbname}` ensured.\n";

    // switch to the database
    $pdo->exec("USE `{$dbname}`");

    // helper to run an SQL file (naive split by semicolon)
    $runSqlFile = function(string $filePath) use ($pdo) {
        if (!file_exists($filePath)) {
            echo "File not found: {$filePath}\n";
            return;
        }
        $sql = file_get_contents($filePath);
        // remove comments and DELIMITER blocks (simple handling)
        $sql = preg_replace('/--.*\n/', "\n", $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        // split on semicolon followed by newline or end (simple)
        $parts = preg_split('/;\s*(\r?\n|$)/', $sql);
        $count = 0;
        foreach ($parts as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '') continue;
            try {
                $pdo->exec($stmt);
                $count++;
            } catch (PDOException $e) {
                // continue on error but report
                echo "Statement failed: " . substr($stmt, 0, 120) . "... \nError: " . $e->getMessage() . "\n";
            }
        }
        echo "Executed {$count} statements from {$filePath}\n";
    };

    // run schema.sql then seed.sql if present
    $schema = __DIR__ . '/../sql/schema.sql';
    $seed = __DIR__ . '/../sql/seed.sql';

    echo "Running schema...\n";
    $runSqlFile($schema);

    if (file_exists($seed)) {
        echo "Running seed data...\n";
        $runSqlFile($seed);
    } else {
        echo "No seed file found at {$seed}\n";
    }

    echo "Initialization complete.\n";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}