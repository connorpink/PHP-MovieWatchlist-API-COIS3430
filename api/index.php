<?php $name = getenv('MYNAME');

// Function to send JSON response
function sendResponse($status, $data)
{
    $json = json_encode($data, JSON_PRETTY_PRINT);
    http_response_code($status);
    header("Content-Type: application/json; charset=UTF-8");
    header("Content-Length: " . strlen($json));
    echo $json;
    exit();
}

//function to check if api key is valid and get userID
function checkApiKey($apiKey, $pdo)
{
    if (empty($apiKey)) {
        sendResponse(400, ["error" => "You must provide an API key"]);
        return false;
    }

    $stmt = $pdo->prepare("SELECT userID, api_key FROM `cois3430_users` WHERE `api_key` = ?");
    $stmt->execute([$apiKey]);
    $user = $stmt->fetchAll();
    if (!$user) {
        sendResponse(403, ["error" => "The provided API key is invalid."]);
        return false;
    } else {
        return $user[0]['userID'];
    }
}

// Parse URL and method
$uri = parse_url($_SERVER['REQUEST_URI']);
define('__BASE__', '/~connorpink/3430/assn/cois-3430-2024su-a2-BigBeill/api/');
$endpoint = strtolower(str_replace(__BASE__, '', $uri['path']));
$method = $_SERVER['REQUEST_METHOD'];

//connect DB
require_once '../includes/library.php';
$pdo = connectDB();

//endpoint code

// Define the pattern to match /movies/{id}
$moviePattern1 = '/^movies\/(\d+)\/$/';
// Define the pattern to match /movies/{id}/rating
$moviePattern2 = '/^movies\/(\d+)$/';

