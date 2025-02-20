<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username, $password)
{
    // Database connection details
    $host = "100.93.130.48"; // Database server IP
    $dbuser = "mjn92";       // Database username
    $dbpass = "TeamDog123";  //Our DB password
    $dbname = "TeamDog123"; // Our DB name 

    // Connect to the database
    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        return array('returnCode' => 'failure', 'message' => 'Database connection failed: ' . $conn->connect_error);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    if ($stmt === false) {
        return array('returnCode' => 'failure', 'message' => 'Database query failed: ' . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Successful login
        return array('returnCode' => 'success', 'message' => 'Login successful');
    } else {
        // Invalid credentials
        return array('returnCode' => 'failure', 'message' => 'Login failed');
    }

    // Close the connection
    $conn->close();
}

function requestProcessor($request)
{
    echo "Received request".PHP_EOL;
    var_dump($request);
    
    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }
    
    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);
        case "validate_session":
            return doValidate($request['sessionId']);
    }
    return array("returnCode" => '0', 'message' => "Server received request and processed");
}

// Create and start the RabbitMQ server
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");
$server->process_requests('requestProcessor');
exit();
?>
