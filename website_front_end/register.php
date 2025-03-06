<?php
// Include necessary files for RabbitMQ communication
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Check if the request method is POST (ensures form data is being sent)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Invalid request']);
    exit;
}

// Retrieve username and password from the POST request
$username = $_POST['username'];
$password = $_POST['password'];
//Hashes the password before sending it
// will do this at a later time 
// $hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Create a new RabbitMQ client instance
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// Prepare request data to send to the server
$request = array();
$request['type'] = "register";  // Tell the server that this is a registration request
$request['username'] = $username;
$request['password'] = $password;  

// Send request to the RabbitMQ server and get the response
$response = $client->send_request($request);

// Return the response from the RabbitMQ server to the frontend
echo json_encode($response);
?>

