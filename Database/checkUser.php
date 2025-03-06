#!/usr/bin/php
<?php
$username = $argv[1];
$password = $argv[2];


$mydb = new mysqli('127.0.0.1','TeamDog123','TeamDog123','movDB');

if ($mydb->errno != 0)
{
        echo "failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "select * from users where username = ? and password = ?";

$stmt = $mydb->prepare($query);

if (!$stmt){
	echo "failed to prepare query: " . $mydb->error . PHP_EOL;
	exit(1);
}

$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0){
	echo "accept" . PHP_EOL;
} else {
	echo "reject" . PHP_EOL;
}
$stmt->close();
$mydb->close();
?>
~
                                                                                                      35,8          Bot


