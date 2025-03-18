#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "apiMesseager");

if (isset($argv[1])) {
    $movieName = $argv[1];
} else {
    echo "No movie name provided.\n";
    exit(1);
}

$request = array();
$request['type'] = "MovieRequest";
$request['movie_name'] = $movieName;

$response = $client->send_request($request);

if (!empty($response) && is_array($response)) {
    echo "Client received response: " . PHP_EOL;
    print_r($response);
    echo "\n\n";

    if (!isset($response['title'], $response['release_year'], $response['genre'], $response['description'],
        $response['director'], $response['rating'], $response['imdb_id'], $response['api_id'], $response['poster_image_url'])) {
        echo "Error: Response missing required movie details.\n";
        exit(1);
    }

    // Extract data from response
    $title = escapeshellarg($response['title']);
    $release_year = escapeshellarg($response['release_year']);
    $genre = escapeshellarg($response['genre']);
    $description = escapeshellarg($response['description']);
    $director = escapeshellarg($response['director']);
    $rating = escapeshellarg($response['rating']);
    $imdb_id = escapeshellarg($response['imdb_id']);
    $api_id = escapeshellarg($response['api_id']);
    $poster_image_url = escapeshellarg($response['poster_image_url']);

    $command = "php insertMovie.php $title $release_year $genre $description $director $rating $imdb_id $api_id $poster_image_url";
    exec($command, $output, $returnVar);

    echo "insertMovie.php output:\n";
    print_r($output);
    echo "\n";

    if ($returnVar !== 0) {
        echo "Error: insertMovie.php execution failed.\n";
    }
} else {
    echo "No valid response received from the server.\n";
}

echo $argv[0] . " END" . PHP_EOL;
?>

