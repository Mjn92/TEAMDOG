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

$query = "insert into users (username, email, password) values ($username, $email, $password);"

$response = $mydb->query($query);
if ($mydb->errno != 0)
{
        echo "failed to execute query:".PHP_EOL;
        echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
        exit(0);
}

if ($response->num_rows > 0){
        while ($row = $response->fetch_assoc()){
                print_r($row);
                echo PHP_EOL;
        }
} else {
        echo "No records found." . PHP_EOL;
}

$mydb->close();
?>

