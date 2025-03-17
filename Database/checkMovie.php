#!/usr/bin/php
<?php

if ($argc < 2) {
    echo "Usage: php checkMovie.php <movie_title>" . PHP_EOL;
    exit(1);
}

$movie_title = $argv[1];

$mydb = new mysqli('127.0.0.1', 'TeamDog123', 'TeamDog123', 'movDB');

if ($mydb->connect_errno) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(1);
}

echo "Successfully connected to database" . PHP_EOL;

$query = "SELECT * FROM movies WHERE title = ?";
$stmt = $mydb->prepare($query);

if (!$stmt) {
    echo "Failed to prepare query: " . $mydb->error . PHP_EOL;
    exit(1);
}

$stmt->bind_param("s", $movie_title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Movie found: " . $row['title'] . " (" . $row['release_year'] . ")" . PHP_EOL;
    echo "Genre: " . $row['genre'] . PHP_EOL;
    echo "Description: " . $row['description'] . PHP_EOL;
    echo "Director: " . $row['director'] . PHP_EOL;
    echo "Rating: " . $row['rating'] . PHP_EOL;
    echo "IMDB ID: " . $row['imdb_id'] . PHP_EOL;
    echo "API ID: " . $row['api_id'] . PHP_EOL;
    echo "Poster URL: " . hex2bin($row['poster_image']) . PHP_EOL;
} else {
    echo "Movie not found in database." . PHP_EOL;
}

$stmt->close();
$mydb->close();
?>
