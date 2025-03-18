<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// API request details for Movies Database API
$rapidApiHost = 'moviesdatabase.p.rapidapi.com';
$rapidApiKey = 'b982b66939msh3b305adfeb3a70ep1ae7f0jsn69e1de67d99e';
$apiUrl = 'https://moviesdatabase.p.rapidapi.com/titles?genre=drama';  // Example genre "drama"

// RabbitMQ connection details
$rabbitmqHost = '100.70.157.66';  // Replace with your RabbitMQ server IP
$rabbitmqPort = 5672;  // Default RabbitMQ port
$rabbitmqUser = 'apiUser';
$rabbitmqPassword = 'apiUser';
$rabbitmqQueue = 'apiQueue';
$rabbitmqvhost = 'apiHost';

// Create a new Guzzle HTTP client
$client = new Client();

// Make the API request to fetch movie data
$response = $client->request('GET', $apiUrl, [
    'headers' => [
        'x-rapidapi-host' => $rapidApiHost,
        'x-rapidapi-key' => $rapidApiKey,
    ],
]);

// Get the response body as an associative array
$data = json_decode($response->getBody()->getContents(), true);

// Check if we got a valid response from the API
if (isset($data['results'])) {
    // Connect to RabbitMQ
    $connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPassword, $rabbitmqvhost);
    $channel = $connection->channel();

    // Declare the queue (ensure it's the same on the consumer side)
    $channel->queue_declare($rabbitmqQueue, false, true, false, false);

    // Convert the API data into a message (using JSON encoding)
    $messageBody = json_encode($data['results']);
    if ($messageBody === false) {
        echo "Error encoding JSON: " . json_last_error_msg() . "\n";
    } else {
        $msg = new AMQPMessage($messageBody, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        // Send the message to RabbitMQ
        $channel->basic_publish($msg, '', $rabbitmqQueue);

        echo "Movie data sent to RabbitMQ.\n";
    }

    // Close the connection to RabbitMQ
    $channel->close();
    $connection->close();
} else {
    echo "Failed to fetch movie data.\n";
}

