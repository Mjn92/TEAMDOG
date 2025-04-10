#!/usr/bin/php
<?php
require_once('path.inc'); 
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');      
// This file should contain all the helper functions.  I am corrected a few things on this file before push
//There is a rapiAPI file as well that will pull information from the api based on the request and whats sent to the function file.
require_once('dmzFunctions.php');

// This function receives all messages sent
function requestProcessor($request){
    echo "Received request" . PHP_EOL;
    var_dump($request); // Debugging output of the full request contents

    // Safety check: ensure the request includes a 'type'
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type"; // Reject incomplete requests
    }

    // Switch based on request type to route to the appropriate handler
    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);

        case "makeUser":
            return makeUser($request['username'], $request['email'], $request['password']);

        case "validate_session":
            return doValidate($request['sessionId']);

        case "makeUserPref":
            return makeUserPref(
                $request['username'],
                $request['comedy'],
                $request['drama'],
                $request['horror'],
                $request['romance'],
                $request['sci_fi']
            );

        case "checkUserPref":
            return checkUserPref($request['username']);

        case "checkMovie":
            return checkMovie($request['movieName']);

        case "add_Friends":
            return add_Friend($request['username'], $request['friendName']);

        case "get_Friends":
            return get_Friends($request['username']);

        default:
            // If the request type is unknown, return a message indicating that
            return array("returnCode" => 1, "message" => "Request type not recognized");
    }
}

// === Create the RabbitMQ Server ===
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "DMZ RabbitMQ Listener STARTING..." . PHP_EOL;

// Start processing incoming requests using your handler
$server->process_requests('requestProcessor');

echo "DMZ RabbitMQ Listener SHUTTING DOWN..." . PHP_EOL;

exit();
?>
