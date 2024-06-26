<?php
$name = getenv('MYNAME');
session_start();
?>


<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
    <link rel="stylesheet" href="./styles/main.css">
</head>

<body>

    <?php include './components/nav.php'; ?>

    <h1> API details </h1>
    your name is <?php echo $name; ?>
    <h2> Routes & Endpoints </h2>
    <h3> Movies </h3>
    <ul>
        <li>Get Movies - {GET} /movies </li>
        <li>Get Movie with ID - {GET} /movies/{ID}/ </li>
        <li>Get Movie rating with ID - {GET} /movies/{ID}/rating</li>
    </ul>
    <h3> to Watch List </h3>
    <ul>
        <li>Get watch list entries - {GET} /toWatchList/entries {X-API-KEY}</li>
        <li>Post watch list entry - {POST} /toWatchList/entries {X-API-KEY} {movieId, priority, notes}</li>
        <li>Put watch list entry - {PUT} /towatchlist/entries/{watchListId} {X-API-KEY} {movieId, priority, notes}</li>
        <li>Patch watch list entry - {PATCH} /towatchlist/entries/{watchListId}/priority {X-API-KEY} { priority }</li>
        <li>Delete watch list entry - {DELETE} /towatchlist/entries/{watchListId} {X-API-KEY} {movieId}</li>
    </ul>
    <h3> Completed Watch List </h3>
    <ul>
        <li>Get completed watch list entries - {GET} /completedwatchlist/entries {X-API-KEY} </li>
        <li>Get completed watch list entry's time-watched - {GET} /completedwatchlist/entries/{movieId}/times-watched
            {X-API-KEY} </li>
        <li>Get completed watch list entry's rating - {GET} /completedwatchlist/entries/{movieId}/rating {X-API-KEY}
        </li>
        <li>Post completed watch list entry {POST} /completedwatchlist/entries {X-API-KEY} {movieId, rating, notes,
            date_Initially_Watched, date_last_watched, times_watched}</li>
        <li>Patch completed watch list entry's rating - {PUT} /completedwatchlist/entries/{movieId}/rating {X-API-KEY}
            {rating}</li>
        <li>Patch completed watch list entry's times-watched - {PATCH}
            /completedwatchlist/entries/{movieId}/times-watched {X-API-KEY} { times_watched }</li>
        <li>Delete completed watch list entry - {DELETE} /completedwatchlist/entries/{movieId} {X-API-KEY} </li>
    </ul>
    <h3> User </h3>
    <ul>
        <li>
            Get User stats /users/{id}/stats {X-API-KEY}
        </li>
    </ul>

</body>

</html>