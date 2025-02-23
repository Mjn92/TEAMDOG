#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function generateSessionKey(){
	return bin2hex(random_bytes(32));
}

function doLogin($username,$password){
    // lookup username in databas
	// check password
	$command = escapeshellcmd("./checkUser.php '$username' '$password'");
	$output = shell_exec($command);

	$output = trim($output);

	if($output === "accept"){
		$sessionKey = generateSessionKey();

		$db = new mysqli('127.0.0.1', 'TeamDog123', 'TeamDog123', 'movDB');
		if($db->connect_errno){
			return array("returnCode" => '1', "message" => "Database connection error");
		}

		$stmt = $db->perpare("insert into sessions (username, session_key) values (?, ?)");
		$stmt->bind_peram("ss", $username, $sessionKey);
		$stmt->execute();

		return array("returnCode" => '0', "message" => "Login successful", "sessionKey" => $sessionKey);
	}
	return array("returnCode" => '1', "message" => "Invalid credentials");
//    return true;
    //return false if not valid
}
function makeUser($username,$email,$password){
    // lookup username in databas
        // check password
        $command = escapeshellcmd("./makeUser.php '$username' '$email' '$password'");
        $output = shell_exec($command);

        $output = trim($output);

        if($output === "user successfully created!"){
                $sessionKey = generateSessionKey();

                file_put_contents('/tmp/session_keys.log', "$username:$sessionKey\n", FILE_APPEND);
                return array("returnCode" => '0', "message" => "user made successful", "sessionKey" => $sessionKey);
        }
        return array("returnCode" => '1', "message" => "Invalid credentials");
//    return true;
    //return false if not valid
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

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

