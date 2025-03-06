#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function forwardToVM3($request)
{
    echo "Received request from VM1:\n";
    var_dump($request);

    // Create client to send request to VM3
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);

    return $response;
}

function requestProcessor($request)
{
    echo "Processing request...\n";
    if (!isset($request['type'])) {
        return "ERROR: Unsupported message type";
    }

    switch ($request['type']) {
        case "login":
            return forwardToVM3($request);
    }

    return array("returnCode" => '0', 'message' => "Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "RabbitMQ Server is running...\n";
$server->process_requests('requestProcessor');
exit();
?>

