#!/usr/bin/php
<?php

if ($argc < 8) {
    echo "Usage: php insertMovie.php <title> <release_year> <genre> <description> <director> <rating> <imdb_id> <api_id> <poster_image_url>" . PHP_EOL;
    exit(1);
}

$title = $argv[1];
$release_year = (int)$argv[2];
$genre = $argv[3];
$description = $argv[4];
$director = $argv[5];
$rating = (float)$argv[6];
$imdb_id = $argv[7];
$api_id = $argv[8];
$poster_image = bin2hex(file_get_contents($argv[9]));

$mydb = new mysqli('127.0.0.1', 'TeamDog123', 'TeamDog123', 'movDB');

if ($mydb->connect_errno) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(1);
}

echo "Successfully connected to database" . PHP_EOL;

$query = "INSERT INTO movies (title, release_year, genre, description, director, rating, imdb_id, api_id, poster_image) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $mydb->prepare($query);

if (!$stmt) {
    echo "Failed to prepare query: " . $mydb->error . PHP_EOL;
    exit(1);
}

$stmt->bind_param("sisssdsis", $title, $release_year, $genre, $description, $director, $rating, $imdb_id, $api_id, $poster_image);
$success = $stmt->execute();

if (!$success) {
    echo "Failed to execute query: " . $stmt->error . PHP_EOL;
    exit(1);
}

echo "Movie successfully inserted!" . PHP_EOL;

$stmt->close();
$mydb->close();
?>

