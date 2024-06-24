<?php 
$name = getenv('MYNAME');

session_start();
// redirect if user is already logged in
if(isset($_SESSION['username']) && isset($_SESSION['userID']) && isset($_SESSION['api_key'])) {
  header("Location:index.php");
  exit();
}

$username = $_POST['username'] ?? "";
$passwordOne = $_POST['passwordOne'] ?? "";

$error = '';
if(isset($_POST['submit'])){

  // check for any invalid data provided by the user (because of you the html is setup only one error message will be shown at once)
  if (empty($username)){ $error = 'no username given'; }
  else if (empty($passwordOne)) { $error = 'no password given'; }
  else {

    //connect to the database
    require './includes/library.php';
    $pdo = connectdb();

    //find user with given username
    $query = 'SELECT * FROM cois3430_users WHERE username = ?';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $databaseUser = $stmt->fetch();
    if(empty($databaseUser)) { $error = 'username not found'; }
    else {

      //check if password is correct
      if (!password_verify($passwordOne, $databaseUser['password'])){ $error = 'incorect passwrod'; }
      else {

        //save all relavent information to session
        $_SESSION['username'] = $databaseUser['username'];
        $_SESSION['userID'] = $databaseUser['userID'];
        $_SESSION['api_key'] = $databaseUser['api_key'];

        //redirect user
        header("Location:index.php");
        exit(); 
      }
    }
  }

}
 
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./styles/login.css">
</head>

<body>
  <div class="loginForm">

    <h1>Login</h1>

    <form id="create-account" method="post" action="">

      <div class="textInput">
        <input 
        type="text" 
        name="username"
        value="<?= $username ?>" 
        placeholder=' '
        />
        <label for="username">Username</label>
      </div>

      <div class="textInput">
        <input 
        type="password" 
        name="passwordOne" 
        value="<?= $passwordOne ?>"
        placeholder=' '
        />
        <label for="password">Password</label>
      </div>

      <input type="submit" name="submit" value="Create Account">

    </form>

    <?php if ($error != ''): ?>
      <p class="error"> error: <?= $error ?> </p>
    <?php endif ?>

    <p>Dont have an account?</p>
    <a href='/~<?= $name ?>/3430/assn/cois-3430-2024su-a2-BigBeill/create-account'>create account</a>

  </div>
</body>

</html>