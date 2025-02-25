#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if ($argc<2)
{
	echo "not valid";
	exit(1);
}
$username=$argv[1];
$password=$argv[2];
//$type=$arg[3];
//$msg=$argv[3];

$request = array();
$request['type'] = "login";
$request['username'] = $username;
$request['password'] = $password;
//$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

