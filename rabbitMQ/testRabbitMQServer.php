#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
	$host="100.93.130.48";
	$dbuser="TeamDog123";
	$dbpass="TeamDog123";
	$dbname="users";// name of the table
	$conn = new mysqli($host,$dbuser,$dbpass,$dbname);
	$stmt=$conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");//could be implemented here too
	$stmt->bind_param("ss",$username,$password);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows>0)
	{
		////$session_token=???
		return array('returnCode' => 'success','message'=>'Login successful');
	}else
	{
		return array('returnCode' => 'failure','message'=>'Login failed');
	}

	$conn->close();
}

function doValidate($sessionId)
{
    if ($sessionId === 'validSession') {
        return array('returnCode' => 'success', 'message' => 'Session valid');
    }
    return array('returnCode' => 'failure', 'message' => 'Invalid session');
}



function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>
