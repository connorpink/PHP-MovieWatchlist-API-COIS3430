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
    <ul>
        <li>Get Movies - {GET} /movies </li>
        <li>Get Movie with ID - {GET} /movies/{ID}/ </li>
        <li>Get Movie rating with ID - {GET} /movies/{ID}/rating</li>
        <li>Get watch list entries - {GET} /toWatchList/entries {X-API-KEY}</li>
        <li>Post watch list entry - {POST} /toWatchList/entries {X-API-KEY, movieId, priority, notes}</li>
    </ul>


</body>

</html>