#!/usr/bin/php


<?php

try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=movDB", "TeamDog123", "TeamDog123");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "DELETE FROM user_sessions WHERE expires < NOW()";
    $deletedRows = $pdo->exec($query);

    echo date('Y-m-d H:i:s') . " - Deleted $deletedRows expired user sessions.\n";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . PHP_EOL;
}
?>

