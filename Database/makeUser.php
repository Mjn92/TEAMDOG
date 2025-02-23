#!/usr/bin/php
<?php

if ($argc < 4){
	echo "requires username, eamil, and password.";
	exit(1);
}

$username = $argv[1];
$email = $argv[2];
$password = $argv[3];

$mydb = new mysqli('127.0.0.1','TeamDog123','TeamDog123','movDB');

if ($mydb->errno != 0){
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "insert into users (username, email, password) values (?, ?, ?)";
$stmt = $mydb->prepare($query);

if (!$stmt){
	echo "failed to prepare query: " . $mydb-error . PHP_EOL;
	exit(1);
}

$stmt->bind_param("sss", $username, $email, $password);
$success = $stmt->execute();

if(!$success){
	echo "failed to execute query: " . $stmt->error . PHP_EOL;
	exit(1);
}

echo "user successfully created!" . PHP_EOL;

$stmt->close();
$mydb->close();
?>

