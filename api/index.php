<?php $name = getenv('MYNAME'); 

// Function to send JSON response
function sendResponse($status, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    http_response_code($status);
    header("Content-Type: application/json; charset=UTF-8");
    header("Content-Length: " . strlen($json));
    echo $json;
    exit();
}

// Parse URL and method
$uri = parse_url($_SERVER['REQUEST_URI']);
define('__BASE__', '/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/');
$endpoint = str_replace(__BASE__, '', $uri['path']);
$method = $_SERVER['REQUEST_METHOD'];

//connect DB
require_once '../includes/library.php';
$pdo = connectDB();

//endpoint code

// Define the pattern to match /movies/{id}
$pattern = '/^movies\/(\d+)\/$/';
// Define the pattern to match /movies/{id}/rating
$pattern2 = '/^movies\/(\d+)\/rating$/';
// if get
if ($method == 'GET'){
    //if movies
    if ($endpoint == 'movies'){
        $stmt = $pdo->query("SELECT movieID,title,release_date,vote_average,vote_count,runtime,description FROM cois3430_movies");
        $movies = $stmt->fetchAll();

        //check for return
        if ($movies) {
            sendResponse(200, $movies);
        } else {
            sendResponse(500, ["error" => "Failed to query database"]);
        }
    }
    // else if retriving movies with a certain id
    else if (preg_match($pattern, $endpoint, $matches)){
        $id = $matches[1];
        $stmt = $pdo->prepare("SELECT movieID,title,release_date,vote_average,vote_count,runtime,description FROM cois3430_movies WHERE movieID = ?");
        $stmt->execute([$id]);
        $movies = $stmt->fetchAll();

        //check for return
        if ($movies) {
            sendResponse(200, $movies);
        } else {
            sendResponse(500, ["error" => "Failed to query database"]);
        }
    }
    // else if retriving movies with a certain id and getting rating
    else if (preg_match($pattern2, $endpoint, $matches)){
        $id = $matches[1];
        $stmt = $pdo->prepare("SELECT movieID,vote_average FROM cois3430_movies WHERE movieID = ?");
        $stmt->execute([$id]);
        $movies = $stmt->fetchAll();

        //check for return
        if ($movies) {
            sendResponse(200, $movies);
        } else {
            sendResponse(500, ["error" => "Failed to query database"]);
        }
    }
    else{
        var_dump($endpoint);
        preg_match($pattern, $endpoint, $matches);
        var_dump($matches);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
</head>

<body>
    <h1>This is the index page. Your name is <?= $name ?></h1>

    <?php var_dump($_SERVER); ?>
</body>

</html>