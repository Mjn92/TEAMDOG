<?php

// Include RabbitMQ library
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Define RabbitMQ server IP address
$rabbitmq_server_ip = "100.89.105.111"; // RabbitMQ IP

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Invalid request method']);
    exit;
}

// Read the data sent from the frontend
$request = $_POST;

// Make sure all required fields are present
if (!isset($request["type"]) || !isset($request["username"]) || !isset($request["password"])) {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Missing required fields']);
    exit;
}

// Create RabbitMQ client instance and manually set the IP
$client = new rabbitMQClient(null, null); // Create client without ini file
$client->host = $rabbitmq_server_ip; // Set RabbitMQ server IP
$client->port = 5672; // Default RabbitMQ port
$client->user = "guest"; // RabbitMQ username (change if needed)
$client->password = "guest"; // RabbitMQ password (change if needed)
$client->vhost = "/"; // Default virtual host

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
