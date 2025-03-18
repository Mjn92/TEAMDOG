<?php
// Include RabbitMQ setup files
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Start a session to track logged-in users
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['returnCode' => 1, 'message' => 'User not logged in']);
    exit;
}

// Get the user’s movie preferences from the form submission
$username = $_SESSION['username']; //defines username
$genre = $_POST['genre'];
$year = $_POST['year'];
$rating = $_POST['rating'];

// Create a RabbitMQ client instance to communicate with the server
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// Prepare a request to send to RabbitMQ
$request = array();
$request['type'] = "get_recommendations"; // Request type: fetch recommendations
$request['username'] = $username; // Send the username to get personalized suggestions

//includes the movie preferences that are provided in the request 
if ($genre) {
    $request['genre'] = $genre;
}
if ($year) {
    $request['year'] = $year;
}
if ($rating) {
    $request['rating'] = $rating;
}
// Send the request and get the response from RabbitMQ
$response = $client->send_request($request);

// Log the response for debugging
error_log("RabbitMQ response: " . json_encode($response));

// Send the response back to the frontend as JSON
echo json_encode($response);
?>
