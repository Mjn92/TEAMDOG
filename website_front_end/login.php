<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Invalid request']);
    exit;
}

// Get user input
$username = $_POST['username'];
$password = $_POST['password'];

// Create RabbitMQ client instance
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// Prepare request for RabbitMQ
$request=array();
$request['type']="login";
$request['username']=$username;
$request['password'] = $password; // Send hashed password

// Send request and get response
$response = $client->send_request($request);

// Log response for debugging
error_log("RabbitMQ response: " . json_encode($response));

// Send response back to frontend
echo json_encode($response);
?>
