<?php

// Include RabbitMQ library (needed for messaging system)
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Invalid request method']);
    exit;
}

// Read the data sent from the frontend
$request = $_POST;

// Ensures input validation. Makes sure all required fields are provided
if (!isset($request["type"]) || !isset($request["username"]) || !isset($request["password"])) {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Missing required fields']);
    exit;
}

// Create RabbitMQ client instance
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// Prepare request for RabbitMQ
$rabbit_request = [
    "type" => "login", // Tells the server it's a login request
    "username" => $request["username"],
    "password" => $request["password"]
];

// Send request to RabbitMQ and wait for response
$response = $client->send_request($rabbit_request);

// Send the response back to the frontend as JSON
echo json_encode($response);
exit;
?>
