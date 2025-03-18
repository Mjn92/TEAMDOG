<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Invalid request']);
    exit;
}

// Get the username from the POST request
$username = $_POST['username'];

// Create RabbitMQ client instance
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// Prepare the request for RabbitMQ to get friends list
$request = array();
$request['type'] = 'get_friends';
$request['username'] = $username;

// Send the request to RabbitMQ
$response = $client->send_request($request);

// Log response for debugging
error_log("RabbitMQ response: " . json_encode($response));

// Check the response from RabbitMQ
if ($response['returnCode'] === 0) {
    // If successful, return the list of friends
    echo json_encode(['returnCode' => 0, 'friends' => $response['friends']]);
} else {
    // If failure, return the error message
    echo json_encode(['returnCode' => 1, 'message' => 'Failed to load friends list']);
}
?>
