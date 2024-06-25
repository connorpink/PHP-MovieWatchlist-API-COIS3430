<?php 
$name = getenv('MYNAME');

session_start();
// redirect if user is already logged in
if(isset($_SESSION['username']) && isset($_SESSION['userID']) && isset($_SESSION['api_key'])) {
  header("Location:index.php");
  exit();
}

$username = $_POST['username'] ?? "";
$email = $_POST['email'] ?? "";
$passwordOne = $_POST['passwordOne'] ?? "";
$passwordTwo = $_POST['passwordTwo'] ?? "";

$error = '';
if(isset($_POST['submit'])){

  // check for any invalid data provided by the user (because of you the html is setup only one error message will be shown at once)
  if (empty($username)){ $error = 'no username given'; }
  else if (empty($email)){ $error = 'no email given'; }
  else if (empty($passwordOne)) { $error = 'no password given'; }
  else if ($passwordOne != $passwordTwo) { $error = 'passwords dont match'; }
  else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'invalid email'; }
  else {

    //connect to the database
    require './includes/library.php';
    $pdo = connectdb();

    //check if username already exists in database
    $query = 'SELECT * FROM cois3430_users WHERE username = ?';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $results = $stmt->rowCount();
    if ($results != 0) { $error = 'username taken'; }
    else {

      //hash the password
      $hash = password_hash($passwordOne, PASSWORD_DEFAULT);

      //create an api key
      $bytes = random_bytes(32);
      $base64ApiKey = base64_encode($bytes);

      //save user data to the database
      $query = 'insert into cois3430_users (username,email,password,api_key,api_date) values (?,?,?,?,NOW())';
      $stmt = $pdo->prepare($query);
      $stmt->execute([$username, $email, $hash,$base64ApiKey]);

      //save relavent data to session
      $_SESSION['userID'] = $pdo->lastInsertId();
      $_SESSION['username'] = $username;
      $_SESSION['api_key'] = $base64ApiKey;

      //redirect user
      header("Location:index.php");
      exit();
    }
  }
}
 
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account</title>
  <link rel="stylesheet" href="./styles/login.css">
  <link rel="stylesheet" href="./styles/main.css">
</head>

<body>
  <?php include './components/nav.php'; ?>

  <div class="loginForm">

    <h1>Create Account</h1>

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
        type="text" 
        name="email" 
        value="<?= $email ?>"
        placeholder=' '
        />
        <label for="email">Email</label>
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

      <div class="textInput">
        <input 
        type="password" 
        name="passwordTwo" 
        value="<?= $passwordTwo ?>"
        placeholder=' '
        />
        <label for="password">Confirm Password</label>
      </div>

      <input type="submit" name="submit" value="Create Account">

    </form>

    <?php if ($error != ''): ?>
      <p class="error"> error: <?= $error ?> </p>
    <?php endif ?>

    <p>Already have an account?</p>
    <a href='/~<?= $name ?>/3430/assn/cois-3430-2024su-a2-BigBeill/login'>account login</a>

  </div>
</body>

</html>