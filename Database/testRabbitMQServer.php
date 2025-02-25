#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function generateSessionKey(){
	return bin2hex(random_bytes(32));
}

function doLogin($username,$password){
    // lookup username in databae and  check password
	$command = escapeshellcmd("./checkUser.php '$username' '$password'");
	$output = shell_exec($command);

	if(preg_match('/\baccept\b', $output)){
		$command = escapeshellcmd("./sessionKey.php '$username'");

		return array("returnCode" => '0', "message" => "Login successful", "sessionKey" => $sessionKey);
	}
	return array("returnCode" => '1', "message" => "Invalid credentials");
}
function makeUser($username,$email,$password){
    // make a  user in database
        $command = escapeshellcmd("./makeUser.php '$username' '$email' '$password'");
        $output = shell_exec($command);

	if(preg_match('/\bcreated\b', $output)){
		return array("returnCode" => '0', "message"  => "User Created");

	}
	return array("returnCode" => '1', "message" => "Invaild Input");
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
    case: "makeUser":
	    return makeUser($request['username',$request['email'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

