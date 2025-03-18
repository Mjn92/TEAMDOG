#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$type=$argv[1];
$username=$argv[2];
$password=$argv[3];
$email=$argv[4];
$request = array();
$request['type'] = $type;  // Simulates a request from VM1
$request['username'] = $username;
$request['password'] = $password;
$request['email']=$email;

$response = $client->send_request($request);

echo "Test client received response:\n";
print_r($response);
echo "\n\n";
?>

