#!/usr/bin/php
<?php

if ($argc < 3){
    echo "Usage: php add_friend.php <username> <friend_username>" . PHP_EOL;
    exit(1);
}

$username = $argv[1];
$friend_username = $argv[2];

$mydb = new mysqli('127.0.0.1', 'TeamDog123', 'TeamDog123', 'movDB');

if ($mydb->connect_errno) {
    echo "Failed to connect to database: " . $mydb->connect_error . PHP_EOL;
    exit(0);
}

echo "Successfully connected to database" . PHP_EOL;

$check_query = "SELECT * FROM user_friends 
                WHERE (user1 = ? AND user2 = ?) 
                OR (user1 = ? AND user2 = ?)";

$check_stmt = $mydb->prepare($check_query);
$check_stmt->bind_param("ssss", $username, $friend_username, $friend_username, $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "Friend request already exists or you are already friends!" . PHP_EOL;
    exit(1);
}

$query = "INSERT INTO user_friends (user1, user2, status) VALUES (?, ?, 'pending')";
$stmt = $mydb->prepare($query);

if (!$stmt) {
    echo "Failed to prepare query: " . $mydb->error . PHP_EOL;
    exit(1);
}

$stmt->bind_param("ss", $username, $friend_username);
$success = $stmt->execute();

if (!$success) {
    echo "Failed to execute query: " . $stmt->error . PHP_EOL;
    exit(1);
}

echo "Friend request sent successfully!" . PHP_EOL;

$stmt->close();
$mydb->close();
?>

