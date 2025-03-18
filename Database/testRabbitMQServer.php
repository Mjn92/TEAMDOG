#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function generateSessionKey(){
	return bin2hex(random_bytes(32));
}

function doLogin($username,$password){
	$command = escapeshellcmd("./checkUser.php '$username' '$password'");
	$output = shell_exec($command);

	if(preg_match('/\baccept\b/', $output)){
		$command = escapeshellcmd("./sessionkey.php '$username'");
		$output = shell_exec($command);

		return array("returnCode" => 0, "message" => "Login successful");
	}
	return array("returnCode" =>1, "message" => "Invalid credentials");
}
function makeUser($username,$email,$password){
        $command = escapeshellcmd("./makeUser.php '$username' '$email' '$password'");
        $output = shell_exec($command);

	if(preg_match('/\bcreated\b/', $output)){
		return array("returnCode" => 0, "message"  => "User Created");

	}else{
		return array("returnCode" => 1, "message" => "failed to create user");
	 }

	return array("returnCode" => 1, "message" => "Invaild Input");
}
function doValidate($username){
        $command = escapeshellcmd("./valSession.php '$username'");
        $output = shell_exec($command);

        if(preg_match('/\baccept\b/', $output)){
                return array("returnCode" => '0', "message" => "User is Logged in.");
        }
        return array("returnCode" => 1, "message" => "User is logged out.");
}

function makeUserPref($username, $comedy, $drama, $horror, $romance, $sci_fi){
    $command = escapeshellcmd("./makeUserPref.php '$username' '$comedy' '$drama' '$horror' '$romance' '$sci_fi'");
    $output = shell_exec($command);

    if(preg_match('/\binserted\b/', $output)){
        return array("returnCode" => 0, "message" => "User preferences saved");
    }
    return array("returnCode" => 1, "message" => "Failed to save user preferences");
}

function checkUserPref($username){
    $command = escapeshellcmd("./checkUserPref.php '$username'");
    $output = shell_exec($command);

    return array("returnCode" => 0, "message" => "User preferences", "data" => $output);
}

function checkMovie($movieName){
    $command = escapeshellcmd("./checkMovie.php '$movieName'");
    $output = shell_exec($command);
    
    if(strpos($output, "Movie not found in database.") !== false) {
        $command = escapeshellcmd("./getMovieRabbitMQ.php '$movieName'");
        $output = shell_exec($command);
    }
    
    return array("returnCode" => 0, "message" => "Movie data", "data" => $output);
}

function add_Friend($username, $friendName){
    $command = escapeshellcmd("./makeFriends.php '$username' '$friendName'");
    $output = shell_exec($command);
    
    if(preg_match('/\bsuccessfully\b/', $output)){
        return array("returnCode" => 0, "message" => "Friend added successfully");
    }
    return array("returnCode" => 1, "message" => "Failed to add friend");
}

function get_Friends($username){
    $command = escapeshellcmd("./checkFriends.php '$username'");
    $output = shell_exec($command);
    
    return array("returnCode" => 0, "message" => "User's friends list", "data" => $output);
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
    case "makeUser":
	    return makeUser($request['username'],$request['email'],$request['password']);
    case "validate_session":
	    return doValidate($request['sessionId']);
    case "makeUserPref":
	    return makeUserPref($request['username'], $request['comedy'], $request['drama'], $request['horror'], $request['romance'], $request['sci_fi']);
    case "checkUserPref":
	    return checkUserPref($request['username']);
    case "checkMovie":
            return checkMovie($request['movieName']);
    case "add_Friends":
      return addFriend($request['username'], $request['friendName']);
    case "get_Friends":
      return getFriends($request['username']); 
  }	
  return array("returnCode" => 1, 'message'=>"Server received request but not found");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

