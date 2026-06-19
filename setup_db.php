<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1", "root", "");
    $pdo->exec("DROP DATABASE IF EXISTS apw_management");
    $pdo->exec("CREATE DATABASE apw_management");
    echo "Database reset.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