// Define the pattern to match /movies/{id}
$movieRatingPattern1 = '/^movies\/(\d+)\/rating$/';
// Define the pattern to match /movies/{id}/rating
$movieRatingPattern2 = '/^movies\/(\d+)\/rating\/$/';
// if get
if ($method == 'GET') {
    // ~~~~~~ movies
    //if movies
    if (str_contains($endpoint, "movies")) {
        if ($endpoint == 'movies' || $endpoint == 'movies/') {
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
        elseif (preg_match($moviePattern1, $endpoint, $matches) || preg_match($moviePattern2, $endpoint, $matches)) {
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
        elseif (preg_match($movieRatingPattern2, $endpoint, $matches) || preg_match($movieRatingPattern2, $endpoint, $matches)) {
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
    }
    // ~~~~~ towatchlist
    // else if watchlist entries
    //    requires API key and returns all entries on users toWatchList
    elseif ($endpoint == 'towatchlist/entries') {
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        $stmt = $pdo->prepare("SELECT * FROM `cois3430_toWatchList` WHERE `userID` = ?");
        $stmt->execute([$userId]);
        $entries = $stmt->fetchAll();

        //check for return
        if ($entries) {
            sendResponse(200, $entries);
        } else {
            sendResponse(500, ["error" => "Returned no entries"]);
        }

    } else {
        sendResponse(500, ["error" => "something was wrong with the endpoint"]);
    }
}
// if method is post
elseif ($method == "POST") {
    // if inserting new watch list entry
    if ($endpoint == 'towatchlist/entries' || $endpoint == 'towatchlist/entries/') {
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        // data as movieID, priority, and notes
        // first check if they are all set
        if (empty($_POST['movieID']) || empty($_POST["priority"]) || empty($_POST["notes"])) {
            sendResponse(500, ["error" => "You must provide a movieid, priority and notes"]);
        }
        $movieID = $_POST["movieID"];
        $priority = $_POST["priority"];
        $notes = $_POST["notes"];
        //check that movieID is a number
        if (!is_numeric($movieID)) {
            //send error response
            sendResponse(400, ["error" => "movieID must be a number"]);
        }
        //check that priority is a number
        if (!is_numeric($priority)) {
            //send error response
            sendResponse(400, ["error" => "Priority must be a number"]);
        }
        //check that notes is a string
        if (!is_string($notes)) {
            //send error response
            sendResponse(400, ["error" => "Notes must be a string"]);
        }
        $stmt = $pdo->prepare("INSERT INTO cois3430_toWatchList (`userID`, `movieID`, `priority`, `notes`) VALUES (?,?,?,?)");
        $stmt->execute([$userId, $movieID, $priority, $notes]);
        // if successful insert into toWatchlist send appropriate response else send error
        if ($stmt) {
            sendResponse(201, ["message" => "Successfully added to watchlist"]);
        } else {
            sendResponse(500, ["error" => "failed to insert watch list entry"]);
        }
    }

}
/* TODO: Work in progress : PUT and PATCH dont take form data (need workaround) */
// else if method is put
elseif ($method == 'PUT') {

    // Define the pattern to match /toWatchList/entries/{id}
    $pattern = '/^towatchlist\/entries\/(\d+)\/$/';
    $pattern2 = '/^towatchlist\/entries\/(\d+)$/';

    // check if the endpoint matches the pattern for watchlist entries with ID
    // matches like towatchlist/entries/{id}/
    // matches like towatchlist/entries/{id}
    if (preg_match($pattern, $endpoint, $matches) || preg_match($pattern2, $endpoint, $matches)) {

        //get watchlist id from url using regex
        $toWatchListId = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        parse_str(file_get_contents('php://input'), $req_data);
        //var_dump($req_data);

        // data as movieID, priority, and notes
        // first check if they are all set
        if (empty($req_data['movieID']) || empty($req_data["priority"]) || empty($req_data["notes"])) {
            sendResponse(500, ["error" => "You must provide a movieid, priority and notes"]);
        }
        // get form data in the same method as the API key
        $movieID = $req_data["movieID"];
        $priority = $req_data["priority"];
        $notes = $req_data["notes"];
        //check that movieID is a number
        if (!is_numeric($movieID)) {
            //send error response
            sendResponse(400, ["error" => "movieID must be a number"]);
        }
        //check that priority is a number
        if (!is_numeric($priority)) {
            //send error response
            sendResponse(400, ["error" => "Priority must be a number"]);
        }
        //check that notes is a string
        if (!is_string($notes)) {
            //send error response
            sendResponse(400, ["error" => "Notes must be a string"]);
        }


        // check if there is an entry with the toWatchListId
        $stmt = $pdo->prepare("SELECT * FROM cois3430_toWatchList WHERE toWatchListID=? AND userID=?");
        $stmt->execute([$toWatchListId, $userId]);
        // if there is an entry with the toWatchListId then update it else, insert new entry into toWatchList table
        if ($stmt->rowCount() > 0) {
            //update code
            $stmt = $pdo->prepare("UPDATE cois3430_toWatchList SET movieID=?, priority=?, notes=? WHERE toWatchListID=? AND userID=?");
            $stmt->execute([$movieID, $priority, $notes, $toWatchListId, $userId]);
            sendResponse(201, ["message" => "To Watch List Updated"]);
        } else {
            //insert code
            $stmt = $pdo->prepare("INSERT INTO cois3430_toWatchList (userID, movieID, priority, notes) VALUES(?,?,?,?)");
            $stmt->execute([$userId, $movieID, $priority, $notes]);
            sendResponse(201, ["message" => "To Watch List Inserted"]);
        }

    }
}

//else if method is patch
elseif ($method == 'PATCH') {
    // Define the pattern to match /toWatchList/entries/{id}
    $pattern = '/^towatchlist\/entries\/(\d+)\/$/';

    if (preg_match($pattern, $endpoint, $matches)) {
        //get toWatchListId from url
        $toWatchListId  = $matches[1];
    }

}

//else if method is delete
elseif ($method == 'DELETE') {
    // Define the pattern to match /toWatchList/entries/{id}
    $pattern = '/^towatchlist\/entries\/(\d+)\/$/';
    $pattern2 = '/^towatchlist\/entries\/(\d+)$/';

    // if endpoint matches for delete watch list entry
    if (preg_match($pattern, $endpoint, $matches) || preg_match($pattern2, $endpoint, $matches)) {
        //get toWatchListId from url
        $toWatchListId = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        // check that a movieID was submit as form data
        if (empty($_POST['movieID'])) {
            sendResponse(500, ["error" => "You must provide a movieid"]);
        }

        //check that movieID is a number
        if (!is_numeric($_POST['movieID'])) {
            //send error response
            sendResponse(400, ["error" => "movieID must be a number"]);
        }

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