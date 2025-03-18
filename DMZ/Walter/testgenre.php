<?php

require 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// RabbitMQ connection details
$rabbitmqHost = '100.70.157.66';  // Replace with your RabbitMQ server IP
$rabbitmqPort = 5672;  // Default RabbitMQ port
$rabbitmqUser = 'apiUser';
$rabbitmqPassword = 'apiUser';
$rabbitmqQueue = 'apiQueue';  // Queue name to consume from
$rabbitmqvhost = 'apiHost';  // Virtual host to connect to

// Connect to RabbitMQ
$connection = new AMQPStreamConnection($rabbitmqHost, $rabbitmqPort, $rabbitmqUser, $rabbitmqPassword, $rabbitmqvhost);
$channel = $connection->channel();

// Declare the queue (same as the producer script to ensure it exists)
$channel->queue_declare($rabbitmqQueue, false, true, false, false);

// Callback function to handle messages from the queue
$callback = function($msg) {
    echo "Received message:\n";
    echo "Raw message body: " . $msg->body . "\n";  // Print raw message body for debugging

    // Decode the JSON data from the message body
    $data = json_decode($msg->body, true);
    
    if ($data) {
        // Loop through the movie data and print each movie's title
        foreach ($data as $movie) {
            echo "Title: " . $movie['title'] . "\n";
        }
    } else {
        echo "Failed to decode message. Possible reasons: Invalid JSON or unexpected format.\n";
    }
};

// Start consuming messages from the queue
echo "Waiting for messages. To exit press CTRL+C\n";
$channel->basic_consume($rabbitmqQueue, '', false, true, false, false, $callback);

// Wait for messages and process them
while($channel->is_consuming()) {
    $channel->wait();
}

// Close the channel and connection when done
$channel->close();
$connection->close();
?>

