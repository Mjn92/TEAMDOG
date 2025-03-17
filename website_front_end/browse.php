<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Invalid request']);
    exit;
}

// Get the action (either 'get_movies' or 'search_movies')
$action = $_POST['action'];

if ($action === 'get_movies') {
    // Get all movies from the database
    $request = array();
    $request['type'] = 'get_movies';
    $response = getMoviesFromRabbitMQ($request);
} elseif ($action === 'search_movies') {
    // Get the search query from POST request
    $query = $_POST['query'];

    // Request to search for movies
    $request = array();
    $request['type'] = 'search_movies';
    $request['query'] = $query;
    $response = getMoviesFromRabbitMQ($request);
} else {
    echo json_encode(['returnCode' => 'failure', 'message' => 'Invalid action']);
    exit;
}

// Send the response back to frontend
echo json_encode($response);

// Function to handle communication with RabbitMQ
function getMoviesFromRabbitMQ($request) {
    // Create RabbitMQ client instance
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

    // Send request to RabbitMQ and get the response
    $response = $client->send_request($request);

    // Check if the response is successful
    if ($response['returnCode'] === 0) {
        // If successful, return the list of movies
        return ['returnCode' => 0, 'movies' => $response['movies']];
    } else {
        // If failure, return the error message
        return ['returnCode' => 1, 'message' => 'Failed to load movies'];
    }
}
?>
