#!/usr/bin/php
<?php

if ($argc < 7) {
    echo "Usage: php script.php <username> <comedy> <drama> <horror> <romance> <sci_fi>" . PHP_EOL;
    exit(1);
}

$username = $argv[1];
$comedy = (int)$argv[2];
$drama = (int)$argv[3];
$horror = (int)$argv[4];
$romance = (int)$argv[5];
$sci_fi = (int)$argv[6];

$mydb = new mysqli('127.0.0.1', 'TeamDog123', 'TeamDog123', 'movDB');

if ($mydb->connect_errno) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(1);
}

echo "Successfully connected to database" . PHP_EOL;

$query = "INSERT INTO user_preferences (username, comedy, drama, horror, romance, sci_fi) VALUES (?, ?, ?, ?, ?, ?) 
          ON DUPLICATE KEY UPDATE comedy=VALUES(comedy), drama=VALUES(drama), horror=VALUES(horror), romance=VALUES(romance), sci_fi=VALUES(sci_fi)";

$stmt = $mydb->prepare($query);

if (!$stmt) {
    echo "Failed to prepare query: " . $mydb->error . PHP_EOL;
    exit(1);
}

$stmt->bind_param("siiiii", $username, $comedy, $drama, $horror, $romance, $sci_fi);
$success = $stmt->execute();

if (!$success) {
    echo "Failed to execute query: " . $stmt->error . PHP_EOL;
    exit(1);
}

echo "User preferences successfully inserted/updated!" . PHP_EOL;

$stmt->close();
$mydb->close();
?>
