<?php 
$name = getenv('MYNAME'); 
session_start();

if(!isset($_SESSION['userID'])) {
  header("Location:login.php");
  exit();
}

//connect to database
require './includes/library.php';
$pdo = connectdb();

//get reinvent data from database user
$query = 'SELECT * FROM cois3430_users WHERE userID = ?';
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['userID']]);
$userData = $stmt->fetch();

if(isset($_POST['newKey'])){

  //create an unique api key
  $keyFound = false;
  while (!$keyFound){
    $bytes = random_bytes(32);
    $base64ApiKey = base64_encode($bytes);
    $query = 'SELECT * FROM cois3430_users WHERE api_key = ?';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$base64ApiKey]);
    $results = $stmt->rowCount();
    if ($results == 0) { $keyFound = true; }
  }

  //set the new api key
  $query = 'UPDATE cois3430_users SET api_key=?, api_date=NOW() WHERE userID = ?';
  $stmt = $pdo->prepare($query);
  $stmt->execute([$base64ApiKey,$_SESSION['userID']]);

  //refresh the page
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

if(isset($_POST['logout'])){
  unset($_SESSION['username']);
  unset($_SESSION['userID']);
  unset($_SESSION['api_key']);
  header("Location:login.php");
  exit();
}

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

  <div class='profileDiv'>
    <div class='imageContainer'>
      <img src='https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png' alt="Image of pokemon" />
    </div>
    <?php if(isset($userData)) : ?>
      <p><b>Username:</b> <?= $userData['username'] ?></p>
      <p><b>Email:</b> <?= $userData['email'] ?></p>
      <p><b>Api_key:</b> <?= $userData['api_key'] ?></p>
      <p><b>Api_date:</b> <?= $userData['api_date'] ?></p>
    <?php endif ?>

    <form id="profileButtons" method="post" action="">
      <input type="submit" name="newKey" value="Request New Api Key">
      <input type="submit" name="logout" value="Logout">
    </form>
  </div>

</body>

</html>