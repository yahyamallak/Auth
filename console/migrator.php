#!/usr/bin/env php
<?php

require_once __DIR__ . '/../src/Database.php';

use Yahya\Auth\Database;

$rootFolder = dirname(__DIR__) . '/../../../'; 

$config = include $rootFolder . 'config/config.php';
$db = new Database($config);

$command = $argv[1] ?? null;

switch ($command) {
    case 'migrate':
        echo "Running migrations...\n";
        migrate($db);
        break;
    
    case 'rollback':
        echo "Rolling back migrations (Not implemented yet).\n";
        // Implement rollback logic here
        break;
    
    default:
        echo "Unknown command. Use 'migrate' to migrate.\n";
        break;
}

function getRunMigrations($db) {
    
    $db->query("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        run_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $result = $db->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
    return $result;
}

function runMigration($db, $migration) {

    global $rootFolder;

    echo "Running migration: $migration\n";
    $sql = file_get_contents($rootFolder . "migrations/$migration");
    $db->query($sql);
    $db->query("INSERT INTO migrations (migration) VALUES (?)", [$migration]);
}

function getMigrationFiles() {

    global $rootFolder;

    return array_diff(scandir($rootFolder . 'migrations'), ['.', '..']);
}

function migrate($db) {
    $runMigrations = getRunMigrations($db);
    $allMigrations = getMigrationFiles();
    $migrationsToRun = array_diff($allMigrations, $runMigrations);

    if (empty($migrationsToRun)) {
        echo "No new migrations to run.\n";
        return;
    }

    foreach ($migrationsToRun as $migration) {
        runMigration($db, $migration);
    }

    echo "All migrations completed.\n";
}
