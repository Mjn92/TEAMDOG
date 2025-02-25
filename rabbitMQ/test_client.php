#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request = array();
$request['type'] = "login";  // Simulates a request from VM1
$request['username'] = "test";
$request['password'] = "pass1234";

$response = $client->send_request($request);

echo "Test client received response:\n";
print_r($response);
echo "\n\n";
?>

