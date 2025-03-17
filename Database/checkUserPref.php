#!/usr/bin/php
<?php
$username = $argv[1];

$mydb = new mysqli('127.0.0.1','TeamDog123','TeamDog123','movDB');

if ($mydb->connect_errno) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(1);
}

echo "Successfully connected to database" . PHP_EOL;

$query = "SELECT * FROM user_preferences WHERE username = ?";
$stmt = $mydb->prepare($query);

if (!$stmt) {
    echo "Failed to prepare query: " . $mydb->error . PHP_EOL;
    exit(1);
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    foreach ($row as $column => $value) {
        if ($column != 'username') { 
            echo "$column: $value" . PHP_EOL;
        }
    }
} else {
    echo "No preferences found for user: $username" . PHP_EOL;
}

$stmt->close();
$mydb->close();
?>
