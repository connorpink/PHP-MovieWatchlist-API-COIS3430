<?php $name = getenv('MYNAME');

// function to validate that input is a date
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

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
define('__BASE__', '/~' . $name . '/3430/assn/cois-3430-2024su-a2-BigBeill/api/');
$endpoint = strtolower(str_replace(__BASE__, '', $uri['path']));
$method = $_SERVER['REQUEST_METHOD'];

//connect DB
require_once '../includes/library.php';
$pdo = connectDB();

//endpoint code


// if get
if ($method == 'GET') {
    // Define the pattern to match /movies/{id}
    $moviePattern1 = '/^movies\/(\d+)\/$/';
    // Define the pattern to match /movies/{id}
    $moviePattern2 = '/^movies\/(\d+)$/';

    // Define the pattern to match /movies/{id}/rating
    $movieRatingPattern1 = '/^movies\/(\d+)\/rating$/';
    // Define the pattern to match /movies/{id}/rating/
    $movieRatingPattern2 = '/^movies\/(\d+)\/rating\/$/';

    // Define the pattern to match /completedWatchList/entries/{id}/times-watched
    $timesWatchedPattern1 = '/^completedwatchlist\/entries\/(\d+)\/times-watched$/';
    // Define the pattern to match /completedWatchList/entries/{id}/times-watched/
    $timesWatchedPattern2 = '/^completedwatchlist\/entries\/(\d+)\/times-watched\/$/';

    // Define the pattern to match /completedWatchList/entries/{id}/times-watched
    $ratingPattern1 = '/^completedwatchlist\/entries\/(\d+)\/rating$/';
    // Define the pattern to match /completedWatchList/entries/{id}/times-watched/
    $ratingPattern2 = '/^completedwatchlist\/entries\/(\d+)\/rating\/$/';

    // Define the pattern to match /users/entries/{id}/stats
    $userPattern1 = '/^users\/(\d+)\/stats$/';
    // Define the pattern to match /users/entries/{id}/stats/
    $userPattern2 = '/^users\/(\d+)\/stats\/$/';

    // ~~~~~~ movies
    //if movies
    if (str_contains($endpoint, "movies")) {
        if ($endpoint == 'movies' || $endpoint == 'movies/') {
            $getVars = array_keys($_GET);

            //check form movie name filter
            if (isset($_GET['name'])) {
                $name = $_GET['name'];
                //check that name is a string
                if (!is_string($name)) {
                    //send error response
                    sendResponse(400, ["error" => "name must be a string"]);
                }
                // check for both name and rating filter
                if (isset($_GET['rating'])) {

                    $rating = $_GET['rating'];
                    //check that rating is a number and is between 1-10 inclusive
                    if (!is_numeric($rating)) {
                        //send error response
                        sendResponse(400, ["error" => "rating must be a number"]);
                    }
                    if ($rating < 1 || $rating > 10) {
                        //send error response
                        sendResponse(400, ["error" => "rating must be between 1 and 10"]);
                    }
                    $stmt = $pdo->prepare("SELECT movieID,title,release_date,vote_average,vote_count,runtime,description FROM cois3430_movies WHERE title LIKE ? AND vote_average >= ?");
                    $stmt->execute(["%$name%", $rating]);
                    $movies = $stmt->fetchAll();
                    //check for return
                    if ($movies) {
                        sendResponse(200, $movies);
                    } else {
                        sendResponse(500, ["error" => "Failed to query database"]);
                    }
                }
                //otherwise just do name filter
                else {
                    $stmt = $pdo->prepare("SELECT movieID,title,release_date,vote_average,vote_count,runtime,description FROM cois3430_movies WHERE title LIKE ?");
                    $stmt->execute(["%$name%"]);
                    $movies = $stmt->fetchAll();
                    //check for return
                    if ($movies) {
                        sendResponse(200, $movies);
                    } else {
                        sendResponse(500, ["error" => "Failed to query database"]);
                    }
                }

            }
            // check for filter rating
            elseif (isset($_GET['rating'])) {
                $rating = $_GET['rating'];
                //check that rating is a number and is between 1-10 inclusive
                if (!is_numeric($rating)) {
                    //send error response
                    sendResponse(400, ["error" => "rating must be a number"]);
                }
                if ($rating < 1 || $rating > 10) {
                    //send error response
                    sendResponse(400, ["error" => "rating must be between 1 and 10"]);
                }
                $stmt = $pdo->prepare("SELECT movieID,title,release_date,vote_average,vote_count,runtime,description FROM cois3430_movies WHERE vote_average >= ?");
                $stmt->execute([$rating]);
                $movies = $stmt->fetchAll();
                //check for return
                if ($movies) {
                    sendResponse(200, $movies);
                } else {
                    sendResponse(500, ["error" => "Failed to query database"]);
                }
            }
            // else just return all movies in DB
            else {
                $stmt = $pdo->query("SELECT movieID,title,release_date,vote_average,vote_count,runtime,description FROM cois3430_movies");
                $movies = $stmt->fetchAll();
                //check for return
                if ($movies) {
                    sendResponse(200, $movies);
                } else {
                    sendResponse(500, ["error" => "Failed to query database"]);
                }
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

        // else if retrieving movies with a certain id and getting rating
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
    elseif ($endpoint == 'towatchlist/entries' || $endpoint == 'towatchlist/entries/') {
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        // if filter set for priority
        if (isset($_GET['priority'])) {
            $priority = $_GET['priority'];
            //check that priority is a number
            if (!is_numeric($priority)) {
                //send error response
                sendResponse(400, ["error" => "Priority must be a number"]);
            }
            $stmt = $pdo->prepare("SELECT * FROM `cois3430_toWatchList` WHERE `userID` = ? AND priority = ?");
            $stmt->execute([$userId, $priority]);
            $entries = $stmt->fetchAll();

            //check for return
            if ($entries) {
                sendResponse(200, $entries);
            } else {
                sendResponse(500, ["error" => "Returned no entries"]);
            }
        }
        // else just return all watch list entries
        else {
            $stmt = $pdo->prepare("SELECT * FROM `cois3430_toWatchList` WHERE `userID` = ?");
            $stmt->execute([$userId]);
            $entries = $stmt->fetchAll();

            //check for return
            if ($entries) {
                sendResponse(200, $entries);
            } else {
                sendResponse(500, ["error" => "Returned no entries"]);
            }
        }
    }
    //else if endpoint is completedwatchlistEntries
    elseif ($endpoint == 'completedwatchlist/entries' || $endpoint == 'completedwatchlist/entries/') {
        //return all entries on users completedWatchList
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        // optional filter on number of times watched
        if (isset($_GET['times_watched'])) {
            $times_watched = $_GET['times_watched'];
            //check that times_watched is a number
            if (!is_numeric($times_watched)) {
                //send error response
                sendResponse(400, ["error" => "times_watched must be a number"]);
            }
            $stmt = $pdo->prepare("SELECT * FROM `cois3430_completedWatchList` WHERE `userID` =? AND times_watched >= ?");
            $stmt->execute([$userId, $times_watched]);
            $entries = $stmt->fetchAll();

            // check for rows returned
            if ($entries) {
                sendResponse(200, $entries);
            } else {
                sendResponse(500, ["error" => "no entries in completed watch list for current user"]); //no content
            }
        }

        //else just return completed watch list entries
        else {
            $stmt = $pdo->prepare("SELECT * FROM `cois3430_completedWatchList` WHERE `userID` =?");
            $stmt->execute([$userId]);
            $entries = $stmt->fetchAll();

            // check for rows returned
            if ($entries) {
                sendResponse(200, $entries);
            } else {
                sendResponse(500, ["error" => "no entries in completed watch list for current user"]); //no content
            }
        }
    }
    //else if endpoint is completedwatchlistEntries/{id}/times-watched
    elseif(preg_match($timesWatchedPattern1, $endpoint, $matches) || preg_match($timesWatchedPattern2, $endpoint, $matches)) {
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userID = checkApiKey($apiKey, $pdo);
        $movieID = $matches[1];
        $stmt = $pdo->prepare("SELECT movieID, times_watched FROM `cois3430_completedWatchList` WHERE `userID`=? AND `movieID`=?");
        $stmt->execute([$userID, $movieID]);

        $entry = $stmt->fetch();
        // check for rows returned
        if ($entry) {
            sendResponse(200, ["times watched" => $entry]);
        } else {
            sendResponse(500, ["error" => "no entries in completed watch list for this movie as current user"]);  //no content
        }
    }
    //else if endpoint is completedwatchlist/Entries/{id}/rating
    elseif(preg_match($ratingPattern1, $endpoint, $matches) || preg_match($ratingPattern2, $endpoint, $matches)) {
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userID = checkApiKey($apiKey, $pdo);
        $movieID = $matches[1];
        $stmt = $pdo->prepare("SELECT movieID, rating FROM `cois3430_completedWatchList` WHERE `userID`=? AND `movieID`=?");
        $stmt->execute([$userID, $movieID]);

        $entry = $stmt->fetch();
        // check for rows returned
        if ($entry) {
            sendResponse(200, ["rating" => $entry]);
        } else {
            sendResponse(500, ["error" => "no entries in completed watch list for this movie as current user"]);  //no content
        }
    }
    //else if endpoint is user/{id}/stats
    elseif(preg_match($userPattern1, $endpoint, $matches) || preg_match($userPattern2, $endpoint, $matches)) {
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userID = checkApiKey($apiKey, $pdo);
        $URLuserID = $matches[1];
        if ($URLuserID == $userID) {
            //get user stats
            // get completed watch list entries
            $stmt = $pdo->prepare("SELECT * FROM `cois3430_completedWatchList` WHERE `userID`=?");
            $stmt->execute([$userID]);
            $completedEntries = $stmt->fetchAll();
            //get watch list entries
            $stmt = $pdo->prepare("SELECT * FROM `cois3430_toWatchList` WHERE `userID`=?");
            $stmt->execute([$userID]);
            $toWatchEntries = $stmt->fetchAll();

            // check for rows returned
            if ($completedEntries || $toWatchEntries) {
                //create user stats json object from data
                // return total time watched, average rating, planned time to watch, and first movie watched
                if ($completedEntries) {
                    // time watched and average rating first movie watched
                    $timeWatched = 0;
                    $averageRating = 0;
                    //set firstMoviewatched to date_initially_watched of first entry in completedEntries
                    $firstMovieWatched = new DateTime($completedEntries[0]['date_initially_watched']);
                    $firstMovieWatchedId = $completedEntries[0]['movieID'];
                    $stmt = $pdo->prepare("SELECT `title` FROM `cois3430_movies` WHERE `movieID`=?");
                    $stmt->execute([$firstMovieWatchedId]);
                    $firstMovieWatchedTitle = $stmt->fetch()['title'];
                    foreach ($completedEntries as $entry) {
                        $movieID = $entry['movieID'];
                        //get average
                        $averageRating = $averageRating + $entry['rating'];
                        //get runtime
                        $stmt = $pdo->prepare("SELECT `runtime`,`title` FROM `cois3430_movies` WHERE `movieID`=?");
                        $stmt->execute([$movieID]);
                        $movie = $stmt->fetch();
                        $runtime = $movie['runtime'];
                        $timeWatched += $runtime;
                        // get first movie watched from lowest date_inititially_watched
                        //if date_initially_watched is less than firstMoviewatched, set firstMoviewatched to date_initially_watched
                        // each date_initially_watched in entry will be a string like 2019-03-04, convert to date object and compare
                        //if date is less than firstMoviewatched, set firstMoviewatched to that date
                        //$firstMovieWatched = new DateTime($firstMovieWatched);
                        $entryDate = new DateTime($entry['date_initially_watched']);
                        if ($entryDate < $firstMovieWatched) {
                            $firstMovieWatched = $entry['date_initially_watched'];
                            $firstMovieWatchedTitle = $movie['title'];
                        }
                    }
                    //update average rating by dividing by number of entries in completedEntries
                    $averageRating = round(($averageRating / count($completedEntries)), 2);
                    //format first movie watched date
                    $firstMovieWatched = $firstMovieWatched->format('Y-m-d');
                } else {
                    //no entries
                    $averageRating = 'N/A';
                    $timeWatched = 'N/A';
                    $firstMovieWatched = 'N/A';
                    $firstMovieWatchedTitle = 'N/A';
                }

                if ($toWatchEntries) {
                    //planned time to watch
                    $plannedTimeWatched = 0;
                    foreach ($toWatchEntries as $entry) {
                        $movieID = $entry['movieID'];
                        //get runtime
                        $stmt = $pdo->prepare("SELECT `runtime` FROM `cois3430_movies` WHERE `movieID`=?");
                        $stmt->execute([$movieID]);
                        $runtime = $stmt->fetch()['runtime'];
                        $plannedTimeWatched += $runtime;
                    }
                } else {
                    $plannedTimeWatched = 'N/A';
                }

                // now take plannedTimeWatched,firstMovieWatched, firstmovieWatchedTitle,averageRating, and timeWatched and put into json object
                $entry = [
                    "date of first Movie Watched" => $firstMovieWatched,
                    "first Movie watched Title" => $firstMovieWatchedTitle,
                    "average movie Rating" => $averageRating,
                    "planned movie Time Watched" => $plannedTimeWatched,
                    "actual movie time watched" => $timeWatched
                ];
                //send response
                sendResponse(200, ["stats" => $entry]);

            } else {
                sendResponse(500, ["error" => "no entries in completed watch list or no watch list for this user"]);  //no content
            }
        } else {
            //send error message
            sendResponse(500, ["error" => "trying to see user stats not authorized by API key"]);
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

        parse_str(file_get_contents('php://input'), $req_data);

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
        $stmt = $pdo->prepare("INSERT INTO cois3430_toWatchList (`userID`, `movieID`, `priority`, `notes`) VALUES (?,?,?,?)");
        $stmt->execute([$userId, $movieID, $priority, $notes]);
        // if successful insert into toWatchlist send appropriate response else send error
        if ($stmt) {
            sendResponse(201, ["message" => "Successfully added to watchlist"]);
        } else {
            sendResponse(500, ["error" => "failed to insert watch list entry"]);
        }
    }

    //else if inserting new completed watch list entry
    elseif ($endpoint == 'completedwatchlist/entries' || $endpoint == 'completedwatchlist/entries/') {
        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        // get form data
        parse_str(file_get_contents('php://input'), $req_data);

        // movieID, rating,notes,date_initially_watched, date_last_watched, times_watched
        // first check if they are all set
        if (empty($req_data['movieID']) || empty($req_data["notes"]) || empty($req_data["date_initially_watched"]) || empty($req_data["date_last_watched"]) || empty($req_data["times_watched"])) {
            sendResponse(500, ["error" => "You must provide a movieID, notes, date_initially_wathced, date_last_watched, and times_watched "]);
        }
        $movieID = $req_data["movieID"];
        $rating = $req_data['rating'];
        $notes = $req_data['notes'];
        $dateInitiallyWatched = $req_data['date_initially_watched'];
        $dateLastWatched = $req_data['date_last_watched'];
        $timesWatched = $req_data['times_watched'];

        //check that movieID is a number
        if (!is_numeric($movieID)) {
            //send error response
            sendResponse(400, ["error" => "movieID must be a number"]);
        }
        //check that rating is a number and is between 1-10 inclusive
        if (!is_numeric($rating)) {
            //send error response
            sendResponse(400, ["error" => "rating must be a number"]);
        }
        if ($rating < 1 || $rating > 10) {
            //send error response
            sendResponse(400, ["error" => "rating must be between 1 and 10"]);
        }

        //check that dateIntitiallyWatched is in SQL date format like 2019-03-15
        if (!validateDate($dateInitiallyWatched, 'Y-m-d')) {
            //send error
            sendResponse(400, ["error" => "date_initially_watched is not in SQL date format like 2019-03-15"]);
        }

        //check that dateLastWatched is in SQL date format like 2019-03-15
        if (!validateDate($dateLastWatched, 'Y-m-d')) {
            //send error
            sendResponse(400, ["error" => "date_last_watched is not in SQL date format like 2019-03-15"]);
        }
        //check that timesWatched is an integer
        if (!is_numeric($timesWatched)) {
            //send error response
            sendResponse(400, ["error" => "times_watched must be an integer"]);
        }
        //check that timesWatched is greater than or equal to zero
        if ($timesWatched < 0) {
            //send error response
            sendResponse(400, ["error" => "times_watched must be a positive number"]);
        }
        // get movie data from movie table with movieID
        $stmt = $pdo->prepare("SELECT * FROM cois3430_movies WHERE movieID=?");
        $stmt->execute([$movieID]);
        $movie = $stmt->fetch();
        // if movieID does not exist in movie database throw error
        if (!$movie) {
            // throw error
            sendResponse(409, ["error" => "movieID $movieID does not exist in movie database"]);
        } else {
            // check if movie ID already exists in CompletedWatchList
            $stmt  = $pdo->prepare("SELECT * FROM cois3430_completedWatchList WHERE movieID =? AND userID =?");
            $stmt->execute([$movieID, $userId]);
            $result = $stmt->fetch();
            if ($result) {
                //if there is an entry with this movie id send an error saying movie already exists
                sendResponse(409, ["error" => "movieID $movieID already exists in CompletedWatchList"]);
            } else {
                // insert new movie to completed watchlist

                $stmt  = $pdo->prepare("INSERT INTO cois3430_completedWatchList (`movieID`, `userID`, `rating`, `notes`, `date_initially_watched`, `date_last_watched`, `times_watched`) VALUES (?,?,?,?,?,?,?)");
                $stmt->execute([$movieID,$userId,$rating,$notes,$dateInitiallyWatched,$dateLastWatched,$timesWatched]);
                // if successful insert into completed watch list send appropriate response else send error
                if ($stmt) {

                    //extract vote_average and vote_count from return
                    $vote_average = $movie['vote_average'];
                    $vote_count = $movie['vote_count'];

                    //update rating for movie in movie table
                    //calculate new rating using forumla (oldAvgRating * oldRatingCount) + NewRating / NewCount
                    $newRating = (($vote_average * $vote_count) + $rating) / ($vote_count + 1);
                    $stmt = $pdo->prepare("UPDATE cois3430_movies SET vote_count=?, vote_average=? WHERE movieID=?");
                    $stmt->execute([$vote_count + 1,$newRating, $movieID]);
                    sendResponse(201, ["message" => "Successfully added to completed watchlist"]);

                } else {
                    sendResponse(500, ["error" => "failed to insert completed watch list entry"]);
                }
            }
        }

    } else {
        sendResponse(500, ["error" => "something was wrong with the endpoint"]);
    }
}
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
        $movieId = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        parse_str(file_get_contents('php://input'), $req_data);

        // data as movieID, priority, and notes
        // first check if they are all set
        if (empty($req_data["priority"]) || empty($req_data["notes"])) {
            sendResponse(500, ["error" => "You must provide a priority and notes"]);
        }
        // get form data in the same method as the API key

        $priority = $req_data["priority"];
        $notes = $req_data["notes"];

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
        $stmt = $pdo->prepare("SELECT * FROM cois3430_toWatchList WHERE movieId=? AND userID=?");
        $stmt->execute([$movieId, $userId]);
        $entry = $stmt->fetch();
        // if there is an entry with the movieID then update it else, insert new entry into toWatchList table
        if ($entry) {
            $toWatchListId = $entry['toWatchListID'];

            //update code
            $stmt = $pdo->prepare("UPDATE cois3430_toWatchList SET priority=?, notes=? WHERE toWatchListID=?");
            $stmt->execute([ $priority, $notes, $toWatchListId]);
            sendResponse(201, ["message" => "To Watch List Updated"]);
        } else {
            //insert code
            $stmt = $pdo->prepare("INSERT INTO cois3430_toWatchList ( userID, movieID, priority, notes) VALUES(?,?,?,?)");
            $stmt->execute([ $userId, $movieId, $priority, $notes]);
            sendResponse(201, ["message" => "To Watch List Inserted"]);
        }

    } else {
        sendResponse(500, ["error" => "something was wrong with the endpoint"]);
    }
}

//else if method is patch
elseif ($method == 'PATCH') {
    // Define the pattern to match /toWatchList/entries/{id}
    $pattern = '/^towatchlist\/entries\/(\d+)\/priority$/';
    $pattern2 = '/^towatchlist\/entries\/(\d+)\/priority\/$/';

    //patterns for /completedWatchList/entries/{id}/rating
    $ratingPattern = '/^completedwatchlist\/entries\/(\d+)\/rating$/';
    $ratingPattern2 = '/^completedwatchlist\/entries\/(\d+)\/rating\/$/';

    //patterns for /completedWatchList/entries/{id}/times-watched
    $watchedPattern = '/^completedwatchlist\/entries\/(\d+)\/times-watched$/';
    $watchedPattern2 = '/^completedwatchlist\/entries\/(\d+)\/times-watched\/$/';

    // if match is successful then update priority for entry
    if (preg_match($pattern, $endpoint, $matches) || preg_match($pattern2, $endpoint, $matches)) {
        //get toWatchListId from url
        $toWatchListId  = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        parse_str(file_get_contents('php://input'), $req_data);

        // data as priority
        // first check if they are all set
        if (empty($req_data['priority'])) {
            sendResponse(500, ["error" => "You must provide a priority"]);
        }
        // get form data in the same method as the API key
        $priority = $req_data["priority"];
        //check that priority is a number
        if (!is_numeric($priority)) {
            //send error response
            sendResponse(400, ["error" => "Priority must be a number"]);
        }

        //check that entry exists in DB
        $stmt = $pdo->prepare('SELECT * FROM cois3430_toWatchList WHERE toWatchListId=? AND userID=?');
        $stmt->execute([$toWatchListId, $userId]);
        //check that entry exists in toWatchList
        if ($stmt->rowCount() == 0) {
            sendResponse(400, ["error" => "this entry does not exist in toWatchList"]);
        }
        //otherwise update watch list entry
        else {
            $stmt = $pdo->prepare("UPDATE cois3430_toWatchList SET priority=? WHERE toWatchListID=? AND userID=?");
            $stmt->execute([$priority, $toWatchListId,$userId]);
            sendResponse(200, ["message" => "priority updated for entry in toWatchList"]);
        }

    }
    // else if endpoint like /completedWatchList/entries/{id}/rating/
    elseif (preg_match($ratingPattern, $endpoint, $matches) || preg_match($ratingPattern2, $endpoint, $matches)) {
        //get movieID from url
        $movieID  = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        //check that movieID exists in completedToWatchList
        $stmt = $pdo->prepare("SELECT * FROM cois3430_completedWatchList WHERE movieID=? AND userID=?");
        $stmt->execute([$movieID,$userId]);
        if ($stmt->rowCount() == 0) {
            // send error that movieID does not exist
            sendResponse(403, ["error" => "movieID does not exist in completedWatchList"]);
        }
        //else update entry code
        {
            // get rating
            parse_str(file_get_contents('php://input'), $req_data);

            if (empty($req_data['rating'])) {
                sendResponse(500, ["error" => "You must provide a rating"]);
            }
            $rating = $req_data['rating'];
            //check that rating is a number and is between 1-10 inclusive
            if (!is_numeric($rating)) {
                //send error response
                sendResponse(400, ["error" => "rating must be a number"]);
            }
            if ($rating < 1 || $rating > 10) {
                //send error response
                sendResponse(400, ["error" => "rating must be between 1 and 10"]);
            }

            //update rating for entry
            $stmt = $pdo->prepare('UPDATE cois3430_completedWatchList SET rating=? WHERE movieID=? AND userId=?');
            //if successful update send appropriate response else send error
            $stmt->execute([$rating,  $movieID, $userId]);

            // if successful update into completed watch list send appropriate response else send error
            if ($stmt) {
                // get movie data from movie table with movieID
                $stmt = $pdo->prepare("SELECT * FROM cois3430_movies WHERE movieID=?");
                $stmt->execute([$movieID]);
                $movie = $stmt->fetch();
                //if movie does not exist in the DB throw error
                if (!$movie) {
                    sendResponse(400, ["error" => "Movie does not exist in Movie Database"]);
                } else {
                    //extract vote_average and vote_count from return
                    $vote_average = $movie['vote_average'];
                    $vote_count = $movie['vote_count'];

                    //update rating for movie in movie table
                    //calculate new rating using forumla (oldAvgRating * oldRatingCount) + NewRating / NewCount
                    $newRating = (($vote_average * $vote_count) + $rating) / ($vote_count + 1);
                    $stmt = $pdo->prepare("UPDATE cois3430_movies SET vote_count=?, vote_average=? WHERE movieID=?");
                    $stmt->execute([$vote_count + 1,$newRating, $movieID]);
                    sendResponse(201, ["message" => "Successfully updated rating for movie in completed watchlist"]);
                }
            } else {
                sendResponse(500, ["error" => "failed to update rating for completed watch list entry"]);
            }
        }
    }

    // else if endpoint like /completedWatchList/entries/{id}/times-watched/
    elseif (preg_match($watchedPattern, $endpoint, $matches) || preg_match($watchedPattern2, $endpoint, $matches)) {
        //get movieID from url
        $movieID  = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        parse_str(file_get_contents('php://input'), $req_data);

        if (empty($req_data['times-watched'])) {
            sendResponse(500, ["error" => "You must provide a new times-watched"]);
        }
        $timesWatched = $req_data['times-watched'];
        //check that timesWatched is an integer
        if (!is_numeric($timesWatched)) {
            //send error response
            sendResponse(400, ["error" => "times_watched must be an integer"]);
        }
        //check that timesWatched is greater than or equal to zero
        if ($timesWatched < 0) {
            //send error response
            sendResponse(400, ["error" => "times_watched must be a positive number"]);
        }

        //first get old times watched in order to increment
        $stmt = $pdo->prepare('SELECT times_watched FROM cois3430_completedWatchList WHERE movieID=? AND userId=?');
        $stmt->execute([$movieID, $userId]);
        //check that movieID exists in toWatchList
        if ($stmt->rowCount() == 0) {
            sendResponse(400, ["error" => "movieID does not exist in toWatchList"]);
        }
        //extract times_watched
        $entry = $stmt->fetch();
        // calculate new incremental times watched
        $newTimesWatched = $entry['times_watched'] + $timesWatched;

        // create variable for current date in format 2019-03-27
        $currentDate = date('Y-m-d', time());

        //update times watched and date last watched for entry
        $stmt = $pdo->prepare('UPDATE cois3430_completedWatchList SET times_watched=?,date_last_watched=? WHERE movieID=? AND userId=?');
        $stmt->execute([$newTimesWatched,$currentDate,$movieID,$userId]);

        // if successful update into completed watch list send appropriate response else send error
        if ($stmt) {
            sendResponse(201, ["message" => "Successfully updated times_watched for movie in completed watchlist"]);
        } else {
            sendResponse(500, ["error" => "failed to update times_watched for completed watch list entry"]);
        }

    } else {
        sendResponse(500, ["error" => "something was wrong with the endpoint"]);
    }

}

//else if method is delete
elseif ($method == 'DELETE') {
    // Define the pattern to match /toWatchList/entries/{id}
    $pattern = '/^towatchlist\/entries\/(\d+)\/$/';
    $pattern2 = '/^towatchlist\/entries\/(\d+)$/';

    //patterns for /completedWatchList/entries/{id}
    $completedPattern = '/^completedwatchlist\/entries\/(\d+)\/$/';
    $completedPattern2 = '/^completedwatchlist\/entries\/(\d+)$/';


    // if endpoint matches for delete watch list entry
    if (preg_match($pattern, $endpoint, $matches) || preg_match($pattern2, $endpoint, $matches)) {
        //get toWatchListId from url
        $movieID = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);

        parse_str(file_get_contents('php://input'), $req_data);


        //check that movieID is a number
        if (!is_numeric($movieID)) {
            //send error response
            sendResponse(400, ["error" => "movieID must be a number"]);
        }
        //check that movieID is in toWatchList
        $stmt = $pdo->prepare("SELECT * FROM cois3430_toWatchList WHERE movieID=? AND userID=?");
        $stmt->execute([$movieID,$userId]);
        //check that movieID exists in toWatchList
        if ($stmt->rowCount() == 0) {
            sendResponse(400, ["error" => "movieID does not exist in toWatchList"]);
        }
        //otherwise remove watch list entry
        else {
            $stmt = $pdo->prepare("DELETE FROM cois3430_toWatchList WHERE movieID=? AND userID=?");
            $stmt->execute([$movieID,$userId]);
            sendResponse(200, ["message" => "movieID removed from toWatchList"]);
        }

    }
    // else if endpoint matches for delete completed watch list entry
    if (preg_match($completedPattern, $endpoint, $matches) || preg_match($completedPattern2, $endpoint, $matches)) {
        //get movieID from url
        $movieID = $matches[1];

        //get api key from form header data (header is X-API-KEY: {key})
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        //check if api key is valid
        $userId = checkApiKey($apiKey, $pdo);


        //check that movieID is in toWatchList
        $stmt = $pdo->prepare("SELECT * FROM cois3430_completedWatchList WHERE movieID=?  AND userID=?");
        $stmt->execute([$movieID,$userId]);
        //check that movieID exists in toWatchList
        if ($stmt->rowCount() == 0) {
            sendResponse(400, ["error" => "movieID does not exist in completedWatchList"]);
        }
        //otherwise remove watch list entry
        else {
            $stmt = $pdo->prepare("DELETE FROM cois3430_completedWatchList WHERE movieID=? AND userID=?");
            $stmt->execute([$movieID,$userId]);
            sendResponse(200, ["message" => "movieID removed from completedWatchList"]);
        }

    } else {
        sendResponse(500, ["error" => "something was wrong with the endpoint"]);
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