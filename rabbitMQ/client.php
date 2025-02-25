#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

$request = array();
$request['type'] = "query";
$request['value1'] = "some_value";
$request['value2'] = "another_value";

$response = $client->send_request($request);

echo "Client received response: \n";
print_r($response);
echo "\n\n";
?>

