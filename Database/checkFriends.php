#!/usr/bin/php
<?php
$username = $argv[1];

$mydb = new mysqli('127.0.0.1', 'TeamDog123', 'TeamDog123', 'movDB');

if ($mydb->connect_errno) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(0);
}

echo "Successfully connected to database" . PHP_EOL;

$query = "SELECT 
            CASE 
                WHEN user1 = ? THEN user2 
                ELSE user1 
            END AS friend 
          FROM user_friends 
          WHERE user1 = ? OR user2 = ? AND status = 'accepted'";

$stmt = $mydb->prepare($query);

if (!$stmt) {
    echo "Failed to prepare query: " . $mydb->error . PHP_EOL;
    exit(1);
}

$stmt->bind_param("sss", $username, $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Friend List:" . PHP_EOL;
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['friend'] . PHP_EOL;
    }
} else {
    echo "No friends found" . PHP_EOL;
}

$stmt->close();
$mydb->close();
?>

